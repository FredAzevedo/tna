<?php
/**
 * TransferenciaBancaria Active Record
 * @author  Fred Azv.
 */
class TransferenciaBancaria extends TRecord
{
    const TABLENAME = 'transferencia_bancaria';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $system_user;
    private $pc_receita;
    private $pc_despesa;
    private $conta_bancariaCredito;
    private $conta_bancariaDebito;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('conta_bancaria_debito_id');
        parent::addAttribute('conta_bancaria_credito_id');
        parent::addAttribute('data_lancamento');
        parent::addAttribute('data_transferencia');
        parent::addAttribute('data_baixa');
        parent::addAttribute('valor');
        parent::addAttribute('pc_despesa_id');
        parent::addAttribute('pc_despesa_nome');
        parent::addAttribute('pc_receita_id');
        parent::addAttribute('pc_receita_nome');
        parent::addAttribute('observacao');
        parent::addAttribute('baixa');
    }

    
    /**
     * Method set_system_unit
     * Sample of usage: $transferencia_bancaria->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $transferencia_bancaria->system_unit->attribute;
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
     * Sample of usage: $transferencia_bancaria->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $transferencia_bancaria->system_user->attribute;
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
    
    
    /**
     * Method set_pc_receita
     * Sample of usage: $transferencia_bancaria->pc_receita = $object;
     * @param $object Instance of PcReceita
     */
    public function set_pc_receita(PcReceita $object)
    {
        $this->pc_receita = $object;
        $this->pc_receita_id = $object->id;
    }
    
    /**
     * Method get_pc_receita
     * Sample of usage: $transferencia_bancaria->pc_receita->attribute;
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
     * Sample of usage: $transferencia_bancaria->pc_despesa = $object;
     * @param $object Instance of PcDespesa
     */
    public function set_pc_despesa(PcDespesa $object)
    {
        $this->pc_despesa = $object;
        $this->pc_despesa_id = $object->id;
    }
    
    /**
     * Method get_pc_despesa
     * Sample of usage: $transferencia_bancaria->pc_despesa->attribute;
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
    

    public function set_conta_bancariaCredito(ContaBancaria $object)
    {
        $this->conta_bancariaCredito = $object;
        $this->conta_bancaria_credito_id = $object->id;
    }
    

    public function get_conta_bancariaCredito()
    {
        // loads the associated object
        if (empty($this->conta_bancariaCredito))
            $this->conta_bancariaCredito = new ContaBancaria($this->conta_bancaria_credito_id);
    
        // returns the associated object
        return $this->conta_bancariaCredito;
    }
    
    public function set_conta_bancariaDebito(ContaBancaria $object)
    {
        $this->conta_bancariaDebito = $object;
        $this->conta_bancaria_debito_id = $object->id;
    }
    

    public function get_conta_bancariaDebito()
    {
        // loads the associated object
        if (empty($this->conta_bancariaDebito))
            $this->conta_bancariaDebito = new ContaBancaria($this->conta_bancaria_debito_id);
    
        // returns the associated object
        return $this->conta_bancariaDebito;
    }

    public function get_tipoBaixa() //Associação
    {
        if($this->baixa == "S"){
            return 'Sim';
        }
        return 'Não';
    }
}
