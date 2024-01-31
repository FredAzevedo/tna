<?php

class CabecalhoReport2 extends TRecord
{
    const TABLENAME = 'cabecalhos_reports2';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unidades_id');
        parent::addAttribute('razao_social');
        parent::addAttribute('nome_fantasia');
        parent::addAttribute('endereco');
        parent::addAttribute('cnpj');
        parent::addAttribute('telefones');
        parent::addAttribute('ie');
        parent::addAttribute('campo1');
        parent::addAttribute('campo2');
        parent::addAttribute('campo3');
        parent::addAttribute('campo4');
        parent::addAttribute('campo5');
        parent::addAttribute('campo6');
        
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

}

