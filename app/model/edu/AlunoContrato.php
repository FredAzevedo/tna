<?php
/**
 * AlunoContrato Active Record
 * @author  Fred Azv.
 */
class AlunoContrato extends TRecord
{
    const TABLENAME = 'aluno_contrato';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $primeiro_responsavel;
    private $segundo_responsavel;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('primeiro_responsavel_id');
        parent::addAttribute('segundo_responsavel_id');
        parent::addAttribute('tipo_pgto_id');
        parent::addAttribute('ano_letivo');
        parent::addAttribute('prazo_meses');
        parent::addAttribute('prazo_inicio');
        parent::addAttribute('prazo_fim');
        parent::addAttribute('preco_valor_integral');
        parent::addAttribute('preco_parcelas');
        parent::addAttribute('preco_parcela_valor'); //valor da parcela  integral
        parent::addAttribute('preco_desconto');
        parent::addAttribute('preco_parcela_valor_desconto'); //valor da parcela  com desconto
        parent::addAttribute('preco_valor_total');
        parent::addAttribute('vencimento_parcela');
        parent::addAttribute('financeiro');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    
    public function set_primeiro_responsavel(Cliente $object)
    {
        $this->responsavel = $object;
        $this->primeiro_responsavel_id = $object->id;
    }

    public function get_primeiro_responsavel()
    {
        if (empty($this->primeiro_responsavel))
            $this->primeiro_responsavel = new Cliente($this->primeiro_responsavel_id);

        return $this->primeiro_responsavel;
    }

    public function set_segundo_responsavel(Cliente $object)
    {
        $this->responsavel = $object;
        $this->segundo_responsavel_id = $object->id;
    }

    public function get_segundo_responsavel()
    {
        if (empty($this->segundo_responsavel))
            $this->segundo_responsavel = new Cliente($this->segundo_responsavel_id);

        return $this->segundo_responsavel;
    }
    


}
