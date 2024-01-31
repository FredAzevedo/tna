<?php
/**
 * EstoqueTransferenciaImobilizadoItem Active Record
 * @author  Fred Azv
 */
class EstoqueTransferenciaImobilizadoItem extends TRecord
{
    const TABLENAME = 'estoque_transferencia_imobilizado_item';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $produto;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('estoque_transferencia_imobilizado_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('estado');
        parent::addAttribute('data_avaliacao');
        parent::addAttribute('valor_justo');
        parent::addAttribute('emplacamento');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
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
