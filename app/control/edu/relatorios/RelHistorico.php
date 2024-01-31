<?php 

use Carbon\Carbon;
class RelHistorico 
{

    private $arquivo = "/tmp/HistoricoEscolar.pdf";

    public function __construct($param)
    {
        $this->onGerarHistorico($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarHistorico($param)
    {

 
        TTransaction::open('sample');
        $historico_id = $param;

        $historico = new Historico($historico_id);

        //Aluno
        $aluno = new Aluno($historico->aluno_id);

        //START TCPDF
        include_once( 'vendor/autoload.php' );

        $pdf = new TCPDF('P','mm','A4');
        $pdf->SetPrintHeader(false);
        $pdf->Open();
        $pdf->AddPage();
        $pdf->SetFont('times','',12);
        $pdf->SetMargins(5,0,0);
         
        //COLÉGIO CAMINHO DO SABER
        $pdf->SetFont('times','B',18);
        $pdf->SetXY(70, 8);
        $pdf->Cell(75,8,"COLÉGIO CAMINHO DO SABER",0,0,'C');

        $pdf->SetFont('times','B',10);
        $pdf->SetXY(70,15);
        $pdf->Cell(75,5,"Educação Infantil Pré-escolar, Ensino Fundamental e Ensino Médio",0,0,'C');
        
        $pdf->SetFont('times','',10);
        $pdf->SetXY(70, 20);
        $pdf->Cell(75,5,"Rua do Mamoeiro Nº 11 Conj. Potengi. Bairro Potengi",0,0,'C');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(70, 25);
        $pdf->Cell(75,5,"CEP: 59.120-600, Natal-RN. Fone: (0xx84) 3661-5831",0,0,'C');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(70, 30);
        $pdf->Cell(75,5,"CNPJ: 07.148.818/0001-03",0,0,'C');

        $pdf->SetFont('times','',15);
        $pdf->SetXY(70, 55);
        $pdf->Cell(75,5,"HISTÓRICO ESCOLAR",0,0,'C');

        $pdf->SetFont('times','',15);
        $pdf->SetXY(70, 62);
        $pdf->Cell(75,5,"ENSINO FUNDAMENTAL E MÉDIO",0,0,'C');

        //$pdf->Rect(5,85,200,30,'T');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(6, 88);
        $pdf->Cell(198,5,"ESTABELECIMENTO: COLÉGIO CAMINHO DO SABER",0,0,'L');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(6, 93);
        $pdf->Cell(198,5,"ENTIDADE MANTEDORA: PARTICULAR",0,0,'L');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(6, 98);
        $pdf->Cell(100,5,"PORTARIA DE AUTORIZAÇÃO: 1059/2018 - SEEC/GS",0,0,'L');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(106, 98);
        $pdf->Cell(98,5,"DATA: ".date('d/m/Y'),0,0,'L');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(6, 103);
        $pdf->Cell(100,5,"LOCALIDADE: POTENGI",0,0,'L');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(106, 103);
        $pdf->Cell(98,5,"MUNICÍPIO: NATAL",0,0,'L');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(6, 108);
        $pdf->Cell(198,5,"ESTADO: RN",0,0,'L');

        //$pdf->Rect(5,120,200,30,'T');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(6, 122);
        $pdf->Cell(198,5,"NOME DO ALUNO(A): ".$aluno->nome,0,0,'L');

        $responsavel_mae = new Responsavel($aluno->mae_responsavel_id);

        $pdf->SetFont('times','',10);
        $pdf->SetXY(6, 127);
        $pdf->Cell(198,5,"FILHO (a) DE: ".$responsavel_mae->nome,0,0,'L');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(6, 132);
        $pdf->Cell(100,5,"NACIONALIDADE: ".$aluno->nacionalidade,0,0,'L');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(106, 132);
        $pdf->Cell(98,5,"NATURALIDADE: ".$aluno->naturalidade,0,0,'L');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(6, 137);
        $pdf->Cell(100,5,"ESTADO: ".$aluno->uf_nascimento,0,0,'L');

        $nascimento = date("d/m/Y", strtotime($aluno->nascimento));

        $pdf->SetFont('times','',10);
        $pdf->SetXY(6, 142);
        $pdf->Cell(100,5,"DATA DE NASCIMENTO: ".$nascimento,0,0,'L');

        $dia = date('d');
        $mes = date('m');
        $ano = date('Y');

        Carbon::setLocale('pt_BR');
        $date = Carbon::create($ano, $mes, $dia);

        $dateExtenso = $date->formatLocalized('%e de %B de %Y');

        $pdf->SetFont('times','',12);
        $pdf->SetXY(5, 190);
        $pdf->Cell(200,5,"Natal-RN, ".$dateExtenso,0,0,'C');

        $pdf->SetFont('times','',12);
        $pdf->SetXY(5, 190);
        $pdf->Cell(200,5,"______________________________________________",0,0,'C');

        $pdf->SetFont('times','',12);
        $pdf->SetXY(5, 195);
        $pdf->Cell(200,5,"Localidade e data de expedição",0,0,'C');

        $pdf->SetFont('times','',12);
        $pdf->SetXY(5, 210);
        $pdf->Cell(200,5,"______________________________________________",0,0,'C');

        $pdf->SetFont('times','',12);
        $pdf->SetXY(5, 215);
        $pdf->Cell(200,5,"Secretário",0,0,'C');

        $pdf->SetFont('times','',12);
        $pdf->SetXY(5, 260);
        $pdf->Cell(200,5,"______________________________________________",0,0,'C');

        $pdf->SetFont('times','',12);
        $pdf->SetXY(5, 265);
        $pdf->Cell(200,5,"Diretor",0,0,'C');

        $pdf->AddPage('','');

        //$pdf->Rect(5,8,200,280,'T');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(5,10);
        $pdf->Cell(200,5,"NOME DO ESTABELECIMENTO: COLÉGIO CAMINHO DO SABER. MUNICÍPIO: Natal. ESTADO: RN.",0,1,'C');

        $pdf->SetFont('times','',12);
        $pdf->Cell(200,5,"Nome do Aluno: ".$aluno->nome,0,1,'l');

        $pdf->SetFont('times','',10);
        $pdf->SetFillColor(220,220,220);
        $pdf->Cell(40,5,"Componentes",0,0,'C', true);

        $pdf->SetFont('times','',10);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetXY(5, 25);
        $pdf->Cell(40,5,"Curriculares",0,0,'C', true);

        $pdf->Rect(5,20,40,10,'T');

        $pdf->SetFont('times','',10);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetXY(45, 20);
        $pdf->Cell(76,5,"Fundamental I",1,0,'C', true);

        $pdf->SetFont('times','',10);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetXY(121, 20);
        $pdf->Cell(42,5,"Fundamental II",1,0,'C', true);

        $pdf->SetFont('times','',10);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetXY(163, 20);
        $pdf->Cell(42,5,"Ensino Médio",1,0,'C', true);

        $pdf->SetFont('times','',10);
        $pdf->SetXY(45, 25);
        $pdf->Cell(15.2,5,"1ª Ano",1,0,'C');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(60.2, 25);
        $pdf->Cell(15.2,5,"2ª Ano",1,0,'C');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(75.4, 25);
        $pdf->Cell(15.2,5,"3ª Ano",1,0,'C');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(90.6, 25);
        $pdf->Cell(15.2,5,"4ª Ano",1,0,'C');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(105.8, 25);
        $pdf->Cell(15.2,5,"5ª Ano",1,0,'C');

        $pdf->SetFont('times','',8);
        $pdf->SetXY(120.96, 25);
        $pdf->Cell(10.5,5,"6ª Ano",1,0,'C');

        $pdf->SetFont('times','',8);
        $pdf->SetXY(131.46, 25);
        $pdf->Cell(10.5,5,"7ª Ano",1,0,'C');

        $pdf->SetFont('times','',8);
        $pdf->SetXY(141.96, 25);
        $pdf->Cell(10.5,5,"8ª Ano",1,0,'C');

        $pdf->SetFont('times','',8);
        $pdf->SetXY(152.46, 25);
        $pdf->Cell(10.5,5,"9ª Ano",1,0,'C');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(162.96, 25);
        $pdf->Cell(14,5,"1ª Série",1,0,'C');

        $pdf->SetFont('times','',10);
        $pdf->SetXY(176.96, 25);
        $pdf->Cell(14,5,"2ª Série",1,0,'C');

        $pdf->SetFont('times','',10);
        //$pdf->SetXY(190.96, 25);
        $pdf->Cell(14,5,"3ª Série",1,1,'C');

        $notas = HistoricoNotas::where('historico_id','=',$historico_id)->load();
        if($notas){
            foreach($notas as $resultado)
            {
                $disciplina = new Disciplina($resultado->disciplina_id);
                ($resultado->disciplina_id == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	    
                $pdf->Cell(40,5,$resultado->disciplina->nome,1,0,'L', true);
                ($resultado->n1_ano == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	
                $pdf->Cell(15.2,5,$resultado->n1_ano,1,0,'C', true);
                ($resultado->n2_ano == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	
                $pdf->Cell(15.2,5,$resultado->n2_ano,1,0,'C', true);
                ($resultado->n3_ano == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	
                $pdf->Cell(15.2,5,$resultado->n3_ano,1,0,'C', true);
                ($resultado->n4_ano == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	
                $pdf->Cell(15.2,5,$resultado->n4_ano,1,0,'C', true);
                ($resultado->n5_ano == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	
                $pdf->Cell(15.2,5,$resultado->n5_ano,1,0,'C', true);
                ($resultado->n6_ano == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	
                $pdf->Cell(10.5,5,$resultado->n6_ano,1,0,'C', true);
                ($resultado->n7_ano == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	
                $pdf->Cell(10.5,5,$resultado->n7_ano,1,0,'C', true);
                ($resultado->n8_ano == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	
                $pdf->Cell(10.5,5,$resultado->n8_ano,1,0,'C', true);
                ($resultado->n9_ano == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	 
                $pdf->Cell(10.5,5,$resultado->n9_ano,1,0,'C', true);
                ($resultado->n1_serie == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	 
                $pdf->Cell(14,5,$resultado->n1_serie,1,0,'C', true);
                ($resultado->n2_serie == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	 
                $pdf->Cell(14,5,$resultado->n2_serie,1,0,'C', true);
                ($resultado->n3_serie == "-") ? $pdf->SetFillColor(220,220,220) : $pdf->SetFillColor(255,255,255);	 
                $pdf->Cell(14,5,$resultado->n3_serie,1,1,'C', true);
            }
        }

        $pdf->ln();
        $pdf->SetFont('times','',10);
        $pdf->SetFillColor(220,220,220);
        $pdf->Cell(40,5,"",0,0,'C', true);
        $pdf->Cell(15.2,5,"1ª Ano",1,0,'C');
        $pdf->Cell(15.2,5,"2ª Ano",1,0,'C');
        $pdf->Cell(15.2,5,"3ª Ano",1,0,'C');
        $pdf->Cell(15.2,5,"4ª Ano",1,0,'C');
        $pdf->Cell(15.2,5,"5ª Ano",1,0,'C');
        $pdf->Cell(10.5,5,"6ª Ano",1,0,'C');
        $pdf->Cell(10.5,5,"7ª Ano",1,0,'C');
        $pdf->Cell(10.5,5,"8ª Ano",1,0,'C');
        $pdf->Cell(10.5,5,"9ª Ano",1,0,'C');
        $pdf->Cell(14,5,"1ª Série",1,0,'C');
        $pdf->Cell(14,5,"2ª Série",1,0,'C');
        $pdf->Cell(14,5,"3ª Série",1,1,'C');
        

        $final = HistoricoResultadoFinal::where('historico_id','=',$historico_id)->load();
        if($final)
        {
            foreach($final as $f)
            {
                $pdf->Cell(40,5,$f->totais,1,0,'L');
                $pdf->Cell(15.2,5,$f->n1_ano,1,0,'C');
                $pdf->Cell(15.2,5,$f->n2_ano,1,0,'C');	
                $pdf->Cell(15.2,5,$f->n3_ano,1,0,'C');
                $pdf->Cell(15.2,5,$f->n4_ano,1,0,'C');
                $pdf->Cell(15.2,5,$f->n5_ano,1,0,'C');
                $pdf->Cell(10.5,5,$f->n6_ano,1,0,'C');
                $pdf->Cell(10.5,5,$f->n7_ano,1,0,'C');
                $pdf->Cell(10.5,5,$f->n8_ano,1,0,'C');
                $pdf->Cell(10.5,5,$f->n9_ano,1,0,'C');
                $pdf->Cell(14,5,$f->n1_serie,1,0,'C');
                $pdf->Cell(14,5,$f->n2_serie,1,0,'C');
                $pdf->Cell(14,5,$f->n3_serie,1,1,'C');
            }
        }

        $pdf->ln();
        $pdf->SetFont('times','B',10);
        $pdf->SetFillColor(220,220,220);
        $pdf->Cell(200,5,"REGISTROS COMPLEMENTARES",1,0,'C', true);
        $pdf->Cell(20,5,"Série",1,0,'C');
        $pdf->Cell(15,5,"Ano",1,0,'C');
        $pdf->Cell(100,5,"Estabelecimento",1,0,'C');
        $pdf->Cell(55,5,"Município",1,0,'C');
        $pdf->Cell(10,5,"UF",1,1,'C');

        $complementar = HistoricoRegistroComplementar::where('historico_id','=',$historico_id)->load();
        if($complementar)
        {   
            $pdf->SetFont('times','',10);
            foreach($complementar as $dt ){
                $serie = new Serie($dt->serie_id);
                $pdf->Cell(20,5,substr($serie->nome, 0,8),1,0,'C');
                $pdf->Cell(15,5,$dt->ano,1,0,'C');
                $pdf->Cell(100,5,$dt->estabelecimento,1,0,'C');	
                $pdf->Cell(55,5,$dt->municipio,1,0,'C');
                $pdf->Cell(10,5,$dt->uf,1,1,'C');
            }
        }

        $pdf->ln();
        $pdf->SetFont('times','B',10);
        $pdf->MultiCell(200,5,"Observação: ".$historico->observacao, '0', 'L');
        $pdf->ln();
        $pdf->SetFillColor(220,220,220);
        $pdf->Cell(200,5,"CERTIFICADO", '1',1,'C', true);
        $pdf->SetFont('times','',9);
        $pdf->MultiCell(200,5,"Certificamos que ".$aluno->nome." cursou o ".$historico->serie->nome." no Ano Letivo de ".$historico->ano_letivo->ano." sendo ".$historico->situacao.", conforme os resultados deste histórico escolar."."\n", '0', 'J');

        $pdf->Output( $this->arquivo, "F");
        TTransaction::close();
    }

}