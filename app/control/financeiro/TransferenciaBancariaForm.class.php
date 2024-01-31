<?php
/**
 * TransferenciaBancariaForm Form
 * @author  <your name here>
 */
class TransferenciaBancariaForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_TransferenciaBancaria');
        $this->form->setFormTitle('Transferência Bancária');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');

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

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));

        $conta_bancaria_debito_id = new TDBCombo('conta_bancaria_debito_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_debito_id->addValidation('Conta Bancária', new TRequiredValidator);

        $conta_bancaria_credito_id = new TDBCombo('conta_bancaria_credito_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_credito_id->addValidation('Conta Bancária', new TRequiredValidator);

        $data_lancamento = new TDate('data_lancamento');
        $data_lancamento->setValue(date('d/m/Y'));
        $data_lancamento->setDatabaseMask('yyyy-mm-dd');
        $data_lancamento->setMask('dd/mm/yyyy');
        $data_lancamento->addValidation('Data de Lançamento', new TRequiredValidator);

        $data_transferencia = new TDate('data_transferencia');
        $data_transferencia->setValue(date('d/m/Y'));
        $data_transferencia->setDatabaseMask('yyyy-mm-dd');
        $data_transferencia->setMask('dd/mm/yyyy');
        $data_transferencia->addValidation('Data da Transferência', new TRequiredValidator);

        $data_baixa = new TDate('data_baixa');
        $data_baixa->setValue(date('d/m/Y'));
        $data_baixa->setDatabaseMask('yyyy-mm-dd');
        $data_baixa->setMask('dd/mm/yyyy');
        $data_baixa->addValidation('Data da Baixa', new TRequiredValidator);

        $valor = new TNumeric('valor',2,',','.',true);
        $valor->addValidation('Valor', new TRequiredValidator);

        $pc_despesa_id = new TDBSeekButton('pc_despesa_id', 'sample', $this->form->getName(), 'PcDespesa', 'nome', 'pc_despesa_id', 'pc_despesa_nome');
        $pc_despesa_id->addValidation('Plano de Contas', new TRequiredValidator);
        $pc_despesa_nome = new TEntry('pc_despesa_nome');
        $pc_despesa_nome->setEditable(FALSE);

        $pc_receita_id = new TDBSeekButton('pc_receita_id', 'sample', $this->form->getName(), 'PcReceita', 'nome', 'pc_receita_id', 'pc_receita_nome');
        $pc_receita_id->addValidation('Plano de Contas', new TRequiredValidator);
        $pc_receita_nome = new TEntry('pc_receita_nome');
        $pc_receita_nome->setEditable(FALSE);

        
        $observacao = new TText('observacao');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Usuário'), $user_id ],
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Lançamento'), $data_lancamento ],
                                       [ new TLabel('Transferência'), $data_transferencia ],
                                       [ new TLabel('Data p/ Baixa'), $data_baixa ]

        );
        $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2'];

        $this->form->addContent( ['<h4><b>Débito</b></h4><hr>'] );
        $row = $this->form->addFields( [ new TLabel('Conta a ser Debitada'), $conta_bancaria_debito_id ],
                                       [ new TLabel('Valor'), $valor ]);
        $row->layout = ['col-sm-10','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Código'), $pc_despesa_id ],
                                       [ new TLabel('Plano de Contas Despesas'), $pc_despesa_nome ]);
        $row->layout = ['col-sm-2','col-sm-10'];

        $this->form->addContent( ['<h4><b>Crédito</b></h4><hr>'] );
        $row = $this->form->addFields( [ new TLabel('Conta a ser Creditada'), $conta_bancaria_credito_id ]);
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Código'), $pc_receita_id ],
                                       [ new TLabel('Plano de Contas Receitas'), $pc_receita_nome ]);
        $row->layout = ['col-sm-2','col-sm-10'];

        $row = $this->form->addFields( [ new TLabel('Observações'), $observacao ]);
        $row->layout = ['col-sm-12'];


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction( ['TransferenciaBancariaList', 'onReload'] ), 'fa:angle-double-left');
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', 'TransferenciaBancariaList'));
        $container->add($this->form);
        
        parent::add($container);
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
            
            $object = new TransferenciaBancaria;  // create an empty object
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
                $object = new TransferenciaBancaria($key); // instantiates the Active Record
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
}
