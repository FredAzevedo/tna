<?php


use Adianti\Control\TWindow;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TFile;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Sicredi;
use Eduardokum\LaravelBoleto\Cnab\Retorno\Factory;

class ImportarRetornoForm extends TWindow {

    protected $form;
    protected static $formname = 'form_importar_retorno';

    public function __construct() {
        parent::__construct();
        parent::setTitle('Importar arquivo retorno');
        parent::setSize(0.8, 0.8);


        $this->form = new BootstrapFormBuilder(self::$formname);
        $this->form->setFormTitle('Importar arquivo retorno');
        $this->form->setFieldSizes('100%');


        $file = new TFile('file_retorno');
        $teste = new TEntry('teste');

        $row = $this->form->addFields(
            [new TLabel('Retorno'), $file]
        );
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields(
            [new TLabel('Teeste'), $teste]
        );
        $row->layout = ['col-sm-12'];

        $btn = $this->form->addAction('Importar', new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';

        $container = new TVBox();
        $container->style = 'width: 100%';
        $container->add($this->form);

        parent::add($container);

    }

    public function onSave($param) {

        $data = $this->form->getData();

        $file_name = $data->file_retorno;

        $remessa = Factory::make('tmp' . DIRECTORY_SEPARATOR .$file_name );

        $remessa->processar();

        $nome_banco = $remessa->getBancoNome();
        //var_dump($remessa->getDetalhes());
        TTransaction::open('sample'); 

        $conn = TTransaction::get();
        $sth = $conn->prepare('TRUNCATE TABLE boleto_retorno');
        $sth->execute();

        foreach($remessa->getDetalhes() as $object) {

                try
                {
                    
                    $retorno = new BoletoRetorno();
                    $retorno->banco_nome = $nome_banco;
                    $retorno->carteira = $object->carteira;
                    $retorno->nossoNumero = $object->nossoNumero;
                    $retorno->numeroDocumento = $object->numeroDocumento;
                    $retorno->numeroControle = $object->numeroControle;
                    $retorno->ocorrencia = $object->ocorrencia;
                    $retorno->ocorrenciaTipo = $object->ocorrenciaTipo;
                    $retorno->ocorrenciaDescricao = $object->ocorrenciaDescricao;
                    $retorno->dataOcorrencia = implode("-", array_reverse(explode("/", $object->dataOcorrencia)));
                    $retorno->dataVencimento = implode("-", array_reverse(explode("/", $object->dataVencimento)));
                    $retorno->dataCredito = implode("-", array_reverse(explode("/", $object->dataCredito)));
                    $retorno->valor = $object->valor;
                    $retorno->valorTarifa = $object->valorTarifa;
                    $retorno->valorIOF = $object->valorIOF;
                    $retorno->valorAbatimento = $object->valorAbatimento;
                    $retorno->valorDesconto = $object->valorDesconto;
                    $retorno->valorRecebido = $object->valorRecebido;
                    $retorno->valorMora = $object->valorMora;
                    $retorno->valorMulta = $object->valorMulta;
                    $retorno->error = $object->error;
                    //$retorno->trash = $object->trash;
                    $retorno->store();
                    
                }
                catch (Exception $e) // in case of exception
                {
                    new TMessage('error', $e->getMessage()); // shows the exception error message
                    $this->form->setData( $this->form->getData() ); // keep form data
                    TTransaction::rollback(); // undo all pending operations
                }
        }

        TTransaction::close();
        $this->form->setData($data);
    }

}