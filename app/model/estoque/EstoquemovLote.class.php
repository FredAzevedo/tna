<?php
/**
 * EstoquemovLote Active Record
 * @author  <your-name-here>
 */
class EstoquemovLote extends TRecord
{
    const TABLENAME = 'estoquemov_lote';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $produto;
    private $estoquemov;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('estoquemov_id');
        parent::addAttribute('local');
        parent::addAttribute('lote');
        parent::addAttribute('quantidade');
        parent::addAttribute('saldo');
        parent::addAttribute('vencimento');
        parent::addAttribute('tipo');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_system_unit
     * Sample of usage: $estoquemov_lote->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $estoquemov_lote->system_unit->attribute;
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
     * Method set_produto
     * Sample of usage: $estoquemov_lote->produto = $object;
     * @param $object Instance of Produto
     */
    public function set_produto(Produto $object)
    {
        $this->produto = $object;
        $this->produto_id = $object->id;
    }
    
    /**
     * Method get_produto
     * Sample of usage: $estoquemov_lote->produto->attribute;
     * @returns Produto instance
     */
    public function get_produto()
    {
        // loads the associated object
        if (empty($this->produto))
            $this->produto = new Produto($this->produto_id);
    
        // returns the associated object
        return $this->produto;
    }
    
    
    /**
     * Method set_estoquemov
     * Sample of usage: $estoquemov_lote->estoquemov = $object;
     * @param $object Instance of Estoquemov
     */
    public function set_estoquemov(Estoquemov $object)
    {
        $this->estoquemov = $object;
        $this->estoquemov_id = $object->id;
    }
    
    /**
     * Method get_estoquemov
     * Sample of usage: $estoquemov_lote->estoquemov->attribute;
     * @returns Estoquemov instance
     */
    public function get_estoquemov()
    {
        // loads the associated object
        if (empty($this->estoquemov))
            $this->estoquemov = new Estoquemov($this->estoquemov_id);
    
        // returns the associated object
        return $this->estoquemov;
    }
    


}
