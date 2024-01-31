<?php

class EmailUnidade extends TRecord
{
    const TABLENAME = 'emails_unidade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';

//    private $email;
//    private $unidades_id;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('responsavel');
        parent::addAttribute('email');
        parent::addAttribute('unidades_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        //parent::addAttribute('deleted_at');
    }
}

