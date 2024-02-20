<?php

use Adianti\Control\TPage;
use Adianti\Widget\Form\TEntry;

/**
 * ClienteForm Form
 * @author  Fred Azv.
 */
class ClienteForm extends TPage
{
    protected $form; // form

    private $fieldlist;
    private $lista_email;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        $script = new TElement('script');
        $script->type = 'text/javascript';
    
        $javascript = "

            $(document).on('keydown', 'input[name=\"cpf_cnpj\"]', function (e) {

                var digit = e.key.replace(/\D/g, '');

                var value = $(this).val().replace(/\D/g, '');

                var size = value.concat(digit).length;

                if(size <= 11) { $('select[name=\"tipo\"]').val('F');}
                else $('select[name=\"tipo\"]').val('J');

                $(this).mask((size <= 11) ? '000.000.000-00' : '00.000.000/0000-00');


            });
            
        ";
    
        $script->add($javascript);
        //parent::add($script);
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Cliente');
        $this->form->setFormTitle('Cliente');
        $this->form->setFieldSizes('100%');
        
        // $this->form->appendPage('Dados Principais');
        // create the form fields
        $id = new TEntry('id');

        $codigo_parceiro = new TEntry('codigo_parceiro');
        $codigo_parceiro->setEditable(FALSE);
        
        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
        $user_id->addValidation('Usuário', new TRequiredValidator);
        $user_id->setEditable(FALSE);

        $estado_civil = new TCombo('estado_civil');
        $comboestado_civil['Casado(a)'] = 'Casado(a)';
        $comboestado_civil['Solteiro(a)'] = 'Solteiro(a)';
        $comboestado_civil['Viuvo(a)'] = 'Viuvo(a)';
        $comboestado_civil['Divorciado(a)'] = 'Divorciado(a)';
        $estado_civil->addItems($comboestado_civil);
        //$estado_civil->addValidation('Estado Civil', new TRequiredValidator);

        /*Dados Principais*/
        $this->form->addContent( ['<h4><b>Dados Principais</b></h4><hr>'] );
        $tipo = new TCombo('tipo');
        $tipo->setChangeAction(new TAction(array($this, 'onChangeSexo')));
        $combo_tipos = array();
        $combo_tipos['F'] = 'Pessoa Física';
        $combo_tipos['J'] = 'Pessoa Jurídica';
        $tipo->addItems($combo_tipos);
        $tipo->addValidation('Tipo de Pessoa', new TRequiredValidator);

        //$tipo->setValue('J');
        
        $cpf_cnpj = new TEntry('cpf_cnpj');
        $cpf_cnpj->onKeyUp = 'fwFormatarCpfCnpj(this)';
        $cpf_cnpj->addValidation('CPF/CNPJ', new TRequiredValidator);

        // $cpf_cnpj->onBlur = 'validaCpfCnpj(this)';
        //$cpf_cnpj->onBlur = 'validaCpfCnpj(this,\'form_Cliente\')';

        $buscaCnpj = new TAction(array($this, 'onCNPJ'));
        $cpf_cnpj->setExitAction($buscaCnpj);

        $rg_ie = new TEntry('rg_ie');
        $rg_ie->setMask('9!');
        $im = new TEntry('im');

        $row = $this->form->addFields( [ new TLabel('Código Único'), $codigo_parceiro ]
                                       );
        $row->layout = ['col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Atualizado Por'), $user_id ],
                                       [ new TLabel('Unidade.'), $unit_id ],
                                       [ new TLabel('Tipo'), $tipo ],
                                       [ new TLabel('CPF/CNPJ'), $cpf_cnpj ],
                                       [ new TLabel('RG'), $rg_ie ]
                                       );
        $row->layout = ['col-sm-1', 'col-sm-2', 'col-sm-3', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        
        $razao_social = new TEntry('razao_social');
        $razao_social->addValidation('Nome Completo', new TRequiredValidator);
        $razao_social->forceUpperCase();
        $nome_fantasia = new TEntry('nome_fantasia');
        $nome_fantasia->forceUpperCase();
        $nascimento = new TDate('nascimento');
        //$nascimento->addValidation('Nascimento', new TRequiredValidator);
        $nascimento->setDatabaseMask('yyyy-mm-dd');
        $nascimento->setMask('dd/mm/yyyy');
        $razao_social->onkeyup = 'copiaRazaoSocial()';

        TScript::create('copiaRazaoSocial = function() {
                
                let tipoPessoa = form_Cliente.tipo.value;
                let razao = form_Cliente.razao_social.value;

                if(tipoPessoa == "F")
                {
                    form_Cliente.nome_fantasia.value = razao;
                }
            };
        ');
        
        $estado_civil = new TCombo('estado_civil');
        $comboestado_civil['Casado(a)'] = 'Casado(a)';
        $comboestado_civil['Solteiro(a)'] = 'Solteiro(a)';
        $comboestado_civil['Viuvo(a)'] = 'Viuvo(a)';
        $comboestado_civil['Divorciado(a)'] = 'Divorciado(a)';
        $estado_civil->addItems($comboestado_civil);

        $orgao_emissor = new TEntry('orgao_emissor');
        $beneficiario_mutuante = new TEntry('beneficiario_mutuante');

        $cpf_beneficiario_mutuante = new TEntry('cpf_beneficiario_mutuante');
      
        $cpf_beneficiario_mutuante->onKeyUp = 'fwFormatarCpfCnpj(this)';
        $cpf_beneficiario_mutuante->onBlur = 'validaCpfCnpj(this,\'form_Cliente\')';

        $row = $this->form->addFields( [ new TLabel('Nome Completo'), $razao_social ],    
                                       [ new TLabel('Como deseja ser chamado'), $nome_fantasia ],
                                       [ new TLabel('Nascimento'), $nascimento ],
                                       [ new TLabel('Orgão Emissor'), $orgao_emissor ]

                                    );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-2','col-sm-2','col-sm-2'];

        //$site = new TEntry('site');
        
        $sexo = new TCombo('sexo');
        $combo_sexo['M'] = 'Masculino';
        $combo_sexo['F'] = 'Feminino';
        $sexo->addItems($combo_sexo);

        $cliente_grupo_id = new TDBCombo('cliente_grupo_id','sample','ClienteGrupo','id','nome','nome');
 

        $telefone_principal = new TEntry('telefone_principal');
        $telefone_principal->setMask('(99)99999-9999');
        $email_principal = new TEntry('email_principal');

        $row = $this->form->addFields(  [ new TLabel('Grupo de cliente'), $cliente_grupo_id ],
                                    [ new TLabel('Telefone Principal'), $telefone_principal ],
                                    [ new TLabel('Email Principal'), $email_principal ]
                                    
        );
        $row->layout = ['col-sm-5','col-sm-2','col-sm-5'];
    
        
        $fornecedor_id = new TDBCombo('fornecedor_id', 'sample', 'Fornecedor','id', 'nome_fantasia');
        $fornecedor_id->enableSearch();
        $fornecedor_id->setSize('100%');
        
        /*Endereço*/
        //$this->form->appendPage('Endereço');
        $this->form->addContent( ['<h4><b>Endereço</b></h4><hr>'] );

        // detail fields
        $cep = new TEntry('cep');
        $cep->addValidation('CEP', new TRequiredValidator);
        $buscaCep = new TAction(array($this, 'onCep'));
        $cep->setExitAction($buscaCep);
        $cep->setMask('99.999-999');
        $logradouro = new TEntry('logradouro');
        $logradouro->forceUpperCase();
        $logradouro->addValidation('Logradouro', new TRequiredValidator);
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $complemento->forceUpperCase();
        $bairro = new TEntry('bairro');
        $bairro->forceUpperCase();
        $cidade = new TEntry('cidade');
        $cidade->forceUpperCase();
        $cidade->addValidation('Cidade', new TRequiredValidator);
        $uf = new TEntry('uf');
        $uf->forceUpperCase();
        $uf->addValidation('UF', new TRequiredValidator);
        $codMuni = new TEntry('codMuni');
        $lat = new TEntry('lat');
        $lon = new TEntry('lon');

        $row = $this->form->addFields( [ new TLabel('CEP'), $cep ],    
                                       [ new TLabel('Logradouro'), $logradouro ],
                                       [ new TLabel('Nº'), $numero ],
                                       [ new TLabel('Bairro'), $bairro ]);
        $row->layout = ['col-sm-2','col-sm-5', 'col-sm-1', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Cidade'), $cidade ],    
                                       [ new TLabel('UF'), $uf ],
                                       [ new TLabel('Código do IBGE'), $codMuni ]);
        $row->layout = ['col-sm-5','col-sm-4', 'col-sm-1', 'col-sm-2'];

        $this->form->addContent( ['<h4><b>Dados de quem indicou</b></h4><hr>'] );
        $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor','id', 'nome_fantasia');
        //$fornecedor_id->addValidation('Quem indicou', new TRequiredValidator);

        $row = $this->form->addFields( [ new TLabel('Quem Indicou'), $fornecedor_id ]   
        );
        $row->layout = ['col-sm-12'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
  
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ClienteForm', 'onEdit']), 'fa:plus green');
        $this->form->addAction('Voltar', new TAction( [$this, 'onExit'] ), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml','ClienteList'));
        $container->add($this->form);
        
        parent::add($container);
        //TPage::include_js('app/resources/ClienteForm.js');
    }

    public static function onChangeSexo($param)
    {
        if ($param['tipo'] == 'F')
        {
            TQuickForm::showField('form_Cliente', 'sexo');
        }
        else
        {
            TQuickForm::hideField('form_Cliente', 'sexo');
        }
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

                TForm::sendData('form_Cliente',$obj, false, false );
                unset($obj);
            }else{
                //new TMessage('info', 'Erro ao buscar endereço por este CEP.');
            }
        }catch (Exception $e){
            new TMessage('error', '<b>Error:</b> ' . $e->getMessage());
        }
    }
                            
    public static function onCNPJ($param)
    {
        try {
        
          if(strlen(trim($param['cpf_cnpj'])) == 18){
          
                $retorno = Utilidades::onCNPJ($param['cpf_cnpj']);
                $objeto  = json_decode($retorno);
                //var_dump($objeto);
                if (isset($objeto->nome)){
                $obj              = new stdClass();
                $obj->razao_social = $objeto->nome;
                $obj->nome_fantasia = $objeto->fantasia;
                
                TForm::sendData('form_Cliente',$obj);
                unset($obj);
                }
           } 
        }catch (Exception $e){
            new TMessage('error', '<b>Error:</b> ' . $e->getMessage());
        }
    }

    public function onReloadUsuarioLogado($param){
        $dados = new stdclass();
        $dados->user_id = TSession::getValue('userid');
        TForm::sendData('form_Cliente', $dados);
    }

    public function onReload($param)
    {
        $this->loaded = TRUE;
    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample');

            //TTransaction::setLogger(new TLoggerSTD); // debugar sql
            
            $data = $this->form->getData('Cliente'); 
            $this->form->validate(); 

            if(empty($param['codigo_parceiro']))
            {   
                $data->codigo_parceiro = Utilidades::referencia();   
            }

            $data->store();

            //Recebendo valor do cliente cadastrado para inserir nas tabelas detalhes
            $id_cliente = $data->id;
            $data->fromArray( $param );

            $this->form->setData($data);
            TTransaction::close();

            // reload form and session items
            $this->onEdit(array('key'=> $id_cliente));

            //$action = new TAction( [$this, 'onExit'] );
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            //AdiantiCoreApplication::loadPage(__CLASS__, 'onEdit', $param);
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            $this->onReload($param);
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public function onEdit( $param )
    {
        try
        {
            if(isset($param['key']))
            {   
                TTransaction::open('sample');

                $id = $param['key'];

                $cliente = new Cliente( $id );
            
                $this->form->setData($cliente); 
                $this->onChangeSexo( ['tipo' => $cliente->tipo] );
                $this->onReload( $param ); // reload items list
                
                TTransaction::close();
            }
            else
            {   
                $this->form->clear(TRUE);
                $this->onReload( $param );
               
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }

    public function onExit()
    {
        $result = TSession::getValue('ClienteList');

        $query = isset($result['query']) ? $result['query'] : null;

        if (!empty($query))
        {
            TScript::create("
                Adianti.waitMessage = 'Listando...';__adianti_post_data('ClienteForm', '$query');                                 
        ");
        }
    }

}
