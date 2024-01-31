<?php
/**
 * CartaoApiList Listing
 * @author  <your name here>
 */
class CartaoApiList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_CartaoApi');
        $this->form->setFormTitle('CartaoApi');
        

        // create the form fields
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'tipo');
        $valor = new TEntry('valor');
        $data_compra = new TEntry('data_compra');
        $pedido_numero = new TEntry('pedido_numero');
        $data_transacao = new TEntry('data_transacao');
        $hora_transacao = new TEntry('hora_transacao');


        // add the fields
        $this->form->addFields( [ new TLabel('Cliente Id') ], [ $cliente_id ] );
        $this->form->addFields( [ new TLabel('Valor') ], [ $valor ] );
        $this->form->addFields( [ new TLabel('Data Compra') ], [ $data_compra ] );
        $this->form->addFields( [ new TLabel('Pedido Numero') ], [ $pedido_numero ] );
        $this->form->addFields( [ new TLabel('Data Transacao') ], [ $data_transacao ] );
        $this->form->addFields( [ new TLabel('Hora Transacao') ], [ $hora_transacao ] );


        // set sizes
        $cliente_id->setSize('100%');
        $valor->setSize('100%');
        $data_compra->setSize('100%');
        $pedido_numero->setSize('100%');
        $data_transacao->setSize('100%');
        $hora_transacao->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['CartaoApiForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_user_id = new TDataGridColumn('user_id', 'User Id', 'right');
        $column_cliente_id = new TDataGridColumn('cliente_id', 'Cliente Id', 'right');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'left');
        $column_valor_liquido = new TDataGridColumn('valor_liquido', 'Valor Liquido', 'left');
        $column_parcelas = new TDataGridColumn('parcelas', 'Parcelas', 'right');
        $column_data_compra = new TDataGridColumn('data_compra', 'Data Compra', 'left');
        $column_pedido_numero = new TDataGridColumn('pedido_numero', 'Pedido Numero', 'left');
        $column_descricao_pagamento = new TDataGridColumn('descricao_pagamento', 'Descricao Pagamento', 'left');
        $column_previsao_credito = new TDataGridColumn('previsao_credito', 'Previsao Credito', 'left');
        $column_msg = new TDataGridColumn('msg', 'Msg', 'left');
        $column_bandeira = new TDataGridColumn('bandeira', 'Bandeira', 'left');
        $column_autorizacao = new TDataGridColumn('autorizacao', 'Autorizacao', 'left');
        $column_tarifa = new TDataGridColumn('tarifa', 'Tarifa', 'left');
        $column_taxa = new TDataGridColumn('taxa', 'Taxa', 'left');
        $column_msg_erro = new TDataGridColumn('msg_erro', 'Msg Erro', 'left');
        $column_msg_erro_estorno = new TDataGridColumn('msg_erro_estorno', 'Msg Erro Estorno', 'left');
        $column_data_cancelamento = new TDataGridColumn('data_cancelamento', 'Data Cancelamento', 'left');
        $column_motivo_cancelamento = new TDataGridColumn('motivo_cancelamento', 'Motivo Cancelamento', 'left');
        $column_data_transacao = new TDataGridColumn('data_transacao', 'Data Transacao', 'left');
        $column_hora_transacao = new TDataGridColumn('hora_transacao', 'Hora Transacao', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_user_id);
        $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_valor_liquido);
        $this->datagrid->addColumn($column_parcelas);
        $this->datagrid->addColumn($column_data_compra);
        $this->datagrid->addColumn($column_pedido_numero);
        $this->datagrid->addColumn($column_descricao_pagamento);
        $this->datagrid->addColumn($column_previsao_credito);
        $this->datagrid->addColumn($column_msg);
        $this->datagrid->addColumn($column_bandeira);
        $this->datagrid->addColumn($column_autorizacao);
        $this->datagrid->addColumn($column_tarifa);
        $this->datagrid->addColumn($column_taxa);
        $this->datagrid->addColumn($column_msg_erro);
        $this->datagrid->addColumn($column_msg_erro_estorno);
        $this->datagrid->addColumn($column_data_cancelamento);
        $this->datagrid->addColumn($column_motivo_cancelamento);
        $this->datagrid->addColumn($column_data_transacao);
        $this->datagrid->addColumn($column_hora_transacao);


        $action1 = new TDataGridAction(['CartaoApiForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
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
            $object = new CartaoApi($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_cliente_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_valor',   NULL);
        TSession::setValue(__CLASS__.'_filter_data_compra',   NULL);
        TSession::setValue(__CLASS__.'_filter_pedido_numero',   NULL);
        TSession::setValue(__CLASS__.'_filter_data_transacao',   NULL);
        TSession::setValue(__CLASS__.'_filter_hora_transacao',   NULL);

        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', '=', $data->cliente_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_cliente_id',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_valor',   $filter); // stores the filter in the session
        }


        if (isset($data->data_compra) AND ($data->data_compra)) {
            $filter = new TFilter('data_compra', 'like', "%{$data->data_compra}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_data_compra',   $filter); // stores the filter in the session
        }


        if (isset($data->pedido_numero) AND ($data->pedido_numero)) {
            $filter = new TFilter('pedido_numero', 'like', "%{$data->pedido_numero}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_pedido_numero',   $filter); // stores the filter in the session
        }


        if (isset($data->data_transacao) AND ($data->data_transacao)) {
            $filter = new TFilter('data_transacao', 'like', "%{$data->data_transacao}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_data_transacao',   $filter); // stores the filter in the session
        }


        if (isset($data->hora_transacao) AND ($data->hora_transacao)) {
            $filter = new TFilter('hora_transacao', 'like', "%{$data->hora_transacao}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_hora_transacao',   $filter); // stores the filter in the session
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
            
            // creates a repository for CartaoApi
            $repository = new TRepository('CartaoApi');
            $limit = 20;
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
            

            if (TSession::getValue(__CLASS__.'_filter_cliente_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cliente_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_valor')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_valor')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_data_compra')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_data_compra')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_pedido_numero')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_pedido_numero')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_data_transacao')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_data_transacao')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_hora_transacao')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_hora_transacao')); // add the session filter
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
            $object = new CartaoApi($key, FALSE); // instantiates the Active Record
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
