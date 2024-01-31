<?php
/**
 * ProdutoUnidadeMedida Active Record
 * @author  <your-name-here>
 */
class ProdutoUnidadeMedida extends TRecord
{
    const TABLENAME = 'produto_unidade_medida';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cod');
        parent::addAttribute('descricao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


}
