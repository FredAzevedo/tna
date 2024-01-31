<?php
/**
 * ClienteCobranca Active Record
 * @author  Fred Azv.
 */
class ClienteCobranca extends TRecord
{
    const TABLENAME = 'cliente_cobranca';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $cliente_id;
    private $system_user;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cliente_id');
        parent::addAttribute('user_id');
        parent::addAttribute('descricao');
        parent::addAttribute('status');
        parent::addAttribute('created_at');
        parent::addAttribute('update_at');
    }

    public function set_cliente_id(Cliente $object)
    {
        $this->cliente_id = $object;
        $this->cliente_id = $object->id;
    }
    
    public function get_cliente_id()
    {
     
        if (empty($this->cliente_id))
            $this->cliente_id = new Cliente($this->cliente_id);
        return $this->cliente_id;
    }
    
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }

    public function get_system_user()
    {

        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);

        return $this->system_user;
    }
    


}
