<?php
/**
 * EstoqueForm Registration
 * @author  <your name here>
 */
class EstoqueForm extends TPage
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
        $this->setActiveRecord('Estoque');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Estoque');
        $this->form->setFormTitle('Estoque');
        

        // create the form fields
        $id = new TEntry('id');
        $unit_id = new TEntry('unit_id');
        $unit_produto = new TCriteria();
        $unit_produto->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $produto_id = new TDBUniqueSearch('produto_id', 'sample', 'Produto', 'id','cod_referencia','cod_referencia', $unit_produto);
        $produto_id->setMask('{cod_referencia} - {nome_produto}');
        $produto_id->addValidation('Produto', new TRequiredValidator);
        $local = new TEntry('local');
        $saldo = new TEntry('saldo');
        $created_at = new TEntry('created_at');
        $updated_at = new TEntry('updated_at');


        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Unit Id') ], [ $unit_id ] );
        $this->form->addFields( [ new TLabel('Produto Id') ], [ $produto_id ] );
        $this->form->addFields( [ new TLabel('Local') ], [ $local ] );
        $this->form->addFields( [ new TLabel('Saldo') ], [ $saldo ] );
        $this->form->addFields( [ new TLabel('Created At') ], [ $created_at ] );
        $this->form->addFields( [ new TLabel('Updated At') ], [ $updated_at ] );



        // set sizes
        $id->setSize('100%');
        $unit_id->setSize('100%');
        $produto_id->setSize('100%');
        $local->setSize('100%');
        $saldo->setSize('100%');
        $created_at->setSize('100%');
        $updated_at->setSize('100%');


        
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
        $container->style = 'width: 90%';
        // ////$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
}
