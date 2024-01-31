<?php
/**
 * ClienteContratoForm Form
 * @author  Fred Avz.
 */

use Adianti\Widget\Base\TScript;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bancoob;
use Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Pessoa;
use Eduardokum\LaravelBoleto\Util;

class ClienteContratoForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ClienteContrato');
        $this->form->setFormTitle('Gestão de Contratos');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userid'));
        $user_id->addValidation('Usuário', new TRequiredValidator);

        $nome = new TEntry('nome');
        $nome->addValidation('Nome do Contrato', new TRequiredValidator);
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $cliente_id->addValidation('Cliente', new TRequiredValidator);
        $tipo_endereco_id = new TDBCombo('tipo_endereco_id', 'sample', 'TipoEndereco', 'id', 'nome');
        $tipo_endereco_id->addValidation('Tipo de Endereço', new TRequiredValidator);
        $relatorio_customizado_id = new TDBCombo('relatorio_customizado_id', 'sample', 'RelatorioCustomizado', 'id', 'nome');
        $relatorio_customizado_id->addValidation('Modelo de Contrato', new TRequiredValidator);

        $inicio_vigencia = new TDate('inicio_vigencia');
        $inicio_vigencia->setDatabaseMask('yyyy-mm-dd');
        $inicio_vigencia->setMask('dd/mm/yyyy');
        $inicio_vigencia->addValidation('Início de Vigência', new TRequiredValidator);

        $fim_vigencia = new TDate('fim_vigencia');
        $fim_vigencia->setDatabaseMask('yyyy-mm-dd');
        $fim_vigencia->setMask('dd/mm/yyyy');
        $fim_vigencia->addValidation('Fim de Vigência', new TRequiredValidator);

        $tipo_forma_pgto_id = new TDBCombo('tipo_forma_pgto_id','sample','TipoFormaPgto','id','nome');
        $tipo_forma_pgto_id->setChangeAction(new TAction(array($this, 'onChangeFormaPGTO')));
        $tipo_forma_pgto_id->addValidation('Forma de Pagamento', new TRequiredValidator);

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);

        $conta_bancaria_entrada_id = new TDBCombo('conta_bancaria_entrada_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_entrada_id->addValidation('Conta Bancária', new TRequiredValidator);

        $tipo_pgto_id = new TDBCombo('tipo_pgto_id','sample','TipoPgto','id','nome','nome'); 
        $tipo_pgto_id->addValidation('Tipo de Pagamento', new TRequiredValidator);

        $plano_id = new TDBCombo('plano_id', 'sample', 'Plano', 'id', 'nome');
        $plano_id->setChangeAction(new TAction(array($this, 'onChangePlano')));
        $plano_id->addValidation('Plano Escolhido', new TRequiredValidator);
        
        $valor = new TNumeric('valor', 2,',','.',true);
        $valor->setEditable(FALSE);

        $entrada = new TNumeric('entrada', 2,',','.',true);
        $entrada->setValue('0.00');
        $entrada->addValidation('Valor de Entrada', new TRequiredValidator);
        $desconto = new TNumeric('desconto', 2,',','.',true);
        $desconto->setValue('0.00');
        $desconto->addValidation('Valor de Desconto', new TRequiredValidator);

        $total = new TNumeric('total', 2,',','.',true);
        $total->setEditable(FALSE);

        $parcela = new TCombo('parcela');
        $combo_parcela['S'] = 'Sim';
        $combo_parcela['N'] = 'Não';
        $parcela->addItems($combo_parcela);
        $parcela->addValidation('Há Parcelas?', new TRequiredValidator);

        $qtd_parcelas = new TEntry('qtd_parcelas');
        $qtd_parcelas->setEditable(FALSE);
        $qtd_parcelas->setValue('0');

        $valor_parcelado = new TNumeric('valor_parcelado', 2,',','.',true);
        $valor_parcelado->setEditable(FALSE);

        $vencimento_primeira_parcela = new TDate('vencimento_primeira_parcela');
        $vencimento_primeira_parcela->setDatabaseMask('yyyy-mm-dd');
        $vencimento_primeira_parcela->setMask('dd/mm/yyyy');


        // add the fields
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Cliente / Razão Social'), $cliente_id ],
                                       [ new TLabel('Tipo de Endereço'), $tipo_endereco_id ]);
        $row->layout = ['col-sm-2', 'col-sm-6', 'col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Modelo de Contrato'), $relatorio_customizado_id ],    
                                       [ new TLabel('Início da Virgência'), $inicio_vigencia ],
                                       [ new TLabel('Fim da Virgência'), $fim_vigencia ],
                                       [ new TLabel('Unidade'), $unit_id ]);
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-2','col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Usuário'), $user_id ],
                                       [ new TLabel('Conta vinculada a Mensalidade'), $conta_bancaria_id ],
                                       [ new TLabel('Conta vinculada a Entrada'), $conta_bancaria_entrada_id ]);
        $row->layout = ['col-sm-4', 'col-sm-4','col-sm-4'];

        $this->form->addContent( ['<h4><b>Valores Acordados</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('Plano escolhido'), $plano_id ]);
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Forma de Pagamento'), $tipo_forma_pgto_id ],
                                       [ new TLabel('Tipo de Pagamento'), $tipo_pgto_id ],   
                                       [ new TLabel('Valor do Plano'), $valor ],
                                       [ new TLabel('Valor de entrada'), $entrada ]);
        $row->layout = ['col-sm-4','col-sm-4', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Desconto'), $desconto ],
                                       [ new TLabel('Total Geral'), $total ],
                                       [ new TLabel('Há Parcelas?'), $parcela ],   
                                       [ new TLabel('Qtd. Parcelas'), $qtd_parcelas ],
                                       [ new TLabel('Valor Parcela'), $valor_parcelado ],
                                       [ new TLabel('Venc. 1º Parcela'), $vencimento_primeira_parcela]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2','col-sm-2','col-sm-2'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction(['ClienteContratoList','onReload']), 'fa:arrow-circle-left red');

        /*$btn_boleto = $this->form->addAction('Gerar Boleto', new TAction([$this, 'onGerarBoleto']), 'fa:barcode');
        $btn_boleto->class = 'btn btn-sm btn-info';*/
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml','ClienteContratoList'));
        $container->add($this->form);
        
        parent::add($container);

        $valor->onBlur   = 'calculando()';
        $entrada->onBlur   = 'calculando()';
        $desconto->onBlur   = 'calculando()';

    TScript::create('calculando = 

            function() {

                let valorPlano = convertToFloatNumber(form_ClienteContrato.valor.value);  
                valorEntrada = convertToFloatNumber(form_ClienteContrato.entrada.value);
                valorDesconto = convertToFloatNumber(form_ClienteContrato.desconto.value);   

                let valorTotal =  parseFloat(valorPlano) - parseFloat(valorEntrada) - parseFloat(valorDesconto);

                let valorTot = formatMoney(valorTotal);
                form_ClienteContrato.total.value = valorTot;
                
                calculandoParcelas();

            };

            function formatMoney (number, decimal, separatord, separatort) {
                var n = number,
                    c = isNaN(decimal = Math.abs(decimal)) ? 2 : decimal,
                    d = separatord == undefined ? "," : separatord,
                    t = separatort == undefined ? "." : separatort,
                    s = n < 0 ? "-" : "",
                    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };

            function convertToFloatNumber(value) {
                value = value.toString();
                if (value.indexOf(\'.\') !== -1 || value.indexOf(\',\') !== -1) {
                    if (value.indexOf(\'.\') >  value.indexOf(\',\')) {
                        return parseFloat(value.replace(/,/gi,\'\'));
                    } else {
                        return parseFloat(value.replace(/\./gi,\'\').replace(/,/gi,\'.\'));
                    }
                } else {
                    return parseFloat(value);
                }
            };
        ');

        $tipo_forma_pgto_id->onBlur  = 'calculandoParcelas()';
        $qtd_parcelas->onChange      = 'calculandoParcelas()';
//        $plano_id->onChange = 'console.log("oi")';

        /** @lang JavaScript 1.8 */
        TScript::create(
            'calculandoParcelas = 

            function() {
                let total = convertToFloatNumber(form_ClienteContrato.total.value);
                let qtd_parcelas = convertToFloatNumber(form_ClienteContrato.qtd_parcelas.value);
                
                if (qtd_parcelas == 0) {
                    qtd_parcelas = 1;    
                }
                
                let valorTotal = total / qtd_parcelas;

                let valorTot = formatMoney(valorTotal);
                form_ClienteContrato.valor_parcelado.value = valorTot;

            };

            function formatMoney (number, decimal, separatord, separatort) {
                var n = number,
                    c = isNaN(decimal = Math.abs(decimal)) ? 2 : decimal,
                    d = separatord == undefined ? "," : separatord,
                    t = separatort == undefined ? "." : separatort,
                    s = n < 0 ? "-" : "",
                    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };
        ');
    }

    public function onGerarBoleto($param) {

        $data = $this->form->getData();

        if (!$data->id) {

            new TMessage('error', 'Salve antes de gerar o boleto!');

            $this->form->setData($data);

            return;

        }

        try {
            TTransaction::open('sample');

            $cliente = new Cliente($data->cliente_id);
            $conta = new ContaBancaria($data->conta_bancaria_id);
            $endereco = ClienteEndereco::where('cliente_id', '=', $cliente->id)->first();

            $banco = $conta->banco;

            $image_name = $banco->num_banco . '.png';
            $image_path = realpath(PATH . '/vendor/eduardokum/laravel-boleto/logos/') . DIRECTORY_SEPARATOR . $image_name;

            $numero = $conta->ultimo_nossonumero + 1; //nosso numero = 1;
            $numeroDocumento = $data->id;
            $carteira = $conta->carteira;
            $agencia = $conta->codigo_cooperativa;//$conta->agencia . $conta->agencia_dv;
            $convenio = $conta->convenio;
            $conta_numero = $conta->conta . $conta->conta_dv;
            $aceite = $conta->aceite;
            $especieDoc = $conta->especieDoc;


            $codigo_cooperativa = $conta->codigo_cooperativa;//$conta->agencia . $conta->agencia_dv;
            $codigo_beneficiario = $conta->beneficiario;//$conta->agencia . $conta->agencia_dv;
            $variacaoCarteira = $conta->variacaoCarteira;
            $cip = $conta->cip;
            $byte = $conta->byte;
            $posto = $conta->posto;
            $contaDv = $conta->contaDv;
            $campo_range = $conta->campo_range;
            $codigoCliente = $conta->codigoCliente;


            $vencimento = new \Carbon\Carbon($data->inicio_vigencia);

            $valor = $data->entrada;

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
                    'bairro'    => $conta->system_unit->bairro, //'Bairro',
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
                    'documento' => $cliente->cpf_cnpj
                ]
            );

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
                'agencia'                => $agencia,
                'convenio'               => $convenio,
                'conta'                  => $conta_numero,
                'descricaoDemonstrativo' => [], //['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
                'instrucoes'             => $instrucao,
                'aceite'                 => $aceite,
                'especieDoc'             => $especieDoc
            ];

            if ($banco->num_banco == Boleto::COD_BANCO_BANCOOB) {
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
                $boleto->setConta(Util::onlyNumbers($codigo_beneficiario));
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

            $pdf = new Pdf();
            $pdf->addBoleto($boleto);

            $nome_boleto = 'boleto' . $data->id . '.pdf';
            $retorno = $pdf->gerarBoleto($pdf::OUTPUT_SAVE, PATH . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $nome_boleto);

            $this->form->setData($data);


            $conta->ultimo_nossonumero = $numero;
            $conta->store();

            $remessa = new Boletos();
            $remessa->dataVencimento = $boleto->getDataVencimento()->format('Y-m-d');//$data->data_vencimento;
            $remessa->valor = $boleto->getValor();//$data->valor;
            $remessa->multa = null;
            $remessa->juros = null;
            $remessa->numero = $boleto->getNumero();
            $remessa->numeroDocumento = $boleto->getNumeroDocumento();
            $remessa->carteira = $boleto->getCarteira();
            $remessa->conta_bancaria_id = $data->conta_bancaria_id;
            $remessa->agencia = $boleto->getAgencia();
            $remessa->conta = $boleto->getConta();
            $remessa->instrucao1 = $conta->instrucoes1;
            $remessa->instrucao2 = $conta->instrucoes2;
            $remessa->instrucao3 = $conta->instrucoes3;
            $remessa->instrucao4 = $conta->instrucoes4;
            $remessa->cliente_id = $data->cliente_id;
            $remessa->conta_receber_id = $data->id;
            $remessa->unit_id = $data->unit_id;
            $remessa->num_parcela = '1/1';
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

            $ContasReceber = new ContaReceber();
            $ContasReceber->data_conta = $data->inicio_vigencia;     
            $ContasReceber->descricao = 'REFERENTE AO CONTRATO Nº '.$data->id; 
            $ContasReceber->documento = $data->id; //COLOCAR O NUMERO ÚNICO DO CARNE
            $ContasReceber->data_vencimento = $data->inicio_vigencia;
            $ContasReceber->valor = $data->entrada;
            $ContasReceber->baixa = 'N'; 
            $ContasReceber->parcelas = 1;
            $ContasReceber->nparcelas = 1; 
            $ContasReceber->replica = 'N'; 
            $ContasReceber->unit_id = $data->unit_id; 
            $ContasReceber->cliente_id = $data->cliente_id;
            $ContasReceber->tipo_pgto_id = $data->tipo_pgto_id;
            $ContasReceber->tipo_forma_pgto_id = $data->tipo_forma_pgto_id;
            $ContasReceber->user_id = $data->user_id;

            $plano = new Plano($data->plano_id);

            $ContasReceber->pc_receita_id = $plano->pc_receita_id;
            $ContasReceber->pc_receita_nome = $plano->pc_receita_nome;
            $ContasReceber->conta_bancaria_id = $data->conta_bancaria_id;
            $ContasReceber->store();

            parent::openFile("tmp/". $nome_boleto);
            new TMessage('info', 'Boleto gerado com sucesso');
            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', 'Problema ao gerar o boleto. <br>' . $e->getMessage());
            $this->form->setData($data);
            return;
        }
    }
    
    public static function onChangePlano( $param )
    {
        $plano_id = $param['plano_id'];

        if ($plano_id)
        {
            $response = new stdClass;
            
            try     
            {
                TTransaction::open('sample');
                
                $plano = Plano::where('id', '=', $plano_id)->load();

                if($plano){

                    foreach ($plano as $prod) {

                        $response->{'valor'} = number_format($prod->valor,2,',', '.');
                        $response->{'total'} = number_format($prod->valor,2,',', '.');
                        $response->{'entrada'} = number_format('0.00',2,',', '.');
                        $response->{'desconto'} = number_format('0.00',2,',', '.');
                        
                        TForm::sendData('form_ClienteContrato', $response);
                    }
                }
                
                TTransaction::close();
                TScript::create('calculandoParcelas()');
            }
            catch (Exception $e)
            {
                TTransaction::rollback();
            }
        }
    }

    public static function onChangeFormaPGTO( $param )
    {
        $tipo_forma_pgto_id = $param['tipo_forma_pgto_id'];

        if ($tipo_forma_pgto_id)
        {
            $response = new stdClass;
            
            try     
            {
                TTransaction::open('sample');
                
                $parcela = TipoFormaPgto::where('id', '=', $tipo_forma_pgto_id)->load();

                if($parcela){

                    foreach ($parcela as $prod) {

                        $response->{'qtd_parcelas'} = $prod->parcela;
                        TForm::sendData('form_ClienteContrato', $response);
                        TScript::create('calculandoParcelas()');
                    }
                }
                
                TTransaction::close();
            }
            catch (Exception $e)
            {
                TTransaction::rollback();
            }
        }
    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array

            $cliente = new Cliente($data->cliente_id);
            $data->nome = $cliente->razao_social;
            
            $object = new ClienteContrato;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('sample'); // open a transaction
                $object = new ClienteContrato($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
