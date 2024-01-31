<?php

class BoletoServiceIugu
{

    public static function emitirBoleto($credencial, $chave, $ambiente,
                                                   $formato,
                                                   $contrato_id = null, $vencimento, $valor, 
                                                   $texto = '',
                                                   $pedido_numero,
                                                   $juros = 1, $multa = 2, $desconto = 0, $grupo = null, $juros_fixo = 0, $multa_fixo = 0,
                                                   $diasdesconto1 = null, $desconto1 = null, $diasdesconto2 = null, $desconto2 = null, $diasdesconto3 = null, $desconto3 = null,
                                                   $nunca_atualizar_boleto = 0,
                                                   $instrucao_adicional = '', $conta_receber_id, $cliente_id){
        try
        {
            TTransaction::open('sample');
            
            $cliente = new Cliente($cliente_id);
            
            $telefone_cliente = $cliente->getTelefonesClientes()[0]->telefone;
            $email_cliente = $cliente->getEmailClientes()[0]->email;

            $endereco_cliente = $cliente->getClienteEndereco()[0];
            if(empty($endereco_cliente)){
                throw new Exception('Endereço do cliente não encontrado.');
            }

            $boleto = new BoletoApi;
            $boleto->formato = $formato;
            $boleto->cliente_id = $cliente->id;
            $boleto->unit_id = TSession::getValue('userunitid');
            $boleto->user_id = TSession::getValue('userid');
            $boleto->vencimento =  $vencimento; 
            $boleto->valor = number_format($valor,2,'.','');
            $boleto->nome_cliente = $cliente->razao_social;
            $boleto->cpf_cliente = $cliente->cpf_cnpj;
            $boleto->endereco_cliente = $endereco_cliente->logradouro;
            $boleto->numero_cliente =  $endereco_cliente->numero;
            $boleto->complemento_cliente =  $endereco_cliente->complemento; 
            $boleto->bairro_cliente =  $endereco_cliente->bairro;
            $boleto->cidade_cliente =  $endereco_cliente->cidade;
            $boleto->estado_cliente =  $endereco_cliente->uf;
            $boleto->cep_cliente =  $endereco_cliente->cep;
            $boleto->email_cliente =  $email_cliente;
            $boleto->telefone_cliente =  Utilidades::removerCaracteresEspeciais($telefone_cliente);
            $boleto->texto = $texto;
            $boleto->pedido_numero = $pedido_numero;
            $boleto->nunca_atualizar_boleto = 0; //0 - 1
            $boleto->especie_documento = 'DS';
            $boleto->credencial = $credencial;
            $boleto->chave = $chave;
            $boleto->url = $ambiente;
            $boleto->contas_receber_id = $conta_receber_id;
            $boleto->cliente_contrato_id = $contrato_id;

            //especifico Iugu
            $boleto->ignore_due_email = "false";
            $boleto->payable_with = "all";
            $boleto->quantity = 1;

            $retorno_boleto = IuguApi::emitirBoleto($boleto);

            if($retorno_boleto->status == 'pending'){

                $boleto->vencimento =  $boleto->vencimento;//->toDateTimeString();
                $boleto->status = '200';//$retorno_boleto->status'';
                $boleto->msg = 'sucsess.';
                $boleto->nossonumero = $retorno_boleto->transaction_number;
                $boleto->id_unico = $retorno_boleto->id;
                $boleto->banco_numero = '';
                $boleto->credencial = $retorno_boleto->credencial;
                $boleto->linkBoleto = $retorno_boleto->secure_url;
                $boleto->linhaDigitavel = $retorno_boleto->bank_slip->digitable_line;
                $boleto->store();

                new TMessage('info', 'Boleto gerado com sucesso');
                
            }
            else {
                throw new Exception('Status: '.$retorno_boleto->status.". Mensagem: Erro ao processar o Boleto, verifique se todos os dados estão corretos.");
            }

            if($formato == 'BOLETO'){
                $parametros['linkBoleto'] = $retorno_boleto->secure_url;
                TApplication::loadPage('ApiImprimeBoleto', 'onSavePDF', $parametros);
            }

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public static function cancelarBoleto($contrato_id){
        try
        {
            TTransaction::open('sample'); 

           

            TTransaction::close();

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
