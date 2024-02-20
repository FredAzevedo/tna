<?php

/**
 * ContaReceberForm Form
 * @author  Fred Azv.
 */
class ContaReceberForm extends TPage
{
    protected $form; // form

    use Adianti\Base\AdiantiFileSaveTrait;

    public function __construct( $param )
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder('form_ContaReceber');
        $this->form->setFormTitle('Contas a Receber');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new TEntry('id');

        if (!empty($_GET['id']))
        {
            $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
            $user_id->setEditable(FALSE);
            $user_id->addValidation('Usuário de Criação', new TRequiredValidator);

            $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade');
            $unit_id->setEditable(FALSE);
            $unit_id->addValidation('Unidade de Criação', new TRequiredValidator);

        } else {

            $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
            $user_id->setValue(TSession::getValue('userid'));
            $user_id->setEditable(FALSE);
            $user_id->addValidation('Unidade de Criação', new TRequiredValidator);

            $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade');
            $unit_id->setValue(TSession::getValue('userunitid'));
            $unit_id->setEditable(FALSE);
            $unit_id->addValidation('Unidade de Criação', new TRequiredValidator);
        }

        $data_conta = new TDate('data_conta');
        $data_conta->setValue(date('d/m/Y'));
        $data_conta->addValidation('Competência', new TRequiredValidator);
        $data_conta->setDatabaseMask('yyyy-mm-dd');
        $data_conta->setMask('dd/mm/yyyy');

        $descricao = new TText('descricao');
        $descricao->addValidation('Descrição', new TRequiredValidator);

        $documento = new TEntry('documento');

        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->addValidation('Data de Vencimento', new TRequiredValidator);
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');

        $previsao = new TEntry('previsao');
        $multa = new TNumeric('multa',2,',','.',true);
        $juros = new TNumeric('juros',2,',','.',true);
        $valor = new TNumeric('valor',2,',','.',true);
        $valor->addValidation('Valor', new TRequiredValidator);
        $desconto = new TNumeric('desconto',2,',','.',true);

        $portador = new TEntry('portador');
        $observacao = new TText('observacao');
        $baixa = new TEntry('baixa');
        $data_baixa = new TDate('data_baixa');
        $valor_pago = new TEntry('valor_pago');
        $valor_parcial = new TEntry('valor_parcial');
        $valor_real = new TEntry('valor_real');

        $replica = new TCombo('replica');
        $replica->addValidation('Replica ?', new TRequiredValidator);
        $combo_replica = array();
        $combo_replica['S'] = 'Sim - Divide o valor pelo nº de parcelas.';
        $combo_replica['N'] = 'Não - Mantem o valor pelo nº de parcelas.';
        //$combo_replica['I'] = 'Sim, com intervalo';
        $replica->addItems($combo_replica);

        $tipo_forma_pgto_id = new TDBCombo('tipo_forma_pgto_id','sample','TipoFormaPgto','id','nome');
        $tipo_forma_pgto_id->addValidation('Forma de Pagamento', new TRequiredValidator);

        $responsavel = new TEntry('responsavel');
        //$responsavel->addValidation('Responsável', new TRequiredValidator);

        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $cliente_id->setMask('{razao_social} - {cpf_cnpj} - {nome_fantasia}');
        $tipo_pgto_id = new TDBCombo('tipo_pgto_id', 'sample', 'TipoPgto', 'id', 'nome');
        $tipo_pgto_id->addValidation('Tipo de Pagamento', new TRequiredValidator);

        $pc_receita_id = new TDBSeekButton('pc_receita_id', 'sample', $this->form->getName(), 'PcReceita', 'nome', 'pc_receita_id', 'pc_receita_nome');
        $pc_receita_id->addValidation('Plano de Contas', new TRequiredValidator);
        $pc_receita_nome = new TEntry('pc_receita_nome');
        //$pc_receita_id->setAuxiliar($pc_receita_nome);
        $pc_receita_nome->setEditable(FALSE);

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);

        $relatorio_customizado_id = new TDBCombo('relatorio_customizado_id', 'sample', 'RelatorioCustomizado', 'id', 'nome');
        
        $gera_nfse = new TCombo('gera_nfse');
        $combo_rgera_nfse = array();
        $combo_rgera_nfse['S'] = 'Sim';
        $combo_rgera_nfse['N'] = 'Não';
        $gera_nfse->addItems($combo_rgera_nfse);

        $departamento_id = new TDBCombo('departamento_id', 'sample', 'Departamento', 'id', 'nome');
        $departamento_id->addValidation('Departamento', new TRequiredValidator);

        $centro_custo_id = new TDBCombo('centro_custo_id','sample','CentroCusto','id','nome','nome');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
            [ new TLabel('Unidade'), $unit_id ],
            [ new TLabel('Competência'), $data_conta ],
            [ new TLabel('Vencimento'), $data_vencimento ],
            [ new TLabel('Usuário'), $user_id ]);
        $row->layout = ['col-sm-2','col-sm-3', 'col-sm-2', 'col-sm-2','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Tipo de Pagamento'), $tipo_pgto_id ],
            [ new TLabel('Forma de Pagamento'), $tipo_forma_pgto_id ],
            [ new TLabel('Nº Documento/REF'), $documento ],
            [ new TLabel('Responsável'), $responsavel ]);
        $row->layout = ['col-sm-3','col-sm-4','col-sm-2','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Conta bancária para a baixa'), $conta_bancaria_id ],
                                       [ new TLabel('Cliente'), $cliente_id ]);
        $row->layout = ['col-sm-6','col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Plano de Contas'), $pc_receita_id ],
            [ new TLabel('Nome do Plano'), $pc_receita_nome ],
            [ new TLabel('Departamento'), $departamento_id ]
        
        );
        $row->layout = ['col-sm-2','col-sm-6','col-sm-4'];

        $row = $this->form->addFields(  [ ],
                                        [ new TLabel('Centro de Custo'), $centro_custo_id ]
        
        );
        $row->layout = ['col-sm-8','col-sm-4'];

        $row = $this->form->addFields([ new TLabel('Descrição do título'), $descricao ]);
        $row->layout = ['col-sm-12'];

        $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'nome_fantasia');
        //$fornecedor_id->addValidation('Fornecedor', new TRequiredValidator);

        $row = $this->form->addFields( [ new TLabel('Modelo de Recibo'), $relatorio_customizado_id ],
                                       [  ]
        );
        $row->layout = ['col-sm-4','col-sm-8'];


        $this->form->addContent( ['<h4>Valor</h4><hr style="height:2px; border:none; color:#bcbcbc; background-color:#bcbcbc; margin-top: 0px; margin-bottom: 0px;">'] );


        $gerar_boleto = new TCombo('gerar_boleto');
        $combo_rgerar_boleto = array();
        $combo_rgerar_boleto['S'] = 'Sim';
        $combo_rgerar_boleto['N'] = 'Não';
        $gerar_boleto->addItems($combo_rgerar_boleto);
        //$gerar_boleto->setValue('N');

        $juridico = new TCombo('juridico');
        $combo_juridico = array();
        $combo_juridico['S'] = 'Sim';
        $combo_juridico['N'] = 'Não';
        $juridico->addItems($combo_juridico);

        $row = $this->form->addFields( [ new TLabel('Valor'), $valor ],
            [ new TLabel('Divide valor?'), $replica]
        );
        $row->layout = ['col-sm-2','col-sm-4', 'col-sm-2','col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Observação'), $observacao ]);
        $row->layout = ['col-sm-12'];

        $arquivo = new TMultiFile('arquivo');
        $arquivo->setAllowedExtensions( ['xls', 'png', 'jpg', 'jpeg','pdf'] );
        $arquivo->enableFileHandling();
        //$arquivo->enableImageGallery();
        $arquivo->enablePopover('Preview', '<img style="max-width:300px" src="download.php?file={file_name}">');
        
        $row = $this->form->addFields( [ new TLabel('<b>Anexar um ou mais arquivos</b><p style="color:red;">Obs: extenções aceitas: Xls, Pdf, Jpg e Jpeg.</p>'), $arquivo ]);
        $row->layout = ['col-sm-6'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';

        if (isset($param['id']) && $param['id']) {
            try {
                $key = $param['id'];
                TTransaction::open('sample');

                $cr = new ContaReceber($key);

                if(!empty($cr->baixa == 'S')){

                    $data_conta->setEditable(false);
                    $data_vencimento->setEditable(false);
                    $tipo_forma_pgto_id->setEditable(false);
                    $tipo_pgto_id->setEditable(false);
                    $user_id->setEditable(false);
                    $conta_bancaria_id->setEditable(false);
                    $cliente_id->setEditable(false);
                    $pc_receita_id->setEditable(false);
                    $valor->setEditable(false);
                    $replica->setEditable(false);
                }

                if (!!$cr->cliente_contrato_id) {
                    $gerar_boleto->setEditable(false);
                }

                TTransaction::close();
            } catch (Exception $exception) {
                TTransaction::rollback();
            }
        }

        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction([$this,'onExit']), 'fa:arrow-circle-left red');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', 'ContaReceberList'));
        $container->add($this->form);

        parent::add($container);
    }


    /**
     * Save form data
     * @param $param Request
     */
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
            $valorForm = number_format($data->valor, 2, '.', '');

            if (!$data->id)
            {
                
//                var_dump($data->data_vencimento);
                $forma = new TipoFormaPgto($data->tipo_forma_pgto_id);
                $formaPagamento = new FormaPagamento($valorForm,$forma->regra,$data->data_vencimento);

                if($forma->parcela > 1){

                    $count = 1;
                    for($i = 0; $i < $formaPagamento->numero_parcelas; ++$i) {

//                        var_dump($formaPagamento);

                        $object = new ContaReceber();
                        $object->data_vencimento = $formaPagamento->vencimentobd[$i]; 

                        if($data->replica == "S"){
                           $object->valor = $formaPagamento->valor_parcela;
                           $object->valor_real = $formaPagamento->valor_parcela; 
                           $object->valor_pago = $formaPagamento->valor_parcela; 
                        }else{
                           $object->valor = $valorForm;
                           $object->valor_real = $valorForm; 
                           $object->valor_pago = $valorForm;
                        }

                        $object->baixa = 'N'; 
                        $object->replica = 'N'; 
                        $object->parcelas = $count++;
                        $object->nparcelas = $formaPagamento->numero_parcelas;
                        $object->unit_id = $data->unit_id;
                        $object->cliente_id = $data->cliente_id;
                        $object->tipo_pgto_id = $data->tipo_pgto_id;
                        $object->tipo_forma_pgto_id = $data->tipo_forma_pgto_id;
                        $object->user_id = $data->user_id;
                        $object->pc_receita_id = $data->pc_receita_id;
                        $object->pc_receita_nome = $data->pc_receita_nome;
                        $object->conta_bancaria_id = $data->conta_bancaria_id;
                        $object->responsavel = $data->responsavel;
                        $object->documento = $data->documento;
                        $object->descricao = $data->descricao;
                        /*$object->multa = $data->multa || 0;
                        $object->juros = $data->juros || 0;
                        $object->desconto = $data->desconto || 0;*/
                        $object->observacao = $data->observacao;
                        $object->gerar_boleto = $data->gerar_boleto;
                        $object->store();

                        $boleto_id = $this->onGerarBoletoChecked($object);

                        if ($boleto_id) {
                            $object->boleto_id = $boleto_id;
                        }

                        $this->saveFiles($object, $data, 'arquivo', 'files/documents', 'ContaReceberArquivo', 'arquivo', 'conta_receber_id'); 

                    }

                }else{

                    $object = new ContaReceber;  // create an empty object
                    $object->fromArray( (array) $data); // load the object with data
                    
                    $this->saveFiles($object, $data, 'arquivo', 'files/documents', 'ContaReceberArquivo', 'arquivo', 'conta_receber_id'); 

                    $object->valor_real = $valorForm;
                    $object->valor_pago = $valorForm;
                    $object->store(); // save the object

                    $boleto_id = $this->onGerarBoletoChecked($object);

                    if ($boleto_id) {
                        $object->boleto_id = $boleto_id;
                    }

                }

            }else{

                $object = new ContaReceber;  // create an empty object
                $object->fromArray( (array) $data); // load the object with data
                
                $this->saveFiles($object, $data, 'arquivo', 'files/documents', 'ContaReceberArquivo', 'arquivo', 'conta_receber_id'); 
                
                $object->valor_pago = $valorForm;
                $object->valor_real = $valorForm;
                $object->store(); // save the object

                $boleto_id = $this->onGerarBoletoChecked($object);

                if ($boleto_id) {
                    $object->boleto_id = $boleto_id;
                }

            }

//             get the generated id
//            $data->id = $object->id;

            $this->form->setData($object); // fill form data


            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([$this, 'onEdit'],['key' => $object->id]);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $pos_action);
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
    

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('sample'); // open a transaction
                $object = new ContaReceber($key); // instantiates the Active Record
                $object->arquivo = ContaReceberArquivo::where('conta_receber_id', '=', $param['key'])->getIndexedArray('id', 'arquivo');
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

    public function onExit()
    {
        $result = TSession::getValue('ContaReceberList');

        $query = isset($result['query']) ? $result['query'] : null;

        if (!empty($query))
        {
            TScript::create("
                Adianti.waitMessage = 'Listando...';__adianti_post_data('ContaReceberForm', '$query');                                 
        ");
        }
    }
}
