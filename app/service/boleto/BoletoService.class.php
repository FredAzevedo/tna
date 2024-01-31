<?php

// ini_set('display_errors',1);
// ini_set('display_startup_erros',1);
// error_reporting(E_ALL);

use Carbon\Carbon;
class BoletoService
{

    /**
     * Emitir um boleto bancário
     * @param $credencial Credencial do PJBank.
     * @param $chave Chave de acesso do PJBank.
     * @param $ambiente Tipo de ambiente URL identificando se é Sandbox ou Produção.
     * @param $contrato_id ID do Cliente que terá boleto emitido.
     * @param $vencimento Vencimento da cobrança no formato MM/DD/AAAA. Boletos gerados vencidos não são registrados. length (10-10). Ex: 12/30/2019
     * @param $valor Valor a ser cobrado em reais. Casas decimais devem ser separadas por ponto, máximo de 2 casas decimais, não enviar caracteres diferentes de número ou ponto. 
     *               Não usar separadores de milhares. Exemplo: 1000.98.
     * @param $pedido_numero Número gerado de forma aleatória e que deve estar vinculado com o pedido_numero do contas a receber
     * @param $texto Texto que ficará no topo dos boletos. Será impresso com fonte fixa. length (0-3800).
     * @param $juros Taxa de juros ao mês em Porcentagem (caso seja enviado junto com o campo juros_fixo = 1, a taxa será em Reais). 
     *               A taxa diária será calculada a partir do valor informado dividido por 30. Casas decimais devem ser separadas por ponto, 
     *               máximo de 2 casas decimais, não enviar caracteres diferentes de número ou ponto. Não usar separadores de milhares. length (1-15).
     */
    public static function emitirBoleto($credencial, 
                                        $chave, 
                                        $ambiente,
                                        $formato,
                                        $contrato_id = null, 
                                        $vencimento, 
                                        $valor, 
                                        $texto = '',
                                        $pedido_numero,
                                        $juros = 1, 
                                        $multa = 2, 
                                        $desconto = 0, 
                                        $grupo = null, 
                                        $juros_fixo = 0, 
                                        $multa_fixo = 0, 
                                        $diasdesconto1 = null, 
                                        $desconto1 = null, 
                                        $diasdesconto2 = null, 
                                        $desconto2 = null, 
                                        $diasdesconto3 = null, 
                                        $desconto3 = null,
                                        $nunca_atualizar_boleto = 0,
                                        $instrucao_adicional = '', 
                                        $conta_receber_id, 
                                        $cliente_id,
                                        $split, 
                                        $unit_id, 
                                        $user_id){
        try
        {
            //TTransaction::open('sample');
            
            $cliente = new Responsavel($cliente_id);
        

            $vencimento = Carbon::parse($vencimento);
            $data_vencimento_enviada_no_pjbank = str_pad($vencimento->month, 2, "0", STR_PAD_LEFT) . "/" . str_pad($vencimento->day, 2, "0", STR_PAD_LEFT) . "/" . $vencimento->year;
            
            $boleto = new BoletoApi;

            $boleto->formato = $formato;

            $boleto->cliente_id = $cliente->id;
            $boleto->unit_id = $unit_id;
            $boleto->user_id = $user_id;

            $boleto->vencimento =  $data_vencimento_enviada_no_pjbank; 
            $boleto->valor = number_format($valor,2,'.','');
            $boleto->juros = $juros;
            $boleto->multa = $multa;
            $boleto->desconto = $desconto;

            $boleto->nome_cliente = $cliente->nome;
            $boleto->cpf_cliente = Utilidades::removerCaracteresEspeciais($cliente->cpf);
            $boleto->endereco_cliente = $cliente->logradouro;
            $boleto->numero_cliente =  $cliente->numero;
            $boleto->complemento_cliente =  $cliente->complemento; 
            $boleto->bairro_cliente =  $cliente->bairro;
            $boleto->cidade_cliente =  $cliente->cidade;
            $boleto->estado_cliente =  $cliente->uf;
            $boleto->cep_cliente =  Utilidades::removerCaracteresEspeciais($cliente->cep);

            $boleto->email_cliente =  $cliente->email;
            $boleto->telefone_cliente =  Utilidades::removerCaracteresEspeciais($cliente->telefone);
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
            $boleto->webhook = WEBHOOK_URL; //informe uma URL de Webhook. Iremos chamá-la com as novas informações sempre que a cobrança for atualizada.
            $boleto->especie_documento = 'DS';

            $boleto->credencial = $credencial;
            $boleto->chave = $chave;
            $boleto->ambiente = $ambiente;
            $boleto->contas_receber_id = $conta_receber_id;
            $boleto->contrato = $contrato_id;

            $boleto->split = $split;

            $json = new PJBankApi;
            $return = $json->emitirBoleto($boleto);
            $retorno_boleto = json_decode($return);     
           
            if($retorno_boleto->status == "200" || $retorno_boleto->status == "201")
            {

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
            else
            {   
                $log = new ResponsePjbank();
                $log->status= $retorno_boleto->status;
                $log->response = $retorno_boleto;
                $log->contrato_id = $contrato;
                $log->descricao = $retorno_boleto->msg;
                $log->user_id = 1;
                $log->unit_id = 1;
                $log->store();

                throw new Exception('Status: '.$retorno_boleto->status.". Mensagem: ".$retorno_boleto->msg);  
            }

            // if($formato == 'BOLETO'){
            //     $parametros['linkBoleto'] = $retorno_boleto->linkBoleto;
            //     TApplication::loadPage('ApiImprimeBoleto', 'onSavePDF', $parametros);
            // }

            //TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public static function imprimirCarne($contrato_id){
        try
        {
            TTransaction::open('sample'); 

            $boleto_carne = BoletoCarne::where('cliente_contrato_id', '=', $contrato_id)->load()[0]; 

            $boleto = BoletoApi::where('cliente_contrato_id','=', $contrato_id)
                                ->where('formato', 'LIKE', 'CARNE')
                                ->load();
            if($boleto)
            {
                foreach($boleto as $bol){
                    $boletos[] = $bol->pedido_numero;
                }
            }

            $dados_api_integracao = new ApiIntegracao(1);

            $print = new stdClass();
            $print->url = $dados_api_integracao->url;
            $print->credencial = $dados_api_integracao->credencial;
            $print->chave = $dados_api_integracao->chave;
            $print->boletos = $boletos;
            $print->contrato_id = $contrato_id;

            $obj = new PJBankApi;
            $json = $obj->imprimirCarne();
            $carne = json_decode($json);

            if($carne->status == '200'){

                $boleto_carne->linkBoleto = $carne->linkBoleto;
                $boleto_carne->store();

                $parametros['id'] = $boleto_carne->id;
                TApplication::loadPage('ApiImprimeCarne', 'onSavePDF', $parametros);

            } else{
                throw new Exception('Status: '. $carne->status . '. Mensagem: '. $carne->msg .' Contate o suporte do sistema');
            }

            TTransaction::close();

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public static function consultarBoletos($credencial,$chave,$ambiente,$data_inicio,$data_fim,$pago)
    {
        try
        {
            $obj = new stdClass();
            $obj->credencial = $credencial;
            $obj->chave = $chave;
            $obj->ambiente = $ambiente;
            $obj->data_inicio = $data_inicio;
            $obj->data_fim = $data_fim;
            $obj->pago = $pago;

            $json = PJBankApi::consultarBoletosRecebimento($obj);

            return $json;
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
