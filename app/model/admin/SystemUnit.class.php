<?php

class SystemUnit extends TRecord
{
    const TABLENAME = 'system_unit';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    use SystemChangeLogTrait;
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('matriz_filial');
        parent::addAttribute('razao_social');
        parent::addAttribute('nome_fantasia');
        parent::addAttribute('unidade');
        parent::addAttribute('responsavel');
        parent::addAttribute('cnpj');
        parent::addAttribute('insc_estadual');
        parent::addAttribute('insc_municipal');
        parent::addAttribute('cnae');
        parent::addAttribute('crt');
        parent::addAttribute('atividade');
        parent::addAttribute('regime');
        parent::addAttribute('junta_comercial');
        parent::addAttribute('ativo');
        parent::addAttribute('porte');
        parent::addAttribute('cep');    
        parent::addAttribute('logradouro');
        parent::addAttribute('numero');
        parent::addAttribute('bairro');
        parent::addAttribute('complemento');
        parent::addAttribute('cidade');
        parent::addAttribute('uf');
        parent::addAttribute('codMuni');
        parent::addAttribute('email');
        parent::addAttribute('telefone');
        parent::addAttribute('telefone_sped');
        parent::addAttribute('site');
        parent::addAttribute('cont_limite');
        parent::addAttribute('limite');
        parent::addAttribute('certificado');
        parent::addAttribute('data_vencimento_certificado');
        parent::addAttribute('contabilista_nome');
        parent::addAttribute('contabilista_cpf');
        parent::addAttribute('contabilista_crc');
        parent::addAttribute('contabilista_cnpj');
        parent::addAttribute('contabilista_cep');
        parent::addAttribute('contabilista_end');
        parent::addAttribute('contabilista_num');
        parent::addAttribute('contabilista_compl');
        parent::addAttribute('contabilista_bairro');
        parent::addAttribute('contabilista_fone');
        parent::addAttribute('contabilista_email');
        parent::addAttribute('connection_name');
        parent::addAttribute('custom_code');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    /* Telefones */
    public function addTelefonesUnidade(TelefonesUnidade $object)
    {
        return $this->telefones[] = $object;
    }

    public function getTelefonesUnidade()
    {
        return $this->telefones;
    }
    /* Telefones */
    
    /* Emails */
    public function addEmailsUnidade(EmailUnidade $object)
    {
        return $this->emails[] = $object;
    }

    public function getEmailsUnidade()
    {
        return $this->emails;
    }
    /* Emails */

    public function load( $id )
    {   
        $repository = new TRepository('TelefonesUnidade');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('unidades_id', '=', $id));
        $this->telefones = $repository->load($criteria);

        
        $repository_email = new TRepository('EmailUnidade');
        $criteria_email = new TCriteria;
        $criteria_email->add(new TFilter('unidades_id', '=', $id));
        $this->emails = $repository_email->load($criteria_email);

        //parent::loadComposite('TelefonesUnidade', 'unidades_id', $id);
        return parent::load( $id );
    }

    //Atualizando metodo store da Unidade para que ele possa atualizar os telefones
    public function store(){

        parent::store();
        parent::saveComposite('TelefonesUnidade', 'unidades_id', $this->id, $this->telefones);
        parent::saveComposite('EmailUnidade', 'unidades_id', $this->id, $this->emails);
        
    }

    public function delete($id = NULL){

        if(isset($id))
            $id = $id;
        else
            $id = $this->id;

        parent::deleteComposite('TelefonesUnidade', 'unidades_id', $id);
        parent::deleteComposite('EmailUnidade', 'unidades_id', $id);

        parent::delete($id);
    }
}

