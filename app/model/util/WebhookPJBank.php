<?php
/**
 * Webhook
 * @author  Fred Azv.
 */

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

class WebhookPJBank extends TPage
{

    public function onHook($param = null)
    {
        
        $return = json_decode($param);
        if (is_array($return)) {
            foreach($return as $item) {
                self::processarItem($item);
            }
        } else {
            self::processarItem($return);
        }
        
    }

    public static function processarItem($return)
    {
        $pagador = $return->pagador;
        $operacao = $return->operacao;//"PUT",
        $tipo = $return->tipo;//"recebimento_boleto",
        $valor = $return->valor; //"100",
        $nosso_numero = $return->nosso_numero;//"24483712",
        $id_unico = $return->id_unico;//"24483712",
        $pedido_numero = $return->pedido_numero;//"10",
        $banco_numero = $return->banco_numero;//"033",
        $token_facilitador = $return->token_facilitador;//"a33235e33d0cec740e01cf6d8ab8325089061692",
        $dateV = explode("/", $return->data_vencimento);//"07/24/2018",
        $data_vencimento = $dateV[2] . "" . $dateV[1] . "" . $dateV[0];
        $credencial = $return->credencial;//"f9c697887f39e31f3f1411fb04516aea54eb0637",
        $chave = $return->chave;//"bd3f0c8adef026744ac54fa19f31aadfb96c0861",
        $registro_sistema_bancario = $return->registro_sistema_bancario;//"confirmado"
        $valor_pago = isset($return->valor_pago) ? $return->valor_pago : null;


        //Confirmação do registro de um boleto:
        if ($registro_sistema_bancario == "confirmado" && $valor_pago == null) {
            
            TTransaction::open('sample');
            $boleto = BoletoApi::where('pedido_numero', '=', $pedido_numero)->first();
            $boleto->registro_sistema_bancario = $registro_sistema_bancario;
            $boleto->nosso_numero = $nosso_numero;
            $boleto->id_unico = $id_unico;
            $boleto->registro_rejeicao_motivo = null;
            $boleto->store();
            TTransaction::close();
            
            print_r(json_encode(['status' => '200']));
        }

        // Webhook de confirmação de pagamento. Você notará que alguns campos novos serão enviados, como: valor_pago, valor_liquido, valor_tarifa, data_pagamento, data_credito:

        if ($valor_pago != null) {
            $valor_liquido = $return->valor_liquido;//"97.5",
            $valor_tarifa = $return->valor_tarifa;//"2.5",

            $date1 = explode("/", $return->data_pagamento);
            $data_pagamento = $date1[2] . "" . $date1[0] . "" . $date1[1];
            //$data_credito = $return->data_credito;//"07/26/2018",

            $date2 = explode("/", $return->data_credito);
            $data_credito = $date2[2] . "" . $date2[0] . "" . $date2[1];

            TTransaction::open('sample');
            $boleto = BoletoApi::where('pedido_numero', '=', $pedido_numero)->first();
            TTransaction::close();
            //var_dump($pedido_numero);
            if ($boleto->pago != "S") {
                
                $hoje = new DateTime();
                //dando baixa em contas a receber
                TTransaction::open('sample');
                $contareceber =  ContaReceber::where('pedido_numero', '=', $pedido_numero)->first();
                $contareceber->data_conta = $hoje->format('Y-m-d');
                $contareceber->previsao = $data_credito;
                $contareceber->data_pagamento = $data_pagamento;
                $contareceber->baixa = 'S';
                $contareceber->data_baixa = $data_credito;
                $contareceber->valor = $valor_pago;
                $contareceber->valor_real = $valor_pago;
                $contareceber->valor_pago = $valor_pago;
                $contareceber->parcelas = 1;
                $contareceber->nparcelas = 1;
                $contareceber->replica = 'N';
                $contareceber->store();
                TTransaction::close();

                TTransaction::open('sample');
                $b = BoletoApi::where('pedido_numero', '=', $pedido_numero)->first();
                $b->registro_sistema_bancario = $registro_sistema_bancario;
                $b->nosso_numero = $nosso_numero;
                $b->id_unico = $id_unico;
                $b->valor_pago = $valor_pago;
                $b->valor_tarifa = $valor_tarifa;
                $b->valor_liquido = $valor_liquido;
                $b->data_pagamento = $data_pagamento;
                $b->data_credito = $data_credito;
                $b->registro_rejeicao_motivo = null;
                $b->pago = 'S';
                $b->contas_receber_id = $contareceber->id;
                $b->store();
                TTransaction::close();

                TTransaction::open('sample');
                //Gravando em Movimentacao Bancaria receita
                $movBancaria = new MovimentacaoBancaria();
                $movBancaria->valor_movimentacao = $valor_pago;
                $movBancaria->data_lancamento = $contareceber->data_conta;
                $movBancaria->data_vencimento = $contareceber->data_vencimento;
                $movBancaria->data_baixa = $data_credito;
                $movBancaria->status = 'Crédito';
                $movBancaria->historico = $contareceber->descricao;
                $movBancaria->baixa = 'S';
                $movBancaria->tipo = 1;
                $movBancaria->documento = $contareceber->documento;
                $movBancaria->unit_id = $contareceber->unit_id;
                $movBancaria->user_id = $contareceber->user_id;
                $movBancaria->cliente_id = $contareceber->cliente_id;
                $movBancaria->pc_receita_id = $contareceber->pc_receita_id;
                $movBancaria->pc_receita_nome = $contareceber->pc_receita_nome;
                $movBancaria->conta_receber_id = $contareceber->id;
                $movBancaria->conta_bancaria_id = $contareceber->conta_bancaria_id;
                $movBancaria->store();
                TTransaction::close();

                TTransaction::open('sample');
                $taxaCP = new ContaPagar();
                $taxaCP->data_lancamento = date('Y-m-d');
                $taxaCP->data_vencimento = date('Y-m-d');
                $taxaCP->data_conta = date('Y-m-d');
                $taxaCP->data_baixa = $data_credito;
                $taxaCP->documento = $contareceber->id;
                $taxaCP->descricao = "Taxa PJbank Ref. CR. Nº: " . $contareceber->id;
                $taxaCP->unit_id = $contareceber->unit_id;
                $taxaCP->conta_bancaria_id = $contareceber->conta_bancaria_id; //verificar conta padão
                $taxaCP->tipo_pgto_id = $contareceber->tipo_pgto_id;
                $taxaCP->tipo_forma_pgto_id = $contareceber->tipo_forma_pgto_id;
                $taxaCP->fornecedor_id = 1; //verificar o id do fornecedor pjbank
                $taxaCP->replica = 'N';
                $taxaCP->parcelas = 1;
                $taxaCP->nparcelas = 1;
                $taxaCP->responsavel = 'PJBank';
                $taxaCP->user_id = $contareceber->user_id;
                $taxaCP->valor = $valor_tarifa;
                $taxaCP->valor_pago = $valor_tarifa;
                $taxaCP->valor_real = $valor_tarifa;
                $taxaCP->baixa = 'S';
                $taxaCP->pc_despesa_id = 42; // criar param = nullentro
                $taxaCP->pc_despesa_nome = '2.03.04.04 - Tarifas';
                $taxaCP->store();
                TTransaction::close();

                TTransaction::open('sample');
                //Gravando em Movimentacao Bancaria despesa a taxa do boleto
                $movBancariaTaxa = new MovimentacaoBancaria();
                $movBancariaTaxa->valor_movimentacao = $taxaCP->valor;
                $movBancariaTaxa->data_lancamento = date('Y-m-d');
                $movBancariaTaxa->data_vencimento = date('Y-m-d');
                $movBancariaTaxa->data_baixa = $data_credito;
                $movBancariaTaxa->status = 'Débito';
                $movBancariaTaxa->historico = $taxaCP->descricao;
                $movBancariaTaxa->baixa = 'S';
                $movBancariaTaxa->tipo = 0;
                $movBancariaTaxa->documento = $taxaCP->documento;
                $movBancariaTaxa->unit_id = $taxaCP->unit_id;
                $movBancariaTaxa->user_id = $contareceber->user_id; // criar param = nullentro
                $movBancariaTaxa->fornecedor_id = 1; // criar param = nullentro
                $movBancariaTaxa->pc_despesa_id = 42; // criar param = nullentro
                $movBancariaTaxa->pc_despesa_nome = '2.03.04.04 - Tarifas';
                $movBancariaTaxa->conta_pagar_id = $taxaCP->id;
                $movBancariaTaxa->conta_bancaria_id = $taxaCP->conta_bancaria_id;
                $movBancariaTaxa->store();
                TTransaction::close();

            }
            print_r(json_encode(['status' => '200']));
        }

        //Registro Rejeitado com o motivo de rejeição:
        if ($registro_sistema_bancario == "rejeitado") {
            $registro_rejeicao_motivo = $return->registro_rejeicao_motivo;//"Data de Vencimento Inválida"
           
            TTransaction::open('sample');
            $boleto = BoletoApi::where('pedido_numero', '=', $pedido_numero)->first();
            $boleto->registro_sistema_bancario = $registro_sistema_bancario;
            $boleto->registro_rejeicao_motivo = $registro_rejeicao_motivo;
            $boleto->store();
            TTransaction::close();

            print_r(json_encode(['status' => '200']));
        }

        //Webhook disparado após a edição de um boleto que já estava registrado:
        if ($registro_sistema_bancario == "pendente") {
            
            TTransaction::open('sample');
            $boleto = BoletoApi::where('pedido_numero', '=', $pedido_numero)->first();
            $boleto->registro_sistema_bancario = $registro_sistema_bancario;
            $boleto->registro_rejeicao_motivo = null;
            $boleto->store();
            TTransaction::close();

            print_r(json_encode(['status' => '200']));
        }

        //Registro Invalidado
        if ($registro_sistema_bancario == "baixado") {

            TTransaction::open('sample');
            $boleto = BoletoApi::where('pedido_numero', '=', $pedido_numero)->first();
            $boleto->registro_sistema_bancario = $registro_sistema_bancario;
            $boleto->nosso_numero = $nosso_numero;
            $boleto->id_unico = $id_unico;
            $boleto->registro_rejeicao_motivo = "Boleto cancelado e baixado!";
            $boleto->store();
            TTransaction::close();

            print_r(json_encode(['status' => '200']));
        }

        
    }

}