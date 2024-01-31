<?php
class RelFluxoCaixaFuturo 
{
    private $arquivo = "/tmp/RelFluxoCaixaFuturo.pdf";

    public function __construct($param)
    {
        $this->onGerarRelFluxoCaixaFuturo($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarRelFluxoCaixaFuturo($param)
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

            // $pdf = new TCPDF();
            // $pdf->SetPrintHeader(false);
            // $pdf->SetMargins(10,35,10);

            $units  = SystemUnit::where('id','=',TSession::getValue('userunitid'))->load();
           
            if ($units)
            {
                foreach ($units as $unit)
                {   
                    //HEADER
                    $pdf = new ReportWizardHorizontal();
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

                    $pdf->addPage('L', 'A4');
                    $pdf->SetMargins(10,35,10);

                    
                }
            }

            $pdf->SetXY(10, 35);
            $pdf->SetFillColor(211,211,211);
            $pdf->SetFont('helvetica','B',9);
            $pdf->Cell(275,5,"FLUXO DE CAIXA FUTURO",0,0,'C', true);

            //$conta = new ContaBancaria($contaBancaria);

            $pdf->SetXY(10, 45);
            $pdf->SetFont('helvetica','B',9);
            $pdf->Cell(275,5,"",0,0,'L');

            $pdf->SetXY(10, 50);
            $pdf->SetFont('helvetica','B',9);
            $pdf->Cell(275,5,"Périodo entre as datas de ".$dataIni." até ".$dataF,0,0,'L');

            // $conn = TTransaction::get();

            // $saldoAnterReceita = $conn->prepare('SELECT sum(valor_movimentacao) as valor FROM movimentacao_bancaria WHERE data_baixa < ? AND tipo = 1 and deleted_at is null'); 

            // $saldoAnterReceita->execute(array($dataInicio));
            // $resultReceita = $saldoAnterReceita->fetchAll();

            // foreach ($resultReceita as $receita) {
            //    $valorReceita = $receita['valor'];
            // }

            // $saldoAnterDespesa = $conn->prepare('SELECT sum(valor_movimentacao) as valor FROM movimentacao_bancaria WHERE data_baixa < ? AND tipo = 0 and deleted_at is null'); 
            // $saldoAnterDespesa->execute(array($dataInicio));
            // $resultDespesa = $saldoAnterDespesa->fetchAll();

            // foreach ($resultDespesa as $despesa) {
            //    $valorDespesa = $despesa['valor'];
            // }
            
            // $total = $valorReceita - $valorDespesa;

            // $pdf->SetXY(195, 50);
            // $pdf->SetFont('helvetica','B',9);
            // $pdf->Cell(90,10,"Saldo anterior: R$ ".number_format($total,2,',','.'),0,1,'C', true);

            $pdf->SetXY(10, 60);
            $pdf->SetFont('helvetica','B',6);
            $pdf->Cell(15,5,"DATA",0,0,'C', true);

            $pdf->SetXY(25, 60);
            $pdf->Cell(15,5,"ID CP/CR",0,0,'L', true);

            $pdf->SetXY(40, 60);
            $pdf->Cell(30,5,"Documento",0,0,'L', true);

            $pdf->SetXY(70, 60);
            $pdf->Cell(70,5,"CLIENTE / FORNECEDOR",0,0,'L', true);

            $pdf->SetXY(140, 60);
            $pdf->Cell(60,5,"PLANO DE CONTAS",0,0,'L', true);

            $pdf->SetXY(200, 60);
            $pdf->Cell(45,5,"CONTA",0,0,'L', true);

            $pdf->SetXY(245, 60);
            $pdf->Cell(20,5,"RECEITA",0,0,'C', true);

            $pdf->SetXY(265, 60);
            $pdf->Cell(20,5,"DESPESA",0,1,'C', true);

            // $pdf->SetXY(265, 60);
            // $pdf->Cell(20,5,"SALDOS",0,1,'R', true);
            $conn = TTransaction::get();

            $listagem = $conn->prepare('SELECT id, descricao, documento, data_vencimento, receita, despesa, cli_for, pc_id, pc_nome, conta
            FROM fluxo_caixa_futuro
            WHERE data_vencimento between ? and ?
            ORDER BY data_vencimento asc'); 

            $listagem->execute(array($dataInicio,$dataFim));
            $resultListagem = $listagem->fetchAll();
            
            $totDespesa = 0.00;
            $totReceita = 0.00;
            $cont = 1;
            foreach ($resultListagem as $dado) {
                
                if($cont % 2 == 0){
                    $pdf->SetFillColor(220,220,220);
                } else {
                    $pdf->SetFillColor(245,255,250);
                }

                $pdf->SetFont('helvetica','',7);
                //$pdf->SetFillColor(211,211,211);
                $data_vencimento = $dado['data_vencimento'];
                $dt = explode("-", $data_vencimento);
                $dataformat = $dt[2]."/".$dt[1]."/".$dt[0];
                $pdf->Cell(15,5,$dataformat,0,0,'C',1);

                $pdf->Cell(15,5,$dado['id'],0,0,'L',1);

                $pdf->Cell(30,5,$dado['documento'],0,0,'L',1);
                
                if($dado['receita'] != ''){
                    // $pdf->SetTextColor(5,110,10);
                    $pdf->SetFont('helvetica','',7);
                    $pdf->Cell(70,5, substr($dado['cli_for'],0,45),0,0,'L',1);
                }else{
                    // $pdf->SetTextColor(248,57,8);
                    $pdf->SetFont('helvetica','',7);
                    $pdf->Cell(70,5,substr($dado['cli_for'],0,45),0,0,'L',1);
                }

                $pdf->Cell(60,5,substr($dado['pc_nome'],0,45),0,0,'L',1);

                $pdf->Cell(45,5,substr($dado['conta'],0,14),0,0,'L',1);
                
                if($dado['receita'] == ''){
                // $pdf->SetTextColor(0,0,0);
                // $pdf->SetFillColor(152,251,152);
                $pdf->Cell(20,5," ",0,0,'R',1);
                }else{
                // $pdf->SetTextColor(0,0,0);
                // $pdf->SetFillColor(152,251,152);
                $pdf->Cell(20,5,number_format($dado['receita'],2,',','.'),0,0,'R',1);   
                }
                
                if($dado['despesa'] == ''){
                // $pdf->SetTextColor(0,0,0);
                // $pdf->SetFillColor(255,106,106);
                $pdf->Cell(20,5," ",0,1,'R',1);
                }else{
                // $pdf->SetTextColor(0,0,0);
                // $pdf->SetFillColor(255,106,106);
                $pdf->Cell(20,5,number_format($dado['despesa'],2,',','.'),0,1,'R',1);
                }
                
                $totReceita = $totReceita + $dado['receita'];
                $totDespesa = $totDespesa + $dado['despesa'];

                $total = $total + $dado['receita'] - $dado['despesa'];
                // $pdf->SetTextColor(0,0,0);
                // $pdf->SetFillColor(135,206,255);
                // $pdf->Cell(20,5,number_format($total,2,',','.'),0,1,'R',1);
                $cont++;
            }

            $pdf->SetX(10);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFillColor(211,211,211);
            $pdf->SetFont('helvetica','B',6);
            $pdf->Cell(235,5,"TOTAL GERAL ENTRE RECEITAS E DESPESAS FUTURAS",0,0,'C', true);

            $pdf->SetX(245);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('helvetica','B',7);
            $pdf->Cell(20,5,number_format($totReceita,2,',','.'),0,0,'R', true);

            $pdf->SetX(265);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20,5,number_format($totDespesa,2,',','.'),0,1,'R', true);

            $pdf->ln(5);

            $pdf->SetX(245);
            $pdf->SetFont('helvetica','B',7);
            $pdf->Cell(40,5,'Total: Receita - Despesa',0,1,'C', true);

            $pdf->SetX(245);
            $pdf->SetFont('helvetica','B',7);
            $pdf->Cell(40,5,number_format($total,2,',','.'),0,1,'R', true);


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
