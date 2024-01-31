<?php
/**
 * ContaReceberList Listing
 * @author  Fred Azv.
 */
class ContaReceberList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ContaReceberList');
        $this->form->setFormTitle('Contas a Receber');
        $this->form->setFieldSizes('100%');

        
        // create the form fields
        $id = new TEntry('id');
        $descricao = new TEntry('descricao');
        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');
        $valor = new TNumeric('valor',2,',','.',true);
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $documento = new TEntry('documento');
        $pc_receita_id = new TDBUniqueSearch('pc_receita_id', 'sample', 'PcReceita', 'id', 'nome');
        
        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);

        $baixa = new TCombo('baixa');
        $combo_baixa = array();
        $combo_baixa['S'] = 'Sim';
        $combo_baixa['N'] = 'Não';
        $baixa->addItems($combo_baixa);
        $baixa->setValue('N');
  
        $de = new TDate('de');
        $de->setDatabaseMask('yyyy-mm-dd');
        $de->setMask('dd/mm/yyyy');
        $ate = new TDate('ate');
        $ate->setDatabaseMask('yyyy-mm-dd');
        $ate->setMask('dd/mm/yyyy');

        // add the fields

        $row = $this->form->addFields( [ new TLabel('ID Contas Receber'), $id ],
                                       [ new TLabel('Descrição'), $descricao ],
                                       [ new TLabel('Documento'), $documento ],
                                       [ new TLabel('Vencimento'), $data_vencimento ],
                                       [ new TLabel('Baixa'), $baixa ]);
        $row->layout = ['col-sm-2','col-sm-4','col-sm-2', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Valor'), $valor ],
                                       [ new TLabel('Cliente'), $cliente_id ],
                                       [ new TLabel('Plano de Contas'), $pc_receita_id ],
                                       [ new TLabel('Conta Bancária'), $conta_bancaria_id ]);
        $row->layout = ['col-sm-2','col-sm-6', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Da data'), $de ],
                                       [ new TLabel('Até a data'), $ate ]);
        $row->layout = ['col-sm-2','col-sm-2'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ContaReceber_filter_data') );
        $this->form->setData( TSession::setValue('ContaReceberList', parse_url($_SERVER['REQUEST_URI'])) );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ContaReceberForm', 'onEdit']), 'fa:plus green');
        $btn2 = $this->form->addAction('Baixar Títulos', new TAction([$this, 'onBaixar']), 'fa:plus green');
        $btn3 = $this->form->addAction('Juntar Títulos', new TAction([$this, 'onJuntar']), 'fa:plus red');
        $btn4 = $this->form->addAction('PDF', new TAction(['RelContaReceber', 'onViewPDF']), 'fa:table');
        $btn5 = $this->form->addAction('CSV', new TAction([$this, 'onExportCSV']), 'fa:table');

        //$this->form->addExpandButton();

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
        $this->formGrid->addField($btn3);
        $this->formGrid->addField($btn4);
        $this->formGrid->addField($btn5);

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

        $column_id->setTransformer(array($this, 'formatColor'));

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

        $column_valor->setTotalFunction( function($values) {
            $total = array_sum((array) $values);
            $total = Utilidades::formatar_valor($total); //(is_numeric($total)) ? round($total,2) : 0;
            return '<div id="total_devido"> <b>Total: R$</b> ' . $total  . '</div>';
        });

        $action1 = new TDataGridAction(array('ContaReceberFormParcial', 'onEdit'));
        $action1->setLabel('Baixa Parcial');
        $action1->setImage('fas:sort-amount-up-alt black');
        $action1->setField('id');
        $action1->setDisplayCondition( array($this, 'displayColumn') );
        
        $action2 = new TDataGridAction(array('ContaReceberFormBaixa', 'onEdit'));
        $action2->setLabel('Baixar Individual');
        $action2->setImage('fas:angle-double-down black');
        $action2->setField('id');
        $action2->setDisplayCondition( array($this, 'displayColumn') );

        $action3 = new TDataGridAction(array($this, 'onGerar'));
        $action3->setLabel('Gerar NFS-e');
        $action3->setImage('fas:space-shuttle black');
        $action3->setField('id');
        //$action3->setDisplayCondition( array($this, 'displayColumn') );

        $action4 = new TDataGridAction(array('RelReciboContaReceber', 'onViewReciboContaReceber'));
        $action4->setLabel('Recibo de Receitas');
        $action4->setImage('fas:receipt black');
        $action4->setField('id');
        $action4->setField('cliente_id');

        $action4 = new TDataGridAction(array($this, 'gerarBoletoAvulso'));
        $action4->setLabel('Boleto Avulso');
        $action4->setImage('fas:money-check-alt black');
        $action4->setField('id');

        $action_group = new TDataGridActionGroup('', 'fas:cog');

        $action_group->addHeader('Opções');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action3);
        $action_group->addAction($action4);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);

        // create EDIT action
        $action_edit = new TDataGridAction(['ContaReceberForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $action_edit->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $action_del->setDisplayCondition( array($this, 'displayColumn') );
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
        // TPage::include_js('app/resources/ExpandButton.js');

        //$this->form->add(ButtonExpand::expandButton(__CLASS__));

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->formGrid, $this->pageNavigation));

        parent::add($container);
     
    }   

    public static function onClose(){}
    
    public function displayColumn( $object )
    {
        
        if($object->baixa == 'N')
        {
            return TRUE;
        }
        return FALSE;
    }

    public function formatColor($column_id, $object, $row)
    {
        $data = new ContaReceber($column_id);

        if ($data->baixa == "S")
        {
            $row->style = "background: #97f498";
            return "<span>$column_id</span>";
        }else{
            return "<span>$column_id</span>";
        }
    }

    public static function onCalcularTotal() {

        $items_devido = (array) TSession::getValue(__CLASS__.'_items');

        $total_devido = array_reduce($items_devido, function ($carry, $item) {
            $carry += Utilidades::to_number($item['valor_pago']);
            return $carry;
        }, 0);

        $total_devido_str = Utilidades::formatar_valor($total_devido);

        TScript::create(" $('#total_devido').text('{$total_devido_str}') ");

        $data = new stdClass();
        $data->total_devido = 'R$ '. $total_devido_str;

        TForm::sendData( 'form_ContaReceberList', $data );

        $vlr = $total_devido;
    }

    public function gerarBoletoAvulso( $param )
    {

        try {

            TTransaction::open('sample');

            $key = $param['id'];

            $conta_receber = new ContaReceber($key);
            
            if($conta_receber->boleto_emitido == "S"){
                throw new Exception('ATENÇÃO: Boleto já emitido para essa conta!');
            }

            $conta_receber->pedido_numero = rand(0, 99999) + time();

            $dados_api_integracao = new ApiIntegracao(1);
    
            $credencial = $dados_api_integracao->credencial;
            $chave      = $dados_api_integracao->chave;
            $ambiente   = $dados_api_integracao->url;
            $split   = $dados_api_integracao->split;

            $boleto_formato = 'BOLETO';

            // $chave      = 'f2b1255ce58534c75077a70c25a7c586';
            // $ambiente   = 'https://api.iugu.com/v1/invoices';
            // BoletoServiceIugu::emitirBoleto($credencial, $chave, $ambiente, $boleto_formato,
            // $conta_receber->cliente_contrato_id,$conta_receber->data_vencimento, 
            // $conta_receber->valor_pago, 'REFERENTE AO CONTRATO Nº ' . $conta_receber->cliente_contrato_id,
            // $conta_receber->pedido_numero,
            // '0', '0', '0', 'Boletos', '0', '0', null, null, null, null, null, null, '0','', $conta_receber->id, $conta_receber->cliente_id);

            BoletoService::emitirBoleto($credencial, $chave, $ambiente, $boleto_formato,
                                        $conta_receber->cliente_contrato_id, $conta_receber->data_vencimento, 
                                        $conta_receber->valor_pago, $conta_receber->descricao,
                                        $conta_receber->pedido_numero,
                                        1, 2, '0', 'Boletos', '0', '0', null, null, null, null, null, null, '0','', $conta_receber->id, $conta_receber->cliente_id,$split,$conta_receber->unit_id,$conta_receber->user_id);
 
            
            $conta_receber->boleto_emitido = "S";
            $conta_receber->store();
            
            TTransaction::close();

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }

    }


    public function onGerar( $param )
    {

        $key = $param['id'];

        TTransaction::open('sample');

        $receitas = ContaReceber::where('nfse','=','N')
                            ->where('id','=',$key)
                            //->where('MONTH(data_vencimento)','=','NOESC:MONTH(NOW())')
                            ->where('unit_id','=',TSession::getValue('userunitid'))
                            ->load();

        $unidade = new SystemUnit(TSession::getValue('userunitid'));

        if($receitas)
        {
            $nfseParametro = new NfseParametro(1);
            $lote =  $nfseParametro->ultimoNumeroLote + 1;

            foreach ($receitas as $dados) {
        
            $cliente = new Cliente($dados->cliente_id);

            if($cliente->gera_nfse == 'N'){

                new TMessage('error', 'Cliente '.$cliente->razao_social.' não tem a opção NFs-e habilitada como SIM em seu devido cadastro! Para emitir uma NFse é necessário que essa opção estaja marcada como SIM.');
                die;

            }

            $nfse->TcpfCnpj = $cliente->cpf_cnpj;
            $nfse->TrazaoSocial = $cliente->razao_social;

            $Endereco = ClienteEndereco::where('cliente_id', '=', $dados->cliente_id)->first();

            if($Endereco){

                $nfse->Tlogradouro = $Endereco->logradouro;
                $nfse->Tnumero = $Endereco->numero;
                $nfse->Tcidade = $Endereco->cidade;
                $nfse->Tbairro = $Endereco->bairro;
                $nfse->Tuf = $Endereco->uf;
                $nfse->Tcomplemento = $Endereco->complemento;
                $nfse->TcodigoCidade = $Endereco->codMuni;
                $nfse->Tcep = $Endereco->cep;

            }else{

                new TMessage('error', 'Cliente '.$cliente->razao_social.' não tem endereço cadastrado!');
                die;
            }

            $Email = EmailCliente::where('cliente_id', '=', $dados->cliente_id)->first();
            
            if($Email == null)
            {
                new TMessage('error', 'Cliente '.$cliente->razao_social.' não tem email cadastrado!');
                die;
            }

            $numero = $nfseParametro->ultimoNumeroNfse + 1;
            $nfseParametro->ultimoNumeroNfse = $numero;
            $nfseParametro->store();

            $nfse = new NFSe();
            $nfse->unit_id = $dados->unit_id;
            $nfse->enviarEmail = $nfseParametro->enviarEmail;
            $nfse->dataEmissao = date('Y-m-d H:i:s');
            $nfse->competencia = $dados->data_vencimento;
            $nfse->Temail = $Email->email;

            $pcServico = new PcReceita($dados->pc_receita_id);

            $nfse->Scodigo = $$pcServico->Scodigo;
            $nfse->Sdiscriminacao = $pcServico->Sdiscriminacao;
            $nfse->Scnae = $pcServico->Scnae;
            
            $nfse->ISSaliquota = $nfseParametro->IssAliquota;
            $nfse->ISStipoTributacao = $nfseParametro->tipoTributacao; //6 - Tributável Dentro do Município
            //$nfse->ISSretido = $nfseParametro->IssRetido;

            $nfse->total_servico = $dados->valor_pago;
            $nfse->base_calculo = $dados->valor_pago;

            $nfse->RetCofins = $nfseParametro->RetCofins;
            $nfse->RetCsll = $nfseParametro->RetCsll;
            $nfse->RetInss = $nfseParametro->RetInss;
            $nfse->RetIrrf = $nfseParametro->RetIrrf;
            $nfse->RetPis = $nfseParametro->RetPis;
            $nfse->RetOutros = $nfseParametro->RetOutros;

            //$nfse->ISSvalor = $nfseParametro->IssValor;
            //colocar caso tiver IssValorRetido no model
            $nfse->status = 'NFSe Gerada pronta para Transmitir';
            $nfse->numeroNfse = $numero;
            $nfse->lote = $lote;
            $nfse->conta_receber_id = $dados->id;
            $nfse->cliente_id = $dados->cliente_id;
            $nfse->tipo = "G";
            $nfse->observacao = $dados->observacao;
            $nfse->store();

            $nfseItem = new NfseItens();
            $nfseItem->nfse_id = $nfse->id;
            $nfseItem->descricao = $dados->descricao;
            $nfseItem->valor = $dados->valor_pago;
            $nfseItem->quantidade = 1;
            $nfseItem->total_item = $dados->valor_pago;
            $nfseItem->store();

            $updateCR = new ContaReceber($dados->id);
            $updateCR->nfse = 'S';
            $updateCR->store();

            }

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', 'Notas Geradas com Sucesso!', $pos_action);

        }else{

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', 'Não foi possível gerar NFS-e, verifique se a opção de Gerar notas esta marcada como Sim! Se estiver, significa que já foi gerado uma NFS-e para essa conta!', $pos_action);

        }

        TTransaction::close();
    }

    public function onJuntar( $param )
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
            $valorSplit = 0.00;

            foreach ($selected as $id) 
            {
                $cr = new ContaReceber($id);

                if($cr->baixa == "N")
                {
                    $cr->data_conta = date('Y-m-d');
                    //$cr->data_vencimento = date('Y-m-d');
                    $cr->baixa = 'S';
                    $cr->split = 'S';
                    $cr->data_baixa = date('Y-m-d');
                    $valorSplit = $valorSplit + $cr->valor;
                    $cr->store();   
                }else{

                    new TMessage('error', 'Não pode juntar títulos já baixados.',$this->onReload($param));
                   
                }
            }   


            $split = new ContaReceber();
            $split->data_conta = date('Y-m-d');
            $split->descricao = $cr->descricao;
            $split->documento = $cr->documento;
            $split->data_vencimento = date('Y-m-d');
            $split->baixa = 'N';
            $split->data_baixa = date('Y-m-d');
            $split->valor = $valorSplit;
            $split->valor_real = $valorSplit;
            $split->responsavel = $cr->responsavel;
            $split->unit_id = $cr->unit_id;
            $split->user_id = $cr->user_id;
            $split->tipo_pgto_id = 1;
            $split->tipo_forma_pgto_id = 1;
            $split->cliente_id = $cr->cliente_id;
            $split->pc_receita_id = $cr->pc_receita_id;
            $split->pc_receita_nome = $cr->pc_receita_nome;
            $split->departamento_id = $cr->departamento_id;
            $split->conta_bancaria_id = $cr->conta_bancaria_id;
            $split->store();

            TTransaction::close();
            $this->onReload($param);
            
            TSession::delValue(__CLASS__.'_selected_objects');

        } catch (Exception $e) {
                TTransaction::rollback();
                new TMessage('error', 'Houve um problema ao juntar os Títulos. <br>' . $e->getMessage());
                $this->form->setData($data);
                return;
        }
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

                if($cr->baixa == "N")
                {
                    $baix = $cr->baixa = 'S';
                    $cr->data_baixa = date('Y-m-d');
                    $cr->store();

                    // Baixa os boletos tbm     
                    $boletos = Boletos::where('ativo','=',true)->where('conta_receber_id','=',$id)->load();     
                    foreach($boletos as $boleto){       
                        
                        $boleto->data_recebimento = date('Y-m-d');      
                        $boleto->ativo = false;     
                        $boleto->store();       
                    }

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

                    Comissionamento::gerarComissao($cr->cliente_id, $cr->id);
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
                new TMessage('erro', '<b>Atenção!</b> Algo do tipo pode estar acontecendo:<br>Você não marcou nenhum título!<br>Ou ele já foi baixado.<br><b>Escolha outro.</b><br>',$pos_action);
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
        TSession::setValue('ContaReceberList_filter_id',   NULL);
        TSession::setValue('ContaReceberList_filter_descricao',   NULL);
        TSession::setValue('ContaReceberList_filter_documento',   NULL);
        TSession::setValue('ContaReceberList_filter_data_vencimento',   NULL);
        TSession::setValue('ContaReceberList_filter_valor',   NULL);
        TSession::setValue('ContaReceberList_filter_cliente_id',   NULL);
        TSession::setValue('ContaReceberList_filter_tipo_pgto_id',   NULL);
        TSession::setValue('ContaReceberList_filter_pc_receita_id',   NULL);
        TSession::setValue('ContaReceberList_filter_conta_bancaria_id',   NULL);
        TSession::setValue('ContaReceberList_filter_baixa',   NULL);
        TSession::setValue('ContaReceberList_filter_de',   NULL);
        TSession::setValue('ContaReceberList_filter_ate',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "{$data->id}"); // create the filter
            TSession::setValue('ContaReceberList_filter_id',   $filter); // stores the filter in the session
        }

        if (isset($data->documento) AND ($data->documento)) {
            $filter = new TFilter('documento', 'like', "%{$data->documento}%"); // create the filter
            TSession::setValue('ContaReceberList_filter_documento',   $filter); // stores the filter in the session
        }

        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
            TSession::setValue('ContaReceberList_filter_descricao',   $filter); // stores the filter in the session
        }

        if (isset($data->data_vencimento) AND ($data->data_vencimento)) {
            $filter = new TFilter('data_vencimento', 'like', "%{$data->data_vencimento}%"); // create the filter
            TSession::setValue('ContaReceberList_filter_data_vencimento',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue('ContaReceberList_filter_valor',   $filter); // stores the filter in the session
        }


        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', '=', "$data->cliente_id"); // create the filter
            TSession::setValue('ContaReceberList_filter_cliente_id',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo_pgto_id) AND ($data->tipo_pgto_id)) {
            $filter = new TFilter('tipo_pgto_id', '=', "$data->tipo_pgto_id"); // create the filter
            TSession::setValue('ContaReceberList_filter_tipo_pgto_id',   $filter); // stores the filter in the session
        }


        if (isset($data->pc_receita_id) AND ($data->pc_receita_id)) {
            $filter = new TFilter('pc_receita_id', '=', "$data->pc_receita_id"); // create the filter
            TSession::setValue('ContaReceberList_filter_pc_receita_id',   $filter); // stores the filter in the session
        }


        if (isset($data->conta_bancaria_id) AND ($data->conta_bancaria_id)) {
            $filter = new TFilter('conta_bancaria_id', '=', "$data->conta_bancaria_id"); // create the filter
            TSession::setValue('ContaReceberList_filter_conta_bancaria_id',   $filter); // stores the filter in the session
        }

        if (isset($data->baixa) AND ($data->baixa)) {
            $filter = new TFilter('baixa', '=', "{$data->baixa}"); // create the filter
            TSession::setValue('ContaReceberList_filter_baixa',   $filter); // stores the filter in the session
        }

        if (isset($data->de) AND ($data->de)) {
            
            $filter = new TFilter('data_vencimento', '>=', "{$data->de}");
            TSession::setValue('ContaReceberList_filter_de',   $filter); 
        }

        if (isset($data->ate) AND ($data->ate)) {

            $filter = new TFilter('data_vencimento', '<=', "{$data->ate}");
            TSession::setValue('ContaReceberList_filter_ate',   $filter); 
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
            $limit = 15;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_vencimento';
                $param['data_vencimento'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));
            //$criteria->add(new TFilter('baixa',  '=', '?'));
            $criteria->add(new TFilter('deleted_at', 'is', null));

            if (TSession::getValue('ContaReceberList_filter_id')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_id')); // add the session filter
            }

            if (TSession::getValue('ContaReceberList_filter_documento')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_documento')); // add the session filter
            }

            if (TSession::getValue('ContaReceberList_filter_descricao')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_descricao')); // add the session filter
            }


            if (TSession::getValue('ContaReceberList_filter_data_vencimento')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_data_vencimento')); // add the session filter
            }


            if (TSession::getValue('ContaReceberList_filter_valor')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_valor')); // add the session filter
            }


            if (TSession::getValue('ContaReceberList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_cliente_id')); // add the session filter
            }


            if (TSession::getValue('ContaReceberList_filter_tipo_pgto_id')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_tipo_pgto_id')); // add the session filter
            }


            if (TSession::getValue('ContaReceberList_filter_pc_receita_id')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_pc_receita_id')); // add the session filter
            }


            if (TSession::getValue('ContaReceberList_filter_conta_bancaria_id')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_conta_bancaria_id')); // add the session filter
            }

            if (TSession::getValue('ContaReceberList_filter_baixa')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_baixa')); // add the session filter
            }

            if (TSession::getValue('ContaReceberList_filter_de')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_de')); // add the session filter
            }

            if (TSession::getValue('ContaReceberList_filter_ate')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_ate')); // add the session filter
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
                            __adianti_ajax_exec(\'class=ContaReceberList&method=onSelect&id=\'+code+\'&check=\'+value_check);
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
    

    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch','onJuntar','onBaixar')))) )
        {
            if (func_num_args() > 0)
            {   
                $filter = new TFilter('baixa', '=', "N"); // create the filter
                TSession::setValue('ContaReceberList_filter_baixa',   $filter);
                $this->onReload( func_get_arg(0) );
            }
            else
            {   
                $filter = new TFilter('baixa', '=', "N"); // create the filter
                TSession::setValue('ContaReceberList_filter_baixa',   $filter);
                $this->onReload();
            }
        }
        parent::show();
    }

    public function onExportCSV()
    {

        //$this->onSearch();

        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('sample');
                
            // creates a repository for Customer
            $repository = new TRepository('ContaReceber');
                
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));

            if (TSession::getValue('ContaReceberList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_cliente_id'));
            }

            if (TSession::getValue('ContaReceberList_filter_pc_despesa_id')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_pc_despesa_id'));
            }

            if (TSession::getValue('ContaReceberList_filter_descricao')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_descricao'));
            }

            if (TSession::getValue('ContaReceberList_filter_documento')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_documento')); 
            }

            if (TSession::getValue('ContaReceberList_filter_data_vencimento')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_data_vencimento')); 
            }

            if (TSession::getValue('ContaReceberList_filter_valor')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_valor')); 
            }

            if (TSession::getValue('ContaReceberList_filter_baixa')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_baixa')); 
            }

            $csv = '';
            // load the objects according to criteria
            $customers = $repository->load($criteria, false);
            if ($customers)
            {
                $csv .= 'Id'.';'.'Cliente'.';'.'Plano de contas'.';'.'Descrição'.';'.'Documento'.';'.
                'Vencimento'.';'.'Valor'."\n";
                $valorTotal = 0;
                foreach ($customers as $customer)
                {
                    $partes = explode(" ", $customer->data_vencimento);
                    $data = explode('-', $partes[0]);

                    $csv .= $customer->id.';'.
                            $customer->cliente->nome_fantasia.';'.
                            $customer->pc_receita->nome.';'.
                            $customer->descricao.';'.
                            $customer->documento.';'.
                            $data[2].'/'.$data[1].'/'.$data[0].';'.
                            $customer->valor."\n";
                            $valorTotal += $customer->valor;
                }

                $csv .= ' '.';'.' '.';'.' '.';'.' '.';'.' '.';'.
                ' '.';'.$valorTotal."\n";

                file_put_contents('app/output/contasreceber.csv', $csv);
                TPage::openFile('app/output/contasreceber.csv');
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
