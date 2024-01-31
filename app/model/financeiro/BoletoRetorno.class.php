<?php
/**
 * BoletoRetorno Active Record
 * @author  <your-name-here>
 */
class BoletoRetorno extends TRecord
{
    const TABLENAME = 'boleto_retorno';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('banco_nome');
        parent::addAttribute('carteira');
        parent::addAttribute('nossoNumero');
        parent::addAttribute('numeroDocumento');
        parent::addAttribute('numeroControle');
        parent::addAttribute('ocorrencia');
        parent::addAttribute('ocorrenciaTipo');
        parent::addAttribute('ocorrenciaDescricao');
        parent::addAttribute('dataOcorrencia');
        parent::addAttribute('dataVencimento');
        parent::addAttribute('dataCredito');
        parent::addAttribute('valor');
        parent::addAttribute('valorTarifa');
        parent::addAttribute('valorIOF');
        parent::addAttribute('valorAbatimento');
        parent::addAttribute('valorDesconto');
        parent::addAttribute('valorRecebido');
        parent::addAttribute('valorMora');
        parent::addAttribute('valorMulta');
        parent::addAttribute('error');
        parent::addAttribute('trash');
    }


}
