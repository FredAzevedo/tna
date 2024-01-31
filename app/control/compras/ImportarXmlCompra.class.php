<?php
/**
 * ImportarXmlCompra Form
 * @author  Fred Azv.
 */
class ImportarXmlCompra extends TPage
{
    protected $form; // form

    public function __construct( $param )
    {
        parent::__construct();

        $this->html = new THtmlRenderer('app/view/nfe/nfe.html');

        $file = TSession::getValue('system_upload_file_ofx');

        $replace = array();

        $this->html->enableSection('main', $replace);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ImportarXmlCompraForm'));
        $container->add($this->html);
        
        parent::add($container);

    }

}
