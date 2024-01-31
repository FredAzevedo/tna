<?php
/**
 * EstoqueDevolucao Active Record
 * @author  Fred Azv.
 */
class EstoqueDevolucao extends TRecord
{
    const TABLENAME = 'estoque_devolucao';
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
        parent::addAttribute('data_devolucao');
        parent::addAttribute('hora_devolucao');
        parent::addAttribute('responsavel_id');
        parent::addAttribute('user_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('observacao');
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
            $this->system_unit = new SystemUnit($this->system_unit_id);

        return $this->system_unit;
    }
    
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }

    public function get_system_user()
    {
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->responsavel_id);

        return $this->system_user;
    }

    public function set_tecnico(SystemUser $object)
    {
        $this->tecnico = $object;
        $this->tecnico_id = $object->id;
    }
    
    public function get_tecnico()
    {
        if (empty($this->tecnico))
            $this->tecnico = new SystemUser($this->user_id);
    
        return $this->tecnico;
    }

}
