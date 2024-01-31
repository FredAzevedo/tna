<?php
/**
 * ContaBancariaList Listing
 * @author  Fred Azv.
 */
class ContaBancariaList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ContaBancaria');
        $this->form->setFormTitle('ContaBancaria');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        $agencia = new TEntry('agencia');
        $conta = new TEntry('conta');
        $banco_id = new TDBUniqueSearch('banco_id', 'sample', 'Banco', 'id', 'num_banco');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Agência'), $agencia ],
                                       [ new TLabel('Conta'), $conta ],
                                       [ new TLabel('Nome do banco'), $banco_id ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-6'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ContaBancaria_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ContaBancariaForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_cod_banco = new TDataGridColumn('cod_banco', 'Cod', 'left');
        $column_agencia = new TDataGridColumn('agencia', 'Agencia', 'right');
        $column_agencia_dv = new TDataGridColumn('agencia_dv', 'Dv', 'Left');
        $column_conta = new TDataGridColumn('conta', 'Conta', 'right');
        $column_conta_dv = new TDataGridColumn('conta_dv', 'Dv', 'left');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'right');
        /*$column_cep = new TDataGridColumn('cep', 'Cep', 'left');
        $column_logradouro = new TDataGridColumn('logradouro', 'Logradouro', 'left');
        $column_numero = new TDataGridColumn('numero', 'Numero', 'right');
        $column_bairro = new TDataGridColumn('bairro', 'Bairro', 'left');
        $column_complemento = new TDataGridColumn('complemento', 'Complemento', 'left');
        $column_cidade = new TDataGridColumn('cidade', 'Cidade', 'left');
        $column_uf = new TDataGridColumn('uf', 'UF', 'left');
        $column_codMuni = new TDataGridColumn('codMuni', 'Codmuni', 'left');
        $column_tel_banco = new TDataGridColumn('tel_banco', 'Banco', 'right');
        */
        $column_gerente = new TDataGridColumn('gerente', 'Gerente', 'left');
        $column_tel_gerente = new TDataGridColumn('tel_gerente', 'Contato do gerente', 'right');
        /*
        $column_data_abaertura = new TDataGridColumn('data_abaertura', 'Data Abertura', 'left');
        $column_banco_id = new TDataGridColumn('banco->nome_banco', 'Banco', 'right');
        //$column_unit_id = new TDataGridColumn('unit_id', 'Unit Id', 'right');*/

        /*$column_data_abaertura->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });*/

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cod_banco);
        $this->datagrid->addColumn($column_agencia);
        $this->datagrid->addColumn($column_agencia_dv);
        $this->datagrid->addColumn($column_conta);
        $this->datagrid->addColumn($column_conta_dv);
        $this->datagrid->addColumn($column_tipo);
        /*$this->datagrid->addColumn($column_cep);
        $this->datagrid->addColumn($column_logradouro);
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_bairro);
        $this->datagrid->addColumn($column_complemento);
        $this->datagrid->addColumn($column_cidade);
        $this->datagrid->addColumn($column_uf);
        $this->datagrid->addColumn($column_codMuni);
        $this->datagrid->addColumn($column_tel_banco);
        */
        $this->datagrid->addColumn($column_gerente);
        $this->datagrid->addColumn($column_tel_gerente);
        /*
        $this->datagrid->addColumn($column_data_abaertura);
        $this->datagrid->addColumn($column_banco_id);
        //$this->datagrid->addColumn($column_unit_id);*/

        
        // create EDIT action
        $action_edit = new TDataGridAction(['ContaBancariaForm', 'onEdit']);
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
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
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
            $object = new ContaBancaria($key); // instantiates the Active Record
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
        TSession::setValue('ContaBancariaList_filter_id',   NULL);
        TSession::setValue('ContaBancariaList_filter_agencia',   NULL);
        TSession::setValue('ContaBancariaList_filter_conta',   NULL);
        TSession::setValue('ContaBancariaList_filter_banco_id',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('ContaBancariaList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->agencia) AND ($data->agencia)) {
            $filter = new TFilter('agencia', 'like', "%{$data->agencia}%"); // create the filter
            TSession::setValue('ContaBancariaList_filter_agencia',   $filter); // stores the filter in the session
        }


        if (isset($data->conta) AND ($data->conta)) {
            $filter = new TFilter('conta', 'like', "%{$data->conta}%"); // create the filter
            TSession::setValue('ContaBancariaList_filter_conta',   $filter); // stores the filter in the session
        }


        if (isset($data->banco_id) AND ($data->banco_id)) {
            $filter = new TFilter('banco_id', '=', "$data->banco_id"); // create the filter
            TSession::setValue('ContaBancariaList_filter_banco_id',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ContaBancaria_filter_data', $data);
        
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
            
            // creates a repository for ContaBancaria
            $repository = new TRepository('ContaBancaria');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('ContaBancariaList_filter_id')) {
                $criteria->add(TSession::getValue('ContaBancariaList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ContaBancariaList_filter_agencia')) {
                $criteria->add(TSession::getValue('ContaBancariaList_filter_agencia')); // add the session filter
            }


            if (TSession::getValue('ContaBancariaList_filter_conta')) {
                $criteria->add(TSession::getValue('ContaBancariaList_filter_conta')); // add the session filter
            }


            if (TSession::getValue('ContaBancariaList_filter_banco_id')) {
                $criteria->add(TSession::getValue('ContaBancariaList_filter_banco_id')); // add the session filter
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
            $object = new ContaBancaria($key, FALSE); // instantiates the Active Record
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
