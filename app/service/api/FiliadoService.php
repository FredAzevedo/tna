<?php
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\filiado\TTransaction;
use Adianti\Route\TRoute;
use Adianti\Widget\Form\TEntry;

class FiliadoService extends TService
{
    public function __construct()
    {
        parent::__construct('filiado');
    }

    public function create($data)
    {
        $filiado = new Filiado;
        $filiado->situacao_id           = $data['situacao_id'];
        $filiado->status_id             = $data['status_id'];
        $filiado->nome                  = $data['nome'];
        $filiado->cpf_cnpj              = $data['cpf_cnpj'];
        $filiado->rg                    = $data['rg'];
        $filiado->naturalidade          = $data['naturalidade'];
        $filiado->nacionalidade         = $data['nacionalidade'];
        $filiado->estado_civil          = $data['estado_civil'];
        $filiado->sexo                  = $data['sexo'];
        $filiado->cep                   = $data['cep'];
        $filiado->logradouro            = $data['logradouro'];
        $filiado->numero                = $data['numero'];
        $filiado->bairro                = $data['bairro'];
        $filiado->complemento           = $data['complemento'];
        $filiado->cidade                = $data['cidade'];
        $filiado->uf                    = $data['uf'];
        $filiado->data_cadastro         = $data['data_cadastro'];
        $filiado->nascimento            = $data['nascimento'];
        $filiado->nome_pai              = $data['nome_pai'];
        $filiado->nome_mae              = $data['nome_mae'];
        $filiado->foto                  = $data['foto'];
        $filiado->escolaridade          = $data['escolaridade'];
        $filiado->data_filiacao         = $data['data_filiacao'];
        $filiado->observacao            = $data['observacao'];
        $filiado->siape                 = $data['siape'];
        $filiado->profissao_id          = $data['profissao_id'];
        $filiado->unit_id               = $data['unit_id'];
        $filiado->margem                = $data['margem'];
        $filiado->terceirizado_id       = $data['terceirizado_id'];
        $filiado->repasse               = $data['repasse'];
        $filiado->roll_id               = $data['roll_id'];
        $filiado->matricula             = $data['matricula'];
        $filiado->local_trabalho_id     = $data['local_trabalho_id'];
        $filiado->data_admissao         = $data['data_admissao'];
        $filiado->desconto              = $data['desconto'];
        $filiado->cargo_id              = $data['cargo_id'];
        $filiado->tipo_matricula_id     = $data['tipo_matricula_id'];
        $filiado->regional_id           = $data['regional_id'];
        $filiado->store();
        return $filiado;
    }

    public function retrieve($id)
    {
        $filiado = new Filiado($id);
        return $filiado;
    }

    public function update($id, $data)
    {
        $filiado = new Filiado($id);
        $filiado->situacao_id           = $data['situacao_id'];
        $filiado->status_id             = $data['status_id'];
        $filiado->nome                  = $data['nome'];
        $filiado->cpf_cnpj              = $data['cpf_cnpj'];
        $filiado->rg                    = $data['rg'];
        $filiado->naturalidade          = $data['naturalidade'];
        $filiado->nacionalidade         = $data['nacionalidade'];
        $filiado->estado_civil          = $data['estado_civil'];
        $filiado->sexo                  = $data['sexo'];
        $filiado->cep                   = $data['cep'];
        $filiado->logradouro            = $data['logradouro'];
        $filiado->numero                = $data['numero'];
        $filiado->bairro                = $data['bairro'];
        $filiado->complemento           = $data['complemento'];
        $filiado->cidade                = $data['cidade'];
        $filiado->uf                    = $data['uf'];
        $filiado->data_cadastro         = $data['data_cadastro'];
        $filiado->nascimento            = $data['nascimento'];
        $filiado->nome_pai              = $data['nome_pai'];
        $filiado->nome_mae              = $data['nome_mae'];
        $filiado->foto                  = $data['foto'];
        $filiado->escolaridade          = $data['escolaridade'];
        $filiado->data_filiacao         = $data['data_filiacao'];
        $filiado->observacao            = $data['observacao'];
        $filiado->siape                 = $data['siape'];
        $filiado->profissao_id          = $data['profissao_id'];
        $filiado->unit_id               = $data['unit_id'];
        $filiado->margem                = $data['margem'];
        $filiado->terceirizado_id       = $data['terceirizado_id'];
        $filiado->repasse               = $data['repasse'];
        $filiado->roll_id               = $data['roll_id'];
        $filiado->matricula             = $data['matricula'];
        $filiado->local_trabalho_id     = $data['local_trabalho_id'];
        $filiado->data_admissao         = $data['data_admissao'];
        $filiado->desconto              = $data['desconto'];
        $filiado->cargo_id              = $data['cargo_id'];
        $filiado->tipo_matricula_id     = $data['tipo_matricula_id'];
        $filiado->regional_id           = $data['regional_id'];
        $filiado->store();
        return $filiado;
    }

    public function delete($id)
    {
        $filiado = new Filiado($id);
        $filiado->delete();
    }
}

