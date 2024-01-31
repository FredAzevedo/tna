<?php
/**
 * EstoqueLoteList Listing
 * @author  Fred Azv.
 */
class EstoqueLoteList extends TWindow
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
        parent::setSize(0.55,NULL);// Manipula a largura do Twindow
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_EstoqueLote');
        $this->form->setFormTitle('Lotes do Produto');

        // create the form fields
        $unit_produto = new TCriteria();
        $unit_produto->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        /*$produto_id = new TDBUniqueSearch('produto_id', 'sample', 'Produto', 'id','cod_referencia','cod_referencia', $unit_produto);
        $produto_id->setMask('{cod_referencia} - {nome_produto}');*/
        $lote = new TEntry('lote');
        $vencimento = new TDate('vencimento');
        $vencimento->setDatabaseMask('yyyy-mm-dd');
        $vencimento->setMask('dd/mm/yyyy');


        $row = $this->form->addFields( [ new TLabel('Lote'), $lote ],
                                       [ new TLabel('Vencimento'), $vencimento ]);
        $row->layout = ['col-sm-8', 'col-sm-4'];

        // set sizes
        //$produto_id->setSize('100%');
        $lote->setSize('100%');
        $vencimento->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('EstoqueLote_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'), new TAction(['EstoqueLoteForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        //$column_produto_id = new TDataGridColumn('produto->nome_produto', 'Produto', 'left');
        $column_lote = new TDataGridColumn('lote', 'Lote', 'right');
        $column_vencimento = new TDataGridColumn('vencimento', 'Vencimento', 'right');
        $column_saldo = new TDataGridColumn('saldo', 'Saldo', 'right');
        //$column_created_at = new TDataGridColumn('created_at', 'Criado', 'right');
        $column_updated_at = new TDataGridColumn('updated_at', 'Movimentado', 'right');


        // add the columns to the DataGrid
        //$this->datagrid->addColumn($column_produto_id);
        $this->datagrid->addColumn($column_lote);
        $this->datagrid->addColumn($column_vencimento);
        $this->datagrid->addColumn($column_saldo);
        //$this->datagrid->addColumn($column_created_at);
        $this->datagrid->addColumn($column_updated_at);

        $column_vencimento->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y h:m:s');
        });

        /*$column_created_at->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y h:m:s');
        });*/

        $column_updated_at->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y h:m:s');
        });
        
        $column_saldo->setTransformer( function($value, $object, $row) {
            return number_format($value, 0, ',', '.');
        });

        $column_saldo->setTotalFunction( function($values) {
            return array_sum((array) $values);
        });
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //////$container->add(new TXMLBreadCrumb('menu.xml', 'EstoqueList'));
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
            $object = new EstoqueLote($key); // instantiates the Active Record
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
    public function onSearch( $param )
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('EstoqueLoteList_filter_produto_id',   NULL);
        TSession::setValue('EstoqueLoteList_filter_lote',   NULL);
        TSession::setValue('EstoqueLoteList_filter_vencimento',   NULL);

        /*if (isset($data->produto_id) AND ($data->produto_id)) {
            $filter = new TFilter('produto_id', '=', "$data->produto_id"); // create the filter
            TSession::setValue('EstoqueLoteList_filter_produto_id',   $filter); // stores the filter in the session
        }*/

        if (isset($data->lote) AND ($data->lote)) {
            $filter = new TFilter('lote', 'like', "%{$data->lote}%"); // create the filter
            TSession::setValue('EstoqueLoteList_filter_lote',   $filter); // stores the filter in the session
        }


        if (isset($data->vencimento) AND ($data->vencimento)) {
            $filter = new TFilter('vencimento', 'like', "%{$data->vencimento}%"); // create the filter
            TSession::setValue('EstoqueLoteList_filter_vencimento',   $filter); // stores the filter in the session
        }

        $filter = new TFilter('unit_id','=', TSession::getvalue('userunitid'));
        $filter = new TFilter('produto_id',  '= ', $param['produto_id']);

        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('EstoqueLote_filter_data', $data);
        
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
            
            // creates a repository for EstoqueLote
            $repository = new TRepository('EstoqueLote');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;

            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));
            $criteria->add(new TFilter('produto_id',  '= ', $param['produto_id']));

            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('EstoqueLoteList_filter_produto_id')) {
                $criteria->add(TSession::getValue('EstoqueLoteList_filter_produto_id')); // add the session filter
            }


            if (TSession::getValue('EstoqueLoteList_filter_lote')) {
                $criteria->add(TSession::getValue('EstoqueLoteList_filter_lote')); // add the session filter
            }


            if (TSession::getValue('EstoqueLoteList_filter_vencimento')) {
                $criteria->add(TSession::getValue('EstoqueLoteList_filter_vencimento')); // add the session filter
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
            $object = new EstoqueLote($key, FALSE); // instantiates the Active Record
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
