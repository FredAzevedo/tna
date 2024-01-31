<?php
/**
 * AlunoResponsavel Active Record
 * @author  Fred Azevedo
 */
class AlunoResponsavel extends TRecord
{
    const TABLENAME = 'aluno_responsavel';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $aluno;
    private $responsavel;

    const CREATEDAT = "created_at";
    const UPDATEDAT = "updated_at";
    const DELETEDAT = "deleted_at";
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('aluno_id');
        parent::addAttribute('responsavel_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_aluno
     * Sample of usage: $aluno_responsavel->aluno = $object;
     * @param $object Instance of Aluno
     */
    public function set_aluno(Aluno $object)
    {
        $this->aluno = $object;
        $this->aluno_id = $object->id;
    }
    
    /**
     * Method get_aluno
     * Sample of usage: $aluno_responsavel->aluno->attribute;
     * @returns Aluno instance
     */
    public function get_aluno()
    {
        // loads the associated object
        if (empty($this->aluno))
            $this->aluno = new Aluno($this->aluno_id);
    
        // returns the associated object
        return $this->aluno;
    }
    
    
    /**
     * Method set_responsavel
     * Sample of usage: $aluno_responsavel->responsavel = $object;
     * @param $object Instance of Responsavel
     */
    public function set_responsavel(Responsavel $object)
    {
        $this->responsavel = $object;
        $this->responsavel_id = $object->id;
    }
    
    /**
     * Method get_responsavel
     * Sample of usage: $aluno_responsavel->responsavel->attribute;
     * @returns Responsavel instance
     */
    public function get_responsavel()
    {
        // loads the associated object
        if (empty($this->responsavel))
            $this->responsavel = new Responsavel($this->responsavel_id);
    
        // returns the associated object
        return $this->responsavel;
    }
    


}
