<?php
class RelComissaoVendedor 
{
    private $arquivo = "/tmp/RelComissaoVendedor.pdf";

    public function __construct($param)
    {
        $this->onGerarComissaoVendedor($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarComissaoVendedor($param)
    {
        $userId = $param['user_id'];

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
            $pdf->Cell(275,5,"COMISSÃO DE VENDAS",0,0,'C', true);

            $user = new SystemUser($userId);

            $pdf->SetXY(10, 45);
            $pdf->SetFont('helvetica','B',12);
            $pdf->Cell(275,5,"Vendedor: ".$user->name,0,0,'L');

            $pdf->SetXY(10, 50);
            $pdf->SetFont('helvetica','B',12);
            $pdf->Cell(275,5,"Périodo entre as datas de ".$dataIni." até ".$dataF,0,0,'L');

            /*$pdf->SetXY(195, 50);
            $pdf->SetFont('helvetica','B',14);
            $pdf->Cell(90,10,"Todal de Comissão: R$ ".number_format($total,2,',','.'),1,1,'C', true);*/

            $pdf->SetXY(10, 60);
            $pdf->SetFont('helvetica','B',10);
            $pdf->Cell(30,5,"FATURADO",1,0,'C', true);

            $pdf->SetXY(40, 60);
            $pdf->Cell(155,5,"DESCRIÇÃO",1,0,'C', true);

            $pdf->SetXY(195, 60);
            $pdf->Cell(30,5,"VALOR",1,0,'C', true);

            $pdf->SetXY(225, 60);
            $pdf->Cell(30,5,"TAXA (% - R$)",1,0,'C', true);

            $pdf->SetXY(255, 60);
            $pdf->Cell(30,5,"COMISSÃO",1,1,'C', true);

            $conn = TTransaction::get();

            //$unit_id = TSession::getValue('userunitid');

            $dados = $conn->prepare('SELECT CU.data_faturamento AS data_faturamento, CU.valor_faturamento AS valor_faturamento, CU.taxa_comissao AS taxa_comissao, CU.valor_comissao AS valor_comissao, CU.descricao AS descricao, CU.pago AS pago, C.nome_fantasia AS nome_fantasia FROM comissao_user CU 
                INNER JOIN cliente C ON (C.id = CU.cliente_id)
             WHERE CU.user_id = ? AND CU.pago = "N" AND CU.data_faturamento between ? and ?'); 

            $dados->execute(array($userId,$dataInicio,$dataFim));
            $resultListagem = $dados->fetchAll();
            
            $total = 0.00;
            foreach ($resultListagem as $dado) {
    
                $pdf->SetFont('helvetica','',9);
                $pdf->SetFillColor(211,211,211);
                $data_baixa = $dado['data_faturamento'];
                $dt = explode("-", $data_baixa);
                $dataformat = $dt[2]."/".$dt[1]."/".$dt[0];
                $pdf->Cell(30,5,$dataformat,1,0,'C');

                $pdf->SetFont('helvetica','B',10);
                $pdf->Cell(155,5,$dado['nome_fantasia'],1,0,'L');
                $pdf->Cell(30,5,number_format($dado['valor_faturamento'],2,',','.'),1,0,'R');   
                $pdf->Cell(30,5,number_format($dado['taxa_comissao'],2,',','.'),1,0,'R');
                $pdf->Cell(30,5,number_format($dado['valor_comissao'],2,',','.'),1,1,'R');

                $total = $total + $dado['valor_comissao'];
            
            }

            $pdf->SetX(10);
            $pdf->SetFillColor(211,211,211);
            $pdf->SetFont('helvetica','B',14);
            $pdf->Cell(275,10,"Total da comissão do péríodo: R$ ".number_format($total,2,',','.'),1,1,'R', true);


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