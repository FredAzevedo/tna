<?php
class RelReciboContaPagar extends TWindow
{
    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Recibo do Serviço');
        parent::setSize(0.8,0.8);    
        $object = new TElement('object');
        $object->data  = "/tmp/RelReciboContaPagar.pdf";
        $object->style = "width: 100%; height:calc(100% - 10px)";
        parent::add($object);

    }

    function onViewReciboContaPagar($param)
    {
        $id = $param['id'];
        $fornecedor_id = $param['fornecedor_id'];


        //START TCPDF
        include_once( 'vendor/autoload.php' );

        try
        {
            TTransaction::open('sample');

            $pdf = new TCPDF();
            $pdf->SetPrintHeader(false); 
            $pdf->addPage('P', 'A4');
            $pdf->SetMargins(10, 20, 10);

            $units  = SystemUnit::where('id','=',TSession::getValue('userunitid'))->load();
           
            if ($units)
            {
                foreach ($units as $unit)
                {  

                    $imageData =file_get_contents("app/images/LogoWS.png");
                    $pdf->Image('@'.$imageData, '10', '10', 20, 20, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

                    $endereco = $unit->logradouro." Nº: ".$unit->numero." Bairro: ".$unit->bairro.". ".$unit->complemento." Cidade: ".$unit->cidade." UF: ".$unit->uf." CEP: ".$unit->cep;

                    //COLUNA 1 - LINHA 1
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 10);
                    $pdf->Cell(0,0,'Razão Social:',0,0,'L');
                  
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(54, 10);
                    $pdf->Cell(0,0,$unit->razao_social,0,0,'L');

                    //COLUNA 1 - LINHA 2
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 14);
                    $pdf->Cell(0,0,'Nome Fantasia:',0,0,'L');
                      
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(57, 14);
                    $pdf->Cell(0,0,$unit->nome_fantasia,0,0,'L');

                    //COLUNA 1 - LINHA 3
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 18);
                    $pdf->Cell(0,0,'Endereço:',0,0,'L');
                      
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(50, 18);
                    $pdf->Cell(0,0,$endereco,0,0,'L');

                    //COLUNA 1 - LINHA 4
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 22);
                    $pdf->Cell(0,0,'CNPJ:',0,0,'L');
                      
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(44, 22);
                    $pdf->Cell(0,0,$unit->cnpj,0,0,'L');
                     
                    //COLUNA 1 - LINHA 5
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 26);
                    $pdf->Cell(0,0, "Fone:",0,0,'L');

                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(43, 26);
                    $pdf->Cell(0,0,$unit->telefone,0,0,'L');

                    $pdf->SetXY(10, 40);
                    $pdf->SetFont('helvetica','B',15);
                    $pdf->Cell(200,5,"RECIBO DE DESPESAS",0,0,'C'); 

                    $recibo = new ContaPagar($id);
                    $conteudos  = RelatorioCustomizado::where('id','=',$recibo->relatorio_customizado_id)->load();

                    //$conteudos  = RelatorioCustomizado::where('id','=',2)->load();

                    if($conteudos) 
                    {
                        foreach ($conteudos as $html)
                        { 
                            $cp1 = ContaPagar::where('id','=',$id)->load();

                            if($cp1){

                                foreach ($cp1 as $itemOs) {

                                    $for = new Fornecedor($itemOs->fornecedor_id);
                                    $razao_social = $for->razao_social;
                                    $endereco = $for->logradouro." Nº ".$for->numero.". Bairro: ".$for->bairro.". Cidade: ".$for->cidade." - ".$for->uf.". CEP: ".$for->cep;
                                    $cpfCnpj = $for->cpf_cnpj;

                                
                                    $html->conteudo = str_replace('[nome]', $itemOs->fornecedor->razao_social, $html->conteudo);
                                    $html->conteudo = str_replace('[endereco]', $endereco, $html->conteudo);
                                    $html->conteudo = str_replace('[cpf_cnpj]', $cpfCnpj, $html->conteudo);

                                    $valor = number_format($itemOs->valor, 2, ',', '.');    
                                    $html->conteudo = str_replace('[valor]', $valor, $html->conteudo);

                                    $valor_extenso = Utilidades::extenso($itemOs->valor);

                                    $html->conteudo = str_replace('[valor_extenso]', $valor_extenso, $html->conteudo);

                                    $user = new SystemUser(TSession::getValue('userid'));
                                    $nomeUser = $user->name;
                                    $html->conteudo = str_replace('[user]', $nomeUser, $html->conteudo);

                                    $date = date('d/m/Y');
                                    $html->conteudo = str_replace('[data]', $date, $html->conteudo);

                                    $pdf->SetFont('helvetica','',10);
                                    $pdf->SetXY(10, 50);
                                    $pdf->writeHTML($html->conteudo, true, false, true, false, '');

                                    $pdf->SetX(10);
                                    $pdf->Cell(200,5,". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .  . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .",0,0,'C'); 

                                }
                            }
                        }
                    }
                }
            }

           $units2  = SystemUnit::where('id','=',TSession::getValue('userunitid'))->load();
           
            if ($units2)
            {
                foreach ($units2 as $unit2)
                {  

                    $imageData =file_get_contents("app/images/LogoWS.png");
                    $pdf->Image('@'.$imageData, '10', '150', 20, 20, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

                    $endereco = $unit2->logradouro." Nº: ".$unit2->numero." Bairro: ".$unit2->bairro.". ".$unit2->complemento." Cidade: ".$unit2->cidade." UF: ".$unit2->uf." CEP: ".$unit2->cep;

                    //COLUNA 1 - LINHA 1
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 150);
                    $pdf->Cell(0,0,'Razão Social:',0,0,'L');
                  
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(54, 150);
                    $pdf->Cell(0,0,$unit2->razao_social,0,0,'L');

                    //COLUNA 1 - LINHA 2
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 154);
                    $pdf->Cell(0,0,'Nome Fantasia:',0,0,'L');
                      
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(57, 154);
                    $pdf->Cell(0,0,$unit2->nome_fantasia,0,0,'L');

                    //COLUNA 1 - LINHA 3
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 158);
                    $pdf->Cell(0,0,'Endereço:',0,0,'L');
                      
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(50, 158);
                    $pdf->Cell(0,0,$endereco,0,0,'L');

                    //COLUNA 1 - LINHA 4
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 162);
                    $pdf->Cell(0,0,'CNPJ:',0,0,'L');
                      
                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(44, 162);
                    $pdf->Cell(0,0,$unit2->cnpj,0,0,'L');
                     
                    //COLUNA 1 - LINHA 5
                    $pdf->SetFont('helvetica','B',8);
                    $pdf->SetXY(35, 166);
                    $pdf->Cell(0,0, "Fone:",0,0,'L');

                    $pdf->SetFont('helvetica','',8);
                    $pdf->SetXY(43, 166);
                    $pdf->Cell(0,0,$unit2->telefone,0,0,'L');

                    $pdf->SetXY(10, 180);
                    $pdf->SetFont('helvetica','B',15);
                    $pdf->Cell(200,5,"RECIBO",0,0,'C'); 


                    $recibo2 = new ContaPagar($id);
                    $conteudos2  = RelatorioCustomizado::where('id','=',$recibo2->relatorio_customizado_id)->load();

                    if($conteudos2) 
                    {
                        foreach ($conteudos2 as $html2)
                        { 
                            $cp2 = ContaPagar::where('id','=',$id)->load();

                            if($cp2){

                                foreach ($cp2 as $items2) {

                                    $for = new Fornecedor($items2->fornecedor_id);
                                    $razao_social = $for->razao_social;
                                    $endereco = $for->logradouro." Nº ".$for->numero.". Bairro: ".$for->bairro.". Cidade: ".$for->cidade." - ".$for->uf.". CEP: ".$for->cep;
                                    $cpfCnpj = $for->cpf_cnpj;

                                
                                    $html2->conteudo = str_replace('[nome]', $items2->fornecedor->razao_social, $html2->conteudo);
                                    $html2->conteudo = str_replace('[endereco]', $endereco, $html2->conteudo);
                                    $html2->conteudo = str_replace('[cpf_cnpj]', $cpfCnpj, $html2->conteudo);

                                    $valor = number_format($items2->valor, 2, ',', '.');    
                                    $html2->conteudo = str_replace('[valor]', $valor, $html2->conteudo);

                                    $valor_extenso = Utilidades::extenso($items2->valor);

                                    $html2->conteudo = str_replace('[valor_extenso]', $valor_extenso, $html2->conteudo);

                                    $user = new SystemUser(TSession::getValue('userid'));
                                    $nomeUser = $user->name;
                                    $html->conteudo = str_replace('[user]', $nomeUser, $html->conteudo);

                                    $date = date('d/m/Y');
                                    $html2->conteudo = str_replace('[data]', $date, $html2->conteudo);

                                    $pdf->SetFont('helvetica','',10);
                                    $pdf->SetXY(10, 190);
                                    $pdf->writeHTML($html2->conteudo, true, false, true, false, '');

                                }
                            }
                        }
                    }
                }
            }
            
            $arq = PATH."/tmp/RelReciboContaPagar.pdf";  
            $pdf->Output( $arq, "F");
            
            //END TCPDF
        TTransaction::close();

        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}   