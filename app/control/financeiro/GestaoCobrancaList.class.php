<?php
/**
 * GestaoCobrancaList Listing
 * @author  <your name here>
 */
class GestaoCobrancaList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_GestaoCobranca');
        $this->form->setFormTitle('Gestao de Cobrança');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'razao_social');
        //$cliente_id = new TEntry('cliente_id');
        $valor = new TEntry('valor');
        $devido = new TEntry('devido');
        $dia = new TEntry('dia');
        $mes = new TEntry('mes');
        $ano = new TEntry('ano');

        $BAIXA = new TCombo('BAIXA');
        $combo_BAIXA = array();
        $combo_BAIXA['S'] = 'Sim';
        $combo_BAIXA['N'] = 'Não';
        $BAIXA->addItems($combo_BAIXA);


        $row = $this->form->addFields( [ new TLabel('Cliente'), $cliente_id ],
                                       [ new TLabel('Dia'), $dia ],
                                       [ new TLabel('Mês'), $mes ],
                                       [ new TLabel('Ano'), $ano ]
        );
        $row->layout = ['col-sm-9', 'col-sm-1', 'col-sm-1', 'col-sm-1'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('GestaoCobranca_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
       
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';        

        $column_cliente_id = new TDataGridColumn('cliente_id', 'ID Cliente', 'left');
        $column_razao_social = new TDataGridColumn('razao_social', 'Razao Social', 'left');
        $column_conta_receber = new TDataGridColumn('conta_receber', 'Em aberto', 'left');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');
        $column_devido = new TDataGridColumn('devido', 'Devido', 'right');
        //$column_telefone = new TDataGridColumn('telefone', 'Telefone', 'right');

        $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_razao_social);
        $this->datagrid->addColumn($column_conta_receber);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_devido);
        //$this->datagrid->addColumn($column_telefone);

        $column_cliente_id->setTransformer(array($this, 'formatColor'));

        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };

        $column_valor->setTransformer( $format_value );
        $column_devido->setTransformer( $format_value );

        $action1 = new TDataGridAction(array('GestaoCobrancaForm', 'onEdit'));
        $action1->setLabel('Ver');
        $action1->setImage('fas:search #7C93CF');
        $action1->setField('cliente_id');

        $action2 = new TDataGridAction(array('TelefonesClienteSeek', 'onSearch'));
        $action2->setLabel('Telefones');
        $action2->setImage('fas:list #7C93CF');
        $action2->setField('cliente_id');

        $action3 = new TDataGridAction(array($this, 'onDev'));
        $action3->setLabel('Histórico de Cobranças');
        $action3->setImage('fas:list #7C93CF');
        $action3->setField('cliente_id');

        $action_group = new TDataGridActionGroup('', 'fas:cog');

        $action_group->addHeader('Opções');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action3);

        $this->datagrid->addActionGroup($action_group);


        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    public function onDev()
    {
        new TMessage('info','Em desenvolvimento! Em breve disponível.');
    }

    public function formatColor($column_cliente_id, $object, $row)
    {
        $data = new Cliente($column_cliente_id);
        $hoje = date('Y-m-d');

        if ($data->data_cobranca == $hoje)
        {
            $row->style = "background: #CFEBCF";
            return "<span>$column_cliente_id</span>";
        }else{
            return "<span>$column_cliente_id</span>";
        }
    }

    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        

        TSession::setValue('GestaoCobrancaList_filter_cliente_id',   NULL);
        TSession::setValue('GestaoCobrancaList_filter_razao_social',   NULL);
        TSession::setValue('GestaoCobrancaList_filter_dia',   NULL);
        TSession::setValue('GestaoCobrancaList_filter_mes',   NULL);
        TSession::setValue('GestaoCobrancaList_filter_ano',   NULL);
        TSession::setValue('GestaoCobrancaList_filter_valor',   NULL);
        TSession::setValue('GestaoCobrancaList_filter_devido',   NULL);
        TSession::setValue('GestaoCobrancaList_filter_vencimento',   NULL);

        $data_atual = date('Y-m-d');
        $filter = new TFilter('data_vencimento', '<', $data_atual);
        TSession::setValue('GestaoCobrancaList_filter_vencimento',   $filter);

        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', '=', "$data->cliente_id"); // create the filter
            TSession::setValue('GestaoCobrancaList_filter_cliente_id',   $filter); // stores the filter in the session
        }

        if (isset($data->razao_social) AND ($data->razao_social)) {
            $filter = new TFilter('razao_social', 'like', "%{$data->razao_social}%"); // create the filter
            TSession::setValue('GestaoCobrancaList_filter_razao_social',   $filter); // stores the filter in the session
        }

        if (isset($data->dia) AND ($data->dia)) {
            $filter = new TFilter('DAY(data_vencimento)', '=', "{$data->dia}"); // create the filter
            TSession::setValue('GestaoCobrancaList_filter_dia',   $filter); // stores the filter in the session
        }

        if (isset($data->mes) AND ($data->mes)) {
            $filter = new TFilter('MONTH(data_vencimento)', '=', "{$data->mes}"); // create the filter
            TSession::setValue('GestaoCobrancaList_filter_mes',   $filter); // stores the filter in the session
        }


        if (isset($data->ano) AND ($data->ano)) {
            $filter = new TFilter('YEAR(data_vencimento)', '=', "{$data->ano}"); // create the filter
            TSession::setValue('GestaoCobrancaList_filter_ano',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('GestaoCobranca_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }

    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'sample'
            TTransaction::open('sample');
            $db = TTransaction::get();
            // creates a repository for GestaoCobranca
            //$repository = new TRepository('GestaoCobranca');
            $select = new TSqlSelect();
            $select->setEntity('cliente C
            INNER JOIN conta_receber CR ON (C.id = CR.cliente_id)');

            $select->addColumn('CR.cliente_id');
            $select->addColumn('C.razao_social');
            $select->addColumn('COUNT(CR.id) as conta_receber');
            $select->addColumn('SUM(CR.valor) as valor');
            $select->addColumn('SUM(CR.valor_pago) as devido');

            
            $limit = 1000;

            $data_atual = date('Y-m-d');
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CR.data_vencimento','<',$data_atual));
            $criteria->add(new TFilter('CR.juridico','=',"N"));
            $criteria->add(new TFilter('CR.baixa','=',"N"));
                
            if (TSession::getValue('GestaoCobrancaList_filter_vencimento')) {
                $criteria->add(TSession::getValue('GestaoCobrancaList_filter_vencimento')); // add the session filter
            }

            if (TSession::getValue('GestaoCobrancaList_filter_contas_receber_id')) {
                $criteria->add(TSession::getValue('GestaoCobrancaList_filter_contas_receber_id')); // add the session filter
            }


            if (TSession::getValue('GestaoCobrancaList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('GestaoCobrancaList_filter_cliente_id')); // add the session filter
            }


            if (TSession::getValue('GestaoCobrancaList_filter_razao_social')) {
                $criteria->add(TSession::getValue('GestaoCobrancaList_filter_razao_social')); // add the session filter
            }

            if (TSession::getValue('GestaoCobrancaList_filter_dia')) {
                $criteria->add(TSession::getValue('GestaoCobrancaList_filter_dia')); // add the session filter
            }

            if (TSession::getValue('GestaoCobrancaList_filter_mes')) {
                $criteria->add(TSession::getValue('GestaoCobrancaList_filter_mes')); // add the session filter
            }


            if (TSession::getValue('GestaoCobrancaList_filter_ano')) {
                $criteria->add(TSession::getValue('GestaoCobrancaList_filter_ano')); // add the session filter
            }


            if (TSession::getValue('GestaoCobrancaList_filter_valor')) {
                $criteria->add(TSession::getValue('GestaoCobrancaList_filter_valor')); // add the session filter
            }


            if (TSession::getValue('GestaoCobrancaList_filter_devido')) {
                $criteria->add(TSession::getValue('GestaoCobrancaList_filter_devido')); // add the session filter
            }

            $groupBy = "group by CR.cliente_id";
            $select->setCriteria($criteria);
            $value_sql = $select->getInstruction();
            $value_sql .= $groupBy;

            $count = $db->query($value_sql)->rowCount();

            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'razao_social';
                $param['direction'] = 'asc';
            }
            // $criteria->setProperties($param); // order, offset
            // $criteria->setProperty('limit', $limit);

            $groupBy = "group by CR.cliente_id";
            $select->setCriteria($criteria);
            $value_sql = $select->getInstruction();
            $value_sql .= 'group by CR.cliente_id ORDER BY razao_social asc LIMIT 1000';
            //var_dump($value_sql);
            $result = $db->query($value_sql);
            

            // load the objects according to criteria
            //$objects = $repository->load($criteria, FALSE);
            $objects = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $obj = new stdClass();
                $obj->cliente_id = $row['cliente_id'];
                $obj->razao_social = $row['razao_social'];
                $obj->conta_receber = $row['conta_receber'];
                $obj->valor = $row['valor'];
                $obj->devido = $row['devido'];
                $obj->devido = $row['devido'];
                $objects[] = $obj;
            }

            
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
            
            // reset the criteria for record count
            // $criteria->resetProperties();
            // $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
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
