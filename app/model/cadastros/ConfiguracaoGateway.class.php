<?php
/**
 * ConfiguracaoGateway Active Record
 * @author  Joao Victor Marques de Oliveira - jvomarques@gmail.com
 */
class ConfiguracaoGateway extends TRecord
{
    const TABLENAME = 'configuracao_gateway';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome_gateway');
        parent::addAttribute('token');
        parent::addAttribute('email');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }
}
