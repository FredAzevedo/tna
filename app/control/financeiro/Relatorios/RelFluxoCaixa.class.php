<?php
class RelFluxoCaixa 
{
    private $arquivo = "/tmp/RelFluxoCaixa.pdf";

    public function __construct($param)
    {
        $this->onGerarFluxoCaixa($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarFluxoCaixa($param)
    {
        $contaBancaria = $param['conta_bancaria_id'];

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
            $pdf->SetMargins(10,35,10);

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
            $pdf->SetFont('helvetica','B',12);
            $pdf->Cell(275,5,"FLUXO DE CAIXA",0,0,'C', true);

            $conta = new ContaBancaria($contaBancaria);

            $pdf->SetXY(10, 45);
            $pdf->SetFont('helvetica','B',12);
            $pdf->Cell(275,5,"Conta: ".$conta->banco->nome_banco." AG:".$conta->agencia." CC:".$conta->conta."-".$conta->conta_dv,0,0,'L');

            $pdf->SetXY(10, 50);
            $pdf->SetFont('helvetica','B',12);
            $pdf->Cell(275,5,"Périodo entre as datas de ".$dataIni." até ".$dataF,0,0,'L');

            $conn = TTransaction::get();

            $saldoAnterReceita = $conn->prepare('SELECT sum(valor_movimentacao) as valor FROM movimentacao_bancaria WHERE data_baixa < ? AND conta_bancaria_id = ? AND tipo = 1'); 

            $saldoAnterReceita->execute(array($dataInicio,$contaBancaria));
            $resultReceita = $saldoAnterReceita->fetchAll();

            foreach ($resultReceita as $receita) {
               $valorReceita = $receita['valor'];
            }

            $saldoAnterDespesa = $conn->prepare('SELECT sum(valor_movimentacao) as valor FROM movimentacao_bancaria WHERE data_baixa < ? AND conta_bancaria_id = ? AND tipo = 0'); 
            $saldoAnterDespesa->execute(array($dataInicio,$contaBancaria));
            $resultDespesa = $saldoAnterDespesa->fetchAll();

            foreach ($resultDespesa as $despesa) {
               $valorDespesa = $despesa['valor'];
            }
            
            $total = $valorReceita - $valorDespesa;

            $pdf->SetXY(195, 50);
            $pdf->SetFont('helvetica','B',12);
            $pdf->Cell(90,10,"Saldo anterior: R$ ".number_format($total,2,',','.'),1,1,'C', true);

            $pdf->SetXY(10, 60);
            $pdf->SetFont('helvetica','B',8);
            $pdf->Cell(25,5,"DATA",1,0,'C', true);

            $pdf->SetXY(35, 60);
            $pdf->Cell(85,5,"DESCRIÇÃO",1,0,'C', true);

            $pdf->SetXY(120, 60);
            $pdf->Cell(90,5,"CLIENTE / FORNECEDOR",1,0,'C', true);

            $pdf->SetXY(210, 60);
            $pdf->Cell(25,5,"ENTRADA",1,0,'C', true);

            $pdf->SetXY(235, 60);
            $pdf->Cell(25,5,"SAÍDA",1,0,'C', true);

            $pdf->SetXY(260, 60);
            $pdf->Cell(25,5,"SALDOS",1,1,'C', true);

            $listagem = $conn->prepare('SELECT data_baixa, historico, cliente.nome_fantasia as cliente, fornecedor.nome_fantasia as fornecedor,
            if(movimentacao_bancaria.tipo = 1,valor_movimentacao,null)  as entrada,
            if(movimentacao_bancaria.tipo = 0,valor_movimentacao,null)  as saida,
            conta_bancaria_id
            FROM movimentacao_bancaria
            LEFT JOIN cliente ON (cliente.id = movimentacao_bancaria.cliente_id)
            LEFT JOIN fornecedor ON (fornecedor.id = movimentacao_bancaria.fornecedor_id)
            WHERE conta_bancaria_id = ? and data_baixa between ? and ?
            ORDER BY 1'); 

            $listagem->execute(array($contaBancaria,$dataInicio,$dataFim));
            $resultListagem = $listagem->fetchAll();
            
            $totDespesa = 0.00;
            $totReceita = 0.00;

            foreach ($resultListagem as $dado) {
    
                $pdf->SetFont('helvetica','',8);
                $pdf->SetFillColor(211,211,211);
                $data_baixa = $dado['data_baixa'];
                $dt = explode("-", $data_baixa);
                $dataformat = $dt[2]."/".$dt[1]."/".$dt[0];
                $pdf->Cell(25,5,$dataformat,1,0,'C',1);
                
                if($dado['entrada'] != 0.00){
                $pdf->SetTextColor(5,110,10);
                $pdf->SetFont('helvetica','B',8);
                $pdf->Cell(85,5,$dado['historico'],1,0,'L');
                }else{
                $pdf->SetTextColor(248,57,8);
                $pdf->SetFont('helvetica','',8);
                $pdf->Cell(85,5,$dado['historico'],1,0,'L');
                }

                if($dado['entrada'] != 0.00){
                $pdf->SetTextColor(5,110,10);
                $pdf->SetFont('helvetica','B',8);
                $pdf->Cell(90,5,$dado['cliente'],1,0,'L');
                }else{
                $pdf->SetTextColor(248,57,8);
                $pdf->SetFont('helvetica','',8);
                $pdf->Cell(90,5,$dado['fornecedor'],1,0,'L');
                }
                
                if($dado['entrada'] == 0.00){
                $pdf->SetTextColor(0,0,0);
                $pdf->SetFillColor(152,251,152);
                $pdf->Cell(25,5," ",1,0,'R',1);
                }else{
                $pdf->SetTextColor(0,0,0);
                $pdf->SetFillColor(152,251,152);
                $pdf->Cell(25,5,number_format($dado['entrada'],2,',','.'),1,0,'R',1);   
                }
                
                if($dado['saida'] == 0.00){
                $pdf->SetTextColor(0,0,0);
                $pdf->SetFillColor(255,106,106);
                $pdf->Cell(25,5," ",1,0,'R',1);
                }else{
                $pdf->SetTextColor(0,0,0);
                $pdf->SetFillColor(255,106,106);
                $pdf->Cell(25,5,number_format($dado['saida'],2,',','.'),1,0,'R',1);
                }
                
                $totReceita = $totReceita + $dado['entrada'];
                $totDespesa = $totDespesa + $dado['saida'];

                $total = $total + $dado['entrada'] - $dado['saida'];
                $pdf->SetTextColor(0,0,0);
                $pdf->SetFillColor(135,206,255);
                $pdf->Cell(25,5,number_format($total,2,',','.'),1,1,'R',1);
            
            }

            $pdf->SetX(10);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFillColor(211,211,211);
            $pdf->SetFont('helvetica','B',8);
            $pdf->Cell(200,5,"TOTAL GERAL DE ENTRADAS, SAÍDAS E SALDOS",1,0,'C', true);

            $pdf->SetX(210);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('helvetica','B',8);
            $pdf->Cell(25,5,number_format($totReceita,2,',','.'),1,0,'R', true);

            $pdf->SetX(235);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(25,5,number_format($totDespesa,2,',','.'),1,0,'R', true);

            $pdf->SetX(260);
            $pdf->SetFont('helvetica','B',8);
            $pdf->Cell(25,5,number_format($total,2,',','.'),1,1,'R', true);


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