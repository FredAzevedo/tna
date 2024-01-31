<?php

use Carbon\Carbon;

class RelDeclaracaoTransferencia 
{
    private $arquivo = "/tmp/RelDeclaracaoTransferencia.pdf";

    public function __construct($param)
    {
        $this->onGerarRelDeclaracaoTransferencia($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarRelDeclaracaoTransferencia($param)
    {
    
        //START TCPDF
        include_once( 'vendor/autoload.php' );

        try
        {
            TTransaction::open('sample');

            $aluno_id = $param['aluno_id'];
            $status = $param['status'];
            $serie = new Serie($param['serie']);
            $anoletivo_id = $param['anoletivo_id'];
            $anoletivo = new AnoLetivo($param['anoletivo_id']);

            $unit_id = TSession::getValue('userunitid');

            $aluno = new Aluno($aluno_id);

            $pdf = new TCPDF();
            $pdf->SetPrintHeader(false);
            $pdf->SetMargins(10,35,10);
            $pdf->AddPage();
           
            $pdf->SetFont('times','B',22);
            $pdf->SetXY(70, 8);
            $pdf->Cell(75,8,"COLÉGIO CAMINHO DO SABER",0,0,'C');

            $pdf->SetFont('times','B',13);
            $pdf->SetXY(70,15);
            $pdf->Cell(75,5,"Educação Infantil Pré-escolar e Ensino Fundamental",0,0,'C');
            
            $pdf->SetFont('times','',13);
            $pdf->SetXY(70, 20);
            $pdf->Cell(75,5,"Rua do Mamoeiro Nº 11 Conj. Potengi. Bairro Potengi",0,0,'C');

            $pdf->SetFont('times','',13);
            $pdf->SetXY(70, 25);
            $pdf->Cell(75,5,"CEP: 59.120-600, Natal-RN. Fone: (0xx84) 3661-5831",0,0,'C');

            $pdf->SetFont('times','',13);
            $pdf->SetXY(70, 30);
            $pdf->Cell(75,5,"CNPJ: 07.148.818/0001-03",0,0,'C');

            //DECLARAÇÃO
            $pdf->SetFont('times','B',15);
            $pdf->SetXY(70, 50);
            $pdf->Cell(75,8,"DECLARAÇÃO",0,0,'C');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 70);
            $pdf->Cell(170,5,"Declaramos para os devidos fins que se fizerem necessários que",0,0,'R');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 76);
            $pdf->Cell(170,5,"_______________________________________________________________________________,",0,0,'L');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 76);
            $pdf->Cell(170,5,$aluno->nome,0,0,'L');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 82);
            $pdf->Cell(170,5,"Nascido (a) aos ".date("d/m/Y", strtotime($aluno->nascimento)). " em  ".$aluno->cidade_nascimento,0,0,'L');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(73, 82);
            $pdf->Cell(190,5," _____________________________________________________, ",0,0,'L');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 88);
            $pdf->Cell(170,5,"filho (a) de ",0,0,'L');

            $pai = new Responsavel($aluno->pai_responsavel_id);

            $pdf->SetFont('times','',12);
            $pdf->SetXY(43, 88);
            $pdf->Cell(190,5,$pai->nome,0,0,'L');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(33, 88);
            $pdf->Cell(170,5," ________________________________________________________________________, ",0,0,'L');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 94);
            $pdf->Cell(170,5,"e de ",0,0,'L');

            $mae = new Responsavel($aluno->mae_responsavel_id);

            $pdf->SetFont('times','',12);
            $pdf->SetXY(30, 94);
            $pdf->Cell(170,5,$mae->nome,0,0,'L');

            $pdf->SetXY(30, 94);
            $pdf->Cell(170,5,"__________________________________________________________________________.",0,0,'L');

            switch ($status) {
                case 1:
            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 115);
            $pdf->MultiCell(170,5,"É aluno(a) regulamente matriculado(a) e está freqüentando até a presente data, o  ".$serie->nome.", neste Estabelecimento de Ensino.\n",0,'J');
                break;
                case 2:
            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 115);
            $pdf->MultiCell(170,5,"Foi aluno(a) regulamente matriculado(a) no ".$serie->nome.", neste Estabelecimento de Ensino, no ano letivo de ".$anoletivo->ano.", tendo sido ___________________________________.\n",0,'J');

                break;
                case 3:
            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 115);
            $pdf->MultiCell(170,5,"Solicitou uma vaga no ".$serie->nome.", neste Estabelecimento de Ensino, que poderá ser preenchida pelo(a) mesmo(a) desde que apresente a documentação necessária para a matricula.\n",0,'J');
                break;
                case 4:	
            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 115);
            $pdf->MultiCell(170,5,"Solicitou nesta data, sua TRANSFERÊNCIA para outro Estabelecimento de Ensino com direito a matricular-se no ".$serie->nome.".\n",0,'J');
                break;
            }

            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 150);
            $pdf->MultiCell(170,5,"NO CASO DE TRANSFERÊNCIA A DOCUMENTAÇÃO SERÁ ENTREGUE NO PRAZO DE 30 (TRINTA) DIAS.",0,'r');

            $pdf->SetFont('times','B',12);
            $pdf->SetXY(20, 165);
            $pdf->MultiCell(170,5,"Obs.: Está declaração tem validade por 30 (trinta) dias, a contar da presente data.\n",0,'J');

            $pdf->SetFont('times','B',12);
            $pdf->SetXY(40, 190);
            $pdf->MultiCell(150,5,"A presente DECLARAÇÃO, só é válida sem emendas ou rasuras.",0,'l');

            $date = date('d/m/Y');
            $pdf->SetFont('times','',12);
            $pdf->SetXY(20, 230);
            $pdf->Cell(170,5,"Natal/RN: ".$date.".",0,0,'R');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(10, 249);
            $pdf->Cell(190,5,"________________________________________",0,0,'C');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(10, 254);
            $pdf->Cell(190,5,"A Direção",0,0,'C');


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
