<?php
// Documentação da API Cloud-DFe
//https://doc.cloud-dfe.com.br/v1/erros.html

class NfseService
{
    public static function onGerar( $param )
    {

        $key        = $param->pedido_id;
        $unit_id    = $param->unit_id;

        TTransaction::open('sample');

        $dados = Pedido::where('id','=',$key)->first();
        $pedidoitens = PedidoItens::where("pedido_id","=",$key)->first();
        $produto = Produto::where("id","=",$pedidoitens->produto_id)->first();
        
        
        if($dados){

            $unidade = new SystemUnit($unit_id);

            $nfseParametro = new NfseParametro(1);
            $lote =  $nfseParametro->ultimoNumeroLote + 1;

            $cliente = new Cliente($dados->cliente_faturamento_id);

            if($dados->geraNFe == 'S'){
            
                $nfse = new NFSe();
                $nfse->TcpfCnpj = $cliente->cpf_cnpj;
                $nfse->TrazaoSocial = $cliente->razao_social;
                $nfse->regime_tributacao = $unidade->crt;

                $nfse->Tlogradouro = $cliente->logradouro;
                $nfse->Tnumero = $cliente->numero;
                $nfse->Tcidade = $cliente->cidade;
                $nfse->Tbairro = $cliente->bairro;
                $nfse->Tuf = $cliente->uf;
                $nfse->Tcomplemento = $cliente->complemento;
                $nfse->TcodigoCidade = $cliente->codMuni;
                $nfse->Tcep = $cliente->cep;

                $Email = EmailCliente::where('cliente_id', '=', $dados->cliente_id)->first();
                
                if($Email == null)
                {
                    // enviar email informando isso?
                }

                $numero = $nfseParametro->ultimoNumeroNfse + 1;
                $nfseParametro->ultimoNumeroNfse = $numero;
                $nfseParametro->store();

                $nfse->unit_id = $dados->unit_id;
                $nfse->enviarEmail = $nfseParametro->enviarEmail;
                $nfse->dataEmissao = date('Y-m-d H:i:s');
                $nfse->competencia = $dados->data_competencia;
                $nfse->Temail = $Email->email;

                $pcServico = new PcReceita($dados->pc_receita_id);

                $nfse->Scodigo = $pcServico->Scodigo;
                $nfse->Sdiscriminacao = $pcServico->Sdiscriminacao;
                $nfse->Scnae = $pcServico->Scnae;
                
                $nfse->ISSaliquota = $nfseParametro->IssAliquota;
                $nfse->ISStipoTributacao = $nfseParametro->tipoTributacao; //6 - Tributável Dentro do Município
                //$nfse->ISSretido = $nfseParametro->IssRetido;

                $nfse->total_servico = $dados->total_pedido;
                $nfse->base_calculo = $dados->total_pedido;

                $nfse->RetCofins = $nfseParametro->RetCofins;
                $nfse->RetCsll = $nfseParametro->RetCsll;
                $nfse->RetInss = $nfseParametro->RetInss;
                $nfse->RetIrrf = $nfseParametro->RetIrrf;
                $nfse->RetPis = $nfseParametro->RetPis;
                $nfse->RetOutros = $nfseParametro->RetOutros;

                //$nfse->ISSvalor = $nfseParametro->IssValor;
                //colocar caso tiver IssValorRetido no model
                $nfse->status = 'NFSe Gerada pronta para Transmitir';
                $nfse->numeroNfse = $numero;
                $nfse->lote = $lote;
                $nfse->pedido_id = $dados->id;
                $nfse->cliente_id = $dados->cliente_id;
                $nfse->tipo = "G";
                $nfse->observacao = "PEDIDO Nº " . $dados->id . " - Referente ao Produto: ".$produto->nome_produto;
                $nfse->store();

                $nfseItem = new NfseItens();
                $nfseItem->nfse_id = $nfse->id;
                $nfseItem->descricao = "Referente ao Produto: ".$produto->nome_produto;
                $nfseItem->valor = $dados->total_pedido;
                $nfseItem->quantidade = 1;
                $nfseItem->total_item = $dados->total_pedido;
                $nfseItem->store();

                if($nfseItem->id){
                    sleep(10);
                    NfseService::onTransmitir($nfseItem->id);
                }
            
            }

        }

        TTransaction::close();
    }

    public static function onTransmitir( $nfse_id )
    {

        TTransaction::open('sample');

        $integracao =  ApiIntegracao::where('gateway','=','4')->where('unit_id','=',1)->first();
        $transmitir = new CloudDfe($integracao);
        $enviarNfse = $transmitir->enviarNfse($nfse_id);

        $salvarRetorno = new Nfse($nfse_id);

        if($enviarNfse->codigo == '100'){
            //Documento fiscal autorizado com sucesso.
            $salvarRetorno->id_retorno = $enviarNfse->chave;
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
            
            sleep(10);
            NfseService::onConsultar($nfse_id);
        }

        if($enviarNfse->codigo == '101'){
            //Documento fiscal cancelado com sucesso.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();

            sleep(10);
            NfseService::onConsultar($nfse_id);

        }

        if($enviarNfse->codigo == '103'){
            //Documento fiscal encerrado com sucesso.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();

            sleep(10);
            NfseService::onConsultar($nfse_id);
        }

        if($enviarNfse->codigo == '135'){
            //Evento registrado com sucesso.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();

            sleep(10);
            NfseService::onConsultar($nfse_id);
        }

        if($enviarNfse->codigo == '205'){

            //Documento fiscal denagado.
            //Verifique junto a receita se existe alguma pendencia no emitente ou destinatario.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();

            sleep(10);
            NfseService::onConsultar($nfse_id);
        }

        if($enviarNfse->codigo == '110'){
            //Documento fiscal denagado.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();

            sleep(10);
            NfseService::onConsultar($nfse_id);
        }

        if($enviarNfse->codigo == '0000'){
            //Estamos em manutenção. Por favor aguarde, voltamos a operar em breve.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5000'){
            //Não foi passado JSON válido. Provável erro de formatação.
            //Verifique o JSON pois o mesmo possui erros de estrutura.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5001'){
            //JSON com erros nos dados: Mensagem invalidada. Violações:[numero_inicial] Deve ter no minimo 1
            //Verifique os dados que estão sendo passados pois não estão compativeis com o manual.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5002'){
            //Erros durante a geração do XML
            //Verifique a variavel 'erros' para identificar os campos não preenchidos.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5003'){
            //Certificado com falha na abertura.
            //O certificado pode estar corrompido ou com senha inválida.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5004'){
            //Acesso Indevido! Não tem acesso a essa rota.
            //Voce não tem acesso ao recurso solicitado.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5005'){
            //É obrigatório informar um CNPJ ou CPF
            //Não foi informado o campo solicitado.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5006'){
            //Não encontrado(s) nenhum Certificado na base de dados.
            //O recurso não foi encontrado para ser processado.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5007'){
            //O número da NFSe inicial incorreto(s) deve ser menor que do o número da NFSe Final!
            //O parâmetro informado não atende aos requisitos.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5008'){
            //Esse NFC-e 'CHAVE' já existe e esta autorizada.
            //O documento já esta processo conforme solicitação.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5009'){
            //Esse emitente 'CNPJ' já esta deletado.
            //O recurso já esta processo conforme solicitação.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5010'){
            //Seu plano já atingiu o limite de emissoes.
            //Verifique o numero de emissões do seu plano.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5011'){
            //Certificado com validade expirada em 12/10/2020 22:03:14
            //Não deixe seu certificado expirar para continuar emitindo.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5014'){
            //Acesso Negado! Foi atingido o limite de emissões para o período.
            //Verifique o plano para continuar emitindo.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5014'){
            //Acesso Negado! Foi atingido o limite de emissões para o período.
            //Verifique o plano para continuar emitindo.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5015'){
            //NF-e está cancelado.
            //Não é possivel fazer a ação solicitada com um documento cancelado.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5017'){
            //Filtro 'X' não disponível para NFSe modelo X.
            //O filtro esclhido não esta disponivel para o modelo solicitado.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5018'){
            //Dados incompleto(s) para para a realização de buscas
            //Não foi passado nenhum dado suficiente para a busca, verifique o menual.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5019'){
            //Não existe o serviço de consulta de NFS-e para o provedor AMTEC
            //O servico solicitado não existe para esse modelo.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5020'){
            //O Municipio de 'X' ainda não é atendido pelo nosso serviço. Entre em contato com o suporte.
            //O municipio informado não é atentido pela API.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5021'){
            //Falha no envio de email
            //Ocorreu um erro ao fazer o envio do email.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5022'){
            //Somente o resumo está disponível
            //Entre em contato com o suporte.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5023'){
            //X - Lote em processamento, aguarde e tente novamente mais tarde.
            //Tente fazer a consulta pela chave de acesso novamente mais tarde.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5024'){
            //MDF-e está encerrado.
            //Não é possivel fazer a ação solicitada com um documento encerrado.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5025'){
            //NF-e está denegado.
            //Não é possivel fazer a ação solicitada com um documento denegado.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5026'){
            //Erro ao emitir a carta de correção.
            //Verifique legistação da NF-e para obter mais detalhes.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5027'){
            //NFS-e em CONFLITO! Não é possivel determinar se a NFS-e foi gerada. Consulte a pagina da prefeitura.

            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5028'){
            //Erro interno do provedor.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5029'){
            //Certificado não pertence ao CNPJ/CPF do emitente.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5030'){
            //NF-e está pendente de autorização.
            //Faça a consulta pela chave para sincronizar o documento.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5031'){
            //Falha ao gerar o PDF.
            //Entre em contato com o suporte.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5032'){
            //Erros durante a validação com o XSD
            //Mostram os erros de validação do xml na receita.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5033'){
            //Documento gerado com erro! Recusado pelo autorizador.
            //Corrija o erro e envie novamente.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5034'){
            //Autorizador não respondeu em 5 tentativas, aguardando mais 1 hora para tentar novamente.
            //O processo é automático.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        if($enviarNfse->codigo == '5500'){
            //Acesso Incorreto! Método não existe nessa rota
            //Método solicitado não existe ou verbo http inválido.
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();
        }

        TTransaction::close();
    }

    public static function onConsultar( $nfse_id )
    {   
        TTransaction::open('sample');

        try {
            
            $objetoNfse = new Nfse($nfse_id);

            $integracao =  ApiIntegracao::where('gateway','=','4')->where('unit_id','=',1)->first();
            $transmitir = new CloudDfe($integracao);
            $consultarNfse = $transmitir->consultaNfse($objetoNfse->id_retorno);
        
            if($consultarNfse->codigo == '100' || $consultarNfse->codigo == '101' || $consultarNfse->codigo == '1' || $consultarNfse->codigo == '9999'){

                $objetoNfse->pdf = $consultarNfse->pdf;
                $objetoNfse->xml = $consultarNfse->xml;
                $objetoNfse->id_retorno = $consultarNfse->chave;
                $objetoNfse->numeroNfse = $consultarNfse->numero;
                $objetoNfse->status = $consultarNfse->mensagem;
                $objetoNfse->statusCode = $consultarNfse->codigo;
                $objetoNfse->store();

            }elseif($consultarNfse->codigo == '400' || $consultarNfse->codigo == '401' || $consultarNfse->codigo == '404'){

                $objetoNfse->status = $consultarNfse->mensagem;
                $objetoNfse->statusCode = $consultarNfse->codigo;
                $objetoNfse->store();

            }else{

                $objetoNfse->status = $consultarNfse->mensagem;
                $objetoNfse->store();

            }
            
        } catch (\Exception $e) {
          new TMessage('error', $e);
        }


        TTransaction::close();
    }
}
