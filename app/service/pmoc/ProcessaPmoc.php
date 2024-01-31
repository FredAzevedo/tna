<?php
/*
 Chamada: ProcessaPmoc::processar($pmoc_id,$cliente_id);
*/
class ProcessaPmoc{

    public static function Processar($pmoc_id,$cliente_id){  

        try
        {
            //Pegar todos os equipamentos do cliente X/Y
            TTransaction::open('sample');
            $equipamento = ClienteEquipamento::where('cliente_id','=',$cliente_id)->load();
            TTransaction::close();

            if($equipamento)
            {
                foreach($equipamento as $item){

                    TTransaction::open('sample');
                    //verifica se já existem registros
                    $pmoc = Pmoc::where('id','=',$pmoc_id)->first();
                    TTransaction::close();

                    if($pmoc->processado != "S")
                    {
                        TTransaction::open('sample');
                        $ambiente_climatizado = new PmocAmbienteClimatizado;
                        $ambiente_climatizado->pmoc_id                  = $pmoc_id;
                        $ambiente_climatizado->pmoc_tipo_atividade_id   = $item->pmoc_tipo_atividade_id;
                        $ambiente_climatizado->cliente_equipamento_id   = $item->id;
                        $ambiente_climatizado->n_ocupantes_fixos        = $item->ocupantes_fixos;
                        $ambiente_climatizado->n_ocupantes_flutuantes   = $item->ocupantes_flutuantes;
                        $ambiente_climatizado->identificacao_ambiente   = $item->pmoc_ambiente->nome;
                        $ambiente_climatizado->area_climatizada         = $item->area_climatizada;
                        $ambiente_climatizado->carga_termica            = $item->carga_termica;
                        $ambiente_climatizado->store();
                        TTransaction::close();

                        //Popula  gestão de equipamentos
                        TTransaction::open('sample');
                        //seleciona os serviços para cada item de equipamento
                        $servicos = PmocItemServico::where('id','is not', null)->load();
                        if($servicos)
                        {
                            $count = 1;
                            foreach($servicos as $servico){
    
                                $servicos_item = new PmocServicoItem();
                                $servicos_item->pmoc_id = $pmoc_id;
                                $servicos_item->item = $count++;
                                $servicos_item->cliente_equipamento_id = $item->id;
                                $servicos_item->pmoc_item_servico_id = $servico->id;
                                $servicos_item->periodicidade = $servico->periodicidade;
                                $servicos_item->store();
                            }
                        }
                        TTransaction::close();
                    }
                }

                TTransaction::open('sample');
                $tualizaPmmoc = new Pmoc($pmoc_id);
                $tualizaPmmoc->processado = "S";
                $tualizaPmmoc->store();
                TTransaction::close();
                
                new TMessage('info', 'Equipamentos processados com sucesso! Verifique se todos os  itens de equipamentos estão conforme. Se estiver tudo  conforme clique no botão Próximo!');
            }
            else
            {
                new TMessage('error', 'Não foi encontrado equipamentos vinculaos a esse cliente! Favor, cadastre os Equipamentos na Gestão de Equipamentos.');
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
    }
}