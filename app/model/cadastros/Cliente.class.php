<?php

use Adianti\Database\TRecord;

/**
 * Cliente Active Record
 * @author  Fred Azevedo
 */
class Cliente extends TRecord
{
    const TABLENAME = 'cliente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    const CREATEDAT = "created_at";
    const UPDATEDAT = "updated_at";
    const DELETEDAT = "deleted_at";
    
    use SystemChangeLogTrait;
    
    private $system_unit;
    private $system_user;
    private $fornecedor;
    private $tabela_precos_cab;
    private $email_clientes;
    private $telefones_clientes;
    private $enderecos_cliente;

    private $cartao_cliente;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ativo');
        parent::addAttribute('tipo');
        parent::addAttribute('cliente_grupo_id');
        parent::addAttribute('cliente_origem_id');
        parent::addAttribute('razao_social');
        parent::addAttribute('nome_fantasia');
        parent::addAttribute('cpf_cnpj');
        parent::addAttribute('rg_ie');
        parent::addAttribute('im');
        parent::addAttribute('nascimento');
        parent::addAttribute('sexo');
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('representante_nome');
        parent::addAttribute('representante_nacionalidade');
        parent::addAttribute('representante_profissao');
        parent::addAttribute('representante_estado_civil');
        parent::addAttribute('representante_rg');
        parent::addAttribute('representante_cpf');
        parent::addAttribute('representante_cep');
        parent::addAttribute('representante_logradouro');
        parent::addAttribute('representante_bairro');
        parent::addAttribute('representante_numero');
        parent::addAttribute('representante_complemento');
        parent::addAttribute('representante_cidade');
        parent::addAttribute('representante_uf');
	    parent::addAttribute('juridico');
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
    
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    public function get_system_user()
    {
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);
    
        return $this->system_user;
    }


}
