<?php
/**
 * ProdutoComposicao Active Record
 * @author  Fred Azv
 */
class ProdutoComposicao extends TRecord
{
    const TABLENAME = 'produto_composicao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $produto;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('produto_id');
        parent::addAttribute('composicao_id');
        parent::addAttribute('quantidade');
        parent::addAttribute('valor_unidade');
        parent::addAttribute('valor_total');
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
            $this->produto = new Produto($this->composicao_id);

        return $this->produto;
    }
    


}
