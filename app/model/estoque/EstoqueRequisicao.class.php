<?php
/**
 * EstoqueRequisicao Active Record
 * @author  <your-name-here>
 */
class EstoqueRequisicao extends TRecord
{
    const TABLENAME = 'estoque_requisicao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $system_user;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_requisicao');
        parent::addAttribute('hora_requisicao');
        parent::addAttribute('responsavel_id');
        parent::addAttribute('user_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('observacao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_system_unit
     * Sample of usage: $estoque_requisicao->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $estoque_requisicao->system_unit->attribute;
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
    
    
    /**
     * Method set_system_user
     * Sample of usage: $estoque_requisicao->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $estoque_requisicao->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->responsavel_id);
    
        // returns the associated object
        return $this->system_user;
    }


    public function set_tecnico(SystemUser $object)
    {
        $this->tecnico = $object;
        $this->tecnico_id = $object->id;
    }
    
    public function get_tecnico()
    {
        // loads the associated object
        if (empty($this->tecnico))
            $this->tecnico = new SystemUser($this->user_id);
    
        // returns the associated object
        return $this->tecnico;
    }

}
