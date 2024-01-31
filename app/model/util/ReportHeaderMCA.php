<?php 

class ReportHeaderMCA extends TCPDF
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
    
    public function set_param($logo = "",$razao_social = "",$nome_fantasia = "",$endereco = "",$cnpj = "",$documento = "",$telefones = "",$ie = "",$im = "")
    {
        $this->logo = "app/images/".$logo;    
        $this->razao_social = $razao_social;
        $this->nome_fantasia = $nome_fantasia;
        $this->endereco = $endereco;
        $this->cnpj = $cnpj;
        $this->documento = $documento;
        $this->telefones = $telefones;
        $this->ie = $ie;
        $this->im = $im;
        
        $this->SetMargins(10, 30, 10, true);
        $this->SetAutoPageBreak(true, 30);
        $this->SetPrintFooter(true);

    }
    
    public function Header()
    {  
        $imageData =file_get_contents($this->logo);
        $imageMarcaDagua =file_get_contents("app/images/LogoWS-Report.png");
        $this->Image('@'.$imageMarcaDagua, '-5', '50', 235, 255, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->Image('@'.$imageData, '80', '-10', 50, 50, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }
        
    
    public function Footer()
    {   
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->SetY(-45);
        $imageRodape = file_get_contents("app/images/LogoWS-Report-Header.png");
        $this->Image('@'.$imageRodape, '65', '268', 75, 20, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }    
    
}
