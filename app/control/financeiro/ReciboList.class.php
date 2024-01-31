<?php
/**
 * ContaReceberList Listing
 * @author  Fred Azv.
 */
class ReciboList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ContaReceber');
        $this->form->setFormTitle('Recibo de Receitas');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->setValue(date('d/m/Y'));
        $data_vencimento->addValidation('Competência', new TRequiredValidator);
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');
        $descricao = new TEntry('descricao');
        $valor = new TNumeric('valor',2,',','.',true);
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $relatorio_customizado_id = new TDBCombo('relatorio_customizado_id', 'sample', 'RelatorioCustomizado', 'id', 'nome');


        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Unidade'), $cliente_id ],
                                       [ new TLabel('Competência'), $data_vencimento ]
        );
        $row->layout = ['col-sm-2','col-sm-6', 'col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Referente'), $descricao ],
                                       [ new TLabel('Valor'), $valor ]
        );
        $row->layout = ['col-sm-10','col-sm-2'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ContaReceber_filter_data') );
        $this->form->setData( TSession::setValue('ReciboList', parse_url($_SERVER['REQUEST_URI'])) );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'), new TAction(['ReciboForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_data_vencimento = new TDataGridColumn('data_vencimento', 'Data', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Referente', 'left');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'left');
        $column_valor_pago = new TDataGridColumn('valor', 'Valor Pago', 'left');
        $column_cliente_id = new TDataGridColumn('cliente->razao_social', 'Cliente', 'left');
        $column_relatorio_customizado_id = new TDataGridColumn('relatorio_customizado_id', 'Relatorio Customizado Id', 'right');
        $column_baixa = new TDataGridColumn('StatusRecibo', 'Status', 'right');
        $column_tipo_pgto_id = new TDataGridColumn('tipo_pgto->nome', 'Tipo PGTO', 'right');
        $column_recibo = new TDataGridColumn('recibo', 'Recibo?', 'right');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data_vencimento);
        $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_valor_pago);
        $this->datagrid->addColumn($column_baixa);
        $this->datagrid->addColumn($column_tipo_pgto_id);
        $this->datagrid->addColumn($column_recibo);
        
        $column_data_vencimento->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });
        
        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };

        $column_valor->setTransformer( $format_value );
        $column_valor_pago->setTransformer( $format_value );

        $column_id->setTransformer(array($this, 'corStatus'));
        
        // create EDIT action
        $action_edit = new TDataGridAction(['ReciboForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fas:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);


        $action1 = new TDataGridAction(array('ReciboContaReceberBaixaForm', 'onEdit'));
        $action1->setLabel('Gerar Recido');
        $action1->setImage('fas:print black');
        $action1->setField('id');
        $action1->setDisplayCondition( array($this, 'displayColumn') );

        $action2 = new TDataGridAction(array('RelReciboReceitaAvulso', 'onViewRecibo'));
        $action2->setLabel('Gerar Recido Avulso');
        $action2->setImage('fa:sort-amount-asc black');
        $action2->setField('id');
        //$action2->setDisplayCondition( array($this, 'displayColumn') );
        
        $action_group = new TDataGridActionGroup('', 'fas:cog');

        $action_group->addHeader('Opções');
        $action_group->addAction($action1);
        $action_group->addAction($action2);

        $this->datagrid->addActionGroup($action_group);


        // create DELETE action
        /*$action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);*/
        
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
    
    public function corStatus($id, $object, $row)
    {   

        $object = new ContaReceber($id);
        $baixa = $object->baixa;

        $data_vencimento = $object->data_vencimento;

        if($data_vencimento < date('Y-m-d')){

            $row->style = "background: #ffa7a7";
        }
        
        switch ($baixa) {

            case "S":
                $row->style = "background: #a7ffb1";
                return $object->id;
                break;
            case "N":
                //$row->style = "background: #ffa7a7";
                return $object->id;
                break;
        }
    }


    public function displayColumn( $object )
    {
        
        if($object->baixa == 'N')
        {
            return TRUE;
        }
        return FALSE;
    }


    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('sample'); // open a transaction with database
            $object = new ContaReceber($key); // instantiates the Active Record
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
        TSession::setValue('ContaReceberList_filter_id',   NULL);
        TSession::setValue('ContaReceberList_filter_data_vencimento',   NULL);
        TSession::setValue('ContaReceberList_filter_descricao',   NULL);
        TSession::setValue('ContaReceberList_filter_valor',   NULL);
        TSession::setValue('ContaReceberList_filter_cliente_id',   NULL);
        TSession::setValue('ContaReceberList_filter_relatorio_customizado_id',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('ContaReceberList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->data_vencimento) AND ($data->data_vencimento)) {
            $filter = new TFilter('data_vencimento', 'like', "%{$data->data_vencimento}%"); // create the filter
            TSession::setValue('ContaReceberList_filter_data_vencimento',   $filter); // stores the filter in the session
        }


        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
            TSession::setValue('ContaReceberList_filter_descricao',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue('ContaReceberList_filter_valor',   $filter); // stores the filter in the session
        }


        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', '=', "$data->cliente_id"); // create the filter
            TSession::setValue('ContaReceberList_filter_cliente_id',   $filter); // stores the filter in the session
        }


        if (isset($data->relatorio_customizado_id) AND ($data->relatorio_customizado_id)) {
            $filter = new TFilter('relatorio_customizado_id', '=', "$data->relatorio_customizado_id"); // create the filter
            TSession::setValue('ContaReceberList_filter_relatorio_customizado_id',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ContaReceber_filter_data', $data);
        
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
            
            // creates a repository for ContaReceber
            $repository = new TRepository('ContaReceber');
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
            

            if (TSession::getValue('ContaReceberList_filter_id')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_id')); // add the session filter
            }

            if (TSession::getValue('ContaReceberList_filter_data_vencimento')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_data_vencimento')); // add the session filter
            }


            if (TSession::getValue('ContaReceberList_filter_descricao')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_descricao')); // add the session filter
            }


            if (TSession::getValue('ContaReceberList_filter_valor')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_valor')); // add the session filter
            }


            if (TSession::getValue('ContaReceberList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_cliente_id')); // add the session filter
            }


            if (TSession::getValue('ContaReceberList_filter_relatorio_customizado_id')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_relatorio_customizado_id')); // add the session filter
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
            $object = new ContaReceber($key, FALSE); // instantiates the Active Record
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
