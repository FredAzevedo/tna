<?php
use Adianti\Widget\Wrapper\TDBMultiSearch;
/**
 * LogUserList Listing
 * @author  Fred Azv.
 */
class LogUserList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_LogUser');
        $this->form->setFormTitle("Log's de Usuários");
        $this->form->setFieldSizes('100%');

        // create the form fields
        $usuario = new TDBCombo('usuario','sample','SystemUser','name','name','name');
        $programa = new TDBUniqueSearch('programa','sample','SystemProgram','name','name','name');
        $transaction_id = new TEntry('transaction_id');
        $log_day = new TEntry('log_day');
        $log_month = new TEntry('log_month');
        $log_year = new TEntry('log_year');

        $row = $this->form->addFields( [ new TLabel('Usuário'), $usuario ],    
                                       [ new TLabel('Programa'), $programa ],
                                       [ new TLabel('Dia'), $log_day ],
                                       [ new TLabel('Mês'), $log_month ],
                                       [ new TLabel('Ano'), $log_year ]
        );              
        $row->layout = ['col-sm-3','col-sm-3','col-sm-2','col-sm-2','col-sm-2'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'), new TAction(['LogUserForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_logdate = new TDataGridColumn('logdate', 'Logdate', 'left');
        $column_usuario = new TDataGridColumn('usuario', 'Usuário', 'left');
        $column_programa = new TDataGridColumn('programa', 'Programa', 'left');
        $column_transaction_id = new TDataGridColumn('transaction_id', 'Transação', 'left');
        $column_log_day = new TDataGridColumn('log_day', 'Dia', 'left');
        $column_log_month = new TDataGridColumn('log_month', 'Mês', 'left');
        $column_log_year = new TDataGridColumn('log_year', 'Ano', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_logdate);
        $this->datagrid->addColumn($column_usuario);
        $this->datagrid->addColumn($column_programa);
        $this->datagrid->addColumn($column_transaction_id);
        $this->datagrid->addColumn($column_log_day);
        $this->datagrid->addColumn($column_log_month);
        $this->datagrid->addColumn($column_log_year);


        $action1 = new TDataGridAction(['LogUserDetailList', 'onReload'], ['transaction_id'=>'{transaction_id}']);
        // $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'fa:search blue');
        // $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
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
            $object = new LogUser($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_usuario',   NULL);
        TSession::setValue(__CLASS__.'_filter_programa',   NULL);
        TSession::setValue(__CLASS__.'_filter_transaction_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_log_day',   NULL);
        TSession::setValue(__CLASS__.'_filter_log_month',   NULL);
        TSession::setValue(__CLASS__.'_filter_log_year',   NULL);

        if (isset($data->usuario) AND ($data->usuario)) {
            $filter = new TFilter('usuario', 'like', "%{$data->usuario}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_usuario',   $filter); // stores the filter in the session
        }


        if (isset($data->programa) AND ($data->programa)) {
            $filter = new TFilter('programa', 'like', "%{$data->programa}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_programa',   $filter); // stores the filter in the session
        }


        if (isset($data->transaction_id) AND ($data->transaction_id)) {
            $filter = new TFilter('transaction_id', '=', $data->transaction_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_transaction_id',   $filter); // stores the filter in the session
        }


        if (isset($data->log_day) AND ($data->log_day)) {
            $filter = new TFilter('log_day', 'like', "%{$data->log_day}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_log_day',   $filter); // stores the filter in the session
        }


        if (isset($data->log_month) AND ($data->log_month)) {
            $filter = new TFilter('log_month', 'like', "%{$data->log_month}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_log_month',   $filter); // stores the filter in the session
        }


        if (isset($data->log_year) AND ($data->log_year)) {
            $filter = new TFilter('log_year', 'like', "%{$data->log_year}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_log_year',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
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
            
            // creates a repository for LogUser
            $repository = new TRepository('LogUser');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_usuario')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_usuario')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_programa')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_programa')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_transaction_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_transaction_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_log_day')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_log_day')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_log_month')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_log_month')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_log_year')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_log_year')); // add the session filter
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
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
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
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
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
            $object = new LogUser($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
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
