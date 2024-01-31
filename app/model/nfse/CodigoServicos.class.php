<?php
/**
 * CodigoServicos
 * @author  Fred Azv.
 */
class CodigoServicos extends TRecord
{
    const TABLENAME = 'codigo_servico';
    const PRIMARYKEY= 'codigo';
    const IDPOLICY =  'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


}