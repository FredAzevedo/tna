<?php

class TelefonesFornecedor extends TRecord
{
    const TABLENAME = 'telefones_fornecedor';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('responsavel');
        parent::addAttribute('telefone');
        parent::addAttribute('fornecedor_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        //parent::addAttribute('deleted_at');
    }
}

