<?php
/**
 * AlunoTelefone Active Record
 * @author  Fred Azv.
 */
class AlunoTelefone extends TRecord
{
    const TABLENAME = 'aluno_telefone';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $aluno;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('aluno_id');
        parent::addAttribute('responsavel');
        parent::addAttribute('telefone');
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
    


}
