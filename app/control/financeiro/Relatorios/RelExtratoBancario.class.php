<?php
class RelExtratoBancario 
{
    private $arquivo = "/tmp/RelExtratoBancario.pdf";

    public function __construct($param)
    {
        $this->onGerarExtratoBancario($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarExtratoBancario($param)
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
            //$pdf->addPage('P', 'A4');

            $units  = SystemUnit::where('id','=',TSession::getValue('userunitid'))->load();
           
            if ($units)
            {
                foreach ($units as $unit)
                {   
                    //HEADER
                    $pdf = new ReportCabecalho();
                    //$pdf = new ReportWizard();
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
                    //$pdf->addPage('L', 'A4');
                    $pdf->SetMargins(10,35,10);

                    
                }
            }

            $pdf->SetXY(10, 35);
            $pdf->SetFillColor(248,248,255);
            $pdf->SetFont('helvetica','B',12);
            $pdf->Cell(190,5,"Relatório de Extrato Bancário",0,0,'C', true);

            $pdf->SetDrawColor(220,220,220);

            $conta = new ContaBancaria($contaBancaria);

            $pdf->SetXY(10, 45);
            $pdf->SetFont('helvetica','I',10);
            $pdf->Cell(190,5,"Conta: ".$conta->banco->nome_banco." AG:".$conta->agencia." CC:".$conta->conta."-".$conta->conta_dv,0,0,'L', true);

            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(193,255,193);
            $pdf->SetXY(10, 55);
            $pdf->SetFont('helvetica','',8);
            $pdf->Cell(20,5,'Data Venci.', 1, 1,'C', true);

            $pdf->SetXY(30, 55);
            $pdf->SetFont('helvetica','',8);
            $pdf->Cell(20,5,'Data Baixa', 1, 1,'C', true);

            $pdf->SetXY(50, 55);
            $pdf->SetFont('helvetica','',8);
            $pdf->Cell(120,5,'Histórico', 1, 1,'C', true);

            $pdf->SetXY(170, 55);
            $pdf->SetFont('helvetica','',8);
            $pdf->Cell(30,5,'Valor', 1, 1,'C', true);


            $conn = TTransaction::get();

            $listagem = $conn->prepare('SELECT data_vencimento, data_baixa, historico, valor_movimentacao FROM extrato_bancario WHERE data_baixa BETWEEN ? AND ? AND conta_bancaria_id = ? AND unit_id = ? ORDER BY data_baixa');

            $unit_id = TSession::getValue('userunitid');
            $listagem->execute(array($dataInicio,$dataFim,$contaBancaria,$unit_id));
            $resultListagem = $listagem->fetchAll();

            $soma = 0.00;
            foreach ($resultListagem as $dado) {

                $data_vencimento = $dado['data_vencimento'];
                $dtv = explode("-", $data_vencimento);
                $dataVenc = $dtv[2]."/".$dtv[1]."/".$dtv[0];
                $pdf->Cell(20,5,$dataVenc,1, 0, 'C');

                $data_baixa = $dado['data_baixa'];
                $dtb = explode("-", $data_baixa);
                $dataBaixa = $dtb[2]."/".$dtb[1]."/".$dtb[0];
                $pdf->Cell(20,5,$dataBaixa,1, 0, 'C');

                $pdf->Cell(120,5,$dado['historico'],1, 0, 'L');
                $pdf->Cell(30,5,'R$ '.number_format($dado['valor_movimentacao'],2,',','.'),1, 1, 'R');
                
                $soma = $soma + $dado['valor_movimentacao'];

            }

            $saldo = $conn->prepare('SELECT SUM( valor_movimentacao ) as valor  FROM extrato_bancario WHERE data_baixa <  ? AND conta_bancaria_id = ? AND unit_id = ? ORDER BY data_baixa'); 
            
            $saldo->execute(array($dataInicio,$contaBancaria,$unit_id));
            $result = $saldo->fetchAll();

            foreach ($result as $retorno) {
               $valorReturn = $retorno['valor'];
            }

            $total = $soma + $valorReturn;

            $pdf->ln(5);
            $pdf->SetX(10);

            $pdf->SetFont('helvetica','B',10);
            $pdf->SetFillColor(248,248,255);
            $pdf->Cell(190,5,'Soma do período: R$ '.number_format($soma,2,',','.'),0,1,'R', true);
            $pdf->ln(5);

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