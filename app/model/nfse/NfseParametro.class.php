<?php
/**
 * NfseParametro Active Record
 * @author  Fred Azv.
 */
class NfseParametro extends TRecord
{
    const TABLENAME = 'nfse_parametro';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';
    
    
    private $system_unit;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('apikey');
        parent::addAttribute('unico_servico');
        parent::addAttribute('nome_servico');
        parent::addAttribute('DeducaoTipo');
        parent::addAttribute('tipo_ambiente');
        parent::addAttribute('DeducaoDescricao');
        parent::addAttribute('EventoTipo');
        parent::addAttribute('EventoDescricao');
        parent::addAttribute('IssAliquota');
        parent::addAttribute('IssExigibilidade');
        parent::addAttribute('IssProcessoSuspensao');
        parent::addAttribute('IssValor');
        parent::addAttribute('IssRetido');
        parent::addAttribute('IssValorRetido');
        parent::addAttribute('RetCofins');
        parent::addAttribute('RetCsll');
        parent::addAttribute('RetInss');
        parent::addAttribute('RetIrrf');
        parent::addAttribute('RetOutrasRetencoes');
        parent::addAttribute('RetPis');
        parent::addAttribute('ServCnae');
        parent::addAttribute('ServCodigo');
        parent::addAttribute('ServCodigoCidadeIncidencia');
        parent::addAttribute('ServCodigoTributacao');
        parent::addAttribute('ServDescricaoCidadeIncidencia');
        parent::addAttribute('ServDiscriminacao');
        parent::addAttribute('ServIdIntegracao');
        parent::addAttribute('unit_id');
        parent::addAttribute('ultimoNumeroNfse');
        parent::addAttribute('ultimoNumeroLote');
        parent::addAttribute('tipoTributacao');
        parent::addAttribute('enviarEmail');
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
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        return $this->system_unit;
    }
    


}
