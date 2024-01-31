<?php
/**
 * ProdutoTabelaPreco Active Record
 * @author  <your-name-here>
 */
class ProdutoTabelaPreco extends TRecord
{
    const TABLENAME = 'produto_tabela_preco';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $tabela_preco;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tabela_preco_id');
        parent::addAttribute('preco');
        parent::addAttribute('controla_validade');
        parent::addAttribute('data_validade');
        parent::addAttribute('descontoMax');
        parent::addAttribute('tem_comissao');
        parent::addAttribute('comissao');
        parent::addAttribute('tem_promocao');
        parent::addAttribute('promocao');
        parent::addAttribute('promocao_validade');
        parent::addAttribute('produto_id');
        parent::addAttribute('markup_preco_custo');
        parent::addAttribute('markup_despesa_variavel');
        parent::addAttribute('markup_despesa_fixa');
        parent::addAttribute('markup_lucro_desejado');
        parent::addAttribute('markup_preco_venda');
        parent::addAttribute('markup_comissao_tecnico');
        parent::addAttribute('markup_comissao_parceiro');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    public function set_tabela_preco(TabelaPrecos $object)
    {
        $this->tabela_preco = $object;
        $this->tabela_preco_id = $object->id;
    }
    
    public function get_tabela_preco()
    {
        if (empty($this->tabela_preco))
            $this->tabela_preco = new TabelaPrecos($this->tabela_preco_id);

        return $this->tabela_preco;
    }


}
