<?php
/**
 * EstoqueControlmovImobilizado Active Record
 * @author  Fred Azv
 */
class EstoqueControlmovImobilizado extends TRecord
{
    const TABLENAME = 'estoque_controlmov_imobilizado';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $system_user;
    private $produto;


    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('local');
        parent::addAttribute('alocado');
        parent::addAttribute('user_id');
        parent::addAttribute('estado');
        parent::addAttribute('validade_mes');
        parent::addAttribute('data_avaliacao');
        parent::addAttribute('valor_justo');
        parent::addAttribute('emplacamento');
        parent::addAttribute('tipo');
        parent::addAttribute('quantidade');
        parent::addAttribute('saldo');
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
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    
    
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
 
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->user_id);
    
        // returns the associated object
        return $this->system_user;
    }
    
    

    public function set_produto(Produto $object)
    {
        $this->produto = $object;
        $this->produto_id = $object->id;
    }
    

    public function get_produto()
    {
        // loads the associated object
        if (empty($this->produto))
            $this->produto = new Produto($this->produto_id);
    
        // returns the associated object
        return $this->produto;
    }
    
    public function get_tipoEstado() //Associação
    {
        if($this->estado == 1){
            return 'Novo';
        }elseif ($this->estado == 2) {
            return 'Usado';
        } else {
            return 'Depreciado';
        }
        
        
    }

    public function set_localidade(Viewlocal $object)
    {
        $this->localidade = $object;
        $this->localidade_local = $object->id;
    }

    public function get_localidade()
    {
        // loads the associated object
        if (empty($this->localidade))
            $this->localidade = new Viewlocal($this->local);
    
        // returns the associated object
        return $this->localidade;//nome_fantasia
    }

}
