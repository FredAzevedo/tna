<?php
/**
 * ConciliacaoBancaria Active Record
 * @author  <your-name-here>
 */
class ConciliacaoBancaria extends TRecord
{
    const TABLENAME = 'conciliacao_bancaria';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $conta_bancaria;
    private $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('dataMov');
        parent::addAttribute('descricao');
        parent::addAttribute('valor');
        parent::addAttribute('tipo');
        parent::addAttribute('unit_id');
        parent::addAttribute('conta_bancaria_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_conta_bancaria
     * Sample of usage: $conciliacao_bancaria->conta_bancaria = $object;
     * @param $object Instance of ContaBancaria
     */
    public function set_conta_bancaria(ContaBancaria $object)
    {
        $this->conta_bancaria = $object;
        $this->conta_bancaria_id = $object->id;
    }
    
    /**
     * Method get_conta_bancaria
     * Sample of usage: $conciliacao_bancaria->conta_bancaria->attribute;
     * @returns ContaBancaria instance
     */
    public function get_conta_bancaria()
    {
        // loads the associated object
        if (empty($this->conta_bancaria))
            $this->conta_bancaria = new ContaBancaria($this->conta_bancaria_id);
    
        // returns the associated object
        return $this->conta_bancaria;
    }
    
    
    /**
     * Method set_system_unit
     * Sample of usage: $conciliacao_bancaria->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $conciliacao_bancaria->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    


}
