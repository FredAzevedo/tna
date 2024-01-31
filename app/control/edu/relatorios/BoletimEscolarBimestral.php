<?php

use Carbon\Carbon;

class BoletimEscolarBimestral 
{
    private $arquivo = "/tmp/BoletimEscolarBimestral.pdf";

    public function __construct($param)
    {
        $this->onGerarBoletim($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarBoletim($param)
    {
    
        $aluno_id = $param['aluno_id'];
        $anoletivo_id = $param['anoletivo_id'];
        $unit_id = TSession::getValue('userunitid');

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
            //$pdf->SetFillColor(211,211,211);
            $pdf->SetTextColor(245,255,250);
            $pdf->SetFillColor(0,0,0);
            $pdf->SetFont('helvetica','B',12);
            $pdf->Cell(275,5,"BOLETIM ESCOLAR",0,0,'C', true);

            $pdf->SetTextColor(0,0,0);
            $pdf->SetFillColor(211,211,211);

            $aluno = new Aluno($aluno_id);
            $nascimento = Carbon::parse($aluno->nascimento)->format('d/m/Y');

            $pdf->SetFont('helvetica','B',10);
            $pdf->SetXY(10, 42);
            $pdf->Cell(210,5,"Aluno:",0,0,'L');

            $pdf->SetFont('helvetica','',9);
            $pdf->SetXY(22, 42);
            $pdf->Cell(210,5,$aluno->nome,0,0,'L');
            
            $apontamento = Apontamento::where('anoletivo_id','=',$anoletivo_id)->where('aluno_id','=',$aluno_id)->first();

            $pdf->SetFont('helvetica','B',10);
            $pdf->SetXY(10, 47);
            $pdf->Cell(210,5,"Série:",0,0,'L');

            $pdf->SetFont('helvetica','',9);
            $pdf->SetXY(20, 47);
            $pdf->Cell(210,5,$apontamento->serie->nome,0,0,'L');

            $pdf->SetFont('helvetica','B',10);
            $pdf->SetXY(10, 52);
            $pdf->Cell(210,5,"Turma:",0,0,'L');

            $pdf->SetFont('helvetica','',9);
            $pdf->SetXY(23, 52);
            $pdf->Cell(210,5,$apontamento->turma->nome,0,0,'L');

            $pdf->SetFont('helvetica','B',10);
            $pdf->SetXY(10, 57);
            $pdf->Cell(210,5,"Ano:",0,0,'L');

            $pdf->SetFont('helvetica','',9);
            $pdf->SetXY(19, 57);
            $pdf->Cell(210,5,$apontamento->ano_letivo->ano,0,0,'L');

            $pdf->SetFont('helvetica','B',10);
            $pdf->SetXY(31, 57);
            $pdf->Cell(210,5,"Turno:",0,0,'L');

            $pdf->SetFont('helvetica','',9);
            $pdf->SetXY(43, 57);
            $pdf->Cell(210,5,$apontamento->turno->nome,0,0,'L');

            $pdf->SetFont('helvetica','B',10);
            $pdf->SetXY(130, 42);
            $pdf->Cell(210,5,"Nascimento:",0,0,'L');

            $pdf->SetFont('helvetica','',10);
            $pdf->SetXY(152, 42);
            $pdf->Cell(210,5,$nascimento,0,0,'L');

            // --------------------------------
            $pdf->SetTextColor(245,255,250);
            $pdf->SetFillColor(0,0,0);
            
            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(10, 63);
            $pdf->Cell(45,5,"",1,0,'C', true);

            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(55, 63);
            $pdf->Cell(39.04,5,"Presença",1,0,'C', true);

            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(94.04, 63);
            $pdf->Cell(48.8,5,"Faltas",1,0,'C', true);

            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(142.84, 63);
            $pdf->Cell(126.88,5,"Notas",1,0,'C', true);

            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(269.76, 63);
            $pdf->Cell(15.24,5,"",1,0,'C', true);

            // --------------------------------

            $pdf->SetTextColor(0,0,0);
            $pdf->SetFillColor(211,211,211);
            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(10, 68);
            $pdf->Cell(45,5,"Disciplina",1,0,'C', true);

            $pdf->SetFillColor(240,255,240);
            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(55, 68);
            $pdf->Cell(9.76,5,"1ªBim",1,0,'C', true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(64.76, 68);
            $pdf->Cell(9.76,5,"2ªBim",1,0,'C', true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(74.52, 68);
            $pdf->Cell(9.76,5,"3ªBim",1,0,'C', true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(84.28, 68);
            $pdf->Cell(9.76,5,"4ªBim",1,0,'C', true);

            $pdf->SetFillColor(255,255,224);
            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(94.04, 68);
            $pdf->Cell(9.76,5,"1ªBim",1,0,'C', true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(103.80, 68);
            $pdf->Cell(9.76,5,"2ªBim",1,0,'C', true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(113.56, 68);
            $pdf->Cell(9.76,5,"3ªBim",1,0,'C', true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(123.32, 68);
            $pdf->Cell(9.76,5,"4ªBim",1,0,'C', true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(133.08, 68);
            $pdf->Cell(9.76,5,"Freq.",1,0,'C', true);

            $pdf->SetFillColor(175,238,238);
            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(142.84, 68);
            $pdf->Cell(9.76,5,"1ªBim",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(152.60, 68);
            $pdf->Cell(9.76,5,"2ªBim",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(162.36, 68);
            $pdf->Cell(9.76,5,"MS1",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(172.12, 68);
            $pdf->Cell(9.76,5,"REC",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(181.88, 68);
            $pdf->Cell(9.76,5,"MDS1",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(191.64, 68);
            $pdf->Cell(9.76,5,"3ªBim",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(201.40, 68);
            $pdf->Cell(9.76,5,"4ªBim",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(211.16, 68);
            $pdf->Cell(9.76,5,"MS2",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(220.92, 68);
            $pdf->Cell(9.76,5,"REC",1,0,'C', true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(230.68, 68);
            $pdf->Cell(9.76,5,"MDS2",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(240.44, 68);
            $pdf->Cell(9.76,5,"MA",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(250.2, 68);
            $pdf->Cell(9.76,5,"PF",1,0,'C',true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(259.96, 68);
            $pdf->Cell(9.76,5,"MFA",1,0,'C',true);

            $pdf->SetFillColor(238,232,170);
            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(269.76, 68);
            $pdf->Cell(15.2,5,"Result.",1,0,'C',true);

            $conn = TTransaction::get();
            $notas = $conn->prepare( "SELECT D.nome as disciplina , AP.p_1bim , AP.p_2bim , AP.p_3bim , AP.p_4bim , AP.f_1bim , AP.f_2bim , AP.f_3bim , AP.f_4bim , AP.ft_anual, AP.n_1bim, 
            AP.n_2bim, AP.MS1 , AP.REC12, AP.MDS1, AP.n_3bim, AP.n_4bim, AP.MS2, AP.REC34, AP.MDS2, AP.MA, AP.PF, AP.MFA, AP.tf_anual, AP.resultado
            FROM aluno A, matricula M, apontamento AP, disciplina D, ano_letivo T
            WHERE M.aluno_id = A.id 
            AND AP.aluno_id = A.id
            AND AP.disciplina_id = D.id
            AND AP.anoletivo_id = T.id 
            AND AP.serie_id = M.serie_id
            AND AP.turma_id = M.turma_id 
            AND AP.turno_id = M.turno_id 
            AND AP.aluno_id = ?
            AND AP.anoletivo_id = ?
            AND AP.unit_id = ?
            GROUP BY D.id
            ORDER BY D.ordem");

            $notas->execute(array($aluno_id,$anoletivo_id,$unit_id));
            
            $pdf->SetFont('helvetica','',7);
            $pdf->SetXY(10, 73);
            $result = "";
            $cont = 0;
            $N_1BIM = 0;
            $N_2BIM = 0;
            $N_3BIM = 0;
            $N_4BIM = 0;
            $MFA = 0;
            $tf = 0;
            
            foreach ($notas as $resultado ){
                
                //if(empty($resultado['MFA']) || $resultado['MFA'] == 0.0){$result = "";}elseif($resultado['MFA'] < 5){$result = "Reprovado";}else{$result = "Aprovado";}
                
                switch ($resultado['resultado']) {
                    case "AP":
                         $result = "Aprov.";
                        break;
                    case "RP":
                        $result = "Reprov.";
                        break;
                    case "TR":
                        $result = "Transf.";
                        break;
                    case "EV":
                        $result = "Evadido";
                        break;
                    case null:
                        $result = "";
                        break;
                }

                $pdf->SetFillColor(211,211,211);
                $pdf->Cell(45,5,$resultado['disciplina'],1,0,'L', true);
                $pdf->SetFillColor(240,255,240);
                $pdf->Cell(9.76,5,$resultado['p_1bim'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['p_2bim'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['p_3bim'],1,0,'C', true);	
                $pdf->Cell(9.76,5,$resultado['p_4bim'],1,0,'C', true);
                $pdf->SetFillColor(255,255,224);
                $pdf->Cell(9.76,5,$resultado['f_1bim'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['f_2bim'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['f_3bim'],1,0,'C', true);	
                $pdf->Cell(9.76,5,$resultado['f_4bim'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['ft_anual']."%",1,0,'C', true);
                $pdf->SetFillColor(175,238,238);
                $pdf->Cell(9.76,5,$resultado['n_1bim'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['n_2bim'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['MS1'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['REC12'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['MDS1'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['N_3BIM'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['N_4BIM'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['MS2'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['REC34'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['MDS2'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['MA'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['PF'],1,0,'C', true);
                $pdf->Cell(9.76,5,$resultado['MFA'],1,0,'C', true);
                $pdf->SetFillColor(238,232,170);
                $pdf->Cell(15.2,5,$result,1,1,'C', true);
                
                $cont++;
                $N_1BIM = $resultado['n_1bim'] + $N_1BIM;
                $N_2BIM = $resultado['n_2bim'] + $N_2BIM;
                $N_3BIM = $resultado['n_3bim'] + $N_3BIM;
                $N_4BIM = $resultado['n_4bim'] + $N_4BIM;
                $MFA = $resultado['MFA'] + $MFA;
                
                $tf = $tf + $resultado['tf_anual'];
                
            }

            $total_tf = (1000 - $tf) * 0.1;

            $pdf->SetFont('helvetica','B',8);
            $pdf->SetX(10);
            $pdf->Cell(133.04,5,"Frequência Anual:",1,0,'L');

            $pdf->SetX(133.04);
            $pdf->Cell(10,5,$total_tf."%",0,0,'C');

            if($cont != 0){
                $N_1BIM = $N_1BIM / $cont;
                $N_2BIM = $N_2BIM / $cont;
                $N_3BIM = $N_3BIM / $cont;
                $N_4BIM = $N_4BIM / $cont;
                $MFA = $MFA / $cont;
            }

            $pdf->SetTextColor(245,255,250);
            $pdf->SetFillColor(0,0,0);
            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(200, 42);
            $pdf->Cell(85,5,"Total Média Geral",0,0,'C', true);

            $pdf->SetTextColor(0,0,0);
            $pdf->SetFillColor(211,211,211);

            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(200, 47);
            $pdf->Cell(17,5,"1ªBim",1,0,'C');

            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(217, 47);
            $pdf->Cell(17,5,"2ªBim",1,0,'C');

            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(234, 47);
            $pdf->Cell(17,5,"3ªBim",1,0,'C');

            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(251, 47);
            $pdf->Cell(17,5,"4ªBim",1,0,'C');

            $pdf->SetFont('helvetica','B',9);
            $pdf->SetXY(268, 47);
            $pdf->Cell(17,5,"MFA",1,0,'C');

            $pdf->SetFont('helvetica','',10);
            $pdf->SetXY(200, 52);
            $pdf->Cell(17,5,round($N_1BIM,1),1,0,'C');

            $pdf->SetFont('helvetica','',10);
            $pdf->SetXY(217, 52);
            $pdf->Cell(17,5,round($N_2BIM,1),1,0,'C');

            $pdf->SetFont('helvetica','',10);
            $pdf->SetXY(234, 52);
            $pdf->Cell(17,5,round($N_3BIM,1),1,0,'C');

            $pdf->SetFont('helvetica','',10);
            $pdf->SetXY(251, 52);
            $pdf->Cell(17,5,round($N_4BIM,1),1,0,'C');

            $pdf->SetFont('helvetica','',10);
            $pdf->SetXY(268, 52);
            $pdf->Cell(17,5,round($MFA,1),1,0,'C');
            

            //Legendas
            $pdf->SetFont('helvetica','i',8);
            $pdf->SetXY(10, 158);
            $pdf->Cell(51,5,"MS1: Média do 1ª Semestre",0,0,'L');

            $pdf->SetFont('helvetica','i',8);
            $pdf->SetXY(71, 158);
            $pdf->Cell(51,5,"MS2: Média do 2ª Semestre",0,0,'L');

            $pdf->SetFont('helvetica','i',8);
            $pdf->SetXY(122, 158);
            $pdf->Cell(51,5,"PF: Prova Final",0,0,'C');

            $pdf->SetFont('helvetica','i',8);
            $pdf->SetXY(173, 158);
            $pdf->Cell(51,5,"MFA: Média Final Anual (MA x 2 + PF x 1/3)",0,0,'L');

            $pdf->SetFont('helvetica','i',8);
            $pdf->SetXY(234, 158);
            $pdf->Cell(51,5,"MDS: Média Definitiva do Semestre",0,0,'R');

            $pdf->SetFont('helvetica','B',8);
            $pdf->SetXY(10, 163);
            $pdf->SetFillColor(200);
            $pdf->Cell(275,5,"Resultado Final:",0,0,'L', true);

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(62.5, 163);
            $pdf->Cell(42.5,5,"APROVADO:( )",0,0,'C');

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(105, 163);
            $pdf->Cell(42.5,5,"REPROVADO:( )",0,0,'C');   
                    
            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(147.5, 163);
            $pdf->Cell(42.5,5,"MAT. CANCELADA:( )",0,0,'C');  

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(190.5, 163);
            $pdf->Cell(42.5,5,"EVADIDO:( )",0,0,'C');  		   
                    
            $pdf->SetFont('helvetica','',9);
            $pdf->SetXY(232.5, 163);
            $pdf->Cell(42.5,5,"TRANSFERIDO:( )",0,0,'C'); 

            $data=date("d/m/Y");
            $pdf->SetFont('helvetica','B',8);
            $pdf->SetXY(55, 170);
            $pdf->Cell(51,5,"Natal ".$data,0,0,'C');

            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(40, 178);
            $pdf->Cell(80,5,"__________________________________",0,0,'C');

            $pdf->SetFont('helvetica','I',8);
            $pdf->SetXY(40, 183);
            $pdf->Cell(80,5,"Secretário (a)",0,0,'C');


            $pdf->SetFont('helvetica','',8);
            $pdf->SetXY(180, 178);
            $pdf->Cell(80,5,"__________________________________",0,0,'C');

            $pdf->SetFont('helvetica','I',8);
            $pdf->SetXY(180, 183);
            $pdf->Cell(80,5,"Diretor (a)",0,0,'C');


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
