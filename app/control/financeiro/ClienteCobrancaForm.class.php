<?php
/**
 * ClienteCobrancaForm Form
 * @author  Fred Azv.
 */
class ClienteCobrancaForm extends TPage
{
    protected $form; // form

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ClienteCobranca');
        $this->form->setFormTitle('Gerar histórico de Cobranças');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
        $user_id->setValue(TSession::getValue('userid'));
        $user_id->addValidation('Usuário', new TRequiredValidator);
        $user_id->setEditable(FALSE);

        $cliente_id = new TDBCombo('cliente_id', 'sample', 'Cliente', 'id', 'razao_social', 'razao_social');
        $cliente_id->setValue(TSession::getValue('clienteid'));
        $cliente_id->setEditable(FALSE);
        
        $descricao = new TText('descricao');
        $descricao->addValidation('Descrição do Atendimento', new TRequiredValidator);

        $status = new TCombo('status');
        $combo_status = [];
        $combo_status['S'] = 'Contato feito com sucesso';
        $combo_status['N'] = 'Contato sem sucesso';
        $status->addItems($combo_status);
        $status->addValidation('Status do Atendimento', new TRequiredValidator);

        $created_at = new TEntry('created_at');
        $update_at = new TEntry('update_at');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Usuário'), $user_id ],
                                       [ new TLabel('Status do Atendimento'), $status ]
        );
        $row->layout = ['col-sm-2', 'col-sm-6', 'col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Cliente'), $cliente_id ]
        );
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Descrição do Atendimento'), $descricao ]
        );
        $row->layout = ['col-sm-12'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
         
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction('Voltar para Listagem',  new TAction(['GestaoCobrancaList', 'onReload']), 'fa:eraser red');
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'GestaoCobrancaList'));
        $container->add($this->form);
        
        parent::add($container);
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample'); // open a transaction
            //TTransaction::setLogger(new TLoggerSTD);
            $cliente_id = $param['cliente_id'];
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new ClienteCobranca();  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            $cli = new Cliente($cliente_id);
            $cli->data_cobranca = date('Y-m-d');
            $cli->store();

            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data

            TTransaction::close(); // close the transaction
            
            $listagem = new TAction(['GestaoCobrancaList', 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'),$listagem);
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
                $object = new ClienteCobranca($key); // instantiates the Active Record
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
