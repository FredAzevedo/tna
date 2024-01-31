<?php
/**
 * PlanoUnidade Active Record
 * @author  Joao Victor Marques de Oliveira - jvo.marques@gmail.com
 */
class PlanoUnidade extends TRecord
{
    const TABLENAME = 'plano_unidade';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $plano;
    private $system_unit;

    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('plano_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->unit_id = $object->id;
    }
    
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }

    public function set_plano(Plano $object)
    {
        $this->plano = $object;
        $this->plano_id = $object->id;
    }
    

    public function get_plano()
    {
        // loads the associated object
        if (empty($this->plano))
            $this->plano = new Plano($this->plano_id);
    
        // returns the associated object
        return $this->plano;
    }
    


}
