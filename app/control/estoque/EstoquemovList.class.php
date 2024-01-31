<?php
/**
 * EstoquemovList Listing
 * @author  Fred Azv.
 */
class EstoquemovList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Estoquemov');
        $this->form->setFormTitle('Inventário (Lançamentos de movimentação em estoque)');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);

        $produto_id = new TDBUniqueSearch('produto_id', 'sample', 'Produto', 'id', 'produto_grupo_id');

        $local = new TDBCombo('local','sample','SystemUnit','cnpj','unidade','unidade');
        //$local->setValue(TSession::getValue('userunitid'));

        $tipo = new TCombo('tipo');
        $combo_tipos = array();
        $combo_tipos['E'] = 'Entrada';
        $combo_tipos['S'] = 'Saída';
        $tipo->addItems($combo_tipos);

        $quantidade = new TEntry('quantidade');
        $saldo = new TEntry('saldo');
        $valor = new TEntry('valor');
        
        $unit_produto = new TCriteria();
        $unit_produto->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $produto_id = new TDBUniqueSearch('produto_id', 'sample', 'Produto', 'id','nome_produto','nome_produto', $unit_produto);
        $produto_id->setMask('{cod_referencia} - {nome_produto}');

        $updated_at = new TEntry('updated_at');
        


        $row = $this->form->addFields( [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Tipo'), $tipo ],
                                       [ new TLabel('Local'), $local ],
                                       [ new TLabel('Produto'), $produto_id ]);
        $row->layout = ['col-sm-3', 'col-sm-2', 'col-sm-2', 'col-sm-5'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Estoquemov_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Fazer Lançamento', new TAction(['EstoquemovForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        //$column_unit_id = new TDataGridColumn('system_unit->unidade', 'Unidade', 'left');
        $column_cod_referencia = new TDataGridColumn('produto->cod_referencia', 'COD/REF', 'left');
        $column_produto_id = new TDataGridColumn('produto->nome_produto', 'Produto', 'left');
        $column_local = new TDataGridColumn('localidade->nome_fantasia', 'Local', 'left');
        $column_tipo = new TDataGridColumn('tipomov', 'Tipo', 'left');
        $column_quantidade = new TDataGridColumn('quantidade', 'Quantidade', 'right');
        $column_saldo = new TDataGridColumn('saldo', 'Saldo', 'right');
        //$column_valor = new TDataGridColumn('valor', 'Valor', 'right');
        //$column_referencia = new TDataGridColumn('referencia', 'Referência', 'left');
        $column_updated_at = new TDataGridColumn('updated_at', 'Atualizado', 'right');

        $column_quantidade->setTransformer( function($value, $object, $row) {
            return number_format($value, 0, ',', '.');
        });

        $column_saldo->setTransformer( function($value, $object, $row) {
            return number_format($value, 0, ',', '.');
        });

        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_unit_id);
        $this->datagrid->addColumn($column_cod_referencia);
        $this->datagrid->addColumn($column_produto_id);
        $this->datagrid->addColumn($column_local);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_quantidade);
        $this->datagrid->addColumn($column_saldo);
        //$this->datagrid->addColumn($column_valor);
        //$this->datagrid->addColumn($column_referencia);
        $this->datagrid->addColumn($column_updated_at);

        $column_updated_at->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y h:m:s');
        });

        // create EDIT action
        $action_edit = new TDataGridAction(['EstoquemovForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        //$this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        //$this->datagrid->addAction($action_del);
        
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
            $object = new Estoquemov($key); // instantiates the Active Record
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
        TSession::setValue('EstoquemovList_filter_unit_id',   NULL);
        TSession::setValue('EstoquemovList_filter_produto_id',   NULL);
        TSession::setValue('EstoquemovList_filter_local',   NULL);
        TSession::setValue('EstoquemovList_filter_tipo',   NULL);
        TSession::setValue('EstoquemovList_filter_quantidade',   NULL);
        TSession::setValue('EstoquemovList_filter_saldo',   NULL);
        TSession::setValue('EstoquemovList_filter_valor',   NULL);
        TSession::setValue('EstoquemovList_filter_referencia',   NULL);
        TSession::setValue('EstoquemovList_filter_updated_at',   NULL);

        $filter = new TFilter('unit_id','=', TSession::getvalue('userunitid'));

        if (isset($data->unit_id) AND ($data->unit_id)) {
            $filter = new TFilter('unit_id', 'like', "%{$data->unit_id}%"); // create the filter
            TSession::setValue('EstoquemovList_filter_unit_id',   $filter); // stores the filter in the session
        }


        if (isset($data->produto_id) AND ($data->produto_id)) {
            $filter = new TFilter('produto_id', '=', "$data->produto_id"); // create the filter
            TSession::setValue('EstoquemovList_filter_produto_id',   $filter); // stores the filter in the session
        }


        if (isset($data->local) AND ($data->local)) {
            $filter = new TFilter('local', 'like', "%{$data->local}%"); // create the filter
            TSession::setValue('EstoquemovList_filter_local',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo) AND ($data->tipo)) {
            $filter = new TFilter('tipo', '=', "$data->tipo"); // create the filter
            TSession::setValue('EstoquemovList_filter_tipo',   $filter); // stores the filter in the session
        }


        if (isset($data->quantidade) AND ($data->quantidade)) {
            $filter = new TFilter('quantidade', 'like', "%{$data->quantidade}%"); // create the filter
            TSession::setValue('EstoquemovList_filter_quantidade',   $filter); // stores the filter in the session
        }


        if (isset($data->saldo) AND ($data->saldo)) {
            $filter = new TFilter('saldo', 'like', "%{$data->saldo}%"); // create the filter
            TSession::setValue('EstoquemovList_filter_saldo',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue('EstoquemovList_filter_valor',   $filter); // stores the filter in the session
        }


        if (isset($data->referencia) AND ($data->referencia)) {
            $filter = new TFilter('referencia', 'like', "%{$data->referencia}%"); // create the filter
            TSession::setValue('EstoquemovList_filter_referencia',   $filter); // stores the filter in the session
        }


        if (isset($data->updated_at) AND ($data->updated_at)) {
            $filter = new TFilter('updated_at', 'like', "%{$data->updated_at}%"); // create the filter
            TSession::setValue('EstoquemovList_filter_updated_at',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Estoquemov_filter_data', $data);
        
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
            
            // creates a repository for Estoquemov
            $repository = new TRepository('Estoquemov');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('EstoquemovList_filter_unit_id')) {
                $criteria->add(TSession::getValue('EstoquemovList_filter_unit_id')); // add the session filter
            }


            if (TSession::getValue('EstoquemovList_filter_produto_id')) {
                $criteria->add(TSession::getValue('EstoquemovList_filter_produto_id')); // add the session filter
            }


            if (TSession::getValue('EstoquemovList_filter_local')) {
                $criteria->add(TSession::getValue('EstoquemovList_filter_local')); // add the session filter
            }


            if (TSession::getValue('EstoquemovList_filter_tipo')) {
                $criteria->add(TSession::getValue('EstoquemovList_filter_tipo')); // add the session filter
            }


            if (TSession::getValue('EstoquemovList_filter_quantidade')) {
                $criteria->add(TSession::getValue('EstoquemovList_filter_quantidade')); // add the session filter
            }


            if (TSession::getValue('EstoquemovList_filter_saldo')) {
                $criteria->add(TSession::getValue('EstoquemovList_filter_saldo')); // add the session filter
            }


            if (TSession::getValue('EstoquemovList_filter_valor')) {
                $criteria->add(TSession::getValue('EstoquemovList_filter_valor')); // add the session filter
            }


            if (TSession::getValue('EstoquemovList_filter_referencia')) {
                $criteria->add(TSession::getValue('EstoquemovList_filter_referencia')); // add the session filter
            }


            if (TSession::getValue('EstoquemovList_filter_updated_at')) {
                $criteria->add(TSession::getValue('EstoquemovList_filter_updated_at')); // add the session filter
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
            $object = new Estoquemov($key, FALSE); // instantiates the Active Record
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
