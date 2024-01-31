<?php
/**
 * CartaoApi Active Record
 * @author  Fred Azv.
 */
class CartaoApi extends TRecord
{
    const TABLENAME = 'cartao_api';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $system_unit;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('valor');
        parent::addAttribute('valor_liquido');
        parent::addAttribute('parcelas');
        parent::addAttribute('data_compra');
        parent::addAttribute('descricao_pagamento');
        parent::addAttribute('pedido_numero');
        parent::addAttribute('tid');
        parent::addAttribute('tid_conciliacao');
        parent::addAttribute('previsao_credito');
        parent::addAttribute('msg');
        parent::addAttribute('bandeira');
        parent::addAttribute('autorizacao');
        parent::addAttribute('cartao_truncado');
        parent::addAttribute('statuscartao');
        parent::addAttribute('status');
        parent::addAttribute('token_cartao');
        parent::addAttribute('data_transacao');
        parent::addAttribute('hora_transacao');
        parent::addAttribute('tarifa');
        parent::addAttribute('taxa');
        parent::addAttribute('msg_erro');
        parent::addAttribute('msg_erro_estorno');
        parent::addAttribute('convenio_proprio');
        parent::addAttribute('autorizada');
        parent::addAttribute('cancelada');
        parent::addAttribute('data_cancelamento');
        parent::addAttribute('motivo_cancelamento');
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
