<?php
/**
 * PcReceitaForm Form
 * @author  <your name here>
 */
class PcReceitaForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        parent::setTargetContainer('adianti_right_panel');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_PcReceita');
        $this->form->setFormTitle('Plano de Contas Receitas');
        $this->form->setFieldSizes('100%');
        
// create the form fields
        $id = new TEntry('id');
        $nivel1 = new TEntry('nivel1');
        $nivel2 = new TEntry('nivel2');
        $nivel3 = new TEntry('nivel3');
        $nivel4 = new TEntry('nivel4');
        $nome = new TEntry('nome');

        $Scodigo = new TDBSeekButton('Scodigo', 'sample', $this->form->getName(), 'CodigoServicos', 'descricao', 'Scodigo', 'Sdiscriminacao');
        $Sdiscriminacao = new TEntry('Sdiscriminacao');

        $Scnae = new TEntry('Scnae');

        /*$id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);*/

        $row = $this->form->addFields( [ new TLabel('ID'), $id ]);
        $row->layout = ['col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('1º Nível (Mãe)'), $nivel1 ]);
        $row->layout = ['col-sm'];
        $row = $this->form->addFields( [ new TLabel('2º Nível (Mãe)'), $nivel2 ]);
        $row->layout = ['col-sm'];
        $row = $this->form->addFields( [ new TLabel('3º Nível (Mãe)'), $nivel3 ]);
        $row->layout = ['col-sm'];
        $row = $this->form->addFields( [ new TLabel('4º Nível (Mãe)'), $nivel4 ]);
        $row->layout = ['col-sm'];

        $row = $this->form->addFields( [ new TLabel('Nome do Plano (Filho)'), $nome ]);
        $row->layout = ['col-sm-6'];
        
        $row = $this->form->addFields( [ new TLabel('Cód. Serviço'), $Scodigo ],    
                                       [ new TLabel('Descrição do Serviço'), $Sdiscriminacao ],
                                       [ new TLabel('CNAE do Serviço'), $Scnae ]
        );
        $row->layout = ['col-sm-2','col-sm-8','col-sm-2'];

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
        $this->form->addAction('Fechar', new TAction([$this,'onReload']), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //////$container->add(new TXMLBreadCrumb('menu.xml', 'PlanoDeContas'));
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
            
            $object = new PcReceita;  // create an empty object
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
                $object = new PcReceita($key); // instantiates the Active Record
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

    public function onReload()
    {
        AdiantiCoreApplication::loadPage('PlanoDeContas', 'onReload');
    }
}
