<?php
/**
 * ApiIntegracao Active Record
 * @author  Fred Azv.
 */
class ApiIntegracao extends TRecord
{
    const TABLENAME = 'api_integracao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('gateway');
        parent::addAttribute('tipo');
        parent::addAttribute('producao');
        parent::addAttribute('url');
        parent::addAttribute('chave');
        parent::addAttribute('credencial');
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
    
    public function get_gatwayNome()
    {

        switch ($this->gatway) {
            case 1:
                return "PJBank";
                break;
            case 2:
                return "PagSeguro";
                break;
            case 3:
                return "Iugu";
                break;
            case 4:
                return "Cloud DFe";
                break;
        }

    }


}
