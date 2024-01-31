<?php
/**
 * ImportarOfx Form
 * @author  Fred Azv.
 */
class ImportarOfx extends TPage
{
    protected $form; // form

    public function __construct( $param )
    {
        parent::__construct();

        $this->html = new THtmlRenderer('app/view/financeiro/DadosOFX.html');

        $file = TSession::getValue('system_upload_file_ofx');

        $ofx  = new Ofx('tmp/'.$file);
        $replace = array();

        $banco = (string)$ofx->org;
        $conta = (string)$ofx->acctId;
        $datainicio = (string)$ofx->dtStar;
        $datafim = (string)$ofx->dtEnd;

        $somaReceita = 0.00;
        $somaDespesa = 0.00;

        foreach($ofx->bankTranList as $extrato){
            
            if($extrato->TRNTYPE == 'PAYMENT' || $extrato->TRNTYPE == 'DEBIT' || $extrato->TRNTYPE == 'CASH' || $extrato->TRNTYPE == 'XFER' && $extrato->TRNAMT < 0){
                
               $saidas[] = [ 

                    'data' => date( 'd/m/Y' , strtotime((string)$extrato->DTPOSTED)),// data de processamento
                    'valor' => number_format((string)$extrato->TRNAMT, 2, ',', '.'),// valor processado
                    'descricao'  =>  (string)$extrato->MEMO // descrição do extrato
                    
                ];

               $somaDespesa = $somaDespesa + $extrato->TRNAMT;
                
            }
           
            if($extrato->TRNTYPE == 'CREDIT' || $extrato->TRNTYPE == 'DEP' || $extrato->TRNTYPE == 'XFER' && $extrato->TRNAMT > 0){

                $entradas[] = [ 

                    'data' => date( 'd/m/Y' , strtotime((string)$extrato->DTPOSTED)),// data de processamento
                    'valor' => number_format((string)$extrato->TRNAMT, 2, ',', '.'),// valor processado
                    'descricao'  =>  (string)$extrato->MEMO // descrição do extrato

                ];
                
                $somaReceita = $somaReceita + $extrato->TRNAMT;
            }

        }
        
        $replace['saidas'] = $saidas;
        $replace['entradas'] = $entradas;
        $replace['banco'] = $banco;
        $replace['conta'] = $conta;
        $replace['dataini'] = date( 'd/m/Y' , strtotime( $datainicio ) );
        $replace['datafim'] = date( 'd/m/Y' , strtotime( $datafim ) );

        $replace['somaReceita'] = number_format($somaReceita, 2, ',', '.');
        $replace['somaDespesa'] = number_format($somaDespesa, 2, ',', '.');
        $diferenca = $somaReceita - ($somaDespesa * -1);
        $razao = number_format($diferenca, 2, ',', '.');
        $replace['diferenca'] = $razao;

        $this->html->enableSection('main', $replace);
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'ImportarOfxForm'));
        $container->add($this->html);
        
        parent::add($container);

       /* $contents = $this->html->getContents();
            
        // converts the HTML template into PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($contents);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // write and open file
        file_put_contents('app/output/document.pdf', $dompdf->output());*/
        
        // open window to show pdf
        /*$window = TWindow::create(_t('Document HTML->PDF'), 0.8, 0.8);
        $object = new TElement('object');
        $object->data  = 'app/output/document.pdf';
        $object->type  = 'application/pdf';
        $object->style = "width: 100%; height:calc(100% - 10px)";
        $window->add($object);
        $window->show();*/
    }

}
