<?php
/**
 * EstoqueMovel Active Record
 * @author  <your-name-here>
 */
class EstoqueMovel extends TRecord
{
    const TABLENAME = 'estoque_movel';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('local');
        parent::addAttribute('saldo');
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
    

    public function set_produto(Produto $object)
    {
        $this->produto = $object;
        $this->produto_id = $object->id;
    }

    public function get_produto()
    {

        if (empty($this->produto))
            $this->produto = new Produto($this->produto_id);

        return $this->produto;
    }

    public function set_SystemUser(SystemUser $object)
    {
        $this->SystemUser = $object;
        $this->SystemUser_id = $object->id;
    }

    public function get_SystemUser()
    {

        if (empty($this->SystemUser))
            $this->SystemUser = new SystemUser($this->local);

        return $this->SystemUser;
    }


}
