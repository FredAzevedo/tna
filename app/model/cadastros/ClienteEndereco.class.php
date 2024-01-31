<?php

use Adianti\Database\TRecord;

/**
 * ClienteEndereco Active Record
 * @author  Fred Azv.
 */
class ClienteEndereco extends TRecord
{
    const TABLENAME = 'cliente_endereco';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $cliente;
    private $tipo_endereco;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cliente_id');
        parent::addAttribute('tipo_endereco_id');
        parent::addAttribute('cep');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('cidade');
        parent::addAttribute('uf');
        parent::addAttribute('codMuni');
        parent::addAttribute('lat');
        parent::addAttribute('lon');
        parent::addAttribute('horario_permitido');
        parent::addAttribute('regiao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_cliente
     * Sample of usage: $cliente_endereco->cliente = $object;
     * @param $object Instance of Cliente
     */
    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }
    
    /**
     * Method get_cliente
     * Sample of usage: $cliente_endereco->cliente->attribute;
     * @returns Cliente instance
     */
    public function get_cliente()
    {
        // loads the associated object
        if (empty($this->cliente))
            $this->cliente = new Cliente($this->cliente_id);
    
        // returns the associated object
        return $this->cliente;
    }

    public function set_tipo_endereco(TipoEndereco $object)
    {
        $this->tipo_endereco = $object;
        $this->tipo_endereco_id = $object->id;
    }

    public function get_tipo_endereco()
    {
        if (empty($this->tipo_endereco))
            $this->tipo_endereco = new TipoEndereco($this->tipo_endereco_id);

        return $this->tipo_endereco;
    }
    


}
