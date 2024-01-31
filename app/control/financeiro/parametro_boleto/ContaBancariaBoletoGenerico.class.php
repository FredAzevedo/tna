<?php

use Adianti\Control\TWindow;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi;

/**
 * Created by PhpStorm.
 * User: AndersonAndradede
 * Date: 13/06/2019
 * Time: 00:38
 */

class ContaBancariaBoletoGenerico extends TWindow {

    protected $form;
    protected $formname = 'form_generico_info';

    protected $banco;

    public function __construct() {
        parent::__construct();
        parent::setTitle('Dados para emissão do boleto');
        parent::setSize(0.8, 0.8);

        $this->banco = new Sicredi();

        $this->form = new BootstrapFormBuilder();
        $this->form->setFormTitle('Conta Bancaria');
        $this->form->setFieldSizes('100%');

        $carteira = new TEntry('carteira');

        $codigoCliente = new TEntry('codigoCliente');
        $codigoCliente->setMaxLength(6);

        $convenio = new TEntry('convenio');


        $variacaoCarteira = new TEntry('variacaoCarteira');
        $variacaoCarteira->setMaxLength(3);


        $cip = new TEntry('cip');
        $cip->setMaxLength(3);
//        $cip->setValue('000'); //valor padrao

        $campo_range = new TEntry('campo_range');
        $campo_range->setMaxLength(5);

        $contaDv = new TEntry('contaDv');
        $contaDv->setMaxLength(1);

        $byte = new TEntry('byte');
        $byte->setMask('9');
        $byte->addValidation('Byte', new TMaxLengthValidator(), [1]);
        $byte->addValidation('Byte', new TNumericValidator());


        $posto = new TEntry('posto');
        $posto->setMask('99');


        $carteira->setValue(TSession::getValue('boleto_carteira'));
        $codigoCliente->setValue(TSession::getValue('boleto_codigoCliente'));
        $convenio->setValue(TSession::getValue('boleto_convenio'));
        $variacaoCarteira->setValue(TSession::getValue('boleto_variacaoCarteira'));
        $cip->setValue(TSession::getValue('boleto_cip'));
        $campo_range->setValue(TSession::getValue('boleto_campo_range'));
        $contaDv->setValue(TSession::getValue('boleto_contaDv'));
        $byte->setValue(TSession::getValue('boleto_byte'));
        $posto->setValue(TSession::getValue('boleto_posto'));

        $row = $this->form->addFields(
            [new TLabel('Carteira'), $carteira],
            [new TLabel('Byte'), $byte],
            [new TLabel('Posto'), $posto],
            [new TLabel('Convênio'), $convenio],
            [new TLabel('Código Cliente'), $codigoCliente]
        );

        $row->layout = ['col-sm-3', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-3'];

        $row = $this->form->addFields(
            [new TLabel('Range'), $campo_range],
            [new TLabel('Conta dv.'), $contaDv],
            [new TLabel('CIP'), $cip],
            [new TLabel('Variação Carteira'), $variacaoCarteira]
        );
        $row->layout = ['col-sm-3', 'col-sm-3', 'col-sm-3', 'col-sm-3'];
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
            TSession::setValue('boleto_codigoCliente', $data->codigoCliente);
            TSession::setValue('boleto_convenio', $data->convenio);
            TSession::setValue('boleto_variacaoCarteira', $data->variacaoCarteira);
            TSession::setValue('boleto_cip', $data->cip);
            TSession::setValue('boleto_campo_range', $data->campo_range);
            TSession::setValue('boleto_contaDv', $data->contaDv);
            TSession::setValue('boleto_byte', $data->byte);
            TSession::setValue('boleto_posto', $data->posto);


            self::closeWindow();

        } catch (Exception $e) {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    public function onEdit($param) {

    }

}