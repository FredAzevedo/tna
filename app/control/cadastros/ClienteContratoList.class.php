<?php
/**
 * ClienteContratoList Listing
 * @author Fred Avz.
 */

use Adianti\Control\TPage;
use Adianti\Widget\Dialog\TMessage;
use Carbon\Carbon;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bancoob;
use Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Pessoa;
use Eduardokum\LaravelBoleto\Boleto\Render\PdfCarne;
use Eduardokum\LaravelBoleto\Util;

class ClienteContratoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ClienteContrato');
        $this->form->setFormTitle('Gestão de Contratos');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');

        $nome = new TEntry('nome');
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $tipo_endereco_id = new TDBCombo('tipo_endereco_id', 'sample', 'TipoEndereco', 'id', 'nome');
        $relatorio_customizado_id = new TDBCombo('relatorio_customizado_id', 'sample', 'RelatorioCustomizado', 'id', 'nome');
        $inicio_vigencia = new TDate('inicio_vigencia');
        $inicio_vigencia->setDatabaseMask('yyyy-mm-dd hh:ii');
        $inicio_vigencia->setMask('dd/mm/yyyy hh:ii');

        $fim_vigencia = new TDate('fim_vigencia');
        $fim_vigencia->setDatabaseMask('yyyy-mm-dd hh:ii');
        $fim_vigencia->setMask('dd/mm/yyyy hh:ii');


        // add the fields
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Cliente / Razão Social'), $cliente_id ],
                                       [ new TLabel('Tipo de Endereço'), $tipo_endereco_id ]);
        $row->layout = ['col-sm-2', 'col-sm-6', 'col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Modelo de Contrato'), $relatorio_customizado_id ],
                                       [ new TLabel('Início da Virgência'), $inicio_vigencia ],
                                       [ new TLabel('Fim da Virgência'), $fim_vigencia ]);
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-2'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ClienteContrato_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ClienteContratoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_cliente_id = new TDataGridColumn('cliente->razao_social', 'Cliente/Razão Social', 'left');
        $column_tipo_endereco_id = new TDataGridColumn('tipo_endereco->nome', 'Tipo de Endereço', 'left');
        $column_relatorio_customizado_id = new TDataGridColumn('relatorio_customizado->nome', 'Modelo de Contrato', 'left');
        $column_inicio_vigencia = new TDataGridColumn('inicio_vigencia', 'Inicio Vigência', 'center');
        $column_fim_vigencia = new TDataGridColumn('fim_vigencia', 'Fim Vigência', 'center');
        $status = new TDataGridColumn('statusContrato', 'Status', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        //$this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cliente_id);
        //$this->datagrid->addColumn($column_tipo_endereco_id);
        //$this->datagrid->addColumn($column_relatorio_customizado_id);
        $this->datagrid->addColumn($column_inicio_vigencia);
        $this->datagrid->addColumn($column_fim_vigencia);
        $this->datagrid->addColumn($status);

        $column_inicio_vigencia->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $column_fim_vigencia->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $action1 = new TDataGridAction(array('OsContrato', 'onViewContrato'));
        $action1->setLabel('Contrato');
        $action1->setImage('fa:file-pdf-o red');
        $action1->setField('id');
        $action1->setField('cliente_id');

        $action2 = new TDataGridAction(array($this, 'gerarCarne'));
        $action2->setLabel('Gerar Carnê');
        $action2->setImage('fa:file-pdf-o red');
        $action2->setField('id');
        $action2->setField('cliente_id');
        
        $action_group = new TDataGridActionGroup('Ações ', 'bs:th');

        $action_group->addHeader('Opções');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);


        // create EDIT action
        $action_edit = new TDataGridAction(['ClienteContratoForm', 'onEdit']);
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

    public function onExportCSV()
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('sample');
                
            // creates a repository for Customer
            $repository = new TRepository('ClienteContrato');
                
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));

            if (TSession::getValue('ClienteContratoList_filter_id')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_id')); // add the session filter
            }

            if (TSession::getValue('ClienteContratoList_filter_nome')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_nome')); // add the session filter
            }

            if (TSession::getValue('ClienteContratoList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_cliente_id')); // add the session filter
            }

            if (TSession::getValue('ClienteContratoList_filter_plano_id')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_plano_id')); // add the session filter
            }

            if (TSession::getValue('ClienteContratoList_filter_user_id')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_user_id')); // add the session filter
            }

            if (TSession::getValue('ClienteContratoList_filter_tipo_endereco_id')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_tipo_endereco_id')); // add the session filter
            }

            if (TSession::getValue('ClienteContratoList_filter_relatorio_customizado_id')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_relatorio_customizado_id')); // add the session filter
            }

            if (TSession::getValue('ClienteContratoList_filter_inicio_vigencia')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_inicio_vigencia')); // add the session filter
            }

            if (TSession::getValue('ClienteContratoList_filter_fim_vigencia')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_fim_vigencia')); // add the session filter
            }

            if (TSession::getValue('ClienteContratoList_filter_contrato_situacao')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_contrato_situacao')); // add the session filter
            }

            if (TSession::getValue('ContaReceberList_filter_data_inicio_filtro')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_data_inicio_filtro')); // add the session filter
            }

            if (TSession::getValue('ContaReceberList_filter_data_fim_filtro')) {
                $criteria->add(TSession::getValue('ContaReceberList_filter_data_fim_filtro')); // add the session filter
            }
    
           

            $csv = '';
            // load the objects according to criteria
            $customers = $repository->load($criteria, false);
            if ($customers)
            {
                // $csv .= 'Id'.';'.'Cliente'.';'.'Plano de contas'.';'.'Descrição'.';'.'Documento'.';'.
                // 'Vencimento'.';'.'Valor'."\n";

                $csv .= 'N° Contrato'.';'.'Cliente'.';'.'Plano'.';'.'Inicio Vigência'.';'.'Fim Vigência'.';'.
                'Tipo Pgto Plano'.';'.'Tipo Pgto Entrada'.';'.'Venda'.';'.'Situação'.';'.'Valor'.';'.'Vendedor'."\n";
                
                $valorTotal = 0;
                foreach ($customers as $customer)
                {
                    $partes = explode(" ", $customer->data_vencimento);
                    $data = explode('-', $partes[0]);

                    $csv .= $customer->id.';'.
                            $customer->cliente->nome_fantasia.';'.
                            $customer->plano->nome.';'.
                            $customer->inicio_vigencia.';'.
                            $customer->fim_vigencia.';'.
                            $customer->tipo_pagamento->nome.';'.
                            $customer->entrada_tipo_pagamento->nome.';'.
                            $customer->contrato_situacao->descricao.';'.
                            $customer->status.';'.
                            $customer->valor.';'.
                            $customer->system_user->name."\n";
                }


                // $csv .= ' '.';'.' '.';'.' '.';'.' '.';'.' '.';'.
                // ' '.';'.' '.';'.' '.';'.' '.';'.' '."\n";

                $csv .= ' '.';'.' '.';'.' '.';'.' '.';'.' '.';'.
                ' '.';'.' '.';'.' '.';'.' '.';'.' '.';'.' '."\n";

                file_put_contents('app/output/contratos.csv', $csv);
                TPage::openFile('app/output/contratos.csv');
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

    public function gerarCarne( $param )
    {   

        try
        {   
            $key   = $param['id'];

            TTransaction::open('sample');

            $object = new ClienteContrato($key);

            $target_folder_carne = $object->carne_path_pdf;

            if ($target_folder_carne && file_exists($target_folder_carne)) {

                parent::openFile($target_folder_carne);
                return;
            }




            $cliente = new Cliente($param['cliente_id']);
            $conta = new ContaBancaria($object->conta_bancaria_id);
            $endereco = ClienteEndereco::where('cliente_id', '=', $cliente->id)->first();

            if($object->status != 'F')
            {
                $object->nome = $cliente->razao_social;
                //$unit = new SystemUnit($object->unit_id); 
                $carne = new PdfCarne();

                $formaPagamento = new FormaPagamento($object->total,$object->TipoFormaPgto->regra,$object->vencimento_primeira_parcela);

                $banco = $conta->banco;

                $image_name = $banco->num_banco . '.png';
                $image_path = realpath(PATH . '/vendor/eduardokum/laravel-boleto/logos/') . DIRECTORY_SEPARATOR . $image_name;

                $numero = $conta->ultimo_nossonumero + 1; //nosso numero = 1;

                $carteira = $conta->carteira;
                $agencia = $conta->agencia;
                $codigo_cooperativa = $conta->codigo_cooperativa;//$conta->agencia . $conta->agencia_dv;
                $variacaoCarteira = $conta->variacaoCarteira;
                $cip = $conta->cip;
                $byte = $conta->byte;
                $posto = $conta->posto;
                $contaDv = $conta->contaDv;
                $campo_range = $conta->campo_range;
                $codigoCliente = $conta->codigoCliente;
                $convenio = $conta->convenio;
                $conta_numero = $conta->conta . $conta->conta_dv;
                $aceite = $conta->aceite;
                $especieDoc = $conta->especieDoc;
                $nbeneficiario = $conta->beneficiario;

                $valor = $object->valor_parcelado;

                $instrucao = [];
                for ($i = 1; $i <= 4; $i++) {
                    $inst = "instrucoes{$i}";
                    if ($conta->$inst) {
                        array_push($instrucao, $conta->$inst);
                    }
                }

                $beneficiario = new Pessoa(
                    [
                        'nome'      => $conta->system_unit->razao_social,
                        'endereco'  => $conta->system_unit->logradouro,
                        'cep'       => $conta->system_unit->cep,
                        'uf'        => $conta->system_unit->uf,
                        'cidade'    => $conta->system_unit->cidade,
                        'documento' => $conta->system_unit->cnpj
                    ]
                );
                $pagador = new Pessoa(
                    [
                        'nome'      => $cliente->razao_social,
                        'endereco'  => $endereco->logradouro, // 'Rua um, 123',
                        'bairro'    => $endereco->bairro, //'Bairro',
                        'cep'       => $endereco->cep, //'99999-999',
                        'uf'        => $endereco->uf, //'UF',
                        'cidade'    => $endereco->cidade, //'CIDADE',
                        'documento' => $cliente->cpf_cnpj,
                    ]
                );

                $count = 1;
                for($i = 0; $i < $formaPagamento->numero_parcelas; ++$i) {

                    $contadorParcelas = $count++;
                    $vencimentoCadaParcela = $formaPagamento->vencimentobd[$i];
                    $vencimento = new \Carbon\Carbon($vencimentoCadaParcela);
                    $numero = $conta->ultimo_nossonumero + 1; //nosso numero = 1;
                    $numeroDocumento = $object->unit_id . ".". $object->user_id . "." . $object->id . "-" . ($i + 1);
        
                    $dados_boleto = [
                        'logo'                   => $image_path,
                        'dataVencimento'         => $vencimento,
                        'valor'                  => $valor,
                        'multa'                  => false,
                        'juros'                  => false,
                        'numero'                 => $numero,
                        'numeroDocumento'        => $numeroDocumento,
                        'pagador'                => $pagador,
                        'beneficiario'           => $beneficiario,
                        'carteira'               => $carteira,
                        'byte'                   => 1,
                        'agencia'                => $agencia,
                        'posto'                  => 1,
                        'convenio'               => $convenio,
                        'conta'                  => $conta_numero,
                        'descricaoDemonstrativo' => [], //['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
                        'instrucoes'             => $instrucao,
                        'aceite'                 => $aceite,
                        'especieDoc'             => $especieDoc,
                    ];

                    if ($banco->num_banco == BoletoContract::COD_BANCO_BANCOOB) {
                        $boleto = new Bancoob($dados_boleto);
                        $boleto->setAgencia($codigo_cooperativa);
                    } else if ($banco->num_banco == BoletoContract::COD_BANCO_BANRISUL) {
                        $boleto = new Banrisul($dados_boleto);
                    } else if ($banco->num_banco == BoletoContract::COD_BANCO_BB) {
                        $boleto = new Bb($dados_boleto);
                        $boleto->setVariacaoCarteira($variacaoCarteira);
                    } else if ($banco->num_banco == BoletoContract::COD_BANCO_SICREDI) {
                        $boleto = new Sicredi($dados_boleto);
                        $boleto->setByte(Util::onlyNumbers($byte));
                        $boleto->setPosto(Util::onlyNumbers($posto));
                        $boleto->setConta(Util::onlyNumbers($nbeneficiario));
                    } else if ($banco->num_banco == BoletoContract::COD_BANCO_BNB) {
                        $boleto = new Bnb($dados_boleto);
                    } else if ($banco->num_banco == BoletoContract::COD_BANCO_BRADESCO) {
                        $boleto = new Bradesco($dados_boleto);
                        $boleto->setCip(Util::onlyNumbers($cip));
                    } else if ($banco->num_banco == BoletoContract::COD_BANCO_CEF) {
                        $boleto = new Caixa($dados_boleto);
                        $boleto->setCodigoCliente($codigoCliente);
                    } else if ($banco->num_banco == BoletoContract::COD_BANCO_HSBC) {
                        $boleto = new Hsbc($dados_boleto);
                        $boleto->setRange($campo_range);
                        $boleto->setContaDv($contaDv);
                    } else if ($banco->num_banco == BoletoContract::COD_BANCO_ITAU) {
                        $boleto = new Itau($dados_boleto);
                    } else if ($banco->num_banco == BoletoContract::COD_BANCO_SANTANDER) {
                        $boleto = new Santander($dados_boleto);
                        $boleto->setCodigoCliente(Util::onlyNumbers($codigoCliente));
                    } else{
                        throw new Exception('Banco não suportado para emitir boleto.');
                    }
                    
                    $carne->addBoleto($boleto);

                    $ContasReceber = new ContaReceber();
                    $ContasReceber->data_conta = $object->inicio_vigencia;     
                    $ContasReceber->descricao = 'REFERENTE AO CONTRATO Nº '.$object->id; 
                    $ContasReceber->documento = $numeroDocumento; //$numero; //COLOCAR O NUMERO ÚNICO DO CARNE
                    $ContasReceber->data_vencimento = $vencimentoCadaParcela; 
                    $ContasReceber->valor = $formaPagamento->valor_parcela;
                    $ContasReceber->baixa = 'N'; 
                    $ContasReceber->parcelas = $contadorParcelas;
                    $ContasReceber->nparcelas = $object->qtd_parcelas; 
                    $ContasReceber->replica = 'N'; 
                    $ContasReceber->unit_id = $object->unit_id; 
                    $ContasReceber->cliente_id = $object->cliente_id;
                    $ContasReceber->tipo_pgto_id = $object->tipo_pgto_id;
                    $ContasReceber->tipo_forma_pgto_id = $object->tipo_forma_pgto_id;
                    $ContasReceber->user_id = $object->user_id;
                    $ContasReceber->pc_receita_id = $object->plano->pc_receita_id;
                    $ContasReceber->pc_receita_nome = $object->plano->pc_receita_nome;
                    $ContasReceber->conta_bancaria_id = $object->conta_bancaria_id;
                    $ContasReceber->cliente_contrato_id = $object->id;
                    $ContasReceber->gerar_boleto = 'S';
                    $ContasReceber->store();

                    $conta->ultimo_nossonumero = $numero;
                    $conta->store();

                    $remessa = new Boletos();
                    $remessa->dataVencimento = $boleto->getDataVencimento()->format('Y-m-d');//$data->data_vencimento;
                    $remessa->valor = $boleto->getValor();//$object->valor;
                    $remessa->multa = null;
                    $remessa->juros = null;
                    $remessa->numero = $boleto->getNumero();
                    $remessa->numeroDocumento = $boleto->getNumeroDocumento();
                    $remessa->carteira = $boleto->getCarteira();
                    $remessa->conta_bancaria_id = $object->conta_bancaria_id;
                    $remessa->agencia = $boleto->getAgencia();
                    //$remessa->convenio = $boleto->getConvenio();
                    $remessa->conta = $boleto->getConta();
                    $remessa->instrucao1 = $conta->instrucoes1;
                    $remessa->instrucao2 = $conta->instrucoes2;
                    $remessa->instrucao3 = $conta->instrucoes3;
                    $remessa->instrucao4 = $conta->instrucoes4;
                    $remessa->cliente_id = $object->cliente_id;
                    $remessa->conta_receber_id = $ContasReceber->id;
                    $remessa->cliente_contrato_id = $object->id;
                    $remessa->unit_id = $object->unit_id;
                    $remessa->num_parcela = $contadorParcelas .'/'. $object->qtd_parcelas;
                    $remessa->remessa = 'N';
                    $remessa->cod_banco = $boleto->getCodigoBanco();
                    $remessa->aceite = $boleto->getAceite();
                    $remessa->especieDoc = $boleto->getEspecieDoc();
                    $remessa->pag_nome      = $cliente->razao_social;
                    $remessa->pag_endereco  = $endereco->logradouro; // 'Rua um, 123',
                    $remessa->pag_bairro    = $endereco->bairro; //'Bairro',
                    $remessa->pag_cep       = $endereco->cep; //'99999-999',
                    $remessa->pag_uf        = $endereco->uf; //'UF',
                    $remessa->pag_cidade    = $endereco->cidade; //'CIDADE',
                    $remessa->pag_documento = $cliente->cpf_cnpj;

                    $remessa->ben_nome      = $conta->system_unit->razao_social;
                    $remessa->ben_endereco  = $conta->system_unit->logradouro;
                    $remessa->ben_bairro    = $conta->system_unit->bairro;
                    $remessa->ben_cep       = $conta->system_unit->cep;
                    $remessa->ben_uf        = $conta->system_unit->uf;
                    $remessa->ben_cidade    = $conta->system_unit->cidade;
                    $remessa->ben_documento = $conta->system_unit->cnpj;

                    if (method_exists($boleto, 'getCodigoCliente'))
                        $remessa->codigoCliente = $boleto->getCodigoCliente();

                    if (method_exists($boleto, 'getVariacaoCarteira'))
                        $remessa->variacaocarteira = $boleto->getVariacaoCarteira();

                    if (method_exists($boleto, 'getCip'))
                        $remessa->cip = $boleto->getCip();

                    if (method_exists($boleto, 'getRange'))
                        $remessa->campo_range = $boleto->getRange();

                    if (method_exists($boleto, 'getContaDv'))
                        $remessa->contaDv = $boleto->getContaDv();

                    if (method_exists($boleto, 'getPosto'))
                        $remessa->posto = $boleto->getPosto();

                    if (method_exists($boleto, 'getByte'))
                        $remessa->byte = $boleto->getByte();

                    if (method_exists($boleto, 'getConvenio'))
                        $remessa->convenio = $boleto->getConvenio();

                    $remessa->dataDesconto = $boleto->getDataDesconto()->format('Y-m-d');
                    $remessa->dataDocumento = $boleto->getDataDocumento()->format('Y-m-d');
                    $remessa->dataProcessamento = $boleto->getDataProcessamento()->format('Y-m-d');
                    $remessa->desconto = $boleto->getDesconto();
                    $remessa->jurosApos = $boleto->getJurosApos();
                    $remessa->store();

                }

                $date_name = new DateTime();
                $pdf_name = substr(md5($date_name->format('Y-m-d H:i:s-u') . $object->id),5,10);

                $nome_boleto = "{$pdf_name}-{$object->id}.pdf";

                $retorno = $carne->gerarBoleto($carne::OUTPUT_SAVE, PATH . '/tmp/' . $nome_boleto);

                $target_folder_carne = 'files/carne/' . $nome_boleto;

                $source_folder = 'tmp/' . $nome_boleto;

                if (file_exists($source_folder)) { //AND $finfo->file($source_file) == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                    if (file_exists($target_folder_carne)) {
                        unlink($target_folder_carne);
                    }
                    rename($source_folder, $target_folder_carne);
//                    return $target_folder_carne;
                }

                $object->carne_path_pdf = $target_folder_carne;
                $object->status = 'F';

                $object->store(); 

                parent::openFile($target_folder_carne);

                new TMessage('info', 'Carnê gerado ' . '<br>' . $target_folder_carne);
            }
            else
            {
                new TMessage('error', 'ATENÇÃO: Contrato já baixado no sistema!');
            }

            TTransaction::close(); 
            $this->onReload($param);
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
            $object = new ClienteContrato($key); // instantiates the Active Record
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
        TSession::setValue('ClienteContratoList_filter_id',   NULL);
        TSession::setValue('ClienteContratoList_filter_nome',   NULL);
        TSession::setValue('ClienteContratoList_filter_cliente_id',   NULL);
        TSession::setValue('ClienteContratoList_filter_tipo_endereco_id',   NULL);
        TSession::setValue('ClienteContratoList_filter_relatorio_customizado_id',   NULL);
        TSession::setValue('ClienteContratoList_filter_inicio_vigencia',   NULL);
        TSession::setValue('ClienteContratoList_filter_fim_vigencia',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('ClienteContratoList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); // create the filter
            TSession::setValue('ClienteContratoList_filter_nome',   $filter); // stores the filter in the session
        }


        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', '=', "$data->cliente_id"); // create the filter
            TSession::setValue('ClienteContratoList_filter_cliente_id',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo_endereco_id) AND ($data->tipo_endereco_id)) {
            $filter = new TFilter('tipo_endereco_id', '=', "$data->tipo_endereco_id"); // create the filter
            TSession::setValue('ClienteContratoList_filter_tipo_endereco_id',   $filter); // stores the filter in the session
        }


        if (isset($data->relatorio_customizado_id) AND ($data->relatorio_customizado_id)) {
            $filter = new TFilter('relatorio_customizado_id', '=', "$data->relatorio_customizado_id"); // create the filter
            TSession::setValue('ClienteContratoList_filter_relatorio_customizado_id',   $filter); // stores the filter in the session
        }


        if (isset($data->inicio_vigencia) AND ($data->inicio_vigencia)) {
            $filter = new TFilter('inicio_vigencia', 'like', "%{$data->inicio_vigencia}%"); // create the filter
            TSession::setValue('ClienteContratoList_filter_inicio_vigencia',   $filter); // stores the filter in the session
        }


        if (isset($data->fim_vigencia) AND ($data->fim_vigencia)) {
            $filter = new TFilter('fim_vigencia', 'like', "%{$data->fim_vigencia}%"); // create the filter
            TSession::setValue('ClienteContratoList_filter_fim_vigencia',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ClienteContrato_filter_data', $data);
        
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
            
            // creates a repository for ClienteContrato
            $repository = new TRepository('ClienteContrato');
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
            

            if (TSession::getValue('ClienteContratoList_filter_id')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ClienteContratoList_filter_nome')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_nome')); // add the session filter
            }


            if (TSession::getValue('ClienteContratoList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_cliente_id')); // add the session filter
            }


            if (TSession::getValue('ClienteContratoList_filter_tipo_endereco_id')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_tipo_endereco_id')); // add the session filter
            }


            if (TSession::getValue('ClienteContratoList_filter_relatorio_customizado_id')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_relatorio_customizado_id')); // add the session filter
            }


            if (TSession::getValue('ClienteContratoList_filter_inicio_vigencia')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_inicio_vigencia')); // add the session filter
            }


            if (TSession::getValue('ClienteContratoList_filter_fim_vigencia')) {
                $criteria->add(TSession::getValue('ClienteContratoList_filter_fim_vigencia')); // add the session filter
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
            $object = new ClienteContrato($key, FALSE); // instantiates the Active Record
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
