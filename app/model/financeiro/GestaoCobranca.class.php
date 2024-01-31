<?php
/**
 * GestaoCobranca Active Record
 * @author  Fred Azv.
 */
class GestaoCobranca extends TRecord
{
    const TABLENAME = 'gestao_cobranca';
    const PRIMARYKEY= 'contas_receber_id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $cliente;
    private $conta_receber;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cliente_id');
        parent::addAttribute('razao_social');
        parent::addAttribute('vencimento');
        parent::addAttribute('dia');
        parent::addAttribute('mes');
        parent::addAttribute('ano');
        parent::addAttribute('valor');
        parent::addAttribute('devido');
        parent::addAttribute('telefone');
        parent::addAttribute('BAIXA');
    }

    
    /**
     * Method set_cliente
     * Sample of usage: $gestao_cobranca->cliente = $object;
     * @param $object Instance of Cliente
     */
    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }
    
    /**
     * Method get_cliente
     * Sample of usage: $gestao_cobranca->cliente->attribute;
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
     * Method set_conta_receber
     * Sample of usage: $gestao_cobranca->conta_receber = $object;
     * @param $object Instance of ContaReceber
     */
    public function set_conta_receber(ContaReceber $object)
    {
        $this->conta_receber = $object;
        $this->conta_receber_id = $object->id;
    }
    
    /**
     * Method get_conta_receber
     * Sample of usage: $gestao_cobranca->conta_receber->attribute;
     * @returns ContaReceber instance
     */
    public function get_conta_receber()
    {
        // loads the associated object
        if (empty($this->conta_receber))
            $this->conta_receber = new ContaReceber($this->conta_receber_id);
    
        // returns the associated object
        return $this->conta_receber;
    }
    


}
