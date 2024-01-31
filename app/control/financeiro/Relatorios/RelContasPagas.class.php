<?php
class RelContasPagas //extends TWindow
{
    private $arquivo = "tmp/RelContasPagas.pdf";

    public function __construct($param = null)
    {
        $this->onContasPagas($param);
        // parent::__construct();
        // parent::setTitle('RELATORIO DE CONTAS PAGAS');
        // parent::setSize(0.8,0.8);    
        // $object = new TElement('object');
        // $object->data  = "tmp/RelContasPagas.pdf";
        // $object->style = "width: 100%; height:calc(100% - 10px)";
        // parent::add($object);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onContasPagas($param = null)
    {   
        $unit_id = TSession::getValue('userunitid');
        // $userId = $param['fornecedor_id'];

        $contaBancaria = $param['conta_bancaria_id'];

        $dataIni = $param['dataInicio'];
        //$dataIni = '01/01/2019';
        $dataExplode = explode("/", $dataIni);
        $dataInicio = $dataExplode[2]."-".$dataExplode[1]."-".$dataExplode[0];

        $dataF    = $param['dataFim'];
        //$dataF    = '31/12/2020';
        $dataImplode = explode("/", $dataF);
        $dataFim = $dataImplode[2]."-".$dataImplode[1]."-".$dataImplode[0];
        
        //START TCPDF
        include_once( 'vendor/autoload.php' );

        try
        {
            TTransaction::open('sample');

            $unit  = new SystemUnit($unit_id);

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

            $pdf->SetXY(10, 35);
            $pdf->SetFillColor(211,211,211);
            $pdf->SetFont('helvetica','B',10);
            $pdf->Cell(275,5,"RELATORIO DE CONTAS PAGAS",0,0,'L', true);

            $pdf->SetXY(10, 35);
            $pdf->SetFont('helvetica','B',8);
            $pdf->Cell(275,5,"Périodo entre as datas de ".$dataIni." até ".$dataF,0,1,'R');

            $conta = new ContaBancaria($contaBancaria);

            $pdf->SetXY(10, 40);
            $pdf->SetFont('helvetica','B',8);
            $pdf->Cell(275,5,"CONTA: ".$conta->banco->nome_banco." AG:".$conta->agencia." CC:".$conta->conta."-".$conta->conta_dv,0,1,'L');

            $conn = TTransaction::get();

            $pdf->SetFont('helvetica','B',7);
            $pdf->Cell(10,5,"ID CONTA",0,0,'L', true);
            $pdf->Cell(20,5,"DT VENC.",0,0,'C', true);
            $pdf->Cell(20,5,"CPF/CNPJ",0,0,'C', true);
            $pdf->Cell(55,5,"FORNECEDOR",0,0,'L', true);
            $pdf->Cell(50,5,"PLANO DE CONTAS",0,0,'L', true);
            $pdf->Cell(20,5,"Nº DOC. NF",0,0,'R', true);
            $pdf->Cell(20,5,"TIPO DE DOC.",0,0,'L', true);
            $pdf->Cell(15,5,"JUROS",0,0,'R', true);
            $pdf->Cell(15,5,"MULTA",0,0,'R', true);
            $pdf->Cell(15,5,"DESC.",0,0,'R', true);
            $pdf->Cell(20,5,"DT. PGTO.",0,0,'C', true);
            $pdf->Cell(15,5,"VAL. PAGO",0,1,'R', true);

            $dados = $conn->prepare("SELECT CP.id, CP.data_vencimento, F.cpf_cnpj, F.nome_fantasia, PC.nome as plano, CP.documento, PGTO.nome as pgto,
            CP.valor , CP.juros , CP.multa , CP.desconto , CP.data_baixa , CP.valor_pago
            FROM conta_pagar CP
            LEFT JOIN fornecedor F ON (F.id = CP.fornecedor_id )
            LEFT JOIN pc_despesa PC ON (PC.id = CP.pc_despesa_id )
            LEFT JOIN tipo_pgto PGTO ON (PGTO.id = CP.tipo_pgto_id )
            WHERE CP.unit_id = ? AND CP.data_baixa BETWEEN ? AND ? AND CP.baixa = 'S' AND CP.split = 'N' AND CP.conta_bancaria_id = $contaBancaria ORDER BY CP.data_baixa");

            $dados->execute(array($unit_id,$dataInicio,$dataFim));
            $resultdados = $dados->fetchAll();
            
            $ultimaData = $resultdados[0]['data_baixa'];
            $valor_pago = 0;
            $juros = 0;
            $multa = 0;
            $desconto = 0;
            $total_juros = 0;
            $total_multa = 0;
            $total_desconto = 0;
            $total_valor_pago = 0;

            foreach ($resultdados as $result) {

                if ($ultimaData !== $result['data_baixa']) {
                    
                    $pdf->SetFillColor(242,242,242);
                    $pdf->SetFont('helvetica','B',6);
                    $pdf->Cell(10,5,"",0,0,'L', true);
                    $pdf->Cell(20,5,"",0,0,'C', true);
                    $pdf->Cell(20,5,"",0,0,'L', true);
                    $pdf->Cell(55,5,"",0,0,'L', true);
                    $pdf->Cell(50,5,"",0,0,'L', true);
                    $pdf->Cell(20,5,"",0,0,'R', true);
                    $pdf->Cell(20,5,"",0,0,'L', true);
                    $pdf->Cell(15,5,number_format($juros,2,',','.'),0,0,'R', true);
                    $pdf->Cell(15,5,number_format($multa,2,',','.'),0,0,'R', true);
                    $pdf->Cell(15,5,number_format($desconto,2,',','.'),0,0,'R', true);
                    $pdf->Cell(20,5,"",0,0,'C', true);
                    $pdf->Cell(15,5,number_format($valor_pago,2,',','.'),0,1,'R', true);

                    $juros = 0;
                    $multa = 0;
                    $desconto = 0;
                    $valor_pago = 0;
                    //cabeçalho
                    $pdf->SetFillColor(211,211,211);
                    $pdf->SetFont('helvetica','B',7);
                    $pdf->Cell(10,5,"ID CONTA",0,0,'L', true);
                    $pdf->Cell(20,5,"DT VENC.",0,0,'C', true);
                    $pdf->Cell(20,5,"CPF/CNPJ",0,0,'L', true);
                    $pdf->Cell(55,5,"FORNECEDOR",0,0,'L', true);
                    $pdf->Cell(50,5,"PLANO DE CONTAS",0,0,'L', true);
                    $pdf->Cell(20,5,"Nº DOC. NF",0,0,'R', true);
                    $pdf->Cell(20,5,"TIPO DE DOC.",0,0,'L', true);
                    $pdf->Cell(15,5,"JUROS",0,0,'R', true);
                    $pdf->Cell(15,5,"MULTA",0,0,'R', true);
                    $pdf->Cell(15,5,"DESC.",0,0,'R', true);
                    $pdf->Cell(20,5,"DT. PGTO.",0,0,'C', true);
                    $pdf->Cell(15,5,"VAL. PAGO",0,1,'R', true);

                }
                
                //corpo
                $pdf->SetFont('helvetica','',6);
                $pdf->Cell(10,5,$result['id'],0,0,'L');
                $data_vencimento = $result['data_vencimento'];
                $dt = explode("-", $data_vencimento);
                $datavencimento = $dt[2]."/".$dt[1]."/".$dt[0];
                $pdf->Cell(20,5,$datavencimento,0,0,'C');
                $pdf->Cell(20,5,$result['cpf_cnpj'],0,0,'L');
                $pdf->Cell(55,5,substr($result['nome_fantasia'], 0,40),0,0,'L');
                $pdf->Cell(50,5,substr($result['plano'], 0,40),0,0,'L');
                $pdf->Cell(20,5,substr($result['documento'], 0,14),0,0,'R');
                $pdf->Cell(20,5,substr($result['pgto'], 0,16),0,0,'L');
                $pdf->Cell(15,5,number_format($result['juros'],2,',','.'),0,0,'R');
                $pdf->Cell(15,5,number_format($result['multa'],2,',','.'),0,0,'R');
                $pdf->Cell(15,5,number_format($result['desconto'],2,',','.'),0,0,'R');
                $data_baixa = $result['data_baixa'];
                $dt = explode("-", $data_baixa);
                $databaixa = $dt[2]."/".$dt[1]."/".$dt[0];
                $pdf->Cell(20,5,$databaixa,0,0,'C');
                $pdf->Cell(15,5,number_format($result['valor_pago'],2,',','.'),0,1,'R');

                $juros += $result['juros'];
                $multa += $result['multa'];
                $desconto += $result['desconto'];
                $valor_pago += $result['valor_pago'];
                $ultimaData = $result['data_baixa'];

                $total_juros += $result['juros'];
                $total_multa += $result['multa'];
                $total_desconto += $result['desconto'];
                $total_valor_pago += $result['valor_pago'];

            }

            $pdf->SetFillColor(242,242,242);
            $pdf->SetFont('helvetica','B',6);
            $pdf->Cell(105,5,"",0,0,'L', true);
            $pdf->Cell(20,5,"",0,0,'C', true);
            $pdf->Cell(20,5,"",0,0,'L', true);
            $pdf->Cell(55,5,"",0,0,'L', true);
            $pdf->Cell(50,5,"",0,0,'L', true);
            $pdf->Cell(20,5,"",0,0,'R', true);
            $pdf->Cell(20,5,"",0,0,'L', true);
            $pdf->Cell(15,5,number_format($juros,2,',','.'),0,0,'R', true);
            $pdf->Cell(15,5,number_format($multa,2,',','.'),0,0,'R', true);
            $pdf->Cell(15,5,number_format($desconto,2,',','.'),0,0,'R', true);
            $pdf->Cell(20,5,"",0,0,'C', true);
            $pdf->Cell(15,5,number_format($valor_pago,2,',','.'),0,1,'R', true);
            $pdf->ln(5);

            //RODAPÉ TOTALIZADOR
            $pdf->SetFillColor(211,211,211);
            $pdf->SetFont('helvetica','B',6);
            $pdf->Cell(25,5,"TOTAL DO PERIODO",0,0,'L', true);
            $pdf->Cell(10,5,"",0,0,'C', true);
            $pdf->Cell(15,5,"",0,0,'L', true);
            $pdf->Cell(55,5,"",0,0,'L', true);
            $pdf->Cell(50,5,"",0,0,'L', true);
            $pdf->Cell(20,5,"",0,0,'R', true);
            $pdf->Cell(20,5,"",0,0,'L', true);
            $pdf->Cell(15,5,number_format($total_juros,2,',','.'),0,0,'R', true);
            $pdf->Cell(15,5,number_format($total_multa,2,',','.'),0,0,'R', true);
            $pdf->Cell(15,5,number_format($total_desconto,2,',','.'),0,0,'R', true);
            $pdf->Cell(20,5,"",0,0,'C', true);
            $pdf->Cell(15,5,number_format($total_valor_pago,2,',','.'),0,1,'R', true);
        
            $arq = PATH."/tmp/RelContasPagas.pdf";  
            $pdf->Output( $arq, "F");

            TTransaction::close();

        }
        catch (Exception $e)
        {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}