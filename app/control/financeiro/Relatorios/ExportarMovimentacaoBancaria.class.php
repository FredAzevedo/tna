<?php

use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Validator\TRequiredValidator;

class ExportarMovimentacaoBancaria extends TPage
{
    private $form; 

    function __construct()
    {   
        parent::__construct();

         // creates the form
        $this->form = new BootstrapFormBuilder('ExportarMovimentacaoBancaria');
        $this->form->setFormTitle('Movimentação Bancaria');
        $this->form->setFieldSizes('100%');
        
        // create the form fields
        $valor_movimentacao = new TNumeric('valor_movimentacao', 2, ',', '.', true);

        $data_vencimento = new TDateTime('data_vencimento');
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');

        $data_baixa = new TDateTime('data_baixa');
        $data_baixa->setDatabaseMask('yyyy-mm-dd');
        $data_baixa->setMask('dd/mm/yyyy');
        
        $status = new TCombo('status');
        $combo_status = array();
        $combo_status['Crédito'] = 'Creditado';
        $combo_status['Débito'] = 'Debitado';
        $status->addItems($combo_status);

        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'razao_social');
        $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'nome_fantasia');
        $pc_despesa_id = new TDBUniqueSearch('pc_despesa_id', 'sample', 'PcDespesa', 'id', 'nome');
        $pc_receita_id = new TDBUniqueSearch('pc_receita_id', 'sample', 'PcReceita', 'id', 'nome');

        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','');

        $tipo = new TCombo('tipo');
        $combo_tipo = array();
        $combo_tipo['0'] = 'Despesa';
        $combo_tipo['1'] = 'Receita';
        $tipo->addItems($combo_tipo);

        $de = new TDate('de');
        $de->setDatabaseMask('yyyy-mm-dd');
        $de->setMask('dd/mm/yyyy');
        $ate = new TDate('ate');
        $ate->setDatabaseMask('yyyy-mm-dd');
        $ate->setMask('dd/mm/yyyy');

        $de->addValidation('De', new TRequiredValidator);
        $ate->addValidation('Até', new TRequiredValidator);

        $historico = new TEntry('historico');

        $documento = new TEntry('documento');

        // $row = $this->form->addFields( [ new TLabel('Valor'), $valor_movimentacao ],
        //                                [ new TLabel('Vencimento'), $data_vencimento ],
        //                                [ new TLabel('Data da Baixa'), $data_baixa ],
        //                                [ new TLabel('Status'), $status ],
        //                                [ new TLabel('Histórico'), $historico ],
        //                                [ new TLabel('Documento'), $documento ]);
        // $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2', 'col-sm-2'];


        $row = $this->form->addFields( [ new TLabel('Cliente'), $cliente_id ],
                                       [ new TLabel('Fornecedor'), $fornecedor_id ]
        );
        $row->layout = ['col-sm-6','col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Plano de Contas Receitas'), $pc_receita_id ],
                                       [ new TLabel('Plano de Contas Despesas'), $pc_despesa_id ]
        );
        $row->layout = ['col-sm-6','col-sm-6'];


        $row = $this->form->addFields( [ new TLabel('Conta Bancária'), $conta_bancaria_id ],
                                       [ new TLabel(''), ],
                                       [ new TLabel('De'), $de ],
                                       [ new TLabel('Até'), $ate ]
        );
        $row->layout = ['col-sm-6','col-sm-2','col-sm-2','col-sm-2'];

        $output_type  = new TRadioGroup('output_type');

        $row = $this->form->addFields( [ new TLabel('Escolha o Tipo de Arquivo'), $output_type ]                          
        );
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-2','col-sm-4'];

        $output_type->setUseButton();
        $options = ['html' =>'HTML', 'pdf' =>'PDF', 'rtf' =>'RTF', 'xls' =>'XLS'];
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

            $cliente_id = $data->cliente_id;
            $fornecedor_id = $data->fornecedor_id;
            $pc_receita_id = $data->pc_receita_id;
            $pc_despesa_id = $data->pc_despesa_id;
            $conta_bancaria_id = $data->conta_bancaria_id;

            $datai = $data->de;
            $result1 = explode("-",$datai);
            $dataini = $result1[0].$result1[1].$result1[2];

            $dataf = $data->ate;
            $result2 = explode("-",$dataf);
            $datafim = $result2[0].$result2[1].$result2[2];
            

            $format  = $data->output_type;

            $sWhere[] = '';

            // if (!empty($dataini) && !empty($datafim)) {
            //     $sWhere[] .= "WHERE mb.data_baixa BETWEEN ".$dataini." AND ".$datafim;
            // }

            if (!empty($cliente_id)) {
                $sWhere[] .= "mb.cliente_id = ".$cliente_id;
            }

            if (!empty($fornecedor_id)) {
                $sWhere[] .= "mb.fornecedor_id = ".$fornecedor_id;
            }

            if (!empty($pc_receita_id)) {
                $sWhere[] .= "mb.pc_receita_id = ".$pc_receita_id;
            }

            if (!empty($pc_despesa_id)) {
                $sWhere[] .= "mb.pc_despesa_id = ".$pc_despesa_id;
            }
            
            if (!empty($conta_bancaria_id)) {
                $sWhere[] .= "mb.conta_bancaria_id = ".$conta_bancaria_id;
            }

            $sW = implode(" AND ", $sWhere);
            
            $source = TTransaction::open('sample');

            //TTransaction::setLogger(new TLoggerSTD); // debugar sql

            $query = "SELECT 
            mb.id, 
            mb.valor_movimentacao, 
            mb.data_lancamento, 
            mb.data_vencimento, 
            mb.data_baixa, 
            mb.status, 
            mb.historico, 
            mb.documento, 
            c.razao_social as cliente , 
            f.razao_social as fornecedor, 
            mb.pc_receita_nome, 
            mb.pc_despesa_nome, 
            mb.conta_pagar_id as n_conta_pagar, 
            mb.conta_receber_id as n_conta_receber, 
            b.nome_banco
            FROM movimentacao_bancaria mb
            LEFT JOIN fornecedor f on (f.id = mb.fornecedor_id )
            LEFT JOIN cliente c on (c.id = mb.cliente_id )
            LEFT JOIN pc_despesa pd on (pd.id = mb.pc_despesa_id )
            LEFT JOIN pc_receita pr on (pr.id = mb.pc_receita_id )
            LEFT JOIN conta_bancaria cb on (cb.id = mb.conta_bancaria_id )
            LEFT JOIN banco b on (b.id = cb.banco_id )
            WHERE mb.data_baixa BETWEEN $dataini AND $datafim $sW";

            $filters = [
            ];

            $data = TDatabase::getData($source, $query, null, $filters ); 
            // echo"<pre>";
            // print_r($data);
            // echo"<pre>";
            
            if ($data)
            {
                $widths = [50,190,190,190,190,190,1000,700,1000,1000,700,700,180,180,180,180];

                $widthsCSV = [30,190,190,190,190,190,1000,700,1000,1000,700,700,180,180,180,180];
                
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
                    $table->addStyle('header', 'Helvetica', '16', '', '#ffffff', '#447baa');
                    $table->addStyle('title',  'Helvetica', '10', '', '#ffffff', '#4f6172');
                    $table->addStyle('datap',  'Helvetica', '10', '',  '#000000', '#E3E3E3', 'LR');
                    $table->addStyle('datai',  'Helvetica', '10', '',  '#000000', '#ffffff', 'LR');
                    $table->addStyle('footer', 'Helvetica', '10', '',  '#2B2B2B', '#a0c2ce');
                    
                    $table->setHeaderCallback( function($table) {
                        $table->addRow();
                        $table->addCell('Movimentações por período', 'center', 'header', 16);
                        
                        $table->addRow();
                        $table->addCell('ID', 'center', 'title');
                        $table->addCell('VALOR MOVIMENTAÇÃO', 'center', 'title');
                        $table->addCell('DATA LANÇAMENTO', 'center', 'title');
                        $table->addCell('DATA VENCIMENTO', 'center', 'title');
                        $table->addCell('DATA BAIXA', 'center', 'title');
                        $table->addCell('STATUS', 'center', 'title');
                        $table->addCell('HISTÓRICO', 'center', 'title');
                        $table->addCell('DOCUMENTO', 'center', 'title');
                        $table->addCell('CLIENTE', 'center', 'title');
                        $table->addCell('FORNECEDOR', 'center', 'title');
                        $table->addCell('RECEITA', 'center', 'title');
                        $table->addCell('DESPESA', 'center', 'title');
                        $table->addCell('CONTA A PAGAR', 'center', 'title');
                        $table->addCell('CONTA A RECEBER', 'center', 'title');
                        $table->addCell('NOME BANCO', 'center', 'title');
                    });
                    
                    $table->setFooterCallback( function($table) {
                        $table->addRow();
                        $table->addCell(date('d/m/Y h:i:s'), 'center', 'footer', 16);
                    });

                    $colour= FALSE;

                    foreach ($data as $row)
                    {
                        $style = $colour ? 'datap' : 'datai';
                        
                        $table->addRow();
                        $table->addCell($row['id'], 'center', $style);
                        $table->addCell($row['valor_movimentacao'], 'right', $style);
                        $table->addCell($row['data_lancamento'], 'center', $style);
                        $table->addCell($row['data_vencimento'], 'center', $style);
                        $table->addCell($row['data_baixa'], 'center', $style);
                        $table->addCell($row['status'], 'center', $style);
                        $table->addCell($row['historico'], 'left', $style);
                        $table->addCell($row['documento'], 'left', $style);
                        $table->addCell($row['cliente'], 'left', $style);
                        $table->addCell($row['fornecedor'], 'left', $style);
                        $table->addCell($row['pc_receita_nome'], 'left', $style);
                        $table->addCell($row['pc_despesa_nome'], 'left', $style);
                        $table->addCell($row['n_conta_pagar'], 'right', $style);
                        $table->addCell($row['n_conta_receber'], 'right', $style);
                        $table->addCell($row['nome_banco'], 'left', $style);
                        
                        $colour = !$colour;
                    }
                    
                    $output = "tmp/tabular.{$format}";
 
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
                new TMessage('error', 'Dados não encontrados de acordo com o filtro aplicado!');
            }
    
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onShow(){
    
    }
}
