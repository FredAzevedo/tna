<?php

use Carbon\Carbon;
class GerarBoleto
{
    public static function processar( $cliente_id, $contrato_id, $valor,
    $conta_bancaria_id, $tipo_pgto_id, $tipo_forma_pgto_id, $vencimento_primeira_parcela,
    $descricao, $documento, $unit_id = 1, $user_id = 1, $previsao = null, $gerar_boleto = 'N', 
    $replica = 'N', $baixa = 'N', $boleto_formato = null, $competencia = null, $pc_receita_id,$pc_receita_nome, $desconto_mes = null)
    {
        try {
            //TTransaction::open('sample');
            
            $hoje = new DateTime();
            $tipo_forma_pgto = new TipoFormaPgto($tipo_forma_pgto_id);
            $formaPagamento = new FormaPagamento($valor, $tipo_forma_pgto->regra, $vencimento_primeira_parcela);

            $contador_parcelas = 1;
            for($i = 0; $i < $formaPagamento->numero_parcelas; ++$i) {

                $vencimentoCadaParcela = $formaPagamento->vencimentobd[$i];

                $conta_receber = new ContaReceber();
                $conta_receber->data_conta = $competencia ? $competencia : $hoje->format('Y-m-d'); 
                $conta_receber->descricao = $descricao; //'REFERENTE AO CONTRATO Nº '.$contrato->id; 
                $conta_receber->documento = $documento; //$numero_aprovacao; //$numero; //COLOCAR O NUMERO ÚNICO DO CARNE
                $conta_receber->previsao = $previsao;
                $conta_receber->data_vencimento = $vencimentoCadaParcela; 
                $conta_receber->valor = $formaPagamento->valor_parcela;
                $conta_receber->valor_real = $formaPagamento->valor_parcela;
                $conta_receber->valor_pago = $formaPagamento->valor_parcela;
                $conta_receber->baixa = $baixa; 
                $conta_receber->parcelas = $contador_parcelas++;
                $conta_receber->nparcelas = $formaPagamento->numero_parcelas; 
                $conta_receber->replica = $replica; 
                $conta_receber->unit_id = $unit_id; 
                $conta_receber->cliente_id = $cliente_id;
                $conta_receber->tipo_pgto_id = $tipo_pgto_id;
                $conta_receber->tipo_forma_pgto_id = $tipo_forma_pgto_id;
                $conta_receber->user_id = $user_id;
                $conta_receber->pc_receita_id = $pc_receita_id;
                $conta_receber->pc_receita_nome = $pc_receita_nome;
                $conta_receber->conta_bancaria_id = $conta_bancaria_id;
                $conta_receber->cliente_contrato_id = $contrato_id;
                $conta_receber->gerar_boleto = $gerar_boleto;

                $conta_receber->pedido_numero = rand(0, 99999) + time();

                $conta_receber->store();

                $dados_api_integracao = new ApiIntegracao(1);
                $credencial = $dados_api_integracao->credencial;
                $chave      = $dados_api_integracao->chave;
                $ambiente   = $dados_api_integracao->url;
                
                if($boleto_formato == null){
                    if($formaPagamento->numero_parcelas > 1)
                        $boleto_formato = 'CARNE';
                    else
                        $boleto_formato = 'BOLETO';
                }
                    
                BoletoService::emitirBoleto(
                    $credencial, 
                    $chave, 
                    $ambiente, 
                    $boleto_formato,
                    $contrato_id, 
                    $vencimentoCadaParcela, 
                    $formaPagamento->valor_parcela, 
                    $descricao,
                    $conta_receber->pedido_numero,
                    1, 
                    2, 
                    $desconto_mes ?? '0', 
                    'Boletos', 
                    '0', 
                    '0', 
                    null,
                    null, 
                    null, 
                    null, 
                    null, 
                    null, 
                    '0',
                    '', 
                    $conta_receber->id, 
                    $cliente_id,
                    '',
                    $conta_receber->unit_id,
                    $conta_receber->user_id);
            
            }

            //TTransaction::close();
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        
    }
}