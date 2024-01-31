<?php
/**
 * HistoricoNotas Active Record
 * @author  Fred Azv
 */
class HistoricoNotas extends TRecord
{
    const TABLENAME = 'historico_notas';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $historico;
    private $disciplina;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('historico_id');
        parent::addAttribute('disciplina_id');
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

    
    /**
     * Method set_historico
     * Sample of usage: $historico_notas->historico = $object;
     * @param $object Instance of Historico
     */
    public function set_historico(Historico $object)
    {
        $this->historico = $object;
        $this->historico_id = $object->id;
    }
    
    /**
     * Method get_historico
     * Sample of usage: $historico_notas->historico->attribute;
     * @returns Historico instance
     */
    public function get_historico()
    {
        // loads the associated object
        if (empty($this->historico))
            $this->historico = new Historico($this->historico_id);
    
        // returns the associated object
        return $this->historico;
    }
    
    
    /**
     * Method set_disciplina
     * Sample of usage: $historico_notas->disciplina = $object;
     * @param $object Instance of Disciplina
     */
    public function set_disciplina(Disciplina $object)
    {
        $this->disciplina = $object;
        $this->disciplina_id = $object->id;
    }
    
    /**
     * Method get_disciplina
     * Sample of usage: $historico_notas->disciplina->attribute;
     * @returns Disciplina instance
     */
    public function get_disciplina()
    {
        // loads the associated object
        if (empty($this->disciplina))
            $this->disciplina = new Disciplina($this->disciplina_id);
    
        // returns the associated object
        return $this->disciplina;
    }
    


}
