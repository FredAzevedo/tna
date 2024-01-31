<?php 
class ReportCabecalhoFPDF extends FPDF
{
    private $logo;
    private $razao_social;
    private $nome_fantasia;
    private $endereco;
    private $cnpj;
    private $documento;
    private $telefones;
    private $ie;
    private $im;

    public function set_logo($logo = "")
    {
        $this->logo = "app/images/".$logo;    
    }
    
    public function set_razao_social($razao_social)
    {
        $this->razao_social = utf8_decode($razao_social);
    }
    
    public function set_nome_fantasia($nome_fantasia)
    {

        $this->nome_fantasia = utf8_decode($nome_fantasia);
    }

    public function set_endereco($endereco)
    {

        $this->endereco = utf8_decode($endereco);
    }

    public function set_cnpj($cnpj)
    {
        $this->cnpj = utf8_decode($cnpj);
    }

    public function set_documento($documento)
    {
        $this->documento = utf8_decode($documento);
    }

    public function set_telefones($telefones)
    {
        $this->telefones = $telefones;
    }

    public function set_ie($ie)
    {
        $this->ie = $ie;
    }

    public function set_im($im)
    {
        $this->im = $im;
    }

    public function Header()
    {   

        //$imageData =file_get_contents($this->logo);
        //$this->Image('@'.$imageData, '10', '10', 20, 20, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->Image('app/images/LogoWS.jpeg',10,8,25,'JPEG');
        //LINHA ESPAÇAMENTO SUPERIOR
        $this->SetFont('helvetica','',10);
        $this->SetXY(10, 8);
        $this->Cell(0,0,'',0,0,'P');
        $this->Line(10,8,200,8);
        $this->ln();
          
        //COLUNA 1 - LINHA 1
        $this->SetFont('helvetica','B',8);
        $this->SetXY(35, 10);
          $this->Cell(0,0,utf8_decode('Razão Social:'),0,0,'L');
          
        $this->SetFont('helvetica','',8);
        $this->SetXY(54, 10);
        $this->Cell(0,0,$this->razao_social,0,0,'L');

        //COLUNA 1 - LINHA 2
        $this->SetFont('helvetica','B',8);
        $this->SetXY(35, 14);
        $this->Cell(0,0,'Nome Fantasia:',0,0,'L');
          
        $this->SetFont('helvetica','',8);
        $this->SetXY(57, 14);
        $this->Cell(0,0,$this->nome_fantasia,0,0,'L');

        //COLUNA 1 - LINHA 3
        $this->SetFont('helvetica','B',8);
        $this->SetXY(35, 18);
        $this->Cell(0,0,utf8_decode('Endereço:'),0,0,'L');
          
        $this->SetFont('helvetica','',8);
        $this->SetXY(50, 18);
        $this->Cell(0,0,$this->endereco,0,0,'L');

        //COLUNA 1 - LINHA 4
        $this->SetFont('helvetica','B',8);
        $this->SetXY(35, 22);
        $this->Cell(0,0,'CNPJ:',0,0,'L');
          
        $this->SetFont('helvetica','',8);
        $this->SetXY(44, 22);
        $this->Cell(0,0,$this->cnpj,0,0,'L');
         
        //COLUNA 1 - LINHA 5
        $this->SetFont('helvetica','B',8);
        $this->SetXY(35, 26);
        $this->Cell(0,0,utf8_decode('Inscrição Municipal:'),0,0,'L');
          
        $this->SetFont('helvetica','',8);
        $this->SetXY(60, 26);
        $this->Cell(0,0,$this->im,0,0,'L'); 
          
        //COLUNA 2 - LINHA 1  
        $this->SetFont('helvetica','B',8);
        $this->SetXY(110, 14);
        $this->Cell(0,0,'Telefone(s):',0,0,'L');
          
        $this->SetFont('helvetica','',8);
        $this->SetXY(127, 14);
        $this->Cell(0,0,$this->telefones,0,0,'L');
        
        //COLUNA 2 - LINHA 3
        $this->SetFont('helvetica','B',8);
        $this->SetXY(110, 22);
        $this->Cell(0,0,utf8_decode('Inscrição Estadual:'),0,0,'L');
          
        $this->SetFont('helvetica','',8);
        $this->SetXY(137, 22);
        $this->Cell(0,0,$this->ie,0,0,'L');
        /*  
        //COLUNA 2 - LINHA 4
        $this->SetFont('helvetica','B',8);
        $this->SetXY(110, 26);
        $this->Cell(0,0,'Inscrição Municipal:',0,0,'L');
          
        $this->SetFont('helvetica','',8);
        $this->SetXY(138, 26);
        $this->Cell(0,0,$this->im,0,0,'L'); 
        
        //COLUNA 3 - LINHA 1          
        $this->SetFont('helvetica','',8);
        $this->SetXY(175, 10);
        $this->Cell(0,0,$campo2),0,0,'L');

        //COLUNA 3 - LINHA 2 
        $this->SetFont('helvetica','',8);
        $this->SetXY(175, 14);
        $this->Cell(0,0,$campo3),0,0,'L');

        //COLUNA 3 - LINHA 3          
        $this->SetFont('helvetica','',8);
        $this->SetXY(175, 18);
        $this->Cell(0,0,$campo4),0,0,'L');
        
        //COLUNA 3 - LINHA 4
                $this->SetFont('helvetica','',8);
        $this->SetXY(175, 22);
        $this->Cell(0,0,$campo5),0,0,'L');
         
        //COLUNA 3 - LINHA 5
        $this->SetFont('helvetica','',8);
        $this->SetXY(175, 26);
        $this->Cell(0,0,$campo6),0,0,'L');
         */
        // QRCODE,H : QR-CODE Best error correction
        /*$style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );  */
        
        //$this->write2DBarcode('#', 'QRCODE,H', 260, 7, 30, 30, $style, 'N');
        //$this->Text(20, 25, 'QRCODE L');
          
        //LINHA ESPAÇAMENTO INFERIOR
        $this->SetFont('helvetica','',10);
        $this->SetXY(11, 33);
        $this->Cell(0,0,'',0,0,'L');
        $this->Line(10,33,200,33);
        $this->ln();
         
    }
        
    public function Footer()
    {
        $this->SetY(-20);
        $this->setFont('helvetica','B',6);        
        $data = strftime("%d/%m/%Y - %T"); 	
        $this->Cell(100, 10, "Gerado : ".utf8_decode($data), 'T', 0, 'L'); 
        //$this->Cell(90, 10, "Página ".$this->PageNo().' de '.$this->getAliasNbPages(),'T',0,'R');
        $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'R');
        //$this->SetXY(-10,-5); 	
    }  

    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }
    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,5,$data[$i], 0, $a, 0, 0, '', '', true);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}
?>