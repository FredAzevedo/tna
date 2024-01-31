<?php
/**
 * MovimentacaoBancariaList Listing
 * @author  <your name here>
 */
class MovimentacaoBancariaList extends TPage
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
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_MovimentacaoBancaria');
        $this->form->setFormTitle('Movimentação Bancaria');
        $this->form->setFieldSizes('100%');
        
        // create the form fields
        $valor_movimentacao = new TNumeric('valor_movimentacao', 2, ',', '.', true);

        $data_vencimento = new TDateTime('data_vencimento');
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');

        $data_baixa = new TDateTime('data_baixa');
        $data_baixa->setDatabaseMask('yyyy-mm-dd');
        $data_baixa->setMask('dd/mm/yyyy');
        
        $status = new TCombo('status');
        $combo_status = array();
        $combo_status['Crédito'] = 'Creditado';
        $combo_status['Débito'] = 'Debitado';
        $status->addItems($combo_status);

        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'razao_social');
        $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'nome_fantasia');
        $pc_despesa_id = new TDBUniqueSearch('pc_despesa_id', 'sample', 'PcDespesa', 'id', 'nome');
        $pc_receita_id = new TDBUniqueSearch('pc_receita_id', 'sample', 'PcReceita', 'id', 'nome');

        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','');

        $tipo = new TCombo('tipo');
        $combo_tipo = array();
        $combo_tipo['0'] = 'Despesa';
        $combo_tipo['1'] = 'Receita';
        $tipo->addItems($combo_tipo);

        $de = new TDate('de');
        $de->setDatabaseMask('yyyy-mm-dd');
        $de->setMask('dd/mm/yyyy');
        $ate = new TDate('ate');
        $ate->setDatabaseMask('yyyy-mm-dd');
        $ate->setMask('dd/mm/yyyy');


        $historico = new TEntry('historico');

        $documento = new TEntry('documento');

        $row = $this->form->addFields( [ new TLabel('Valor'), $valor_movimentacao ],
                                       [ new TLabel('Vencimento'), $data_vencimento ],
                                       [ new TLabel('Data da Baixa'), $data_baixa ],
                                       [ new TLabel('Status'), $status ],
                                       [ new TLabel('Histórico'), $historico ],
                                       [ new TLabel('Documento'), $documento ]);
        $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2', 'col-sm-2'];


        $row = $this->form->addFields( [ new TLabel('Cliente'), $cliente_id ],
                                       [ new TLabel('Fornecedor'), $fornecedor_id ]
        );
        $row->layout = ['col-sm-6','col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Plano de Contas Receitas'), $pc_receita_id ],
                                       [ new TLabel('Plano de Contas Despesas'), $pc_despesa_id ]
        );
        $row->layout = ['col-sm-6','col-sm-6'];


        $row = $this->form->addFields( [ new TLabel('Conta Bancária'), $conta_bancaria_id ],
                                       [ new TLabel('Tipo'), $tipo ],
                                       [ new TLabel('De'), $de ],
                                       [ new TLabel('Até'), $ate ]
        );
        $row->layout = ['col-sm-6','col-sm-2','col-sm-2','col-sm-2'];

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('MovimentacaoBancaria_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Lançamento Bancário', new TAction(['LancamentoBancarioForm', 'onEdit']), 'fa:plus green');
        $this->form->addAction('PDF', new TAction(['RelMovBancaria', 'onViewPDF']), 'fa:table');
        $this->form->addAction('CSV', new TAction([$this, 'onExportCSV']), 'fa:table');
        $this->form->addAction('Exportação', new TAction(['ExportarMovimentacaoBancaria', 'onShow']), 'fa:table');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $this->datagrid->disableHtmlConversion();
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_valor_movimentacao = new TDataGridColumn('valor_movimentacao', 'Valor', 'left');
        $column_data_lancamento = new TDataGridColumn('data_lancamento', 'Lancamento', 'left');
        $column_data_vencimento = new TDataGridColumn('data_vencimento', 'Vencimento', 'left');
        $column_data_baixa = new TDataGridColumn('data_baixa', 'Baixa', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        $column_historico = new TDataGridColumn('historico_completo', 'Histórico', 'left');
        $column_baixa = new TDataGridColumn('baixa', 'Baixa', 'left');
        $column_documento = new TDataGridColumn('documento', 'Documento', 'left');
        $column_conta_bancaria_id = new TDataGridColumn('conta_bancaria->conta', 'Conta', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_historico);
        $this->datagrid->addColumn($column_documento);
        //$this->datagrid->addColumn($column_data_lancamento);
        $this->datagrid->addColumn($column_data_vencimento);
        $this->datagrid->addColumn($column_data_baixa);
        //$this->datagrid->addColumn($column_baixa);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_conta_bancaria_id);
        $this->datagrid->addColumn($column_valor_movimentacao);
        

        $column_data_vencimento->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $column_data_baixa->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };

        $column_valor_movimentacao->setTransformer( $format_value );
        
        // create EDIT action
        $action_edit = new TDataGridAction(['MovimentacaoBancariaForm', 'onEdit']);
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
        
        // $botao = $this->form->addHeaderActionLink( 'Expandir',  new TAction([$this, 'onClose'], ['register_state' => 'false']), 'fa:search' );
        // $botao->class = "btn btn-info btn-sm";
        // $botao->id = 'custom-id-botao';

        // TScript::create('
        //     $(document).ready(function(){
        //       $("form .panel-body").toggleClass("collapse");
        //       $("#custom-id-botao").click(function(){
        //         event.preventDefault();
        //         $(".card-body.panel-body").toggleClass("collapse show");    
        //       });
        //     });
        // ');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public static function onClose()
    {

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
            $object = new MovimentacaoBancaria($key); // instantiates the Active Record
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
        TSession::setValue('MovimentacaoBancariaList_filter_valor_movimentacao',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_data_vencimento',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_data_baixa',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_status',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_historico',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_tipo',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_documento',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_fornecedor_id',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_pc_receita_id',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_pc_despesa_id',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_conta_pagar_id',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_conta_receber_id',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_conta_bancaria_id',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_cliente_id',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_de',   NULL);
        TSession::setValue('MovimentacaoBancariaList_filter_ate',   NULL);

        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', '=', "$data->cliente_id"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_cliente_id',   $filter); // stores the filter in the session
        }

        if (isset($data->valor_movimentacao) AND ($data->valor_movimentacao)) {
            $filter = new TFilter('valor_movimentacao', 'like', "%{$data->valor_movimentacao}%"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_valor_movimentacao',   $filter); // stores the filter in the session
        }


        if (isset($data->data_vencimento) AND ($data->data_vencimento)) {
            $filter = new TFilter('data_vencimento', 'like', "%{$data->data_vencimento}%"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_data_vencimento',   $filter); // stores the filter in the session
        }


        if (isset($data->data_baixa) AND ($data->data_baixa)) {
            $filter = new TFilter('data_baixa', 'like', "%{$data->data_baixa}%"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_data_baixa',   $filter); // stores the filter in the session
        }


        if (isset($data->status) AND ($data->status)) {
            $filter = new TFilter('status', 'like', "%{$data->status}%"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_status',   $filter); // stores the filter in the session
        }


        if (isset($data->historico) AND ($data->historico)) {
            $filter = new TFilter('historico', 'like', "%{$data->historico}%"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_historico',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo) AND ($data->tipo)) {
            $filter = new TFilter('tipo', '=', "$data->tipo"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_tipo',   $filter); // stores the filter in the session
        }


        if (isset($data->documento) AND ($data->documento)) {
            $filter = new TFilter('documento', 'like', "$data->documento"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_documento',   $filter); // stores the filter in the session
        }


        if (isset($data->fornecedor_id) AND ($data->fornecedor_id)) {
            $filter = new TFilter('fornecedor_id', '=', "$data->fornecedor_id"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_fornecedor_id',   $filter); // stores the filter in the session
        }


        if (isset($data->pc_receita_id) AND ($data->pc_receita_id)) {
            $filter = new TFilter('pc_receita_id', '=', "$data->pc_receita_id"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_pc_receita_id',   $filter); // stores the filter in the session
        }


        if (isset($data->pc_despesa_id) AND ($data->pc_despesa_id)) {
            $filter = new TFilter('pc_despesa_id', '=', "$data->pc_despesa_id"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_pc_despesa_id',   $filter); // stores the filter in the session
        }


        if (isset($data->conta_pagar_id) AND ($data->conta_pagar_id)) {
            $filter = new TFilter('conta_pagar_id', '=', "$data->conta_pagar_id"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_conta_pagar_id',   $filter); // stores the filter in the session
        }


        if (isset($data->conta_receber_id) AND ($data->conta_receber_id)) {
            $filter = new TFilter('conta_receber_id', '=', "$data->conta_receber_id"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_conta_receber_id',   $filter); // stores the filter in the session
        }


        if (isset($data->conta_bancaria_id) AND ($data->conta_bancaria_id)) {
            $filter = new TFilter('conta_bancaria_id', '=', "$data->conta_bancaria_id"); // create the filter
            TSession::setValue('MovimentacaoBancariaList_filter_conta_bancaria_id',   $filter); // stores the filter in the session
        }


        if (isset($data->de) AND ($data->de)) {
            
            $filter = new TFilter('data_vencimento', '>=', "{$data->de}");
            TSession::setValue('MovimentacaoBancariaList_filter_de',   $filter); 
        }

        if (isset($data->ate) AND ($data->ate)) {

            $filter = new TFilter('data_vencimento', '<=', "{$data->ate}");
            TSession::setValue('MovimentacaoBancariaList_filter_ate',   $filter); 
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('MovimentacaoBancaria_filter_data', $data);
        
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
            
            // creates a repository for MovimentacaoBancaria
            $repository = new TRepository('MovimentacaoBancaria');
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
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));
            

            if (TSession::getValue('MovimentacaoBancariaList_filter_valor_movimentacao')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_valor_movimentacao')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_data_vencimento')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_data_vencimento')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_data_baixa')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_data_baixa')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_status')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_status')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_historico')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_historico')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_tipo')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_tipo')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_documento')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_documento')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_fornecedor_id')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_fornecedor_id')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_pc_receita_id')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_pc_receita_id')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_pc_despesa_id')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_pc_despesa_id')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_conta_pagar_id')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_conta_pagar_id')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_conta_receber_id')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_conta_receber_id')); // add the session filter
            }


            if (TSession::getValue('MovimentacaoBancariaList_filter_conta_bancaria_id')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_conta_bancaria_id')); // add the session filter
            }

            if (TSession::getValue('MovimentacaoBancariaList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_cliente_id')); // add the session filter
            }

            if (TSession::getValue('MovimentacaoBancariaList_filter_de')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_de')); // add the session filter
            }

            if (TSession::getValue('MovimentacaoBancariaList_filter_ate')) {
                $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_ate')); // add the session filter
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
            $object = new MovimentacaoBancaria($key, FALSE); // instantiates the Active Record

            if($object->conta_pagar_id != NULL){

                $cp = new ContaPagar($object->conta_pagar_id);
                $cp->baixa = 'N';
                $cp->store();
            }

            if($object->conta_receber_id != NULL){

                $cr = new ContaReceber($object->conta_receber_id);
                $cr->baixa = 'N';
                $cr->store();
            }

            $object->delete($key);
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

    public function onExportCSV()
    {
        try
        {
            // open a transaction with database 'samples'
                TTransaction::open('sample');
                
                // creates a repository for Customer
                $repository = new TRepository('MovimentacaoBancaria');
                
                // creates a criteria
                $criteria = new TCriteria;
                $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));

                if (TSession::getValue('MovimentacaoBancariaList_filter_historico')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_historico')); // add the session filter
                 }


                if (TSession::getValue('MovimentacaoBancariaList_filter_documento')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_documento')); // add the session filter
                }

                if (TSession::getValue('MovimentacaoBancariaList_filter_data_vencimento')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_data_vencimento')); // add the session filter
                }

                if (TSession::getValue('MovimentacaoBancariaList_filter_data_baixa')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_data_baixa')); // add the session filter
                }

                if (TSession::getValue('MovimentacaoBancariaList_filter_status')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_status')); // add the session filter
                }

                if (TSession::getValue('MovimentacaoBancariaList_filter_conta_bancaria_id')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_conta_bancaria_id')); // add the session filter
                }

                if (TSession::getValue('MovimentacaoBancariaList_filter_valor_movimentacao')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_valor_movimentacao')); // add the session filter
                }

            $csv = '';
            // load the objects according to criteria
            $customers = $repository->load($criteria, false);
            if ($customers)
            {
                $csv .= 'Id'.';'.'Historico'.';'.'Documento'.';'.'Vencimento'.';'.'Baixa'.';'.
                'Status'.';'.'Conta'.';'.'Valor'."\n";
                $valorTotal = 0;
                foreach ($customers as $customer)
                {
                    $partes1 = explode(" ", $customer->data_vencimento);
                    $data1 = explode('-', $partes1[0]);

                    $partes2 = explode(" ", $customer->data_baixa);
                    $data2 = explode('-', $partes2[0]);

                    $csv .= $customer->id.';'.
                            $customer->historico.';'.
                            $customer->documento.';'.
                            $data1[2].'/'.$data1[1].'/'.$data1[0].';'.
                            $data2[2].'/'.$data2[1].'/'.$data2[0].';'.
                            $customer->status.';'.
                            $customer->conta_bancaria_id.';'.
                            $customer->valor_movimentacao."\n";

                    if($customer->status == "Débito"){
                        $valorTotal -= $customer->valor_movimentacao;
                    }else{
                        $valorTotal += $customer->valor_movimentacao;
                    }
                }

                $csv .= ' '.';'.' '.';'.' '.';'.' '.';'.' '.';'.
                ' '.';'.' '.';'.$valorTotal."\n";

                file_put_contents('app/output/movbancaria.csv', $csv);
                TPage::openFile('app/output/movbancaria.csv');
            }
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}
