<?php
/**
 * EstoqueTransferenciaImobilizadoList Listing
 * @author  Fred Azv.
 */
class EstoqueTransferenciaImobilizadoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_EstoqueTransferenciaImobilizado');
        $this->form->setFormTitle('Transferencia entre Locais');
        $this->form->setFieldSizes('100%');

        $local_origem = new TDBCombo('local_origem','sample','Viewlocal','id','nome_fantasia','nome_fantasia');
        $local_origem->enableSearch();
        $local_origem->addValidation('Local de origem', new TRequiredValidator);

        $local_destino = new TDBCombo('local_destino','sample','Viewlocal','id','nome_fantasia','nome_fantasia');
        $local_destino->enableSearch();
        $local_destino->addValidation('Local de destino', new TRequiredValidator);

        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');

        $created_at = new TDate('created_at');
        //$created_at->setValue(date("d-m-Y hh:ii"));
        $created_at->setDatabaseMask('yyyy-mm-dd');
        $created_at->setMask('dd/mm/yyyy');
 
    
        $row = $this->form->addFields( [ new TLabel('Local de Origem'), $local_origem ],
                                       [ new TLabel('Local de Destino'), $local_destino ],
                                       [ new TLabel('Usuário'), $user_id ],
                                       [ new TLabel('Usuário'), $created_at ]
        );
        $row->layout = ['col-sm-4','col-sm-4', 'col-sm-2','col-sm-2'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['EstoqueTransferenciaImobilizadoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_local_origem = new TDataGridColumn('localOrigem->nome_fantasia', 'Local Origem', 'left');
        $column_local_destino = new TDataGridColumn('localDestino->nome_fantasia', 'Local Destino', 'left');
        $column_user_id = new TDataGridColumn('system_user->name', 'Usuário', 'right');
        $column_created_at = new TDataGridColumn('created_at', 'Criação', 'right');

        $column_id->setTransformer(array($this, 'formatColor'));

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_local_origem);
        $this->datagrid->addColumn($column_local_destino);
        $this->datagrid->addColumn($column_user_id);
        $this->datagrid->addColumn($column_created_at);

        $column_created_at->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y H:i');
        });

        $action1 = new TDataGridAction(array($this, 'onFinalizarTransferencia'));
        $action1->setLabel('Finalizar Transferência');
        $action1->setImage('fa:arrow-down black');
        $action1->setField('id');
        $action1->setField('baixa');
    
        $action_group = new TDataGridActionGroup('Ações ', 'bs:th');
        
        $action_group->addHeader('Processamento');
        $action_group->addAction($action1);
        $action_group->addSeparator();

        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);


        $action1 = new TDataGridAction(['EstoqueTransferenciaImobilizadoForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public function formatColor($column_id, $object, $row)
    {
        $data = new EstoqueTransferenciaImobilizado($column_id);

        if ($data->baixa == "S")
        {
            $row->style = "background: #97f498";
            return "<span>$column_id</span>";
        }else{
            return "<span>$column_id</span>";
        }
    }

    public function onFinalizarTransferencia($param)
    {
        TTransaction::open('sample');
    
        if($param['baixa'] == 'N')
        {
            $object = new EstoqueTransferenciaImobilizado($param['id']); 
            $object->baixa = S;
            $object->store();

            $trans = EstoqueTransferenciaImobilizadoItem::where('estoque_transferencia_imobilizado_id','=',$param['id'])->load();

            if($trans)
            {
                foreach($trans as $item)
                {
                    //INSERINDO SAIDA DA ORIGEM
                    $estoqueOrigem = new EstoqueControlmovImobilizado;
                    $estoqueOrigem->unit_id = $object->unit_id;
                    $estoqueOrigem->produto_id = $item->produto_id;
                    $estoqueOrigem->local = $object->local_origem;
                    $estoqueOrigem->alocado = 'PRODUTO MOVIMENTADO SOB TRANSFERÊNCIA DE Nº '.$param['id'];
                    $estoqueOrigem->user_id = $object->user_id;
                    $estoqueOrigem->estado = $item->estado;
                    $estoqueOrigem->validade_mes = $item->validade_mes;
                    $estoqueOrigem->data_avaliacao = $item->data_avaliacao;
                    $estoqueOrigem->valor_justo = $item->valor_justo;
                    $estoqueOrigem->emplacamento = $item->emplacamento;
                    $estoqueOrigem->tipo = 'S';
                    $estoqueOrigem->quantidade = 1;
                    $estoqueOrigem->store();

                    //INSERINDO ENTRADA NO DESTINO
                    $estoqueDestino = new EstoqueControlmovImobilizado;
                    $estoqueDestino->unit_id = $object->unit_id;
                    $estoqueDestino->produto_id = $item->produto_id;
                    $estoqueDestino->local = $object->local_destino;
                    $estoqueDestino->alocado = 'PRODUTO MOVIMENTADO SOB TRANSFERÊNCIA DE Nº '.$param['id'];
                    $estoqueDestino->user_id = $object->user_id;
                    $estoqueDestino->estado = $item->estado;
                    $estoqueDestino->validade_mes = $item->validade_mes;
                    $estoqueDestino->data_avaliacao = $item->data_avaliacao;
                    $estoqueDestino->valor_justo = $item->valor_justo;
                    $estoqueDestino->emplacamento = $item->emplacamento;
                    $estoqueDestino->tipo = 'E';
                    $estoqueDestino->quantidade = 1;
                    $estoqueDestino->store();
                }
            }

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', 'Transferência realizada com sucesso. <br>',$pos_action);
        }

        TTransaction::close();
    }


    public function onSearch()
    {
        
        $data = $this->form->getData();
        

        TSession::setValue(__CLASS__.'_filter_local_origem',   NULL);
        TSession::setValue(__CLASS__.'_filter_local_destino',   NULL);
        TSession::setValue(__CLASS__.'_filter_created_at',   NULL);
        TSession::setValue(__CLASS__.'_filter_user_id',   NULL);


        if (isset($data->local_origem) AND ($data->local_origem)) {
            $filter = new TFilter('local_origem', 'like', "%{$data->local_origem}%"); 
            TSession::setValue(__CLASS__.'_filter_local_origem',   $filter);
        }

        if (isset($data->local_destino) AND ($data->local_destino)) {
            $filter = new TFilter('local_destino', 'like', "%{$data->local_destino}%"); 
            TSession::setValue(__CLASS__.'_filter_local_destino',   $filter);
        }

        if (isset($data->created_at) AND ($data->created_at)) {
            $filter = new TFilter('created_at', 'like', "%{$data->created_at}%"); 
            TSession::setValue(__CLASS__.'_filter_created_at',   $filter);
        }

        if (isset($data->user_id) AND ($data->user_id)) {
            $filter = new TFilter('user_id', '=', "{$data->user_id}"); 
            TSession::setValue(__CLASS__.'_filter_user_id',   $filter);
        }

        $this->form->setData($data);
        
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {

            TTransaction::open('sample');
            
            $repository = new TRepository('EstoqueTransferenciaImobilizado');
            $limit = 20;
            
            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); 
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue(__CLASS__.'_filter_local_origem')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_local_origem')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_local_destino')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_local_destino')); 
            }

            if (TSession::getValue(__CLASS__.'_filter_created_at')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_created_at')); 
            }


            if (TSession::getValue(__CLASS__.'_filter_user_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_user_id')); 
            }

            
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); 
        
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; 
            TTransaction::open('sample'); 
            $object = new EstoqueTransferenciaImobilizado($key, FALSE); 
            $object->delete(); 
            TTransaction::close(); 
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback();
        }
    }
    
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
