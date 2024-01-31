<?php
/**
 * ResponsavelTipo Active Record
 * @author  Fred Azv.
 */
class ResponsavelTipo extends TRecord
{
    const TABLENAME = 'responsavel_tipo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}

    const CREATEDAT = "created_at";
    const UPDATEDAT = "updated_at";
    const DELETEDAT = "deleted_at";
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


}
