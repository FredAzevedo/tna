<?php

use Carbon\Carbon;
use ClienteContrato;

class ContaReceberService
{
    public static function lancar($cliente_id, $contrato_id, Plano $plano, $valor,
                                  $conta_bancaria_id, $tipo_pgto_id, $tipo_forma_pgto_id, $vencimento_primeira_parcela,
                                  $descricao, $documento,
                                  $unit_id, $user_id = 1,
                                  $previsao = null, $gerar_boleto = 'N', $replica = 'N', $baixa = 'N', $boleto_formato = null, $competencia = null) 
    {

        try {
            TTransaction::open('sample');
            
            $lista_contas_receber_lancados = array();

            $hoje = new DateTime();
            $tipo_forma_pgto = new TipoFormaPgto($tipo_forma_pgto_id);
            $formaPagamento = new FormaPagamento($valor, $tipo_forma_pgto->regra, $vencimento_primeira_parcela);

            $contador_parcelas = 1;
            for($i = 0; $i < $formaPagamento->numero_parcelas; ++$i) {

                $contador_parcelas = $contador_parcelas++;

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
                $conta_receber->parcelas = $contador_parcelas;
                $conta_receber->nparcelas = $formaPagamento->numero_parcelas; 
                $conta_receber->replica = $replica; 
                $conta_receber->unit_id = $unit_id; 
                $conta_receber->cliente_id = $cliente_id;
                $conta_receber->tipo_pgto_id = $tipo_pgto_id;
                $conta_receber->tipo_forma_pgto_id = $tipo_forma_pgto_id;
                $conta_receber->user_id = $user_id;
                $conta_receber->pc_receita_id = $plano->pc_receita_id;
                $conta_receber->pc_receita_nome = $plano->pc_receita_nome;
                $conta_receber->conta_bancaria_id = $conta_bancaria_id;
                $conta_receber->cliente_contrato_id = $contrato_id;
                $conta_receber->gerar_boleto = $gerar_boleto;

                $conta_receber->pedido_numero = rand(0, 99999) + time();

                $conta_receber->store();

                array_push($lista_contas_receber_lancados, $conta_receber);

                if($gerar_boleto != 'N'){

                    $system_unit = new SystemUnit($unit_id);
                    $dados_api_integracao = $system_unit->getApiIntegracao(GatewayTipoEnum::BOLETO);

                    $credencial = $dados_api_integracao->credencial;
                    $chave      = $dados_api_integracao->chave;
                    $ambiente   = $dados_api_integracao->url;
                    
                    if($boleto_formato == null){
                        if($formaPagamento->numero_parcelas > 1)
                            $boleto_formato = 'CARNE';
                        else
                            $boleto_formato = 'BOLETO';
                    }

                        
                    BoletoService::emitirBoleto($credencial, $chave, $ambiente, $boleto_formato,
                                                $contrato_id, $vencimentoCadaParcela, $formaPagamento->valor_parcela, $descricao,
                                                $conta_receber->pedido_numero,
                                                1, 2, '0', 'Boletos', '0', '0', null, null, null, null, null, null, '0','', $conta_receber->id, $cliente_id,'',$conta_receber->unit_id,$conta_receber->user_id);
                }

            }

            TTransaction::close();
            return $lista_contas_receber_lancados;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        
    }
}