<?php
/**
 * ReciboForm Form
 * @author  Fred Azv.
 */
class ReciboForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Recibo');
        $this->form->setFormTitle('Recibo');
        $this->form->setFieldSizes('100%');


        // create the form fields
        $id = new TEntry('id');
        $data_conta = new TDate('data_conta');
        $data_conta->setValue(date('d/m/Y'));
        $data_conta->addValidation('Competência', new TRequiredValidator);
        $data_conta->setDatabaseMask('yyyy-mm-dd');
        $data_conta->setMask('dd/mm/yyyy');
        $descricao = new TEntry('descricao');
        $valor = new TNumeric('valor',2,',','.',true);
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $relatorio_customizado_id = new TDBCombo('relatorio_customizado_id', 'sample', 'RelatorioCustomizado', 'id', 'nome');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);
        
        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userunitid'));
        $user_id->addValidation('Usuário', new TRequiredValidator);
        //$user_id->setEditable(FALSE);
        
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Usuário'), $user_id ],
                                       [ new TLabel('Competência'), $data_conta ]
        );
        $row->layout = ['col-sm-2','col-sm-4', 'col-sm-4', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Referente'), $descricao ],
                                       [ new TLabel('Valor'), $valor ]
        );
        $row->layout = ['col-sm-10','col-sm-2'];

        $tipo_forma_pgto_id = new TDBCombo('tipo_forma_pgto_id','sample','TipoFormaPgto','id','nome');
        $tipo_forma_pgto_id->addValidation('Forma de Pagamento', new TRequiredValidator);

        $tipo_pgto_id = new TDBCombo('tipo_pgto_id', 'sample', 'TipoPgto', 'id', 'nome');
        $tipo_pgto_id->addValidation('Tipo de Pagamento', new TRequiredValidator);

        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->addValidation('Data de Vencimento', new TRequiredValidator);
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');

        $row = $this->form->addFields( [ new TLabel('Modelo de Recibo'), $relatorio_customizado_id ],
                                       [ new TLabel('Tipo de Pagamento'), $tipo_pgto_id ],
                                       [ new TLabel('Forma de Pagamento'), $tipo_forma_pgto_id ],
                                       [ new TLabel('Vencimento'), $data_vencimento ]
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-2'];

        $pc_receita_id = new TDBSeekButton('pc_receita_id', 'sample', $this->form->getName(), 'PcReceita', 'nome', 'pc_receita_id', 'pc_receita_nome');
        $pc_receita_id->addValidation('Plano de Contas', new TRequiredValidator);
        $pc_receita_nome = new TEntry('pc_receita_nome');
        $pc_receita_nome->setEditable(FALSE);

        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');

        $row = $this->form->addFields( [ new TLabel('Cliente'), $cliente_id ]
        );
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Plano de Contas'), $pc_receita_id ],
            [ new TLabel('Nome do Plano'), $pc_receita_nome ]);
        $row->layout = ['col-sm-2','col-sm-10'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }        

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction([$this,'onExit']), 'fa:arrow-circle-left red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ReciboList'));
        $container->add($this->form);
        
        parent::add($container);
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample'); // open a transaction

            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array

            if (!$data->id)
            {
                $valorForm = number_format($data->valor, 2, '.', '');
//              var_dump($data->data_vencimento);
                $forma = new TipoFormaPgto($data->tipo_forma_pgto_id);
                $formaPagamento = new FormaPagamento($valorForm,$forma->regra,$data->data_vencimento);

                if($forma->parcela > 1){

                    $count = 1;
                    for($i = 0; $i < $formaPagamento->numero_parcelas; ++$i) {

//                      var_dump($formaPagamento);

                        $object = new ContaReceber();
                        $object->data_vencimento = $formaPagamento->vencimentobd[$i]; 

                        if($data->replica == "S"){
                           $object->valor = $formaPagamento->valor_parcela;
                           $object->valor_real = $formaPagamento->valor_parcela; 
                        }else{
                           $object->valor = $valorForm;
                           $object->valor_real = $valorForm; 
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
                        $object->observacao = $data->observacao;
                        $object->gerar_boleto = $data->gerar_boleto;
                        $object->store();

                        $boleto_id = $this->onGerarBoletoChecked($object);

                        if ($boleto_id) {
                            $object->boleto_id = $boleto_id;
                        }

                    }

                }else{

                    $object = new ContaReceber;  
                    $object->fromArray( (array) $data); 
                    $object->store();

                }

            }else{

                $object = new ContaReceber; 
                $object->fromArray( (array) $data);
                $object->store();

            }

//             get the generated id
//            $data->id = $object->id;

            $this->form->setData($object); 


            TTransaction::close();
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData( $this->form->getData() ); 
            TTransaction::rollback(); 
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
                $object = new ContaReceber($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form

                TTransaction::close(); // close the transaction
            }
            else
            {

                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onExit()
    {
        $result = TSession::getValue('ReciboList');

        $query = isset($result['query']) ? $result['query'] : null;

        if (!empty($query))
        {
            TScript::create("
                Adianti.waitMessage = 'Listando...';__adianti_post_data('ReciboForm', '$query');                                 
        ");
        }
    }
}
