<?php

class TelefonesCliente extends TRecord
{
    const TABLENAME = 'telefones_cliente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('responsavel');
        parent::addAttribute('telefone');
        parent::addAttribute('cliente_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        //parent::addAttribute('deleted_at');
    }
}

