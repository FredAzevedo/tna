<?php

use Adianti\Control\TWindow;
use Adianti\Validator\TMaxLengthValidator;
use Adianti\Validator\TNumericValidator;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Wrapper\BootstrapFormBuilder;
use Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi;

/**
 * Created by PhpStorm.
 * User: AndersonAndradede
 * Date: 08/06/2019
 * Time: 15:40
 */

class ContaBancariaBoletoSicredi extends TWindow {

    protected $form;
    protected $formname = 'form_sicoob_info';

    protected $banco;

    public function __construct() {
        parent::__construct();
        parent::setTitle('Dados para emissão do boleto Sicredi');
        parent::setSize(0.8, 0.8);

        $this->banco = new Sicredi();

        $this->form = new BootstrapFormBuilder();
        $this->form->setFormTitle('Conta Bancaria');
        $this->form->setFieldSizes('100%');

        $carteiras = $this->banco->getCarteiras();
        $carteiras = array_combine($carteiras, $carteiras);
        $carteira = new TCombo('carteira');
        $carteira->addItems($carteiras);

        $byte = new TEntry('byte');
        $byte->setMask('9');
        $byte->addValidation('Byte', new TMaxLengthValidator(), [1]);
        $byte->addValidation('Byte', new TNumericValidator());


        $posto = new TEntry('posto');
        $posto->setMask('99');

        $beneficiario = new TEntry('beneficiario');

        $carteira->setValue(TSession::getValue('boleto_carteira'));
        $byte->setValue(TSession::getValue('boleto_byte'));
        $posto->setValue(TSession::getValue('boleto_posto'));
        $beneficiario->setValue(TSession::getValue('boleto_beneficiario'));

        $row = $this->form->addFields(
            [new TLabel('Carteira'), $carteira],
            [new TLabel('Byte'), $byte],
            [new TLabel('Posto'), $posto],
            [new TLabel('Código beneficiário'), $beneficiario]
        );
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-3','col-sm-3'];

//        $this->form->addContent($carteiras);

        $btn = $this->form->addAction('Confirmar', new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';

        $container = new TVBox();
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);
    }

    public function onSave($param) {
        try {
            $this->form->validate();
            $data = $this->form->getData();

            TSession::setValue('boleto_carteira', $data->carteira);
            TSession::setValue('boleto_byte', $data->byte);
            TSession::setValue('boleto_posto', $data->posto);
            TSession::setValue('boleto_beneficiario', $data->beneficiario);

            self::closeWindow();

        } catch (Exception $e) {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    public function onEdit($param) {

    }

}