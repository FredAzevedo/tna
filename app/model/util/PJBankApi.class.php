<?php

use Adianti\Database\TTransaction;

class PJBankApi {

    public function comunicacaoPJBank($method, $data, $url, $chave = null)
	{
		$curl = curl_init();
		$datajson = json_encode($data);
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => $method,
		  CURLOPT_POSTFIELDS => $datajson,
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/json",
		    "X-CHAVE: ".$chave
		  ),
		));
		
		$response = curl_exec($curl);
		curl_close($curl);
		//var_dump($response);
		return $response;
	}

	//RECEBIMENTOS
	//BOLETO BANCÁRIO

	//emitir boleto registrado
	public function emitirBoleto($param)
	{
		
		$url = $param->ambiente."/recebimentos/".$param->credencial."/transacoes";

		$data = [
		  	"vencimento" =>  $param->vencimento, 
		  	"valor" =>  $param->valor,
		  	"juros" =>  $param->juros,  
		  	"multa" =>  $param->multa, 
		  	"juros_fixo" => $param->juros_fixo, 
		  	"desconto" =>  $param->desconto,  
		  	"diasdesconto1" => $param->diasdesconto1,
		  	"desconto2" =>  $param->desconto2,
		  	"diasdesconto2" => $param->diasdesconto2,
		  	"desconto3" =>  $param->desconto3,
		  	"diasdesconto3" => $param->diasdesconto3,
		  	"nunca_atualizar_boleto" => $param->nunca_atualizar_boleto,
		  	"nome_cliente" =>  $param->nome_cliente, 
		  	"email_cliente" => $param->email_cliente,
		  	"telefone_cliente" => $param->telefone_cliente,
		  	"cpf_cliente" => $param->cpf_cliente,
		  	"endereco_cliente" => $param->endereco_cliente, 
		  	"numero_cliente" =>  $param->numero_cliente, 
		  	"complemento_cliente" =>  $param->complemento_cliente, 
		  	"bairro_cliente" =>  $param->bairro_cliente,
		  	"cidade_cliente" =>  $param->cidade_cliente,
		  	"estado_cliente" =>  $param->estado_cliente,
		  	"cep_cliente" =>  $param->cep_cliente,
		  	"logo_url" =>  $param->logo_url,
		  	"texto" =>  $param->texto, 
		  	"instrucao_adicional" => $param->instrucao_adicional,
		  	"grupo" =>  $param->grupo,
		  	"webhook" => $param->webhook,
		  	"pedido_numero" =>  $param->pedido_numero,
		  	"especie_documento" => $param->especie_documento,
			"pix" => "pix-e-boleto",
			"exibir_zoom_boleto" => 1
		];

		$response = self::comunicacaoPJBank("POST",$data, $url, $param->chave);
		return $response;
		
	}

	//imprimir boletos registrados em lote
	public function imprimirBoletoLote($param)
	{
		$url = $param->ambiente."/recebimentos/".$param->credencial."/transacoes/lotes";

		$data = [
			"pedido_numero" => [$param->pedido_numero]
		];

		$response = self::comunicacaoPJBank("POST",$data, $url, $param->chave);
		return $response;
	}

	//Imprimir carnê de boletos bancários
	public function imprimirCarne($param)
	{

		$url = $param->ambiente."/recebimentos/".$param->credencial."/transacoes/lotes";

		$data = [
			"pedido_numero" => $param->pedido_numero,
			"formato" => "carne"
		];

		$response = self::comunicacaoPJBank("POST",$data, $url, $param->chave);
		return $response;
	}

	//Emitir um boleto com pagamento parcelado
	public function imprimirBoletoParcelado($param)
	{
	
		$url = $param->ambiente."/recebimentos/".$param->credencial."/transacoes";
		$data =  [
			 "vencimento" => $param->vencimento, 
			 "valor" => $param->valor, 
			 "juros" => $param->juros, 
			 "multa" => $param->multa, 
			 "desconto" => $param->desconto, 
			 "nome_cliente" => $param->nome_cliente, 
			 "cpf_cliente" => $param->cpf_cliente, 
			 "endereco_cliente" => $param->endereco_cliente, 
			 "numero_cliente" => $param->numero_cliente, 
			 "complemento_cliente" => $param->complemento_cliente, 
			 "bairro_cliente" => $param->bairro_cliente, 
			 "cidade_cliente" => $param->cidade_cliente, 
			 "estado_cliente" => $param->estado_cliente, 
			 "cep_cliente" => $param->cep_cliente, 
			 "logo_url" => $param->logo_url, 
			 "texto" => $param->texto, 
			 "grupo" => $param->grupo, 
			 "split" => 
			 	[ 	
			 		"nome" =>"Fornecedor Exemplo", 
			 		"cnpj" =>"10488175000143", 
			 		"banco_repasse" =>"001", 
			 		"agencia_repasse" =>"0001", 
			 		"conta_repasse" =>"99999-9", 
			 		"valor_porcentagem" =>50.00 
			 	]
		];

		$response = self::comunicacaoPJBank("POST",$data, $url, $param->chave);
		return $response;
		
	}

	//invalidar boleto bancário (cancelar)
	public function invalidarBoleto($param)
	{
		$url = $param->ambiente."/recebimentos/".$param->credencial."/transacoes/".$param->pedido_numero;

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "DELETE",
		  CURLOPT_HTTPHEADER => array(
		    "X-CHAVE: ".$param->chave
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		print_r($response);
		return $response;
	}

	public function consultarCredencial($param)
	{

		$url = $param->ambiente."/recebimentos/".$param->credencial;
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "X-CHAVE: ".$param->chave
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		print_r($response);
		return $response;

	}

	public static function criarCredencial($param)
	{

		$url = $param->ambiente."/recebimentos";
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS =>"{\n  \"nome_empresa\": \"Multimais administradora de cartao Ltda\",\n  \"conta_repasse\": \"05634-0\",\n  \"agencia_repasse\": \"2207\",\n  \"banco_repasse\": \"748\",\n  \"cnpj\": \"26325623000128\",\n  \"ddd\": \"84\",\n  \"telefone\": \"992108421\",\n  \"email\": \"wesley.soares@grupomultimais.com\",\n \"cartao\": \"true\",\n \"agencia\": \"0899\"\n }",
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/json",
		    "Accept: application/json"
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		print_r($response);
		return $response;

	}

	//Consultar boletos por identificador
	public static function consultarBoletos($param)
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $param->ambiente."/recebimentos/".$param->credencial."/transacoes/".$param->nossonumero,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "X-CHAVE: ".$param->chave
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;

	}

	//Consultar boletos por recebimento
	public static function consultarBoletosRecebimento($param)
	{

		$curl = curl_init();
	
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $param->ambiente."/recebimentos/".$param->credencial."/transacoes?data_inicio=".$param->data_inicio."&data_fim=".$param->data_fim."&pago=".$param->pago,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "X-CHAVE: ".$param->chave
		  ),
		));

		$response = curl_exec($curl);
		
		curl_close($curl);
		return $response;

	}

}