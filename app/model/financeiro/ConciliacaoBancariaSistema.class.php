<?php
/**
 * ConciliacaoBancariaSistema Active Record
 * @author  <your-name-here>
 */
class ConciliacaoBancariaSistema extends TRecord
{
    const TABLENAME = 'conciliacao_bancaria_sistema';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('data_vencimento');
        parent::addAttribute('valor');
        parent::addAttribute('unit_id');
        parent::addAttribute('cedente');
        parent::addAttribute('tipo_pgto_id');
        parent::addAttribute('lct_id');
        parent::addAttribute('lct_nome');
        parent::addAttribute('conta_bancaria_id');
        parent::addAttribute('conciliado');
        parent::addAttribute('tipo');
    }


}
