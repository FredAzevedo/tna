<?php
/**
 * PcDespesa Active Record
 * @author  Fred Az.
 */
class PcDespesa extends TRecord
{
    const TABLENAME = 'pc_despesa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nivel1');
        parent::addAttribute('nivel2');
        parent::addAttribute('nivel3');
        parent::addAttribute('nivel4');
        parent::addAttribute('nome');
        parent::addAttribute('unit_id');
    }

    
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    

    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    
}
