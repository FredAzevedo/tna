<?php

use Carbon\Carbon;

class RelFrequencia 
{
    private $arquivo = "/tmp/RelFrequencia.pdf";

    public function __construct($param)
    {
        $this->onGerarRelFrequencia($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarRelFrequencia($param)
    {
    
        //START TCPDF
        include_once( 'vendor/autoload.php' );

        try
        {

            TTransaction::open('sample');

            $aluno_id = $param['aluno_id'];
            $serie = new Serie($param['serie']);
            $anoletivo = new AnoLetivo($param['anoletivo_id']);
            $turno = $param['turno'];

            $unit_id = TSession::getValue('userunitid');

            $aluno = new Aluno($aluno_id);

            $pdf = new TCPDF();
            $pdf->SetPrintHeader(false);
            $pdf->SetMargins(10,35,10);
            $pdf->AddPage();

            //COLÉGIO CAMINHO DO SABER
            $pdf->SetFont('times','B',21);
            $pdf->SetXY(70, 8);
            $pdf->Cell(75,8,"COLÉGIO CAMINHO DO SABER",0,0,'C');

            $pdf->SetFont('times','B',12);
            $pdf->SetXY(70,15);
            $pdf->Cell(75,5,"Educação Infantil Pré-escolar e Ensino Fundamental",0,0,'C');
            
            $pdf->SetFont('times','',12);
            $pdf->SetXY(70, 20);
            $pdf->Cell(75,5,"Rua do Mamoeiro Nº 11 Conj. Potengi. Bairro Potengi",0,0,'C');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(70, 25);
            $pdf->Cell(75,5,"CEP: 59.120-600, Natal-RN. Fone: (0xx84) 3661-5831",0,0,'C');

            $pdf->SetFont('times','',12);
            $pdf->SetXY(70, 30);
            $pdf->Cell(75,5,"CNPJ: 07.148.818/0001-03",0,0,'C');

            //DECLARAÇÃO
            $pdf->SetFont('times','B',15);
            $pdf->SetXY(70, 50);
            $pdf->Cell(75,8,"DECLARAÇÃO",0,0,'C');


            $pdf->SetFont('times','',14);
            $pdf->SetXY(10, 70);
            $pdf->Cell(180,5,"Declaramos para os devidos fins de direitos e efeitos legais que o aluno(a)",0,0,'R');

            $pdf->SetFont('times','',14);
            $pdf->SetXY(20, 76);
            $pdf->Cell(170,5,"____________________________________________________________________,",0,0,'L');

            $pdf->SetFont('times','',14);
            $pdf->SetXY(20, 76);
            $pdf->Cell(170,5,$aluno->nome,0,0,'L');

            $nascimento = date("d/m/Y", strtotime($aluno->nascimento));

            $pdf->SetFont('times','',14);
            $pdf->SetXY(20, 82);
            $pdf->Cell(170,5,"Nascido (a): ".$nascimento,0,0,'L');

            $pai = new Responsavel($aluno->pai_responsavel_id);
            $mae = new Responsavel($aluno->mae_responsavel_id);

            $pdf->SetFont('times','',14);
            $pdf->SetXY(69, 82);
            $pdf->Cell(134,5,", filho (a):".$pai->nome,0,0,'L');

            $pdf->SetFont('times','',14);
            $pdf->SetXY(76, 82);
            $pdf->Cell(120,5,"       __________________________________________, ",0,0,'L');

            $pdf->SetFont('times','',14);
            $pdf->SetXY(20, 88);
            $pdf->Cell(170,5,"e ".$mae->nome,0,0,'L');

            $pdf->SetFont('times','',14);
            $pdf->SetXY(22, 88);
            $pdf->Cell(170,5," ___________________________________________________________________, ",0,0,'L');

            $pdf->SetFont('times','',14);
            $pdf->SetXY(20, 94);
            $pdf->MultiCell(170,5,"é aluno(a) regulamente matriculado(a) no ".$serie->nome." no turno ".$turno." neste estabelecimento de ensino, para o ano letivo de ".$anoletivo->ano.", está frequentando com assiduidade até a presente data.\n",0,'J');

            $date = date("d/m/Y");
            $pdf->SetFont('times','',14);
            $pdf->SetXY(20, 200);
            $pdf->Cell(170,5,"Natal/RN: ".$date.".",0,0,'R');

            $pdf->SetFont('times','',14);
            $pdf->SetXY(10, 219);
            $pdf->Cell(190,5,"________________________________________",0,0,'C');

            $pdf->SetFont('times','',14);
            $pdf->SetXY(10, 226);
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
