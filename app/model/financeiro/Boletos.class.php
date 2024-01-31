<?php

use Adianti\Database\TRecord;

/**
 * Boleto Active Record
 * @author  <your-name-here>
 */
class Boletos extends TRecord
{
    const TABLENAME = 'boleto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $cliente;
    private $conta_receber;
    private $banco;
    private $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('dataVencimento');
        parent::addAttribute('valor');
        parent::addAttribute('multa');
        parent::addAttribute('juros');
        parent::addAttribute('numero');
        parent::addAttribute('numeroDocumento');
        parent::addAttribute('carteira');
        parent::addAttribute('conta_bancaria_id');
        parent::addAttribute('agencia');
        parent::addAttribute('convenio');
        parent::addAttribute('conta');
        parent::addAttribute('instrucao1');
        parent::addAttribute('instrucao2');
        parent::addAttribute('instrucao3');
        parent::addAttribute('instrucao4');
        parent::addAttribute('cliente_id');
        parent::addAttribute('conta_receber_id');
        parent::addAttribute('cliente_contrato_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('num_parcela');
        parent::addAttribute('remessa');
        parent::addAttribute('cod_banco');
        parent::addAttribute('aceite');
        parent::addAttribute('especieDoc');
        parent::addAttribute('codigoCliente');
        parent::addAttribute('codigo_cooperativa');
        parent::addAttribute('variacaocarteira');
        parent::addAttribute('cip');
        parent::addAttribute('campo_range');
        parent::addAttribute('contaDv');
        parent::addAttribute('posto');
        parent::addAttribute('byte');
        parent::addAttribute('dataDesconto');
        parent::addAttribute('dataDocumento');
        parent::addAttribute('dataProcessamento');
        parent::addAttribute('desconto');
        parent::addAttribute('jurosApos');
        parent::addAttribute('diasProtesto');
        parent::addAttribute('ativo');
        parent::addAttribute('ben_documento');
        parent::addAttribute('ben_nome');
        parent::addAttribute('ben_cep');
        parent::addAttribute('ben_endereco');
        parent::addAttribute('ben_bairro');
        parent::addAttribute('ben_uf');
        parent::addAttribute('ben_cidade');
        parent::addAttribute('pag_documento');
        parent::addAttribute('pag_nome');
        parent::addAttribute('pag_cep');
        parent::addAttribute('pag_endereco');
        parent::addAttribute('pag_bairro');
        parent::addAttribute('pag_uf');
        parent::addAttribute('pag_cidade');
        parent::addAttribute('path_pdf');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


    public function set_conta_bancaria(ContaBancaria $object)
    {
        $this->conta_bancaria = $object;
        $this->conta_bancaria_id = $object->id;
    }
    
    public function get_conta_bancaria()
    {
       
        if (empty($this->conta_bancaria))
            $this->conta_bancaria = new ContaBancaria($this->conta_bancaria_id);

        return $this->conta_bancaria;
    }

    
    /**
     * Method set_cliente
     * Sample of usage: $boleto->cliente = $object;
     * @param $object Instance of Cliente
     */
    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }
    
    /**
     * Method get_cliente
     * Sample of usage: $boleto->cliente->attribute;
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
     * Sample of usage: $boleto->conta_receber = $object;
     * @param $object Instance of ContaReceber
     */
    public function set_conta_receber(ContaReceber $object)
    {
        $this->conta_receber = $object;
        $this->conta_receber_id = $object->id;
    }
    
    /**
     * Method get_conta_receber
     * Sample of usage: $boleto->conta_receber->attribute;
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
    
    
    /**
     * Method set_banco
     * Sample of usage: $boleto->banco = $object;
     * @param $object Instance of Banco
     */
    public function set_banco(Banco $object)
    {
        $this->banco = $object;
        $this->banco_id = $object->id;
    }
    
    /**
     * Method get_banco
     * Sample of usage: $boleto->banco->attribute;
     * @returns Banco instance
     */
    public function get_banco()
    {
        // loads the associated object
        if (empty($this->banco))
            $this->banco = new Banco($this->banco_id);
    
        // returns the associated object
        return $this->banco;
    }
    
    
    /**
     * Method set_system_unit
     * Sample of usage: $boleto->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $boleto->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    


}
