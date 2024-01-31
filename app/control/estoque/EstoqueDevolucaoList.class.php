<?php
/**
 * EstoqueDevolucaoList Listing
 * @author  Fred Azv.s
 */
class EstoqueDevolucaoList extends TPage
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
        
        $this->setDatabase('sample');            
        $this->setActiveRecord('EstoqueDevolucao');   
        $this->setDefaultOrder('id', 'asc');         


        $this->addFilterField('id', '=', 'id'); 
        $this->addFilterField('data_devolucao', 'like', 'data_devolucao'); 
        $this->addFilterField('hora_devolucao', 'like', 'hora_devolucao');
        $this->addFilterField('responsavel_id', 'like', 'responsavel_id');
        $this->addFilterField('user_id', 'like', 'user_id');
        $this->addFilterField('unit_id', 'like', 'unit_id');
        
        $this->form = new BootstrapFormBuilder('form_EstoqueDevolucao');
        $this->form->setFormTitle('Devolução de Produtos');
        $this->form->setFieldSizes('100%');
        
        $id = new TEntry('id');
        $data_devolucao = new TDate('data_devolucao');
        $data_devolucao->setDatabaseMask('yyyy-mm-dd');
        $data_devolucao->setMask('dd/mm/yyyy');
        $hora_devolucao = new TTime('hora_devolucao');
        $responsavel_id = new TDBCombo('responsavel_id','sample','SystemUser','id','name','name');
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Data Devolução'), $data_devolucao ],
                                       [ new TLabel('Hora Devolução'), $hora_devolucao ]);
        $row->layout = ['col-sm-2','col-sm-4','col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Responsavel pelo Estoque'), $responsavel_id ],
                                       [ new TLabel('Técnico'), $user_id ]);
        $row->layout = ['col-sm-6','col-sm-6'];

        $this->form->setData( TSession::getValue('EstoqueDevolucao_filter_data') );
        
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['EstoqueDevolucaoForm', 'onEdit']), 'fa:plus green');
        
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
        
        //$action_group->addHeader('Relatórios');
        //$action_group->addAction($action1);
        $action_group->addSeparator();
        $action_group->addHeader('Estoque');
        $action_group->addAction($action2);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_data_devolucao = new TDataGridColumn('data_devolucao', 'Data Devolução', 'left');
        $column_hora_devolucao = new TDataGridColumn('hora_devolucao', 'Hora Devolução', 'left');
        $column_responsavel_id = new TDataGridColumn('system_user->name', 'Responsavel pelo Estoque', 'left');
        $column_user_id = new TDataGridColumn('tecnico->name', 'Ténico', 'left');

        $column_data_devolucao->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data_devolucao);
        $this->datagrid->addColumn($column_hora_devolucao);
        $this->datagrid->addColumn($column_responsavel_id);
        $this->datagrid->addColumn($column_user_id);

        
        // create EDIT action
        $action_edit = new TDataGridAction(['EstoqueDevolucaoForm', 'onEdit']);
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

            $devolucao = new EstoqueDevolucao($key); //para pegar o ID Técnico

            $cnpjUnit = new SystemUnit($devolucao->unit_id);

            $devolucao_itens = EstoqueDevolucaoItens::where('estoque_devolucao_id','=',$devolucao->id)->load();
            
            if ($devolucao_itens)
            {
                foreach ($devolucao_itens as $itens)
                { 
                    $estoqueEmpresa = new Estoquemov();
                    $estoqueEmpresa->unit_id = $devolucao->unit_id;
                    $estoqueEmpresa->produto_id = $itens->produto_id;
                    $estoqueEmpresa->local = $cnpjUnit->cnpj;
                    $estoqueEmpresa->tipo = 'E';
                    $estoqueEmpresa->quantidade = $itens->quantidade;
                    $estoqueEmpresa->referencia = 'DEVOLUÇÃO DE Nº '.$devolucao->id.'PARA O ESTOQUE DA EMPRESA';
                    $estoqueEmpresa->controla_lote = 'N';
                    $estoqueEmpresa->store();

                    $estoqueTecnico = new EstoquemovMovel();
                    $estoqueTecnico->unit_id = $devolucao->unit_id;
                    $estoqueTecnico->produto_id = $itens->produto_id;
                    $estoqueTecnico->local = $devolucao->user_id;
                    $estoqueTecnico->tipo = 'S';
                    $estoqueTecnico->quantidade = $itens->quantidade;
                    $estoqueTecnico->referencia = 'DEVOLUÇÃO DE Nº '.$devolucao->id.'PARA O ESTOQUE DA EMPRESA';
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
