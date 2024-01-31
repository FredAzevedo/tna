<?php

use Adianti\Control\TWindow;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Validator\TRequiredValidator;

class ExportarReceitaDespesa extends TWindow
{
    private $form; 
    private $total;
    private $data_inicio;
    private $data_fim;

    function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_ExportarReceitaDespesa_report');
        $this->form->setFormTitle( 'Fluxo de Caixa por período' );
        $this->form->setFieldSizes('100%');

        $dataini = new TDate('dataini');
        $dataini->setDatabaseMask('yyyy-mm-dd');
        $dataini->setMask('dd/mm/yyyy');

        $datafim = new TDate('datafim');
        $datafim->setDatabaseMask('yyyy-mm-dd');
        $datafim->setMask('dd/mm/yyyy');

        $centro_custo_id = new TDBCombo('centro_custo_id', 'sample', 'CentroCusto', 'id', 'nome');

        $dataini->addValidation('Data Inicio', new TRequiredValidator);
        $datafim->addValidation('Data Fim', new TRequiredValidator);
        
        $output_type  = new TRadioGroup('output_type');

        $row = $this->form->addFields( [ new TLabel('Escolha o Tipo de Arquivo'), $output_type ],   
                                       [ new TLabel('Data Inicio'), $dataini ],
                                       [ new TLabel('Data Fim'), $datafim ],
                                       [ new TLabel(''), $centro_custo_id ]

                                       
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

    function onGenerate( $param )
    {
        try
        {
            $data = $this->form->getData();
            $this->form->setData($data);
            $this->form->validate();

            $dataini            = $data->dataini;
            $datafim            = $data->datafim;
            $centro_custo_id    = $data->centro_custo_id;
            $format             = $data->output_type;


            $sWhere[] = '';

            if (!empty($centro_custo_id)) {
                $sWhere[] .= "centro_custo_id = ".$centro_custo_id;
            }

            $sW = implode(" AND ", $sWhere);


            $data_inicio_format = DateTime::createFromFormat('Y-m-d', $dataini);
            $this->data_inicio = $data_inicio_format->format('d/m/Y');

            $data_fim_format = DateTime::createFromFormat('Y-m-d', $datafim);
            $this->data_fim = $data_fim_format->format('d/m/Y');
            
            $widths = [80,600,100];

            $widthsCSV = [80,600,100];
            
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
                $table->addStyle('header', 'Helvetica', '15', 'B', '#000000', '#E3E3E3');
                $table->addStyle('header_receita', 'Helvetica', '15', 'B', '#000000', '#c2fccd');
                $table->addStyle('header_receita_titulo', 'Helvetica', '11', 'B', '#000000', '#c2fccd');
                $table->addStyle('footer_receita', 'Helvetica', '9', '',  '#000000', '#c2fccd');
                $table->addStyle('header_despesa_titulo', 'Helvetica', '11', 'B', '#000000', '#fcd4d4');
                $table->addStyle('header_despesa', 'Helvetica', '15', 'B', '#000000', '#fcd4d4');
                $table->addStyle('footer_despesa', 'Helvetica', '9', '',  '#000000', '#fcd4d4');
                $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#363636');
                $table->addStyle('datap',  'Helvetica', '9', '',  '#000000', '#E3E3E3', 'LR');
                $table->addStyle('datai',  'Helvetica', '9', '',  '#000000', '#ffffff', 'LR');
                $table->addStyle('footer', 'Helvetica', '10', '',  '#ffffff', '#808080');
                $table->addStyle('espaco', 'Helvetica', '10', '',  '#ffffff', '#ffffff');

                $table->setHeaderCallback( function($table) {
                    $table->addRow();
                    $table->addCell('Relatório Gerencial de Receitas e Despesas '.$this->data_inicio.' e '.$this->data_fim, 'center', 'header', 3);
                    $table->addRow();
                    $table->addCell('', 'center', 'header', 3);
                    $table->addRow();
                    $table->addCell('LANÇAMENTOS DE RECEITAS', 'center', 'header', 3);
                    $table->addRow();
                    $table->addCell('', 'center', 'header', 3);
                });
                
                $table->setFooterCallback( function($table) {
                    $table->addRow();
                    $table->addCell("Relatório gerado em ".date('d/m/Y h:i:s'), 'center', 'footer', 3);
                });

                $colour= FALSE;

                TTransaction::open('sample');
                $conn = TTransaction::get();

                $unit_id = TSession::getValue('userunitid');

                $listagemReceita = $conn->prepare("SELECT ccr.nome, mb.pc_receita_id FROM movimentacao_bancaria mb INNER JOIN pc_receita ccr ON (ccr.id = mb.pc_receita_id) WHERE mb.unit_id = ? AND mb.data_baixa BETWEEN ? AND ? $sW AND mb.deleted_at IS NULL GROUP BY ccr.nome ORDER BY ccr.nome, mb.data_baixa"); 

                $listagemReceita->execute(array($unit_id,$dataini,$datafim));
                $resultListagemReceita = $listagemReceita->fetchAll();

                $i=0;
                $totalr=0;

                foreach ($resultListagemReceita as $receita1) {

                    $ccr = $receita1['pc_receita_id'];

                    $table->addRow();
                    $table->addCell($receita1['nome'], 'left', 'header_receita_titulo', 3);

                    $table->addRow();
                    $table->addCell('Data da Baixa', 'center', 'title');
                    $table->addCell('Descrição', 'center', 'title');
                    $table->addCell('Valor', 'center', 'title');

                    $listagemItensReceita = $conn->prepare("SELECT data_baixa, historico, valor_movimentacao FROM movimentacao_bancaria WHERE pc_receita_id = ? AND data_baixa BETWEEN ? AND ? AND unit_id = ? ORDER BY data_baixa $sW AND deleted_at IS NULL"); 

                    $listagemItensReceita->execute(array($ccr,$dataini,$datafim,$unit_id));
                    $resultListagemItensReceita = $listagemItensReceita->fetchAll();

                    $y=0;
                    $totalReceita=0;

                    foreach ($resultListagemItensReceita as $row)
                    {
                        $style = $colour ? 'datap' : 'datai';
                        
                        $table->addRow();
                        $data_data_baixa = DateTime::createFromFormat('Y-m-d', $row['data_baixa']);
                        $data_baixa = $data_data_baixa->format('d/m/Y');
                        $table->addCell($data_baixa, 'center', $style);
                        $table->addCell($row['historico'], 'left', $style);
                        $table->addCell(number_format($row['valor_movimentacao'],2,',','.'), 'right', $style);

                        $totalReceita = $totalReceita + $row['valor_movimentacao'];
                        $colour = !$colour;
                    }
                    $table->addRow();
                    $table->addCell("TOTAL: ".number_format($totalReceita,2,',','.'), 'right', 'footer_receita', 3);
                    $table->addRow();
                    $table->addCell('', 'right', 'espaco', 3);

                    $i++;
                    
                    $totalr = $totalr + $totalReceita;
                }

                $table->addRow();
                $table->addCell("TOTAL GERAL: ".number_format($totalr,2,',','.'), 'right', 'footer_receita', 3);
                $table->addRow();
                $table->addCell('', 'right', 'espaco', 3);

                $table->addRow();
                $table->addCell('', 'center', 'header', 3);
                $table->addRow();
                $table->addCell('LANÇAMENTOS DE DESPESAS', 'center', 'header', 3);
                $table->addRow();
                $table->addCell('', 'center', 'header', 3);

                $listagemDespesa = $conn->prepare("SELECT ccd.nome, mb.pc_despesa_id FROM movimentacao_bancaria mb INNER JOIN pc_despesa ccd ON (ccd.id = mb.pc_despesa_id) WHERE mb.data_baixa BETWEEN ? AND ? AND mb.unit_id = ? $sW AND mb.deleted_at IS NULL GROUP BY 1 ORDER BY ccd.nome, mb.data_baixa"); 

                $listagemDespesa->execute(array($dataini,$datafim,$unit_id));
                $resultListagemDespesa = $listagemDespesa->fetchAll();

                $i=0;
                $totald=0;

                foreach ($resultListagemDespesa as $despesa1) {

                    $ccd = $despesa1['pc_despesa_id'];

                    $table->addRow();
                    $table->addCell($despesa1['nome'], 'left', 'header_despesa_titulo', 3);

                    $table->addRow();
                    $table->addCell('Data da Baixa', 'center', 'title');
                    $table->addCell('Descrição', 'center', 'title');
                    $table->addCell('Valor', 'center', 'title');

                    $listagemItensDespesa = $conn->prepare("SELECT data_baixa, historico, valor_movimentacao FROM movimentacao_bancaria WHERE pc_despesa_id = ? AND data_baixa BETWEEN ? AND ? AND unit_id = ? $sW AND deleted_at IS NULL ORDER BY data_baixa"); 

                    $listagemItensDespesa->execute(array($ccd,$dataini,$datafim,$unit_id));
                    $resultListagemItensDespesa = $listagemItensDespesa->fetchAll();

                    $y=0;
                    $totalDespesa=0;

                    foreach ($resultListagemItensDespesa as $row)
                    {
                        $style = $colour ? 'datap' : 'datai';
                        
                        $table->addRow();
                        $data_data_baixa = DateTime::createFromFormat('Y-m-d', $row['data_baixa']);
                        $data_baixa = $data_data_baixa->format('d/m/Y');
                        $table->addCell($data_baixa, 'center', $style);
                        $table->addCell($row['historico'], 'left', $style);
                        $table->addCell(number_format($row['valor_movimentacao'],2,',','.'), 'right', $style);

                        $totalDespesa = $totalDespesa + $row['valor_movimentacao'];
                        $colour = !$colour;
                    }
                    $table->addRow();
                    $table->addCell("TOTAL: ".number_format($totalDespesa,2,',','.'), 'right', 'footer_despesa', 3);
                    $table->addRow();
                    $table->addCell('', 'right', 'espaco', 3);

                    $i++;   
                
                    $totald = $totald + $totalDespesa;
                }
                $table->addRow();
                $table->addCell("TOTAL GERAL: ".number_format($totald,2,',','.'), 'right', 'footer_despesa', 3);
                $table->addRow();
                $table->addCell('', 'right', 'espaco', 3);
                
                $table->addRow();


                $output = "tmp/RelatorioReceitaDespesa.{$format}";
                
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
