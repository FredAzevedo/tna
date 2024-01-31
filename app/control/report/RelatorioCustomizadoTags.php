<?php

use Http\Discovery\HttpAsyncClientDiscovery;

class RelatorioCustomizadoTags extends TWindow
{
    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Relatório');
        parent::setSize(0.5,0.9);    
        $object = new TElement('object');
        $object->data  = "tmp/RelatorioProjeto.pdf";
        $object->style = "width: 100%; height:calc(100% - 10px)";
        parent::add($object);

    }

    function onViewContrato($param)
    {
        $key = $param['id'];
        $relatorio_customizado_id = $param['relatorio_customizado_id'];

        //START TCPDF
        include_once( 'vendor/autoload.php' );

        try
        {
            TTransaction::open('sample');

            $projeto = Projeto::where('id','=',$key)->first();
            $numero_contrato = str_pad($key, 5, "0", STR_PAD_LEFT);
            $unit  = new SystemUnit(TSession::getValue('userunitid'));
            $endereco = $unit->logradouro." Nº: ".$unit->numero." Bairro: ".$unit->bairro.". ".$unit->complemento." Cidade: ".$unit->cidade." UF: ".$unit->uf." CEP: ".$unit->cep;

            $pdf = new ReportHeaderMCA('P', 'mm', 'A4', true, 'UTF-8', false, true);
            
            $pdf->set_param("LogoWS.png");
            $pdf->addPage('P', 'A4');
            $pdf->SetFont('freesans','',12,'', true);

            $pdf->setFontSubsetting(true);

            $cliente = new Cliente($projeto->cliente_id);
            
            if($projeto){

                $html = RelatorioCustomizado::where('id','=',$relatorio_customizado_id)->first();

                $tag_numero_projeto         = $projeto->cod_projeto;
                $tag_cod_projeto            = $projeto->cod_projeto;
                $tag_nome_projeto           = $projeto->nome_projeto;
                $tag_cnpj_cliente           = $projeto->cliente->cpf_cnpj;

                //verifica se é cnpj ou cpf
                if(strlen($projeto->cliente->cpf_cnpj) == 14){
                    $tag_nome_razao_cpf_cnpj = "Nome: ".$projeto->cliente->razao_social." | CPF: ".$projeto->cliente->cpf_cnpj;
                }else{
                    $tag_nome_razao_cpf_cnpj = "Razão Social: ".$projeto->cliente->razao_social." | CNPJ: ".$projeto->cliente->cpf_cnpj;
                }

                $tag_gestor                 = $cliente->gestor->nome;
                $tag_escopo_servico         = $projeto->escopo_servico;
                $tag_diagnostico            = $projeto->diagnostico;
                $tag_razao_social           = $projeto->cliente->razao_social;
                $tag_nome_fantasia          = $projeto->cliente->nome_fantasia;
                $tag_relatorio_projeto      = $projeto->relatorio_projeto;

                $partes_data_pre_diagnostico = explode("-", $projeto->data_pre_diagnostico);
                $dia_data_pre_diagnostico = $partes_data_pre_diagnostico[2];
                $mes_data_pre_diagnostico = $partes_data_pre_diagnostico[1];
                $ano_data_pre_diagnostico = $partes_data_pre_diagnostico[0];
                $tag_data_pre_diagnostico = $dia_data_pre_diagnostico."/".$mes_data_pre_diagnostico."/".$ano_data_pre_diagnostico;

                $partes_data_etapa = explode("-", $projeto->data_etapa);
                $dia_data_etapa = $partes_data_etapa[2];
                $mes_data_etapa = $partes_data_etapa[1];
                $ano_data_etapa = $partes_data_etapa[0];
                $tag_data_etapa = $dia_data_etapa."/".$mes_data_etapa."/".$ano_data_etapa;

                $partes_data_tecnico_final = explode("-", $projeto->data_tecnico_final);
                $dia_data_tecnico_final = $partes_data_tecnico_final[2];
                $mes_data_tecnico_final = $partes_data_tecnico_final[1];
                $ano_data_tecnico_final = $partes_data_tecnico_final[0];
                $tag_data_tecnico_final = $dia_data_tecnico_final."/".$mes_data_tecnico_final."/".$ano_data_tecnico_final;

                $partes = explode("-", $projeto->data_projeto);
                $dia = $partes[2];
                $mes = $partes[1];
                $ano = $partes[0];
                $tag_data_projeto           = $dia."/".$mes."/".$ano;

                $partes_data_orcamento = explode("-", $projeto->data_orcamento);
                $dia_data_orcamento = $partes_data_orcamento[2];
                $mes_data_orcamento = $partes_data_orcamento[1];
                $ano_data_orcamento = $partes_data_orcamento[0];
                $tag_data_orcamento = $dia_data_orcamento."/".$mes_data_orcamento."/".$ano_data_orcamento;

                $tag_endereco_cliente       = $cliente->getClienteEndereco()[0]->logradouro." - ".$cliente->getClienteEndereco()[0]->numero." ".$cliente->getClienteEndereco()[0]->complemento.". Bairro: ".$cliente->getClienteEndereco()[0]->bairro.". Cidade: ".$cliente->getClienteEndereco()[0]->cidade.". CEP: ".$cliente->getClienteEndereco()[0]->cep;
                $tag_telefone_cliente       = $cliente->getTelefonesClientes()[0]->telefone;
                $tag_email_cliente          = $cliente->getEmailClientes()[0]->email;

                //serviços inclusos na etapa 1
                $servicosinclusos = ProjetoServicoIncluso::where("projeto_id","=",$projeto->id)->load();
                $cont = 0;
                $alphabet = range('a', 'z');
                if($servicosinclusos)
                {   
                    foreach($servicosinclusos as $inclusos){
                        $tag_servicos_incluso_etapa2 .= $alphabet[$cont]." ) ".$inclusos->servico->nome."<br>";
                        $cont++;
                    }
                }else{
                    $tag_servicos_incluso_etapa2 .= "<p>Não existem serviços inclusos no formulário</p>";
                }

                // serviços nào inclusos na etapa 1?
                $servicosNinclusos = ProjetoServicoNincluso::where("projeto_id","=",$projeto->id)->load();
                $cont2 = 0;
                if($servicosNinclusos)
                {
                    foreach($servicosNinclusos as $Ninclusos){
                        $tag_produtos_nao_incluidos .= "* ".$Ninclusos->servico->nome."<br>";
                        $cont2++;
                    }
                }else{
                    $tag_produtos_nao_incluidos .= "<p>Não existem serviços inclusos no formulário</p>";
                }


                //formas de pagamentos
                $projetopreco = ProjetoPreco::where("projeto_id","=",$projeto->id)->load();
                if($projetopreco)
                {
                    foreach($projetopreco as $pc){
                        $forma_pagamento .= $pc->descricao." no valor de: R$ ".Utilidades::formatar_valor($pc->preco_porcent).".<br>";
                    }
                }else{
                    $forma_pagamento = "";
                }
             
                $tag_servicos_incluso_etapa1 = $projeto->servicos_incluso_etapa1;
                $tag_prazo                  = $projeto->prazo;
                $tag_valor_projeto          = Utilidades::formatar_valor($projeto->valor_projeto);
                $tag_valor_projeto_extenso  = Utilidades::extenso($projeto->valor_projeto);
                $tag_forma_pagamento        = $forma_pagamento;
                $tag_descricao_pagamento    = $projeto->descricao_pagamento;
                
                $tag_orcamento_descricao    = $projeto->orcamento_descricao;
                $tag_numero_funcionarios    = $projeto->numero_funcionarios;
                $tag_info_gerais            = $projeto->info_gerais;
                $tag_concorrencia           = $projeto->concorrencia;
                $tag_verba_investimento     = $projeto->verba_investimento;
                $tag_historico_empresa      = $projeto->historico_empresa;
                $tag_problematica_solucao   = $projeto->problematica_solucao;
                $tag_documentos             = $projeto->documentos;
                $tag_projetos_executados_observacao = $projeto->projetos_executados_observacao;
                $tag_area_intervencao_valor = $projeto->area_intervencao_valor;
                $tag_plano_acao = $projeto->plano_acao;
                $tag_plano_acao_final = $projeto->plano_acao_final;
                $tag_consideracao_final_texto = $projeto->consideracao_final_texto;


                //projetos executados
                $select_exec = ProjetoServicoExecutado::where("projeto_id","=",$projeto->id)->load();
                if($select_exec)
                {   
                    foreach($select_exec as $itens){
                        $executados[] .= $itens->servico_id;
                    }
                }
                
                //lista de serviços
                $items_servicos = Servico::where('id',"is not",null)->orderBy('nome')->load();
                $tag_servico_executados = '
                <table class="table table-bordered" style="border: 1px solid black; border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td style="text-align: center;border: 1px solid black; width:440px;"><b>SERVIÇOS</b></td>
                            <td style="text-align: center; border: 1px solid black; width:80px;"><b>INCLUSO?</b></td>
                        </tr>';
                       
                        foreach($items_servicos as $key => $item)
                        {
                            if (in_array($item->id,$executados)) { 
                                $i = "Sim";
                            }else{
                                $i = "Não";
                            }
                           
                            $tag_servico_executados .= 
                            '<tr>
                                <td style="border: 1px solid black;">'.$item->nome.'</td>
                                <td style="text-align: center; border: 1px solid black;">'.$i.'</td>
                            </tr>';
                        }
                $tag_servico_executados .= '</tbody></table>';

                $tag_projetos_executados    = $tag_servico_executados;

                //pegar imagens da area de intervencao
                $get_img = ProjetoFotoIntervencao::where('projeto_id','=',$projeto->id)->load();
                if($get_img)
                {
                    foreach($get_img as $img)
                    {
                        //adiciona o html ao relaório
                        $tag_area_intervencao .= '<div style="text-align: center;"><span style="display: block; margin: auto;"><img src="'.$img->arquivo.'" width="400" height="400"></span></br></div>';
                    }
                }

                //pegar imagens do registro local
                $arr = ProjetoFotoRegistro::where('projeto_id','=',$projeto->id)->load();
                if($arr)
                {   
                    //adiciona o html ao relaório
                    $tag_registro_local = '<table border="0" cellpadding="2" cellspacing="2" style="width:500px">
                    <tbody>';

                    $itens_per_row = 2;
                    $i = 0;
                    foreach ($arr as $it) {

                        $mod = ($i % $itens_per_row);
                        if ($mod === 0) {
                            $tag_registro_local.= '<tr>';
                            $open = true;
                        }
                            $file_name = pathinfo($it->arquivo, PATHINFO_FILENAME);

                            $tag_registro_local.= '
                            <td style="max-width:500px;">
                                <p>"'.$file_name.'"</p>
                                <img style="width:250px;" src="'.$it->arquivo.'">
                            </td>';
                            $qtd++;

                        if ($mod === $itens_per_row - 1) {
                            $tag_registro_local .= '</tr>';
                            $open = false;
                            $qtd = 0;
                        }   
                        $i++;
                    }

                    while ($qtd !== 0 && $qtd < $itens_per_row) {
                        $tag_registro_local .= '<td></td>';
                        if ($qtd === $itens_per_row - 1) {
                            $tag_registro_local .= '</tr>';
                        }
                        $qtd++;
                    }

                    $tag_registro_local .= '</tbody></table>';
                }


                //pegar imagens das considerações finais
                $arrFinal = ProjetoFotoFinal::where('projeto_id','=',$projeto->id)->load();
                if($arrFinal)
                {   
                    //adiciona o html ao relaório
                    $tag_consideracao_final = '<table border="0" cellpadding="2" cellspacing="2" style="width:500px">
                    <tbody>';

                    $itens_por_linha = 2;
                    $i = 0;
                    foreach ($arrFinal as $it) {

                        $mod = ($i % $itens_por_linha);
                        if ($mod === 0) {
                            $tag_consideracao_final.= '<tr>';
                            $open = true;
                        }
                            $file_name = pathinfo($it->arquivo, PATHINFO_FILENAME);

                            $tag_consideracao_final.= '
                            <td style="max-width:500px;">
                                <p>"'.$file_name.'"</p>
                                <img style="width:250px;" src="'.$it->arquivo.'">
                            </td>';
                            $qtd++;

                        if ($mod === $itens_por_linha - 1) {
                            $tag_consideracao_final .= '</tr>';
                            $open = false;
                            $qtd = 0;
                        }   
                        $i++;
                    }

                    while ($qtd !== 0 && $qtd < $itens_por_linha) {
                        $tag_consideracao_final .= '<td></td>';
                        if ($qtd === $itens_por_linha - 1) {
                            $tag_consideracao_final .= '</tr>';
                        }
                        $qtd++;
                    }

                    $tag_consideracao_final .= '</tbody></table>';
                }


                $html->conteudo = str_replace('[tag_numero_projeto]', $tag_numero_projeto, $html->conteudo);
                $html->conteudo = str_replace('[tag_nome_projeto]', $tag_nome_projeto, $html->conteudo);
                $html->conteudo = str_replace('[tag_cnpj_cliente]', $tag_cnpj_cliente, $html->conteudo);
                $html->conteudo = str_replace('[tag_gestor]', $tag_gestor, $html->conteudo);
                $html->conteudo = str_replace('[tag_escopo_servico]', $tag_escopo_servico, $html->conteudo);
                $html->conteudo = str_replace('[tag_diagnostico]', $tag_diagnostico, $html->conteudo);
                $html->conteudo = str_replace('[tag_razao_social]', $tag_razao_social, $html->conteudo);
                $html->conteudo = str_replace('[tag_data_projeto]', $tag_data_projeto, $html->conteudo);
                $html->conteudo = str_replace('[tag_endereco_cliente]', $tag_endereco_cliente, $html->conteudo);
                $html->conteudo = str_replace('[tag_telefone_cliente]', $tag_telefone_cliente, $html->conteudo);
                $html->conteudo = str_replace('[tag_email_cliente]', $tag_email_cliente, $html->conteudo);
                $html->conteudo = str_replace('[tag_servicos_incluso]', $tag_servicos_incluso, $html->conteudo);
                $html->conteudo = str_replace('[tag_servicos_incluso_etapa1]', $tag_servicos_incluso_etapa1, $html->conteudo);
                $html->conteudo = str_replace('[tag_servicos_incluso_etapa2]', $tag_servicos_incluso_etapa2, $html->conteudo);
                $html->conteudo = str_replace('[tag_prazo]', $tag_prazo, $html->conteudo);
                $html->conteudo = str_replace('[tag_valor_projeto]', $tag_valor_projeto, $html->conteudo);
                $html->conteudo = str_replace('[tag_valor_projeto_extenso]', $tag_valor_projeto_extenso, $html->conteudo);
                $html->conteudo = str_replace('[tag_forma_pagamento]', $tag_forma_pagamento, $html->conteudo);
                $html->conteudo = str_replace('[tag_descricao_pagamento]', $tag_descricao_pagamento, $html->conteudo);
                $html->conteudo = str_replace('[tag_orcamento_descricao]', $tag_orcamento_descricao, $html->conteudo);
                $html->conteudo = str_replace('[tag_numero_funcionarios]', $tag_numero_funcionarios, $html->conteudo);
                $html->conteudo = str_replace('[tag_info_gerais]', $tag_info_gerais, $html->conteudo);
                $html->conteudo = str_replace('[tag_concorrencia]', $tag_concorrencia, $html->conteudo);
                $html->conteudo = str_replace('[tag_verba_investimento]', $tag_verba_investimento, $html->conteudo);
                $html->conteudo = str_replace('[tag_historico_empresa]', $tag_historico_empresa, $html->conteudo);
                $html->conteudo = str_replace('[tag_problematica_solucao]', $tag_problematica_solucao, $html->conteudo);
                $html->conteudo = str_replace('[tag_produtos_nao_incluidos]', $tag_produtos_nao_incluidos, $html->conteudo);
                $html->conteudo = str_replace('[tag_nome_fantasia]', $tag_nome_fantasia, $html->conteudo);
                $html->conteudo = str_replace('[tag_documentos]', $tag_documentos, $html->conteudo);
                $html->conteudo = str_replace('[tag_projetos_executados]', $tag_projetos_executados, $html->conteudo);
                $html->conteudo = str_replace('[tag_area_intervencao]', $tag_area_intervencao, $html->conteudo);
                $html->conteudo = str_replace('[tag_registro_local]', $tag_registro_local, $html->conteudo);
                $html->conteudo = str_replace('[tag_projetos_executados_observacao]', $tag_projetos_executados_observacao, $html->conteudo);
                $html->conteudo = str_replace('[tag_area_intervencao_valor]', $tag_area_intervencao_valor, $html->conteudo);
                $html->conteudo = str_replace('[tag_plano_acao]', $tag_plano_acao, $html->conteudo);
                $html->conteudo = str_replace('[tag_relatorio_projeto]', $tag_relatorio_projeto, $html->conteudo);
                $html->conteudo = str_replace('[tag_plano_acao_final]', $tag_plano_acao_final, $html->conteudo);
                $html->conteudo = str_replace('[tag_consideracao_final_texto]', $tag_consideracao_final_texto, $html->conteudo);
                $html->conteudo = str_replace('[tag_consideracao_final]', $tag_consideracao_final, $html->conteudo);
                $html->conteudo = str_replace('[tag_data_pre_diagnostico]', $tag_data_pre_diagnostico, $html->conteudo);
                $html->conteudo = str_replace('[tag_data_etapa]', $tag_data_etapa, $html->conteudo);
                $html->conteudo = str_replace('[tag_data_tecnico_final]', $tag_data_tecnico_final, $html->conteudo);
                $html->conteudo = str_replace('[tag_data_orcamento]', $tag_data_orcamento, $html->conteudo);
                $html->conteudo = str_replace('[tag_nome_razao_cpf_cnpj]', $tag_nome_razao_cpf_cnpj, $html->conteudo);

                

                $pdf->writeHTML($html->conteudo, true, false, true, false, '');
            }
                 
            $arq = PATH."/tmp/RelatorioProjeto.pdf"; 
            $pdf->Output( $arq, "F");
            
            //END TCPDF
        TTransaction::close();

        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}   