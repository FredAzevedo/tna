<?php
/**
 * ContaReceberListMes Listing
 * @author  <your name here>
 */
class ContaReceberListMes extends TPage
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
        $this->form->setFormTitle('Contas a Receber');
        $this->form->setFieldSizes('100%');

        

        // create the form fields
        $descricao = new TEntry('descricao');
        $data_vencimento = new TEntry('data_vencimento');
        $valor = new TEntry('valor');
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $documento = new TEntry('documento');
        $pc_receita_id = new TDBUniqueSearch('pc_receita_id', 'sample', 'PcReceita', 'id', 'nivel1');
        $conta_bancaria_id = new TDBUniqueSearch('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', 'cod_banco');


        // add the fields

        $row = $this->form->addFields( [ new TLabel('Descrição'), $descricao ],
                                       [ new TLabel('Documento'), $documento ],
                                       [ new TLabel('Vencimento'), $data_vencimento ]);
        $row->layout = ['col-sm-6','col-sm-4', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Valor'), $valor ],
                                       [ new TLabel('Cliente'), $cliente_id ],
                                       [ new TLabel('Plano de Contas'), $pc_receita_id ],
                                       [ new TLabel('Conta Bancária'), $conta_bancaria_id ]);
        $row->layout = ['col-sm-2','col-sm-4', 'col-sm-4', 'col-sm-2'];

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ContaReceber_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ContaReceberForm', 'onEdit']), 'fa:plus green');
        $btn2 = $this->form->addAction('Baixar Títulos', new TAction([$this, 'onBaixar']), 'fa:plus green');
       
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        
        //adicionando o botão checkbox ao form pra passar dados via post
        $this->formGrid = new TForm;
        $this->formGrid->add($this->datagrid);
        $this->formGrid->addField($btn2);


        // creates the datagrid columns
        $column_check = new TDataGridColumn('checkbox', ' ', 'center'); 
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_data_conta = new TDataGridColumn('data_conta', 'Data Conta', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');
        $column_documento = new TDataGridColumn('documento', 'Documento', 'left');
        $column_data_vencimento = new TDataGridColumn('data_vencimento', 'Vencimento', 'center');
        $column_previsao = new TDataGridColumn('previsao', 'Previsao', 'left');
        $column_multa = new TDataGridColumn('multa', 'Multa', 'left');
        $column_juros = new TDataGridColumn('juros', 'Juros', 'left');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');
        $column_desconto = new TDataGridColumn('desconto', 'Desconto', 'left');
        $column_portador = new TDataGridColumn('portador', 'Portador', 'left');
        $column_observacao = new TDataGridColumn('observacao', 'Observacao', 'left');
        $column_baixa = new TDataGridColumn('baixa', 'Baixa', 'left');
        $column_data_baixa = new TDataGridColumn('data_baixa', 'Data Baixa', 'left');
        $column_valor_pago = new TDataGridColumn('valor_pago', 'Valor Pago', 'left');
        $column_valor_parcial = new TDataGridColumn('valor_parcial', 'Valor Parcial', 'left');
        $column_valor_real = new TDataGridColumn('valor_real', 'Valor Real', 'left');
        $column_replica = new TDataGridColumn('replica', 'Replica', 'left');
        $column_parcelas = new TDataGridColumn('parcelas', 'Parcelas', 'right');
        $column_nparcelas = new TDataGridColumn('nparcelas', 'Nparcelas', 'right');
        $column_intervalo = new TDataGridColumn('intervalo', 'Intervalo', 'right');
        $column_responsavel = new TDataGridColumn('responsavel', 'Responsavel', 'left');
        $column_boleto_status = new TDataGridColumn('boleto_status', 'Boleto Status', 'left');
        $column_boleto_emitido = new TDataGridColumn('boleto_emitido', 'Boleto Emitido', 'left');
        $column_unit_id = new TDataGridColumn('unit_id', 'Unit Id', 'right');
        $column_boleto_id = new TDataGridColumn('boleto_id', 'Boleto Id', 'left');
        $column_cliente_id = new TDataGridColumn('cliente->nome_fantasia', 'Cliente', 'left');
        $column_tipo_pgto_id = new TDataGridColumn('tipo_pgto->nome', 'Tipo Pgto', 'left');
        $column_pc_receita_id = new TDataGridColumn('pc_receita->nome', 'Plano de Contas', 'left');
        $column_conta_bancaria_id = new TDataGridColumn('conta_bancaria_id', 'Conta', 'right');
        $column_boleto_account_id = new TDataGridColumn('boleto_account_id', 'Boleto Account Id', 'left');

        // creates the datagrid actions
        $order1 = new TAction(array($this, 'onReload'));

        // define the ordering parameters
        $order1->setParameter('order', 'id');

        // assign the ordering actions
        $column_id->setAction($order1);

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_check);
        $this->datagrid->addColumn($column_id);
        //$this->datagrid->addColumn($column_data_conta);
        $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_pc_receita_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_documento);
        $this->datagrid->addColumn($column_data_vencimento);
        //$this->datagrid->addColumn($column_previsao);
        //$this->datagrid->addColumn($column_multa);
        //$this->datagrid->addColumn($column_juros);
        //$this->datagrid->addColumn($column_desconto);
        //$this->datagrid->addColumn($column_portador);
        //$this->datagrid->addColumn($column_observacao);
        //$this->datagrid->addColumn($column_baixa);
        //$this->datagrid->addColumn($column_data_baixa);
        //$this->datagrid->addColumn($column_valor_pago);
        //$this->datagrid->addColumn($column_valor_parcial);
        //$this->datagrid->addColumn($column_valor_real);
        //$this->datagrid->addColumn($column_replica);
        //$this->datagrid->addColumn($column_parcelas);
        //$this->datagrid->addColumn($column_nparcelas);
        //$this->datagrid->addColumn($column_intervalo);
        //$this->datagrid->addColumn($column_responsavel);
        //$this->datagrid->addColumn($column_boleto_status);
        //$this->datagrid->addColumn($column_boleto_emitido);
        //$this->datagrid->addColumn($column_unit_id);
        //$this->datagrid->addColumn($column_boleto_id);
        //$this->datagrid->addColumn($column_tipo_pgto_id);
        //$this->datagrid->addColumn($column_conta_bancaria_id);
        $this->datagrid->addColumn($column_valor);
        //$this->datagrid->addColumn($column_boleto_account_id);

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

        /*$action1 = new TDataGridAction(array($this, 'onBaixar'));
        $action1->setLabel('Contrato');
        $action1->setImage('fa:file-pdf-o red');
        $action1->setField('id');
        $action1->setField('cliente_id');

        $action_group = new TDataGridActionGroup('Ações ', 'bs:th');

        $action_group->addHeader('Ações');
        $action_group->addAction($action1);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);*/
        
        // create EDIT action
        $action_edit = new TDataGridAction(['ContaReceberForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        $action_del->setButtonClass('btn btn-default');
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
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'DashboardFinanceiroView'));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->formGrid, $this->pageNavigation));

        parent::add($container);
     
    }   

    public function onBaixar( $param )
    { 

        $data = $this->form->getData();

        $selected = TSession::getValue(__CLASS__.'_selected_objects');
        if (!(is_array($selected) && count($selected))){
            new TMessage('info', 'Nenhum registro selecionado');
            return;
        }

        TTransaction::open('sample');
        try {

            $this->form->setData($data);
            
            $baix = '';
            foreach ($selected as $id) 
            {
                $selected[] = $id;
                $cr = new ContaReceber($id);
                $baix = $cr->baixa = 'S';
                $cr->data_baixa = date('Y-m-d');
                $cr->store();

                $movBancaria = new MovimentacaoBancaria();
                $movBancaria->valor_movimentacao = $cr->valor;
                $movBancaria->data_lancamento = $cr->data_conta;
                $movBancaria->data_vencimento = $cr->data_vencimento;
                $movBancaria->data_baixa = date('Y-m-d');
                $movBancaria->status = 'Crédito';
                $movBancaria->historico = $cr->descricao;
                $movBancaria->baixa = 'S';
                $movBancaria->tipo = 1;
                $movBancaria->documento = $cr->documento;
                $movBancaria->unit_id = $cr->unit_id;
                $movBancaria->cliente_id = $cr->cliente_id;
                $movBancaria->pc_receita_id = $cr->pc_receita_id;
                $movBancaria->pc_receita_nome = $cr->pc_receita_nome;
                $movBancaria->conta_receber_id = $id;
                $movBancaria->conta_bancaria_id = $cr->conta_bancaria_id;
                $movBancaria->store();

                $cliente = new Cliente($cr->cliente_id);
                $comissaoUser = new ComissaoTabela($cliente->comissao_vendedor);

                //COMISSÃO DO VENDEDOR PRINCIPAL DO CLIENTE
                if(!empty($cliente->comissao_vendedor) AND !empty($cliente->vendedor_user_id))
                {
                    if($comissaoUser->forma_comissao == "P"){

                        $valorComissao = $cr->valor * $comissaoUser->valor_comissao/100;

                        $comissao = new ComissaoUser();
                        $comissao->data_faturamento = date('Y-m-d');
                        $comissao->valor_faturamento = $cr->valor;
                        $comissao->taxa_comissao = $comissaoUser->valor_comissao;
                        $comissao->valor_comissao = $valorComissao;
                        $comissao->descricao = $cr->descricao;
                        $comissao->pago = 'N';
                        $comissao->tipo = 'P';
                        $comissao->unit_id = $cr->unit_id;
                        $comissao->user_id = $cliente->vendedor_user_id;
                        $comissao->cliente_id = $cliente->id;
                        $comissao->store();

                    }else{

                        $valorComissao = $comissaoUser->valor_comissao;

                        $comissao = new ComissaoUser();
                        $comissao->data_faturamento = date('Y-m-d');
                        $comissao->valor_faturamento = $cr->valor;
                        $comissao->taxa_comissao = $comissaoUser->valor_comissao;
                        $comissao->valor_comissao = $valorComissao;
                        $comissao->descricao = $cr->descricao;
                        $comissao->pago = 'N';
                        $comissao->tipo = 'D';
                        $comissao->unit_id = $cr->unit_id;
                        $comissao->user_id = $cliente->vendedor_user_id;
                        $comissao->cliente_id = $cliente->id;
                        $comissao->store();

                    }
                }

                //COMISSÃO DO VENDEDOR EXTERNO DO CLIENTE
                $comissaoUserExterno = new ComissaoTabela($cliente->comissao_vendedor_externo);

                if(!empty($cliente->comissao_vendedor_externo) AND !empty($cliente->vendedor_externo_user_id))
                {
                    if($comissaoUserExterno->forma_comissao == "P"){

                        $valorComissao = $cr->valor * $comissaoUserExterno->valor_comissao/100;

                        $comissao = new ComissaoUser();
                        $comissao->data_faturamento = date('Y-m-d');
                        $comissao->valor_faturamento = $cr->valor;
                        $comissao->taxa_comissao = $comissaoUserExterno->valor_comissao;
                        $comissao->valor_comissao = $valorComissao;
                        $comissao->descricao = $cr->descricao;
                        $comissao->pago = 'N';
                        $comissao->tipo = 'P';
                        $comissao->unit_id = $cr->unit_id;
                        $comissao->user_id = $cliente->vendedor_externo_user_id;
                        $comissao->store();

                    }else{

                        $valorComissao = $comissaoUserExterno->valor_comissao;

                        $comissao = new ComissaoUser();
                        $comissao->data_faturamento = date('Y-m-d');
                        $comissao->valor_faturamento = $cr->valor;
                        $comissao->taxa_comissao = $comissaoUserExterno->valor_comissao;
                        $comissao->valor_comissao = $valorComissao;
                        $comissao->descricao = $cr->descricao;
                        $comissao->pago = 'N';
                        $comissao->tipo = 'D';
                        $comissao->unit_id = $cr->unit_id;
                        $comissao->user_id = $cliente->vendedor_externo_user_id;
                        $comissao->store();

                    }
                }

                //COMISSÃO DE QUEM INDICOU O CLIENTE
                $comissaoIndicador = new ComissaoTabela($cliente->comissao_parceiro);

                if(!empty($cliente->comissao_parceiro) AND !empty($cliente->fornecedor_id))
                {
                    if($comissaoIndicador->forma_comissao == "P"){

                        $valorComissao = $cr->valor * $comissaoIndicador->valor_comissao/100;

                        $comissao = new ComissaoFornecedor();
                        $comissao->data_faturamento = date('Y-m-d');
                        $comissao->valor_faturamento = $cr->valor;
                        $comissao->taxa_comissao = $comissaoIndicador->valor_comissao;
                        $comissao->valor_comissao = $valorComissao;
                        $comissao->descricao = $cr->descricao;
                        $comissao->pago = 'N';
                        $comissao->tipo = 'P';
                        $comissao->unit_id = $cr->unit_id;
                        $comissao->fornecedor_id = $cliente->fornecedor_id;
                        $comissao->store();

                    }else{

                        $valorComissao = $comissaoIndicador->valor_comissao;

                        $comissao = new ComissaoFornecedor();
                        $comissao->data_faturamento = date('Y-m-d');
                        $comissao->valor_faturamento = $cr->valor;
                        $comissao->taxa_comissao = $comissaoIndicador->valor_comissao;
                        $comissao->valor_comissao = $valorComissao;
                        $comissao->descricao = $cr->descricao;
                        $comissao->pago = 'N';
                        $comissao->tipo = 'D';
                        $comissao->unit_id = $cr->unit_id;
                        $comissao->fornecedor_id = $cliente->fornecedor_id;
                        $comissao->store();

                    }
                }
            }   
            TTransaction::close();

            if($baix == 'S')
            {   
                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', 'Baixa realizada com sucesso. <br>',$pos_action);
            }
            else
            {   
                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('erro', 'Atenção. Você não marcou nenhum título! Escolha pelo menos um título. <br>',$pos_action);
            }
            
            TSession::delValue(__CLASS__.'_selected_objects');

            } catch (Exception $e) {
                TTransaction::rollback();
                new TMessage('error', 'Houve um problema a dar baixa no Título. <br>' . $e->getMessage());
                $this->form->setData($data);
                return;
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
        TSession::setValue('ContaReceberListMes_filter_descricao',   NULL);
        TSession::setValue('ContaReceberListMes_filter_data_vencimento',   NULL);
        TSession::setValue('ContaReceberListMes_filter_valor',   NULL);
        TSession::setValue('ContaReceberListMes_filter_cliente_id',   NULL);
        TSession::setValue('ContaReceberListMes_filter_tipo_pgto_id',   NULL);
        TSession::setValue('ContaReceberListMes_filter_pc_receita_id',   NULL);
        TSession::setValue('ContaReceberListMes_filter_conta_bancaria_id',   NULL);

        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
            TSession::setValue('ContaReceberListMes_filter_descricao',   $filter); // stores the filter in the session
        }


        if (isset($data->data_vencimento) AND ($data->data_vencimento)) {
            $filter = new TFilter('data_vencimento', 'like', "%{$data->data_vencimento}%"); // create the filter
            TSession::setValue('ContaReceberListMes_filter_data_vencimento',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue('ContaReceberListMes_filter_valor',   $filter); // stores the filter in the session
        }


        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', '=', "$data->cliente_id"); // create the filter
            TSession::setValue('ContaReceberListMes_filter_cliente_id',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo_pgto_id) AND ($data->tipo_pgto_id)) {
            $filter = new TFilter('tipo_pgto_id', '=', "$data->tipo_pgto_id"); // create the filter
            TSession::setValue('ContaReceberListMes_filter_tipo_pgto_id',   $filter); // stores the filter in the session
        }


        if (isset($data->pc_receita_id) AND ($data->pc_receita_id)) {
            $filter = new TFilter('pc_receita_id', '=', "$data->pc_receita_id"); // create the filter
            TSession::setValue('ContaReceberListMes_filter_pc_receita_id',   $filter); // stores the filter in the session
        }


        if (isset($data->conta_bancaria_id) AND ($data->conta_bancaria_id)) {
            $filter = new TFilter('conta_bancaria_id', '=', "$data->conta_bancaria_id"); // create the filter
            TSession::setValue('ContaReceberListMes_filter_conta_bancaria_id',   $filter); // stores the filter in the session
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

    public static function onSelect($param) {
        // get the selected objects from session
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');

        $check = $param['check'];
        $id = $param['id'];
        if ($check == 'false'){
            if (isset($selected_objects[$id])){
                unset($selected_objects[$id]);
            }
        }
        else
        {
            $selected_objects[$id] = $id; // add the object inside the array
        }
        TSession::setValue(__CLASS__.'_selected_objects', $selected_objects); // put the array back to the sessio
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
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));
            $criteria->add(new TFilter('baixa',  '= ', 'N'));
            $criteria->add(new TFilter('data_vencimento',  '>=', "NOESC:DATE(DATE_FORMAT(CURDATE() ,'%Y-%m-01'))"));
            $criteria->add(new TFilter('data_vencimento',  '<=', "NOESC:LAST_DAY(CURDATE())"));
            

            if (TSession::getValue('ContaReceberListMes_filter_descricao')) {
                $criteria->add(TSession::getValue('ContaReceberListMes_filter_descricao')); // add the session filter
            }


            if (TSession::getValue('ContaReceberListMes_filter_data_vencimento')) {
                $criteria->add(TSession::getValue('ContaReceberListMes_filter_data_vencimento')); // add the session filter
            }


            if (TSession::getValue('ContaReceberListMes_filter_valor')) {
                $criteria->add(TSession::getValue('ContaReceberListMes_filter_valor')); // add the session filter
            }


            if (TSession::getValue('ContaReceberListMes_filter_cliente_id')) {
                $criteria->add(TSession::getValue('ContaReceberListMes_filter_cliente_id')); // add the session filter
            }


            if (TSession::getValue('ContaReceberListMes_filter_tipo_pgto_id')) {
                $criteria->add(TSession::getValue('ContaReceberListMes_filter_tipo_pgto_id')); // add the session filter
            }


            if (TSession::getValue('ContaReceberListMes_filter_pc_receita_id')) {
                $criteria->add(TSession::getValue('ContaReceberListMes_filter_pc_receita_id')); // add the session filter
            }


            if (TSession::getValue('ContaReceberListMes_filter_conta_bancaria_id')) {
                $criteria->add(TSession::getValue('ContaReceberListMes_filter_conta_bancaria_id')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            
            $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');

            if ($objects)
            {
                $total = count($objects);
                $atual = 0;
                foreach ($objects as $object)
                {
                    $atual++;

                    $chk_selecionar = new TCheckButton("chkcheckbutton");
                    $chk_selecionar->id = "chkcheckbutton{$object->id}";
                    $chk_selecionar->code = $object->id;
                    $chk_selecionar->setIndexValue('on');

                    if (isset($selected_objects[$object->id])){
                        $chk_selecionar->setValue('on');
                    }

                    $c = new TElement('div');
                    $c->add($chk_selecionar);

                    if ($total == $atual){

                        $selected = '';
                        foreach ((array)$selected_objects as $s) {
                            $selected .= $s . ',';
                        }

                        $selected = "[{$selected}]";

                        $script = TScript::create('$(document).ready(function () {
                            window.boleto_selected = '. $selected .';
                            
                            $("input[name=chkcheckbutton]").off("change").change(function () {
                            var value_check = $(this).is(\':checked\');
                            var code = $(this).attr("code");
                            if (value_check) {
                                if (window.boleto_selected.indexOf(code) === -1) {
                                    window.boleto_selected.push(code);
                                }
                            } else {
                                var index = window.boleto_selected.indexOf(code);
                                if (index > -1) {
                                    window.boleto_selected.splice(index,1);
                                }
                            }
                            __adianti_ajax_exec(\'class=ContaReceberListInadiplentes&method=onSelect&id=\'+code+\'&check=\'+value_check);
                            });});',false);

                        $c->add($script);
                    }

//                    $object->checkbox = new TCheckButton('checkbox'.$object->id);
//                    $object->checkbox->setIndexValue($object->id);
                    $object->checkbox = $c;
//                    $this->form->addField($object->checkbox);
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
