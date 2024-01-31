<?php
/**
 * CartaoCliente Active Record
 * @author  Fred Azv.
 */
class CartaoCliente extends TRecord
{
    const TABLENAME = 'cartao_cliente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $system_unit;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('cartao_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('ano_vencimento');
        parent::addAttribute('mes_vencimento');
        parent::addAttribute('cartao_truncado');
        parent::addAttribute('token_cartao');
        parent::addAttribute('msg');
        parent::addAttribute('status');
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
