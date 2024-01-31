<?php

use Adianti\Widget\Wrapper\TDBCombo;

class Relatorios extends TPage
{

    protected $form; // form


    public function __construct()
    {

        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ContaBancaria');
        $this->form->setFormTitle('Relatórios Gerenciais');
        $this->form->setFieldSizes('100%');

        $fluxoCaixa = new TButton('fluxoCaixa');
        $fluxoCaixa->setAction(new TAction(array($this, 'onFluxoCaixa')), 'Filtrar e Visualizar');
        $fluxoCaixa->setImage('bs:random red');

        $fluxoCaixaFuturo = new TButton('fluxoCaixaFuturo');
        $fluxoCaixaFuturo->setAction(new TAction(array($this, 'onfluxoCaixaFuturo')), 'Filtrar e Visualizar');
        $fluxoCaixaFuturo->setImage('bs:random red');

        $fluxoCaixaDetalhado = new TButton('fluxoCaixaDetalhado');
        $fluxoCaixaDetalhado->setAction(new TAction(array($this, 'onFluxoCaixaDetalhado')), 'Filtrar e Visualizar');
        $fluxoCaixaDetalhado->setImage('bs:random red');

        $fluxoCaixaGeral = new TButton('fluxoCaixaGeral');
        $fluxoCaixaGeral->setAction(new TAction(array($this, 'onFluxoCaixaGeral')), 'Filtrar e Visualizar');
        $fluxoCaixaGeral->setImage('bs:random red');

        $extratoBancario = new TButton('extratoBancario');
        $extratoBancario->setAction(new TAction(array($this, 'onExtratoBancario')), 'Filtrar e Visualizar');
        $extratoBancario->setImage('bs:list-alt black');

        $razaoDespesaReceita = new TButton('razaoDespesaReceita');
        $razaoDespesaReceita->setAction(new TAction(array($this, 'onReceitaDespesa')), 'Filtrar e Visualizar');
        $razaoDespesaReceita->setImage('bs:retweet');

        $despesaFornecedor = new TButton('despesaFornecedor');
        $despesaFornecedor->setAction(new TAction(array($this, 'onDespesaFornecedores')), 'Filtrar e Visualizar');
        $despesaFornecedor->setImage('bs:tags');

        $despesaFornecedorPrevisionado = new TButton('despesaFornecedorprevisionado');
        $despesaFornecedorPrevisionado->setAction(new TAction(array($this, 'onDespesaFornecedoresPrevisionado')), 'Filtrar e Visualizar');
        $despesaFornecedorPrevisionado->setImage('bs:tags');

        $relatorioDER = new TButton('relatorioDER');
        $relatorioDER->setAction(new TAction(array($this, 'onFluxoCaixa')), 'Filtrar e Visualizar');
        $relatorioDER->setImage('bs:list blue');
        
        $relatorioUser = new TButton('relatorioUser');
        $relatorioUser->setAction(new TAction(array($this, 'onGerarRelatorioComissaoVend')), 'Filtrar e Visualizar');
        $relatorioUser->setImage('bs:list blue');

        $relatorioFor = new TButton('relatorioFor');
        $relatorioFor->setAction(new TAction(array($this, 'onGerarRelatorioComissaoFor')), 'Filtrar e Visualizar');
        $relatorioFor->setImage('bs:list blue');

        $despesaPorFornecedor = new TButton('despesaPorFornecedor');
        $despesaPorFornecedor->setAction(new TAction(array($this, 'onDespesaPorFornecedor')), 'Filtrar e Visualizar');
        $despesaPorFornecedor->setImage('bs:tags');

        $ContasPagas = new TButton('ContasPagas');
        $ContasPagas->setAction(new TAction(array($this, 'onContasPagas')), 'Filtrar e Visualizar');
        $ContasPagas->setImage('bs:tags');

        $DerPlanoContas = new TButton('DerPlanoContas');
        $DerPlanoContas->setAction(new TAction(array('DrePlanoContas', 'onDerPlanoContas')), 'Filtrar e Visualizar');
        $DerPlanoContas->setImage('bs:tags');

        $centroDeCustos = new TButton('centroDeCustos');
        $centroDeCustos->setAction(new TAction(array($this, 'onCentroDeCustos')), 'Filtrar e Visualizar');
        $centroDeCustos->setImage('bs:retweet');

        $centroDeCustosFuturo = new TButton('centroDeCustosFuturo');
        $centroDeCustosFuturo->setAction(new TAction(array($this, 'onCentroDeCustosFuturo')), 'Filtrar e Visualizar');
        $centroDeCustosFuturo->setImage('bs:retweet');

        

        $row = $this->form->addFields( [ new TLabel('Fluxo de Caixa'), $fluxoCaixa ],
                                       [ new TLabel('Extrato Bancário'), $extratoBancario ], 
                                       [ new TLabel('Receitas e Despesas'), $razaoDespesaReceita ],
                                       [ new TLabel('Despesa de Fornecedores'), $despesaFornecedor ]);
        $row->layout = ['col-sm-3','col-sm-3', 'col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Comissão de Vendedores'), $relatorioUser ],
                                       [ new TLabel('Comissão de Parceiros'), $relatorioFor ], 
                                       [ new TLabel('Despesa Por Um Fornecedor'), $despesaPorFornecedor ],
                                       [ new TLabel('Centro de Custos'), $centroDeCustos ]);
        $row->layout = ['col-sm-3','col-sm-3', 'col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Fluxo de Caixa Detalhado'), $fluxoCaixaDetalhado ],
                                       [ new TLabel('DRE Plano de Contas'), $DerPlanoContas ], 
                                       [ new TLabel('Fluxo de Caixa Geral'), $fluxoCaixaGeral ],
                                       [ new TLabel('Despesas Projetadas'), $despesaFornecedorPrevisionado ]);
        $row->layout = ['col-sm-3','col-sm-3', 'col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Fluxo de Caixa Futuro'), $fluxoCaixaFuturo ],
                                       [ new TLabel('Centro de Custos Futuro'), $centroDeCustosFuturo ], 
                                       [ ],
                                       [ ]);
        $row->layout = ['col-sm-3','col-sm-3', 'col-sm-3','col-sm-3'];

        $this->form->addContent( ['<hr><h4><b>Exportação para Excel</b></h4>'] );


        $fluxoCaixaExcel = new TButton('fluxoCaixaExcel');
        $fluxoCaixaExcel->setAction(new TAction(array('ExportarFluxoCaixaDetalhado', 'onLoad')), 'Filtrar e Exportar');
        $fluxoCaixaExcel->setImage('fas:random red');

        $fluxoCaixaGeralExcel = new TButton('fluxoCaixaGeralExcel');
        $fluxoCaixaGeralExcel->setAction(new TAction(array('ExportarFluxoCaixaGeralDetalhado', 'onLoad')), 'Filtrar e Exportar');
        $fluxoCaixaGeralExcel->setImage('fas:random red');

        $razaoDespesaReceitaExcel = new TButton('razaoDespesaReceitaExcel');
        $razaoDespesaReceitaExcel->setAction(new TAction(array('ExportarReceitaDespesa', 'onLoad')), 'Filtrar e Visualizar');
        $razaoDespesaReceitaExcel->setImage('bs:retweet');

        $row = $this->form->addFields( [ new TLabel('Fluxo de Caixa Detalhado'), $fluxoCaixaExcel ],
                                       [ new TLabel('Fluxo de Caixa Geral'), $fluxoCaixaGeralExcel ], 
                                       [ new TLabel('Receitas e Despesas'), $razaoDespesaReceitaExcel ], 
                                       [  ]);
        $row->layout = ['col-sm-3','col-sm-3', 'col-sm-3','col-sm-3'];

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        
        parent::add( $vbox );
    }

    public function onFluxoCaixa( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('Conta Bancária:', $conta_bancaria_id);
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarRelFluxoCaixa')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Fluxo de Caixa)', $report);
    }
    

    public function onGerarRelFluxoCaixa( $param )
    {
       $gerar = new RelFluxoCaixa($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onFluxoCaixaFuturo( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarRelFluxoCaixaFuturo')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Fluxo de Caixa Futuro)', $report);
    }
    

    public function onGerarRelFluxoCaixaFuturo( $param )
    {
       $gerar = new RelFluxoCaixaFuturo($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onExtratoBancario( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('Conta Bancária:', $conta_bancaria_id);
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarExtratoBancario')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Extrato Bancário)', $report);
    }


    public function onGerarExtratoBancario( $param )
    {
       $gerar = new RelExtratoBancario($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onReceitaDespesa( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');

        $centro_custo_id = new TDBCombo('centro_custo_id','sample','CentroCusto','id','nome','nome');
        
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        $report->addQuickField('Centro de Custo:', $centro_custo_id);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarReceitaDespesa')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Receitas e Despesas)', $report);
    }


    public function onGerarReceitaDespesa( $param )
    {
       $gerar = new RelReceitaDispesa($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onDespesaFornecedores( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        //$fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'nome_fantasia');
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        //$report->addQuickField('Fornecedores:', $fornecedor_id);
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarDespesaFornecedores')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Despesa de Fornecedores)', $report);
    }


    public function onGerarDespesaFornecedores( $param )
    {
       $gerar = new RelDespesaFornecedores($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onDespesaFornecedoresPrevisionado( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        //$fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'nome_fantasia');
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        //$report->addQuickField('Fornecedores:', $fornecedor_id);
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarDespesaFornecedoresPrevisionado')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Despesa de Fornecedores)', $report);
    }


    public function onGerarDespesaFornecedoresPrevisionado( $param )
    {
       $gerar = new RelDespesaFornecedoresPrevisionado($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onGerarRelatorioComissaoVend( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
        $user_id->addValidation('Vendedor', new TRequiredValidator);
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('Vendedor:', $user_id);
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarRelComissaoVendedor')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Comissão do Vendedor)', $report);
    }

    public function onGerarRelComissaoVendedor( $param )
    {
       $gerar = new RelComissaoVendedor($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onGerarRelatorioComissaoFor( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        $fornecedor_id = new TDBCombo('fornecedor_id','sample','Fornecedor','id','nome_fantasia','nome_fantasia');
        $fornecedor_id->addValidation('Indicador', new TRequiredValidator);
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('Indicador:', $fornecedor_id);
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarRelComissaoFornecedor')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Comissão do Indicador)', $report);
    }

    public function onGerarRelComissaoFornecedor( $param )
    {
       $gerar = new RelComissaoFornecedor($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onDespesaPorFornecedor( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'nome_fantasia');
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('Fornecedores:', $fornecedor_id, '400px');
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarDespesaPorFornecedor')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Despesa por um Fornecedor)', $report);
    }


    public function onGerarDespesaPorFornecedor( $param )
    {
       $gerar = new RelDespesaPorFornecedor($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }


    public function onContasPagas( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('Conta Bancária:', $conta_bancaria_id);
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarRelContaspagas')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Contas Pagas)', $report);
    }

    public function onGerarRelContaspagas( $param )
    {
       $gerar = new RelContasPagas($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onFluxoCaixaDetalhado( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('Conta Bancária:', $conta_bancaria_id);
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarRelFluxoCaixaDetalhado')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Fluxo de Caixa)', $report);
    }
    

    public function onGerarRelFluxoCaixaDetalhado( $param )
    {
       $gerar = new RelFluxoCaixaDetalhado($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onFluxoCaixaGeral( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarRelFluxoCaixaGeral')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Fluxo de Caixa)', $report);
    }
    

    public function onGerarRelFluxoCaixaGeral( $param )
    {
       $gerar = new RelFluxoCaixaGeral($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onCentroDeCustos( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');
        
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarCentroDeCustos')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório (Centro de Custos)', $report);
    }


    public function onGerarCentroDeCustos( $param )
    {
       $gerar = new RelCentroCusto($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }

    public function onCentroDeCustosFuturo( $param )
    {
        $report = new TQuickForm('input_form');
        $report->style = 'padding:20px';

        $departamento_id = new TDBCombo('departamento_id', 'sample', 'Departamento', 'id', 'nome');
        
        $dataInicio = new TDate('dataInicio');
        $dataInicio->addValidation('Data de Início (De)', new TRequiredValidator);
        $dataInicio->setDatabaseMask('yyyy-mm-dd');
        $dataInicio->setMask('dd/mm/yyyy');

        $dataFim  = new TDate('dataFim');
        $dataFim->addValidation('Data de Início (Até)', new TRequiredValidator);
        $dataFim->setDatabaseMask('yyyy-mm-dd');
        $dataFim->setMask('dd/mm/yyyy');

        $report->addQuickField('Centro de Custo:', $departamento_id);
        $report->addQuickField('De:', $dataInicio);
        $report->addQuickField('Até:', $dataFim);
        
        $report->addQuickAction('Gerar Relatório', new TAction(array($this, 'onGerarCentroDeCustosFuturo')), 'fa:save green');

        new TInputDialog('Parâmetros para geração do relatório futuro (Centro de Custos)', $report);
    }


    public function onGerarCentroDeCustosFuturo( $param )
    {
       $gerar = new RelCentroCustoFuturo($param);

       $relatorio = $gerar->get_arquivo();
       if($relatorio)
       {
          parent::openFile($relatorio);
       }
    }
}
