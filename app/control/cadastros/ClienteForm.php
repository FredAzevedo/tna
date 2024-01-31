<?php

use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Wrapper\TDBCombo;

/**
 * ClienteForm Form
 * @author  Fred Azv.
 */
class ClienteForm extends TPage
{
    protected $form; // form

    private $fieldlist;
    private $lista_email;
    
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

                // if(size <= 11) { $('select[name=\"tipo\"]').val('F');}
                // else $('select[name=\"tipo\"]').val('J');

                $(this).mask((size <= 11) ? '000.000.000-00' : '00.000.000/0000-00');


            });

            $(document).on('keydown', 'input[name=\"representante_cpf\"]', function (e) {

                var digit = e.key.replace(/\D/g, '');

                var value = $(this).val().replace(/\D/g, '');

                var size = value.concat(digit).length;

                $(this).mask((size <= 11) ? '000.000.000-00' : '00.000.000/0000-00');


            });
            
        ";
       
        $script->add($javascript);
        parent::add($script);

    
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Cliente');
        $this->form->setFormTitle('Cliente');
        $this->form->setFieldSizes('100%');     
        
        // $this->form->appendPage('Dados Principais');
        // create the form fields
        $id = new TEntry('id');
        
        $user_id = new THidden('user_id');
        $user_id->setValue(TSession::getValue('userid'));

        $unit_id = new THidden('unit_id');
        $unit_id->setValue(TSession::getValue('userunitid'));

        $tipo = new THidden('tipo');
        $tipo->setValue('F');

        $row = $this->form->addFields( [ $user_id ],[ $unit_id ], [ $tipo ] );

        $cpf_cnpj = new TEntry('cpf_cnpj');
        $cpf_cnpj->addValidation('CPF/CNPJ', new TRequiredValidator);

        $cpf_cnpj->onkeyup = 'copiaCpf()';

        TScript::create('copiaCpf = function() {
                
                let tipoPessoa = form_Cliente.tipo.value;
                let cpf = form_Cliente.cpf_cnpj.value;

                if(tipoPessoa == "F")
                {
                    form_Cliente.representante_cpf.value = cpf;
                }
            };
        ');

        $representante_nome = new TEntry('representante_nome');
    

        $nascimento = new TDate('nascimento');
        $nascimento->setDatabaseMask('yyyy-mm-dd');
        $nascimento->setMask('dd/mm/yyyy');
     
        $documento_identificacao = new TEntry('documento_identificacao');
        $documento_identificacao->addValidation('Documento de Identificação', new TRequiredValidator);

        $row = $this->form->addFields( [ new TLabel('ID'), $id ]);
        $row->layout = ['col-sm-2'];

        $this->form->addContent( ['<hr><h4><b>Dados do Representante pelo Aluno</b></h4>'] );

        $row = $this->form->addFields( [ new TLabel('Nome do Representante'), $representante_nome]);
        $row->layout = ['col-sm-12'];
        
        $this->form->addContent( ['<hr><h4><b>Dados do Aluno</b></h4>'] );

        $razao_social = new TEntry('razao_social');
        $razao_social->forceUpperCase();
        $razao_social->addValidation('Razão Social', new TRequiredValidator);

        $aluno_id = new TEntry('aluno_id');
        $aluno_id->setEditable(FALSE);

        $row = $this->form->addFields( [ new TLabel('Código do Aluno'), $aluno_id ], 
                                       [ new TLabel('Nome do Aluno<span style="color: red; font-size:20px;">*</span>'), $razao_social ],
                                       [ new TLabel('CPF/CNPJ<span style="color: red; font-size:20px;">*</span>'), $cpf_cnpj ]
                                    );
        $row->layout = ['col-sm-2','col-sm-8','col-sm-2'];

    

        if (!empty($_GET['id']))
        {
            TTransaction::open('sample');
            $pegarID = new Cliente($_GET['id']);
            if (!empty($pegarID->id)) {
                if($pegarID->tipo == 'J'){
                    $cpf_cnpj->setMask('99.999.999/9999-99');
                }else{
                    $cpf_cnpj->setMask('999.999.999-99');
                }
            }
            TTransaction::close();
        }

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
        

        /*Endereço*/
        //$this->form->appendPage('Endereço');
        $this->form->addContent( ['<hr><h4><b>Endereço do Aluno</b></h4>'] );

        // detail fields
      
        $cep = new TEntry('cep');
        $buscaCep = new TAction(array($this, 'onCep'));
        $cep->setExitAction($buscaCep);
        $cep->addValidation('CEP', new TRequiredValidator);
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
        $uf = new TCombo('uf');
        $uf_array = Utilidades::uf();
        $uf->addItems($uf_array);
        $uf->addValidation('UF', new TRequiredValidator);
        
        $codMuni = new TEntry('codMuni');
        $codMuni->addValidation('Código do IBGE', new TRequiredValidator);
    
        
        $row = $this->form->addFields( [ new TLabel('CEP'), $cep ],    
                                       [ new TLabel('Logradouro'), $logradouro ],
                                       [ new TLabel('Nº'), $numero ],
                                       [ new TLabel('Bairro'), $bairro ]);
        $row->layout = ['col-sm-2', 'col-sm-5', 'col-sm-1', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Cidade'), $cidade ],    
                                       [ new TLabel('UF'), $uf ],
                                       [ new TLabel('Código do IBGE'), $codMuni ]);
        $row->layout = ['col-sm-5','col-sm-3', 'col-sm-2', 'col-sm-2'];
      

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }


        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave'],[ 'static' => '1']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ClienteForm', 'onEdit']), 'fa:plus green');
        $this->form->addAction('Voltar', new TAction( [$this, 'onExit'] ), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml','ClienteList'));
        $container->add($this->form);

        
        parent::add($container);
        //TPage::include_js('app/resources/ClienteForm.js');
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
                            

    public function onReload($param)
    {
        $this->loaded = TRUE;
    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample');

            $data = $this->form->getData('Cliente'); 
            $this->form->validate(); 
            //TTransaction::setLogger(new TLoggerSTD); // debugar sql
            $data->store();

            //Recebendo valor do cliente cadastrado para inserir nas tabelas detalhes
            $id_cliente = $data->id;

            
            $this->onEdit(array('key'=> $id_cliente));
            
            $this->form->setData($data);
            TTransaction::close();

            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            AdiantiCoreApplication::loadPage(__CLASS__, 'onEdit', $param);
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            //$this->form->setData( $this->form->getData() ); // keep form data
            $this->form->setData( $this->fieldlist->getPostData() );
            $this->onEdit($param);
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
                TTransaction::close();
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
