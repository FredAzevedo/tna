<?php

use Adianti\Control\TWindow;
use Adianti\Registry\TSession;
use Adianti\Validator\TMaxLengthValidator;
use Adianti\Validator\TNumericValidator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;
use Eduardokum\LaravelBoleto\Boleto\Banco\Bancoob;

/**
 * Created by PhpStorm.
 * User: AndersonAndradede
 * Date: 02/06/2019
 * Time: 21:26
 */
class ContaBancariaBoletoSicoob extends TWindow {

    protected $form;
    protected $formname = 'form_sicoob_info';

    protected $banco;

    public function __construct() {
        parent::__construct();
        parent::setTitle('Dados para emissão do boleto Sicoob');
        parent::setSize(0.8, 0.8);

        $this->banco = new Bancoob();

        $this->form = new BootstrapFormBuilder();
        $this->form->setFormTitle('Conta Bancaria');
        $this->form->setFieldSizes('100%');

        $carteiras = $this->banco->getCarteiras();
        $carteiras = array_combine($carteiras, $carteiras);
        $carteira = new TCombo('carteira');
        $carteira->addItems($carteiras);

        $convenio = new TEntry('convenio');
        $convenio->setTip('Somente números');
        $convenio->setMask('9!');
        $convenio->addValidation('Convênio', new TMaxLengthValidator, [7]);
        $convenio->addValidation('Convênio', new TNumericValidator);


        $cooperativa = new TEntry('codigo_cooperativa');
        $cooperativa->setMask('9999-9');


        $carteira->setValue(TSession::getValue('boleto_carteira'));
        $convenio->setValue(TSession::getValue('boleto_convenio'));
        $cooperativa->setValue(TSession::getValue('boleto_codigo_cooperativa'));

        $row = $this->form->addFields(
            [new TLabel('Carteira'), $carteira],
            [new TLabel('Convênio'), $convenio],
            [new TLabel('Cooperativa'), $cooperativa]
        );
        $row->layout = ['col-sm-2', 'col-sm-3', 'col-sm-3'];

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
            TSession::setValue('boleto_convenio', $data->convenio);
            TSession::setValue('boleto_codigo_cooperativa', $data->codigo_cooperativa);

            self::closeWindow();

        } catch (Exception $e) {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    public function onEdit($param) {

    }

}