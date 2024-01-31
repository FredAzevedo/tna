<?php

class BoletoPJBank
{
    public static function gerarBoletoPJBank($cliente_id,$valor,$data_vencimento,$descricao,$pedido)
    {

        try 
        {
            
            TTransaction::open('sample');

            // if($conta_receber->boleto_emitido == "S"){
            //     throw new Exception('ATENÇÃO: Boleto já emitido para essa conta!');
            // }

            //gerar contas receber
            $conta_receber = new ContaReceber();
            $conta_receber->pedido_numero       = $pedido;
            $conta_receber->data_conta          = date('Y-m-d');
            $conta_receber->descricao           = $descricao;
            $conta_receber->documento           = '';
            $dataMais3Dias = date('Y-m-d', strtotime('+4 days', strtotime($data_vencimento))); // Adiciona 3 dias à data atual
            $conta_receber->data_vencimento     = $dataMais3Dias;
            $conta_receber->previsao            = '';
            $conta_receber->multa               = '0';
            $conta_receber->juros               = '0';
            $conta_receber->taxas               = '0';
            $conta_receber->valor               = $valor;
            $conta_receber->desconto            = '';
            $conta_receber->portador            = '';
            $conta_receber->observacao          = '';
            $conta_receber->baixa               = 'N';
            $conta_receber->data_baixa          = '';
            $conta_receber->valor_pago          = 0.00;
            $conta_receber->valor_parcial       = $valor;
            $conta_receber->valor_real          = $valor;
            $conta_receber->replica             = 'N';
            $conta_receber->parcelas            = '1';
            $conta_receber->nparcelas           = '1';
            $conta_receber->intervalo           = '';
            $conta_receber->responsavel         = '';
            $conta_receber->boleto_status       = '';
            $conta_receber->boleto_emitido      = 'S';
            $conta_receber->unit_id             = 1;
            $conta_receber->user_id             = 1;
            $conta_receber->boleto_id           = '';
            $conta_receber->cliente_id          = $cliente_id;
            $conta_receber->pc_receita_id       = '1';
            $conta_receber->pc_receita_nome     = '1.01.01.01 - Certificados e Tokens';
            $conta_receber->conta_bancaria_id   = '1';
            $conta_receber->boleto_account_id   = '';
            $conta_receber->tipo_forma_pgto_id  = '1';  // 1 = 1 Parcela
            $conta_receber->tipo_pgto_id        = '4'; // 4 = Boleto
            $conta_receber->split               = 'N';
            $conta_receber->nfse                = 'S';
            $conta_receber->gera_nfse           = 'N';
            $conta_receber->gerar_boleto        = 'S';
            $conta_receber->cliente_contrato_id = NULL;
            $conta_receber->relatorio_customizado_id = '1';
            $conta_receber->recibo              = '';
            $conta_receber->juridico            = 'N';
            $conta_receber->store();
            
            
            //pegar credencial PJBank
            $system_unit = new SystemUnit(1);
            //$dados_api_integracao = $system_unit->getApiIntegracao(GatewayTipoEnum::BOLETO);
            $dados_api_integracao = $system_unit->getApiIntegracao(1);

            $credencial = $dados_api_integracao->credencial;
            $chave      = $dados_api_integracao->chave;
            $ambiente   = $dados_api_integracao->url;
            //$split      = $dados_api_integracao->split;

            TTransaction::close();

            $formato = "BOLETO";
            $vencimento = $dataMais3Dias;
            $valor_boleto = $valor; 
            $texto = $descricao;
            $pedido_numero = $pedido;
            $cliente_boleto_id = $cliente_id;

            $conta_receber_id = $conta_receber->id;

            $contrato_id = null;
            $juros = ""; 
            $multa = ""; 
            $desconto = 0;
            $grupo = null;
            $juros_fixo = 0;
            $multa_fixo = 0;
            $diasdesconto1 = null;
            $desconto1 = null;
            $diasdesconto2 = null;
            $desconto2 = null;
            $diasdesconto3 = null;
            $desconto3 = null;
            $nunca_atualizar_boleto = 0;
            $instrucao_adicional = '';
            $split = '';

            BoletoService::emitirBoleto($credencial, $chave, $ambiente,$formato,$contrato_id, $vencimento, $valor_boleto, $texto,$pedido_numero,$juros, $multa, $desconto, $grupo , $juros_fixo, $multa_fixo,$diasdesconto1, $desconto1, $diasdesconto2, $desconto2, $diasdesconto3, $desconto3,$nunca_atualizar_boleto,$instrucao_adicional, $conta_receber_id, $cliente_boleto_id,$split);
        
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

}


