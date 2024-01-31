<?php
/**
 * MovimentacaoBancaria Active Record
 * @author  Fred Az.
 */
class MovimentacaoBancaria extends TRecord
{
    const TABLENAME = 'movimentacao_bancaria';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $fornecedor;
    private $pc_receita;
    private $pc_despesa;
    private $conta_pagar;
    private $conta_receber;
    private $conta_bancaria;
    private $lancamento_bancario;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('valor_movimentacao');
        parent::addAttribute('data_lancamento');
        parent::addAttribute('data_vencimento');
        parent::addAttribute('data_baixa');
        parent::addAttribute('status');
        parent::addAttribute('historico');
        parent::addAttribute('baixa');
        parent::addAttribute('tipo');
        parent::addAttribute('documento');
        parent::addAttribute('unit_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('fornecedor_id');
        parent::addAttribute('pc_receita_id');
        parent::addAttribute('pc_receita_nome');
        parent::addAttribute('pc_despesa_id');
        parent::addAttribute('pc_despesa_nome');
        parent::addAttribute('conta_pagar_id');
        parent::addAttribute('conta_receber_id');
        parent::addAttribute('conta_bancaria_id');
        parent::addAttribute('lancamento_bancario_id');
        parent::addAttribute('transferencia_bancaria_id');
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
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    
    
    public function set_fornecedor(Fornecedor $object)
    {
        $this->fornecedor = $object;
        $this->fornecedor_id = $object->id;
    }
    
    public function get_fornecedor()
    {
        // loads the associated object
        if (empty($this->fornecedor))
            $this->fornecedor = new Fornecedor($this->fornecedor_id);
    
        // returns the associated object
        return $this->fornecedor;
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
    
    
    public function set_conta_pagar(ContaPagar $object)
    {
        $this->conta_pagar = $object;
        $this->conta_pagar_id = $object->id;
    }
    

    public function get_conta_pagar()
    {
        // loads the associated object
        if (empty($this->conta_pagar))
            $this->conta_pagar = new ContaPagar($this->conta_pagar_id);
    
        // returns the associated object
        return $this->conta_pagar;
    }
    
    
    public function set_conta_receber(ContaReceber $object)
    {
        $this->conta_receber = $object;
        $this->conta_receber_id = $object->id;
    }
    
    public function get_conta_receber()
    {
        // loads the associated object
        if (empty($this->conta_receber))
            $this->conta_receber = new ContaReceber($this->conta_receber_id);

        return $this->conta_receber;
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
    
    

    public function set_lancamento_bancario(LancamentoBancario $object)
    {
        $this->lancamento_bancario = $object;
        $this->lancamento_bancario_id = $object->id;
    }
    

    public function get_lancamento_bancario()
    {
        
        if (empty($this->lancamento_bancario))
            $this->lancamento_bancario = new LancamentoBancario($this->lancamento_bancario_id);

        return $this->lancamento_bancario;
    }

    public function get_historico_completo(){
        $complemento_historico = "";

        if($this->cliente_id != null){
            $cliente = new Cliente($this->cliente_id);
            $complemento_historico = $cliente->razao_social;
        } else if($this->fornecedor_id != null) {
            $fornecedor = new Fornecedor($this->fornecedor_id);
            $complemento_historico = $fornecedor->razao_social;
        }

        $retorno = $this->historico;

        if($complemento_historico != ""){

            $retorno = $retorno.'<div style="font-weight: bold;">'.$complemento_historico.'</div>';
        }

        return $retorno; 

    }
    
    
}
