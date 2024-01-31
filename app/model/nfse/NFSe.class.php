<?php
/**
 * Nfse Active Record
 * @author  Fred Azv.
 */
class NFSe extends TRecord
{
    const TABLENAME = 'nfse';
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
        parent::addAttribute('serie');
        parent::addAttribute('tipoRPS');
        parent::addAttribute('statusRPS');
        parent::addAttribute('natureza_operacao');
        parent::addAttribute('regime_tributacao');
        parent::addAttribute('conta_receber_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('lote');
        parent::addAttribute('numeroNfse');
        parent::addAttribute('enviarEmail');
        parent::addAttribute('dataEmissao');
        parent::addAttribute('competencia');
        parent::addAttribute('regime_tributacao');
        parent::addAttribute('incentivador_cultural');
        parent::addAttribute('incentivo_fiscal');
        parent::addAttribute('substituicao');
        parent::addAttribute('TcpfCnpj');
        parent::addAttribute('TrazaoSocial');
        parent::addAttribute('Temail');
        parent::addAttribute('Tlogradouro');
        parent::addAttribute('Tnumero');
        parent::addAttribute('Tbairro');
        parent::addAttribute('Tcidade');
        parent::addAttribute('Tuf');
        parent::addAttribute('Tcomplemento');
        parent::addAttribute('TcodigoCidade');
        parent::addAttribute('Tcep');
        parent::addAttribute('Ttelefone');
        parent::addAttribute('Scodigo');
        parent::addAttribute('Sdiscriminacao');
        parent::addAttribute('Scnae');
        parent::addAttribute('ISSaliquota');
        parent::addAttribute('ISStipoTributacao');
        parent::addAttribute('ISSretido');
        parent::addAttribute('ISSvalor');
        parent::addAttribute('ISSexigibilidade');
        parent::addAttribute('ISSProcessoSuspencao');
        parent::addAttribute('status');
        parent::addAttribute('statusCode');
        parent::addAttribute('protocolo');
        parent::addAttribute('protocolo_cancelamento');
        parent::addAttribute('pdf');
        parent::addAttribute('xml');
        parent::addAttribute('tipo');
        parent::addAttribute('id_retorno');
        parent::addAttribute('ServCodigo');
        parent::addAttribute('ServDescricao');
        parent::addAttribute('RetCofins');
        parent::addAttribute('RetCsll');
        parent::addAttribute('RetInss');
        parent::addAttribute('RetIrrf');
        parent::addAttribute('RetPis');
        parent::addAttribute('RetOutros');
        parent::addAttribute('vRetCofins');
        parent::addAttribute('vRetCsll');
        parent::addAttribute('vRetInss');
        parent::addAttribute('vRetIrrf');
        parent::addAttribute('vRetPis');
        parent::addAttribute('EventoTipo');
        parent::addAttribute('EventoDescricao');
        parent::addAttribute('total_servico');
        parent::addAttribute('deducoes');
        parent::addAttribute('base_calculo');
        parent::addAttribute('observacao');
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
            $this->system_unit = new SystemUnit($this->unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }
    


}
