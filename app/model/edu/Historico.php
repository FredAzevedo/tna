<?php
/**
 * Historico Active Record
 * @author  Fred Azv
 */
class Historico extends TRecord
{
    const TABLENAME = 'historico';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    const CREATEDAT = "created_at";
    const UPDATEDAT = "updated_at";
    const DELETEDAT = "deleted_at";
    
    private $aluno;
    private $serie;
    private $ano_letivo;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('aluno_id');
        parent::addAttribute('observacao');
        parent::addAttribute('serie_id');
        parent::addAttribute('ano_letivo_id');
        parent::addAttribute('situacao');
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
        // loads the associated object
        if (empty($this->aluno))
            $this->aluno = new Aluno($this->aluno_id);
    
        // returns the associated object
        return $this->aluno;
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
    
    
    public function set_ano_letivo(AnoLetivo $object)
    {
        $this->ano_letivo = $object;
        $this->ano_letivo_id = $object->id;
    }
    
    public function get_ano_letivo()
    {
        // loads the associated object
        if (empty($this->ano_letivo))
            $this->ano_letivo = new AnoLetivo($this->ano_letivo_id);
    
        // returns the associated object
        return $this->ano_letivo;
    }
    


}
