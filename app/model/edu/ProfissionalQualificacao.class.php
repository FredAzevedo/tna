<?php
/**
 * ProfissionalQualificacao Active Record
 * @author  Fred Azv
 */
class ProfissionalQualificacao extends TRecord
{
    const TABLENAME = 'profissional_qualificacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max';

    const CREATEDAT = "created_at";
    const UPDATEDAT = "updated_at";
    const DELETEDAT = "deleted_at";
    
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('instituicao_ensino');
        parent::addAttribute('instituicao_ensino_uf');
        parent::addAttribute('data_conclusao_curso');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }


}
