<?php
/**
 * Produto Active Record
 * @author  Fred Azv.
 */
class Produto extends TRecord
{
    const TABLENAME = 'produto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $produto_grupo;
    private $produto_subgrupo;
    private $produto_fabricante;
    private $produto_modelo;
    private $system_unit;
    private $nfe_regra;
    private $produto_unidade_medida;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('produto_grupo_id');
        parent::addAttribute('produto_subgrupo_id');
        parent::addAttribute('produto_fabricante_id');
        parent::addAttribute('produto_modelo_id');
        parent::addAttribute('produto_complexidade_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('ncm');
        parent::addAttribute('barras');
        parent::addAttribute('cod_referencia');
        parent::addAttribute('padrao');
        parent::addAttribute('unidade_medida_id');
        parent::addAttribute('nome_produto');
        parent::addAttribute('local');
        parent::addAttribute('preco_venda');
        parent::addAttribute('estoque_min');
        parent::addAttribute('estoque_max');
        parent::addAttribute('serial');
        parent::addAttribute('preco_ultima_compra'); 
        parent::addAttribute('obs');
        parent::addAttribute('tipo_produto');
        parent::addAttribute('image_produto');
        parent::addAttribute('composicao');
        parent::addAttribute('kit');
        parent::addAttribute('nve');
        parent::addAttribute('cEANTrib');
        parent::addAttribute('CEST');
        parent::addAttribute('vFrete');
        parent::addAttribute('vSeg');
        parent::addAttribute('vOutro');
        parent::addAttribute('extipi');
        parent::addAttribute('orig');
        parent::addAttribute('MVA');
        parent::addAttribute('impostos_venda');
        parent::addAttribute('impostos_compra');
        parent::addAttribute('anvisa');
        parent::addAttribute('pis');
        parent::addAttribute('cofins');
        parent::addAttribute('icms');
        parent::addAttribute('iss');
        parent::addAttribute('ipi');
        parent::addAttribute('tipo');
        parent::addAttribute('comissao');
        parent::addAttribute('tributacao_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }
    
    public function set_produto_modelo(ProdutoModelo $object)
    {
        $this->produto_modelo = $object;
        $this->produto_modelo_id = $object->id;
    }
    
    public function get_produto_modelo()
    {
        if (empty($this->produto_modelo))
            $this->produto_modelo = new ProdutoModelo($this->produto_modelo_id);

        return $this->produto_modelo;
    }

    public function set_produto_fabricante(ProdutoFabricante $object)
    {
        $this->produto_fabricante = $object;
        $this->produto_fabricante_id = $object->id;
    }
    
    public function get_produto_fabricante()
    {
        if (empty($this->produto_fabricante))
            $this->produto_fabricante = new ProdutoFabricante($this->produto_fabricante_id);
        
        return $this->produto_fabricante;
    }


    public function set_produto_complexidade(ProdutoComplexidade $object)
    {
        $this->produto_complexidade = $object;
        $this->produto_complexidade_id = $object->id;
    }
    
    public function get_produto_complexidade()
    {
        // loads the associated object
        if (empty($this->produto_complexidade))
            $this->produto_complexidade = new ProdutoComplexidade($this->produto_complexidade_id);
    
        // returns the associated object
        return $this->produto_complexidade;
    }

    public function set_produto_grupo(ProdutoGrupo $object)
    {
        $this->produto_grupo = $object;
        $this->produto_grupo_id = $object->id;
    }
    
    public function get_produto_grupo()
    {
        // loads the associated object
        if (empty($this->produto_grupo))
            $this->produto_grupo = new ProdutoGrupo($this->produto_grupo_id);
    
        // returns the associated object
        return $this->produto_grupo;
    }
    
    public function set_produto_subgrupo(ProdutoSubgrupo $object)
    {
        $this->produto_subgrupo = $object;
        $this->produto_subgrupo_id = $object->id;
    }
    
    public function get_produto_subgrupo()
    {
        // loads the associated object
        if (empty($this->produto_subgrupo))
            $this->produto_subgrupo = new ProdutoSubgrupo($this->produto_subgrupo_id);
    
        // returns the associated object
        return $this->produto_subgrupo;
    }
    
    public function set_produto_unidade_medida( ProdutoUnidadeMedida $object)
    {
        $this->produto_unidade_medida = $object;
        $this->unidade_medida_id = $object->id;
    }

    public function get_produto_unidade_medida()
    {
        if(empty($this->produto_unidade_medida))
            $this->produto_unidade_medida = new ProdutoUnidadeMedida($this->unidade_medida_id);

        return $this->produto_unidade_medida;
    }
    
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    

    public function set_nfe_regra(NfeRegra $object)
    {
        $this->nfe_regra = $object;
        $this->nfe_regra_id = $object->id;
    }
    
    public function get_nfe_regra()
    {
        // loads the associated object
        if (empty($this->nfe_regra))
            $this->nfe_regra = new NfeRegra($this->nfe_regra_id);
    
        // returns the associated object
        return $this->nfe_regra;
    }

    public function get_produtoServico() //Associação
    {
        if($this->tipo == "P"){
            return 'Produto';
        }
        return 'Serviço';  
    }
    
    public function get_simNao() //Associação
    {
        if($this->sim_nao == "N"){
            return 'Não';
        }
        return 'Sim';
    }


}
