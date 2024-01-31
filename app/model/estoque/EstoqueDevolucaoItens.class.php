<?php
/**
 * EstoqueDevolucaoItens Active Record
 * @author  Fred Azv.
 */
class EstoqueDevolucaoItens extends TRecord
{
    const TABLENAME = 'estoque_devolucao_itens';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';
    
    
    private $produto;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('estoque_devolucao_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('quantidade');
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
