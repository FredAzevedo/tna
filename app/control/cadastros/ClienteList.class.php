<?php
/**
 * ClienteList Listing
 * @author Fred Azv.
 */

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
class ClienteList extends TPage
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
    public function __construct($param)
    {
        parent::__construct();
        
        $this->setDatabase('sample');            // defines the database
        $this->setActiveRecord('Cliente');   // defines the active record
        $this->setDefaultOrder('id', 'desc');         // defines the default order
        //$this->setCriteria($criteria); // define a standard filter
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Cliente');
        $this->form->setFormTitle('Cliente');
        
        // create the form fields
        $razao_social = new TDBUniqueSearch('razao_social', 'sample', 'Cliente', 'razao_social', 'razao_social');
        $razao_social->setMinLength(0);

        $nome_fantasia = new TDBUniqueSearch('nome_fantasia', 'sample', 'Cliente', 'nome_fantasia', 'nome_fantasia');
        $nome_fantasia->setMinLength(0);

   
        // add the fields
        $row = $this->form->addFields( [ new TLabel('Nome/Razão Social'), $razao_social ],
                                       [ new TLabel('Nome Fantasia'), $nome_fantasia ]);
        $row->layout = ['col-sm-6', 'col-sm-6'];


        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Cliente_filter_data') );
        $this->form->setData( TSession::setValue('ClienteList', parse_url($_SERVER['REQUEST_URI'])) );

        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ClienteForm', 'onEdit']), 'fa:plus green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_razao_social = new TDataGridColumn('razao_social', 'Nome/Razão Social', 'left');
        $column_cpf_cnpj = new TDataGridColumn('cpf_cnpj', 'CPF/CNPJ', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_razao_social);
        $this->datagrid->addColumn($column_cpf_cnpj);
  
        // create EDIT action
        $action_edit = new TDataGridAction(['ClienteForm', 'onEdit']);
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
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
        
        // search box
        $input_search = new TEntry('input_search');
        $input_search->placeholder = _t('Search');
        $input_search->setSize('100%');
        
        $panel = new TPanelGroup();
        $panel->addHeaderWidget($input_search);
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        //$panel->addFooter('footer');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    

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
            $object = new Cliente($key, FALSE); // instantiates the Active Record
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

    public function onSearch()
    {
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('ClienteList_filtro_nome_fantasia',   NULL);
        TSession::setValue('ClienteList_filtro_razao_social',   NULL);


        if (isset($data->nome_fantasia) AND ($data->nome_fantasia)) {
            $filter = new TFilter('nome_fantasia', 'like', "%{$data->nome_fantasia}%"); // create the filter
            TSession::setValue('ClienteList_filtro_nome_fantasia',   $filter); // stores the filter in the session
        }

        if (isset($data->razao_social) AND ($data->razao_social)) {
            $filter = new TFilter('razao_social', 'like', "%{$data->razao_social}%"); // create the filter
            TSession::setValue('ClienteList_filtro_razao_social',   $filter); // stores the filter in the session
        }

        if (isset($data->cpf_cnpj) AND ($data->cpf_cnpj)) {
            $filter = new TFilter('cpf_cnpj', 'like', "%{$data->cpf_cnpj}%"); // create the filter
            TSession::setValue('ClienteList_filtro_cpf_cnpj',   $filter); // stores the filter in the session
        }

        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ClienteList_filter_data', $data);
        
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
            
            $repository = new TRepository('Cliente');
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
            
            TSession::setValue('ClienteList', parse_url($_SERVER['REQUEST_URI']));

            if (TSession::getValue('ClienteList_filtro_nome_fantasia')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_nome_fantasia')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_razao_social')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_razao_social')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_cpf_cnpj')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_cpf_cnpj')); // add the session filter
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
}
