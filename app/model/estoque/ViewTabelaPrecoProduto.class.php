<?php
/**
 * Viewlocal Active Record
 * @author  Fred Azv.
 */
class ViewTabelaPrecoProduto extends TRecord
{
    const TABLENAME = 'viewtabelaprecoproduto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}


    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('produto_id');
        parent::addAttribute('preco');
        parent::addAttribute('promocao');

    }


}