<?php
/**
 * ReciboForm Form
 * @author  Fred Azevedo
 */
class ReciboFormBaixa extends TPage
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
        $this->form->setFormTitle('Baixa de Recibo');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->setValue(date('d/m/Y'));
        $data_vencimento->addValidation('Vencimento', new TRequiredValidator);
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');
        $descricao = new TEntry('descricao');
        $valor = new TNumeric('valor',2,',','.',true);
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userid'));
        $user_id->addValidation('Usuário', new TRequiredValidator);
        //$user_id->setEditable(FALSE);

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Usuário'), $user_id ]
        );
        $row->layout = ['col-sm-2','col-sm-4', 'col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Referente'), $descricao ]
                                       
        );
        $row->layout = ['col-sm-12'];

        $tipo_forma_pgto_id = new TDBCombo('tipo_forma_pgto_id','sample','TipoFormaPgto','id','nome');
        $tipo_forma_pgto_id->addValidation('Forma de Pagamento', new TRequiredValidator);

        $tipo_pgto_id = new TDBCombo('tipo_pgto_id', 'sample', 'TipoPgto', 'id', 'nome');
        $tipo_pgto_id->addValidation('Tipo de Pagamento', new TRequiredValidator);

        $row = $this->form->addFields( [ new TLabel('Tipo de Pagamento'), $tipo_pgto_id ],
                                       [ new TLabel('Forma de Pagamento'), $tipo_forma_pgto_id ],
                                       [ new TLabel('Vencimento'), $data_vencimento ],
                                       [ new TLabel('Valor'), $valor ]
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-2','col-sm-2'];

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
        $this->form->addAction('Baixar Recibo',  new TAction([$this, 'onBaixar']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction([$this,'onExit']), 'fa:arrow-circle-left red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ReciboList'));
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onBaixar( $param ){

    
        try
        {
            

            TTransaction::open('sample'); // open a transaction

            //TTransaction::setLogger(new TLoggerSTD); // standard output


            $recibo = new Recibo($param['id']);
            $descricao = $recibo->descricao;
            $documento = "RECIBO Nº ".$recibo->id;
            $dataVencimento = $recibo->data_vencimento;
            $valoR = $recibo->valor;
            $unitId = $recibo->unit_id;
            $tipoPgto_id = $recibo->tipo_pgto_id;
            $tipoForma_pgto_id = $recibo->tipo_forma_pgto_id;
            $userId = $recibo->user_id;
            $pcReceita_id = $recibo->pc_receita_id;
            $pcReceita_nome = $recibo->pc_receita_nome;
            $cliente = $recibo->cliente_id;
            $recibo->status = "B";
            $recibo->store();

            $ContasReceber = new ContaReceber();
            $ContasReceber->data_conta = date('Y-m-d');     
            $ContasReceber->descricao = $descricao; 
            $ContasReceber->documento = $documento; //$numero; //COLOCAR O NUMERO ÚNICO DO CARNE
            //$ContasReceber->data_vencimento = $vencimentoCadaParcela; 
            //$ContasReceber->valor = $formaPagamento->valor_parcela;valoR
            $ContasReceber->data_vencimento = $dataVencimento;
            $ContasReceber->valor = $valoR;
            $ContasReceber->baixa = 'N'; 
            $ContasReceber->parcelas = '1';
            $ContasReceber->nparcelas = '1'; 
            $ContasReceber->replica = 'N'; 
            $ContasReceber->unit_id = $unitId; 
            $ContasReceber->cliente_id = $cliente;
            $ContasReceber->tipo_pgto_id = $tipoPgto_id;
            $ContasReceber->tipo_forma_pgto_id = $tipoForma_pgto_id;
            $ContasReceber->user_id = $userId;
            $ContasReceber->pc_receita_id = $pcReceita_id;
            $ContasReceber->pc_receita_nome = $pcReceita_nome;
            $ContasReceber->conta_bancaria_id = 1;
            $ContasReceber->cliente_contrato_id = 1;
            $ContasReceber->gerar_boleto = 'N';
            $ContasReceber->store();

            $pos_action = new TAction(['ReciboList', 'onReload']);
            new TMessage('info', 'Baixa realizada com Sucesso!', $pos_action);

            TTransaction::close(); 

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }

    }

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
            
            $object = new Recibo;  // create an empty object
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
                $object = new Recibo($key); // instantiates the Active Record
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
