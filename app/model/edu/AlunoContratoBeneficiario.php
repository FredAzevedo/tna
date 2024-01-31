<?php
/**
 * AlunoContratoBeneficiario Active Record
 * @author  <your-name-here>
 */
class AlunoContratoBeneficiario extends TRecord
{
    const TABLENAME = 'aluno_contrato_beneficiario';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $aluno;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('aluno_id');
        parent::addAttribute('aluno_contrato_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_aluno
     * Sample of usage: $aluno_contrato_beneficiario->aluno = $object;
     * @param $object Instance of Aluno
     */
    public function set_aluno(Aluno $object)
    {
        $this->aluno = $object;
        $this->aluno_id = $object->id;
    }
    
    /**
     * Method get_aluno
     * Sample of usage: $aluno_contrato_beneficiario->aluno->attribute;
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
    


}
