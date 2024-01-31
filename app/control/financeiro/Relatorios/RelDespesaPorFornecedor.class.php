<?php
class RelDespesaPorFornecedor 
{
    private $arquivo = "/tmp/RelDespesaPorFornecedor.pdf";

    public function __construct($param)
    {
        $this->onGerarDespesaPorFornecedor($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarDespesaPorFornecedor($param)
    {   

        $fornecedor_id = $param['fornecedor_id'];
        
        $dataIni = $param['dataInicio'];
        $dataExplode = explode("/", $dataIni);
        $dataInicio = $dataExplode[2]."-".$dataExplode[1]."-".$dataExplode[0];

        $dataF    = $param['dataFim'];
        $dataImplode = explode("/", $dataF);
        $dataFim = $dataImplode[2]."-".$dataImplode[1]."-".$dataImplode[0];
        
        //START TCPDF
        include_once( 'vendor/autoload.php' );

        try
        {
            TTransaction::open('sample');

            $pdf = new TCPDF();
            $pdf->SetPrintHeader(false);
            //$pdf->addPage('P', 'A4');

            $units  = SystemUnit::where('id','=',TSession::getValue('userunitid'))->load();
           
            if ($units)
            {
                foreach ($units as $unit)
                {   
                    //HEADER
                    $pdf = new ReportCabecalho();
                    $pdf->set_logo("LogoWS.png");
                    $pdf->set_razao_social($unit->razao_social);
                    $pdf->set_nome_fantasia($unit->nome_fantasia);

                    $endereco = $unit->logradouro." Nº: ".$unit->numero." Bairro: ".$unit->bairro.". ".$unit->complemento." Cidade: ".$unit->cidade." UF: ".$unit->uf." CEP: ".$unit->cep;
                    $pdf->set_endereco($endereco);

                    $pdf->set_cnpj($unit->cnpj);
                    //$pdf->set_documento($numeroOrc);
                    $pdf->set_telefones($unit->telefone);
                    $pdf->set_ie($unit->insc_estadual);
                    $pdf->set_im($unit->insc_municipal);

                    $pdf->addPage('P', 'A4');
                    $pdf->SetMargins(10,35,10);

                    
                }
            }

            $pdf->SetXY(10, 35);
            $pdf->SetFillColor(248,248,255);
            $pdf->SetFont('helvetica','B',12);
            $pdf->Cell(190,5,"Resumo de despesas por um único fornecedor",0,0,'C', true);
   
            $conn = TTransaction::get();

            $unit_id = TSession::getValue('userunitid');

            $listagem = $conn->prepare("SELECT cp.fornecedor_id, f.nome_fantasia FROM conta_pagar cp INNER JOIN fornecedor f ON (f.id = cp.fornecedor_id) WHERE cp.data_baixa BETWEEN ? AND ? AND cp.unit_id = ? AND cp.fornecedor_id = ? GROUP BY 1 ORDER BY cp.data_baixa"); 

            $listagem->execute(array($dataInicio,$dataFim,$unit_id,$fornecedor_id));
            $resultlistagem = $listagem->fetchAll();

            $y=0;
            $valor=0;
            $totalr = 0;
            foreach ($resultlistagem as $result) {

                $codfor = $result['fornecedor_id'];
                $pdf->ln();
                $pdf->SetFillColor(207,207,207);
                $pdf->SetFont('helvetica','B',10);
                $pdf->Cell(190,5,$result['nome_fantasia'],1, 1, 'L', 1);
                $pdf->SetFont('helvetica','B',9);
                $pdf->Cell(20,5,'Baixa',1,0,'C',1);
                $pdf->Cell(150,5,'Descrição',1,0,'C',1);
                $pdf->Cell(20,5,'Valor',1,0,'C',1);
                $pdf->ln(); 


                $listagem2 = $conn->prepare("SELECT data_baixa, descricao, valor FROM conta_pagar WHERE fornecedor_id = ? AND data_baixa BETWEEN ? AND ? AND unit_id = ? AND baixa = 'S' ORDER BY data_baixa"); 

                $listagem2->execute(array($codfor,$dataInicio,$dataFim,$unit_id));
                $resultlistagem2 = $listagem2->fetchAll();

                $totalFornecedor=0;
                $y=0;
                
                foreach ($resultlistagem2 as $result2) {

                    $totalFornecedor = $totalFornecedor + $result2['valor'];

                    if($y%2){
                    $pdf->SetFillColor(248,248,255);
                    }else{
                    $pdf->SetFillColor(255,255,255);    
                    }
                    
                    $pdf->SetFont('helvetica','',9);
                    $data_baixa = $result2['data_baixa'];
                    $dt = explode("-", $data_baixa);
                    $dataformat = $dt[2]."/".$dt[1]."/".$dt[0];
                    $pdf->Cell(20,5,$dataformat,1,0,'C',1);
                    $pdf->Cell(150,5,$result2['descricao'],1,0,'l',1);
                    $pdf->Cell(20,5,number_format($result2['valor'],2,',','.'),1,0,'R',1);
                    
                    $pdf->ln(); 
                    $y++;

                }

                $pdf->SetFillColor(207,207,207);
                $pdf->SetFont('helvetica','B',10);
                $pdf->Cell(190,5,"Total: R$".number_format($totalFornecedor,2,',','.'),1,0,'R',1);
                $pdf->ln();
                    
                $totalr = $totalr + $totalFornecedor;
                            
            }
         

            $pdf->Output( $this->arquivo, "F");

            TTransaction::close();

        }
        catch (Exception $e)
        {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}