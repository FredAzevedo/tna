<?php
/**
 * ComissaoUserForm Form
 * @author  Fred Azv.
 */
class ComissaoUserForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ComissaoUser');
        $this->form->setFormTitle('Comissão de Usuário');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');

        $data_faturamento = new TDate('data_faturamento');
        $data_faturamento->setDatabaseMask('yyyy-mm-dd');
        $data_faturamento->setMask('dd/mm/yyyy');

        $valor_faturamento = new TNumeric('valor_faturamento',2,',','.',true);
        $taxa_comissao = new TNumeric('taxa_comissao',2,',','.',true);
        $valor_comissao = new TNumeric('valor_comissao',2,',','.',true);
        $pago = new TEntry('pago');
        
        $tipo = new TCombo('tipo');
        $combo_tipos = array();
        $combo_tipos['P'] = '(%) Em Porcentagem';
        $combo_tipos['D'] = '(R$) Em Dinheiro)';
        $tipo->addItems($combo_tipos);

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');

        $descricao = new TEntry('descricao');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Data Faturado'), $data_faturamento ],
                                       [ new TLabel('Valor Faturado'), $valor_faturamento ],
                                       [ new TLabel('Taxa de Comissão'), $taxa_comissao ],
                                       [ new TLabel('Valor da Comissão'), $valor_comissao ],
                                       [ new TLabel('Foi Pago?'), $pago ]
        );              
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Tipo'), $tipo ],    
                                       [ new TLabel('Usuário comissionado'), $user_id ],
                                       [ new TLabel('Unidade pertecente'), $unit_id ]
        );              
        $row->layout = ['col-sm-4', 'col-sm-5', 'col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Descrição'), $descricao ]);              
        $row->layout = ['col-sm-12'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction(['ComissaoUserList','onReload']), 'fa:arrow-circle-left red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ComissaoUserList'));
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
            
            $object = new ComissaoUser;  // create an empty object
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
                $object = new ComissaoUser($key); // instantiates the Active Record
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
