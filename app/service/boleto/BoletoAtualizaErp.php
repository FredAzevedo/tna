<?php

// ini_set('display_errors',1);
// ini_set('display_startup_erros',1);
// error_reporting(E_ALL);

use Carbon\Carbon;
class BoletoAtualizaErp
{   

    public function run()
    {

        TTransaction::open('sample');
        $dados_api_integracao = new ApiIntegracao(1);
        TTransaction::close();

        // $credencial     = "6a2b8bb152f455e181873efc299f66e522e1e398";
        // $chave          = "7ede60a763b8955e8bf3e82f2b84906ab101adb0";
        // $ambiente       = "https://api.pjbank.com.br";
        // $data_inicio    = "07/01/2021"; 
        // $data_fim       = "07/06/2021";
        // $pago           = '2';

        $dataAtual = new DateTime();
        $dataSubtraida = $dataAtual->modify('-30 days');
        $data_atual = date('m/d/Y');

        $credencial     = $dados_api_integracao->credencial;
        $chave          = $dados_api_integracao->chave;
        $ambiente       = $dados_api_integracao->url;
        $data_inicio    = $dataSubtraida->format('m/d/Y');
        $data_fim       = $data_atual;
        $pago           = "2";

        $return = BoletoService::consultarBoletos(
                $credencial, 
                $chave, 
                $ambiente, 
                $data_inicio, 
                $data_fim, 
                $pago);
        self::atualizarERP($return);

    }

    public static function atualizarERP($param)
	{

      try
      {
          TTransaction::open('sample');
          $hook = new WebhookPJBank;
          TTransaction::close();
      	  $hook->onHook($param);

      }catch(Exception $e){
          throw new Exception($e->getMessage());
      }

	}
}
