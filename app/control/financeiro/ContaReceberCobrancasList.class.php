<?php
/**
 * ContaReceberCobrancasList Listing
 * @author  <your name here>
 */
class ContaReceberCobrancasList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ContaReceberCobrancas');
        $this->form->setFormTitle('ContaReceberCobrancas');
        

        // create the form fields
        $id = new TEntry('id');
        $conta_receber_id = new TDBUniqueSearch('conta_receber_id', 'sample', 'ContaReceber', 'id', 'data_conta');
        $user_id = new TEntry('user_id');
        $status = new TEntry('status');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Conta Receber Id') ], [ $conta_receber_id ] );
        $this->form->addFields( [ new TLabel('User Id') ], [ $user_id ] );
        $this->form->addFields( [ new TLabel('Status') ], [ $status ] );


        // set sizes
        $id->setSize('100%');
        $conta_receber_id->setSize('100%');
        $user_id->setSize('100%');
        $status->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ContaReceberCobrancas_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ContaReceberCobrancasForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_conta_receber_id = new TDataGridColumn('conta_receber_id', 'Conta Receber Id', 'right');
        $column_user_id = new TDataGridColumn('user_id', 'User Id', 'right');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_conta_receber_id);
        $this->datagrid->addColumn($column_user_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_status);

        
        // create EDIT action
        $action_edit = new TDataGridAction(['ContaReceberCobrancasForm', 'onEdit']);
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
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // ////$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('sample'); // open a transaction with database
            $object = new ContaReceberCobrancas($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('ContaReceberCobrancasList_filter_id',   NULL);
        TSession::setValue('ContaReceberCobrancasList_filter_conta_receber_id',   NULL);
        TSession::setValue('ContaReceberCobrancasList_filter_user_id',   NULL);
        TSession::setValue('ContaReceberCobrancasList_filter_status',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('ContaReceberCobrancasList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->conta_receber_id) AND ($data->conta_receber_id)) {
            $filter = new TFilter('conta_receber_id', '=', "$data->conta_receber_id"); // create the filter
            TSession::setValue('ContaReceberCobrancasList_filter_conta_receber_id',   $filter); // stores the filter in the session
        }


        if (isset($data->user_id) AND ($data->user_id)) {
            $filter = new TFilter('user_id', 'like', "%{$data->user_id}%"); // create the filter
            TSession::setValue('ContaReceberCobrancasList_filter_user_id',   $filter); // stores the filter in the session
        }


        if (isset($data->status) AND ($data->status)) {
            $filter = new TFilter('status', 'like', "%{$data->status}%"); // create the filter
            TSession::setValue('ContaReceberCobrancasList_filter_status',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ContaReceberCobrancas_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'sample'
            TTransaction::open('sample');
            
            // creates a repository for ContaReceberCobrancas
            $repository = new TRepository('ContaReceberCobrancas');
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
            

            if (TSession::getValue('ContaReceberCobrancasList_filter_id')) {
                $criteria->add(TSession::getValue('ContaReceberCobrancasList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ContaReceberCobrancasList_filter_conta_receber_id')) {
                $criteria->add(TSession::getValue('ContaReceberCobrancasList_filter_conta_receber_id')); // add the session filter
            }


            if (TSession::getValue('ContaReceberCobrancasList_filter_user_id')) {
                $criteria->add(TSession::getValue('ContaReceberCobrancasList_filter_user_id')); // add the session filter
            }


            if (TSession::getValue('ContaReceberCobrancasList_filter_status')) {
                $criteria->add(TSession::getValue('ContaReceberCobrancasList_filter_status')); // add the session filter
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
    
    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('sample'); // open a transaction with database
            $object = new ContaReceberCobrancas($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
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
