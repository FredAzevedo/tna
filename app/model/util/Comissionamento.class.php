<?php

use Adianti\Database\TTransaction;

class Comissionamento
{

	public static function gerarComissao($cliente_id,$conta_receber_id)
	{

		TTransaction::open('sample');

		$cr = new ContaReceber($conta_receber_id);
		$cliente = new Cliente($cliente_id);
        $comissaoUser = new ComissaoTabela($cliente->comissao_vendedor);

        //COMISSÃO DO VENDEDOR PRINCIPAL DO CLIENTE
        if(!empty($cliente->comissao_vendedor) AND !empty($cliente->vendedor_user_id))
        {
            if($comissaoUser->forma_comissao == "P"){

                $valorComissao = $cr->valor * $comissaoUser->valor_comissao/100;

                $comissao = new ComissaoUser();
                $comissao->data_faturamento = date('Y-m-d');
                $comissao->valor_faturamento = $cr->valor;
                $comissao->taxa_comissao = $comissaoUser->valor_comissao;
                $comissao->valor_comissao = $valorComissao;
                $comissao->descricao = $cr->descricao;
                $comissao->pago = 'N';
                $comissao->tipo = 'P';
                $comissao->unit_id = $cr->unit_id;
                $comissao->user_id = $cliente->vendedor_user_id;
                $comissao->cliente_id = $cliente->id;
                $comissao->store();

            }else{

                $valorComissao = $comissaoUser->valor_comissao;

                $comissao = new ComissaoUser();
                $comissao->data_faturamento = date('Y-m-d');
                $comissao->valor_faturamento = $cr->valor;
                $comissao->taxa_comissao = $comissaoUser->valor_comissao;
                $comissao->valor_comissao = $valorComissao;
                $comissao->descricao = $cr->descricao;
                $comissao->pago = 'N';
                $comissao->tipo = 'D';
                $comissao->unit_id = $cr->unit_id;
                $comissao->user_id = $cliente->vendedor_user_id;
                $comissao->cliente_id = $cliente->id;
                $comissao->store();

            }
        }

        //COMISSÃO DO VENDEDOR EXTERNO DO CLIENTE
        $comissaoUserExterno = new ComissaoTabela($cliente->comissao_vendedor_externo);

        if(!empty($cliente->comissao_vendedor_externo) AND !empty($cliente->vendedor_externo_user_id))
        {
            if($comissaoUserExterno->forma_comissao == "P"){

                $valorComissao = $cr->valor * $comissaoUserExterno->valor_comissao/100;

                $comissao = new ComissaoUser();
                $comissao->data_faturamento = date('Y-m-d');
                $comissao->valor_faturamento = $cr->valor;
                $comissao->taxa_comissao = $comissaoUserExterno->valor_comissao;
                $comissao->valor_comissao = $valorComissao;
                $comissao->descricao = $cr->descricao;
                $comissao->pago = 'N';
                $comissao->tipo = 'P';
                $comissao->unit_id = $cr->unit_id;
                $comissao->user_id = $cliente->vendedor_externo_user_id;
                $comissao->store();

            }else{

                $valorComissao = $comissaoUserExterno->valor_comissao;

                $comissao = new ComissaoUser();
                $comissao->data_faturamento = date('Y-m-d');
                $comissao->valor_faturamento = $cr->valor;
                $comissao->taxa_comissao = $comissaoUserExterno->valor_comissao;
                $comissao->valor_comissao = $valorComissao;
                $comissao->descricao = $cr->descricao;
                $comissao->pago = 'N';
                $comissao->tipo = 'D';
                $comissao->unit_id = $cr->unit_id;
                $comissao->user_id = $cliente->vendedor_externo_user_id;
                $comissao->store();

            }
        }

        //COMISSÃO DE QUEM INDICOU O CLIENTE
        $comissaoIndicador = new ComissaoTabela($cliente->comissao_parceiro);

        if(!empty($cliente->comissao_parceiro) AND !empty($cliente->fornecedor_id))
        {
            if($comissaoIndicador->forma_comissao == "P"){

                $valorComissao = $cr->valor * $comissaoIndicador->valor_comissao/100;

                $comissao = new ComissaoFornecedor();
                $comissao->data_faturamento = date('Y-m-d');
                $comissao->valor_faturamento = $cr->valor;
                $comissao->taxa_comissao = $comissaoIndicador->valor_comissao;
                $comissao->valor_comissao = $valorComissao;
                $comissao->descricao = $cr->descricao;
                $comissao->pago = 'N';
                $comissao->tipo = 'P';
                $comissao->unit_id = $cr->unit_id;
                $comissao->fornecedor_id = $cliente->fornecedor_id;
                $comissao->cliente_id = $cliente->id;
                $comissao->store();

            }else{

                $valorComissao = $comissaoIndicador->valor_comissao;

                $comissao = new ComissaoFornecedor();
                $comissao->data_faturamento = date('Y-m-d');
                $comissao->valor_faturamento = $cr->valor;
                $comissao->taxa_comissao = $comissaoIndicador->valor_comissao;
                $comissao->valor_comissao = $valorComissao;
                $comissao->descricao = $cr->descricao;
                $comissao->pago = 'N';
                $comissao->tipo = 'D';
                $comissao->unit_id = $cr->unit_id;
                $comissao->fornecedor_id = $cliente->fornecedor_id;
                $comissao->cliente_id = $cliente->id;
                $comissao->store();

            }
        }
        TTransaction::close();
	} 
}