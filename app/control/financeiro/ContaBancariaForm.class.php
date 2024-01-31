<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TCriteria;
use Adianti\Database\TTransaction;
use Adianti\Log\TLoggerTXT;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Wrapper\BootstrapFormBuilder;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto;

/**
 * ContaBancariaForm Form
 * @author  Fred Azv.
 */
class ContaBancariaForm extends TPage
{
    protected $form; // form
    
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ContaBancaria');
        $this->form->setFormTitle('Conta Bancaria');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);
        $cod_banco = new TEntry('cod_banco');
        $agencia = new TEntry('agencia');
        $agencia_dv = new TEntry('agencia_dv');
        $conta = new TEntry('conta');
        $conta_dv = new TEntry('conta_dv');
        $tipo = new TCombo('tipo');
        $combo_tipos = array();
        $combo_tipos['F'] = 'Pessoa Física';
        $combo_tipos['J'] = 'Pessoa Jurídica';
        $tipo->addItems($combo_tipos);
        $cep = new TEntry('cep');
        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $bairro = new TEntry('bairro');
        $complemento = new TEntry('complemento');
        $cidade = new TEntry('cidade');
        $uf = new TEntry('uf');
        $codMuni = new TEntry('codMuni');
        $tel_gerente = new TEntry('tel_gerente');
        $tel_gerente->setMask('(99)99999-9999');
        $tel_banco = new TEntry('tel_banco');
        $tel_banco->setMask('(99)99999-9999');
        $gerente = new TEntry('gerente');
        $data_abaertura = new TDate('data_abaertura');
        $data_abaertura->setDatabaseMask('yyyy-mm-dd');
        $data_abaertura->setMask('dd/mm/yyyy');
//        $banco_id = new TEntry('banco_id');
        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $banco_id = new TDBCombo('banco_id','sample','Banco','id','nome_banco','nome_banco',$id_unit_session);
//        $banco_id->setChangeAction(new TAction([$this, 'onChangeBanco']));
        $aceite = new TCombo('aceite');
        $especieDoc = new TEntry('especieDoc');
        $especieDoc->setValue('DM');
        $aceite->addItems(['S'=> 'S', 'N' => 'N']);
        $aceite->setValue('N');

        $ultimo_nossonumero = new TEntry('ultimo_nossonumero');
        $ultimo_nossonumero->setMask('9!');

        $ultima_remessa = new TEntry('ultima_remessa');
        $ultima_remessa->setMask('9!');

        $tipo_remessa = new TCombo('tipo_remessa');

        $tipo_remessa->addItems([
            'Cnab400' => 'Cnab400',
            'Cnab240' => 'Cnab240'
        ]);

        $instrucoes1 = new TEntry('instrucoes1');
        $instrucoes2 = new TEntry('instrucoes2');
        $instrucoes3 = new TEntry('instrucoes3');
        $instrucoes4 = new TEntry('instrucoes4');

        $btn_infoBanco = TButton::create('infobanco', [$this, 'onOpenInfo'], 'Informações Obrigatórias', 'fa:plus green');

        $this->form->appendPage('Dados Principais');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Tipo de conta'), $tipo ],
                                       [ new TLabel('Unidade'), $unit_id ]);
        $row->layout = ['col-sm-2','col-sm-4', 'col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Banco'), $banco_id ],    
                                       [ new TLabel('Cod. Banco'), $cod_banco ],
                                       [ new TLabel('Agencia'), $agencia ],
                                       [ new TLabel('DV'), $agencia_dv ],
                                       [ new TLabel('Conta'), $conta ],
                                       [ new TLabel('DV'), $conta_dv ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-3', 'col-sm-1', 'col-sm-3', 'col-sm-1'];

        $row = $this->form->addFields( [ new TLabel('Nome do gerente'), $gerente ],    
                                       [ new TLabel('Contato do gerente'), $tel_gerente ],
                                       [ new TLabel('Telefone do banco'), $tel_banco ],
                                       [ new TLabel('Data Abertura'), $data_abaertura ]);
        $row->layout = ['col-sm-6', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $this->form->appendPage('Endereço Bancário');

        $row = $this->form->addFields( [ new TLabel('CEP'), $cep ],    
                                       [ new TLabel('Logradouro'), $logradouro ],
                                       [ new TLabel('Número'), $numero ]);
        $row->layout = ['col-sm-2', 'col-sm-8', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Bairro'), $bairro ],    
                                       [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Cidade'), $cidade ],
                                       [ new TLabel('UF'), $uf ]);
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-3', 'col-sm-1'];

        $row = $this->form->addFields( [ new TLabel('Código IBGE'), $codMuni ]);
        $row->layout = ['col-sm-2'];

        $this->form->appendPage('Dados para Boletos');

        $this->form->addFields( [$btn_infoBanco]);

        $row = $this->form->addFields( //[ new TLabel('Nosso Nº'), $nosso_numero ],
                                       [ new TLabel('Aceite'), $aceite ],
                                       [ new TLabel('Último Nosso Número gerado'), $ultimo_nossonumero ],
                                       [ new TLabel('Última remessa enviada'), $ultima_remessa ],
                                       [ new TLabel('CNAB'), $tipo_remessa ]
        );
//                                       [ new TLabel('Espécie do Doc.'), $especieDoc]);
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-3', 'col-sm-3'];


        $row = $this->form->addFields( [ new TLabel('Instruções linha 1'), $instrucoes1 ],    
                                       [ new TLabel('Instruções linha 2'), $instrucoes2 ]);
        $row->layout = ['col-sm-6', 'col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Instruções linha 3'), $instrucoes3 ],    
                                       [ new TLabel('Instruções linha 4'), $instrucoes4 ]);
        $row->layout = ['col-sm-6', 'col-sm-6'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
                 
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');

        $this->form->addAction('Voltar', new TAction(['ContaBancariaList','onReload']), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ContaBancariaList'));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Não sei se a função é util, porque não sei se o conteudo do campo Cod. Banco da tabela é o código do banco de 3 digitos, ou algum
     * outro tipo de código.
     */
//    public static function onChangeBanco($param) {
//        $banco_id = $param['banco_id'];
//
//        if ($banco_id == '') {
//            $data = new stdClass;
//            $data->cod_banco = '';
//            TForm::sendData( 'form_ContaBancaria', $data );
//            return;
//        }
//
//        try
//        {
//            TTransaction::open('sample'); // open a transaction
//
//            $banco = new Banco($banco_id);
//
//            if ($banco->num_banco != '') {
//                $data = new stdClass;
//                $data->cod_banco = $banco->num_banco;
//                TForm::sendData( 'form_ContaBancaria', $data );
//            }
//
//            TTransaction::close();
//        }
//        catch (Exception $e) // in case of exception
//        {
//            new TMessage('error', 'Não foi possível encontrar o banco selecionado.<br>'.$e->getMessage()); // shows the exception error message
//            TTransaction::rollback(); // undo all pending operations
//        }
//    }

    public static function onOpenInfo($param) {
        $banco_id = $param['banco_id'];

        $codigo_banco = null;

        TTransaction::open('sample');
        try {

            $banco = new Banco($banco_id);

            $codigo_banco = $banco->num_banco;

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
        }

        if (!$codigo_banco) {
            new TMessage('error', 'Não foi possível encontrar o banco. Verifique se está selecionado na aba "Dados Principais"');
            return;
        }


        if ($codigo_banco === Boleto::COD_BANCO_BANCOOB) {
            Adianti\Core\AdiantiCoreApplication::loadPage('ContaBancariaBoletoSicoob');
        } else
        if ($codigo_banco === Boleto::COD_BANCO_SICREDI) {
            Adianti\Core\AdiantiCoreApplication::loadPage('ContaBancariaBoletoSicredi');
        }
        else {
            Adianti\Core\AdiantiCoreApplication::loadPage('ContaBancariaBoletoGenerico');
//            new TMessage('error', 'Banco não suportado para emitir boleto');
        }
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            **/
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file

            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array

            $data = $this->setValuesData($data);

            $object = new ContaBancaria;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function clearValuesWindow() {
        TSession::delValue('boleto_carteira');
        TSession::delValue('boleto_convenio');
        TSession::delValue('boleto_codigo_cooperativa');
        TSession::delValue('boleto_codigoCliente');
        TSession::delValue('boleto_variacaoCarteira');
        TSession::delValue('boleto_cip');
        TSession::delValue('boleto_campo_range');
        TSession::delValue('boleto_contaDv');
        TSession::delValue('boleto_posto');
        TSession::delValue('boleto_byte');
        TSession::delValue('boleto_beneficiario');
    }

    public function setValuesData($data) {

        $data->carteira = TSession::getValue('boleto_carteira');
        $data->convenio = TSession::getValue('boleto_convenio');
        $data->codigo_cooperativa = TSession::getValue('boleto_codigo_cooperativa');
        $data->codigoCliente = TSession::getValue('boleto_codigoCliente');
        $data->variacaoCarteira = TSession::getValue('boleto_variacaoCarteira');
        $data->cip = TSession::getValue('boleto_cip');
        $data->campo_range = TSession::getValue('boleto_campo_range');
        $data->contaDv = TSession::getValue('boleto_contaDv');
        $data->posto = TSession::getValue('boleto_posto');
        $data->byte = TSession::getValue('boleto_byte');
        $data->beneficiario = TSession::getValue('boleto_beneficiario');

        return $data;

    }

    public function setValuesWindow($data) {
         TSession::setValue('boleto_carteira', $data->carteira);
         TSession::setValue('boleto_convenio', $data->convenio);
         TSession::setValue('boleto_codigo_cooperativa', $data->codigo_cooperativa);
         TSession::setValue('boleto_codigoCliente', $data->codigoCliente);
         TSession::setValue('boleto_variacaoCarteira', $data->variacaoCarteira);
         TSession::setValue('boleto_cip', $data->cip);
         TSession::setValue('boleto_campo_range', $data->campo_range);
         TSession::setValue('boleto_contaDv', $data->contaDv);
         TSession::setValue('boleto_posto', $data->posto);
         TSession::setValue('boleto_byte', $data->byte);
         TSession::setValue('boleto_beneficiario', $data->beneficiario);
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        $this->clearValuesWindow();
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('sample'); // open a transaction
                $object = new ContaBancaria($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                $this->setValuesWindow($object);
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
