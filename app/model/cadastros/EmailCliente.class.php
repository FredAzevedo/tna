<?php

use Adianti\Database\TRecord;

class EmailCliente extends TRecord
{
    const TABLENAME = 'emails_cliente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('responsavel');
        parent::addAttribute('email');
        parent::addAttribute('cliente_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }
}

