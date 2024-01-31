<?php
/**
 * TransferenciaBancariaList Listing
 * @author  Fred Azv
 */
class TransferenciaBancariaList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_TransferenciaBancaria');
        $this->form->setFormTitle('Transferência Bancária');
        $this->form->setFieldSizes('100%');
        
        //$this->form->addExpandButton();

        // create the form fields
        $id = new TEntry('id');
        //$unit_id = new TEntry('unit_id');
        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);

        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_unit_session);

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));

        $conta_bancaria_debito_id = new TDBCombo('conta_bancaria_debito_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_debito_id->addValidation('Conta Bancária', new TRequiredValidator);
        $conta_bancaria_credito_id = new TDBCombo('conta_bancaria_credito_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_credito_id->addValidation('Conta Bancária', new TRequiredValidator);
        $data_lancamento = new TDate('data_lancamento');
        $data_lancamento->setDatabaseMask('yyyy-mm-dd');
        $data_lancamento->setMask('dd/mm/yyyy');
        $data_transferencia = new TDate('data_transferencia');
        $data_transferencia->setDatabaseMask('yyyy-mm-dd');
        $data_transferencia->setMask('dd/mm/yyyy');
        $valor = new TEntry('valor');
        $baixa = new TCombo('baixa');
        $combo_baixas = array();
        $combo_baixas['S'] = 'Sim';
        $combo_baixas['N'] = 'Não';
        $baixa->addItems($combo_baixas);
        /*$pc_despesa_id = new TDBUniqueSearch('pc_despesa_id', 'sample', 'PcDespesa', 'id', 'nivel1');
        $pc_despesa_nome = new TEntry('pc_despesa_nome');
        $pc_receita_id = new TDBUniqueSearch('pc_receita_id', 'sample', 'PcReceita', 'id', 'nivel1');
        $pc_receita_nome = new TEntry('pc_receita_nome');
        $observacao = new TEntry('observacao');*/


        $row = $this->form->addFields( [ new TLabel('Usuário'), $user_id ],
                                       [ new TLabel('Lançamento'), $data_lancamento ],
                                       [ new TLabel('Transferência'), $data_transferencia ],
                                       [ new TLabel('Valor'), $valor ],
                                       [ new TLabel('Baixa'), $baixa ]);
        $row->layout = ['col-sm-4','col-sm-2', 'col-sm-2','col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Conta Debitada'), $conta_bancaria_debito_id ],
                                       [ new TLabel('Conta Creditada'), $conta_bancaria_credito_id ]);
        $row->layout = ['col-sm-6','col-sm-6'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('TransferenciaBancaria_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['TransferenciaBancariaForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        //$column_unit_id = new TDataGridColumn('unit_id', 'Unit Id', 'right');
        $column_user_id = new TDataGridColumn('system_user->name', 'Usuário', 'left');
        $column_conta_bancaria_debito_id = new TDataGridColumn('conta_bancariaDebito->conta', 'Conta Debitada', 'left');
        $column_conta_bancaria_credito_id = new TDataGridColumn('conta_bancariaCredito->conta', 'Conta  Creditada', 'left');
        $column_data_lancamento = new TDataGridColumn('data_lancamento', 'Lancamento', 'center');
        $column_data_transferencia = new TDataGridColumn('data_transferencia', 'Transferência', 'center');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'left');
        /*$column_pc_despesa_id = new TDataGridColumn('pc_despesa_id', 'Pc Despesa Id', 'left');
        $column_pc_despesa_nome = new TDataGridColumn('pc_despesa_nome', 'Pc Despesa Nome', 'left');
        $column_pc_receita_id = new TDataGridColumn('pc_receita_id', 'Pc Receita Id', 'left');
        $column_pc_receita_nome = new TDataGridColumn('pc_receita_nome', 'Pc Receita Nome', 'left');
        $column_observacao = new TDataGridColumn('observacao', 'Observacao', 'left');*/
        $column_baixa = new TDataGridColumn('tipoBaixa', 'Baixado?', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        //$this->datagrid->addColumn($column_unit_id);
        $this->datagrid->addColumn($column_user_id);
        $this->datagrid->addColumn($column_conta_bancaria_debito_id);
        $this->datagrid->addColumn($column_conta_bancaria_credito_id);
        $this->datagrid->addColumn($column_data_lancamento);
        $this->datagrid->addColumn($column_data_transferencia);
        $this->datagrid->addColumn($column_valor);
        /*$this->datagrid->addColumn($column_pc_despesa_id);
        $this->datagrid->addColumn($column_pc_despesa_nome);
        $this->datagrid->addColumn($column_pc_receita_id);
        $this->datagrid->addColumn($column_pc_receita_nome);
        $this->datagrid->addColumn($column_observacao);*/
        $this->datagrid->addColumn($column_baixa);

        $column_data_lancamento->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $column_data_transferencia->setTransformer( function($value, $object, $row) {
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

        $action1 = new TDataGridAction(array($this, 'onTransferir'));
        $action1->setLabel('Fazer Transferência');
        $action1->setImage('fas:file-pdf red');
        $action1->setField('id');
        
        $action_group = new TDataGridActionGroup('Ações ', 'bs:th');

        $action_group->addHeader('Ações');
        $action_group->addAction($action1);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);
        
        // create EDIT action
        $action_edit = new TDataGridAction(['TransferenciaBancariaForm', 'onEdit']);
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
        $action_del->setImage('fas:trash red fa-lg');
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

    public function onTransferir( $param )
    {
        try
        {   

            TTransaction::open('sample');

            $transBancaria = new TransferenciaBancaria( $param['id'] );

            $saldo = new SaldoBancario($transBancaria->conta_bancaria_debito_id);
            
            if($saldo->valor < $transBancaria->valor)
            {    
                new TMessage('error', "Valor a ser trasferido é maior do que o saldo disponível no banco a ser debitado!");
            }
            else
            {   
                $contaDebitada = new MovimentacaoBancaria();
                $contaDebitada->valor_movimentacao = $transBancaria->valor;
                $contaDebitada->data_lancamento = $transBancaria->data_lancamento;
                $contaDebitada->data_vencimento = date('Y-m-d');
                $contaDebitada->data_baixa = $transBancaria->data_baixa;
                $contaDebitada->status = 'Débito';
                $contaDebitada->historico = "Transferência Bancária (Debitado)";
                $contaDebitada->baixa = 'S';
                $contaDebitada->tipo = 0;
                $contaDebitada->documento = "Transferência (Débito)";
                $contaDebitada->unit_id = $transBancaria->unit_id;
                $contaDebitada->pc_despesa_id = $transBancaria->pc_despesa_id;
                $contaDebitada->pc_despesa_nome = $transBancaria->pc_despesa_nome;
                $contaDebitada->conta_bancaria_id = $transBancaria->conta_bancaria_debito_id;
                $contaDebitada->store();

                $contaCredidata = new MovimentacaoBancaria();
                $contaCredidata->valor_movimentacao = $transBancaria->valor;
                $contaCredidata->data_lancamento = $transBancaria->data_lancamento;
                $contaCredidata->data_vencimento = date('Y-m-d');
                $contaCredidata->data_baixa = $transBancaria->data_baixa;
                $contaCredidata->status = 'Crédito';
                $contaCredidata->historico = "Transferência Bancária (Creditado)";
                $contaCredidata->baixa = 'S';
                $contaCredidata->tipo = 1;
                $contaCredidata->documento = "Transferência (Crédito)";
                $contaCredidata->unit_id = $transBancaria->unit_id;
                $contaCredidata->pc_receita_id = $transBancaria->pc_receita_id;
                $contaCredidata->pc_receita_nome = $transBancaria->pc_receita_nome;
                $contaCredidata->conta_bancaria_id = $transBancaria->conta_bancaria_credito_id;
                $contaCredidata->store();

                $transBancaria->baixa = "S";
                $transBancaria->store();

                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', "Transferência realizada com sucesso!",$pos_action);
            }
            

            TTransaction::close(); 

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback();
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
            $object = new TransferenciaBancaria($key); // instantiates the Active Record
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
        TSession::setValue('TransferenciaBancariaList_filter_id',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_unit_id',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_user_id',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_conta_bancaria_debito_id',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_conta_bancaria_credito_id',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_data_lancamento',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_data_transferencia',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_valor',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_pc_despesa_id',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_pc_despesa_nome',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_pc_receita_id',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_pc_receita_nome',   NULL);
        TSession::setValue('TransferenciaBancariaList_filter_observacao',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->unit_id) AND ($data->unit_id)) {
            $filter = new TFilter('unit_id', 'like', "%{$data->unit_id}%"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_unit_id',   $filter); // stores the filter in the session
        }


        if (isset($data->user_id) AND ($data->user_id)) {
            $filter = new TFilter('user_id', 'like', "%{$data->user_id}%"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_user_id',   $filter); // stores the filter in the session
        }


        if (isset($data->conta_bancaria_debito_id) AND ($data->conta_bancaria_debito_id)) {
            $filter = new TFilter('conta_bancaria_debito_id', 'like', "%{$data->conta_bancaria_debito_id}%"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_conta_bancaria_debito_id',   $filter); // stores the filter in the session
        }


        if (isset($data->conta_bancaria_credito_id) AND ($data->conta_bancaria_credito_id)) {
            $filter = new TFilter('conta_bancaria_credito_id', 'like', "%{$data->conta_bancaria_credito_id}%"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_conta_bancaria_credito_id',   $filter); // stores the filter in the session
        }


        if (isset($data->data_lancamento) AND ($data->data_lancamento)) {
            $filter = new TFilter('data_lancamento', 'like', "%{$data->data_lancamento}%"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_data_lancamento',   $filter); // stores the filter in the session
        }


        if (isset($data->data_transferencia) AND ($data->data_transferencia)) {
            $filter = new TFilter('data_transferencia', 'like', "%{$data->data_transferencia}%"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_data_transferencia',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_valor',   $filter); // stores the filter in the session
        }


        if (isset($data->pc_despesa_id) AND ($data->pc_despesa_id)) {
            $filter = new TFilter('pc_despesa_id', '=', "$data->pc_despesa_id"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_pc_despesa_id',   $filter); // stores the filter in the session
        }


        if (isset($data->pc_despesa_nome) AND ($data->pc_despesa_nome)) {
            $filter = new TFilter('pc_despesa_nome', 'like', "%{$data->pc_despesa_nome}%"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_pc_despesa_nome',   $filter); // stores the filter in the session
        }


        if (isset($data->pc_receita_id) AND ($data->pc_receita_id)) {
            $filter = new TFilter('pc_receita_id', '=', "$data->pc_receita_id"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_pc_receita_id',   $filter); // stores the filter in the session
        }


        if (isset($data->pc_receita_nome) AND ($data->pc_receita_nome)) {
            $filter = new TFilter('pc_receita_nome', 'like', "%{$data->pc_receita_nome}%"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_pc_receita_nome',   $filter); // stores the filter in the session
        }


        if (isset($data->observacao) AND ($data->observacao)) {
            $filter = new TFilter('observacao', 'like', "%{$data->observacao}%"); // create the filter
            TSession::setValue('TransferenciaBancariaList_filter_observacao',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('TransferenciaBancaria_filter_data', $data);
        
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
            
            // creates a repository for TransferenciaBancaria
            $repository = new TRepository('TransferenciaBancaria');
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
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));
            $criteria->add(new TFilter('baixa',  '= ', 'N'));
            

            if (TSession::getValue('TransferenciaBancariaList_filter_id')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_id')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_unit_id')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_unit_id')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_user_id')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_user_id')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_conta_bancaria_debito_id')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_conta_bancaria_debito_id')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_conta_bancaria_credito_id')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_conta_bancaria_credito_id')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_data_lancamento')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_data_lancamento')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_data_transferencia')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_data_transferencia')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_valor')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_valor')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_pc_despesa_id')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_pc_despesa_id')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_pc_despesa_nome')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_pc_despesa_nome')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_pc_receita_id')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_pc_receita_id')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_pc_receita_nome')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_pc_receita_nome')); // add the session filter
            }


            if (TSession::getValue('TransferenciaBancariaList_filter_observacao')) {
                $criteria->add(TSession::getValue('TransferenciaBancariaList_filter_observacao')); // add the session filter
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
            $object = new TransferenciaBancaria($key, FALSE); // instantiates the Active Record
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
