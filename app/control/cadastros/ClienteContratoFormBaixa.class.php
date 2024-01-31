<?php
/**
 * ClienteContratoForm Form
 * @author  Fred Avz.
 */

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Dialog\TQuestion;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Wrapper\BootstrapFormBuilder;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bancoob;
use Eduardokum\LaravelBoleto\Boleto\Banco\Banrisul;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bb;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bnb;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bradesco;
use Eduardokum\LaravelBoleto\Boleto\Banco\Caixa;
use Eduardokum\LaravelBoleto\Boleto\Banco\Hsbc;
use Eduardokum\LaravelBoleto\Boleto\Banco\Itau;
use Eduardokum\LaravelBoleto\Boleto\Banco\Santander;
use Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Pessoa;
use Eduardokum\LaravelBoleto\Util;
use Eduardokum\LaravelBoleto\Boleto\Render\PdfCarne;

class ClienteContratoFormBaixa extends TWindow
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
        $this->form = new BootstrapFormBuilder('form_ClienteContratoBaixa');
        $this->form->setFormTitle('Gestão de Contratos');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        if (!empty($_GET['id']))
        {
            $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_unit_session);
            //$user_id->setEditable(FALSE);

        }else{

            $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_unit_session);
            $user_id->setValue(TSession::getValue('userunitid'));
            $user_id->setEditable(FALSE);
        }

        /*$nome = new TEntry('nome');
        $nome->addValidation('Nome do Contrato', new TRequiredValidator);*/
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $cliente_id->addValidation('Cliente', new TRequiredValidator);
        
        $tipo_forma_pgto_id = new TDBCombo('tipo_forma_pgto_id','sample','TipoFormaPgto','id','nome');
        //$tipo_forma_pgto_id->setChangeAction(new TAction(array($this, 'onChangeFormaPGTO')));
        $tipo_forma_pgto_id->addValidation('Forma de Pagamento', new TRequiredValidator);

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);

        $conta_bancaria_entrada_id = new TDBCombo('conta_bancaria_entrada_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_entrada_id->addValidation('Conta Bancária', new TRequiredValidator);   

        $valor = new TNumeric('valor', 2,',','.',true);
        $valor->setEditable(FALSE);
        
        $entrada = new TNumeric('entrada', 2,',','.',true);
        $entrada->setValue('0.00');
        $entrada->addValidation('Valor de Entrada', new TRequiredValidator);
        $entrada->setEditable(FALSE);
        
        $desconto = new TNumeric('desconto', 2,',','.',true);
        $desconto->setValue('0.00');
        $desconto->addValidation('Valor de Desconto', new TRequiredValidator);
        $desconto->setEditable(FALSE);

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

        $this->form->appendPage('Dados para o Financeiro');
        // add the fields
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Cliente / Razão Social'), $cliente_id ],
                                       [ new TLabel('Unidade'), $unit_id ]);
        $row->layout = ['col-sm-2', 'col-sm-7','col-sm-3'];


        $row = $this->form->addFields( [ new TLabel('Conta vinculada ao Boleto/Carnê'), $conta_bancaria_id ],
                                       [ new TLabel('Conta vinculada a Entrada'), $conta_bancaria_entrada_id ]);
        $row->layout = ['col-sm-6','col-sm-6'];


        $row = $this->form->addFields( [ new TLabel('Valor do Plano'), $valor ],
                                       [ new TLabel('Valor de entrada'), $entrada ],
                                       [ new TLabel('Desconto'), $desconto ],
                                       [ new TLabel('Total Geral'), $total ],
                                       [ new TLabel('Qtd. Parcelas'), $qtd_parcelas ],
                                       [ new TLabel('Valor Parcela'), $valor_parcelado ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2','col-sm-2','col-sm-2'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        /*$this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction(['ClienteContratoList','onReload']), 'fa:arrow-circle-left red');*/

        $btn_boleto = $this->form->addAction('Gerar Carnê', new TAction([$this, 'gerarCarne']), 'fa:barcode');
        $btn_boleto->class = 'btn btn-sm btn-info';
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml','ClienteContratoList'));
        $container->add($this->form);
        
        parent::add($container);

    }

    
    public function gerarCarne( $param )
    {   

        try
        {   
            $key   = $param['id'];

            TTransaction::open('sample');

            $object = new ClienteContrato($key); 

            $cliente = new Cliente($param['cliente_id']);
            $conta = new ContaBancaria($object->conta_bancaria_id);
            $endereco = ClienteEndereco::where('cliente_id', '=', $cliente->id)->first();

            if($object->status != 'F')
            {
                //$unit = new SystemUnit($object->unit_id); 
                $carne = new PdfCarne();

                $formaPagamento = new FormaPagamento($object->total,$object->TipoFormaPgto->regra,$object->vencimento_primeira_parcela);

                $banco = $conta->banco;

                $image_name = $banco->num_banco . '.png';
                $image_path = realpath(PATH . '/vendor/eduardokum/laravel-boleto/logos/') . DIRECTORY_SEPARATOR . $image_name;

                $numero = $conta->ultimo_nossonumero + 1; //nosso numero = 1;
                $numeroDocumento = $object->unit_id."/".$object->id;
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
                $nBeneficiario = $conta->beneficiario;
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
                        $boleto->setConta(Util::onlyNumbers($nBeneficiario));
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
                    $ContasReceber->documento = $numero; //COLOCAR O NUMERO ÚNICO DO CARNE
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
                    $ContasReceber->store();

                    $conta->ultimo_nossonumero = $numero;
                    $conta->store();

                    $remessa = new Boletos();
                    $remessa->dataVencimento = $boleto->getDataVencimento()->format('Y-m-d');//$data->data_vencimento;
                    $remessa->valor = $boleto->getValor();$object->valor;
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
                    $remessa->conta_receber_id = $object->id;
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

                $nome_boleto = 'carne.pdf';
                $retorno = $carne->gerarBoleto($carne::OUTPUT_SAVE, PATH . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $nome_boleto);

                $object->status = 'F';
                $object->store(); 

                parent::openFile("tmp/". $nome_boleto);
                new TMessage('info', 'Carnê gerado ' . '<br>' . $retorno);
            }
            else
            {
                new TMessage('error', 'ATENÇÃO: Contrato já baixado no sistema!');
            }

            TTransaction::close(); 
            //$this->onReload($param);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample'); // open a transaction
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
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
