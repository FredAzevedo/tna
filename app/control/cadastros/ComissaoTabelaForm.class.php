<?php
/**
 * ComissaoTabelaForm Form
 * @author  <your name here>
 */
class ComissaoTabelaForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ComissaoTabela');
        $this->form->setFormTitle('ComissaoTabela');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        $descricao = new TEntry('descricao');

        $forma_comissao = new TCombo('forma_comissao');
        $combo_tipos['P'] = '(%) Porcentagem';
        $combo_tipos['D'] = '(R$) Dinheiro';
        $forma_comissao->addItems($combo_tipos);
        
        $valor_comissao = new TNumeric('valor_comissao', 2, ',', '.', true);
        $observacao = new TEntry('observacao');

        
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Descrição'), $descricao ],
                                       [ new TLabel('Forma Comissão'), $forma_comissao ],
                                       [ new TLabel('Valor Comissão'), $valor_comissao ]);
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-3', 'col-sm-3'];
        
        $row = $this->form->addFields( [ new TLabel('Observação'), $observacao ]);
        $row->layout = ['col-sm-6'];

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
        $this->form->addAction('Voltar', new TAction(['ComissaoTabelaList','onReload']), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ComissaoTabelaList'));
        $container->add($this->form);

        /*$container->adianti_target_container = 'ComissaoTabelaList';
        $container->adianti_target_title = 'Tabela de Comissão ';*/
        
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
            
            $object = new ComissaoTabela;  // create an empty object
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
                $object = new ComissaoTabela($key); // instantiates the Active Record
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
