<?php
/**
 * ComissaoTabela Active Record
 * @author  Fred Azevedo
 */
class ComissaoTabela extends TRecord
{
    const TABLENAME = 'comissao_tabela';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('forma_comissao');
        parent::addAttribute('valor_comissao');
        parent::addAttribute('observacao');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
    }

    public function get_valorComissao() //Associação
    {
        if($this->forma_comissao == "D"){
            return '(R$) Dinheiro';
        }
        return '(%) Porcentagem';
    }

}
