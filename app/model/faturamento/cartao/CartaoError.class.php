<?php
/**
 * CartaoError Active Record
 * @author  Fred Azv.
 */
class CartaoError extends TRecord
{
    const TABLENAME = 'cartao_error';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $cliente;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('msg');
        parent::addAttribute('codigo');
        parent::addAttribute('cliente_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }

    public function get_cliente()
    {
       
        if (empty($this->cliente))
            $this->cliente = new Cliente($this->cliente_id);

        return $this->cliente;
    }

}
