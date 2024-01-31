<?php
/**
 * LimiteCreditoForm Registration
 * @author  <your name here>
 */
class LimiteCreditoForm extends TPage
{
    protected $form; // form
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('sample');              // defines the database
        $this->setActiveRecord('LimiteCredito');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_LimiteCredito');
        $this->form->setFormTitle('Limite de Crédito');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        $unit_id = $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade');
        $unit_id->addValidation('Unidade', new TRequiredValidator);

        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userid'));
        $user_id->addValidation('Usuário', new TRequiredValidator);

        $historico = new TEntry('historico');
        $historico->addValidation('Histórico', new TRequiredValidator);

        $tipo = new TCombo('tipo');
        $combo_tipo = array();
        $combo_tipo['E'] = 'Creditar valor';
        $combo_tipo['S'] = 'Debitar valor';
        $tipo->addItems($combo_tipo);
        $tipo->setValue('E');
        $tipo->addValidation('Tipo', new TRequiredValidator);

        $tipo->setChangeAction( new TAction( array($this, 'onChangeRadio')) );
        self::onChangeRadio( array('tipo'=>'E') );

        $credito = new TNumeric('credito',2,',','.',true);
        $debito = new TNumeric('debito',2,',','.',true);

        $saldo = new TEntry('saldo');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Usuário'), $user_id ]
        );
        $row->layout = ['col-sm-2','col-sm-5', 'col-sm-5'];

        $row = $this->form->addFields( [ new TLabel('Tipo'), $tipo ],
                                       [ new TLabel('Histórico'), $historico ]
        );
        $row->layout = ['col-sm-2','col-sm-10'];

        $row = $this->form->addFields( [ new TLabel('Crédito'), $credito ],
                                       [ new TLabel('Débito'), $debito ]

        );
        $row->layout = ['col-sm-2','col-sm-2'];

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
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // ////$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    public static function onChangeRadio($param)
    {
        if ($param['tipo'] == 'E')
        {
            
            TEntry::enableField('form_LimiteCredito', 'credito');
            TEntry::disableField('form_LimiteCredito', 'debito');
            TEntry::clearField('form_LimiteCredito', 'debito');

        }
        else
        {
            TEntry::disableField('form_LimiteCredito', 'credito');
            TEntry::enableField('form_LimiteCredito', 'debito');
            TEntry::clearField('form_LimiteCredito', 'credito');

        }
    }
}
