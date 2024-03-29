<?php

// ini_set('display_errors',1);
// ini_set('display_startup_erros',1);
// error_reporting(E_ALL);

use Adianti\Control\TPage;

/**
 * ApiIntegracaoList Listing
 * @author  Fred Azv.
 */
class ApiIntegracaoList extends TPage
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
        $this->setActiveRecord('ApiIntegracao');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('gateway', 'like', 'gateway'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_ApiIntegracao');
        $this->form->setFormTitle('Filtro');
        

        // create the form fields
        $gateway = new TEntry('gateway');


        // add the fields
        $this->form->addFields( [ new TLabel('gateway') ], [ $gateway ] );


        // set sizes
        $gateway->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ApiIntegracaoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $coluna_tipo = new TDataGridColumn('gatewayTipo', 'Tipo', 'left');
        $column_unit_id = new TDataGridColumn('system_unit->unidade', 'Unidade', 'left');
        $column_gateway = new TDataGridColumn('gatewayNome', 'Gateway', 'left');
        $coluna_url = new TDataGridColumn('url', 'URL', 'left');
        $coluna_producao = new TDataGridColumn('producao', 'Produção', 'left');
        $column_chave = new TDataGridColumn('chave', 'Chave', 'left');
        $column_credencial = new TDataGridColumn('credencial', 'Credencial', 'left');

        $coluna_producao = new TDataGridColumn('producao', 'Ambiente de Produção?', 'left');
        $coluna_producao->setTransformer( function ($producao, $object) {
            if($producao == 0)
                return 'Não';
            else 
                return 'Sim';
        });


        // add the columns to the DataGrid
        $this->datagrid->addColumn($coluna_tipo);
        $this->datagrid->addColumn($column_unit_id);
        $this->datagrid->addColumn($column_gateway);
        $this->datagrid->addColumn($coluna_url);
        $this->datagrid->addColumn($coluna_producao);
        $this->datagrid->addColumn($column_chave);
        $this->datagrid->addColumn($column_credencial);

        //scope from new version fw
        //$action1 = new TDataGridAction(['ApiIntegracaoForm', 'onEdit'], ['id'=>'{id}']);
        //$action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        //$this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        //$this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');

        $action1 = new TDataGridAction(['ApiIntegracaoForm', 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        $this->datagrid->addAction($action1);

        $action2 = new TDataGridAction(array($this, 'onDelete'));
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
        $this->datagrid->addAction($action2);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        /*$dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );*/
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
}
