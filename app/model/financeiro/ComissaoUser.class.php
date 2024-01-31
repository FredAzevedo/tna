<?php
/**
 * ComissaoUser Active Record
 * @author  Fred Azv.
 */
class ComissaoUser extends TRecord
{
    const TABLENAME = 'comissao_user';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $system_user;
    private $cliente;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_faturamento');
        parent::addAttribute('valor_faturamento');
        parent::addAttribute('taxa_comissao');
        parent::addAttribute('valor_comissao');
        parent::addAttribute('descricao');
        parent::addAttribute('pago');
        parent::addAttribute('tipo');
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_system_unit
     * Sample of usage: $comissao_user->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $comissao_user->system_unit->attribute;
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
     * Sample of usage: $comissao_user->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $comissao_user->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->user_id);
    
        // returns the associated object
        return $this->system_user;
    }


    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }
    
    public function get_cliente()
    {

        if (empty($this->cliente))
            $this->cliente = new Cliente($this->cliente_id);

        return $this->cliente;
    }


}
