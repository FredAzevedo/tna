<?php
/**
 * Departamento Active Record
 * @author Fred Az.
 */
class Departamento extends TRecord
{
    const TABLENAME = 'departamento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('unit_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_system_unit
     * Sample of usage: $departamento->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $departamento->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    


}
