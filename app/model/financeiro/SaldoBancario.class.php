<?php
/**
 * SaldoBancario Active Record
 * @author  <your-name-here>
 */
class SaldoBancario extends TRecord
{
    const TABLENAME = 'saldo_bancario';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_banco');
        parent::addAttribute('valor');
    }


}
