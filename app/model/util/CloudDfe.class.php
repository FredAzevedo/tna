<?php

use Adianti\Database\TTransaction;

class CloudDfe {

	private $token;
	private $url;

	public function __construct($integracao)
    {    
		$this->token   	= $integracao->credencial;
		$this->url   	= $integracao->url;
        //$this->url   	= $integracao->sandbox;//homologacao sandbox | 
    }

	public function comunicacaoCloudDfe( $endpoint, $method, $data )
	{
		//die(sprintf($this->url."%s",$endpoint));
		$ch = curl_init(sprintf($this->url."%s",$endpoint));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    "Authorization: ".$this->token,
		    "Content-Type: application/json"
		]);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$resp = curl_exec($ch);
		
		return json_decode($resp);
	}

	//NFSE	
	//enviar nfse


	// NFE
	// =============================================================================================================
	public function enviarNfe( $param )
	{	
		try
		{
			TTransaction::open('sample');

			$id = $param['id'];
			$nfe = new NFe($id);
		
			$data = [

					//"codigo_aleatorio" => rand(1, 99999999),
					"numero" => $nfe->ide_nNF,
					"serie" => $nfe->ide_serie,
					"natureza_operacao" => Utilidades::tirarCaracterEspecial($nfe->ide_natOp),
					"data_emissao" => date("Y-m-d\TH:i:sP",strtotime($nfe->ide_dEmi)),
					"data_entrada_saida" => date("Y-m-d\TH:i:sP",strtotime($nfe->ide_SaiEnt)),
					"tipo_operacao" => $nfe->ide_tpNF, //0: Nota de entrada ; 1: Nota de saída
					"finalidade_emissao" => $nfe->ide_finNFe,
					"local_destino" => $nfe->idDest, //1: Operação interna, 2: Operação interestadual, 3: Operação com exterior
					"consumidor_final" => $nfe->indFinal,//0: Normal , 1: Consumidor final
					"presenca_comprador" => $nfe->indPres,
					"intermediario" => [
						"indicador" => "0"
					],
					"notas_referenciadas" => [],
					"destinatario" => [
						"cpf" => (strlen($nfe->dest_cnpj_cpf) == 14) ? Utilidades::limpaCaracter($nfe->dest_cnpj_cpf) : null,
						"cnpj" => (strlen($nfe->dest_cnpj_cpf) == 18) ? Utilidades::limpaCaracter($nfe->dest_cnpj_cpf) : null,
						"nome" =>  $nfe->dest_xNome,
						"indicador_inscricao_estadual" => $nfe->indIEDest,
						"inscricao_estadual" => $nfe->dest_IE,
						"endereco" => [
							"logradouro" => $nfe->dest_xLgr,
							"numero" => $nfe->dest_nro,
							"bairro" => $nfe->dest_xBairro,
							"codigo_municipio" => $nfe->dest_cMun,
							"nome_municipio" => $nfe->dest_xMun,
							"uf" => $nfe->dest_UF,
							"cep" => Utilidades::limpaCaracter($nfe->dest_CEP),
							"codigo_pais" => "1058",
							"nome_pais" => "BRASIL",
							"telefone" => Utilidades::limpaCaracter($nfe->dest_fone)
						]
					],
					"itens" => [],
					"frete" => [
						"modalidade_frete" => $nfe->modFrete,
						"volumes" => [],
						"transportador" => []
					],
					// "cobranca" => [
					// 	"fatura" => [
					// 		"numero" => null,
					// 		"valor_original" => null,
					// 		"valor_desconto" => null,
					// 		"valor_liquido" => null
					// 	]
					// ],
					"pagamento" => [
						"formas_pagamento" => [
							
						]
					],
					"informacoes_adicionais_fisco" => Utilidades::tirarCaracterEspecial($nfe->infAdFisco),
					"informacoes_adicionais_contribuinte" => Utilidades::tirarCaracterEspecial($nfe->infCpl),
					"pessoas_autorizadas" => [
						// [
						// 	"cnpj" => (strlen($nfe->dest_cnpj_cpf) == 18) ? Utilidades::limpaCaracter($nfe->dest_cnpj_cpf) : null,
						// ]
					]
			];
			//notas referenciadas
			$nfeReferenciadas = NfeReferenciadas::where('nfe_id','=',$id)->load();

			if($nfeReferenciadas){
				foreach( $nfeReferenciadas as $idx => $ref) {
					
					$data["notas_referenciadas"][$idx]["nfe"] = ["chave" => $ref->chave,];
				}
			}

			// tipo de pagamento
			$formaPgto =  NfeFaturamento::where('nfe_id','=',$id)->load();

			foreach($formaPgto as $idx => $tipo){

				$tipo_pgto = new TipoPgto($tipo->tipo_pgto_id);
				// var_dump($tipo_pgto);
				$data["pagamento"]["formas_pagamento"][$idx] = [
					
					"meio_pagamento" => $tipo_pgto->cod_nfe,
					"valor" => ($tipo_pgto->cod_nfe != '90') ? $tipo->valor : "0",
					"tipo_integracao" => "2",
					
				];
			}

			// frete -> volumos do frete
			if($nfe->modFrete != 9){

				$data["frete"]["volumes"] = [
					"quantidade" => $nfe->vol_qVol,
					"especie" => $nfe->vol_esp,
					"marca" => $nfe->vol_marca,
					"numero" => $nfe->vol_nVol,
					"peso_liquido" => $nfe->vol_pesoL,
					"peso_bruto" => $nfe->vol_pesoB
				];
			}

			if($nfe->transp_cnpj_cpf != null){

				$data["frete"]["transportador"] = [
					"cnpj" => (strlen($nfe->transp_cnpj_cpf) == 18) ? Utilidades::limpaCaracter($nfe->transp_cnpj_cpf) : null,
					"cpf" => (strlen($nfe->transp_cnpj_cpf) == 14) ? Utilidades::limpaCaracter($nfe->transp_cnpj_cpf) : null,
					"nome" => Utilidades::tirarCaracterEspecial($nfe->transp_xNome),
					"inscricao_estadual" => $nfe->transp_IE,
					"endereco" => Utilidades::tirarCaracterEspecial($nfe->transp_xEnder),
					"nome_municipio" => $nfe->transp_xMun,
					"uf" => $nfe->transp_UF
				];
			}

			$nfeItens = NfeItens::where('nfe_id','=',$id)->load();

			if($nfeItens){

				foreach ($nfeItens as $idx => $produto) {

					$data["itens"][$idx] = [
						//"numero_item" => $produto[0],
						"numero_item" => $produto->sequencia,
						"codigo_produto" => $produto->produto_id,
						"descricao" => $produto->produto->nome_produto,
						"codigo_ncm" => $produto->produto->ncm,
						"cfop" => $produto->cfop,
						"unidade_comercial" => $produto->produto->produto_unidade_medida->cod,
						"quantidade_comercial" => $produto->quantidade,
						"valor_unitario_comercial" => $produto->preco,
						"valor_bruto" => $produto->total,
						"unidade_tributavel" => $produto->produto->produto_unidade_medida->cod,
						"quantidade_tributavel" => $produto->quantidade,
						"valor_unitario_tributavel" => $produto->preco,
						"origem" => $produto->produto->orig,
						"inclui_no_total" => "1",
						"valor_desconto" => $produto->desconto,
						"valor_frete" => $produto->vFrete,
						"valor_seguro" => $produto->vSeg,
						"valor_outras_despesas" => $produto->vOutro,
						"informacoes_adicionais_item" => "",
						"imposto" => [
							"valor_total_tributos" => 9.43,
							"icms" => [
								"situacao_tributaria" => $produto->cst_icms,
								"modalidade_base_calculo" => "3",
								"valor_base_calculo" => $produto->bc_icms,
								"modalidade_base_calculo_st" => "4",
								"aliquota_reducao_base_calculo" => "0.00",
								"aliquota" => $produto->aliq_icms,
								"valor" => $produto->vlr_icms,
								
							],
							"pis" => [
								"situacao_tributaria" => $produto->cst_pis,
								"valor_base_calculo" => $produto->bc_pis,
								"aliquota" => $produto->aliq_pis,
								"valor" => $produto->vlr_pis,
							],
							"cofins" => [
								"situacao_tributaria" => $produto->cst_cofins,
								"valor_base_calculo" => $produto->bc_cofins,
								"aliquota" => $produto->aliq_cofins,
								"valor" => $produto->vlr_cofins,
							],
							"ipi" => []
						],

					];

					if($produto->cst_icms == '60' or $produto->cst_icms == '500' ){

						$data["itens"][$idx]['imposto']['icms'] += ["aliquota_final" => $produto->aliq_icms,];
						$data["itens"][$idx]['imposto']['icms'] += ["valor_st" => $produto->vicmsst,];
						// "margem_valor_adicionado_st" => "0.00",
						// "reducao_base_calculo_st" => "0.00",
						// "base_calculo_st" => "0.00",
						// "aliquota_st" => "0.00",

					}

					if($produto->cst_icms == '101'){
						
						$data["itens"][$idx]['imposto']['icms'] += ["aliquota_credito_simples" => $produto->pCredSN,];
						$data["itens"][$idx]['imposto']['icms'] += ["valor_credito_simples" => $produto->vCredICMSSN,];

					}

					$_cstICMSST = array('10','20','51','70','90','201','202','203','900');
					if(in_array($produto->cst_icms,$_cstICMSST)){

						//$data["itens"][$idx] += ["icms_base_calculo_retido_st" => $produto->pRedBCST,];

					}

					if($produto->cst_ipi != null){
						
						$data["itens"][$idx]['imposto']['ipi'] = ["situacao_tributaria" => $produto->cst_ipi,];
						$data["itens"][$idx]['imposto']['ipi'] += ["valor_base_calculo" => $produto->bc_ipi,];
						$data["itens"][$idx]['imposto']['ipi'] += ["aliquota" => $produto->aliq_ipi,];
						$data["itens"][$idx]['imposto']['ipi'] += ["codigo_enquadramento_legal" => $produto->enq_ipi,];
						$data["itens"][$idx]['imposto']['ipi'] += ["valor" => $produto->vlr_ipi,];
					}
				}
			}
			
			// $result = json_encode($data);
			// echo $result;die;
			$response = $this->comunicacaoCloudDfe("nfe", "POST", $data);
			return $response;

			TTransaction::close();

		} catch (Exception $e) {

            TTransaction::rollback();
            echo $e->getMessage();
            echo '<br>';
            echo $e->getTraceAsString();

        }
	}	

	//consultar uma nfe
	public function consultaNfe( $chave )
	{	
		try
		{
			$data = [];
			//var_dump($chave);
			$response = $this->comunicacaoCloudDfe("nfe/".$chave, "GET", $data);
			return $response;

		} catch (Exception $e) {

            TTransaction::rollback();
            echo $e->getMessage();
            echo '<br>';
            echo $e->getTraceAsString();
        }
	}

	// Inutilizar NF-e
	public function inutilizarNfe( $id )
	{	
		try
		{
			TTransaction::open('sample');

			$nfe = new NfeInutiliza($id);

			$data = [
				"serie" => $nfe->serie,
				"justificativa" => $nfe->justificativa,
				"numero_inicial" => $nfe->numero_inicial,
				"numero_final" => $nfe->numero_final
			];
			
			$response = $this->comunicacaoCloudDfe("nfe/inutiliza", "POST", $data);
			return $response;

			TTransaction::close();

		} catch (Exception $e) {

            TTransaction::rollback();
            echo $e->getMessage();
            echo '<br>';
            echo $e->getTraceAsString();
        }
	}

	public function cancelarNfe( $nfe_id )
	{	
		try
		{
			TTransaction::open('sample');

			$nfe = new Nfe($nfe_id);

			$data = [
				"chave" => $nfe->chave,
				"justificativa" => $nfe->motivo_cancelamento
			];
			
			$response = $this->comunicacaoCloudDfe("nfe/cancela", "POST", $data);
			return $response;

			TTransaction::close();

		} catch (Exception $e) {

			TTransaction::rollback();
			echo $e->getMessage();
			echo '<br>';
			echo $e->getTraceAsString();

		}
	}

	public function cceNfe( $nfe_id )
	{	
		try
		{
			TTransaction::open('sample');

			$nfe = new Nfe($nfe_id);

			$data = [
				"chave" => $nfe->chave,
				"justificativa" => $nfe->motivo_cce
			];
			
			$response = $this->comunicacaoCloudDfe("nfe/correcao", "POST", $data);
			return $response;

			TTransaction::close();

		} catch (Exception $e) {

			TTransaction::rollback();
			echo $e->getMessage();
			echo '<br>';
			echo $e->getTraceAsString();

		}
	}

	//MDFE

	//CTE

	//SPED


}
