<?php
class RelReciboReceitaAvulso extends TWindow
{
    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Recibo do Serviço');
        parent::setSize(0.8,0.8);    
        $object = new TElement('object');
        $object->data  = "tmp/RelReciboReceitaAvulso.pdf";
        $object->style = "width: 100%; height:calc(100% - 10px)";
        parent::add($object);

    }

    function onViewRecibo($param)
    {
        $id = $param['id'];

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

                    $imageData =file_get_contents("app/images/logo.png");
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

                    $recibo = new ContaReceber($id);
                    $conteudos  = RelatorioCustomizado::where('id','=',$recibo->relatorio_customizado_id)->load();

                    $pdf->SetXY(10, 40);
                    $pdf->SetFont('helvetica','B',15);
                    $pdf->Cell(190,5,"Nº ".str_pad($recibo->id, 6, "0", STR_PAD_LEFT),0,0,'R'); 
                    //$conteudos  = RelatorioCustomizado::where('id','=',1)->load();

                    if($conteudos) 
                    {
                        foreach ($conteudos as $html)
                        {   
                            $pdf->SetXY(10, 40);
                            $pdf->SetFont('helvetica','B',15);
                            $pdf->Cell(200,5,$html->nome,0,0,'L'); 

                            $cp1 = ContaReceber::where('id','=',$id)->first();

                            if($cp1){

                            
                                $for = new Cliente($cp1->cliente_id);
                                $razao_social = $for->razao_social;
                                $cli_endereco = new ClienteEndereco($cp1->cliente_id);
                                    $endereco = $cli_endereco->logradouro." Nº ".$cli_endereco->numero.". Bairro: ".$cli_endereco->bairro.". Cidade: ".$cli_endereco->cidade." - ".$cli_endereco->uf.". CEP: ".$cli_endereco->cep;
                                $cpfCnpj = $for->cpf_cnpj;

                                $html->conteudo = str_replace('[nome]', $cp1->cliente->razao_social, $html->conteudo);
                                $html->conteudo = str_replace('[endereco]', $endereco, $html->conteudo);
                                $html->conteudo = str_replace('[cpf_cnpj]', $cpfCnpj, $html->conteudo);

                                if($cp1->valor_pago == 0.00){
                                    $valor = number_format($cp1->valor, 2, ',', '.');    
                                    $html->conteudo = str_replace('[valor]', $valor, $html->conteudo);

                                }else{
                                    $valor = number_format($cp1->valor_pago, 2, ',', '.');    
                                    $html->conteudo = str_replace('[valor]', $valor, $html->conteudo);
                                }
                                
                                $html->conteudo = str_replace('[descricao]', $cp1->descricao, $html->conteudo);

                                $valor_extenso = Utilidades::extenso($cp1->valor_pago);

                                $html->conteudo = str_replace('[valor_extenso]', $valor_extenso, $html->conteudo);

                                $date = date('d/m/Y');
                                $html->conteudo = str_replace('[data]', $date, $html->conteudo);

                                $referencia = $cp1->descricao;
                                $html->conteudo = str_replace('[referente]', $referencia, $html->conteudo);

                                $user = new SystemUser(TSession::getValue('userid'));
                                $nomeUser = $user->name;
                                $html->conteudo = str_replace('[user]', $nomeUser, $html->conteudo);

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

           $units2  = SystemUnit::where('id','=',TSession::getValue('userunitid'))->load();
           
            if ($units2)
            {
                foreach ($units2 as $unit2)
                {  

                    $imageData =file_get_contents("app/images/logo.png");
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
                    $pdf->Cell(190,5,"Nº ".str_pad($id, 6, "0", STR_PAD_LEFT),0,0,'R'); 

                    $recibo2 = new ContaReceber($id);
                    $conteudos2  = RelatorioCustomizado::where('id','=',$recibo2->relatorio_customizado_id)->load();

                    if($conteudos2) 
                    {
                        foreach ($conteudos2 as $html2)
                        { 

                            $pdf->SetXY(10, 180);
                            $pdf->SetFont('helvetica','B',15);
                            $pdf->Cell(200,5,$html2->nome,0,0,'L');


                            $cp2 = ContaReceber::where('id','=',$id)->first();

                            if($cp2){

                                

                                $for = new Cliente($cp2->cliente_id);
                                $razao_social = $for->razao_social;
                                $cli_endereco = new ClienteEndereco($cp2->cliente_id);
                                    $endereco = $cli_endereco->logradouro." Nº ".$cli_endereco->numero.". Bairro: ".$cli_endereco->bairro.". Cidade: ".$cli_endereco->cidade." - ".$cli_endereco->uf.". CEP: ".$cli_endereco->cep;
                                $cpfCnpj = $for->cpf_cnpj;

                            
                                $html2->conteudo = str_replace('[nome]', $cp2->cliente->razao_social, $html2->conteudo);
                                $html2->conteudo = str_replace('[endereco]', $endereco, $html2->conteudo);
                                $html2->conteudo = str_replace('[cpf_cnpj]', $cpfCnpj, $html2->conteudo);

                                $html2->conteudo = str_replace('[descricao]', $cp2->descricao, $html2->conteudo);

                                if($cp2->valor_pago == 0.00){
                                    $valor = number_format($cp2->valor, 2, ',', '.');    
                                    $html->conteudo = str_replace('[valor]', $valor, $html->conteudo);

                                }else{
                                    $valor = number_format($cp2->valor_pago, 2, ',', '.');    
                                    $html->conteudo = str_replace('[valor]', $valor, $html->conteudo);
                                }

                                $valor_extenso = Utilidades::extenso($cp2->valor_pago);

                                $html2->conteudo = str_replace('[valor_extenso]', $valor_extenso, $html2->conteudo);

                                $referencia = $cp2->descricao;
                                $html2->conteudo = str_replace('[referente]', $referencia, $html2->conteudo);

                                $user = new SystemUser(TSession::getValue('userid'));
                                $nomeUser = $user->name;
                                $html2->conteudo = str_replace('[user]', $nomeUser, $html2->conteudo);

                                $date = date('d/m/Y');
                                $html2->conteudo = str_replace('[data]', $date, $html2->conteudo);

                                $pdf->SetFont('helvetica','',10);
                                $pdf->SetXY(10, 190);
                                $pdf->writeHTML($html2->conteudo, true, false, true, false, '');

                                
                            }
                        }
                    }else{

                        new TMessage('error', "ATENÇÃO: O recibo não poderá ser gerado! Pois não existe um recibo vinculado ao contas a pagar. Favor vincular o recibo na opção Modelo de Recibo e tente novamente.");
                    }
                }
            }
            
            $arq = PATH."/tmp/RelReciboReceitaAvulso.pdf";  
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