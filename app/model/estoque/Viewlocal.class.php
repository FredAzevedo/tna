<?php
/**
 * Viewlocal Active Record
 * @author  Fred Azv.
 */
class Viewlocal extends TRecord
{
    const TABLENAME = 'viewlocal';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_fantasia');
    }


}
