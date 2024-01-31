<?php
/**
 * EstoqueConsumo Active Record
 * @author  Fred Azv.
 */
class EstoqueConsumo extends TRecord
{
    const TABLENAME = 'estoque_consumo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('local');
        parent::addAttribute('saldo');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


}
