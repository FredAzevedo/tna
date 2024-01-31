<?php

class TagsReport extends TPage
{

    protected $form; // form

    public function __construct( $param )
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_TagsReport');
        $this->form->setFormTitle('Relatório Customizado');
        $this->form->setFieldSizes('100%');

        parent::setTargetContainer('adianti_right_panel');

        $tags = new TElement('tags');
        $tags->class = 'tagsreport';
        $tags->add('
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Descrição</th>
                    <th scope="col">TAG</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">TAG</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>Número do Projeto</b></td>
                    <td>[tag_numero_projeto]</td>
                    <td><b>Nome do Projeto</b></td>
                    <td>[tag_nome_projeto]</td>
                </tr>
                <tr>
                    <td><b>CNPJ ou CPF do Cliente</b></td>
                    <td>[tag_cnpj_cliente]</td>
                    <td><b>Gestor do Projeto</b></td>
                    <td>[tag_gestor]</td>
                </tr>
                <tr>
                    <td><b>Escopo do Serviço</b></td>
                    <td>[tag_escopo_servico]</td>
                    <td><b>Diagnóstico</b></td>
                    <td>[tag_diagnostico]</td>
                </tr>
                <tr>
                    <td><b>Razão Social do Cliente</b></td>
                    <td>[tag_razao_social]</td>
                    <td><b>Data do Projeto</b></td>
                    <td>[tag_data_projeto]</td>
                </tr>
                <tr>
                    <td><b>Endereço do Cliente</b></td>
                    <td>[tag_endereco_cliente]</td>
                    <td><b>Telefone do Cliente</b></td>
                    <td>[tag_telefone_cliente]</td>
                </tr>
                <tr>
                    <td><b>E-mail do Cliente</b></td>
                    <td>[tag_email_cliente]</td>
                    <td><b>Serviços Incluso Etapa 1</b></td>
                    <td>[tag_servicos_incluso_etapa1]</td>
                </tr>
                <tr>
                    <td><b>Serviços Incluso Etapa 2</b></td>
                    <td>[tag_servicos_incluso_etapa2]</td>
                    <td><b>Prazo</b></td>
                    <td>[tag_prazo]</td>
                </tr>
                <tr>
                    <td><b>Valor do Projeto</b></td>
                    <td>[tag_valor_projeto]</td>
                    <td><b>Valor por Extenso</b></td>
                    <td>[tag_valor_projeto_extenso]</td>
                </tr>
                <tr>
                    <td><b>Forma Pagamento</b></td>
                    <td>[tag_forma_pagamento]</td>
                    <td><b>Descrição da Forma de Pagamento</b></td>
                    <td>[tag_descricao_pagamento]</td>
                </tr>
                <tr>
                    <td><b>Descrição do Orçamento</b></td>
                    <td>[tag_orcamento_descricao]</td>
                    <td><b>Nome Fantasia do Cliente</b></td>
                    <td>[tag_mone_fantasia]</td>
                </tr>
                <tr>
                    <td><b>Número de Funcionários</b></td>
                    <td>[tag_numero_funcionarios]</td>
                    <td><b>Informações Gerais</b></td>
                    <td>[tag_info_gerais]</td>
                </tr>
                <tr>
                    <td><b>Concorrência do cliente</b></td>
                    <td>[tag_concorrencia]</td>
                    <td><b>Verbas ao Investimento</b></td>
                    <td>[tag_verba_investimento]</td>
                </tr>
                <tr>
                    <td><b>Histórico da Empresa</b></td>
                    <td>[tag_historico_empresa]</td>
                    <td><b>Problemática/Solução</b></td>
                    <td>[tag_problematica_solucao]</td>
                </tr>
                <tr>
                    <td><b>Documentos do Projeto</b></td>
                    <td>[tag_documentos]</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            </table>
        ');

        $row = $this->form->addFields( [ new TLabel('TAGS E VARIÁVEIS'), $tags ]
        );
        $row->layout = ['col-sm-12'];

        $btn = $this->form->addAction('Fechar', new TAction([$this, 'onClose']), '');
        $btn->class = 'btn btn-sm btn-primary';

        $container = new TVBox;
        $container->style = 'width: 100%';

        $container->add($this->form);

        parent::add($container);
    
    }
    

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
