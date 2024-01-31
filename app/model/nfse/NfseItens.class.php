<?php
/**
 * NfseItens Active Record
 * @author  Fred Azv.
 */
class NfseItens extends TRecord
{
    const TABLENAME = 'nfse_itens';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $nfse;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nfse_id');
        parent::addAttribute('descricao');
        parent::addAttribute('valor');
        parent::addAttribute('quantidade');
        parent::addAttribute('total_item');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    public function set_nfse(NFSe $object)
    {
        $this->nfse = $object;
        $this->nfse_id = $object->id;
    }
    
    public function get_nfse()
    {
        // loads the associated object
        if (empty($this->nfse))
            $this->nfse = new NFSe($this->nfse_id);
    
        // returns the associated object
        return $this->nfse;
    }
    
}
