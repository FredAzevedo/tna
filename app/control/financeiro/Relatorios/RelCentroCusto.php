<?php
class RelCentroCusto 
{
    private $arquivo = "/tmp/RelCentroCusto.pdf";

    public function __construct($param)
    {
        $this->onGerarRelCentroCusto($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarRelCentroCusto($param)
    {
        //$contaBancaria = $param['conta_bancaria_id'];

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
            $pdf->Cell(190,5,"Relatório de Despesas por Centro de Custos",0,0,'C', true);
            
            $conn = TTransaction::get();

            $unit_id = TSession::getValue('userunitid');

            $pdf->SetDrawColor(220,220,220);

            $pdf->SetXY(10, 45);
            $pdf->SetFont('helvetica','I',10);
            $pdf->SetFillColor(211,211,211);
            $pdf->Cell(190,5,'LANÇAMENTOS',1,0,'C', true);

            $pdf->SetX(10);
            $pdf->SetFont('Times','',10);

            $listagemDespesa = $conn->prepare('SELECT d.nome, mb.departamento_id  FROM movimentacao_bancaria mb 
            INNER JOIN departamento d ON (d.id = mb.departamento_id) 
            WHERE mb.data_baixa BETWEEN ? AND ? AND mb.unit_id = ? and mb.tipo = 0
            GROUP BY 1 ORDER BY mb.data_baixa'); 

            $listagemDespesa->execute(array($dataInicio,$dataFim,$unit_id));
            $resultListagemDespesa = $listagemDespesa->fetchAll();

            $i=0;
            $totald=0;

            foreach ($resultListagemDespesa as $departamento) {

                $ccd = $departamento['departamento_id'];
                $pdf->ln();
                $pdf->SetFont('helvetica','B',10);
                $pdf->Cell(190,5,"Departamento: ".$departamento['nome'],1, 1, 'L', 1);
                $pdf->SetFont('helvetica','B',9);
                // $pdf->SetFillColor(248,248,255);
                $pdf->Cell(20,5,'Baixa',1,0,'C',1);
                $pdf->Cell(150,5,'Descrição',1,0,'C',1);
                $pdf->Cell(20,5,'Valor',1,0,'C',1);
                $pdf->ln();

                $listagemItensDespesa = $conn->prepare('SELECT data_baixa, historico, valor_movimentacao FROM movimentacao_bancaria WHERE departamento_id = ? AND data_baixa BETWEEN ? AND ? AND unit_id = ? ORDER BY data_baixa'); 

                $listagemItensDespesa->execute(array($ccd,$dataInicio,$dataFim,$unit_id));
                $resultListagemItensDespesa = $listagemItensDespesa->fetchAll();


                $y=0;
                $totalDespesa=0;

                foreach ($resultListagemItensDespesa as $despesa2) {

                    $totalDespesa = $totalDespesa + $despesa2['valor_movimentacao'];
                    if($y%2){
                    $pdf->SetFillColor(248,248,255);
                    }else{
                    $pdf->SetFillColor(255,255,255);    
                    }
                    $pdf->SetFont('helvetica','',9);
                    $data_baixa = $despesa2['data_baixa'];
                    $dt = explode("-", $data_baixa);
                    $dataformatD = $dt[2]."/".$dt[1]."/".$dt[0];
                    $pdf->Cell(20,5,$dataformatD,1,0,'C',1);
                    $historicoD = $despesa2['historico'];
                    $pdf->Cell(150,5,$historicoD,1,0,'l',1);
                    $pdf->Cell(20,5,number_format($despesa2['valor_movimentacao'],2,',','.'),1,0,'R',1);
                    
                    $pdf->ln(); 
                    $y++;
                }

                $pdf->SetFillColor(211,211,211);
                $pdf->SetFont('helvetica','B',10);
                $pdf->Cell(190,5,"Total: R$".number_format($totalDespesa,2,',','.'),1,0,'R',1);
                $pdf->ln();

                $i++;   
                
                $totald = $totald + $totalDespesa;

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