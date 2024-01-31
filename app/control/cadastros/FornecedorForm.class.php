<?php
/**
 * FornecedorForm Form
 * @author  <your name here>
 */
class FornecedorForm extends TPage
{
    protected $form; // form
    
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

            // personaliza os campos de acordo com o tipo de pessoa
            $('select[name=\"tipo\"]').change(function(event){
                var tipoPessoa;
                $('select[name=\"tipo\"] > option:selected').each(function(){
                           tipoPessoa = $(this).text();
                });
                
                //alert(tipoPessoa.toLowerCase());
                if (tipoPessoa.toLowerCase() == 'pessoa física') {
                    //$('label:contains(CNPJ/CPF)').text('CPF');
                    //$('label:contains(CNPJ)').text('CPF');
                    $('input[name=\"cpf_cnpj\"]').attr({onkeypress:'return tentry_mask(this,event,\"999.999.999-99\")'}).val('');
                }
                if (tipoPessoa.toLowerCase() == 'pessoa jurídica') {
                    //$('label:contains(CNPJ/CPF)').text('CNPJ');
                    //$('label:contains(CPF)').text('CNPJ');
                    $('input[name=\"cpf_cnpj\"]').attr({onkeypress:'return tentry_mask(this,event,\"99.999.999/9999-99\")'}).val('');
                }
            });
        ";
   
        $script->add($javascript);
        parent::add($script);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Fornecedor');
        $this->form->setFormTitle('Fornecedor');
        $this->form->setFieldSizes('100%');
        
        $this->form->appendPage('Dados Principais');
        // create the form fields
        $id = new TEntry('id');
        $nome_fantasia = new TEntry('nome_fantasia');
        $razao_social = new TEntry('razao_social');
        $insc_estadual = new TEntry('insc_estadual');
        $insc_estadual->setMask('9!');
            
        $tipo = new TCombo('tipo');
        $comboTipo['F'] = 'Pessoa Física';
        $comboTipo['J'] = 'Pessoa Jurídica';
        $tipo->addItems($comboTipo);
        
        $cpf_cnpj = new TEntry('cpf_cnpj');
        $buscaCnpj = new TAction(array($this, 'onCNPJ'));
        $cpf_cnpj->onBlur = 'validaCpfCnpj(this,\'form_Fornecedor\')';
        $cpf_cnpj->setExitAction($buscaCnpj);

        $cep = new TEntry('cep');
        $buscaCep = new TAction(array($this, 'onCep'));
        $cep->setExitAction($buscaCep);
        $cep->setMask('99.999-999');

        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $bairro = new TEntry('bairro');
        $complemento = new TEntry('complemento');
        $cidade = new TEntry('cidade');
        $uf = new TEntry('uf');
        $codMuni = new TEntry('codMuni');
        $site = new TEntry('site');
        
        $parceria = new TCombo('parceria');
        $comboParceria['S'] = 'Sim';
        $comboParceria['N'] = 'Não';
        $parceria->addItems($comboParceria);
        
        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);
        
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
        $user_id->addValidation('Usuário', new TRequiredValidator);
        $user_id->setEditable(FALSE);


        // add the fields
        
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Atualizado Por'), $user_id ], 
                                       [ new TLabel('Unidade'), $unit_id ],   
                                       [ new TLabel('Tipo'), $tipo ],
                                       [ new TLabel('CPF/CNPJ'), $cpf_cnpj ]);
        $row->layout = ['col-sm-1', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-5'];
        
        $row = $this->form->addFields( [ new TLabel('Razão Social'), $razao_social ],
                                       [ new TLabel('Nome Fantasia'), $nome_fantasia ],    
                                       [ new TLabel('IE'), $insc_estadual ]);
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        //$this->form->addContent( ['<h4>Endereço</h4><hr>'] );
        
        $row = $this->form->addFields( [ new TLabel('CEP'), $cep ],    
                                       [ new TLabel('Logradouro'), $logradouro ],
                                       [ new TLabel('Nº'), $numero ],
                                       [ new TLabel('Bairro'), $bairro ]);
        $row->layout = ['col-sm-2', 'col-sm-5', 'col-sm-1', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Cidade'), $cidade ],    
                                       [ new TLabel('UF'), $uf ],
                                       [ new TLabel('Código do IBGE'), $codMuni ]);
        $row->layout = ['col-sm-5','col-sm-4', 'col-sm-1', 'col-sm-2'];
        
        //$this->form->addContent( ['<h4>Complementos</h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('Site'), $site ]);
        $row->layout = ['col-sm-4','col-sm-2', 'col-sm-3', 'col-sm-3'];
        
        
        $this->form->addContent( ['<h4>Contatos</h4><hr>'] );
        
        /* Telefones */
        $tel_responsavel = new TEntry('tel_responsavel[]');
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

        //Detalhe dos emails
        $email_array = $this->lista_email = new TFieldList;
        $this->lista_email->addField( '<b>Responsável</b>', $email_responsavel);
        $this->lista_email->addField( '<b>E-mail</b>', $email_endereco);
        $this->lista_email->enableSorting();
        /* Emails */
        
        $row = $this->form->addFields( [ new TLabel('Telefone'), $telefone_array ],    
                                       [ new TLabel('Email'), $email_array ]);
        $row->layout = ['col-sm-5', 'col-sm-5'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['FornecedorForm', 'onEdit']), 'fa:plus green');
        $this->form->addAction('Voltar', new TAction([$this, 'onExit']), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'FornecedorList'));
        $container->add($this->form);

        /*$container->adianti_target_container = 'FornecedorList';
        $container->adianti_target_title = 'Fornecedor ';*/
        
        parent::add($container);
    }

    public function onReloadUsuarioLogado($param){
        $dados = new stdclass();
        $dados->user_id = TSession::getValue('userid');
        TForm::sendData('form_Fornecedor', $dados);
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

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample');
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); 
            $data = $this->form->getData(); 
            
            $fornecedor = new Fornecedor;  
            $fornecedor->fromArray( (array) $data);
            $fornecedor->store();
            
            // get the generated id
            $data->id = $fornecedor->id;
            
            //Recebendo valor da unidade cadastrada para inserir na tabela TelefonesFornecedor
            $id_fornecedor = $fornecedor->id;

            $fornecedor->fromArray( $param );
            if( !empty($param['tel_telefone']) AND is_array($param['tel_telefone']) )
            {
                foreach( $param['tel_telefone'] as $row => $tel_telefone)
                {
                    if ($tel_telefone)
                    {
                        $tel = new TelefonesFornecedor;
                        $tel->fornecedor_id = $id_fornecedor;
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
                        $email = new EmailFornecedor;
                        $email->fornecedor_id = $id_fornecedor;
                        $email->responsavel  = $param['email_responsavel'][$row];
                        $email->email = $email_endereco;
                        
                        $email->store();
                    }
                }
            }
            
            $this->form->setData($data); 
            TTransaction::close(); 
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            AdiantiCoreApplication::loadPage(__CLASS__, 'onEdit', $param);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); 
            $this->onReload($param);
            $this->loadListData($param);
            TTransaction::rollback(); 
        }
    }
    
    public function onAdd(){

        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addCloneAction();

        $this->lista_email->addHeader();
        $this->lista_email->addDetail( new stdClass );
        $this->lista_email->addCloneAction();
    }
    
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }


    public static function onCNPJ($param){
        try {
        
          if(strlen(trim($param['cpf_cnpj'])) == 18){
          
                $retorno = Utilidades::onCNPJ($param['cpf_cnpj']);
                $objeto  = json_decode($retorno);
                //var_dump($objeto);
                if (isset($objeto->nome)){
                $obj              = new stdClass();
                $obj->razao_social = $objeto->nome;
                $obj->nome_fantasia = $objeto->fantasia;
                
                TForm::sendData('form_Fornecedor',$obj);
                unset($obj);
                }
           } 
        }catch (Exception $e){
            new TMessage('error', '<b>Error:</b> ' . $e->getMessage());
        }
    }

    public static function onCep($param){
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

                TForm::sendData('form_Fornecedor',$obj, false, false );
                unset($obj);
            }else{
                //new TMessage('info', 'Erro ao buscar endereço por este CEP.');
            }
        }catch (Exception $e){
            new TMessage('error', '<b>Error:</b> ' . $e->getMessage());
        }
    }
    
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['id']))
            {
            
                TTransaction::open('sample'); 
                $fornecedor = new Fornecedor($param['id']);
                
                $telefone = $fornecedor->getTelefonesFornecedors();
                $email = $fornecedor->getEmailFornecedors();

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
                
                
                $this->form->setData($fornecedor); // fill the form
                $this->onReload( $param );
                TTransaction::close(); // close the transaction
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

                $this->onReload( $param );

            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onExit()
    {
        $result = TSession::getValue('FornecedorList');

        $query = isset($result['query']) ? $result['query'] : null;

        if (!empty($query))
        {
            TScript::create("
                Adianti.waitMessage = 'Listando...';__adianti_post_data('FornecedorList', '$query');                                 
        ");
        }
    }
}
