<?php
/**
 * ContaPagarArquivo Active Record
 * @author  Fred Azv.
 */
class ContaPagarArquivo extends TRecord
{
    const TABLENAME = 'conta_pagar_arquivo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $conta_pagar;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('conta_pagar_id');
        parent::addAttribute('arquivo');
    }

    
    /**
     * Method set_conta_pagar
     * Sample of usage: $conta_pagar_arquivo->conta_pagar = $object;
     * @param $object Instance of ContaPagar
     */
    public function set_conta_pagar(ContaPagar $object)
    {
        $this->conta_pagar = $object;
        $this->conta_pagar_id = $object->id;
    }
    
    /**
     * Method get_conta_pagar
     * Sample of usage: $conta_pagar_arquivo->conta_pagar->attribute;
     * @returns ContaPagar instance
     */
    public function get_conta_pagar()
    {
        // loads the associated object
        if (empty($this->conta_pagar))
            $this->conta_pagar = new ContaPagar($this->conta_pagar_id);
    
        // returns the associated object
        return $this->conta_pagar;
    }
    


}
