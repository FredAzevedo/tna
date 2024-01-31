<?php
/**
 * Transportadora Active Record
 * @author  Fred Azevedo
 */
class Transportadora extends TRecord
{
    const TABLENAME = 'transportadora';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_fantasia');
        parent::addAttribute('razao_social');
        parent::addAttribute('insc_estadual');
        parent::addAttribute('im');
        parent::addAttribute('cnpj');
        parent::addAttribute('cep');
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('bairro');
        parent::addAttribute('complemento');
        parent::addAttribute('cidade');
        parent::addAttribute('uf');
        parent::addAttribute('telefone');
        parent::addAttribute('fax');
        parent::addAttribute('responsavel');
        parent::addAttribute('email');
        parent::addAttribute('site');
    }


}
