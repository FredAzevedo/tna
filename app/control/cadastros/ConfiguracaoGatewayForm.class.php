<?php
/**
 * ConfiguracaoGateway Form
 * @author  João Victor Marques de Oliveira - jvomarques@gmail.com
 */
class ConfiguracaoGatewayForm extends TPage
{
    protected $form; 
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_configuracao_gateway');
        $this->form->setFormTitle('Novo Gateway');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        $id->setEditable(FALSE);

        $nome_gateway = new TEntry('nome_gateway');
        

        $email = new TEntry('email');
        $token = new TEntry('token');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                        [ new TLabel('Nome do Gateway'), $nome_gateway ],
                                        [ new TLabel('E-mail'), $email ],
                                        [ new TLabel('Token'), $token ]);
        $row->layout = ['col-sm-1','col-sm-2','col-sm-4', 'col-sm-5'];
        
        if (!empty($nome_gateway))
            $nome_gateway->setEditable(FALSE);
        
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction(['ConfiguracaoGatewayList','onReload']), 'fa:angle-double-left');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'PlanoList'));
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
            

            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new ConfiguracaoGateway;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            $this->onEdit(array('key'=> $data->id)); 

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
                $gateway = new ConfiguracaoGateway($key); // instantiates the Active Record

                
                $this->form->setData($gateway); // fill the form



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

    public function onReload($param)
    {
        $this->loaded = TRUE;
    }

    
}
