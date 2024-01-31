<?php

class CabecalhoReport1 extends TRecord
{
    const TABLENAME = 'cabecalhos_reports1';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unidades_id');
        parent::addAttribute('linha1');
        parent::addAttribute('linha2');
        parent::addAttribute('linha3');
        parent::addAttribute('linha4');
        parent::addAttribute('linha5');
        parent::addAttribute('linha6');
        
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

}

