<?php
/**
 * EstoqueRequisicaoList Listing
 * @author  Fred Azv.s
 */
class EstoqueRequisicaoList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('sample');            // defines the database
        $this->setActiveRecord('EstoqueRequisicao');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('data_requisicao', 'like', 'data_requisicao'); // filterField, operator, formField
        $this->addFilterField('hora_requisicao', 'like', 'hora_requisicao'); // filterField, operator, formField
        $this->addFilterField('responsavel_id', 'like', 'responsavel_id'); // filterField, operator, formField
        $this->addFilterField('user_id', 'like', 'user_id'); // filterField, operator, formField
        $this->addFilterField('unit_id', 'like', 'unit_id'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_EstoqueRequisicao');
        $this->form->setFormTitle('Requisição de Produtos');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        $data_requisicao = new TDate('data_requisicao');
        $data_requisicao->setDatabaseMask('yyyy-mm-dd');
        $data_requisicao->setMask('dd/mm/yyyy');
        $hora_requisicao = new TTime('hora_requisicao');
        $responsavel_id = new TDBCombo('responsavel_id','sample','SystemUser','id','name','name');
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);


        // add the fields
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Data Requisição'), $data_requisicao ],
                                       [ new TLabel('Hora Requisição'), $hora_requisicao ]);
        $row->layout = ['col-sm-2','col-sm-4','col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Responsavel do Estoque'), $responsavel_id ],
                                       [ new TLabel('Técnico Requerente'), $user_id ]);
        $row->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('EstoqueRequisicao_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['EstoqueRequisicaoForm', 'onEdit']), 'fa:plus green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        
        $action1 = new TDataGridAction(array('RequerimentoReport', 'onViewRequerimentoReport'));
        $action1->setLabel('Requerimento');
        $action1->setImage('fa:file-pdf-o #FFD700');
        $action1->setField('id');
        
        $action2 = new TDataGridAction(array($this, 'onBaixa'));
        $action2->setLabel('Baixar Estoque');
        $action2->setImage('fa:arrow-down  red');
        $action2->setField('id');
        
        $action_group = new TDataGridActionGroup('Ações ', 'bs:th');
        
        $action_group->addHeader('Relatórios');
        $action_group->addAction($action1);
        $action_group->addSeparator();
        $action_group->addHeader('Estoque');
        $action_group->addAction($action2);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_data_requisicao = new TDataGridColumn('data_requisicao', 'Data Requisição', 'left');
        $column_hora_requisicao = new TDataGridColumn('hora_requisicao', 'Hora Requisição', 'left');
        $column_responsavel_id = new TDataGridColumn('system_user->name', 'Responsavel pelo Estoque', 'left');
        $column_user_id = new TDataGridColumn('tecnico->name', 'Ténico Requerente', 'left');

        $column_data_requisicao->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data_requisicao);
        $this->datagrid->addColumn($column_hora_requisicao);
        $this->datagrid->addColumn($column_responsavel_id);
        $this->datagrid->addColumn($column_user_id);

        
        // create EDIT action
        $action_edit = new TDataGridAction(['EstoqueRequisicaoForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
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


    public function onBaixa( $param ){

        try
        {
            // get the parameter $key
            $key   = $param['key'];
            
            TTransaction::open('sample'); // open a transaction with database

            $requisicao = new EstoqueRequisicao($key); //para pegar o ID Técnico

            $cnpjUnit = new SystemUnit($requisicao->unit_id);

            $requisicao_itens = EstoqueRequisicaoItens::where('estoque_requisicao_id','=',$requisicao->id)->load();
            
            if ($requisicao_itens)
            {
                foreach ($requisicao_itens as $itens)
                { 
                    $estoqueEmpresa = new Estoquemov();
                    $estoqueEmpresa->unit_id = $requisicao->unit_id;
                    $estoqueEmpresa->produto_id = $itens->produto_id;
                    $estoqueEmpresa->local = $cnpjUnit->cnpj;
                    $estoqueEmpresa->tipo = 'S';
                    $estoqueEmpresa->quantidade = $itens->quantidade;
                    $estoqueEmpresa->referencia = 'REQUISIÇÃO DE Nº '.$requisicao->id.'PARA O ESTOQUE DO TÉCNICO';
                    $estoqueEmpresa->controla_lote = 'N';
                    $estoqueEmpresa->store();

                    $estoqueTecnico = new EstoquemovMovel();
                    $estoqueTecnico->unit_id = $requisicao->unit_id;
                    $estoqueTecnico->produto_id = $itens->produto_id;
                    $estoqueTecnico->local = $requisicao->user_id;
                    $estoqueTecnico->tipo = 'E';
                    $estoqueTecnico->quantidade = $itens->quantidade;
                    $estoqueTecnico->referencia = 'REQUISIÇÃO DE Nº '.$requisicao->id.'PARA O ESTOQUE DO TÉCNICO';
                    $estoqueTecnico->controla_lote = 'N';
                    $estoqueTecnico->store();
                }

            }else{

                $this->onReload($param); // reload the listing
                new TMessage('error', "Erro ao dar baixa no estoque!");
                TTransaction::rollback();
            }     

            TTransaction::close();
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Baixa em estoque realizada com sucesso!");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }

    }
    

}
