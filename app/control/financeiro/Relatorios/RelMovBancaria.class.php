<?php
class RelMovBancaria extends TWindow
{
    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Movimentacao Bancaria');
        parent::setSize(0.8,0.8);    
        $object = new TElement('object');
        $object->data  = "/tmp/movbancaria.pdf";
        $object->style = "width: 100%; height:calc(100% - 10px)";
        parent::add($object);

    }
    public function onViewPDF($param)
    {
        //$id = $param['id'];
        //START TCPDF
        include_once( 'vendor/autoload.php' );

        try
        {
            TTransaction::open('sample');

            $pdf = new TCPDF();
            $pdf->SetPrintHeader(false); 
            $pdf->addPage('L', 'A4');
            $pdf->SetMargins(10, 20, 10);

            
            $units  = SystemUnit::where('id','=',TSession::getValue('userunitid'))->load();
        
            if ($units)
            {
                foreach ($units as $unit)
                {  

                    $imageData =file_get_contents("app/images/LogoWS.png");
                    $pdf->Image('@'.$imageData, '10', '10', 20, 20, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

                    $endereco = $unit->logradouro." Nº: ".$unit->numero." Bairro: ".$unit->bairro.". ".$unit->complemento." Cidade: ".$unit->cidade." UF: ".$unit->uf." CEP: ".$unit->cep;

                    //COLUNA 1 - LINHA 1
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 10);
                    $pdf->Cell(0,0,'Razão Social:',0,0,'L');
                  
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(54, 10);
                    $pdf->Cell(0,0,$unit->razao_social,0,0,'L');

                    //COLUNA 1 - LINHA 2
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 14);
                    $pdf->Cell(0,0,'Nome Fantasia:',0,0,'L');
                      
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(57, 14);
                    $pdf->Cell(0,0,$unit->nome_fantasia,0,0,'L');

                    //COLUNA 1 - LINHA 3
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 18);
                    $pdf->Cell(0,0,'Endereço:',0,0,'L');
                      
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(50, 18);
                    $pdf->Cell(0,0,$endereco,0,0,'L');

                    //COLUNA 1 - LINHA 4
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 22);
                    $pdf->Cell(0,0,'CNPJ:',0,0,'L');
                      
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(44, 22);
                    $pdf->Cell(0,0,$unit->cnpj,0,0,'L');
                     
                    //COLUNA 1 - LINHA 5
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 26);
                    $pdf->Cell(0,0, "Fone:",0,0,'L');

                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(43, 26);
                    $pdf->Cell(0,0,$unit->telefone,0,0,'L');

                    $pdf->SetXY(10, 40);
                    $pdf->SetFont('helvetica','B',15);
                    $pdf->Cell(275,5,"Movimentação Bancária",0,0,'C'); 
                }
            }  

            //INICIO DO CORPO
            $pdf->SetXY(10, 60);
            $pdf->SetFont('helvetica','B',9);
            $pdf->Cell(10,5,'ID',0,0,'C');

            $pdf->SetXY(20, 60);
            $pdf->Cell(80,5,'Histórico',0,0,'C');

            $pdf->SetXY(100, 60);
            $pdf->Cell(30,5,'Documento',0,0,'C');

            $pdf->SetXY(130, 60);
            $pdf->Cell(50,5,'Vencimento',0,0,'C');

            $pdf->SetXY(180, 60);
            $pdf->Cell(50,5,'Baixa',0,0,'C');

            $pdf->SetXY(230, 60);
            $pdf->Cell(20,5,'Status',0,0,'C');

            $pdf->SetXY(250, 60);
            $pdf->Cell(20,5,'Conta',0,0,'C');

            $pdf->SetXY(270, 60);
            $pdf->Cell(20,5,'Valor',0,0,'C');

            try
            {
                // open a transaction with database 'samples'
                TTransaction::open('sample');
                
                // creates a repository for Customer
                $repository = new TRepository('MovimentacaoBancaria');
                
                // creates a criteria
                $criteria = new TCriteria;
                $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));

                if (TSession::getValue('MovimentacaoBancariaList_filter_historico')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_historico')); // add the session filter
                 }


                if (TSession::getValue('MovimentacaoBancariaList_filter_documento')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_documento')); // add the session filter
                }

                if (TSession::getValue('MovimentacaoBancariaList_filter_data_vencimento')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_data_vencimento')); // add the session filter
                }

                if (TSession::getValue('MovimentacaoBancariaList_filter_data_baixa')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_data_baixa')); // add the session filter
                }

                if (TSession::getValue('MovimentacaoBancariaList_filter_status')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_status')); // add the session filter
                }

                if (TSession::getValue('MovimentacaoBancariaList_filter_conta_bancaria_id')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_conta_bancaria_id')); // add the session filter
                }

                if (TSession::getValue('MovimentacaoBancariaList_filter_valor_movimentacao')) {
                    $criteria->add(TSession::getValue('MovimentacaoBancariaList_filter_valor_movimentacao')); // add the session filter
                }

                // load the objects according to criteria
                $customers = $repository->load($criteria, false);
                if ($customers)
                {
                    $pdf->SetY(65);
                    $valorTotal = 0.00;
                    foreach ($customers as $customer)
                    {
                        $pdf->SetX(10);
                        $pdf->SetFont('helvetica','',8);
                        $pdf->Cell(10,5,$customer->id,1,0,'C');

                        $pdf->SetX(20);
                        $pdf->Cell(80,5,$customer->historico,1,0,'L');

                        $pdf->SetX(100);
                        $pdf->Cell(30,5,$customer->documento,1,0,'R');

                        
                        $partes = explode(" ", $customer->data_vencimento);
                        $data = explode('-', $partes[0]);
                        $pdf->SetX(130);
                        $pdf->Cell(50,5,$data[2].'/'
                                        .$data[1].'/'
                                        .$data[0],1,0,'C');

                        $partes = explode(" ", $customer->data_baixa);
                        $data = explode('-', $partes[0]);
                        $pdf->SetX(180);
                        $pdf->Cell(50,5,$data[2].'/'
                                        .$data[1].'/'
                                        .$data[0],1,0,'C'); 

                        $pdf->SetX(230);
                        $pdf->Cell(20,5,$customer->status,1,0,'C');

                        $pdf->SetX(250);
                        $pdf->Cell(20,5,$customer->conta_bancaria_id,1,0,'C');

                        $pdf->SetX(270);
                        $pdf->Cell(20,5,"R$ ".$customer->valor_movimentacao,1,1,'R');
                        if($customer->status == "Débito"){
                            $valorTotal -= $customer->valor_movimentacao;
                        }else{
                            $valorTotal += $customer->valor_movimentacao;
                        }
                    }
                    $pdf->SetX(250);
                    $pdf->SetFont('helvetica','B',9);
                    $pdf->Cell(20,5,'Valor total',1,0,'C');

                    $pdf->SetX(270);
                    $pdf->Cell(20,5,"R$ ".$valorTotal,1,0,'R');
                }
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                
                // undo all pending operations
                TTransaction::rollback();
            }
            //FIM DO CORPO

            $arq = PATH."/tmp/movbancaria.pdf";  
            $pdf->Output( $arq, "F");
            
            //END TCPDF
            TTransaction::close();
        }
        catch (Exception $e)
        {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}