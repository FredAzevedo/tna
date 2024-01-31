<?php
/**
 * CentroCustoForm Master/Detail
 * @author  Fred Azv.
 */
class CentroCustoForm extends TPage
{
    protected $form;
    protected $fieldlist;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($param)
    {
        parent::__construct($param);
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_CentroCusto');
        $this->form->setFormTitle('CentroCusto');
        
        $this->fieldlist = new TFieldList;
        $this->fieldlist->width = '100%';
        $this->fieldlist->enableSorting();
        
        // add field list to the form
        $this->form->addContent( [$this->fieldlist] );
        
        $id = new TEntry('list_id[]');
        $nome = new TEntry('list_nome[]');

        $id->setSize('100%');
        $nome->setSize('100%');

        $this->fieldlist->addField( '<b>Id</b>', $id);
        $this->fieldlist->addField( '<b>Nome</b>', $nome);

        $this->form->addField($id);
        $this->form->addField($nome);
        
        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addCloneAction();
        
        // create an action button (save)
        $this->form->addAction( 'Save', new TAction([$this, 'onSave']), 'fa:save blue');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Clear form
     */
    public function onClear($param)
    {
    }
    
    /**
     * Save the CentroCusto
     */
    public static function onSave($param)
    {
        try
        {
            TTransaction::open('sample');
            
            if( !empty($param['list_id']) AND is_array($param['list_id']) )
            {
                foreach( $param['list_id'] as $row => $id)
                {
                    if (!empty($id))
                    {
                        $detail = new CentroCusto;
                        $detail->id = $param['list_id'][$row];
                        $detail->nome = $param['list_nome'][$row];
                        $detail->store();
                    }
                }
            }
            
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
