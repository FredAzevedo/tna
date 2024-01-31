<?php
/**
 * ContaReceberArquivo Active Record
 * @author  Fred Azv.
 */
class ContaReceberArquivo extends TRecord
{
    const TABLENAME = 'conta_receber_arquivo';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $conta_receber;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('conta_receber_id');
        parent::addAttribute('arquivo');
    }

    
    /**
     * Method set_conta_receber
     * Sample of usage: $conta_receber_arquivo->conta_receber = $object;
     * @param $object Instance of ContaReceber
     */
    public function set_conta_receber(ContaReceber $object)
    {
        $this->conta_receber = $object;
        $this->conta_receber_id = $object->id;
    }
    
    /**
     * Method get_conta_receber
     * Sample of usage: $conta_receber_arquivo->conta_receber->attribute;
     * @returns ContaReceber instance
     */
    public function get_conta_receber()
    {
        // loads the associated object
        if (empty($this->conta_receber))
            $this->conta_receber = new ContaReceber($this->conta_receber_id);
    
        // returns the associated object
        return $this->conta_receber;
    }
    


}
