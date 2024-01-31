<?php

class RelDespesaPorFornecedorArquivo extends TPage
{
    private $form; // form
    
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_RelDespesaPorFornecedorArquivo_report');
        $this->form->setFormTitle( 'Despesa por Fornecedores' );
        $this->form->setFieldSizes('100%');
        
        // create the form fields
        // $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'razao_social');
        // $fornecedor_id->setMinLength(0);
        
        $data_inicio_filtro = new TDate('data_inicio_filtro');
        $data_inicio_filtro->setDatabaseMask('yyyy-mm-dd');
        $data_inicio_filtro->setMask('dd/mm/yyyy');
        
        $data_fim_filtro = new TDate('data_fim_filtro');
        $data_fim_filtro->setDatabaseMask('yyyy-mm-dd');
        $data_fim_filtro->setMask('dd/mm/yyyy');

        $data_inicio_filtro->addValidation('De', new TRequiredValidator);
        $data_fim_filtro->addValidation('Até', new TRequiredValidator);

        $row = $this->form->addFields( 
                                       [ new TLabel('Da data'), $data_inicio_filtro ],
                                       [ new TLabel('Até a data'), $data_fim_filtro ]);
        $row->layout = ['col-sm-2','col-sm-2'];

        $output_type  = new TRadioGroup('output_type');

        $row = $this->form->addFields( [ new TLabel('Escolha o Tipo de Arquivo'), $output_type ]                          
        );
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-2','col-sm-4'];

        $output_type->setUseButton();
        $options = ['html' =>'HTML', 'pdf' =>'PDF', 'rtf' =>'RTF', 'xls' =>'XLS'];
        $output_type->addItems($options);
        $output_type->setValue('xls');
        $output_type->setLayout('horizontal');
        
        $this->form->addAction( 'Gerar', new TAction(array($this, 'onGenerate')), 'fa:download blue');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        // $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        
        parent::add($vbox);
    }

    /**
     * method onGenerate()
     * Executed whenever the user clicks at the generate button
     */
    function onGenerate()
    {
        try
        {
            $data = $this->form->getData();
            $this->form->setData($data);
            $this->form->validate();

            $unit_id = TSession::getValue('userunitid');

            $datai = $data->data_inicio_filtro;
            $result1 = explode("-",$datai);
            $dataini = $result1[0].$result1[1].$result1[2];

            $dataf = $data->data_fim_filtro;
            $result2 = explode("-",$dataf);
            $datafim = $result2[0].$result2[1].$result2[2];
            
            $format = $data->output_type;

            // $sWhere[] = '';

            // if (!empty($numero_contrato)) {
            //     $sWhere[] .= "cc.id = ".$numero_contrato;
            // }

            // if (!empty($cliente_id)) {
            //     $sWhere[] .= "cc.cliente_id = ".$cliente_id;
            // }

            // if (!empty($plano_id)) {
            //     $sWhere[] .= "cc.plano_id = ".$plano_id;
            // }

            // $sW = implode(" AND ", $sWhere);

            $source = TTransaction::open('sample');
            
            // define the query
            $query = "SELECT cp.data_baixa as data, f.razao_social as fornecedor, cp.descricao as descricao, cp.valor as valor 
            FROM conta_pagar cp
            INNER JOIN fornecedor f ON (f.id = cp.fornecedor_id)
            WHERE  cp.data_baixa BETWEEN $dataini AND $datafim 
            AND cp.unit_id = $unit_id  
            AND cp.baixa = 'S' ORDER BY cp.data_baixa";
        

            $filters = [];

            
            $data = TDatabase::getData($source, $query, null, $filters );
            
            if ($data)
            {
                $widths = [150,400,600,150];
                
                switch ($format)
                {
                    case 'html':
                        $table = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $table = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        $table = new TTableWriterRTF($widths);
                        break;
                    case 'xls':
                        $table = new TTableWriterXLS($widths);
                        break;
                }
                
                if (!empty($table))
                {
                    // create the document styles
                    $table->addStyle('header', 'Helvetica', '16', 'B', '#ffffff', '#4B8E57');
                    $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#6CC361');
                    $table->addStyle('datap',  'Helvetica', '10', '',  '#000000', '#E3E3E3', 'LR');
                    $table->addStyle('datai',  'Helvetica', '10', '',  '#000000', '#ffffff', 'LR');
                    $table->addStyle('footer', 'Helvetica', '10', '',  '#2B2B2B', '#B5FFB4');
                    
                    $table->setHeaderCallback( function($table) {
                        $table->addRow();
                        $table->addCell('Despesas por Fornecedor', 'center', 'header', 12);
                        
                        $table->addRow();
                        $table->addCell('Data Baixa', 'center', 'title');
                        $table->addCell('Fornecedor', 'center', 'title');
                        $table->addCell('Descrição', 'center', 'title');
                        $table->addCell('Valor', 'center', 'title');

                    });
                    $table->setFooterCallback( function($table) {
                        $table->addRow();
                        $table->addCell(date('Y-m-d h:i:s'), 'center', 'footer', 12);
                    });
                    
                    // controls the background filling
                    $colour= FALSE;
                    
                    // data rows
                    foreach ($data as $row)
                    {
                        $style = $colour ? 'datap' : 'datai';
                        
                        $table->addRow();
                        $table->addCell($row['data'], 'center', $style);
                        $table->addCell($row['fornecedor'], 'center', $style);
                        $table->addCell($row['descricao'], 'center', $style);
                        $table->addCell($row['valor'], 'center', $style);

                        $colour = !$colour;
                    }
                    
                    $output = "tmp/listagem.{$format}";
                    
                    // stores the file
                    if (!file_exists($output) OR is_writable($output))
                    {
                        $table->save($output);
                        parent::openFile($output);
                    }
                    else
                    {
                        throw new Exception(_t('Permission denied') . ': ' . $output);
                    }
                    
                    // shows the success message
                    new TMessage('info', 'Relatório gerado com sucesso. Desabilite o PopUp de seu Browser.');
                }
            }
            else
            {
                new TMessage('error', 'No records found');
            }
    
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onShow(){

    }
}
