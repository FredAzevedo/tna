<?php
/**
 * BoletoRegra Active Record
 * @author  Fred Azv.
 */
class BoletoRegra extends TRecord
{
    const TABLENAME = 'boleto_regra';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $conta_bancaria;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('valor_juros');
        parent::addAttribute('valor_multa');
        parent::addAttribute('conta_bancaria_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


    public function set_conta_bancaria(ContaBancaria $object)
    {
        $this->conta_bancaria = $object;
        $this->conta_bancaria_id = $object->id;
    }

    public function get_conta_bancaria()
    {
        // loads the associated object
        if (empty($this->conta_bancaria))
            $this->conta_bancaria = new ContaBancaria($this->conta_bancaria_id);
    
        // returns the associated object
        return $this->conta_bancaria;
    }
    


}
