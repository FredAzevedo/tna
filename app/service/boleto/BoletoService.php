<?php

use Carbon\Carbon;
class BoletoService
{

    public static function emitirBoleto($credencial, $chave, $ambiente,
                                                   $formato,
                                                   $contrato_id = null, $vencimento, $valor, 
                                                   $texto = '',
                                                   $pedido_numero,
                                                   $juros = 1, $multa = 2, $desconto = 0, $grupo = null, $juros_fixo = 0, $multa_fixo = 0,
                                                   $diasdesconto1 = null, $desconto1 = null, $diasdesconto2 = null, $desconto2 = null, $diasdesconto3 = null, $desconto3 = null,
                                                   $nunca_atualizar_boleto = 0,
                                                   $instrucao_adicional = '', $conta_receber_id, $cliente_id,$split = null){
        try
        {
            TTransaction::open('sample');
            
            $cliente = new Cliente($cliente_id);
            
            $telefone_cliente = $cliente->getTelefonesClientes()[0]->telefone;
            $email_cliente = $cliente->getEmailClientes()[0]->email;

            // $endereco_cliente = $cliente->getClienteEndereco()[0];
            // if(empty($endereco_cliente)){
            //     throw new Exception('Endereço do cliente não encontrado.');
            // }

            $vencimento = Carbon::parse($vencimento);
            $data_vencimento_enviada_no_pjbank = str_pad($vencimento->month, 2, "0", STR_PAD_LEFT) . "/" . str_pad($vencimento->day, 2, "0", STR_PAD_LEFT) . "/" . $vencimento->year;
            
            $boleto = new BoletoApi;

            $boleto->formato = $formato;

            $boleto->cliente_id = $cliente->id;
            $boleto->unit_id = 1;
            $boleto->user_id = $cliente->user_id;

            $boleto->vencimento =  $data_vencimento_enviada_no_pjbank; 
            $boleto->valor = number_format($valor,2,'.','');
            $boleto->juros = $juros;
            $boleto->multa = $multa;
            $boleto->desconto = $desconto;

            $boleto->nome_cliente = $cliente->razao_social;
            $boleto->cpf_cliente = Utilidades::removerCaracteresEspeciais($cliente->cpf_cnpj);
            $boleto->endereco_cliente = $cliente->logradouro;
            $boleto->numero_cliente =  $cliente->numero;
            $boleto->complemento_cliente =  $cliente->complemento; 
            $boleto->bairro_cliente =  $cliente->bairro;
            $boleto->cidade_cliente =  $cliente->cidade;
            $boleto->estado_cliente =  $cliente->uf;
            $boleto->cep_cliente =  Utilidades::removerCaracteresEspeciais($cliente->cep);

            $boleto->email_cliente =  $email_cliente;
            $boleto->telefone_cliente =  Utilidades::removerCaracteresEspeciais($telefone_cliente);
            $boleto->logo_url = LOGO_URL;
            
            $boleto->texto = $texto;

            $boleto->grupo = $grupo; //Quando um valor é informado neste campo, é retornado um link adicional para impressão de todos os boletos do mesmo grupo.

            $boleto->pedido_numero = $pedido_numero;

            $boleto->juros_fixo = $juros_fixo;
            $boleto->multa_fixo = $multa_fixo;

            $boleto->diasdesconto1 = $diasdesconto1; //Quantidade de dias de antecedencia do pagamento que será dado desconto
            $boleto->desconto1 = $desconto1; // Valor em Reais do desconto

            $boleto->desconto2 = $desconto2;
            $boleto->diasdesconto2 = $diasdesconto2;

            $boleto->desconto3 = $desconto3;
            $boleto->diasdesconto3 = $diasdesconto3;

            $boleto->nunca_atualizar_boleto = 0; //0 - 1
            $boleto->instrucao_adicional = $instrucao_adicional; //Inclusão do texto adicional abaixo da instrução referente a juros e descontos. length (0-255).
            $boleto->webhook = "https://martins.macroerp.com.br/engine.php?class=WebhookPJBank&method=onHook&static=1";//WEBHOOK_URL; //informe uma URL de Webhook. Iremos chamá-la com as novas informações sempre que a cobrança for atualizada.
            $boleto->especie_documento = 'DS';

            $boleto->credencial = $credencial;
            $boleto->chave = $chave;
            $boleto->ambiente = $ambiente;
            $boleto->contas_receber_id = $conta_receber_id;
            $boleto->cliente_contrato_id = $contrato_id;

            //$boleto->split = $split;

            $retorno_boleto = PJBankApi::emitirBoleto($boleto);

            if($retorno_boleto->status == '200' || $retorno_boleto->status == '201'){

                $boleto->vencimento =  $vencimento->toDateTimeString();
                $boleto->status = $retorno_boleto->status;
                $boleto->msg = $retorno_boleto->msg;
                $boleto->nossonumero = $retorno_boleto->nossonumero;
                $boleto->id_unico = $retorno_boleto->id_unico;
                $boleto->banco_numero = $retorno_boleto->banco_numero;
                $boleto->token_facilitador = $retorno_boleto->token_facilitador;
                $boleto->credencial = $retorno_boleto->credencial;
                $boleto->linkBoleto = $retorno_boleto->linkBoleto;
                $boleto->linkGrupo = $retorno_boleto->linkGrupo;
                $boleto->linhaDigitavel = $retorno_boleto->linhaDigitavel;
                $boleto->store();

                new TMessage('info', 'Boleto gerado com sucesso');
                
            }
            else {
                throw new Exception('Status: '.$retorno_boleto->status.". Mensagem: ".$retorno_boleto->msg);
            }

            TTransaction::close();

            if($formato == 'BOLETO'){
                TSession::setValue('link', $retorno_boleto->linkBoleto);
                TApplication::loadPage('CatalogoLojaPagamentoBoleto', 'onEdit');

                TTransaction::open('sample');
                //pega o html do corpo do email
                $html_email = new Email(2); //email de boleto revenda
                $html_email->corpo = str_replace('[link]', $retorno_boleto->linkBoleto, $html_email->corpo);
                TTransaction::close();
                //envia o email
                MailService::send($email_cliente, 'Pedido Certificado Digital', $html_email->corpo, 'html');
    
            }

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
