<?php
/**
 * Plano Active Record
 * @author  <your-name-here>
 */
class Plano extends TRecord
{
    const TABLENAME = 'plano';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $lista_plano_unidade;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('nome');
        parent::addAttribute('valor');
        parent::addAttribute('pc_receita_id');
        parent::addAttribute('pc_receita_nome');
        parent::addAttribute('conta_bancaria_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_system_unit
     * Sample of usage: $plano->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $plano->system_unit->attribute;
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

    public function set_pc_receita(PcReceita $object)
    {
        $this->pc_receita = $object;
        $this->pc_receita_id = $object->id;
    }
    

    public function get_pc_receita()
    {
        // loads the associated object
        if (empty($this->pc_receita))
            $this->pc_receita = new PcReceita($this->pc_receita_id);
    
        // returns the associated object
        return $this->pc_receita;
    }
    


}
