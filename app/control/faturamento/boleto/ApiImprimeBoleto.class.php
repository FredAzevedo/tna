<?php
class ApiImprimeBoleto extends TWindow
{
    public function __construct($param)
    {
        parent::__construct();
        parent::setTitle('Boleto Bancário');
        parent::setSize(0.8, 0.8);
        
    }

    public function onSavePDF($param)
    {

        $idboleto = $param['id'];
        
        $iframe = new TElement('iframe');
        $iframe->id = "iframe_external";
        TTransaction::open('sample');
        $linkBoletoGerado = new BoletoApi($idboleto);
        $iframe->src = $linkBoletoGerado->linkBoleto;
        TTransaction::close();
        $iframe->frameborder = "0";
        $iframe->scrolling = "yes";
        $iframe->width = "100%";
        $iframe->height = "600px";

        parent::add($iframe);

    }
}