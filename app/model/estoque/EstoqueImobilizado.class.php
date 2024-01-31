<?php
/**
 * EstoqueImobilizado Active Record
 * @author  Fred Azv.
 */
class EstoqueImobilizado extends TRecord
{
    const TABLENAME = 'estoque_imobilizado';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; 
    
    
    private $system_unit;
    private $produto;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('local');
        parent::addAttribute('saldo');
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
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);

        return $this->system_unit;
    }
    
    public function set_produto(Produto $object)
    {
        $this->produto = $object;
        $this->produto_id = $object->id;
    }
    
    public function get_produto()
    {

        if (empty($this->produto))
            $this->produto = new Produto($this->produto_id);

        return $this->produto;
    }
    


}
