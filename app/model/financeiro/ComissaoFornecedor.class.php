<?php
/**
 * ComissaoFornecedor Active Record
 * @author  Fred Azv.
 */
class ComissaoFornecedor extends TRecord
{
    const TABLENAME = 'comissao_fornecedor';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $fornecedor;
    private $system_unit;
    private $cliente;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_faturamento');
        parent::addAttribute('valor_faturamento');
        parent::addAttribute('taxa_comissao');
        parent::addAttribute('valor_comissao');
        parent::addAttribute('descricao');
        parent::addAttribute('pago');
        parent::addAttribute('tipo');
        parent::addAttribute('unit_id');
        parent::addAttribute('fornecedor_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('descricao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_fornecedor
     * Sample of usage: $comissao_fornecedor->fornecedor = $object;
     * @param $object Instance of Fornecedor
     */
    public function set_fornecedor(Fornecedor $object)
    {
        $this->fornecedor = $object;
        $this->fornecedor_id = $object->id;
    }
    
    /**
     * Method get_fornecedor
     * Sample of usage: $comissao_fornecedor->fornecedor->attribute;
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
     * Method set_system_unit
     * Sample of usage: $comissao_fornecedor->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $comissao_fornecedor->system_unit->attribute;
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
