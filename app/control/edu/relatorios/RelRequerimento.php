<?php 

class RelRequerimento 
{

    private $arquivo = "/tmp/RequerimentoEscolar.pdf";

    public function __construct($param)
    {
        $this->onGerarRequerimento($param);
    }

    public function get_arquivo(){
        return $this->arquivo;
    }

    public function onGerarRequerimento($param)
    {

        $aluno_id = $param['aluno_id'];
        $anoletivo_id = $param['anoletivo_id'];

        TTransaction::open('sample');

        //Aluno
        $aluno = new Aluno($aluno_id);

        //START TCPDF
        include_once( 'vendor/autoload.php' );

        $pdf = new TCPDF('P','mm','A4');
        $pdf->SetPrintHeader(false);
        $pdf->Open();
        $pdf->AddPage();
        $pdf->SetFont('times','',12);
         
        //COLÉGIO CAMINHO DO SABER
        $pdf->SetFont('times','B',18);
        $pdf->SetXY(70, 8);
        $pdf->Cell(75,8,"COLÉGIO CAMINHO DO SABER",0,0,'C');
        
        $pdf->SetFont('times','B',10);
        $pdf->SetXY(70,15);
        $pdf->Cell(75,5,"Educação Infantil Pré-escolar e Ensino Fundamental",0,0,'C');
         
        $pdf->SetFont('times','',10);
        $pdf->SetXY(70, 20);
        $pdf->Cell(75,5,"Rua do Mamoeiro Nº 11 Conj. Potengi. Bairro Potengi",0,0,'C');
        
        $pdf->SetFont('times','',10);
        $pdf->SetXY(70, 25);
        $pdf->Cell(75,5,"CEP: 59.120-600, Natal-RN. Fone: (0xx84) 3661-5831",0,0,'C');
        
        $pdf->SetFont('times','',10);
        $pdf->SetXY(70, 30);
        $pdf->Cell(75,5,"CNPJ: 07.148.818/0001-03",0,0,'C');
        
        
        // //Nº Req:
        // $pdf->SetFont('times','',12);
        // $pdf->SetXY(5, 40);
        // $pdf->Cell(0,0,"Nº Req:",0,0,'L');
        // $pdf->SetXY(20, 40);
        // $pdf->Cell(0,0,"{IDREQUERIMENTO}",0,0,'L');
        
        // //Requerimento de Matrícula nº:
        // $pdf->SetFont('times','',12);
        // $pdf->SetXY(5, 45);
        // $pdf->Cell(0,0,"Requerimento de Matrícula nº:{IDMATRICULA}",0,0,'L');
        
        
        //DADOS DO ALUNO
        $pdf->SetFont('times','B',12);
        $pdf->SetXY(5, 50);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetDrawColor(220,220,220);
        $pdf->Cell(200,6,"DADOS DO ALUNO",1,0,'C', true);
        
        //Nome do Aluno(a):
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 65);
        $pdf->Cell(0,0,"Nome do Aluno(a):",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(40, 65);
        $pdf->Cell(0,0,$aluno->nome,0,0,'L');
        
        //Foto do aluno
        // $imgdata = base64_encode('');
        // $foto = base64_decode($imgdata);
        // $pdf->SetXY(5, 65);
        // $pdf->setJPEGQuality(75);
        //$pdf->Image('@'.$foto,175, 15, '30', '35', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        
        //Data de Nascimento:
        $nascimento = date("d/m/Y", strtotime($aluno->nascimento));

        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 70);
        $pdf->Cell(0,0,"Data de Nascimento:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(44, 70);
        $pdf->Cell(0,0,$nascimento,0,0,'L');
        
        
        //Cidade: 
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(100, 70);
        $pdf->Cell(0,0,"Cidade:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(116, 70);
        $pdf->Cell(0,0,$aluno->cidade,0,0,'L');
        
        
        //UF:: 
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(160, 70);
        $pdf->Cell(0,0,"UF:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(168, 70);
        $pdf->Cell(0,0,$aluno->uf,0,0,'L');
        
        
        //Nº do Registro de Nascimento / RG: 
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 75);
        $pdf->Cell(0,0,"Nº do Registro:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(33, 75);
        $pdf->Cell(0,0,$aluno->numero_registro,0,0,'L');
        
        
        //Folha: 
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(120, 75);
        $pdf->Cell(0,0,"Folha:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(132, 75);
        $pdf->Cell(0,0,$aluno->folha,0,0,'L');
        
        //Livro:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(160, 75);
        $pdf->Cell(0,0,"Livro:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(172, 75);
        $pdf->Cell(0,0,$aluno->livro,0,0,'L');
        
        
        //Nome do Cartório:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 80);
        $pdf->Cell(0,0,"Nome do Cartório:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(40, 80);
        $pdf->Cell(0,0,$aluno->cartorio_nome,0,0,'L');
        
        
        //Data:
        $data = date("d/m/Y", strtotime($aluno->data_registro));
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(100, 80);
        $pdf->Cell(0,0,"Data:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(111, 80);
        $pdf->Cell(0,0,$data,0,0,'L');
        
        //Município do Cartório:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 85);
        $pdf->Cell(0,0,"Município do Cartório:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(48, 85);
        $pdf->Cell(0,0,$aluno->cartorio_municipio,0,0,'L');
        
        
        //UF:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(100, 85);
        $pdf->Cell(0,0,"UF:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(108, 85);
        $pdf->Cell(0,0,$aluno->cartorio_uf,0,0,'L');
        
        //CPF:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(115, 85);
        $pdf->Cell(0,0,"CPF:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(125, 85);
        $pdf->Cell(0,0,$aluno->cpf,0,0,'L');
        
        //Nº do Registro de Nascimento / RG: 
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(160, 85);
        $pdf->Cell(0,0,"RG:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(168, 85);
        $pdf->Cell(0,0,$aluno->rg,0,0,'L');
        
        
        //NOME DO RESPONSÁVEL
        $pdf->SetFont('times','B',12);
        $pdf->SetXY(5, 100);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetDrawColor(220,220,220);
        $pdf->Cell(200,6,"NOME DO RESPONSÁVEL",1,0,'C', true);
        
        
        $pai = new Responsavel($aluno->pai_responsavel_id);
        //Nome do Pai:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 110);
        $pdf->Cell(0,0,"Nome do Pai:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(30, 110);
        $pdf->Cell(0,0,$pai->nome,0,0,'L');
        
        //Profissão: 
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 115);
        $pdf->Cell(0,0,"Profissão:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(24, 115);
        $pdf->Cell(0,0,$pai->profissao,0,0,'L');
        
        //Local de Trabalho:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(100, 115);
        $pdf->Cell(0,0,"Local de Trabalho:",0,0,'L');
        $pdf->SetFont('times','',10);
        $pdf->SetXY(136, 115);
        $pdf->Cell(0,0,$pai->local_trabalho,0,0,'L');
        
        //RG:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 120);
        $pdf->Cell(0,0,"RG:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(13, 120);
        $pdf->Cell(0,0,$pai->rg,0,0,'L');
        
        //CPF::
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(100, 120);
        $pdf->Cell(0,0,"CPF:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(110, 120);
        $pdf->Cell(0,0,$pai->cpf,0,0,'L');
        
        //EMAIL::
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 125);
        $pdf->Cell(0,0,"E-mail:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(19, 125);
        $pdf->Cell(0,0,$pai->email,0,0,'L');
        
        $mae = new Responsavel($aluno->mae_responsavel_id);
        //Nome da Mãe:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 130);
        $pdf->Cell(0,0,"Nome da Mãe:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(32, 130);
        $pdf->Cell(0,0,$mae->nome,0,0,'L');;
        
        //Profissão:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 135);
        $pdf->Cell(0,0,"Profissão:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(24, 135);
        $pdf->Cell(0,0,$mae->profissao,0,0,'L');
        
        //Local de Trabalho:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(100, 135);
        $pdf->Cell(0,0,"Local de Trabalho:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(136, 135);
        $pdf->Cell(0,0,$mae->local_trabalho,0,0,'L');
        
        //RG:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 140);
        $pdf->Cell(0,0,"RG:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(13, 140);
        $pdf->Cell(0,0,$mae->rg,0,0,'L');
        
        //CPF::
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(100, 140);
        $pdf->Cell(0,0,"CPF:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(110, 140);
        $pdf->Cell(0,0,$mae->cpf,0,0,'L');
        
        //EMAIL::
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 145);
        $pdf->Cell(0,0,"E-mail:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(19, 145);
        $pdf->Cell(0,0,$mae->email,0,0,'L');
        
        //TELEFONE PARA CONTATO
        $pdf->SetFont('times','B',12);
        $pdf->SetXY(5, 155);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetDrawColor(220,220,220);
        $pdf->Cell(200,6,"TELEFONE PARA CONTATO",1,0,'C', true);
        
        //Residencial:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 170);
        $pdf->Cell(0,0,"Celular:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(22, 170);
        $pdf->Cell(0,0,(!empty($mae->telefone1)) ? $mae->telefone1 : '',0,0,'L');
        
        //Nome:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(105, 170);
        $pdf->Cell(0,0,"Nome:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(119, 170);
        $pdf->Cell(0,0,(!empty($mae->telefone1)) ? $mae->nome : '',0,0,'L');
        
        
        //Celular:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 175);
        $pdf->Cell(0,0,"Celular:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(22, 175);
        $pdf->Cell(0,0,(!empty($pai->telefone1)) ? $pai->telefone1 : '',0,0,'L');
        
        
        //Nome:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(105, 175);
        $pdf->Cell(0,0,"Nome:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(119, 175);
        $pdf->Cell(0,0,(!empty($pai->telefone1)) ? $pai->nome : '',0,0,'L');
        
        
        //Celular:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 180);
        $pdf->Cell(0,0,"Celular:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(22, 180);
        $pdf->Cell(0,0,(!empty($mae->telefone2)) ? $mae->telefone2 : '',0,0,'L');
        
        //Nome:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(105, 180);
        $pdf->Cell(0,0,"Nome:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(119, 180);
        $pdf->Cell(0,0,(!empty($mae->telefone2)) ? $mae->nome : '',0,0,'L');
        
        
        //Celular:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(5, 185);
        $pdf->Cell(0,0,"Celular:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(22, 185);
        $pdf->Cell(0,0,(!empty($pai->telefone2)) ? $pai->telefone2 : '',0,0,'L');
        
        //Nome:
        $pdf->SetFont('times','b',12);
        $pdf->SetXY(105, 185);
        $pdf->Cell(0,0,"Nome:",0,0,'L');
        $pdf->SetFont('times','',12);
        $pdf->SetXY(119, 185);
        $pdf->Cell(0,0,(!empty($pai->telefone2)) ? $pai->nome : '',0,0,'L');
        
        //OBSERVAÇÕES
        $pdf->SetFont('times','B',12);
        $pdf->SetXY(5, 195);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetDrawColor(220,220,220);
        $pdf->Cell(200,6,"OBSERVAÇÕES",1,0,'C', true);
        
        $pdf->ln(10);
        
        $pdf->SetX(5);
        //$pdf->SetDrawColor(0,0,0);
        $pdf->SetFont('times','',10);
        $pdf->MultiCell(200, 5,$aluno->observacao."\n", 0, 'J', 0, 2, '' ,'');
        
        
        //Linhas
        /*
        $pdf->SetDrawColor(0,0,0);
        $pdf->Line(5,210,205,210);
        $pdf->Line(5,215,205,215);
        $pdf->Line(5,220,205,220);
        $pdf->Line(5,225,205,225);
        */
        
        //Responsável pela Matrícula
        $pdf->SetXY(5, 245);
        $pdf->Cell(0,0,"_____________________________________",0,0,'L');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(25, 250);
        $pdf->Cell(0,0,"Responsável pela Matrícula",0,0,'L');
        
        
        //Diretor
        $pdf->SetXY(5, 268);
        $pdf->Cell(0,0,"_____________________________________",0,0,'C');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(95, 273);
        $pdf->Cell(0,0,"Diretor (a)",0,0,'L');
        
        
        
        //Secretário(a)
        $pdf->SetXY(5, 245);
        $pdf->Cell(0,0,"_____________________________________",0,0,'R');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(165, 250);
        $pdf->Cell(0,0,"Secretário(a)",0,0,'L');
        
        
        //$pdf->Output("arquivo.pdf");
        
        
        //Página 2. ==========================================================================
        
        
        
        $pdf->AddPage('','');
        
        //ACEITAMOS AS NORMAS DESTA ESCOLA, PEDE MATRÍCULA
        $pdf->SetFont('times','',10);
        $pdf->SetXY(5, 20);
        $pdf->Cell(0,0,"ACEITAMOS AS NORMAS DESTA ESCOLA, PEDE MATRÍCULA",0,0,'C');
        
        //dado1
        $pdf->Rect(5,35,90,38,'T');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(5, 40);
        $pdf->Cell(0,0,"Ano:_________Série:____________Ensino:__________________",0,0);
        $pdf->SetXY(5, 45);
        $pdf->Cell(0,0,"Natal:_________/_________________________/______________",0,0);
        $pdf->SetXY(5, 50);
        $pdf->Cell(0,0,"Endereço:_____________________________________________",0,0);
        $pdf->SetXY(5, 55);
        $pdf->Cell(0,0,"________Nº_______Bairro:_______________________________",0,0);
        $pdf->SetXY(5, 60);
        $pdf->Cell(0,0,"CEP:____________________________ ",0,0);
        $pdf->SetXY(5, 65);
        $pdf->Cell(0,0,"___________________ ___________________________________",0,0);
        $pdf->SetXY(30, 69);
        $pdf->Cell(0,0,"Ass. do Responsável pela Matrícula",0,0);
        
        
        
        $pdf->Rect(115,35,90,38,'T');
        $pdf->SetFont('times','B',9);
        $pdf->SetXY(115, 40);
        $pdf->Cell(90,5,"TRANSFERÊNCIA/CANCELAMENTO DE MATRÍCULA",0,0,'C');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(115, 50);
        $pdf->Cell(90,0,"___________________ ___________________________________",0,0,'C');
        $pdf->SetXY(140, 53);
        $pdf->Cell(0,0,"Ass. do Responsável pela Matrícula",0,0);
        $pdf->SetXY(115, 60);
        $pdf->Cell(90,0,"Data:_________/_________________________/______________",0,0,'C');
        $pdf->SetXY(115, 65);
        $pdf->Cell(90,0,"_______________________________________________________",0,0,'C');
        $pdf->SetXY(115, 68);
        $pdf->Cell(90,0,"Ass. do Funcionário",0,0,'C');
        
        $pdf->Rect(5,80,90,38,'T');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(5, 85);
        $pdf->Cell(0,0,"Ano:_________Série:____________Ensino:__________________",0,0);
        $pdf->SetXY(5, 90);
        $pdf->Cell(0,0,"Natal:_________/_________________________/______________",0,0);
        $pdf->SetXY(5, 95);
        $pdf->Cell(0,0,"Endereço:_____________________________________________",0,0);
        $pdf->SetXY(5, 100);
        $pdf->Cell(0,0,"________Nº_______Bairro:_______________________________",0,0);
        $pdf->SetXY(5, 105);
        $pdf->Cell(0,0,"CEP:____________________________ ",0,0);
        $pdf->SetXY(5, 110);
        $pdf->Cell(0,0,"___________________ ___________________________________",0,0);
        $pdf->SetXY(30, 114);
        $pdf->Cell(0,0,"Ass. do Responsável pela Matrícula",0,0);
        
        
        
        $pdf->Rect(115,80,90,38,'T');
        $pdf->SetFont('times','B',9);
        $pdf->SetXY(115, 85);
        $pdf->Cell(90,5,"TRANSFERÊNCIA/CANCELAMENTO DE MATRÍCULA",0,0,'C');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(115, 95);
        $pdf->Cell(90,0,"___________________ ___________________________________",0,0,'C');
        $pdf->SetXY(140, 98);
        $pdf->Cell(0,0,"Ass. do Responsável pela Matrícula",0,0);
        $pdf->SetXY(115, 105);
        $pdf->Cell(90,0,"Data:_________/_________________________/______________",0,0,'C');
        $pdf->SetXY(115, 110);
        $pdf->Cell(90,0,"_______________________________________________________",0,0,'C');
        $pdf->SetXY(115, 113);
        $pdf->Cell(90,0,"Ass. do Funcionário",0,0,'C');
        
        
        
        $pdf->Rect(5,125,90,38,'T');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(5, 130);
        $pdf->Cell(0,0,"Ano:_________Série:____________Ensino:__________________",0,0);
        $pdf->SetXY(5, 135);
        $pdf->Cell(0,0,"Natal:_________/_________________________/______________",0,0);
        $pdf->SetXY(5, 140);
        $pdf->Cell(0,0,"Endereço:_____________________________________________",0,0);
        $pdf->SetXY(5, 145);
        $pdf->Cell(0,0,"________Nº_______Bairro:_______________________________",0,0);
        $pdf->SetXY(5, 150);
        $pdf->Cell(0,0,"CEP:____________________________ ",0,0);
        $pdf->SetXY(5, 155);
        $pdf->Cell(0,0,"___________________ ___________________________________",0,0);
        $pdf->SetXY(30, 159);
        $pdf->Cell(0,0,"Ass. do Responsável pela Matrícula",0,0);
        
        
        
        $pdf->Rect(115,125,90,38,'T');
        $pdf->SetFont('times','B',9);
        $pdf->SetXY(115, 130);
        $pdf->Cell(90,5,"TRANSFERÊNCIA/CANCELAMENTO DE MATRÍCULA",0,0,'C');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(115, 140);
        $pdf->Cell(90,0,"___________________ ___________________________________",0,0,'C');
        $pdf->SetXY(140, 143);
        $pdf->Cell(0,0,"Ass. do Responsável pela Matrícula",0,0);
        $pdf->SetXY(115, 150);
        $pdf->Cell(90,0,"Data:_________/_________________________/______________",0,0,'C');
        $pdf->SetXY(115, 155);
        $pdf->Cell(90,0,"_______________________________________________________",0,0,'C');
        $pdf->SetXY(115, 158);
        $pdf->Cell(90,0,"Ass. do Funcionário",0,0,'C');
        
        
        $pdf->Rect(5,170,90,38,'T');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(5, 175);
        $pdf->Cell(0,0,"Ano:_________Série:____________Ensino:__________________",0,0);
        $pdf->SetXY(5, 180);
        $pdf->Cell(0,0,"Natal:_________/_________________________/______________",0,0);
        $pdf->SetXY(5, 185);
        $pdf->Cell(0,0,"Endereço:_____________________________________________",0,0);
        $pdf->SetXY(5, 190);
        $pdf->Cell(0,0,"________Nº_______Bairro:_______________________________",0,0);
        $pdf->SetXY(5, 195);
        $pdf->Cell(0,0,"CEP:____________________________ ",0,0);
        $pdf->SetXY(5, 200);
        $pdf->Cell(0,0,"___________________ ___________________________________",0,0);
        $pdf->SetXY(30, 204);
        $pdf->Cell(0,0,"Ass. do Responsável pela Matrícula",0,0);
        
        
        
        $pdf->Rect(115,170,90,38,'T');
        $pdf->SetFont('times','B',9);
        $pdf->SetXY(115, 175);
        $pdf->Cell(90,5,"TRANSFERÊNCIA/CANCELAMENTO DE MATRÍCULA",0,0,'C');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(115, 185);
        $pdf->Cell(90,0,"___________________ ___________________________________",0,0,'C');
        $pdf->SetXY(140, 188);
        $pdf->Cell(0,0,"Ass. do Responsável pela Matrícula",0,0);
        $pdf->SetXY(115, 195);
        $pdf->Cell(90,0,"Data:_________/_________________________/______________",0,0,'C');
        $pdf->SetXY(115, 200);
        $pdf->Cell(90,0,"_______________________________________________________",0,0,'C');
        $pdf->SetXY(115, 203);
        $pdf->Cell(90,0,"Ass. do Funcionário",0,0,'C');
        
        
        $pdf->Rect(5,215,90,38,'T');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(5, 220);
        $pdf->Cell(0,0,"Ano:_________Série:____________Ensino:__________________",0,0);
        $pdf->SetXY(5, 225);
        $pdf->Cell(0,0,"Natal:_________/_________________________/______________",0,0);
        $pdf->SetXY(5, 230);
        $pdf->Cell(0,0,"Endereço:_____________________________________________",0,0);
        $pdf->SetXY(5, 235);
        $pdf->Cell(0,0,"________Nº_______Bairro:_______________________________",0,0);
        $pdf->SetXY(5, 240);
        $pdf->Cell(0,0,"CEP:____________________________ ",0,0);
        $pdf->SetXY(5, 245);
        $pdf->Cell(0,0,"___________________ ___________________________________",0,0);
        $pdf->SetXY(30, 249);
        $pdf->Cell(0,0,"Ass. do Responsável pela Matrícula",0,0);
        
        
        
        $pdf->Rect(115,215,90,38,'T');
        $pdf->SetFont('times','B',9);
        $pdf->SetXY(115, 220);
        $pdf->Cell(90,5,"TRANSFERÊNCIA/CANCELAMENTO DE MATRÍCULA",0,0,'C');
        $pdf->SetFont('times','',8);
        $pdf->SetXY(115, 230);
        $pdf->Cell(90,0,"___________________ ___________________________________",0,0,'C');
        $pdf->SetXY(140, 233);
        $pdf->Cell(0,0,"Ass. do Responsável pela Matrícula",0,0);
        $pdf->SetXY(115, 240);
        $pdf->Cell(90,0,"Data:_________/_________________________/______________",0,0,'C');
        $pdf->SetXY(115, 245);
        $pdf->Cell(90,0,"_______________________________________________________",0,0,'C');
        $pdf->SetXY(115, 248);
        $pdf->Cell(90,0,"Ass. do Funcionário",0,0,'C');
        
        $pdf->Output( $this->arquivo, "F");
        TTransaction::close();
    }

}