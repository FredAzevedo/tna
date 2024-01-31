<?php
/**
 * ProdutoSubgrupo Active Record
 * @author  <your-name-here>
 */
class ProdutoSubgrupo extends TRecord
{
    const TABLENAME = 'produto_subgrupo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $produto_grupo;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('produto_grupo_id');
        parent::addAttribute('nome');
    }

    
    /**
     * Method set_produto_grupo
     * Sample of usage: $produto_subgrupo->produto_grupo = $object;
     * @param $object Instance of ProdutoGrupo
     */
    public function set_produto_grupo(ProdutoGrupo $object)
    {
        $this->produto_grupo = $object;
        $this->produto_grupo_id = $object->id;
    }
    
    /**
     * Method get_produto_grupo
     * Sample of usage: $produto_subgrupo->produto_grupo->attribute;
     * @returns ProdutoGrupo instance
     */
    public function get_produto_grupo()
    {
        // loads the associated object
        if (empty($this->produto_grupo))
            $this->produto_grupo = new ProdutoGrupo($this->produto_grupo_id);
    
        // returns the associated object
        return $this->produto_grupo;
    }
    


}
