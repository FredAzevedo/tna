<?php
/**
 * Estoquemov Active Record
 * @author  Frez Azv.
 */
class Estoquemov extends TRecord
{
    const TABLENAME = 'estoquemov';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;
    private $produto;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('local');
        parent::addAttribute('tipo');
        parent::addAttribute('quantidade');
        parent::addAttribute('saldo');
        parent::addAttribute('valor');
        parent::addAttribute('referencia');
        parent::addAttribute('controla_lote');
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
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    
    public function set_produto(Produto $object)
    {
        $this->produto = $object;
        $this->produto_id = $object->id;
    }
    

    public function get_produto()
    {
        if (empty($this->produto))
            $this->produto = new Produto($this->produto_id);
    
        return $this->produto;
    }

    public function set_viewlocal(Viewlocal $object)
    {
        $this->viewlocal = $object;
        $this->viewlocal_id = $object->id;
    }
    
    public function get_viewlocal()
    {
        if (empty($this->viewlocal))
            $this->viewlocal = new Viewlocal($this->viewlocal_id);
    
        return $this->viewlocal;
    }

    public function get_tipomov() //Associação
    {
        if($this->tipo == "E"){
            return 'Entrada';
        }
        return 'Saída';
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
