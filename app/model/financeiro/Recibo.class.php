<?php
/**
 * Recibo Active Record
 * @author  Fred Azv.
 */
class Recibo extends TRecord
{
    const TABLENAME = 'recibo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $cliente;
    private $pc_receita;
    private $pc_despesa;
    private $system_user;
    private $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_vencimento');
        parent::addAttribute('descricao');
        parent::addAttribute('valor');
        parent::addAttribute('cliente_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('tipo_forma_pgto_id');
        parent::addAttribute('tipo_pgto_id');
        parent::addAttribute('pc_receita_id');
        parent::addAttribute('pc_receita_nome');
        parent::addAttribute('status');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    public function get_StatusRecibo() //Associação
    {
        if($this->status == "B"){
            return 'Baixado';
        }
        return 'Em Aberto';
    }

    /**
     * Method set_cliente
     * Sample of usage: $recibo->cliente = $object;
     * @param $object Instance of Cliente
     */
    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }
    
    /**
     * Method get_cliente
     * Sample of usage: $recibo->cliente->attribute;
     * @returns Cliente instance
     */
    public function get_cliente()
    {
        // loads the associated object
        if (empty($this->cliente))
            $this->cliente = new Cliente($this->cliente_id);
    
        // returns the associated object
        return $this->cliente;
    }
    
    
    /**
     * Method set_pc_receita
     * Sample of usage: $recibo->pc_receita = $object;
     * @param $object Instance of PcReceita
     */
    public function set_pc_receita(PcReceita $object)
    {
        $this->pc_receita = $object;
        $this->pc_receita_id = $object->id;
    }
    
    /**
     * Method get_pc_receita
     * Sample of usage: $recibo->pc_receita->attribute;
     * @returns PcReceita instance
     */
    public function get_pc_receita()
    {
        // loads the associated object
        if (empty($this->pc_receita))
            $this->pc_receita = new PcReceita($this->pc_receita_id);
    
        // returns the associated object
        return $this->pc_receita;
    }
    
    
    /**
     * Method set_pc_despesa
     * Sample of usage: $recibo->pc_despesa = $object;
     * @param $object Instance of PcDespesa
     */
    public function set_pc_despesa(PcDespesa $object)
    {
        $this->pc_despesa = $object;
        $this->pc_despesa_id = $object->id;
    }
    
    /**
     * Method get_pc_despesa
     * Sample of usage: $recibo->pc_despesa->attribute;
     * @returns PcDespesa instance
     */
    public function get_pc_despesa()
    {
        // loads the associated object
        if (empty($this->pc_despesa))
            $this->pc_despesa = new PcDespesa($this->pc_despesa_id);
    
        // returns the associated object
        return $this->pc_despesa;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $recibo->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $recibo->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);
    
        // returns the associated object
        return $this->system_user;
    }
    
    
    /**
     * Method set_system_unit
     * Sample of usage: $recibo->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $recibo->system_unit->attribute;
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
