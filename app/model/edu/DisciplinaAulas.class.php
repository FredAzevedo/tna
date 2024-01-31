<?php
/**
 * DisciplinaAulas Active Record
 * @author  Fred Azv.
 */
class DisciplinaAulas extends TRecord
{
    const TABLENAME = 'disciplina_aulas';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    const CREATEDAT = "created_at";
    const UPDATEDAT = "updated_at";
    const DELETEDAT = "deleted_at";
    
    private $disciplina;
    private $serie;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('disciplina_id');
        parent::addAttribute('serie_id');
        parent::addAttribute('a_1bim');
        parent::addAttribute('a_2bim');
        parent::addAttribute('a_3bim');
        parent::addAttribute('a_4bim');
        parent::addAttribute('ta_anual');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_disciplina
     * Sample of usage: $disciplina_aulas->disciplina = $object;
     * @param $object Instance of Disciplina
     */
    public function set_disciplina(Disciplina $object)
    {
        $this->disciplina = $object;
        $this->disciplina_id = $object->id;
    }
    
    /**
     * Method get_disciplina
     * Sample of usage: $disciplina_aulas->disciplina->attribute;
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
    
    
    /**
     * Method set_serie
     * Sample of usage: $disciplina_aulas->serie = $object;
     * @param $object Instance of Serie
     */
    public function set_serie(Serie $object)
    {
        $this->serie = $object;
        $this->serie_id = $object->id;
    }
    
    /**
     * Method get_serie
     * Sample of usage: $disciplina_aulas->serie->attribute;
     * @returns Serie instance
     */
    public function get_serie()
    {
        // loads the associated object
        if (empty($this->serie))
            $this->serie = new Serie($this->serie_id);
    
        // returns the associated object
        return $this->serie;
    }
    


}
