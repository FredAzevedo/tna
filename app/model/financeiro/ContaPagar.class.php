<?php
/**
 * ContaPagar Active Record
 * @author  Fred Az.
 */
class ContaPagar extends TRecord
{
    const TABLENAME = 'conta_pagar';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $tipo_pgto;
    private $fornecedor;
    private $pc_despesa;
    private $departamento;
    private $conta_bancaria;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_conta');
        parent::addAttribute('descricao');
        parent::addAttribute('documento');
        parent::addAttribute('previsao');
        parent::addAttribute('data_vencimento');
        parent::addAttribute('multa');
        parent::addAttribute('juros');
        parent::addAttribute('taxas');
        parent::addAttribute('valor');
        parent::addAttribute('desconto');
        parent::addAttribute('portador');
        parent::addAttribute('observacao');
        parent::addAttribute('baixa');
        parent::addAttribute('data_baixa');
        parent::addAttribute('valor_pago');
        parent::addAttribute('valor_parcial');
        parent::addAttribute('valor_real');
        parent::addAttribute('replica');
        parent::addAttribute('parcelas');
        parent::addAttribute('nparcelas');
        parent::addAttribute('intervalo');
        parent::addAttribute('responsavel');
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('tipo_pgto_id');
        parent::addAttribute('tipo_forma_pgto_id');
        parent::addAttribute('fornecedor_id');
        parent::addAttribute('pc_despesa_id');
        parent::addAttribute('pc_despesa_nome');
        parent::addAttribute('departamento_id');
        parent::addAttribute('conta_bancaria_id');
        parent::addAttribute('relatorio_customizado_id');
        parent::addAttribute('split');
        parent::addAttribute('conciliado');
    }

    
    /**
     * Method set_system_unit
     * Sample of usage: $conta_pagar->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $conta_pagar->system_unit->attribute;
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
     * Method set_tipo_pgto
     * Sample of usage: $conta_pagar->tipo_pgto = $object;
     * @param $object Instance of TipoPgto
     */
    public function set_tipo_pgto(TipoPgto $object)
    {
        $this->tipo_pgto = $object;
        $this->tipo_pgto_id = $object->id;
    }
    
    /**
     * Method get_tipo_pgto
     * Sample of usage: $conta_pagar->tipo_pgto->attribute;
     * @returns TipoPgto instance
     */
    public function get_tipo_pgto()
    {
        // loads the associated object
        if (empty($this->tipo_pgto))
            $this->tipo_pgto = new TipoPgto($this->tipo_pgto_id);
    
        // returns the associated object
        return $this->tipo_pgto;
    }
    
    
    /**
     * Method set_fornecedor
     * Sample of usage: $conta_pagar->fornecedor = $object;
     * @param $object Instance of Fornecedor
     */
    public function set_fornecedor(Fornecedor $object)
    {
        $this->fornecedor = $object;
        $this->fornecedor_id = $object->id;
    }
    
    /**
     * Method get_fornecedor
     * Sample of usage: $conta_pagar->fornecedor->attribute;
     * @returns Fornecedor instance
     */
    public function get_fornecedor()
    {
        // loads the associated object
        if (empty($this->fornecedor))
            $this->fornecedor = new Fornecedor($this->fornecedor_id);
    
        // returns the associated object
        return $this->fornecedor;
    }
    
    
    /**
     * Method set_pc_despesa
     * Sample of usage: $conta_pagar->pc_despesa = $object;
     * @param $object Instance of PcDespesa
     */
    public function set_pc_despesa(PcDespesa $object)
    {
        $this->pc_despesa = $object;
        $this->pc_despesa_id = $object->id;
    }
    
    /**
     * Method get_pc_despesa
     * Sample of usage: $conta_pagar->pc_despesa->attribute;
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
     * Method set_departamento
     * Sample of usage: $conta_pagar->departamento = $object;
     * @param $object Instance of Departamento
     */
    public function set_departamento(Departamento $object)
    {
        $this->departamento = $object;
        $this->departamento_id = $object->id;
    }
    
    /**
     * Method get_departamento
     * Sample of usage: $conta_pagar->departamento->attribute;
     * @returns Departamento instance
     */
    public function get_departamento()
    {
        // loads the associated object
        if (empty($this->departamento))
            $this->departamento = new Departamento($this->departamento_id);
    
        // returns the associated object
        return $this->departamento;
    }
    
    
    /**
     * Method set_conta_bancaria
     * Sample of usage: $conta_pagar->conta_bancaria = $object;
     * @param $object Instance of ContaBancaria
     */
    public function set_conta_bancaria(ContaBancaria $object)
    {
        $this->conta_bancaria = $object;
        $this->conta_bancaria_id = $object->id;
    }
    
    /**
     * Method get_conta_bancaria
     * Sample of usage: $conta_pagar->conta_bancaria->attribute;
     * @returns ContaBancaria instance
     */
    public function get_conta_bancaria()
    {
        // loads the associated object
        if (empty($this->conta_bancaria))
            $this->conta_bancaria = new ContaBancaria($this->conta_bancaria_id);
    
        // returns the associated object
        return $this->conta_bancaria;
    }
    
    public function set_TipoFormaPgto(TipoFormaPgto $object)
    {
        $this->TipoFormaPgto = $object;
        $this->os_id = $object->id;
    }
    
    public function get_TipoFormaPgto()
    {
        // loads the associated object
        if (empty($this->TipoFormaPgto))
            $this->TipoFormaPgto = new TipoFormaPgto($this->tipo_forma_pgto_id);
    
        // returns the associated object
        return $this->TipoFormaPgto;
    }


}
