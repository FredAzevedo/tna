<?php
class ButtonExpand
{ 
    
    public static function expandButton($class)
    {
        $form = new BootstrapFormBuilder($class);
        $botao = $form->addHeaderActionLink( 'Expandir',  new TAction(['ButtonExpand', 'onOpenClose'], ['register_state' => 'false']), 'fa:search' );
        $botao->class = "btn btn-info btn-sm";
        $botao->id = 'custom-id-botao';
        TPage::include_js('app/resources/ExpandButton.js');

        return $botao;

    }
    public static function onOpenClose(){}
}