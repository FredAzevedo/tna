<?php
/**
 * Cartao Active Record
 * @author  Fred Azv.
 */
class Cartao extends TRecord
{
    const TABLENAME = 'cartao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $system_unit;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        //parent::addAttribute('unit_id');
        parent::addAttribute('nome_cartao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }

    public function get_system_unit()
    {
       
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->unit_id);

        return $this->system_unit;
    }

}
