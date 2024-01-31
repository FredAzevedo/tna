<?php
/**
 * TelefonesClienteSeek Listing
 * @author  <your name here>
 */
class TelefonesClienteSeek extends TWindow
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Contatos');
        parent::setSize(0.4, null);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_TelefonesCliente');
        $this->form->setFormTitle('Telefones do cliente');
        

        // create the form fields
        $responsavel = new TEntry('responsavel');


        // add the fields
        $this->form->addFields( [ new TLabel('Responsável') ], [ $responsavel ] );


        // set sizes
        $responsavel->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('TelefonesCliente_filter_data') );
        
        // add the search form actions
        $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_cliente_id = new TDataGridColumn('cliente_id', 'Cliente Id', 'right');
        $column_responsavel = new TDataGridColumn('responsavel', 'Responsavel', 'left');
        $column_telefone = new TDataGridColumn('telefone', 'Telefone', 'left');


        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        // $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_responsavel);
        $this->datagrid->addColumn($column_telefone);

        
        // create SELECT action
        // $action_select = new TDataGridAction(array($this, 'onSelect'));
        // $action_select->setUseButton(TRUE);
        // $action_select->setButtonClass('nopadding');
        // $action_select->setLabel('');
        // $action_select->setImage('far:hand-pointer green');
        // $action_select->setField('id');
        // $this->datagrid->addAction($action_select);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%;margin-bottom:0;border-radius:0';
        //$container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    public function onEdit($param)
    {
        try
        {
            var_dump($param);
            if (isset($param['cliente_id']))
            {
                $cliente_id = $param['cliente_id'];  // get the parameter $cliente_id
                TTransaction::open('sample'); // open a transaction
                $object = TelefonesCliente::where('cliente_id','=',$cliente_id)->load(); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
                $this->onReload( $param );
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }

    }
    public function onSearch( $param )
    {
        // get the search form data
        $data = $this->form->getData();
        $cliente_id = $param['cliente_id'];
        // clear session filters
        TSession::setValue('TelefonesClienteSeek_filter_cliente_id',   NULL);
        TSession::setValue('TelefonesClienteSeek_filter_responsavel',   NULL);

        if (isset($cliente_id) AND ($cliente_id)) {
            $filter = new TFilter('cliente_id', '=', "{$cliente_id}"); // create the filter
            TSession::setValue('TelefonesClienteSeek_filter_cliente_id',   $filter); // stores the filter in the session
        }

        if (isset($data->responsavel) AND ($data->responsavel)) {
            $filter = new TFilter('responsavel', 'like', "%{$data->responsavel}%"); // create the filter
            TSession::setValue('TelefonesClienteSeek_filter_responsavel',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('TelefonesCliente_filter_data', $data);
        
        $param=array();
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
            
            // creates a repository for TelefonesCliente
            $repository = new TRepository('TelefonesCliente');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue('TelefonesClienteSeek_filter_cliente_id')) {
                $criteria->add(TSession::getValue('TelefonesClienteSeek_filter_cliente_id')); // add the session filter
            }

            if (TSession::getValue('TelefonesClienteSeek_filter_responsavel')) {
                $criteria->add(TSession::getValue('TelefonesClienteSeek_filter_responsavel')); // add the session filter
            }

            
            // load the objects according to criteria
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
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
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
