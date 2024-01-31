<?php
/**
 * ImportaOFXConciliacaoForm
 * @author  Fred Azv.
 */
class ImportaOFXConciliacaoForm extends TPage
{
    protected $form; // form

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ImportaOFXConciliacaoForm');
        $this->form->setFormTitle('Importar OFX');
        $this->form->setFieldSizes('100%');
        
        $filename = new TFile('filename');
        $filename->setAllowedExtensions( ['ofx'] );
        
        $row = $this->form->addFields( [ new TLabel('Importar Arquivo OFX'), $filename ]

        );
        $row->layout = ['col-sm-12'];   

        $btn = $this->form->addAction('Processar Arquivo', new TAction([$this, 'onProcessarOFX']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        /*
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction([$this,'onReload']), 'fa:angle-double-left');*/

        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ImportaOFXConciliacaoForm'));
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onProcessarOFX( $param )
    {

        try
        {   
            TTransaction::open('sample');

            $conn = TTransaction::get();
            $sth = $conn->prepare('TRUNCATE conciliacao_bancaria');
            $sth->execute([]);

            $unit_id = TSession::getValue('userunitid');
            $data = $this->form->getData(); // get form data as array
            $this->form->validate(); // validate form data
    
            $file = $data->filename;
            $ofx  = new Ofx('tmp/'.$file);

            $banco = (string)$ofx->org;
            $conta = (string)$ofx->acctId;
            $cont = explode("-", $conta);
            $contaBancaria = ContaBancaria::where('conta','=',$cont[0])->first();

            if($contaBancaria == null){
                throw new Exception('Conta Corrente deste arquivo não está cadastrada no sistema! Favor cadastre e tente novamente.');
            }

            $datainicio = (string)$ofx->dtStar;
            $datafim = (string)$ofx->dtEnd;
            
            TSession::setValue('datainicio', $datainicio);
            TSession::setValue('datafim', $datafim);

            foreach($ofx->bankTranList as $extrato){
                
                if($extrato->TRNTYPE == 'PAYMENT' || $extrato->TRNTYPE == 'DEBIT' || $extrato->TRNTYPE == 'CASH' || $extrato->TRNTYPE == 'XFER' && $extrato->TRNAMT < 0){
                    $tipo = 'D';
                }

                if($extrato->TRNTYPE == 'CREDIT' || $extrato->TRNTYPE == 'DEP' || $extrato->TRNTYPE == 'XFER' && $extrato->TRNAMT > 0){
                    $tipo = 'R';
                }

                $dataMov = date( 'Y-m-d' , strtotime((string)$extrato->DTPOSTED)); // data de processamento
                $valor = number_format((string)$extrato->TRNAMT, 2, ',', '.'); // valor processado
                $descricao  = (string)$extrato->MEMO; // descrição do extrato

                $conciliacao = new ConciliacaoBancaria;
                $conciliacao->dataMov = $dataMov;
                $conciliacao->descricao = trim($descricao);
                $conciliacao->valor = Utilidades::to_number($valor);
                $conciliacao->unit_id = $unit_id;
                $conciliacao->conta_bancaria_id = $contaBancaria->id;
                $conciliacao->tipo = $tipo;
                $conciliacao->store();

            }

            TTransaction::close(); 
            AdiantiCoreApplication::loadPage('ImportarOfxConciliacao');

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
        }

    }

}
