<?php

use Adianti\Widget\Dialog\TToast;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TForm;
use Adianti\Validator\TEmailValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Validator\TMinLengthValidator;
/**
 * CheckOutStepTwo
 * @author  Fred Azv.
 */
class CheckOutStepTwo extends TPage
{
    protected $form; // form
    protected $list;
    
    public function __construct($param)
    {
        parent::__construct();

        TPage::include_css('app/resources/public.css');

        $this->form = new BootstrapFormBuilder('form_CheckOutStepTwo');
        $this->form->addContent( ['<h4><b>Dados Pessoais</b></h4><hr>'] );
        
        $cliente_id = new THidden('cliente_id');
        $this->form->addFields( [$cliente_id] );

        $unit_id = new THidden('unit_id');
        //$unit_id->setValue($param['unit_id']);
        $this->form->addFields( [$unit_id] );
        
        $user_id = new THidden('user_id');
        //$user_id->setValue($param['user_id']);
        $this->form->addFields( [$user_id] );

        $tipo = new TCombo('tipo');
        $combo_tipos = array();
        $combo_tipos['F'] = 'Pessoa Física';
        $combo_tipos['J'] = 'Pessoa Jurídica';
        $tipo->addItems($combo_tipos);

        //form cadastro cliente
        $razao_social = new TEntry('razao_social');
        $razao_social->forceUpperCase();

        $cpf_cnpj = new TEntry('cpf_cnpj');
        $cpf_cnpj->addValidation('CPF/CNPJ', new TRequiredValidator);
        $cpf_cnpj->onKeyUp = 'fwFormatarCpfCnpj(this)';
        $cpf_cnpj->onChange = 'validaCpf(this,\'form_CheckOutStepTwo\')';
        $cpf_cnpj->setExitAction(new TAction([$this, 'onLoadStatic']));

        $row = $this->form->addFields( [ new TLabel('CPF/CNPJ'), $cpf_cnpj ],
                                       [ new TLabel('Tipo de pessoa'), $tipo ],
                                       [ new TLabel('Nome completo'), $razao_social ]
                                     
        );
        $row->layout = ['col-sm-2','col-sm-2','col-sm-8'];

        $sexo = new TCombo('sexo');
        $sexo->addValidation('Sexo', new TRequiredValidator);
        $combo_sexo['M'] = 'Masculino';
        $combo_sexo['F'] = 'Feminino';
        $sexo->addItems($combo_sexo);

        $nascimento = new TDate('nascimento');
        $nascimento->setDatabaseMask('yyyy-mm-dd');
        $nascimento->setMask('dd/mm/yyyy');
        $nascimento->addValidation('Nascimento', new TRequiredValidator);


        $cnh = new TEntry('cnh');
        //$cnh->addValidation('CNH', new TRequiredValidator);
        $cnh->setMask('9!');
        $categoria_cnh = new TCombo('categoria_cnh');
        $combo_categorias['A'] = 'A';
        $combo_categorias['B'] = 'B';
        $combo_categorias['C'] = 'C';
        $combo_categorias['D'] = 'D';
        $combo_categorias['E'] = 'E';
        $combo_categorias['AB'] = 'AB';
        $combo_categorias['AC'] = 'AC';
        $combo_categorias['AD'] = 'AD';
        $combo_categorias['AE'] = 'AE';
        $categoria_cnh->addItems($combo_categorias);
        //$categoria_cnh->addValidation('Categoria CNH', new TRequiredValidator);

        $cnh_validade = new TDate('cnh_validade');
        $cnh_validade->setDatabaseMask('yyyy-mm-dd');
        $cnh_validade->setMask('dd/mm/yyyy');
        //$cnh_validade->addValidation('Validade', new TRequiredValidator);

        $celular = new TEntry('celular');
        $celular->setMask('(99)9999-99999');
        $celular->addValidation('Celular', new TRequiredValidator);
        $email = new TEntry('email');
        $email->addValidation('Email', new TEmailValidator);

        $row = $this->form->addFields( [ new TLabel('Sexo'), $sexo ],
                                       [ new TLabel('Nacimento'), $nascimento ],
                                       [ new TLabel('Celular'), $celular ],
                                       [ new TLabel('E-mail'), $email ]
        );
        $row->layout = ['col-sm-2','col-sm-2','col-sm-2','col-sm-6'];

    
        // $row = $this->form->addFields( [ new TLabel('Celular'), $celular ],
        //                                [ new TLabel('E-mail'), $email ]
        // );
        // $row->layout = ['col-sm-2','col-sm-10'];

        $tipo_endereco_id = new THidden('tipo_endereco_id');
        $this->form->addFields( [$tipo_endereco_id] );
        $tipo_endereco_id->setValue('1');
        $cep = new TEntry('cep');
        $cep->addValidation('CEP', new TRequiredValidator);
        $buscaCep = new TAction(array($this, 'onCep'));
        $cep->setExitAction($buscaCep);
        $cep->setMask('99.999-999');
        $logradouro = new TEntry('logradouro');
        $logradouro->addValidation('Logradouro', new TRequiredValidator);
        $logradouro->forceUpperCase();
        $numero = new TEntry('numero');
        $numero->addValidation('Número', new TRequiredValidator);
        $complemento = new TEntry('complemento');
        $complemento->forceUpperCase();
        $bairro = new TEntry('bairro');
        $bairro->addValidation('Bairro', new TRequiredValidator);
        $bairro->forceUpperCase();
        $cidade = new TEntry('cidade');
        $cidade->addValidation('Cidade', new TRequiredValidator);
        $cidade->forceUpperCase();
        $uf = new TEntry('uf');
        $uf->forceUpperCase();
        $uf->setMask('SS');
        $uf->addValidation('UF', new TMinLengthValidator, array(2));
        $codMuni = new TEntry('codMuni');

        $this->form->addContent( ['<h4><b>Endereço</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('CEP'), $cep ],    
                                       [ new TLabel('Logradouro'), $logradouro ],
                                       [ new TLabel('Número'), $numero ],
                                       [ new TLabel('Bairro'), $bairro ]);
        $row->layout = ['col-sm-2','col-sm-6', 'col-sm-1', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Cidade'), $cidade ],    
                                       [ new TLabel('UF'), $uf ],
                                       [ new TLabel('Código do IBGE'), $codMuni ]);
        $row->layout = ['col-sm-5','col-sm-4', 'col-sm-1', 'col-sm-2'];

        $pagestep = new TPageStep;
        $pagestep->addItem('Escolha o seu Plano');
        $pagestep->addItem('Dados Principais');
        $pagestep->addItem('Pagamento');       
        $pagestep->addItem('Confirmação');  
        $pagestep->style = 'margin-bottom: 2%; background-color: white;';

        $pagestep->select('Dados Principais');

        $row = $this->form->addAction( 'Próximo',  new TAction([$this, 'onNextPage'], ['register_state' => 'false']), 'fa:arrow-right white' );
        $row->class = 'btn btn-success';
        $row->layout = ['col-sm-12'];
        $row->style = 'float: right; margin-bottom: 2%;';

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($pagestep);
        $container->add($this->form);
        parent::add($container);

    }

    public static function onCep($param)
    {
        try {
            $retorno = Utilidades::onCep($param['cep']);
            $objeto  = json_decode($retorno);
            
            if (isset($objeto->logradouro)){
                $obj              = new stdClass();
                $obj->logradouro = $objeto->logradouro;
                $obj->bairro   = $objeto->bairro;
                $obj->cidade   = $objeto->localidade;
                $obj->uf       = $objeto->uf;
                $obj->codMuni  = $objeto->ibge;

                TForm::sendData('form_CheckOutStepTwo',$obj);
                unset($obj);
            }else{
                new TMessage('info', 'Erro ao buscar endereço por este CEP.');
            }
        }catch (Exception $e){
            new TMessage('error', '<b>Error:</b> ' . $e->getMessage());
        }
    }

    public function onNextPage($param) {

        TTransaction::open('sample');
        $form = $this->form->getData();
       
        try
        { 
            TTransaction::open('sample');
            $this->form->validate();
            if($form->cliente_id){

                TSession::setValue('form_one', $form);
                TApplication::loadPage('CheckOutStepThree', 'onLoad', ['register_state' => 'false']);
            }
            elseif($form->cliente_id)
            {
                $cliente = new Cliente;
                $cliente->user_id = $form->user_id;
                $cliente->tipo = $form->tipo;
                $cliente->cliente_grupo_id = '1';
                $cliente->razao_social = $form->razao_social;
                $cliente->nome_fantasia = $form->razao_social;
                $cliente->cpf_cnpj = $form->cpf_cnpj;
                $cliente->rg_ie = NULL;
                $cliente->im = NULL;
                $cliente->nascimento = $form->nascimento;
                $cliente->sexo = $form->sexo;
                $cliente->indicador_ie = NULL;
                $cliente->site = NULL;
                $cliente->fornecedor_id = NULL;
                $cliente->comissao_parceiro = NULL;
                $cliente->comissao_vendedor = NULL; 
                $cliente->comissao_vendedor_externo = NULL;
                $cliente->unit_id = $form->unit_id;
                $cliente->tabela_precos_id = NULL;
                $cliente->gera_nfse = 'N';
                $cliente->juridico = 'N';
                $cliente->cnh = $form->cnh;
                $cliente->categoria_cnh = $form->categoria_cnh;
                $cliente->cnh_validade = $form->cnh_validade;
                $cliente->store();

                $pessoaEndereco = new ClienteEndereco;
                $pessoaEndereco->cliente_id = $cliente->id;
                $pessoaEndereco->tipo_endereco_id = '1';
                $pessoaEndereco->cep = $form->cep;
                $pessoaEndereco->logradouro = $form->logradouro;
                $pessoaEndereco->numero = $form->numero;
                $pessoaEndereco->complemento = $form->complemento;
                $pessoaEndereco->bairro = $form->bairro;
                $pessoaEndereco->cidade = $form->cidade;
                $pessoaEndereco->uf = $form->uf;
                $pessoaEndereco->codMuni = $form->codMuni;
                $pessoaEndereco->store();
                
                if($form->celular){
                    $pessoaTelefone = new TelefonesCliente;
                    $pessoaTelefone->cliente_id = $cliente->id;
                    $pessoaTelefone->responsavel = $form->razao_social;
                    $pessoaTelefone->telefone = $form->celular;
                    $pessoaTelefone->store();
                }

                if($form->email){
                    $pessoaEmail = new EmailCliente;
                    $pessoaEmail->cliente_id = $cliente->id;
                    $pessoaEmail->responsavel = $form->razao_social;
                    $pessoaEmail->email = $form->email;
                    $pessoaEmail->store();
                }

                TSession::setValue('form_one', $form);
                TApplication::loadPage('CheckOutStepTwo', 'onLoad', ['register_state' => 'false']);
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        TTransaction::close();
    }

    public static function onLoadStatic($param) {
        
        TSession::delValue('form_CheckOutStepOne');
        TSession::delValue('form_CheckOutStepTwo');
        TSession::delValue('form_CheckOutStepThree');
        
        if(isset($param['cpf_cnpj'])){

            $validador = Utilidades::formataCPF_CNPJ($param['cpf_cnpj']);
            
            if($validador){
                TScript::create('Adianti.waitMessage = \'Carregando\';');
                TApplication::postData('form_CheckOutStepTwo', 'CheckOutStepTwo', 'onLoad',['register_state' => 'false']);
            }
        }
    }

    public function onLoad($param)
    {

        $unit_id = TSession::getValue('unidade');
        $user_id = TSession::getValue('usuario');
        
        $data = $this->form->getData();
        TTransaction::open('sample');
        $pessoa = Cliente::where('cpf_cnpj', '=', $param['cpf_cnpj'])->first();

        if($pessoa) {

            $data->cliente_id = $pessoa->id;
            $data->unit_id = $unit_id;
            $data->user_id = $user_id;
            $data->razao_social = $pessoa->razao_social;
            $data->tipo = $pessoa->tipo;
            $data->sexo = $pessoa->sexo;
            $data->nascimento = $pessoa->nascimento;
            $data->cnh = $pessoa->cnh;
            $data->cnh_validade = $pessoa->cnh_validade; 
            $data->categoria_cnh = $pessoa->categoria_cnh;    

            $pessoaEndereco = ClienteEndereco::where('cliente_id','=',$pessoa->id)->first();

            if($pessoaEndereco){

                $data->cep = $pessoaEndereco->cep;
                $data->logradouro = $pessoaEndereco->logradouro;
                $data->numero = $pessoaEndereco->numero;
                $data->complemento = $pessoaEndereco->complemento;
                $data->bairro = $pessoaEndereco->bairro;
                $data->cidade = $pessoaEndereco->cidade;
                $data->uf = $pessoaEndereco->uf;
                $data->codMuni = $pessoaEndereco->codMuni;
            }

            $pessoaTelefone = TelefonesCliente::where('cliente_id','=',$pessoa->id)->first();
            if($pessoaTelefone){
                $data->celular = $pessoaTelefone->telefone;
            }

            $pessoaEmail = EmailCliente::where('cliente_id','=',$pessoa->id)->first();
            if($pessoaEmail){
                $data->email = $pessoaEmail->email;
            }

            TToast::show('success', '<b>Registro Encontrado em nosso banco de dados!</b>!',
                    'bottom right', 'fas fa-edit');

        }else{

            $data->cliente_id = '';
            $data->unit_id = $param['unit_id'];
            $data->user_id = $param['user_id'];
            $data->razao_social = '';
            $data->tipo = '';
            $data->sexo = '';
            $data->nascimento = '';
            $data->cnh = '';
            $data->cnh_validade = '';
            $data->categoria_cnh = '';   
            $data->email = '';
            $data->celular = '';
            $data->cep = '';
            $data->logradouro = '';
            $data->numero = '';
            $data->complemento = '';
            $data->bairro = '';
            $data->cidade = '';
            $data->uf = '';
            $data->codMuni = '';

        }


        TTransaction::close();
        $this->form->setData($data);
    }

    function show()
    {
        parent::show();
    }

}