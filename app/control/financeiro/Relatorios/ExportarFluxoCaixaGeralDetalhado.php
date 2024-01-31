<?php

use Adianti\Control\TWindow;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Validator\TRequiredValidator;

class ExportarFluxoCaixaGeralDetalhado extends TWindow
{
    private $form; 
    private $total;
    private $data_inicio;
    private $data_fim;

    function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_ExportarFluxoCaixaGeralDetalhado_report');
        $this->form->setFormTitle( 'Fluxo de Caixa por período' );
        $this->form->setFieldSizes('100%');

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);

        $dataini = new TDate('dataini');
        $dataini->setDatabaseMask('yyyy-mm-dd');
        $dataini->setMask('dd/mm/yyyy');

        $datafim = new TDate('datafim');
        $datafim->setDatabaseMask('yyyy-mm-dd');
        $datafim->setMask('dd/mm/yyyy');

        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);
        $dataini->addValidation('Data Inicio', new TRequiredValidator);
        $datafim->addValidation('Data Fim', new TRequiredValidator);
        
        $output_type  = new TRadioGroup('output_type');

        $row = $this->form->addFields( [ new TLabel('Escolha o Tipo de Arquivo'), $output_type ],   
                                       [ new TLabel('Data Inicio'), $dataini ],
                                       [ new TLabel('Data Fim'), $datafim ],
                                       [ ]
                                       
        );
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-2','col-sm-4'];

        $output_type->setUseButton();
        $options = ['html' =>'HTML', 'xls' =>'XLS'];
        $output_type->addItems($options);
        $output_type->setValue('xls');
        $output_type->setLayout('horizontal');
        
        $this->form->addAction( 'Gerar Arquivo', new TAction(array($this, 'onGenerate')), 'fa:download blue');
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        // $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        
        parent::add($vbox);
    }

    function onGenerate()
    {
        try
        {
            $data = $this->form->getData();
            $this->form->setData($data);
            $this->form->validate();

            $conta_bancaria_id = $data->conta_bancaria_id;
            $dataini = $data->dataini;
            $datafim = $data->datafim;
            $format  = $data->output_type;
            
            $source = TTransaction::open('sample');

            $query = 'SELECT 
            movimentacao_bancaria.created_at, 
            movimentacao_bancaria.data_baixa, 
            movimentacao_bancaria.historico, 
            cliente.nome_fantasia as cliente, 
            fornecedor.nome_fantasia as fornecedor,
            if(movimentacao_bancaria.tipo = 1,valor_movimentacao,null)  as entrada,
            if(movimentacao_bancaria.tipo = 0,valor_movimentacao,null)  as saida,
            if(movimentacao_bancaria.tipo = 1,conta_receber_id,conta_pagar_id)  as id,
            if(movimentacao_bancaria.tipo = 1,pc_receita.nome,pc_despesa.nome)  as pc,
            if(movimentacao_bancaria.tipo = 1,conta_receber.documento,conta_pagar.documento ) as doc,
            if(movimentacao_bancaria.tipo = 1,pgto2.nome,pgto1.nome ) as pgto,
            movimentacao_bancaria.conta_bancaria_id
            FROM movimentacao_bancaria
            LEFT JOIN conta_pagar ON (conta_pagar.id = movimentacao_bancaria.conta_pagar_id)
            LEFT JOIN tipo_pgto pgto1 ON (pgto1.id = conta_pagar.tipo_pgto_id)
            LEFT JOIN conta_receber ON (conta_receber.id = movimentacao_bancaria.conta_receber_id)
            LEFT JOIN tipo_pgto pgto2 ON (pgto2.id = conta_receber.tipo_pgto_id)
            LEFT JOIN pc_despesa ON (pc_despesa.id = movimentacao_bancaria.pc_despesa_id)
            LEFT JOIN pc_receita ON (pc_receita.id = movimentacao_bancaria.pc_receita_id)
            LEFT JOIN cliente ON (cliente.id = movimentacao_bancaria.cliente_id)
            LEFT JOIN fornecedor ON (fornecedor.id = movimentacao_bancaria.fornecedor_id)
            WHERE movimentacao_bancaria.data_baixa between :dataini and :datafim and movimentacao_bancaria.deleted_at is null
            ORDER BY movimentacao_bancaria.data_baixa';
            
            $filters = [

                'dataini' => $dataini,
                'datafim' => $datafim

            ];

            $data_inicio_format = DateTime::createFromFormat('Y-m-d', $dataini);
            $this->data_inicio = $data_inicio_format->format('d/m/Y');

            $data_fim_format = DateTime::createFromFormat('Y-m-d', $datafim);
            $this->data_fim = $data_fim_format->format('d/m/Y');
            
            $data = TDatabase::getData($source, $query, null, $filters );
            
            if ($data)
            {
                $widths = [80,300,300,300,100,100,100];

                $widthsCSV = [80,300,300,300,100,100,100];
                
                switch ($format)
                {
                    case 'html':
                        $table = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $table = new TTableWriterPDF($widths,'L');
                        break;
                    case 'rtf':
                        $table = new TTableWriterRTF($widths);
                        break;
                    case 'xls':
                        $table = new TTableWriterXLS($widthsCSV);
                        break;
                }
                
                if (!empty($table))
                {
                    $table->addStyle('header', 'Helvetica', '15', 'B', '#ffffff', '#363636');
                    $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#808080');
                    $table->addStyle('datap',  'Helvetica', '9', '',  '#000000', '#E3E3E3', 'LR');
                    $table->addStyle('datai',  'Helvetica', '9', '',  '#000000', '#ffffff', 'LR');
                    $table->addStyle('footer', 'Helvetica', '10', '',  '#ffffff', '#363636');

                    $conn = TTransaction::get();

                    $saldoAnterReceita = $conn->prepare('SELECT sum(valor_movimentacao) as valor FROM movimentacao_bancaria WHERE data_baixa < ? AND tipo = 1 and deleted_at is null'); 

                    $saldoAnterReceita->execute(array($dataini));
                    $resultReceita = $saldoAnterReceita->fetchAll();

                    foreach ($resultReceita as $receita) {
                        $valorReceita = $receita['valor'];
                    }

                    $saldoAnterDespesa = $conn->prepare('SELECT sum(valor_movimentacao) as valor FROM movimentacao_bancaria WHERE data_baixa < ? AND tipo = 0 and deleted_at is null'); 
                    $saldoAnterDespesa->execute(array($dataini));
                    $resultDespesa = $saldoAnterDespesa->fetchAll();

                    foreach ($resultDespesa as $despesa) {
                        $valorDespesa = $despesa['valor'];
                    }
                    
                    $this->total = $valorReceita - $valorDespesa;
                    
                    $totDespesa = 0.00;
                    $totReceita = 0.00;

                    
                    $table->setHeaderCallback( function($table) {
                        $table->addRow();
                        $table->addCell('Fluxo de Caixa Detalhado - Filtro baseado entre períodos: '.$this->data_inicio.' e '.$this->data_fim, 'center', 'header', 7);
                        $table->addRow();
                        $table->addCell('Saldo Anterior: R$ '.number_format($this->total, 2, ',', '.'), 'right', 'header', 7);
                        
                        $table->addRow();
                        $table->addCell('Data Baixa', 'center', 'title');
                        $table->addCell('Descrição', 'center', 'title');
                        $table->addCell('Cliente/Fornecedor', 'center', 'title');
                        $table->addCell('Plano de Contas', 'center', 'title');
                        $table->addCell('Entrada', 'center', 'title');
                        $table->addCell('Saída', 'center', 'title');
                        $table->addCell('Saldos', 'center', 'title');

                    });
                    
                    $table->setFooterCallback( function($table) {
                        $table->addRow();
                        $table->addCell(date('d/m/Y h:i:s'), 'center', 'footer', 7);
                    });

                    $colour= FALSE;

                    foreach ($data as $row)
                    {
                        $style = $colour ? 'datap' : 'datai';
                        
                        $table->addRow();
                        $data_data_baixa = DateTime::createFromFormat('Y-m-d', $row['data_baixa']);
                        $data_baixa = $data_data_baixa->format('d/m/Y');
                        $table->addCell($data_baixa, 'center', $style);
                        $table->addCell($row['historico'], 'left', $style);

                        if($row['entrada'] != 0.00){
                            $table->addCell($row['cliente'], 'left', $style);
                        }else{
                            $table->addCell($row['fornecedor'], 'left', $style);
                        }                    
                        
                        $table->addCell($row['pc'], 'left', $style);
                        $table->addCell($row['entrada'], 'right', $style);
                        $table->addCell($row['saida'], 'right', $style);

                        $totReceita = $totReceita + $row['entrada'];
                        $totDespesa = $totDespesa + $row['saida'];
                        $this->total = $this->total + $row['entrada'] - $row['saida'];

                        $table->addCell($this->total, 'right', $style);
                        
                        $colour = !$colour;
                    }
                    $table->addRow();

                    $table->addCell($totReceita, 'right','footer',5);
                    $table->addCell($totDespesa, 'right','footer',1);
                    $table->addCell($this->total, 'right','footer',1);

                    $output = "tmp/FluxoDeCaixa.{$format}";
 
                    if (!file_exists($output) OR is_writable($output))
                    {
                        $table->save($output);
                        parent::openFile($output);
                    }
                    else
                    {
                        throw new Exception(_t('Permission denied') . ': ' . $output);
                    }

                    new TMessage('info', 'Relatório gerado. Por favor, ative pop-ups no navegador.');
                }
            }
            else
            {
                new TMessage('info', 'Dados não encontrados de acordo com o Filtro aplicado. Gerãção não concluída.');
            }
    
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onLoad(){}
}
