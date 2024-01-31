<?php
/**
 * EstoqueTransferenciaImobilizado Active Record
 * @author  Fred Azv.
 */
class EstoqueTransferenciaImobilizado extends TRecord
{
    const TABLENAME = 'estoque_transferencia_imobilizado';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_user;
    private $system_unit;
    private $localorigem;
    private $localdestino;

 
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('local_origem');
        parent::addAttribute('local_destino');
        parent::addAttribute('baixa');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    public function get_system_user()
    {
        
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->user_id);
    
        return $this->system_user;
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
    

    public function get_localOrigem()
    {
        $this->localorigem = new Viewlocal($this->local_origem);
        return $this->localorigem;
    }

    public function get_localDestino()
    {
        $this->localdestino = new Viewlocal($this->local_destino);
        return $this->localdestino;
    }

}
