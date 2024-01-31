<?php
/**
 * ResponsePjbank Active Record
 * @author  <your-name-here>
 */
class ResponsePjbank extends TRecord
{
    const TABLENAME = 'response_pjbank';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('status');
        parent::addAttribute('response');
        parent::addAttribute('contrato_id');
        parent::addAttribute('descricao');
        parent::addAttribute('user_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


}
