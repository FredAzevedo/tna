<?php

use Adianti\Database\TRecord;

/**
 * ClienteContrato Active Record
 * @author  <your-name-here>
 */
class ClienteContrato extends TRecord
{
    const TABLENAME = 'cliente_contrato';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $cliente;
    private $tipo_endereco;
    private $relatorio_customizado;
    private $_plano;

    /**
     * Constructor method
     * @throws Exception
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cliente_id');
        parent::addAttribute('tipo_endereco_id');
        parent::addAttribute('relatorio_customizado_id');
        parent::addAttribute('tipo_forma_pgto_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('user_id');
        parent::addAttribute('conta_bancaria_id');
        parent::addAttribute('conta_bancaria_entrada_id');
        parent::addAttribute('tipo_pgto_id');
        parent::addAttribute('inicio_vigencia');
        parent::addAttribute('fim_vigencia');
        parent::addAttribute('plano_id');
        parent::addAttribute('entrada');
        parent::addAttribute('desconto');
        parent::addAttribute('total');
        parent::addAttribute('parcela');
        parent::addAttribute('qtd_parcelas');
        parent::addAttribute('valor_parcelado');
        parent::addAttribute('vencimento_primeira_parcela');
        parent::addAttribute('valor');
        parent::addAttribute('status');
        parent::addAttribute('carne_path_pdf');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    /**
     * Method set_cliente
     * Sample of usage: $cliente_contrato->cliente = $object;
     * @param $object Instance of Cliente
     */
    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }
    
    /**
     * Method get_cliente
     * Sample of usage: $cliente_contrato->cliente->attribute;
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
    
    
    /**
     * Method set_tipo_endereco
     * Sample of usage: $cliente_contrato->tipo_endereco = $object;
     * @param $object Instance of TipoEndereco
     */
    public function set_tipo_endereco(TipoEndereco $object)
    {
        $this->tipo_endereco = $object;
        $this->tipo_endereco_id = $object->id;
    }
    
    /**
     * Method get_tipo_endereco
     * Sample of usage: $cliente_contrato->tipo_endereco->attribute;
     * @returns TipoEndereco instance
     */
    public function get_tipo_endereco()
    {
        // loads the associated object
        if (empty($this->tipo_endereco))
            $this->tipo_endereco = new TipoEndereco($this->tipo_endereco_id);
    
        // returns the associated object
        return $this->tipo_endereco;
    }
    
    
    /**
     * Method set_relatorio_customizado
     * Sample of usage: $cliente_contrato->relatorio_customizado = $object;
     * @param $object Instance of RelatorioCustomizado
     */
    public function set_relatorio_customizado(RelatorioCustomizado $object)
    {
        $this->relatorio_customizado = $object;
        $this->relatorio_customizado_id = $object->id;
    }
    
    /**
     * Method get_relatorio_customizado
     * Sample of usage: $cliente_contrato->relatorio_customizado->attribute;
     * @returns RelatorioCustomizado instance
     */
    public function get_relatorio_customizado()
    {
        // loads the associated object
        if (empty($this->relatorio_customizado))
            $this->relatorio_customizado = new RelatorioCustomizado($this->relatorio_customizado_id);
    
        // returns the associated object
        return $this->relatorio_customizado;
    }

    public function set_TipoFormaPgto(TipoFormaPgto $object)
    {
        $this->TipoFormaPgto = $object;
        $this->os_id = $object->id;
    }
    
    public function get_TipoFormaPgto()
    {
        // loads the associated object
        if (empty($this->TipoFormaPgto))
            $this->TipoFormaPgto = new TipoFormaPgto($this->tipo_forma_pgto_id);
    
        // returns the associated object
        return $this->TipoFormaPgto;
    }


    public function set_Plano(Plano $object)
    {
        $this->_plano = $object;
        $this->plano_id = $object->id;
    }
    
    public function get_Plano()
    {
        // loads the associated object
        if (empty($this->_plano))
            $this->_plano = new Plano($this->plano_id);
    
        // returns the associated object
        return $this->_plano;
    }
    
    public function get_statusContrato() //Associação
    {
        if($this->status == "A"){
            return 'Em aberto';
        }
        return 'Finalizado';
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
