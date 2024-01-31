<?php
/**
 * ComissaoTabelaList Listing
 * @author  <your name here>
 */
class ComissaoTabelaList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ComissaoTabela');
        $this->form->setFormTitle('Tabela de Comissão');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        $descricao = new TEntry('descricao');
        $forma_comissao = new TCombo('forma_comissao');
        $forma_comissao->addItems(Utilidades::tipo_comissao());
        $valor_comissao = new TNumeric('valor_comissao', 2, ',', '.', true);
        $observacao = new TEntry('observacao');


        // add the fieldsß
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Descrição'), $descricao ],
                                       [ new TLabel('Forma Comissão'), $forma_comissao ],
                                       [ new TLabel('Valor Comissão'), $valor_comissao ]);
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-3', 'col-sm-3'];
        

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ComissaoTabela_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ComissaoTabelaForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');
        $column_forma_comissao = new TDataGridColumn('valorComissao', 'Forma da Comissão', 'left');
        $column_valor_comissao = new TDataGridColumn('valor_comissao', 'Valor da Comissão', 'right');
        $column_observacao = new TDataGridColumn('observacao', 'Observação', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_forma_comissao);
        $this->datagrid->addColumn($column_valor_comissao);
        $this->datagrid->addColumn($column_observacao);

        
        // create EDIT action
        $action_edit = new TDataGridAction(['ComissaoTabelaForm', 'onEdit']);
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

        /*$container->adianti_target_container = 'ComissaoTabelaList';
        $container->adianti_target_title = 'Tabela de Comissão ';*/
        
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
            $object = new ComissaoTabela($key); // instantiates the Active Record
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
        TSession::setValue('ComissaoTabelaList_filter_id',   NULL);
        TSession::setValue('ComissaoTabelaList_filter_descricao',   NULL);
        TSession::setValue('ComissaoTabelaList_filter_forma_comissao',   NULL);
        TSession::setValue('ComissaoTabelaList_filter_valor_comissao',   NULL);
        TSession::setValue('ComissaoTabelaList_filter_observacao',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('ComissaoTabelaList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
            TSession::setValue('ComissaoTabelaList_filter_descricao',   $filter); // stores the filter in the session
        }


        if (isset($data->forma_comissao) AND ($data->forma_comissao)) {
            $filter = new TFilter('forma_comissao', 'like', "%{$data->forma_comissao}%"); // create the filter
            TSession::setValue('ComissaoTabelaList_filter_forma_comissao',   $filter); // stores the filter in the session
        }


        if (isset($data->valor_comissao) AND ($data->valor_comissao)) {
            $filter = new TFilter('valor_comissao', 'like', "%{$data->valor_comissao}%"); // create the filter
            TSession::setValue('ComissaoTabelaList_filter_valor_comissao',   $filter); // stores the filter in the session
        }


        if (isset($data->observacao) AND ($data->observacao)) {
            $filter = new TFilter('observacao', 'like', "%{$data->observacao}%"); // create the filter
            TSession::setValue('ComissaoTabelaList_filter_observacao',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ComissaoTabela_filter_data', $data);
        
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
            
            // creates a repository for ComissaoTabela
            $repository = new TRepository('ComissaoTabela');
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
            

            if (TSession::getValue('ComissaoTabelaList_filter_id')) {
                $criteria->add(TSession::getValue('ComissaoTabelaList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ComissaoTabelaList_filter_descricao')) {
                $criteria->add(TSession::getValue('ComissaoTabelaList_filter_descricao')); // add the session filter
            }


            if (TSession::getValue('ComissaoTabelaList_filter_forma_comissao')) {
                $criteria->add(TSession::getValue('ComissaoTabelaList_filter_forma_comissao')); // add the session filter
            }


            if (TSession::getValue('ComissaoTabelaList_filter_valor_comissao')) {
                $criteria->add(TSession::getValue('ComissaoTabelaList_filter_valor_comissao')); // add the session filter
            }


            if (TSession::getValue('ComissaoTabelaList_filter_observacao')) {
                $criteria->add(TSession::getValue('ComissaoTabelaList_filter_observacao')); // add the session filter
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
            $object = new ComissaoTabela($key, FALSE); // instantiates the Active Record
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
