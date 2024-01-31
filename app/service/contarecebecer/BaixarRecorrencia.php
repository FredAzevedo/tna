<?php

// 0 23 * * * php /var/www/html/macroerp.com.br/multimais/sistema/cmd.php "class=BaixarRecorrencia&method=baixar"
// php /Library/WebServer/Documents/macro-recorrencia/cmd.php "class=BaixarRecorrencia&method=baixar"

class BaixarRecorrencia
{
    public static function baixar() 
    {
        
        try 
        {
            TTransaction::open('sample');
            $today = date('Y-m-d');
            //consulta contas a receber que esta com data de previsao 
            $conta_receber_previsionada = ContaReceber::where('previsao','<=',$today)->where('baixa','=','N')->load();
          
            if($conta_receber_previsionada){

                foreach($conta_receber_previsionada as $dado){

                    $cr = new ContaReceber($dado->id);
                    $cr->data_baixa = $cr->previsao;
                    $cr->baixa = 'S';
                    $cr->store();

                    $movBancaria = new MovimentacaoBancaria();
                    $movBancaria->valor_movimentacao = $cr->valor;
                    $movBancaria->data_lancamento = $cr->data_conta;
                    $movBancaria->data_vencimento = $cr->previsao;
                    $movBancaria->data_baixa = $cr->previsao;
                    $movBancaria->status = 'Crédito';
                    $movBancaria->historico = $cr->descricao;
                    $movBancaria->baixa = 'S';
                    $movBancaria->tipo = 1;
                    $movBancaria->documento = $cr->documento;
                    $movBancaria->unit_id = $cr->unit_id;
                    $movBancaria->user_id = 1;
                    $movBancaria->cliente_id = $cr->cliente_id;
                    $movBancaria->pc_receita_id = $cr->pc_receita_id;
                    $movBancaria->pc_receita_nome = $cr->pc_receita_nome;
                    $movBancaria->conta_receber_id = $cr->id;
                    $movBancaria->conta_bancaria_id = $cr->conta_bancaria_id;
                    $movBancaria->store();
                }
            }

            TTransaction::close();

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        
    }
}