<?php
/**
 * NfseTipotributacao Active Record
 * @author  Fred Azv.
 */
class NfseTipotributacao extends TRecord
{
    const TABLENAME = 'nfse_tipotributacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


}
