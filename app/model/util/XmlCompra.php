<?php
/**
 * XmlCompra
 * @author  Fred Azv.
 */
class XmlCompra
{
    private  $arquivo;

    public function __construct($arquivo)
    {
        $this->arquivo = $arquivo;
        return $this->processamento($this->arquivo);
        
    }

    public function processamento($param)
    {
        TTransaction::open('sample');
        $chave = $param;
        $unit_id = TSession::getValue('userunitid');
        $unit = new SystemUnit($unit_id);
        $unit->regime; //1 - Simples 2 - Normal

        if ($chave)
        {
            if ($chave == '')
            {
                echo "<h4>Informe a chave de acesso!</h4>";
                exit;	
            }	
            $arquivo = "tmp/".$chave;	
            //var_dump($arquivo);
            //$arquivo = nfe-15190926315031000125550010000165711424204606
            if (file_exists($arquivo)) 
            {
                // $Arqxml = file_get_contents( $arquivo );
                // $arq = utf8_decode($Arqxml);
                $xml = simplexml_load_file($arquivo);
                // imprime os atributos do objeto criado
                
                if (empty($xml->protNFe->infProt->nProt))
                {
                    echo "<h4>Arquivo sem dados de autorização! Ou XML não esta no padrão NFe 4.0. Ou não é um XML de Compra.</h4>";
                    exit;	
                }
                    $chave = $xml->NFe->infNFe->attributes()->Id;
                    $chave = strtr(strtoupper($chave), array("NFE" => NULL));
            }

            //<ide>
            $cUF = $xml->NFe->infNFe->ide->cUF;    		    //<cUF>41</cUF>  Código do Estado do Fator gerador
            $cNF = $xml->NFe->infNFe->ide->cNF;       	    //<cNF>21284519</cNF>   Código número da nfe
            $natOp = $xml->NFe->infNFe->ide->natOp;         //<natOp>V E N D A</natOp>  Resumo da Natureza de operação
            $indPag = $xml->NFe->infNFe->ide->indPag;      //<indPag>2</indPag> 0 – pagamento à vista; 1 – pagamento à prazo; 2 - outros
            $mod = $xml->NFe->infNFe->ide->mod;            //<mod>55</mod> Modelo do documento Fiscal
            $serie = $xml->NFe->infNFe->ide->serie;    	   //<serie>2</serie> 
            $nNF =  $xml->NFe->infNFe->ide->nNF;   	       //<nNF>19685</nNF> Número da Nota Fiscal
            $dEmi = $xml->NFe->infNFe->ide->dhEmi;          //<dEmi>2011-09-06</dEmi> Data de emissão da Nota Fiscal
            $ano = substr($dEmi, 0, 4);
            $mes = substr($dEmi, 5, 2);
            $dia = substr($dEmi, 8, 2);
            $dEmii = $dia."/".$mes."/".$ano;
            //$dEmi = explode('-', $dEmi);
            //$dEmi = $dEmi[2]."/".$dEmi[1]."/".$dEmi[0];
            $dSaiEn = $xml->NFe->infNFe->ide->dhSaiEnt ;    //<dSaiEnt>2011-09-06</dSaiEnt> Data de entrada ou saida da Nota Fiscal
            $ano = substr($dSaiEn, 0, 4);
            $mes = substr($dSaiEn, 5, 2);
            $dia = substr($dSaiEn, 8, 2);
            $dSaiEntt = $dia."/".$mes."/".$ano;
            //$dSaiEnt = explode('-', $dSaiEnt);
            //$dSaiEnt = $dSaiEnt[2]."/".$dSaiEnt[1]."/".$dSaiEnt[0];
            $tpNF = $xml->NFe->infNFe->ide->tpNF;         //<tpNF>1</tpNF>  0-entrada / 1-saída
            $cMunFG = $xml->NFe->infNFe->ide->cMunFG;     //<cMunFG>4106407</cMunFG> Código do municipio Tabela do IBGE
            $tpImp = $xml->NFe->infNFe->ide->tpImp;       //<tpImp>1</tpImp> 
            $tpEmis = $xml->NFe->infNFe->ide->tpEmis;     //<tpEmis>1</tpEmis>
            $cDV = $xml->NFe->infNFe->ide->cDV;           //<cDV>0</cDV>
            $tpAmb = $xml->NFe->infNFe->ide->tpAmb;       //<tpAmb>1</tpAmb>
            if ($tpAmb != 1)
            {
                echo "<h4>Documento emitido em ambiente de homologação!</h4>";
                exit;	
            }
            $finNFe = $xml->NFe->infNFe->ide->finNFe;     //<finNFe>1</finNFe>
            $procEmi = $xml->NFe->infNFe->ide->procEmi;   //<procEmi>0</procEmi>
            $verProc = $xml->NFe->infNFe->ide->verProc;   //<verProc>2.0.0</verProc>
            //</ide>
            $xMotivo = $xml->protNFe->infProt->xMotivo;	
            $nProt = $xml->protNFe->infProt->nProt;

            // <emit> Emitente
            $emit_CPF = $xml->NFe->infNFe->emit->CPF;
            $emit_CNPJ = $xml->NFe->infNFe->emit->CNPJ;  				
            $emit_xNome = $xml->NFe->infNFe->emit->xNome; 				
            $emit_xFant = $xml->NFe->infNFe->emit->xFant;     			
            //<enderEmit>
            $emit_xLgr = $xml->NFe->infNFe->emit->enderEmit->xLgr;		//<xLgr>AV. AGOSTINHO DUCCI , 409</xLgr>
            $emit_nro = $xml->NFe->infNFe->emit->enderEmit->nro; 			//<nro>.</nro>
            $emit_xBairro = $xml->NFe->infNFe->emit->enderEmit->xBairro; //<xBairro>PARQUE INDUSTRIAL</xBairro>
            $emit_cMun = $xml->NFe->infNFe->emit->enderEmit->cMun; 		//<cMun>4106407</cMun>
            $emit_xMun = $xml->NFe->infNFe->emit->enderEmit->xMun; 		//<xMun>CORNELIO PROCOPIO</xMun>
            $emit_UF = $xml->NFe->infNFe->emit->enderEmit->UF; 			//<UF>PR</UF>
            $emit_CEP = $xml->NFe->infNFe->emit->enderEmit->CEP; 		//<CEP>86300000</CEP>
            $emit_cPais = $xml->NFe->infNFe->emit->enderEmit->cPais; 	//<cPais>1058</cPais>
            $emit_xPais = $xml->NFe->infNFe->emit->enderEmit->xPais; 	//<xPais>BRASIL</xPais>
            $emit_fone = $xml->NFe->infNFe->emit->enderEmit->fone; 		//<fone>4335242165</fone>
            //</enderEmit>
            $emit_IE = $xml->NFe->infNFe->emit->IE; 				   //<IE>9014134104</IE>
            $emit_IM = $xml->NFe->infNFe->emit->IM; 				   //<IM>8636</IM>
            $emit_CNAE = $xml->NFe->infNFe->emit->CNAE; 			   //<CNAE>4789099</CNAE>
            $emit_CRT = $xml->NFe->infNFe->emit->CRT; //<CRT>3</CRT>
            //</emit>
            //<dest>
            $dest_cnpj =  $xml->NFe->infNFe->dest->CNPJ;   //<CNPJ>01153928000179</CNPJ>
            //<CPF></CPF>
            $dest_xNome = $xml->NFe->infNFe->dest->xNome;   //<xNome>AGROVENETO S.A.- INDUSTRIA DE ALIMENTOS  -002825</xNome>
            $dest_xFant = $xml->NFe->infNFe->dest->xNome;
            //***********************************************************************************************************************************************
            //<enderDest>
            $dest_xLgr = $xml->NFe->infNFe->dest->enderDest->xLgr;            //<xLgr>ALFREDO PESSI, 2.000</xLgr>
            $dest_nro =  $xml->NFe->infNFe->dest->enderDest->nro;     		  //<nro>.</nro>
            $dest_xBairro = $xml->NFe->infNFe->dest->enderDest->xBairro;      //<xBairro>PARQUE INDUSTRIAL</xBairro>
            $dest_cMun = $xml->NFe->infNFe->dest->enderDest->cMun;            //<cMun>4211603</cMun>
            $dest_xMun = $xml->NFe->infNFe->dest->enderDest->xMun;            //<xMun>NOVA VENEZA</xMun>
            $dest_UF = $xml->NFe->infNFe->dest->enderDest->UF;                //<UF>SC</UF>
            $dest_CEP = $xml->NFe->infNFe->dest->enderDest->CEP;              //<CEP>88865000</CEP>
            $dest_cPais = $xml->NFe->infNFe->dest->enderDest->cPais;          //<cPais>1058</cPais>
            $dest_xPais = $xml->NFe->infNFe->dest->enderDest->xPais;          //<xPais>BRASIL</xPais>
            //</enderDest>
            $dest_IE = $xml->NFe->infNFe->dest->IE;                           //<IE>253323029</IE>
            //</dest>
            //Totais		
            $vBC_xml = $xml->NFe->infNFe->total->ICMSTot->vBC;
            $vBC_xml = ($unit->regime == 1) ? 0.00 : $vBC_xml; 
            $vBC = number_format((double) $vBC_xml, 2, ",", ".");
            $vICMS_xml = $xml->NFe->infNFe->total->ICMSTot->vICMS;
            $vICMS_xml = ($unit->regime == 1) ? 0.00 : $vICMS_xml;
            $vICMS = number_format((double) $vICMS_xml, 2, ",", ".");
            $vBCST_xml = $xml->NFe->infNFe->total->ICMSTot->vBCST;
            $vBCST_xml = ($unit->regime == 1) ? 0.00 : $vBCST_xml;
            //$vBCST = number_format((double) $vBCST_xml, 2, ",", ".");
            $vST_xml = $xml->NFe->infNFe->total->ICMSTot->vST;
            //$vST = number_format((double) $vST_xml, 2, ",", ".");
            $vProd_xml = $xml->NFe->infNFe->total->ICMSTot->vProd;
            //$vProd = number_format((double) $vProd_xml, 2, ",", ".");
            $vNF_xml = $xml->NFe->infNFe->total->ICMSTot->vNF;
            //$vNF = number_format((double) $vNF_xml, 2, ",", ".");
            $vFrete_xml = $xml->NFe->infNFe->total->ICMSTot->vFrete;
            //$vFrete = number_format((double) $vFrete_xml, 2, ",", ".");
            $vSeg_xml = $xml->NFe->infNFe->total->ICMSTot->vSeg;
            //$vSeg = number_format((double) $vSeg_xml, 2, ",", ".");
            $vDesc_xml = $xml->NFe->infNFe->total->ICMSTot->vDesc;
            //$vDesc = number_format((double) $vDesc_xml, 2, ",", ".");
            $vIPI_xml = $xml->NFe->infNFe->total->ICMSTot->vIPI;
            //$vIPI = number_format((double)  $vIPI_xml, 2, ",", ".");	

            $vFCP_xml = $xml->NFe->infNFe->total->ICMSTot->vFCP;
            $vFCPST_xml = $xml->NFe->infNFe->total->ICMSTot->vFCPST;
            $vFCPSTRet_xml = $xml->NFe->infNFe->total->ICMSTot->vFCPSTRet;
            $vFCPSTRet_xml = $xml->NFe->infNFe->total->ICMSTot->vFCPSTRet;
            $vII_xml = $xml->NFe->infNFe->total->ICMSTot->vII;
            $vIPIDevol_xml = $xml->NFe->infNFe->total->ICMSTot->vIPIDevol;
            $vPIS_xml = $xml->NFe->infNFe->total->ICMSTot->vPIS;
            $vCOFINS_xml = $xml->NFe->infNFe->total->ICMSTot->vCOFINS;
            $vOutro_xml = $xml->NFe->infNFe->total->ICMSTot->vOutro;
            $vTotTrib_xml = $xml->NFe->infNFe->total->ICMSTot->vTotTrib;
            $vTotTrib_xml = ($unit->regime == 1) ? 0.00 : $vTotTrib_xml;

            $modFrete_xml = $xml->NFe->infNFe->transp->modFrete;
            $transporta_xNome = $xml->NFe->infNFe->transp->transporta->xNome;
            $qVol = $xml->NFe->infNFe->transp->vol->qVol;

            $infAdic = $xml->NFe->infNFe->infAdic;

            $xMotivo = $xml->protNFe->infProt->xMotivo;	
            $nProt = $xml->protNFe->infProt->nProt;
            $chNFe = $xml->protNFe->infProt->chNFe;
                    
            if($emit_CNPJ){
                $cpf_cnpj = $emit_CNPJ;
            }

            if($emit_CPF){
                $cpf_cnpj = $emit_CPF;
            }

            $cpf_cnpj_fornecedor = Utilidades::formata_cpf_cnpj($cpf_cnpj);
            $for = Fornecedor::where('cpf_cnpj','=',$cpf_cnpj_fornecedor)->first();
            
            if($for == null){
            
                $fornecedor = new Fornecedor;
                $fornecedor->nome_fantasia = "{$emit_xFant}";
                $fornecedor->razao_social = "{$emit_xNome}";
                $fornecedor->insc_estadual = "{$emit_IE}";
                $fornecedor->tipo = "F";
                $fornecedor->cpf_cnpj = Utilidades::formata_cpf_cnpj("{$cpf_cnpj}");
                $fornecedor->cep = "{$emit_CEP}";
                $fornecedor->logradouro = "{$emit_xLgr}";
                $fornecedor->numero = "{$emit_nro}";
                $fornecedor->bairro = "{$emit_xBairro}";
                $fornecedor->complemento = "";
                $fornecedor->cidade = "{$emit_xMun}";
                $fornecedor->uf = "{$emit_UF}";
                $fornecedor->codMuni = "{$emit_cMun}";
                $fornecedor->site = "";
                $fornecedor->parceria = "N";
                $fornecedor->unit_id = 1;//TSession::getValue("userunitid");
                $fornecedor->user_id = 1;//TSession::getValue("userid");
                $fornecedor->comissao_tabela_id = "";
                $fornecedor->store();
                
                $fornecedor_id = $fornecedor->id;
            }else{

                $fornecedor_id = $for->id;
            }
            
            $nfe_entrada = NfeEntrada::where('chNFe','=',"{$chNFe}")->first();
            
            if($nfe_entrada){

                // new TMessage('info', 'NFe já cadastrada. Não é possível duplicar notas de entrada.');
                throw new Exception('Mensagem: NFe já cadastrada. Não é possível duplicar notas de entrada.');

            }else{
                //TTransaction::setLogger(new TLoggerSTD);
                $nfeEntrada = new NfeEntrada;
                $nfeEntrada->unit_id = 1;//TSession::getValue('userunitid');
                $nfeEntrada->fornecedor_id = $fornecedor_id;
                $nfeEntrada->cUF = "{$cUF}";
                $nfeEntrada->cNF = "{$cNF}";
                $nfeEntrada->natOp = "{$natOp}";
                $nfeEntrada->mod = "{$mod}";
                $nfeEntrada->serie = "{$serie}";
                $nfeEntrada->nNF = "{$nNF}";
                $nfeEntrada->dhEmi = date("Y-m-d",strtotime("{$dEmi}"));
                $nfeEntrada->dhSaiEnt = date("Y-m-d",strtotime("{$dEmi}"));//date("Y-m-d",strtotime("{$dSaiEn}"));
                $nfeEntrada->tpNF = "{$tpNF}";
                $nfeEntrada->idDest = '';
                $nfeEntrada->cMunFG = "{$cMunFG}";
                $nfeEntrada->tpImp = "{$tpImp}";
                $nfeEntrada->tpEmis = "{$tpEmis}";
                $nfeEntrada->cDV = "{$cDV}";
                $nfeEntrada->tpAmb = "{$tpAmb}";
                $nfeEntrada->finNFe = "{$finNFe}";
                $nfeEntrada->indFinal = '';
                $nfeEntrada->indPres = '';
                $nfeEntrada->procEmi = "{$procEmi}";
                $nfeEntrada->verProc = "{$verProc}";
                $nfeEntrada->emit_CNPJ = "{$cpf_cnpj}";
                $nfeEntrada->emit_xNome = "{$emit_xNome}";
                $nfeEntrada->emit_xFant = "{$emit_xFant}";
                $nfeEntrada->emit_xLgr = "{$emit_xLgr}";
                $nfeEntrada->emit_nro = "{$emit_nro}";
                $nfeEntrada->emit_xCpl = '';
                $nfeEntrada->emit_xBairro =  "{$emit_xBairro}";
                $nfeEntrada->emit_cMun = "{$emit_cMun}";
                $nfeEntrada->emit_xMun = "{$emit_xMun}";
                $nfeEntrada->emit_UF = "{$emit_UF}";
                $nfeEntrada->emit_CEP = "{$emit_CEP}";
                $nfeEntrada->emit_xPais = "{$emit_xPais}";
                $nfeEntrada->emit_fone = "{$emit_fone}";
                $nfeEntrada->emit_IE = "{$emit_IE}";
                $nfeEntrada->emit_CRT = "{$emit_CRT}";
                $nfeEntrada->dest_CNPJ = "{$dest_cnpj}";
                $nfeEntrada->dest_xNome = "{$dest_xNome}";
                $nfeEntrada->dest_xFant = "{$dest_xFant}";
                $nfeEntrada->dest_xLgr = "{$dest_xLgr}";
                $nfeEntrada->dest_nro = "{$dest_nro}";
                $nfeEntrada->dest_xCpl = '';
                $nfeEntrada->dest_xBairro = "{$dest_xBairro}";
                $nfeEntrada->dest_cMun = "{$dest_cMun}";
                $nfeEntrada->dest_xMun = "{$dest_xMun}";
                $nfeEntrada->dest_UF = "{$dest_UF}";
                $nfeEntrada->dest_CEP =  "{$dest_CEP}";
                $nfeEntrada->dest_xPais = "{$dest_xPais}";
                $nfeEntrada->dest_fone = '';
                $nfeEntrada->dest_IE = "{$dest_IE}";
                $nfeEntrada->indIEDest = '';
                $nfeEntrada->vBC = "{$vBC_xml}";
                $nfeEntrada->vICMS = "{$vICMS_xml}";
                $nfeEntrada->vICMSDeson = ''; 
                $nfeEntrada->vFCP = "{$vFCP_xml}";
                $nfeEntrada->vBCST = "{$vBCST_xml}";
                $nfeEntrada->vST = "{$vST_xml}";
                $nfeEntrada->vFCPST = "{$vFCPST_xml}";
                $nfeEntrada->vFCPSTRet = "{$vFCPSTRet_xml}";
                $nfeEntrada->vProd = "{$vProd_xml}";
                $nfeEntrada->vFrete = "{$vFrete_xml}";
                $nfeEntrada->vSeg = "{$vSeg_xml}";
                $nfeEntrada->vDesc = "{$vDesc_xml}";
                $nfeEntrada->vII = "{$vII_xml}";
                $nfeEntrada->vIPI = "{$vIPI_xml}";
                $nfeEntrada->vPIS = "{$vPIS_xml}";
                $nfeEntrada->vCOFINS = "{$vCOFINS_xml}";
                $nfeEntrada->vOutro = "{$vOutro_xml}";
                $nfeEntrada->vNF = "{$vNF_xml}";
                $nfeEntrada->vTotTrib = "{$vTotTrib_xml}";
                $nfeEntrada->modFrete = "{$modFrete_xml}";
                $nfeEntrada->transporta_xNome = "{$transporta_xNome}";
                $nfeEntrada->qVol = "{$qVol}";
                $nfeEntrada->infAdic = "{$infAdic}";
                $nfeEntrada->chNFe = "{$chNFe}";
                $nfeEntrada->nProt = "{$nProt}";
                $nfeEntrada->store();
                
            }

            //TÍTULOS	
            $id = 0;
            if (!empty($xml->NFe->infNFe->cobr->dup)){
                
                foreach($xml->NFe->infNFe->cobr->dup as $dup) {
                    $id++;
                    $duplicata = new NfeEntradaCobranca;
                    $duplicata->nfe_entrada_id = $nfeEntrada->id;
                    $duplicata->nDup = Utilidades::soNumero($dup->nDup);
                    $duplicata->dVenc = Utilidades::limpaCaracter($dup->dVenc);
                    $duplicata->vDup = (float)$dup->vDup;
                    $duplicata->store();
                }
                    
            }
                
            //Dados dos itens
            if($nfeEntrada->id){
               
                $seq = 0;
                foreach($xml->NFe->infNFe->det as $item) {
                    $seq++;
                    $codigo = $item->prod->cProd;
                    $xProd = $item->prod->xProd;
                    $cEAN = $item->prod->cEAN;
                    $infAdProd  = $item->infAdProd;
                    $NCM = $item->prod->NCM;
                    $CEST = $item->prod->CEST;
                    $CFOP = $item->prod->CFOP;
                    $uCom = $item->prod->uCom;
                    $qCom = $item->prod->qCom;
                    $cEANTrib = $item->prod->cEANTrib;
                    $uTrib = $item->prod->uTrib;
                    $qTrib = $item->prod->qTrib;
                    $vUnTrib = $item->prod->vUnTrib;
                    $indTot = $item->prod->indTot;
                    $rastro_nLote = $item->prod->rastro->nLote;
                    $rastro_qLote = $item->prod->rastro->qLote;
                    $rastro_dFab = $item->prod->rastro->dFab;
                    $rastro_dVal = $item->prod->rastro->dVal;
                    $vTotTrib = $item->imposto->vTotTrib;
                    //$qCom = number_format((double) $qCom, 2, '.', '');
                    $vUnCom = $item->prod->vUnCom;
                    //$vUnCom = number_format((double) $vUnCom, 2, '.', '');
                    $vProdu = $item->prod->vProd;
                    //$vProd = number_format((double) $vProd, 2, '.', '');	
                    $vDesc = $item->prod->vDesc;
                    //$vDesc = number_format((double) $vDesc, 2, '.', '');
                    $vBC_item = $item->imposto->ICMS->ICMS00->vBC;
                    $icms00 = $item->imposto->ICMS->ICMS00;
                    $icms10 = $item->imposto->ICMS->ICMS10;
                    $icms20 = $item->imposto->ICMS->ICMS20;
                    $icms30 = $item->imposto->ICMS->ICMS30;
                    $icms40 = $item->imposto->ICMS->ICMS40;
                    $icms50 = $item->imposto->ICMS->ICMS50;
                    $icms51 = $item->imposto->ICMS->ICMS51;
                    $icms60 = $item->imposto->ICMS->ICMS60;
                    $icms70 = $item->imposto->ICMS->ICMS70;
                    $icms90 = $item->imposto->ICMS->ICMS90;
                    $ICMSSN101 = $item->imposto->ICMS->ICMSSN101; 
                    $ICMSSN102 = $item->imposto->ICMS->ICMSSN102; 
                    $ICMSSN201 = $item->imposto->ICMS->ICMSSN201; 
                    $ICMSSN202 = $item->imposto->ICMS->ICMSSN202; 
                    $ICMSSN900 = $item->imposto->ICMS->ICMSSN900; 
                    $ICMSPart = $item->imposto->ICMS->ICMSPart; 
                    $ICMSST = $item->imposto->ICMS->ICMSST; 

                    $vICMSST = '0.00';
                    $pCredSN = '0.00';
                    $vCredICMSSN = '0.00';
                    $pMVAST = '0.00';
                    $pRedBCST = '0.00';
                    $modBC = '';
                    $pRedBC = '0.00';
                    $pBCOp = '0.00';
                    $UFST = '0.00';
                    $modBCST = '';
                    $pICMSST = '0.00';
                    $vBCST = '0.00';
                    $vBCSTRet = '0.00';
                    $vICMSSTRet = '0.00';
                    $vBCSTDest = '0.00';
                    $vICMSSTDest = '0.00';

                    if(!empty($ICMSST)){

                        $orig = $item->imposto->ICMS->ICMSST->orig;
                        $CST = $item->imposto->ICMS->ICMSST->CSOSN;
                        $vBCSTRet = $item->imposto->ICMS->ICMSST->vBCSTRet;
                        $pST = $item->imposto->ICMS->ICMSST->pST;
                        //$pST = ($unit->regime == 1) ? 0.00 : $pST;
                        $vICMSSTRet = $item->imposto->ICMS->ICMSST->vICMSSTRet;
                        //$vICMSSTRet = ($unit->regime == 1) ? 0.00 : $vICMSSTRet;
                        $vBCSTDest = $item->imposto->ICMS->ICMSST->vBCSTDest;
                        //$vBCSTDest = ($unit->regime == 1) ? 0.00 : $vBCSTDest;
                        $vICMSSTDest = $item->imposto->ICMS->ICMSST->vICMSSTDest;
                        //$vICMSSTDest = ($unit->regime == 1) ? 0.00 : $vICMSSTDest;
   
                    }		

                    if(!empty($ICMSSN201)){

                        $orig = $item->imposto->ICMS->ICMSSN201->orig;
                        $CST = $item->imposto->ICMS->ICMSSN201->CSOSN;
                        $modBCST = $item->imposto->ICMS->ICMSSN201->modBCST;
                        $vBCST = $item->imposto->ICMS->ICMSSN201->vBCST;
                        $vBCST = ($unit->regime == 1) ? 0.00 : $vBCST;
                        $pICMSST = $item->imposto->ICMS->ICMSSN201->pICMSST;
                        $pICMSST = ($unit->regime == 1) ? 0.00 : $pICMSST;
                        $vICMSST = $item->imposto->ICMS->ICMSSN201->vICMSST;
                        $vICMSST = ($unit->regime == 1) ? 0.00 : $vICMSST;
                        $pCredSN = $item->imposto->ICMS->ICMSSN201->pCredSN;
                        $pCredSN = ($unit->regime == 1) ? 0.00 : $pCredSN;
                        $vCredICMSSN = $item->imposto->ICMS->ICMSSN201->vCredICMSSN;
                        $vCredICMSSN = ($unit->regime == 1) ? 0.00 : $vCredICMSSN;

                    }		

                    if(!empty($ICMSSN202)){

                        $orig = $item->imposto->ICMS->ICMSSN202->orig;
                        $CST = $item->imposto->ICMS->ICMSSN202->CSOSN;
                        $modBCST = $item->imposto->ICMS->ICMSSN202->modBCST;
                        $pMVAST = $item->imposto->ICMS->ICMSSN202->pMVAST;
                        $pMVAST = ($unit->regime == 1) ? 0.00 : $pMVAST;
                        $pRedBCST = $item->imposto->ICMS->ICMSSN202->pRedBCST;
                        $pRedBCST = ($unit->regime == 1) ? 0.00 : $pRedBCST;
                        $vBCST = $item->imposto->ICMS->ICMSSN202->vBCST;
                        $vBCST = ($unit->regime == 1) ? 0.00 : $vBCST;
                        $pICMSST = $item->imposto->ICMS->ICMSSN202->pICMSST;
                        $pICMSST = ($unit->regime == 1) ? 0.00 : $pICMSST;
                        $vICMSST = $item->imposto->ICMS->ICMSSN202->vICMSST;
                        $vICMSST = ($unit->regime == 1) ? 0.00 : $vICMSST;
                    }

                    if(!empty($ICMSSN900)){

                        $orig = $item->imposto->ICMS->ICMSSN900->orig;
                        $CST = $item->imposto->ICMS->ICMSSN900->CSOSN;
                        $modBC = $item->imposto->ICMS->ICMSSN900->modBC;
                        $pRedBC = $item->imposto->ICMS->ICMSSN900->pRedBC;
                        $pRedBC = ($unit->regime == 1) ? 0.00 : $pRedBC;
                        $vlr_icms = $item->imposto->ICMS->ICMSSN900->vICMS;
                        $vlr_icms = ($unit->regime == 1) ? 0.00 : $vlr_icms;
                        $pICMS = $item->imposto->ICMS->ICMSSN900->pICMS;
                        $pICMS = ($unit->regime == 1) ? 0.00 : $pICMS;
                        $bc_icms = $item->imposto->ICMS->ICMSSN900->vBC;
                        $bc_icms = ($unit->regime == 1) ? 0.00 : $bc_icms;
                        
                    }

                    if(!empty($ICMSSN101)){

                        $orig = $item->imposto->ICMS->ICMSSN101->orig;
                        $CST = $item->imposto->ICMS->ICMSSN101->CSOSN;
                        $pCredSN = $item->imposto->ICMS->ICMSSN101->pCredSN;
                        $pCredSN = ($unit->regime == 1) ? 0.00 : $pCredSN;
                        $vCredICMSSN = $item->imposto->ICMS->ICMSSN101->vCredICMSSN;
                        $vCredICMSSN = ($unit->regime == 1) ? 0.00 : $vCredICMSSN;
                        $bc_icms = "0.00";	
                        $pICMS = "0	";
                        $vlr_icms = "0.00";
                    }

                    if(!empty($ICMSSN102)){

                        $orig = $item->imposto->ICMS->ICMSSN102->orig;
                        $CST = $item->imposto->ICMS->ICMSSN102->CSOSN;
                        $bc_icms = "0.00";	
                        $pICMS = "0	";
                        $vlr_icms = "0.00";
                    }	

                    if(!empty($ICMSPart)){
                        
                        $bc_icms = "0.00";	
                        $pICMS = "0	";
                        $vlr_icms = "0.00";

                        $orig = $item->imposto->ICMS->ICMSPart->orig;
                        $CST = $item->imposto->ICMS->ICMSPart->CST;
                        $modBC = $item->imposto->ICMS->ICMSPart->modBC;
                        $bc_icms = $item->imposto->ICMS->ICMSPart->vBC;
                        $bc_icms = ($unit->regime == 1) ? 0.00 : $bc_icms;
                        $pICMS = $item->imposto->ICMS->ICMSPart->pICMS;
                        $pICMS = ($unit->regime == 1) ? 0.00 : $pICMS;
                        $vlr_icms = $item->imposto->ICMS->ICMSPart->vICMS;
                        $vlr_icms = ($unit->regime == 1) ? 0.00 : $vlr_icms;
                        $modBCST = $item->imposto->ICMS->ICMSPart->modBCST;
                        $modBCST = ($unit->regime == 1) ? 0.00 : $modBCST;
                        $vBCST = $item->imposto->ICMS->ICMSPart->vBCST;
                        $vBCST = ($unit->regime == 1) ? 0.00 : $vBCST;
                        $pICMSST = $item->imposto->ICMS->ICMSPart->pICMSST;
                        $pICMSST = ($unit->regime == 1) ? 0.00 : $pICMSST;
                        $vICMSST = $item->imposto->ICMS->ICMSPart->vICMSST;
                        $vICMSST = ($unit->regime == 1) ? 0.00 : $vICMSST;
                        $pBCOp = $item->imposto->ICMS->ICMSPart->pBCOp;
                        $pBCOp = ($unit->regime == 1) ? 0.00 : $pBCOp;
                        $UFST = $item->imposto->ICMS->ICMSPart->UFST;
                        $UFST = ($unit->regime == 1) ? 0.00 : $UFST;
                    }

                    if(!empty($icms10)){

                        $orig = $item->imposto->ICMS->ICMS10->orig;
                        $CST = $item->imposto->ICMS->ICMS10->CST;
                        $modBC = $item->imposto->ICMS->ICMS10->modBC;
                        $bc_icms = $item->imposto->ICMS->ICMS10->vBC;
                        $bc_icms = ($unit->regime == 1) ? 0.00 : $bc_icms;
                        $pICMS = $item->imposto->ICMS->ICMS10->pICMS;
                        $pICMS = ($unit->regime == 1) ? 0.00 : $pICMS;
                        $vlr_icms = $item->imposto->ICMS->ICMS10->vICMS;
                        $vlr_icms = ($unit->regime == 1) ? 0.00 : $vlr_icms;
                        $modBCST = $item->imposto->ICMS->ICMS10->modBCST;
                        $modBCST = ($unit->regime == 1) ? 0.00 : $modBCST;
                        $pMVAST = $item->imposto->ICMS->ICMS10->pMVAST;
                        $pMVAST = ($unit->regime == 1) ? 0.00 : $pMVAST;
                        $vBCST = $item->imposto->ICMS->ICMS10->vBCST;
                        $vBCST = ($unit->regime == 1) ? 0.00 : $vBCST;
                        $pICMSST = $item->imposto->ICMS->ICMS10->pICMSST;
                        $pICMSST = ($unit->regime == 1) ? 0.00 : $pICMSST;
                        $vICMSST = $item->imposto->ICMS->ICMS10->vICMSST;
                        $vICMSST = ($unit->regime == 1) ? 0.00 : $vICMSST;
                    }
                
                    if (!empty($icms00)){

                        $orig = $item->imposto->ICMS->ICMS00->orig;
                        $CST = $item->imposto->ICMS->ICMS00->CST;
                        
                        $bc_icms = $item->imposto->ICMS->ICMS00->vBC;
                        $bc_icms = ($unit->regime == 1) ? 0.00 : $bc_icms;
                        //$bc_icms = number_format((double) $bc_icms, 2, '.', '');
                        $pICMS = $item->imposto->ICMS->ICMS00->pICMS;
                        $pICMS = ($unit->regime == 1) ? 0.00 : $pICMS;
                        //$pICMS = round($pICMS,0);
                        $vlr_icms = $item->imposto->ICMS->ICMS00->vICMS;
                        $vlr_icms = ($unit->regime == 1) ? 0.00 : $vlr_icms;
                        //$vlr_icms = number_format((double) $vlr_icms, 2, '.', '');
                    }
                    if (!empty($icms20))
                    {   
                        $orig = $item->imposto->ICMS->ICMS20->orig;
                        $CST = $item->imposto->ICMS->ICMS20->CST;

                        $bc_icms = $item->imposto->ICMS->ICMS20->vBC;
                        //$bc_icms = ($unit->regime == 1) ? 0.00 : $bc_icms;
                        //$bc_icms = number_format((double) $bc_icms, 2, '.', '');
                        $pICMS = $item->imposto->ICMS->ICMS20->pICMS;
                        //$pICMS = ($unit->regime == 1) ? 0.00 : $pICMS;
                        //$pICMS = round($pICMS,0);
                        $vlr_icms = $item->imposto->ICMS->ICMS20->vICMS;
                        //$vlr_icms = ($unit->regime == 1) ? 0.00 : $vlr_icms;
                        //$vlr_icms = number_format((double) $vlr_icms, 2, '.', '');
                    }
                    if(!empty($icms30)) {

                        $orig = $item->imposto->ICMS->ICMS30->orig;
                        $CST = $item->imposto->ICMS->ICMS30->CST;
                        $vBCST = $item->imposto->ICMS->ICMS30->vBCST;
                        $vBCST = ($unit->regime == 1) ? 0.00 : $vBCST;
                        $pICMSST = $item->imposto->ICMS->ICMS30->pICMSST;
                        $pICMSST = ($unit->regime == 1) ? 0.00 : $pICMSST;
                        $vICMSST = $item->imposto->ICMS->ICMS30->vICMSST;
                        $vICMSST = ($unit->regime == 1) ? 0.00 : $vICMSST;
                        $bc_icms = "0.00";	
                        $pICMS = "0";
                        $vlr_icms = "0.00";
                    }
                    if(!empty($icms40)) {

                        $orig = $item->imposto->ICMS->ICMS40->orig;
                        $CST = $item->imposto->ICMS->ICMS40->CST;

                        $bc_icms = "0.00";	
                        $pICMS = "0	";
                        $vlr_icms = "0.00";
                    }
                    if(!empty($icms50)) {

                        $orig = $item->imposto->ICMS->ICMS50->orig;
                        $CST = $item->imposto->ICMS->ICMS50->CST;

                        $bc_icms = "0.00";	
                        $pICMS = "0	";
                        $vlr_icms = "0.00";
                    }
                    if(!empty($icms51)) {

                        $orig = $item->imposto->ICMS->ICMS51->orig;
                        $CST = $item->imposto->ICMS->ICMS51->CST;

                        $bc_icms = $item->imposto->ICMS->ICMS51->vBC;
                        $bc_icms = ($unit->regime == 1) ? 0.00 : $bc_icms;
                        $pICMS = $item->imposto->ICMS->ICMS51->pICMS;
                        $pICMS = ($unit->regime == 1) ? 0.00 : $pICMS;
                        //$pICMS = round($pICMS,0);
                        $vlr_icms = $item->imposto->ICMS->ICMS51->vICMS;
                        $vlr_icms = ($unit->regime == 1) ? 0.00 : $vlr_icms;
                    }
                    if(!empty($icms60)) {

                        $orig = $item->imposto->ICMS->ICMS60->orig;
                        $CST = $item->imposto->ICMS->ICMS60->CST;

                        $bc_icms = "0.00";	
                        $pICMS = "0";
                        $vlr_icms = "0.00";
                    }

                    if(!empty($icms70)) {

                        $orig = $item->imposto->ICMS->ICMS70->orig;
                        $CST = $item->imposto->ICMS->ICMS70->CST;
                        $modBCST = $item->imposto->ICMS->ICMS70->modBCST;
                        $pRedBC = $item->imposto->ICMS->ICMS70->pRedBC;
                        //$pRedBC = ($unit->regime == 1) ? 0.00 : $pRedBC;
                        $bc_icms = $item->imposto->ICMS->ICMS70->vBC;
                        //$bc_icms = ($unit->regime == 1) ? 0.00 : $bc_icms;
                        $pICMS = $item->imposto->ICMS->ICMS70->pICMS;
                        //$pICMS = ($unit->regime == 1) ? 0.00 : $pICMS;
                        $vlr_icms = $item->imposto->ICMS->ICMS70->vICMS;
                        //$vlr_icms = ($unit->regime == 1) ? 0.00 : $vlr_icms;
                        $modBC = $item->imposto->ICMS->ICMS70->modBC;
                        $pMVAST = $item->imposto->ICMS->ICMS70->pMVAST;
                        //$pMVAST = ($unit->regime == 1) ? 0.00 : $pMVAST;
                        $vBCST = $item->imposto->ICMS->ICMS70->vBCST;
                        //$vBCST = ($unit->regime == 1) ? 0.00 : $vBCST;
                        $pICMSST = $item->imposto->ICMS->ICMS70->pICMSST;
                        //$pICMSST = ($unit->regime == 1) ? 0.00 : $pICMSST;
                        $vICMSST = $item->imposto->ICMS->ICMS70->vICMSST;
                        //$vICMSST = ($unit->regime == 1) ? 0.00 : $vICMSST;
                    }

                    if(!empty($icms90)) {

                        $orig = $item->imposto->ICMS->ICMS90->orig;
                        $CST = $item->imposto->ICMS->ICMS90->CST;
                        $modBC = $item->imposto->ICMS->ICMS90->modBC;
                        $bc_icms = $item->imposto->ICMS->ICMS90->vBC;
                        $bc_icms = ($unit->regime == 1) ? 0.00 : $bc_icms;
                        $pICMS = $item->imposto->ICMS->ICMS90->pICMS;
                        $pICMS = ($unit->regime == 1) ? 0.00 : $pICMS;
                        $vlr_icms = $item->imposto->ICMS->ICMS90->vICMS;
                        $vlr_icms = ($unit->regime == 1) ? 0.00 : $vlr_icms;
                        $modBCST = $item->imposto->ICMS->ICMS90->modBCST;
                        $vBCST = $item->imposto->ICMS->ICMS90->vBCST;
                        $vBCST = ($unit->regime == 1) ? 0.00 : $vBCST;
                        $pICMSST = $item->imposto->ICMS->ICMS90->pICMSST;
                        $pICMSST = ($unit->regime == 1) ? 0.00 : $pICMSST;
                        $vICMSST = $item->imposto->ICMS->ICMS90->vICMSST;
                        $vICMSST = ($unit->regime == 1) ? 0.00 : $vICMSST;
                    }


                    $cst_ipi = null;
                    $bc_ipi = 0;
                    $perc_ipi =  0;
                    $vlr_ipi = 0;
                    $cod_Enq = null;

                    $IPITrib = $item->imposto->IPI->IPITrib;
                    if (!empty($IPITrib)){

                        $cod_Enq = $item->imposto->IPI->cEnq;
                        $cst_ipi = $item->imposto->IPI->IPITrib->CST;
                        $bc_ipi = $item->imposto->IPI->IPITrib->vBC;
                        $bc_ipi = ($unit->regime == 1) ? 0.00 : $bc_ipi;
                        //$bc_ipi = number_format((double) $bc_ipi, 2, '.', '');
                        $perc_ipi =  $item->imposto->IPI->IPITrib->pIPI;
                        $perc_ipi = ($unit->regime == 1) ? 0.00 : $perc_ipi;
                        //$perc_ipi = round($perc_ipi,0);
                        $vlr_ipi = $item->imposto->IPI->IPITrib->vIPI;
                        $vlr_ipi = ($unit->regime == 1) ? 0.00 : $vlr_ipi;
                        //$vlr_ipi = number_format((double) $vlr_ipi, 2, '.', '');
                    }

                    $cst_pis = null;
                    $bc_pis = 0;
                    $perc_pis =  0;
                    $vlr_pis = 0;

                    $PIS = $item->imposto->PIS->PISOutr;
                    if (!empty($PIS)){

                        $cst_pis = $item->imposto->PIS->PISOutr->CST;
                        $bc_pis  = $item->imposto->PIS->PISOutr->vBC;
                        $bc_pis = ($unit->regime == 1) ? 0.00 : $bc_pis;
                        $perc_pis =  $item->imposto->PIS->PISOutr->pPIS;
                        $perc_pis = ($unit->regime == 1) ? 0.00 : $perc_pis;
                        $vlr_pis = $item->imposto->PIS->PISOutr->vPIS;
                        $vlr_pis = ($unit->regime == 1) ? 0.00 : $vlr_pis;
                    }

                    $cst_cofins = null;
                    $bc_cofins = 0;
                    $perc_cofins =  0;
                    $vlr_cofins = 0;

                    $COFINS = $item->imposto->COFINS->COFINSOutr;
                    if (!empty($COFINS)){

                        $cst_cofins = $item->imposto->COFINS->COFINSOutr->CST;
                        $bc_cofins = $item->imposto->COFINS->COFINSOutr->vBC;
                        $bc_cofins = ($unit->regime == 1) ? 0.00 : $bc_cofins;
                        $perc_cofins =  $item->imposto->COFINS->COFINSOutr->pCOFINS;
                        $perc_cofins = ($unit->regime == 1) ? 0.00 : $perc_cofins;
                        $vlr_cofins = $item->imposto->COFINS->COFINSOutr->vCOFINS;
                        $vlr_cofins = ($unit->regime == 1) ? 0.00 : $vlr_cofins;
                    }

                    //sql dos itens
                    $itens = new NfeEntradaItens();
                    $itens->nfe_entrada_id = $nfeEntrada->id;
                    $itens->sequencia = $seq;
                    $itens->cProd = "{$codigo}";
                    $itens->cEAN = "{$cEAN}";
                    $itens->xProd = "{$xProd}";
                    $itens->NCM = "{$NCM}";
                    $itens->CEST = "{$CEST}";
                    $itens->CFOP = "{$CFOP}";
                    $itens->uCom = "{$uCom}";
                    $itens->qCom = "{$qCom}";
                    $itens->vUnCom = "{$vUnCom}";
                    $itens->vProd = "{$vProdu}";
                    $itens->cEANTrib = "{$cEANTrib}";
                    $itens->uTrib = "{$uTrib}";
                    $itens->qTrib = "{$qTrib}";
                    $itens->vUnTrib = "{$vUnTrib}";
                    $itens->indTot = "{$indTot}";
                    $itens->rastro_nLote = "{$rastro_nLote}";
                    $itens->rastro_qLote = "{$rastro_qLote}";
                    $itens->rastro_dFab = "{$rastro_dFab}";
                    $itens->rastro_dVal = "{$rastro_dVal}";
                    $itens->vTotTrib = "{$vTotTrib}";
                    $itens->ICMS_orig = "{$orig}";
                    $itens->ICMS_vBC = "{$bc_icms}";
                    $itens->ICMS_CST = "{$CST}";
                    $itens->ICMS_pICMS = "{$pICMS}";
                    $itens->ICMS_vICMS = "{$vlr_icms}";
                    $itens->ICMS_modBCST = "{$modBCST}";

                    $itens->ICMS_vBCST = "{$vBCST}";
                    $itens->ICMS_pICMSST = "{$pICMSST}";
                    $itens->ICMS_vICMSST = "{$vICMSST}";
                    $itens->ICMS_pCredSN = "{$pCredSN}";
                    $itens->ICMS_vCredICMSSN = "{$vCredICMSSN}";
                    $itens->ICMS_pMVAST = "{$pMVAST}";
                    $itens->ICMS_pRedBCST = "{$pRedBCST}";
                    $itens->ICMS_modBC = "{$modBC}";
                    $itens->ICMS_pRedBC = "{$pRedBC}";
                    $itens->ICMS_pBCOp = "{$pBCOp}";
                    $itens->ICMS_UFST = "{$UFST}";


                    $itens->PIS_CST = "{$cst_pis}";
                    $itens->PIS_vBC = "{$bc_pis}";
                    $itens->PIS_pPIS = "{$perc_pis}";
                    $itens->PIS_vPIS = "{$vlr_pis}";

                    $itens->COFINS_CST = "{$cst_cofins}";
                    $itens->COFINS_vBC = "{$bc_cofins}";
                    $itens->COFINS_pCOFINS = "{$perc_cofins}";
                    $itens->COFINS_vCOFINS = "{$vlr_cofins}";

                    $itens->IPI_CST = "{$cst_ipi}";
                    $itens->IPI_vBC = "{$bc_ipi}";
                    $itens->IPI_pIPI = "{$perc_ipi}";
                    $itens->IPI_vIPI = "{$vlr_ipi}";
                    $itens->IPI_cENQ = "{$cod_Enq}";

                    $itens->store();
                } 
            }        
        }
        TTransaction::close();
    }
}
