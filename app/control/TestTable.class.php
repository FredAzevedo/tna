<?php

class TestTable extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        $sandbox 	= "https://sandbox.pjbank.com.br";
        $producao 	= "https://api.pjbank.com.br";
        $chave 		= "d474ec92ee9639e075e3cb375f5c26b26f9be36d";
        $credencial = "9f1136b508e36de1a95085f990a9728c0d15dcf9";

        $boleto = new StdClass;
        $boleto->vencimento =  "04/01/2020"; 
	  	$boleto->valor =  10.00;
	  	$boleto->juros =  0;  
	  	$boleto->multa =  0; 
	  	$boleto->desconto =  "";  
	  	$boleto->nome_cliente = "Cliente de Exemplo";  
	  	$boleto->cpf_cliente = "05131888433";
	  	$boleto->endereco_cliente = "Rua Joaquim Vilac"; 
	  	$boleto->numero_cliente =  "509"; 
	  	$boleto->complemento_cliente =  ""; 
	  	$boleto->bairro_cliente =  "Vila Teixeira";
	  	$boleto->cidade_cliente =  "Campinas";
	  	$boleto->estado_cliente =  "SP";
	  	$boleto->cep_cliente =  "13301510";
	  	$boleto->email_cliente =  "fred@macroerp.com.br";
	  	$boleto->telefone_cliente =  "8888888888";
	  	$boleto->logo_url =  "http://wallpapercave.com/wp/xK64fR4.jpg";
	  	$boleto->texto =  "Exemplo de emissão de boleto"; 
	  	$boleto->grupo =  "Boletos"; //Quando um valor é informado neste campo, é retornado um link adicional para impressão de todos os boletos do mesmo grupo.
	  	$boleto->pedido_numero =  "6666";
	  	$boleto->juros_fixo = "";
	  	$boleto->multa_fixo = "";
	  	$boleto->diasdesconto1 = "";
	  	$boleto->desconto2 = "";
	  	$boleto->diasdesconto2 = "";
	  	$boleto->desconto3 = "";
	  	$boleto->diasdesconto3 = "";
	  	$boleto->nunca_atualizar_boleto = 0; //0 - 1
	  	$boleto->instrucao_adicional = "Teste de emissão"; //nclusão do texto adicional abaixo da instrução referente a juros e descontos. length (0-255).
	  	$boleto->webhook = "https://macroerp.com.br";//informe uma URL de Webhook. Iremos chamá-la com as novas informações sempre que a cobrança for atualizada.
	  	$boleto->especie_documento = "DS";
	  	$boleto->credencial = $credencial;
	  	$boleto->ambiente = $producao;
	  	$boleto->chave = $chave;


	  	//PJBankApi::emitirBoleto($boleto);  //emitir um boleto OK
	  	//PJBankApi::imprimirBoletoLote($boleto); //consultar um boleto
	  	//PJBankApi::imprimirBoletoParcelado($boleto);
	  	//PJBankApi::invalidarBoleto($boleto); // invalidar (cnacelar) um boleto Ok
	  	//PJBankApi::imprimirCarne($boleto); // imprimir carne Ok
	  	//PJBankApi::consultarCredencial($boleto);
		PJBankApi::criarCredencial($boleto);
    }
}
