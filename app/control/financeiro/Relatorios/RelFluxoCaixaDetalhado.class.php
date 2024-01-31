<?php
class RelFluxoCaixaDetalhado 
{
    private $arquivo = "/tmp/RelFluxoCaixaDetalhado.pdf";

    public function __construct($param)
    {
        $this->onGerarRelFluxoCaixaDetalhado($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarRelFluxoCaixaDetalhado($param)
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
            $pdf->Cell(275,5,"FLUXO DE CAIXA",0,0,'C', true);

            $conta = new ContaBancaria($contaBancaria);

            $pdf->SetXY(10, 45);
            $pdf->SetFont('helvetica','B',9);
            $pdf->Cell(275,5,"Conta: ".$conta->banco->nome_banco." AG:".$conta->agencia." CC:".$conta->conta."-".$conta->conta_dv,0,0,'L');

            $pdf->SetXY(10, 50);
            $pdf->SetFont('helvetica','B',9);
            $pdf->Cell(275,5,"Périodo entre as datas de ".$dataIni." até ".$dataF,0,0,'L');

            $conn = TTransaction::get();

            $saldoAnterReceita = $conn->prepare('SELECT sum(valor_movimentacao) as valor FROM movimentacao_bancaria WHERE data_baixa < ? AND conta_bancaria_id = ? AND tipo = 1 and deleted_at is null'); 

            $saldoAnterReceita->execute(array($dataInicio,$contaBancaria));
            $resultReceita = $saldoAnterReceita->fetchAll();

            foreach ($resultReceita as $receita) {
               $valorReceita = $receita['valor'];
            }

            $saldoAnterDespesa = $conn->prepare('SELECT sum(valor_movimentacao) as valor FROM movimentacao_bancaria WHERE data_baixa < ? AND conta_bancaria_id = ? AND tipo = 0 and deleted_at is null'); 
            $saldoAnterDespesa->execute(array($dataInicio,$contaBancaria));
            $resultDespesa = $saldoAnterDespesa->fetchAll();

            foreach ($resultDespesa as $despesa) {
               $valorDespesa = $despesa['valor'];
            }
            
            $total = $valorReceita - $valorDespesa;

            $pdf->SetXY(195, 50);
            $pdf->SetFont('helvetica','B',9);
            $pdf->Cell(90,10,"Saldo anterior: R$ ".number_format($total,2,',','.'),0,1,'C', true);

            $pdf->SetXY(10, 60);
            $pdf->SetFont('helvetica','B',6);
            $pdf->Cell(15,5,"DATA",0,0,'C', true);

            $pdf->SetXY(25, 60);
            $pdf->Cell(15,5,"ID CP/CR",0,0,'L', true);

            $pdf->SetXY(40, 60);
            $pdf->Cell(70,5,"CLIENTE / FORNECEDOR",0,0,'L', true);

            $pdf->SetXY(110, 60);
            $pdf->Cell(65,5,"PLANO DE CONTAS",0,0,'L', true);

            $pdf->SetXY(175, 60);
            $pdf->Cell(20,5,"Nº DOC",0,0,'L', true);

            $pdf->SetXY(195, 60);
            $pdf->Cell(30,5,"TIPO PGTO.",0,0,'L', true);

            $pdf->SetXY(225, 60);
            $pdf->Cell(20,5,"ENTRADA",0,0,'R', true);

            $pdf->SetXY(245, 60);
            $pdf->Cell(20,5,"SAÍDA",0,0,'R', true);

            $pdf->SetXY(265, 60);
            $pdf->Cell(20,5,"SALDOS",0,1,'R', true);

            $listagem = $conn->prepare('SELECT movimentacao_bancaria.created_at, movimentacao_bancaria.data_baixa, cliente.nome_fantasia as cliente, fornecedor.nome_fantasia as fornecedor,
            if(movimentacao_bancaria.tipo = 1,valor_movimentacao,null)  as entrada,
            if(movimentacao_bancaria.tipo = 0,valor_movimentacao,null)  as saida,
            if(movimentacao_bancaria.tipo = 1,conta_receber_id,conta_pagar_id)  as id,
            if(movimentacao_bancaria.tipo = 1,pc_receita.nome,pc_despesa.nome)  as pc,
            if(movimentacao_bancaria.tipo = 1,conta_receber.documento,conta_pagar.documento ) as doc,
            if(movimentacao_bancaria.tipo = 1,pgto2.nome,pgto1.nome ) as pgto,
            movimentacao_bancaria.conta_bancaria_id
            FROM movimentacao_bancaria
            LEFT JOIN conta_pagar ON (conta_pagar.id = movimentacao_bancaria.conta_pagar_id)
            LEFT JOIN tipo_pgto pgto1 ON (pgto1.id = conta_pagar.tipo_pgto_id)
            LEFT JOIN conta_receber ON (conta_receber.id = movimentacao_bancaria.conta_receber_id)
            LEFT JOIN tipo_pgto pgto2 ON (pgto2.id = conta_receber.tipo_pgto_id)
            LEFT JOIN pc_despesa ON (pc_despesa.id = movimentacao_bancaria.pc_despesa_id)
            LEFT JOIN pc_receita ON (pc_receita.id = movimentacao_bancaria.pc_receita_id)
            LEFT JOIN cliente ON (cliente.id = movimentacao_bancaria.cliente_id)
            LEFT JOIN fornecedor ON (fornecedor.id = movimentacao_bancaria.fornecedor_id)
            WHERE movimentacao_bancaria.conta_bancaria_id = ? and movimentacao_bancaria.data_baixa between ? and ? and movimentacao_bancaria.deleted_at is null
            ORDER BY movimentacao_bancaria.data_baixa'); 

            $listagem->execute(array($contaBancaria,$dataInicio,$dataFim));
            $resultListagem = $listagem->fetchAll();
            
            $totDespesa = 0.00;
            $totReceita = 0.00;

            $cont = 1;
            foreach ($resultListagem as $dado) {
                
                if($cont % 2 == 0){
                    $pdf->SetFillColor(220,220,220);
                }else{
                    $pdf->SetFillColor(245,255,250);
                }

                $pdf->SetFont('helvetica','',7);
                // $pdf->SetFillColor(211,211,211);
                $data_baixa = $dado['data_baixa'];
                $dt = explode("-", $data_baixa);
                $dataformat = $dt[2]."/".$dt[1]."/".$dt[0];
                $pdf->Cell(15,5,$dataformat,0,0,'C',1);

                $pdf->Cell(15,5,$dado['id'],0,0,'L',1);
                
                if($dado['entrada'] != 0.00){
                // $pdf->SetTextColor(5,110,10);
                $pdf->SetFont('helvetica','',7);
                $pdf->Cell(70,5, substr($dado['cliente'],0,45),0,0,'L',1);
                }else{
                // $pdf->SetTextColor(248,57,8);
                $pdf->SetFont('helvetica','',7);
                $pdf->Cell(70,5,substr($dado['fornecedor'],0,45),0,0,'L',1);
                }

                $pdf->Cell(65,5,substr($dado['pc'],0,45),0,0,'L',1);

                $pdf->Cell(20,5,substr($dado['doc'],0,14),0,0,'L',1);

                $pdf->Cell(30,5,$dado['pgto'],0,0,'L',1);
                
                if($dado['entrada'] == 0.00){
                // $pdf->SetTextColor(0,0,0);
                // $pdf->SetFillColor(152,251,152);
                $pdf->Cell(20,5," ",0,0,'R',1);
                }else{
                // $pdf->SetTextColor(0,0,0);
                // $pdf->SetFillColor(152,251,152);
                $pdf->Cell(20,5,number_format($dado['entrada'],2,',','.'),0,0,'R',1);   
                }
                
                if($dado['saida'] == 0.00){
                // $pdf->SetTextColor(0,0,0);
                // $pdf->SetFillColor(255,106,106);
                $pdf->Cell(20,5," ",0,0,'R',1);
                }else{
                // $pdf->SetTextColor(0,0,0);
                // $pdf->SetFillColor(255,106,106);
                $pdf->Cell(20,5,number_format($dado['saida'],2,',','.'),0,0,'R',1);
                }
                
                $totReceita = $totReceita + $dado['entrada'];
                $totDespesa = $totDespesa + $dado['saida'];

                $total = $total + $dado['entrada'] - $dado['saida'];
                $pdf->SetTextColor(0,0,0);
                // $pdf->SetFillColor(135,206,255);
                $pdf->Cell(20,5,number_format($total,2,',','.'),0,1,'R',1);
                
                $cont++;
            }

            $pdf->SetX(10);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFillColor(211,211,211);
            $pdf->SetFont('helvetica','B',6);
            $pdf->Cell(215,5,"TOTAL GERAL DE ENTRADAS, SAÍDAS E SALDOS",0,0,'C', true);

            $pdf->SetX(225);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('helvetica','B',7);
            $pdf->Cell(20,5,number_format($totReceita,2,',','.'),0,0,'R', true);

            $pdf->SetX(245);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20,5,number_format($totDespesa,2,',','.'),0,0,'R', true);

            $pdf->SetX(265);
            $pdf->SetFont('helvetica','B',7);
            $pdf->Cell(20,5,number_format($total,2,',','.'),0,1,'R', true);


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
