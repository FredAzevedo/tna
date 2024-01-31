<?php
/**
 * Apontamento Active Record
 * @author  Fred Azv.
 */
class Apontamento extends TRecord
{
    const TABLENAME = 'apontamento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    const CREATEDAT = "created_at";
    const UPDATEDAT = "updated_at";
    const DELETEDAT = "deleted_at";
    
    private $aluno;
    private $disciplina;
    private $matricula;
    private $serie;
    private $turma;
    private $ano_letivo;
    private $turno;
    private $system_unit;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('aluno_id');
        parent::addAttribute('disciplina_id');
        parent::addAttribute('matricula_id');
        parent::addAttribute('serie_id');
        parent::addAttribute('turma_id');
        parent::addAttribute('turno_id');
        parent::addAttribute('anoletivo_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('a_1bim');
        parent::addAttribute('a_2bim');
        parent::addAttribute('a_3bim');
        parent::addAttribute('a_4bim');
        parent::addAttribute('ta_anual');
        parent::addAttribute('f_1bim');
        parent::addAttribute('f_2bim');
        parent::addAttribute('f_3bim');
        parent::addAttribute('f_4bim');
        parent::addAttribute('tf_anual');
        parent::addAttribute('p_1bim');
        parent::addAttribute('p_2bim');
        parent::addAttribute('p_3bim');
        parent::addAttribute('p_4bim');
        parent::addAttribute('tp_anual');
        parent::addAttribute('ft_1bim');
        parent::addAttribute('ft_2bim');
        parent::addAttribute('ft_3bim');
        parent::addAttribute('ft_4bim');
        parent::addAttribute('ft_anual');

        parent::addAttribute('n_1bim');
        parent::addAttribute('n_2bim');
        parent::addAttribute('n_3bim');
        parent::addAttribute('n_4bim');
        parent::addAttribute('MS1');
        parent::addAttribute('MS2');
        parent::addAttribute('MDS1');
        parent::addAttribute('MDS2');
        parent::addAttribute('REC12');
        parent::addAttribute('REC34');
        parent::addAttribute('MA');
        parent::addAttribute('PF');
        parent::addAttribute('MFA');
        parent::addAttribute('resultado');
        
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
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
    
   
    public function set_disciplina(Disciplina $object)
    {
        $this->disciplina = $object;
        $this->disciplina_id = $object->id;
    }
    
    public function get_disciplina()
    {
     
        if (empty($this->disciplina))
            $this->disciplina = new Disciplina($this->disciplina_id);

        return $this->disciplina;
    }

    public function set_matricula(Matricula $object)
    {
        $this->matricula = $object;
        $this->matricula_id = $object->id;
    }

    public function get_matricula()
    {
        
        if (empty($this->matricula))
            $this->matricula = new Matricula($this->matricula_id);

        return $this->matricula;
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

    public function set_ano_letivo(AnoLetivo $object)
    {
        $this->ano_letivo = $object;
        $this->anoletivo_id = $object->id;
    }

    public function get_ano_letivo()
    {
      
        if (empty($this->ano_letivo))
            $this->ano_letivo = new AnoLetivo($this->anoletivo_id);

        return $this->ano_letivo;
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

    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }

    public function get_system_unit()
    {

        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->unit_id);

        return $this->system_unit;
    }
    


}
