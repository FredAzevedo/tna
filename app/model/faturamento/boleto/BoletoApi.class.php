<?php
/**
 * BoletoApi Active Record
 * @author  Fred Azv.
 */
class BoletoApi extends TRecord
{
    const TABLENAME = 'boleto_api';
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
        parent::addAttribute('user_id');
        parent::addAttribute('vencimento');
        parent::addAttribute('valor');
        parent::addAttribute('valor_pago');
        parent::addAttribute('valor_tarifa');
        parent::addAttribute('valor_liquido');
        parent::addAttribute('data_pagamento');
        parent::addAttribute('data_credito');
        parent::addAttribute('juros');
        parent::addAttribute('multa');
        parent::addAttribute('desconto');
        parent::addAttribute('cliente_id');
        parent::addAttribute('nome_cliente');
        parent::addAttribute('cpf_cliente');
        parent::addAttribute('endereco_cliente');
        parent::addAttribute('numero_cliente');
        parent::addAttribute('complemento_cliente');
        parent::addAttribute('bairro_cliente');
        parent::addAttribute('cidade_cliente');
        parent::addAttribute('estado_cliente');
        parent::addAttribute('cep_cliente');
        parent::addAttribute('email_cliente');
        parent::addAttribute('telefone_cliente');
        parent::addAttribute('texto');
        parent::addAttribute('grupo');
        parent::addAttribute('pedido_numero');
        parent::addAttribute('juros_fixo');
        parent::addAttribute('multa_fixo');
        parent::addAttribute('diasdesconto1');
        parent::addAttribute('desconto2');
        parent::addAttribute('diasdesconto2');
        parent::addAttribute('desconto3');
        parent::addAttribute('diasdesconto3');
        parent::addAttribute('nunca_atualizar_boleto');
        parent::addAttribute('instrucao_adicional');
        parent::addAttribute('especie_documento');
        parent::addAttribute('status');
        parent::addAttribute('msg');
        parent::addAttribute('nossonumero');
        parent::addAttribute('id_unico');
        parent::addAttribute('banco_numero');
        parent::addAttribute('token_facilitador');
        parent::addAttribute('credencial');
        parent::addAttribute('linkBoleto');
        parent::addAttribute('linkGrupo');
        parent::addAttribute('linhaDigitavel');
        parent::addAttribute('registro_sistema_bancario');
        parent::addAttribute('registro_rejeicao_motivo');
        parent::addAttribute('contrato');
        parent::addAttribute('formato');
        parent::addAttribute('pago');
        parent::addAttribute('contas_receber_id');
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
