<?php
/**
 * Fornecedor Active Record
 * @author  <your-name-here>
 */
class Fornecedor extends TRecord
{
    const TABLENAME = 'fornecedor';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    const CREATEDAT = "created_at";
    const UPDATEDAT = "updated_at";
    const DELETEDAT = "deleted_at";
    
    private $system_unit;
    private $system_user;
    private $comissao_tabela;
    private $email_fornecedors;
    private $telefones_fornecedors;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_fantasia');
        parent::addAttribute('razao_social');
        parent::addAttribute('insc_estadual');
        parent::addAttribute('tipo');
        parent::addAttribute('cpf_cnpj');
        parent::addAttribute('cep');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('bairro');
        parent::addAttribute('complemento');
        parent::addAttribute('cidade');
        parent::addAttribute('uf');
        parent::addAttribute('codMuni');
        parent::addAttribute('site');
        parent::addAttribute('parceria');
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('comissao_tabela_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_system_unit
     * Sample of usage: $fornecedor->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }
    
    /**
     * Method get_system_unit
     * Sample of usage: $fornecedor->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    
    
    /**
     * Method set_system_user
     * Sample of usage: $fornecedor->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $fornecedor->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);
    
        // returns the associated object
        return $this->system_user;
    }
    
    
    /**
     * Method set_comissao_tabela
     * Sample of usage: $fornecedor->comissao_tabela = $object;
     * @param $object Instance of ComissaoTabela
     */
    public function set_comissao_tabela(ComissaoTabela $object)
    {
        $this->comissao_tabela = $object;
        $this->comissao_tabela_id = $object->id;
    }
    
    /**
     * Method get_comissao_tabela
     * Sample of usage: $fornecedor->comissao_tabela->attribute;
     * @returns ComissaoTabela instance
     */
    public function get_comissao_tabela()
    {
        // loads the associated object
        if (empty($this->comissao_tabela))
            $this->comissao_tabela = new ComissaoTabela($this->comissao_tabela_id);
    
        // returns the associated object
        return $this->comissao_tabela;
    }
    
    
    /**
     * Method addEmailFornecedor
     * Add a EmailFornecedor to the Fornecedor
     * @param $object Instance of EmailFornecedor
     */
    public function addEmailFornecedor(EmailFornecedor $object)
    {
        $this->email_fornecedors[] = $object;
    }
    
    /**
     * Method getEmailFornecedors
     * Return the Fornecedor' EmailFornecedor's
     * @return Collection of EmailFornecedor
     */
    public function getEmailFornecedors()
    {
        return $this->email_fornecedors;
    }
    
    /**
     * Method addTelefonesFornecedor
     * Add a TelefonesFornecedor to the Fornecedor
     * @param $object Instance of TelefonesFornecedor
     */
    public function addTelefonesFornecedor(TelefonesFornecedor $object)
    {
        $this->telefones_fornecedors[] = $object;
    }
    
    /**
     * Method getTelefonesFornecedors
     * Return the Fornecedor' TelefonesFornecedor's
     * @return Collection of TelefonesFornecedor
     */
    public function getTelefonesFornecedors()
    {
        return $this->telefones_fornecedors;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->email_fornecedors = array();
        $this->telefones_fornecedors = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        $this->email_fornecedors = parent::loadComposite('EmailFornecedor', 'fornecedor_id', $id);
        $this->telefones_fornecedors = parent::loadComposite('TelefonesFornecedor', 'fornecedor_id', $id);
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        parent::saveComposite('EmailFornecedor', 'fornecedor_id', $this->id, $this->email_fornecedors);
        parent::saveComposite('TelefonesFornecedor', 'fornecedor_id', $this->id, $this->telefones_fornecedors);
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        parent::deleteComposite('EmailFornecedor', 'fornecedor_id', $id);
        parent::deleteComposite('TelefonesFornecedor', 'fornecedor_id', $id);
    
        // delete the object itself
        parent::delete($id);
    }


}
