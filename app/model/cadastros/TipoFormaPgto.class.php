<?php
/**
 * TipoFormaPgto Active Record
 * @author  Fred Azv.
 */
class TipoFormaPgto extends TRecord
{
    const TABLENAME = 'tipo_forma_pgto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('regra');
        parent::addAttribute('parcela');
        parent::addAttribute('valor_minimo');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


}
