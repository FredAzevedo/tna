<?php
/**
 * NfseCodigoServicos Active Record
 * @author  Fred Azv.
 */
class NfseCodigoServicos extends TRecord
{
    const TABLENAME = 'nfse_codigo_servicos';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('codigo');
        parent::addAttribute('descricao');
        parent::addAttribute('IRRF');
        parent::addAttribute('PIS');
        parent::addAttribute('COFINS');
        parent::addAttribute('CSLL');
        parent::addAttribute('comentario');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


}
