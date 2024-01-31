<?php

ini_set('max_execution_time', '3000');

class Teste extends TPage
{

    public function __construct()
    {
        parent::__construct();
        
	}

    public function run()
    {

        TTransaction::open('sample');
        $dados_api_integracao = new ApiIntegracao(1);
        TTransaction::close();

        $credencial     = "6a2b8bb152f455e181873efc299f66e522e1e398";
        $chave          = "7ede60a763b8955e8bf3e82f2b84906ab101adb0";
        $ambiente       = "https://api.pjbank.com.br";
        // $data_inicio    = "07/01/2021"; 
        // $data_fim       = "07/06/2021";
        // $pago           = '2';

        $dataAtual = new DateTime();
        $dataSubtraida = $dataAtual->sub(new DateInterval('P30D'));
        $data_atual = date('m/d/Y');

        // $credencial     = $dados_api_integracao->credencial;
        // $chave          = $dados_api_integracao->chave;
        // $ambiente       = $dados_api_integracao->ambiente;
        $data_inicio    = $dataSubtraida->format('m/d/Y');
        $data_fim       = $data_atual;
        $pago           = '2';

        $return = BoletoService::consultarBoletos(
                $credencial, 
                $chave, 
                $ambiente, 
                $data_inicio, 
                $data_fim, 
                $pago);
        $json = json_encode($return, true);
        self::atualizarERP($json);

    }

    public static function atualizarERP($param)
	{

		$curl = curl_init();
	
		curl_setopt_array($curl, array(
          CURLOPT_URL => "https://serido.macroerp.com.br/engine.php?class=WebhookPJBank&method=onHook&static=1",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 300,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS =>$param,
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/json",
		    "Accept: application/json"
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		var_dump($response);

	}

}
