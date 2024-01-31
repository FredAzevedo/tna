<?php
/**
 * ProdutoFator Active Record
 * @author  <your-name-here>
 */
class ProdutoFator extends TRecord
{
    const TABLENAME = 'produto_fator';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo');
        parent::addAttribute('unid_ent');
        parent::addAttribute('unid_conv');
        parent::addAttribute('descricao');
        parent::addAttribute('valor');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    public function get_valorFator() //Associação
    {
        if($this->tipo == 'D'){
            return 'Divisor';
        }
        return 'Multiplicador';
    }

}
