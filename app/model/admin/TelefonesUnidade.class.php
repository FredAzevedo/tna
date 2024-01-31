<?php

class TelefonesUnidade extends TRecord
{
    const TABLENAME = 'telefones_unidade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}

    //private $telefone;
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('responsavel');
        parent::addAttribute('telefone');
        parent::addAttribute('unidades_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        //parent::addAttribute('deleted_at');
    }

}

