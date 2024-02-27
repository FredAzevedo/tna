<?php

use Adianti\Widget\Form\TEntry;

/**
 * FornecedorForm Form
 * @author  <your name here>
 */
class FornecedorForm extends TPage
{
    protected $form; // form
    private $pageNavigation;
    private $datagrid_historico;
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
        $cpf_cnpj->onKeyUp = 'fwFormatarCpfCnpj(this)';
        $buscaCnpj = new TAction(array($this, 'onCNPJ'));
        //$cpf_cnpj->onBlur = 'validaCpfCnpj(this,\'form_Fornecedor\')';
        //$cpf_cnpj->setExitAction($buscaCnpj);

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
        
        $comissao_tabela_id = new TDBCombo('comissao_tabela_id','sample','ComissaoTabela','id','descricao','descricao');

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
        
        $rg = new TEntry('rg');

        $estado_civil = new TCombo('estado_civil');
        $comboestado_civil['Casado(a)'] = 'Casado(a)';
        $comboestado_civil['Solteiro(a)'] = 'Solteiro(a)';
        $comboestado_civil['Viuvo(a)'] = 'Viuvo(a)';
        $comboestado_civil['Divorciado(a)'] = 'Divorciado(a)';
        $estado_civil->addItems($comboestado_civil);
        
        // add the fields
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Atualizado Por'), $user_id ], 
                                       [ new TLabel('Unidade'), $unit_id ],   
                                       [ new TLabel('Tipo'), $tipo ],
                                       [ new TLabel('CPF/CNPJ'), $cpf_cnpj ]);
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-2', 'col-sm-2', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Razão Social'), $razao_social ],
                                       [ new TLabel('Nome Fantasia'), $nome_fantasia ],
                                       [ new TLabel('IE'), $insc_estadual ],
                                       [ new TLabel('RG'), $rg ]
                                    
        );
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-2','col-sm-2'];

        $orgao_emissor = new TEntry('orgao_emissor');
        $beneficiario_mutuante = new TEntry('beneficiario_mutuante');
        $cpf_beneficiario_mutuante = new TEntry('cpf_beneficiario_mutuante');
        $cpf_beneficiario_mutuante->onKeyUp = 'fwFormatarCpfCnpj(this)';
        //$cpf_beneficiario_mutuante->onBlur = 'validaCpfCnpj(this,\'form_Fornecedor\')';

        $row = $this->form->addFields(  [ new TLabel('Estado Civil'), $estado_civil ],
                                        [ new TLabel('Orgão Emissor'), $orgao_emissor ],
                                        [ new TLabel('Beneficiário do Mutuante'), $beneficiario_mutuante ],
                                        [ new TLabel('CPF do Beneficiário'), $cpf_beneficiario_mutuante ]
                                    
        );
        $row->layout = ['col-sm-2','col-sm-2','col-sm-4','col-sm-2'];

        $telefone_principal = new TEntry('telefone_principal');
        $telefone_principal->setMask('(99)99999-9999');
        $email_principal = new TEntry('email_principal');
        $email_principal->addValidation('Email Principal', new TRequiredValidator);

        $row = $this->form->addFields(  [ new TLabel('Telefone Principal'), $telefone_principal ],
                                        [ new TLabel('Email Principal'), $email_principal ],
                                        [ new TLabel('Comissão'), $comissao_tabela_id  ]);
        $row->layout = ['col-sm-2','col-sm-3','col-sm-3'];
        
        $this->form->addContent( ['<h4>Endereço</h4><hr>'] );
        
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

        //$this->form->addContent( ['<h4>Complementos</h4><hr>'] );

        // $row = $this->form->addFields( [ new TLabel('Site'), $site ],
        //                                [ new TLabel('É Parceria?'), $parceria ]);
        // $row->layout = ['col-sm-4','col-sm-2', 'col-sm-3', 'col-sm-3'];

        $this->form->appendPage('Dados Bancários');

        $banco_nome = new TEntry('banco_nome');
        $banco_representante = new TEntry('banco_representante');
        $banco_agencia = new TEntry('banco_agencia');
        $banco_conta = new TEntry('banco_conta');
        $banco_pix = new TEntry('banco_pix');
        
        $row = $this->form->addFields( [ new TLabel('Nome do Banco'), $banco_nome ]);
        $row->layout = ['col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Representante da conta'), $banco_representante ]);
        $row->layout = ['col-sm-4'];

        $row = $this->form->addFields(  [ new TLabel('Agência'), $banco_agencia ],
                                        [ new TLabel('Conta Corrente'), $banco_conta ]
        );
        $row->layout = ['col-sm-2','col-sm-2'];

        $row = $this->form->addFields(  [ new TLabel('PIX'), $banco_pix ]
        );
        $row->layout = ['col-sm-4'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        //Histórico de compras
        // $this->form->appendPage('Histórico de Compras');
        // $this->datagrid_historico = new BootstrapDatagridWrapper(new TDataGrid);
        // $this->datagrid_historico->style = "width:100%";
        // $this->datagrid_historico->setHeight(300);
        // $this->datagrid_historico->makeScrollable();

        // $this->datagrid_historico->addColumn( new TDataGridColumn('produto','Produto','left','50%') );
        // $this->datagrid_historico->addColumn( new TDataGridColumn('referencia','Referência','left','10%') );
        // //$this->datagrid_historico->addColumn( new TDataGridColumn('nome_fantasia', 'Fornecedor','left') );
        // $this->datagrid_historico->addColumn( new TDataGridColumn('nota','Nº NFe','left','10%') );
        // $this->datagrid_historico->addColumn( $column_emissao = new TDataGridColumn('emissao','Emissão','center','10%') );
        // $this->datagrid_historico->addColumn( $column_lancado = new TDataGridColumn('lancado','Lançado','left','10%') );
        // $this->datagrid_historico->addColumn( $column_valor = new TDataGridColumn('valor_compra','Valor','left','10%') );
        
        // $column_emissao->setTransformer( function($value, $object, $row) {
        //     $date = new DateTime($value);
        //     return $date->format('d/m/Y');
        // });

        // $column_lancado->setTransformer( function($value, $object, $row) {
        //     $date = new DateTime($value);
        //     return $date->format('d/m/Y');
        // });

        // $format_value = function($value) {
        //     if (is_numeric($value)) {
        //         return 'R$ '.number_format($value, 2, ',', '.');
        //     }
        //     return $value;
        // };

        // $input_search = new TEntry('input_search');
        // $input_search->placeholder = _t('Search');
        // $input_search->setSize('100%');

        // $this->datagrid_historico->enableSearch($input_search, 'produto, referencia, nota, emissao,lancado,valor_compra');

        // $column_valor->setTransformer( $format_value );

        // $this->datagrid_historico->createModel();

        // // $this->pageNavigation = new TPageNavigation;
        // // $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        // // $this->pageNavigation->enableCounters();

        // $panel2 = new TPanelGroup;
        // $panel2->addHeaderWidget($input_search);
        // $panel2->add($this->datagrid_historico);
        // $panel2->getBody()->style = 'overflow-x:auto';
        // $panel2->addFooter($this->pageNavigation);
        // //$panel2->addFooter('footer');
        // $this->form->addContent( [$panel2] );
         
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
                
                TSession::setValue('fornecedor_id', $param['id']);

                TTransaction::open('sample'); 
                $fornecedor = new Fornecedor($param['id']);

                // $historico_produto = HistoricoCompraProduto::where('fornecedor_id','=', $param['id'])->load();

                // foreach( $historico_produto as $item )
                // {
                //     $row = $this->datagrid_historico->addItem( $item );
                // }
                
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

    // function onReload($param)
    // {
    //     try
    //     {
    //         $this->onReloadUsuarioLogado($param);
    //         TTransaction::open('sample');

    //         $fornecedor = new Fornecedor(TSession::getValue('fornecedor_id'));
    //         $this->form->setData($fornecedor);

    //         $repository = new TRepository('HistoricoCompraProduto');
    //         $limit = 15;
 
    //         $criteria = new TCriteria;

    //         if (empty($param['order']))
    //         {
    //             $param['order'] = 'lancado';
    //             $param['direction'] = 'desc';
                
    //         }

    //         $criteria->add(new TFilter('fornecedor_id','=', TSession::getValue('fornecedor_id'))); 
            
    //         $criteria->setProperties($param); // order, offset
    //         $criteria->setProperty('limit', $limit);
            
    //         if (TSession::getValue('compra_filter'))
    //         {
    //             $criteria->add(TSession::getValue('compra_filter'));
    //         }
            
    //         $objects = $repository->load($criteria);
            
    //         $this->datagrid_historico->clear();
    //         if ($objects)
    //         {
    //             foreach ($objects as $object)
    //             {
    //                 $this->datagrid_historico->addItem($object);
    //             }
    //         }
            
    //         $criteria->resetProperties();
    //         $count = $repository->count($criteria);
            
    //         $this->pageNavigation->setCount($count); // count of records
    //         $this->pageNavigation->setProperties($param); // order, page
    //         $this->pageNavigation->setLimit($limit); // limit
            
    //         $this->form->setCurrentPage(2);

    //         TTransaction::close();
    //         $this->loaded = true;
    //     }
    //     catch (Exception $e)
    //     {
    //         new TMessage('error', $e->getMessage());
    //         TTransaction::rollback();
    //     }
    // }

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
