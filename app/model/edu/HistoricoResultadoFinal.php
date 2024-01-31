<?php
/**
 * HistoricoResultadoFinal Active Record
 * @author  Fred Azv.
 */
class HistoricoResultadoFinal extends TRecord
{
    const TABLENAME = 'historico_resultado_final';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $historico;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('historico_id');
        parent::addAttribute('totais');
        parent::addAttribute('n1_ano');
        parent::addAttribute('n2_ano');
        parent::addAttribute('n3_ano');
        parent::addAttribute('n4_ano');
        parent::addAttribute('n5_ano');
        parent::addAttribute('n6_ano');
        parent::addAttribute('n7_ano');
        parent::addAttribute('n8_ano');
        parent::addAttribute('n9_ano');
        parent::addAttribute('n1_serie');
        parent::addAttribute('n2_serie');
        parent::addAttribute('n3_serie');
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
    


}
