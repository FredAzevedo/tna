<?php
/**
 * ProdutoFabricante Active Record
 * @author  Fred Avz.
 */
class ProdutoFabricante extends TRecord
{
    const TABLENAME = 'produto_fabricante';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


}
