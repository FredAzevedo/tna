<?php
/**
 * ClienteOrigem Active Record
 * @author  Joao Victor Marques - jvomarques@gmail.com
 */
class ClienteOrigem extends TRecord
{
    const TABLENAME = 'cliente_origem';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
    }


}
