<?php
/**
 * NfseCodigoAtividades Active Record
 * @author  Fred Azv.
 */
class NfseCodigoAtividades extends TRecord
{
    const TABLENAME = 'nfse_codigo_atividades';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $codigo_servico;
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nfse_codigo_servicos_id');
        parent::addAttribute('codigo');
        parent::addAttribute('descricao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    public function set_codigo_servico(NfseCodigoServicos $object)
    {
        $this->codigo_servico = $object;
        $this->nfse_codigo_servicos_id = $object->id;
    }
    
    public function get_codigo_servico()
    {
       
        if (empty($this->codigo_servico))
            $this->codigo_servico = new NfseCodigoServicos($this->nfse_codigo_servicos_id);
    
        return $this->codigo_servico;
    }

}   
