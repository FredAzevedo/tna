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
        
        $this->form->appendPage('Dados Principais');
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
        $estado_civil->addValidation('Estado Civil', new TRequiredValidator);

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
                                       [ new TLabel('Tipo<span style="color: red; font-size:20px;">*</span>'), $tipo ],
                                       [ new TLabel('CPF/CNPJ<span style="color: red; font-size:20px;">*</span>'), $cpf_cnpj ],
                                       [ new TLabel('RG'), $rg_ie ]
                                       );
        $row->layout = ['col-sm-1', 'col-sm-2', 'col-sm-3', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        
        $razao_social = new TEntry('razao_social');
        $razao_social->addValidation('Nome Completo', new TRequiredValidator);
        $razao_social->forceUpperCase();
        $nome_fantasia = new TEntry('nome_fantasia');
        $nome_fantasia->forceUpperCase();
        $nascimento = new TDate('nascimento');
        $nascimento->addValidation('Nascimento', new TRequiredValidator);
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
        $estado_civil->addValidation('Estado Civíl', new TRequiredValidator);

        $orgao_emissor = new TEntry('orgao_emissor');
        $beneficiario_mutuante = new TEntry('beneficiario_mutuante');
        $beneficiario_mutuante->addValidation('Beneficiário do Mutuante', new TRequiredValidator);
        $cpf_beneficiario_mutuante = new TEntry('cpf_beneficiario_mutuante');
        $cpf_beneficiario_mutuante->addValidation('CPF do Beneficiário', new TRequiredValidator);
        $cpf_beneficiario_mutuante->onKeyUp = 'fwFormatarCpfCnpj(this)';
        $cpf_beneficiario_mutuante->onBlur = 'validaCpfCnpj(this,\'form_Cliente\')';

        $row = $this->form->addFields( [ new TLabel('Nome Completo<span style="color: red; font-size:20px;">*</span>'), $razao_social ],    
                                       [ new TLabel('Como deseja ser chamado'), $nome_fantasia ],
                                       [ new TLabel('Nascimento<span style="color: red; font-size:20px;">*</span>'), $nascimento ],
                                       [ new TLabel('Orgão Emissor'), $orgao_emissor ],
                                       [ new TLabel('Estado Civil<span style="color: red; font-size:20px;">*</span>'), $estado_civil ]

                                    );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-2','col-sm-2','col-sm-2'];

        //$site = new TEntry('site');
        
        $sexo = new TCombo('sexo');
        $combo_sexo['M'] = 'Masculino';
        $combo_sexo['F'] = 'Feminino';
        $sexo->addItems($combo_sexo);

        // $prazo_atendimento = new TEntry('prazo_atendimento');
        // $prazo_atendimento->setMask('9!');

        $cliente_grupo_id = new TDBCombo('cliente_grupo_id','sample','ClienteGrupo','id','nome','nome');
        $cliente_grupo_id->addValidation('Grupo de Clientes', new TRequiredValidator);
        //$cliente_grupo_id->enableSearch();

        $row = $this->form->addFields(  [ new TLabel('Grupo de cliente<span style="color: red; font-size:20px;">*</span>'), $cliente_grupo_id ],
                                        [ new TLabel('Beneficiário do Mutuante<span style="color: red; font-size:20px;">*</span>'), $beneficiario_mutuante ],
                                        [ new TLabel('CPF do Beneficiário<span style="color: red; font-size:20px;">*</span>'), $cpf_beneficiario_mutuante ]
        );
        $row->layout = ['col-sm-5','col-sm-5','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Sexo'), $sexo ]
                                       );
        $row->layout = ['col-sm-2'];
        
        $representante_naturalidade = new TEntry('representante_naturalidade');
        $representante_naturalidade->addValidation('Naturalidade', new TRequiredValidator);
        $telefone_principal = new TEntry('telefone_principal');
        $telefone_principal->setMask('(999 99)99999-9999');
        $telefone_principal->addValidation('Telefone Principal', new TRequiredValidator);
        $email_principal = new TEntry('email_principal');
        $email_principal->addValidation('Email Principal', new TRequiredValidator);
        $profissao_id = new TDBUniqueSearch('profissao_id', 'sample', 'Profissao', 'id', 'nome');
        $profissao_id->addValidation('Profissão', new TRequiredValidator);

        $row = $this->form->addFields(  [  new TLabel('Profissão<span style="color: red; font-size:20px;">*</span>'),$profissao_id ],
                                        [ new TLabel('Naturalidade<span style="color: red; font-size:20px;">*</span>'), $representante_naturalidade ],
                                        [ new TLabel('Telefone Principal<span style="color: red; font-size:20px;">*</span>'), $telefone_principal ],
                                        [ new TLabel('Email Principal<span style="color: red; font-size:20px;">*</span>'), $email_principal ]);
        $row->layout = ['col-sm-4','col-sm-3','col-sm-2','col-sm-3'];

        $filhos = new TCombo('filhos');
        $combo_filhos['S'] = 'Sim';
        $combo_filhos['N'] = 'Não';
        $filhos->addItems($combo_filhos);
        $filhos->addValidation('Tem Filhos?', new TRequiredValidator);

        $row = $this->form->addFields(  [  new TLabel('Tem Filhos?<span style="color: red; font-size:20px;">*</span>'),$filhos ]);
        $row->layout = ['col-sm-2','col-sm-10'];

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

        $row = $this->form->addFields( [ new TLabel('CEP<span style="color: red; font-size:20px;">*</span>'), $cep ],    
                                       [ new TLabel('Logradouro<span style="color: red; font-size:20px;">*</span>'), $logradouro ],
                                       [ new TLabel('Número'), $numero ],
                                       [ new TLabel('Bairro<span style="color: red; font-size:20px;">*</span>'), $bairro ]);
        $row->layout = ['col-sm-2','col-sm-5', 'col-sm-1', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Cidade<span style="color: red; font-size:20px;">*</span>'), $cidade ],    
                                       [ new TLabel('UF<span style="color: red; font-size:20px;">*</span>'), $uf ],
                                       [ new TLabel('Código do IBGE'), $codMuni ]);
        $row->layout = ['col-sm-5','col-sm-4', 'col-sm-1', 'col-sm-2'];

        $this->form->addContent( ['<h4><b>Dados de quem indicou</b></h4><hr>'] );
        $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor','id', 'nome_fantasia');
        $fornecedor_id->addValidation('Quem indicou', new TRequiredValidator);

        $row = $this->form->addFields( [ new TLabel('Quem Indicou<span style="color: red; font-size:20px;">*</span>'), $fornecedor_id ]   
        );
        $row->layout = ['col-sm-12'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        $this->form->addContent( ['<h4><b>Contatos Secundários</b></h4><hr>'] );
        /* Telefones */
        $tel_responsavel = new TEntry('tel_responsavel[]');
        $tel_responsavel->forceUpperCase();
        $tel_telefone = new TEntry('tel_telefone[]');
        $tel_telefone->setMask('(99)99999-9999');

        //Detalhe dos telefones
        $telefone_array = $this->fieldlist = new TFieldList;
        $this->fieldlist->addField( '<b>Responsável</b>', $tel_responsavel);
        $this->fieldlist->addField( '<b>Telefone</b>', $tel_telefone);
        $this->fieldlist->enableSorting();
        /* Telefones */

        /* Emails */
        $email_responsavel = new TEntry('email_responsavel[]');
        $email_endereco = new TEntry('email_endereco[]');
        $tel_telefone->setMask('(99)99999-9999');

        //Detalhe dos emails
        $email_array = $this->lista_email = new TFieldList;
        $this->lista_email->addField( '<b>Responsável</b>', $email_responsavel);
        $this->lista_email->addField( '<b>E-mail</b>', $email_endereco);
        $this->lista_email->enableSorting();
        /* Emails */
        
        $row = $this->form->addFields( [ new TLabel('Telefone'), $telefone_array ],    
                                       [ new TLabel('Email'), $email_array ]);
        $row->layout = ['col-sm-6', 'col-sm-6'];

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
        $this->onReloadUsuarioLogado($param);
        $this->loaded = TRUE;
    }

    public function loadListData($param) {
        // verifica se tem conteudo do telefone e email
        $tel_responsavel = $param['tel_responsavel'] ?? null;
        $tel_telefone = $param['tel_telefone'] ?? null;
        $email_responsavel = $param['email_responsavel'] ?? null;
        $email_endereco = $param['email_endereco'] ?? null;

        //verifica se é array e tem algum valor
        if ($tel_telefone && is_array($tel_telefone) && count($tel_telefone) > 0)
        {
            $this->fieldlist->addHeader();
            foreach ($tel_telefone as $index => $telefone)
            {

                //cria a classe e adiciona o item
                $tel_detail = new stdClass;
                $tel_detail->tel_responsavel  = $tel_responsavel[$index];
                $tel_detail->tel_telefone = $telefone;
                $this->fieldlist->addDetail($tel_detail);

            }
            $this->fieldlist->addCloneAction();
        }
        else
        {
            $this->fieldlist->addHeader();
            $this->fieldlist->addDetail( new stdClass );
            $this->fieldlist->addCloneAction();
        }

        //mesmo principio do telefone acima.
        if ($email_endereco && is_array($email_endereco) && count($email_endereco) > 0)
        {
            $this->lista_email->addHeader();
            foreach ($email_endereco as $index => $endereco)
            {

                $email_detail = new stdClass;
                $email_detail->email_responsavel  = $$email_responsavel[$index];;
                $email_detail->email_endereco = $endereco;
                $this->lista_email->addDetail($email_detail);

            }
            $this->lista_email->addCloneAction();
        }
        else
        {
            $this->lista_email->addHeader();
            $this->lista_email->addDetail( new stdClass );
            $this->lista_email->addCloneAction();
        }

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

            if( !empty($param['tel_telefone']) AND is_array($param['tel_telefone']) )
            {
                foreach( $param['tel_telefone'] as $row => $tel_telefone)
                {
                    if ($tel_telefone)
                    {
                        $tel = new TelefonesCliente;
                        $tel->cliente_id = $id_cliente;
                        $tel->telefone = $tel_telefone;
                        $tel->responsavel = $param['tel_responsavel'][$row];
                        $tel->store();
                    }
                }
            }

            if( !empty($param['email_endereco']) AND is_array($param['email_endereco']) )
            {
                foreach( $param['email_endereco'] as $row => $email_endereco)
                {
                    if ($email_endereco)
                    {
                        $email = new EmailCliente;
                        $email->cliente_id = $id_cliente;
                        $email->responsavel  = $param['email_responsavel'][$row];
                        $email->email = $email_endereco;
                        
                        $email->store();
                    }
                }
            }
            
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
            $this->loadListData($param);
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
                
                $telefone = $cliente->getTelefonesClientes();
                $email = $cliente->getEmailClientes();

                if ($telefone)
                {
                   $this->fieldlist->addHeader();
                    foreach ($telefone as $tel)
                    {
                        
                        $tel_detail = new stdClass;
                        $tel_detail->tel_responsavel  = $tel->responsavel;
                        $tel_detail->tel_telefone = $tel->telefone;
                        
                        $this->fieldlist->addDetail($tel_detail);

                    }
                    $this->fieldlist->addCloneAction();
                }
                else
                {
                    // $this->onClear($param);
                    $this->fieldlist->addHeader();
                    $this->fieldlist->addDetail( new stdClass );
                    $this->fieldlist->addCloneAction();
                }

                if ($email)
                {
                    $this->lista_email->addHeader();
                    foreach ($email as $e)
                    {
                        
                        $email_detail = new stdClass;
                        $email_detail->email_responsavel  = $e->responsavel;
                        $email_detail->email_endereco = $e->email;
                        
                        $this->lista_email->addDetail($email_detail);

                    }
                    $this->lista_email->addCloneAction();
                }
                else
                {
                    $this->lista_email->addHeader();
                    $this->lista_email->addDetail( new stdClass );
                    $this->lista_email->addCloneAction();
                }

                TTransaction::close();
            }
            else
            {   
                $this->form->clear(TRUE);
                $this->fieldlist->addHeader();
                $this->fieldlist->addDetail( new stdClass );
                $this->fieldlist->addCloneAction();

                $this->lista_email->addHeader();
                $this->lista_email->addDetail( new stdClass );
                $this->lista_email->addCloneAction();

                TSession::setValue(__CLASS__.'_items', null);
                TSession::setValue(__CLASS__.'_items_equipamento', null);
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
