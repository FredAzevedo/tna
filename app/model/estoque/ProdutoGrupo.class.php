<?php
/**
 * ProdutoGrupo Active Record
 * @author  <your-name-here>
 */
class ProdutoGrupo extends TRecord
{
    const TABLENAME = 'produto_grupo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
    }


}
