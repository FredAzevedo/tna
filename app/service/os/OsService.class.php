<?php

class OsService {

    public static function atualizarDiagnosticoDaOS($os_id, $diagnostico){

        try
        {
            $os = new Os($os_id);
            $os->diagnostico = $diagnostico;
            $os->store();
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            //$this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
        
    }
}