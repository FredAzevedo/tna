<?php
/**
 * ImportarOfxForm
 * @author  Fred Azv.
 */
class ImportarOfxForm extends TPage
{
    protected $form; // form

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_PcReceita');
        $this->form->setFormTitle('Importar OFX');
        $this->form->setFieldSizes('100%');
        
        $filename = new TFile('filename');
        $filename->setAllowedExtensions( ['ofx'] );
        
        $row = $this->form->addFields( [ new TLabel('Importar Arquivo OFX'), $filename ]

        );
        $row->layout = ['col-sm-12'];   

        $btn = $this->form->addAction('Processar Arquivo', new TAction([$this, 'onProcessarOFX']), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        /*
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction([$this,'onReload']), 'fa:angle-double-left');*/

        
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ImportarOfxForm'));
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onProcessarOFX( $param )
    {

        try
        {
            $data = $this->form->getData(); // get form data as array
            $this->form->validate(); // validate form data
            TSession::setValue('system_upload_file_ofx', $data->filename);
            
            AdiantiCoreApplication::loadPage('ImportarOfx');

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
        }

    }

}
