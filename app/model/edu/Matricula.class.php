<?php
/**
 * Matricula Active Record
 * @author  Fred Azv
 */
class Matricula extends TRecord
{
    const TABLENAME = 'matricula';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    const CREATEDAT = "created_at";
    const UPDATEDAT = "updated_at";
    const DELETEDAT = "deleted_at";
    
    private $system_unit;
    private $aluno;
    private $serie;
    private $turma;
    private $turno;
    private $ano_letivo;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('serie_id');
        parent::addAttribute('turma_id');
        parent::addAttribute('turno_id');
        parent::addAttribute('aluno_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('anoletivo_id');
        parent::addAttribute('referencia');
        parent::addAttribute('status');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    public function get_system_unit()
    {
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        return $this->system_unit;
    }
    
    public function set_aluno(Aluno $object)
    {
        $this->aluno = $object;
        $this->aluno_id = $object->id;
    }
    
    public function get_aluno()
    {
        if (empty($this->aluno))
            $this->aluno = new Aluno($this->aluno_id);
    
        return $this->aluno;
    }
    
    
    public function set_serie(Serie $object)
    {
        $this->serie = $object;
        $this->serie_id = $object->id;
    }
    
    public function get_serie()
    {
        if (empty($this->serie))
            $this->serie = new Serie($this->serie_id);
    
        return $this->serie;
    }
    
    public function set_turma(Turma $object)
    {
        $this->turma = $object;
        $this->turma_id = $object->id;
    }
    
    public function get_turma()
    {
        if (empty($this->turma))
            $this->turma = new Turma($this->turma_id);
    
        return $this->turma;
    }
    
    public function set_turno(Turno $object)
    {
        $this->turno = $object;
        $this->turno_id = $object->id;
    }
    
    public function get_turno()
    {
        if (empty($this->turno))
            $this->turno = new Turno($this->turno_id);

        return $this->turno;
    }
    
    public function set_ano_letivo(AnoLetivo $object)
    {
        $this->ano_letivo = $object;
        $this->ano_letivo_id = $object->id;
    }
    
    public function get_ano_letivo()
    {

        if (empty($this->ano_letivo))
            $this->ano_letivo = new AnoLetivo($this->ano_letivo_id);

        return $this->ano_letivo;
    }
    


}
