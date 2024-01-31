<?php

class WelcomeView extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        $html1 = new THtmlRenderer('app/resources/system_welcome_pt.html');
        

        // replace the main section variables
        $html1->enableSection('main', array());
        
        $panel1 = new TPanelGroup('Bem vindo(a)!');
        $panel1->add($html1);

        $vbox = TVBox::pack($panel1);
        $vbox->style = 'display:block; width: 100%';
        
        // add the template to the page
        parent::add( $vbox );
    }
}
