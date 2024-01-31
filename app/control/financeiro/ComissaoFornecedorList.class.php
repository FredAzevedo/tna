<?php
/**
 * ComissaoFornecedorList Listing
 * @author  <your name here>
 */
class ComissaoFornecedorList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ComissaoFornecedor');
        $this->form->setFormTitle('Comissão de Fornecedor');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');

        $data_faturamento = new TDate('data_faturamento');
        $data_faturamento->setDatabaseMask('yyyy-mm-dd');
        $data_faturamento->setMask('dd/mm/yyyy');

        $valor_faturamento = new TNumeric('valor_faturamento',2,',','.',true);
        $taxa_comissao = new TNumeric('taxa_comissao',2,',','.',true);
        $valor_comissao = new TNumeric('valor_comissao',2,',','.',true);
        $pago = new TEntry('pago');
        
        $tipo = new TCombo('tipo');
        $combo_tipos = array();
        $combo_tipos['P'] = '(%) Em Porcentagem';
        $combo_tipos['D'] = '(R$) Em Dinheiro)';
        $tipo->addItems($combo_tipos);

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $cliente_id = new TDBCombo('cliente_id','sample','Cliente','id','nome_fantasia','nome_fantasia');

        $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'nome_fantasia');


        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Data Faturado'), $data_faturamento ],
                                       [ new TLabel('Valor Faturado'), $valor_faturamento ],
                                       [ new TLabel('Taxa de Comissão'), $taxa_comissao ],
                                       [ new TLabel('Valor da Comissão'), $valor_comissao ],
                                       [ new TLabel('Foi Pago?'), $pago ]
        );              
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Tipo'), $tipo ],    
                                       [ new TLabel('Usuário comissionado'), $fornecedor_id ],
                                       [ new TLabel('Cliente'), $cliente_id ]
        );              
        $row->layout = ['col-sm-2', 'col-sm-5','col-sm-5'];

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ComissaoFornecedor_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ComissaoFornecedorForm', 'onEdit']), 'fa:plus green');
        $btn2 = $this->form->addAction('Gerar Comissões', new TAction([$this, 'onGerarRelatorioComissao']), 'fa:file-pdf-o');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_data_faturamento = new TDataGridColumn('data_faturamento', 'Data Faturado', 'center');
        $column_valor_faturamento = new TDataGridColumn('valor_faturamento', 'Valor Faturado', 'right');
        $column_taxa_comissao = new TDataGridColumn('taxa_comissao', 'Taxa de Comissão', 'right');
        $column_valor_comissao = new TDataGridColumn('valor_comissao', 'Valor da Comissão', 'right');
        $column_pago = new TDataGridColumn('pago', 'Pago?', 'left');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'left');
        $column_unit_id = new TDataGridColumn('unit_id', 'Unit Id', 'right');
        $column_fornecedor_id = new TDataGridColumn('fornecedor->nome_fantasia', 'Fornecedor', 'left');
        $column_cliente_id = new TDataGridColumn('cliente->nome_fantasia', 'Cliente', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_fornecedor_id);
        $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_data_faturamento);
        $this->datagrid->addColumn($column_valor_faturamento);
        $this->datagrid->addColumn($column_taxa_comissao);
        $this->datagrid->addColumn($column_valor_comissao);
        //$this->datagrid->addColumn($column_pago);
        /*$this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_unit_id);*/
        

        $column_data_faturamento->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });


        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };     

        $column_valor_faturamento->setTransformer( $format_value );
        $column_valor_comissao->setTransformer( $format_value );

        
        // create EDIT action
        $action_edit = new TDataGridAction(['ComissaoFornecedorForm', 'onEdit']);
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
    
    
    public function onGerarRelatorioComissao( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        $fornecedor_id = new TDBCombo('fornecedor_id','sample','Fornecedor','id','nome_fantasia','nome_fantasia');
        $fornecedor_id->addValidation('Indicador', new TRequiredValidator);
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('Indicador:', $fornecedor_id);
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarRelComissaoFornecedor')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Comissão do Indicador)', $report);
    }

    public function onGerarRelComissaoFornecedor( $param )
    {
       $gerar = new RelComissaoFornecedor($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
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
            $object = new ComissaoFornecedor($key); // instantiates the Active Record
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
        TSession::setValue('ComissaoFornecedorList_filter_id',   NULL);
        TSession::setValue('ComissaoFornecedorList_filter_data_faturamento',   NULL);
        TSession::setValue('ComissaoFornecedorList_filter_valor_faturamento',   NULL);
        TSession::setValue('ComissaoFornecedorList_filter_taxa_comissao',   NULL);
        TSession::setValue('ComissaoFornecedorList_filter_valor_comissao',   NULL);
        TSession::setValue('ComissaoFornecedorList_filter_pago',   NULL);
        TSession::setValue('ComissaoFornecedorList_filter_tipo',   NULL);
        TSession::setValue('ComissaoFornecedorList_filter_unit_id',   NULL);
        TSession::setValue('ComissaoFornecedorList_filter_fornecedor_id',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('ComissaoFornecedorList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->data_faturamento) AND ($data->data_faturamento)) {
            $filter = new TFilter('data_faturamento', 'like', "%{$data->data_faturamento}%"); // create the filter
            TSession::setValue('ComissaoFornecedorList_filter_data_faturamento',   $filter); // stores the filter in the session
        }


        if (isset($data->valor_faturamento) AND ($data->valor_faturamento)) {
            $filter = new TFilter('valor_faturamento', 'like', "%{$data->valor_faturamento}%"); // create the filter
            TSession::setValue('ComissaoFornecedorList_filter_valor_faturamento',   $filter); // stores the filter in the session
        }


        if (isset($data->taxa_comissao) AND ($data->taxa_comissao)) {
            $filter = new TFilter('taxa_comissao', 'like', "%{$data->taxa_comissao}%"); // create the filter
            TSession::setValue('ComissaoFornecedorList_filter_taxa_comissao',   $filter); // stores the filter in the session
        }


        if (isset($data->valor_comissao) AND ($data->valor_comissao)) {
            $filter = new TFilter('valor_comissao', 'like', "%{$data->valor_comissao}%"); // create the filter
            TSession::setValue('ComissaoFornecedorList_filter_valor_comissao',   $filter); // stores the filter in the session
        }


        if (isset($data->pago) AND ($data->pago)) {
            $filter = new TFilter('pago', 'like', "%{$data->pago}%"); // create the filter
            TSession::setValue('ComissaoFornecedorList_filter_pago',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo) AND ($data->tipo)) {
            $filter = new TFilter('tipo', '=', "$data->tipo"); // create the filter
            TSession::setValue('ComissaoFornecedorList_filter_tipo',   $filter); // stores the filter in the session
        }


        if (isset($data->unit_id) AND ($data->unit_id)) {
            $filter = new TFilter('unit_id', 'like', "%{$data->unit_id}%"); // create the filter
            TSession::setValue('ComissaoFornecedorList_filter_unit_id',   $filter); // stores the filter in the session
        }


        if (isset($data->fornecedor_id) AND ($data->fornecedor_id)) {
            $filter = new TFilter('fornecedor_id', '=', "$data->fornecedor_id"); // create the filter
            TSession::setValue('ComissaoFornecedorList_filter_fornecedor_id',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ComissaoFornecedor_filter_data', $data);
        
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
            
            // creates a repository for ComissaoFornecedor
            $repository = new TRepository('ComissaoFornecedor');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_faturamento';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('ComissaoFornecedorList_filter_id')) {
                $criteria->add(TSession::getValue('ComissaoFornecedorList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ComissaoFornecedorList_filter_data_faturamento')) {
                $criteria->add(TSession::getValue('ComissaoFornecedorList_filter_data_faturamento')); // add the session filter
            }


            if (TSession::getValue('ComissaoFornecedorList_filter_valor_faturamento')) {
                $criteria->add(TSession::getValue('ComissaoFornecedorList_filter_valor_faturamento')); // add the session filter
            }


            if (TSession::getValue('ComissaoFornecedorList_filter_taxa_comissao')) {
                $criteria->add(TSession::getValue('ComissaoFornecedorList_filter_taxa_comissao')); // add the session filter
            }


            if (TSession::getValue('ComissaoFornecedorList_filter_valor_comissao')) {
                $criteria->add(TSession::getValue('ComissaoFornecedorList_filter_valor_comissao')); // add the session filter
            }


            if (TSession::getValue('ComissaoFornecedorList_filter_pago')) {
                $criteria->add(TSession::getValue('ComissaoFornecedorList_filter_pago')); // add the session filter
            }


            if (TSession::getValue('ComissaoFornecedorList_filter_tipo')) {
                $criteria->add(TSession::getValue('ComissaoFornecedorList_filter_tipo')); // add the session filter
            }


            if (TSession::getValue('ComissaoFornecedorList_filter_unit_id')) {
                $criteria->add(TSession::getValue('ComissaoFornecedorList_filter_unit_id')); // add the session filter
            }


            if (TSession::getValue('ComissaoFornecedorList_filter_fornecedor_id')) {
                $criteria->add(TSession::getValue('ComissaoFornecedorList_filter_fornecedor_id')); // add the session filter
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
            $object = new ComissaoFornecedor($key, FALSE); // instantiates the Active Record
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
