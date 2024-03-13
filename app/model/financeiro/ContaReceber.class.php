<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  Fred Az.
 */
class ContaReceber extends TRecord
{
    const TABLENAME = 'conta_receber';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $system_user;
    private $cliente;
    private $tipo_pgto;
    private $pc_receita;
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
        parent::addAttribute('data_vencimento');
        parent::addAttribute('previsao');
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
        parent::addAttribute('boleto_status');
        parent::addAttribute('boleto_emitido');
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('boleto_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('tipo_pgto_id');
        parent::addAttribute('pc_receita_id');
        parent::addAttribute('pc_receita_nome');
        parent::addAttribute('conta_bancaria_id');
        parent::addAttribute('boleto_account_id');
        parent::addAttribute('tipo_forma_pgto_id');
        parent::addAttribute('tipo_pgto_id');
        parent::addAttribute('split');
        parent::addAttribute('nfse');
        parent::addAttribute('gera_nfse');
        parent::addAttribute('gerar_boleto');
        parent::addAttribute('cliente_contrato_id');
        parent::addAttribute('relatorio_customizado_id');
        parent::addAttribute('recibo');
        parent::addAttribute('juridico');
        parent::addAttribute('pedido_numero');
        parent::addAttribute('conciliado');
        parent::addAttribute('departamento_id');
        parent::addAttribute('centro_custo_id');
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
    
    
    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }
    

    public function get_cliente()
    {
        // loads the associated object
        if (empty($this->cliente))
            $this->cliente = new Cliente($this->cliente_id);
    
        // returns the associated object
        return $this->cliente;
    }
    
    
    public function set_tipo_pgto(TipoPgto $object)
    {
        $this->tipo_pgto = $object;
        $this->tipo_pgto_id = $object->id;
    }
    
    public function get_tipo_pgto()
    {
        // loads the associated object
        if (empty($this->tipo_pgto))
            $this->tipo_pgto = new TipoPgto($this->tipo_pgto_id);
    
        // returns the associated object
        return $this->tipo_pgto;
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
