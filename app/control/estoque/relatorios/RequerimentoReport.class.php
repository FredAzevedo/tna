<?php
class RequerimentoReport extends TWindow
{
    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Requerimento de Peças para Serviços');
        parent::setSize(0.8,0.8);    
        $object = new TElement('object');
        $object->data  = "tmp/RequerimentoReport.pdf";
        $object->style = "width: 100%; height:calc(100% - 10px)";
        parent::add($object);

    }

    function onViewRequerimentoReport($param)
    {
        
        $key = $param['key'];

        //START TCPDF
        include_once( 'vendor/autoload.php' );

        try
        {
            TTransaction::open('sample');

            $units  = SystemUnit::where('id','=',TSession::getValue('userunitid'))->load();
           
            if ($units)
            {
                foreach ($units as $unit)
                {   
                    //HEADER
                    $pdf = new ReportWizard();
                    $pdf->set_logo("LogoWS.png");
                    $pdf->set_razao_social($unit->razao_social);
                    $pdf->set_nome_fantasia($unit->nome_fantasia);
                    $endereco = $unit->logradouro." Nº: ".$unit->numero." Bairro: ".$unit->bairro.". ".$unit->complemento." Cidade: ".$unit->cidade." UF: ".$unit->uf." CEP: ".$unit->cep;
                    $pdf->set_endereco($endereco);
                    $pdf->set_cnpj($unit->cnpj);
                    $numeroRequisicao = str_pad($key, 10, "0", STR_PAD_LEFT);
                    $pdf->set_documento($numeroRequisicao);
                    $pdf->set_telefones($unit->telefone);
                    $pdf->set_ie($unit->insc_estadual);
                    $pdf->set_im($unit->insc_municipal);

                    $pdf->addPage('L', 'A4');
                    $arq = PATH."/tmp/RequerimentoReport.pdf";

                    //END HEAER
                    //$pdf->SetMargins(10,35,10);
                    $pdf->SetXY(10, 54);
                    $pdf->SetFont('helvetica','B',9);
                    $pdf->Cell(50,5,"DATA DA REQUISIÇÀO:",0,0,'L');

                    $dataReq = new EstoqueRequisicao( $key );

                    $year = substr($dataReq->data_requisicao,0,4);
                    $mon  = substr($dataReq->data_requisicao,5,2);
                    $day  = substr($dataReq->data_requisicao,8,2);
                    $dataConvetida = "$day/$mon/$year";
                    
                    $pdf->SetXY(48, 54);
                    $pdf->SetFont('helvetica','I',10);
                    $pdf->Cell(50,5,$dataConvetida,0,0,'L');

                    $pdf->SetXY(10, 2);
                    $pdf->SetFont('helvetica','B',10);
                    $pdf->Cell(50,5,"REQUERIMENTO DE MATERIAIS",0,0,'L');

                    $pdf->SetXY(10, 33);
                    $pdf->SetFont('helvetica','B',10);
                    $pdf->Cell(50,5,"TERMO DE ACEITE:",0,0,'L');

                    $pdf->SetXY(84, 35);
                    $pdf->SetFont('helvetica','B',10);
                    $pdf->Cell(50,5,"",0,0,'L');

                    $Req  = EstoqueRequisicao::where('id','=',$key)->load();

                    if ($Req)
                    {
                        foreach ($Req as $objReq)
                        { 
                            //248,248,255
                            $pdf->SetXY(10, 38);
                            $pdf->SetFillColor(245,245,245);
                            $pdf->SetFont('helvetica','B',10);
                            $pdf->Cell(275,15,"",0,0,'L', true);

                            $pdf->SetXY(10, 38);
                            $pdf->SetFont('helvetica','',10);
                            $pdf->MultiCell(275,15,"         Declaro que recebi o(s) material(is) listado(s) abaixo neste documento, estou ciente de que estou passível a cobranças caso não faça o seu uso correto e por isso comprometo-me em utilizá-lo(s) apenas para o(s) serviço(s) destinados à Polyclima.",0,1,'L');
                        
                            //ITENS DO ORÇAMENTO
                            $pdf->SetFillColor(173,216,230);

                            $pdf->SetXY(10, 59);
                            $pdf->SetFont('helvetica','B',8);
                            $pdf->Cell(25, 5,'COD/REF',1,0,'C', true);

                            $pdf->SetXY(35, 59);
                            $pdf->SetFont('helvetica','B',8);
                            $pdf->Cell(225, 5,'MATERIAIS',1,0,'C', true);

                            $pdf->SetXY(260, 59);
                            $pdf->SetFont('helvetica','B',8);
                            $pdf->Cell(25, 5,'Quantidade',1,0,'C', true);

                            //loop dos itens dos produtos

                            $produtos  = EstoqueRequisicaoItens::where('estoque_requisicao_id','=',$key)->load();

                            if ($produtos)
                            {
                                $pdf->SetXY(10,64);
                                //$pdf->SetMargins(165, 0, 0);
                                $pdf->SetFont('helvetica','I',8);
                                $prod = 0.00;
                                foreach ($produtos as $obj)
                                {   
                                    $pdf->Cell(25, 5,substr($obj->produto->cod_referencia,0,47),1,0,'L');
                                    $pdf->Cell(225, 5,substr($obj->produto->nome_produto,0,47),1,0,'L');
                                    $pdf->Cell(25, 5,$obj->quantidade,1,1,'R');
                                }
                            }

                            //fim do loop dos itens dos produtos

                            //assinaturas

                            $pdf->SetXY(120, 180);
                            $pdf->SetFont('helvetica','I',8);
                            $pdf->Cell(80, 5,'___________________________________________',0,0,'C');

                            $pdf->SetXY(120, 183);
                            $pdf->SetFont('helvetica','I',7);
                            $pdf->Cell(80, 5,'Ass. Responsável pelo Almoxarife',0,0,'C');


                            $pdf->SetXY(210, 180);
                            $pdf->SetFont('helvetica','I',8);
                            $pdf->Cell(80, 5,'___________________________________________',0,0,'C');

                            $pdf->SetXY(210, 183);
                            $pdf->SetFont('helvetica','I',7);
                            $pdf->Cell(80, 5,'Ass. do Técnico Responsável pela Requisição',0,0,'C');

                            //retangulo

                            $pdf->SetXY(8, 155);
                            $pdf->SetFont('helvetica','B',7);
                            $pdf->Cell(20, 5,'Observação',0,0,'C');

                            $pdf->SetXY(10, 160);
                            $pdf->Cell(100, 25,'',1,0,'L');

                            $pdf->SetXY(10, 160);
                            $pdf->SetFont('helvetica','B',7);
                            $pdf->MultiCell(100, 5,$objReq->observacao,0, 'L', 0, 0, '', '', true);
                        }   
                    }

                    $pdf->Output($arq, "F");
                }
            }

            
            //END TCPDF
        TTransaction::close();

        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}   