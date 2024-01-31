<?php

use CWG\PagSeguro\PagSeguroAssinaturas;
class PagSeguroService
{

    public static function consultarStatusAssinatura($contrato_id)
    {
        try {

            TTransaction::open('sample');
            $contrato = new ClienteContrato($contrato_id);
            
            //Valores passados por parametros entre paginas
            $data = new StdClass;
            $data->contrato_id = $contrato_id;


            $codigo_assinatura = $contrato->codigo_assinatura_plano;
            $gateway = ConfiguracaoGateway::where('nome_gateway', '=', 'PagSeguro')->first();
            $email = $gateway->email;
            $token = $gateway->token;
            $sandbox = PAGSEGURO_SANDBOX;

            $pagseguro = new PagSeguroAssinaturas($email, $token, $sandbox);

            // $codigo_assinatura = '481AF3A88484F582240EEFA849AD1AA1';
            $response = $pagseguro->consultaAssinatura($codigo_assinatura);
            // var_dump($response);
            // die;

            if($response['status'] == 'ACTIVE')
                $data->status_assinatura = 'ATIVO';
            else if($response['status'] == 'CANCELLED')
                $data->status_assinatura = 'CANCELADO';
            else if($response['status'] == 'PENDING')
                $data->status_assinatura = 'PAGAMENTO PENDENTE';


            AdiantiCoreApplication::loadPage('ConsultaAprovacaoPagSeguroPage', '', (array) $data);

            TTransaction::close(); 
        }
        catch (Exception $e) {
            TTransaction::rollback();
            TTransaction::close(); 
            new TMessage('error', $e->getMessage());
        }
    }



}
