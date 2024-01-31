<?php

use Adianti\Database\TRecord;

/**
 * ContaBancaria Active Record
 * @author  Fred Az.
 */
class ContaBancaria extends TRecord
{
    const TABLENAME = 'conta_bancaria';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $_banco;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cod_banco');
        parent::addAttribute('agencia');
        parent::addAttribute('agencia_dv');
        parent::addAttribute('conta');
        parent::addAttribute('conta_dv');
        parent::addAttribute('tipo');
        parent::addAttribute('cep');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('bairro');
        parent::addAttribute('complemento');
        parent::addAttribute('cidade');
        parent::addAttribute('uf');
        parent::addAttribute('codMuni');
        parent::addAttribute('tel_gerente');
        parent::addAttribute('tel_banco');
        parent::addAttribute('gerente');
        parent::addAttribute('data_abaertura');
        parent::addAttribute('banco_id');
        parent::addAttribute('aceite');
        parent::addAttribute('especieDoc');
        parent::addAttribute('carteira');
        parent::addAttribute('convenio');
        parent::addAttribute('codigoCliente');
        parent::addAttribute('instrucoes1');
        parent::addAttribute('instrucoes2');
        parent::addAttribute('instrucoes3');
        parent::addAttribute('instrucoes4');
        parent::addAttribute('unit_id');
        parent::addAttribute('codigo_cooperativa');
        parent::addAttribute('tipo_remessa');
        parent::addAttribute('variacaoCarteira');
        parent::addAttribute('cip');
        parent::addAttribute('campo_range');
        parent::addAttribute('contaDv');
        parent::addAttribute('posto');
        parent::addAttribute('byte');
        parent::addAttribute('beneficiario');
        parent::addAttribute('ultimo_nossonumero');
        parent::addAttribute('ultima_remessa');
    }

    
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->unit_id = $object->id;
    }
    
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    

    public function set_banco(Banco $object)
    {
        $this->_banco = $object;
        $this->banco_id = $object->id;
    }
    
    public function get_banco()
    {
        // loads the associated object
        if (empty($this->_banco))
            $this->_banco = new Banco($this->banco_id);
    
        // returns the associated object
        return $this->_banco;
    }
    
}
