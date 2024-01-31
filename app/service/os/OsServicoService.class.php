<?php

class OsServicoService {

    public static function cadastrarHistorico($os_id, $os_servico_id, $tecnico_id, $situacao_id, $observacao, $diagnostico_atribuido){
        
        try {
            $ultimo_item_do_historico = HistoricoOsServico::where('os_servico_id', '=', $os_servico_id)
            ->orderBy('data_fim', 'desc')
            ->first();

            $data_agora = new DateTime();
            $data_agora = $data_agora->format('Y-m-d H:i:s');
            $data_inicio = $data_agora;
            $data_fim = $data_agora;

            if($ultimo_item_do_historico){
                $data_inicio = $ultimo_item_do_historico->data_fim;
            }

            $historico_os_servico = new HistoricoOsServico;
            $historico_os_servico->system_user_id = TSession::getValue('userid');
            $historico_os_servico->os_id = $os_id;
            $historico_os_servico->os_servico_id = $os_servico_id;
            $historico_os_servico->tecnico_id = $tecnico_id;
            $historico_os_servico->situacao_id = $situacao_id;
            $historico_os_servico->observacao = $observacao;
            $historico_os_servico->diagnostico_atribuido = $diagnostico_atribuido;
            $historico_os_servico->data_inicio = $data_inicio;
            $historico_os_servico->data_fim = $data_fim;
            $historico_os_servico->store();
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage()); 
            TTransaction::close();
            TTransaction::rollback();
        } 
    }
}