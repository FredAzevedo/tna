<?php
/**
 * ImportarXmlCompraForm
 * @author  Fred Azv.
 */
class ImportarXmlCompraForm extends TPage
{
    protected $form; // form

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ImportarXmlCompraForm');
        $this->form->setFormTitle('Importar XML de Compra');
        $this->form->setFieldSizes('100%');
        
        $filename = new TFile('filename');
        //$filename->setAllowedExtensions( ['mxl'] );
        
        $row = $this->form->addFields( [ new TLabel('Importar Arquivo XML'), $filename ]

        );
        $row->layout = ['col-sm-12'];   

        $btn = $this->form->addAction('Processar Arquivo', new TAction([$this, 'onProcessarOFX']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        /*
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction([$this,'onReload']), 'fa:angle-double-left');*/

        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ImportarXmlCompraForm'));
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onProcessarOFX( $param )
    {

        try
        {
            $data = $this->form->getData(); // get form data as array
            $this->form->validate(); // validate form data
            TSession::setValue('system_upload_file_xml', $data->filename);
            
            $xml = new XmlCompra($data->filename);
            //return $xml;
            AdiantiCoreApplication::loadPage('NfeEntradaList');

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
        }

    }

}
