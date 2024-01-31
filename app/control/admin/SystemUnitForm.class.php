<?php
/**
 * SystemUnitForm
 *
 * @version    7.6
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class SystemUnitForm extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_SystemUnit');
        $this->form->setFormTitle(_t('Unit'));
        $this->form->setFieldSizes('100%');
        
        $this->form->appendPage('Dados Principais');
        $this->form->addContent( ['<h4>DADOS PRINCIPAIS</h4><hr>'] );
        /*Dados Principais*/
        $id = new TEntry('id');
        $id->setEditable(FALSE);
        $matriz_filial = new TCombo('matriz_filial');
        $combo_items = [];
        $combo_items['1'] = 'Matriz';
        $combo_items['2'] = 'Filial';
        $matriz_filial->addItems($combo_items);
        $razao_social = new TEntry('razao_social');
        $nome_fantasia = new TEntry('nome_fantasia');
        $unidade = new TEntry('unidade');
        $responsavel = new TEntry('responsavel');
        /*Dados Principais*/
        
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Matriz/Filial'), $matriz_filial ],
                                       [ new TLabel('Razão Social'), $razao_social ],
                                       [ new TLabel('Nome Fantasia'), $nome_fantasia ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-4', 'col-sm-4'];
        
         $row = $this->form->addFields([ new TLabel('Unidade'), $unidade ],
                                       [ new TLabel('Responsável Legal'), $responsavel ]);
        $row->layout = ['col-sm-4', 'col-sm-4'];
        
        //$this->form->addContent( ['<h4>DADOS JURÍDICOS</h4><hr>'] );
        /*Dados Jurídicos*/
        $cnpj = new TEntry('cnpj');
        $cnpj->setMask('99.999.999/9999-99');
        $buscarCnpj = new TAction(array($this, 'onCNPJ'));
        $cnpj->setExitAction($buscarCnpj);
        $insc_estadual = new TEntry('insc_estadual');
        $insc_municipal = new TEntry('insc_municipal');
        $cnae = new TEntry('cnae');

        $crt = new TCombo('crt');
        $combo_crt = [];
        $combo_crt['1'] = 'Simples Nacional';
        $combo_crt['3'] = 'Regime Normal';
        $crt->addItems($combo_crt);
        
        $atividade = new TEntry('atividade');
        $junta_comercial = new TEntry('junta_comercial');
        /*Dados Jurídicos*/
        
        $row = $this->form->addFields( [ new TLabel('CNPJ'), $cnpj ],    
                                       [ new TLabel('Inscrição Estadual'), $insc_estadual ],
                                       [ new TLabel('Inscrição Municipal'), $insc_municipal ],
                                       [ new TLabel('CNAE'), $cnae ],
                                       [ new TLabel('CRT'), $crt ],
                                       [ new TLabel('Junta Comercial'), $junta_comercial ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2'];
        
        $this->form->addContent( ['<h4>DADOS COMPLEMENTARES</h4><hr>'] );
        /*Dados Complementares*/
        $porte = new TCombo('porte');
        $combo_items = [];
        $combo_items['1'] = 'Pequeno';
        $combo_items['2'] = 'Medio';
        $combo_items['3'] = 'Grande';
        $porte->addItems($combo_items);
        $email = new TEntry('email');
        $site = new TEntry('site');

        $ativo = new TCombo('ativo');
        $combo_items_ativo['S'] = 'Sim';
        $combo_items_ativo['N'] = 'Não';
        $ativo->addItems($combo_items_ativo);

        $cont_limite = new TCombo('cont_limite');
        $combo_cont_limite['S'] = 'Sim';
        $combo_cont_limite['N'] = 'Não';
        $cont_limite->addItems($combo_cont_limite);
        $telefone = new TEntry('telefone');

        $limite = new TNumeric('limite','2',',','.');
        /*Dados Complementares*/
        
        $row = $this->form->addFields( [ new TLabel('Porte'), $porte ],    
                                       [ new TLabel('E-mail principal'), $email ],
                                       [ new TLabel('Site'), $site ],
                                       [ new TLabel('Ativo'), $ativo ],
                                       [ new TLabel('Controla Limite?'), $cont_limite ],
                                       [ new TLabel('Limite'), $limite ]);
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-2', 'col-sm-1', 'col-sm-2','col-sm-2'];
        

        $row = $this->form->addFields( [ new TLabel('Telefone Principal'), $telefone ]);
        $row->layout = ['col-sm-4'];

        $this->form->appendPage('Endereço');
        $this->form->addContent( ['<h4>ENDEREÇO</h4><hr>'] );

        /*Endereço*/
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
        /*Endereço*/
        
        $row = $this->form->addFields( [ new TLabel('CEP'), $cep ],    
                                       [ new TLabel('Logradouro'), $logradouro ],
                                       [ new TLabel('Número'), $numero ],
                                       [ new TLabel('Bairro'), $bairro ],
                                       [ new TLabel('Complemento'), $complemento ]);
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-1', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Cidade'), $cidade ],    
                                       [ new TLabel('UF'), $uf ],
                                       [ new TLabel('Código do Município'), $codMuni ]);
        $row->layout = ['col-sm-3', 'col-sm-1', 'col-sm-2'];
        
        $this->form->appendPage('Dados Adicionais');
        $this->form->addContent( ['<h4>DADOS ADICIONAIS</h4><hr>'] );
        /* Telefones */
        $tel_responsavel = new TEntry('tel_responsavel[]');
        $tel_telefone = new TEntry('tel_telefone[]');
        $tel_telefone->setMask('(84)99999-9999');

        //Detalhe dos telefones
        $telefone_array = $this->fieldlist = new TFieldList;
        $this->fieldlist->addField( '<b>Responsável</b>', $tel_responsavel);
        $this->fieldlist->addField( '<b>Telefone</b>', $tel_telefone);
        $this->fieldlist->enableSorting();
        /* Telefones */
        
        /* Emails */
        $email_responsavel = new TEntry('email_responsavel[]');
        $email_endereco = new TEntry('email_endereco[]');
        $tel_telefone->setMask('(84)99999-9999');

        //Detalhe dos emails
        $email_array = $this->lista_email = new TFieldList;
        $this->lista_email->addField( '<b>Responsável</b>', $email_responsavel);
        $this->lista_email->addField( '<b>E-mail</b>', $email_endereco);
        $this->lista_email->enableSorting();
        /* Emails */
        
        $row = $this->form->addFields( [ new TLabel('Telefone'), $telefone_array ],    
                                       [ new TLabel('Email'), $email_array ]);
        $row->layout = ['col-sm-5', 'col-sm-5'];

        $this->form->appendPage('Dados do Contabilista');
        $this->form->addContent( ['<h4>DADOS DO CONTABILISTA</h4><hr>'] );

        $contabilista_nome  = new TEntry('contabilista_nome');
        $contabilista_cpf   = new TEntry('contabilista_cpf');
        $contabilista_cpf->setMask('999.999.999-99');
        $contabilista_crc   = new TEntry('contabilista_crc');
        $contabilista_cnpj  = new TEntry('contabilista_cnpj');
        $contabilista_cnpj->setMask('99.999.999/9999-99');
        $contabilista_cep   = new TEntry('contabilista_cep');
        $contabilista_cep->setMask('99.999-999');
        $contabilista_end   = new TEntry('contabilista_end');
        $contabilista_num   = new TEntry('contabilista_num');
        $contabilista_compl = new TEntry('contabilista_compl');
        $contabilista_bairro = new TEntry('contabilista_bairro');
        $contabilista_fone  = new TEntry('contabilista_fone');
        $contabilista_email = new TEntry('contabilista_email');

        $row = $this->form->addFields( [ new TLabel('Nome do Contabilista'), $contabilista_nome ],    
                                       [ new TLabel('CPF'), $contabilista_cpf ],
                                       [ new TLabel('CRC'), $contabilista_crc ],  
                                       [ new TLabel('CNPJ'), $contabilista_cnpj ]
                                    
        );
        $row->layout = ['col-sm-6', 'col-sm-2','col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('CEP'), $contabilista_cep ],    
                                       [ new TLabel('Logradouro'), $contabilista_end ],
                                       [ new TLabel('Número'), $contabilista_num ],
                                       [ new TLabel('Bairro'), $contabilista_bairro ],
                                       [ new TLabel('Complemento'), $contabilista_compl ]);
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-1', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Fone'), $contabilista_fone ],    
                                       [ new TLabel('Email'), $contabilista_email ]
        );
        $row->layout = ['col-sm-2', 'col-sm-4'];

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('Clear'),  new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addAction(_t('Back'),new TAction(array('SystemUnitList','onReload')),'fa:arrow-circle-o-left blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'SystemUnitList'));
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
                $obj->codMuni       = $objeto->ibge;

                TForm::sendData('form_SystemUnit',$obj);
                unset($obj);
            }else{
                new TMessage('info', 'Erro ao buscar endereço por este CEP.');
            }
        }catch (Exception $e){
            new TMessage('error', '<b>Error:</b> ' . $e->getMessage());
        }
    }
    
    public static function onCNPJ($param)
    {
        try {
            $retorno = Utilidades::onCNPJ($param['cnpj']);
            $objeto  = json_decode($retorno);
         
            if (isset($objeto->nome)){
            $obj              = new stdClass();
            $obj->razao_social = $objeto->nome;
            $obj->nome_fantasia = $objeto->fantasia;
            
            TForm::sendData('form_SystemUnit',$obj);
            unset($obj);
            }else{
                new TMessage('info', 'Erro ao buscar endereço por este CNPJ.');
            }
            
        }catch (Exception $e){
            new TMessage('error', '<b>Error:</b> ' . $e->getMessage());
        }
    }
    
    public function onSave($param)
    {
        try
        {   
            TTransaction::open('sample');
            $unidade = $this->form->getData('SystemUnit');
            $unidade->store();

            //Recebendo valor da unidade cadastrada para inserir na tabela TelefonesUnidade
            $id_unidade = $unidade->id;

            $unidade->fromArray( $param );
            if( !empty($param['tel_responsavel']) AND is_array($param['tel_responsavel']) )
            {

                foreach( $param['tel_responsavel'] as $row => $tel_responsavel)
                {
                    if ($tel_responsavel)
                    {
                        $tel = new TelefonesUnidade;
                        $tel->unidades_id = $id_unidade;
                        $tel->responsavel  = $tel_responsavel;
                        $tel->telefone = $param['tel_telefone'][$row];
                        $tel->store();
                    }
                }
            }

            if( !empty($param['email_responsavel']) AND is_array($param['email_responsavel']) )
            {
                foreach( $param['email_responsavel'] as $row => $email_responsavel)
                {
                    if ($email_responsavel)
                    {
                        $email = new EmailUnidade;
                        $email->unidades_id = $id_unidade;
                        $email->responsavel  = $email_responsavel;
                        $email->email = $param['email_endereco'][$row];
                        $email->store();
                    }
                }
            }

            $this->form->setData( $unidade );
            TTransaction::close();

            new TMessage('info', 'Dados salvos com sucesso!');

           //$param['key'] = $param['cliente_id'];
            AdiantiCoreApplication::loadPage(__CLASS__, 'onEdit', $param);

            // $win = TWindow::create('test', 0.6, 0.8);
            // $win->add( '<pre>'.str_replace("\n", '<br>', print_r($param, true) ).'</pre>'  );
            // $win->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
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

    public function onEdit($param)
    {
        try
        {   
            if(isset($param['id']))
            {
                TTransaction::open('sample');

                $unidade = new SystemUnit( $param['id'] );
                $this->form->setData( $unidade );

                $telefone = $unidade->getTelefonesUnidade();
                $email = $unidade->getEmailsUnidade();

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
                $this->form->clear();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onClear($param)
    {
        $this->form->clear();
    }
    
}
