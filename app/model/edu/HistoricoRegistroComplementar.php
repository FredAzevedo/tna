<?php
/**
 * HistoricoRegistroComplementar Active Record
 * @author  Fred Azv
 */
class HistoricoRegistroComplementar extends TRecord
{
    const TABLENAME = 'historico_registro_complementar';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $historico;
    private $serie;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('historico_id');
        parent::addAttribute('serie_id');
        parent::addAttribute('ano');
        parent::addAttribute('estabelecimento');
        parent::addAttribute('municipio');
        parent::addAttribute('uf');
    }

    
    public function set_historico(Historico $object)
    {
        $this->historico = $object;
        $this->historico_id = $object->id;
    }
    
    public function get_historico()
    {
        // loads the associated object
        if (empty($this->historico))
            $this->historico = new Historico($this->historico_id);
    
        // returns the associated object
        return $this->historico;
    }
    
    
    public function set_serie(Serie $object)
    {
        $this->serie = $object;
        $this->serie_id = $object->id;
    }
    

    public function get_serie()
    {
        // loads the associated object
        if (empty($this->serie))
            $this->serie = new Serie($this->serie_id);
    
        // returns the associated object
        return $this->serie;
    }
    


}
