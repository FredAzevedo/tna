<?php
/**
 * Aluno Active Record
 * @author  Fred Azv.
 */
class Aluno extends TRecord
{
    const TABLENAME = 'aluno';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    const CREATEDAT = "created_at";
    const UPDATEDAT = "updated_at";
    const DELETEDAT = "deleted_at";
    
    private $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('nome');
        parent::addAttribute('cpf');
        parent::addAttribute('rg');
        parent::addAttribute('orgao_emissor');
        parent::addAttribute('nascimento');
        parent::addAttribute('cidade_nascimento');
        parent::addAttribute('uf_nascimento');
        parent::addAttribute('sexo');
        parent::addAttribute('cep');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('bairro');
        parent::addAttribute('cidade');
        parent::addAttribute('uf');
        parent::addAttribute('complemento');
        parent::addAttribute('folha');
        parent::addAttribute('livro');
        parent::addAttribute('numero_registro');
        parent::addAttribute('cartorio_nome');
        parent::addAttribute('cartorio_municipio');
        parent::addAttribute('cartorio_uf');
        parent::addAttribute('observacao');
        parent::addAttribute('foto');
        parent::addAttribute('instagram');
        parent::addAttribute('facebook');
        parent::addAttribute('tweeter');
        parent::addAttribute('email');
        parent::addAttribute('tipo_sanguineo');
        parent::addAttribute('telefone');
        parent::addAttribute('mae_responsavel_id');
        parent::addAttribute('pai_responsavel_id');
        parent::addAttribute('contrato_responsavel_id');
        parent::addAttribute('data_registro');
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
            $this->system_unit = new SystemUnit($this->unit_id);

        return $this->system_unit;
    }
    


}
