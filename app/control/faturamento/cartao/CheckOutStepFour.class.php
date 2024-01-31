<?php

use Adianti\Widget\Form\THidden;

/**
 * PessoaFormWebFive
 * @author  Fred Azv.
 */

class CheckOutStepFour extends TPage
{
    protected $form;
    protected $detail_list;

    public function __construct()
    {   

        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_CheckOutStepFour');
        $this->form->addContent( [] );

        TPage::include_css('app/resources/public.css');

        $this->html = new THtmlRenderer('app/view/faturamento/confirmacaoSite.html');

        $msg_item = TSession::getValue('msg');
        $valor_item = TSession::getValue('valor');
        $pedido_numero_item = TSession::getValue('pedido_numero');
        $descricao_pagamento_item = TSession::getValue('descricao_pagamento');
        $autorizacao_item = TSession::getValue('autorizacao');

        $data = [];
        $data['msg'] = $msg_item;
        $data['valor'] = $valor_item;
        $data['pagamento'] = "Pagamento Recorrente.";
        $data['pedido_numero'] = $pedido_numero_item;
        $data['autorizacao'] = $autorizacao_item;
        $data['descricao_pagamento'] = $descricao_pagamento_item;

        $this->html->enableSection('main', $data);
        $this->form->add($this->html);

        $pagestep = new TPageStep;
        $pagestep->addItem('Escolha o seu Plano');
        $pagestep->addItem('Dados Principais');
        $pagestep->addItem('Pagamento');
        $pagestep->addItem('Confirmação');          
        $pagestep->style = 'margin-bottom: 2%; background-color: white;';

        $pagestep->select('Confirmação');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($pagestep);
        $container->add($this->form);
        parent::add($container);

    }

    public function onLoad()
    {
       
    }

}