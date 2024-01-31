<?php
/**
 * LancamentoBancario Active Record
 * @author  Fred Az.
 */
class LancamentoBancario extends TRecord
{
    const TABLENAME = 'lancamento_bancario';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $system_user;
    private $pc_despesa;
    private $pc_receita;
    private $conta_bancaria;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('conta_bancaria_id');
        parent::addAttribute('data_lancamento');
        parent::addAttribute('tipo');
        parent::addAttribute('historico');
        parent::addAttribute('status');
        parent::addAttribute('valor');
        parent::addAttribute('pc_receita_id');
        parent::addAttribute('pc_despesa_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
    }

    public function get_valorTipo() //Associação
    {
        if($this->tipo == "1"){
            return 'Receita';
        }
        return 'Despesa';
    }


    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    

    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    
    
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    

    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);
    
        // returns the associated object
        return $this->system_user;
    }
    
    
    public function set_pc_despesa(PcDespesa $object)
    {
        $this->pc_despesa = $object;
        $this->pc_despesa_id = $object->id;
    }
    

    public function get_pc_despesa()
    {
        // loads the associated object
        if (empty($this->pc_despesa))
            $this->pc_despesa = new PcDespesa($this->pc_despesa_id);
    
        // returns the associated object
        return $this->pc_despesa;
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
    
    
    public function set_conta_bancaria(ContaBancaria $object)
    {
        $this->conta_bancaria = $object;
        $this->conta_bancaria_id = $object->id;
    }
    
    public function get_conta_bancaria()
    {
        // loads the associated object
        if (empty($this->conta_bancaria))
            $this->conta_bancaria = new ContaBancaria($this->conta_bancaria_id);
    
        // returns the associated object
        return $this->conta_bancaria;
    }
    
    public function get_contaBancaria()
    {
        if (empty($this->conta_bancaria))
            $this->conta_bancaria = new ContaBancaria($this->conta_bancaria_id);
    
        return $this->conta_bancaria->conta." - ".$this->conta_bancaria->conta_dv;
    }

}
