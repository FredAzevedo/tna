<?php
/**
 * BoletoRetornoSelectionList Record selection
 * @author  <your name here>
 */
class BoletoRetornoSelectionList extends TPage
{
    protected $form;     // search form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('sample');            // defines the database
        $this->setActiveRecord('BoletoRetorno');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('banco_nome', 'like', 'banco_nome'); // filterField, operator, formField
        $this->addFilterField('carteira', 'like', 'carteira'); // filterField, operator, formField
        $this->addFilterField('nossoNumero', 'like', 'nossoNumero'); // filterField, operator, formField
        $this->addFilterField('numeroDocumento', 'like', 'numeroDocumento'); // filterField, operator, formField
        $this->addFilterField('numeroControle', 'like', 'numeroControle'); // filterField, operator, formField
        $this->addFilterField('ocorrencia', 'like', 'ocorrencia'); // filterField, operator, formField
        $this->addFilterField('ocorrenciaTipo', 'like', 'ocorrenciaTipo'); // filterField, operator, formField
        $this->addFilterField('ocorrenciaDescricao', 'like', 'ocorrenciaDescricao'); // filterField, operator, formField
        $this->addFilterField('dataOcorrencia', 'like', 'dataOcorrencia'); // filterField, operator, formField
        $this->addFilterField('dataVencimento', 'like', 'dataVencimento'); // filterField, operator, formField
        $this->addFilterField('dataCredito', 'like', 'dataCredito'); // filterField, operator, formField
        $this->addFilterField('valor', 'like', 'valor'); // filterField, operator, formField
        $this->addFilterField('valorTarifa', 'like', 'valorTarifa'); // filterField, operator, formField
        $this->addFilterField('valorIOF', 'like', 'valorIOF'); // filterField, operator, formField
        $this->addFilterField('valorAbatimento', 'like', 'valorAbatimento'); // filterField, operator, formField
        $this->addFilterField('valorDesconto', 'like', 'valorDesconto'); // filterField, operator, formField
        $this->addFilterField('valorRecebido', 'like', 'valorRecebido'); // filterField, operator, formField
        $this->addFilterField('valorMora', 'like', 'valorMora'); // filterField, operator, formField
        $this->addFilterField('valorMulta', 'like', 'valorMulta'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_BoletoRetorno');
        $this->form->setFormTitle('Retorno de Remessa');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        $banco_nome = new TEntry('banco_nome');
        $carteira = new TEntry('carteira');
        $nossoNumero = new TEntry('nossoNumero');
        $numeroDocumento = new TEntry('numeroDocumento');
        $numeroControle = new TEntry('numeroControle');
        $ocorrencia = new TEntry('ocorrencia');
        $ocorrenciaTipo = new TEntry('ocorrenciaTipo');
        $ocorrenciaDescricao = new TEntry('ocorrenciaDescricao');
        $dataOcorrencia = new TEntry('dataOcorrencia');
        $dataVencimento = new TEntry('dataVencimento');
        $dataCredito = new TEntry('dataCredito');
        $valor = new TEntry('valor');
        $valorTarifa = new TEntry('valorTarifa');
        $valorIOF = new TEntry('valorIOF');
        $valorAbatimento = new TEntry('valorAbatimento');
        $valorDesconto = new TEntry('valorDesconto');
        $valorRecebido = new TEntry('valorRecebido');
        $valorMora = new TEntry('valorMora');
        $valorMulta = new TEntry('valorMulta');


        // add the fields
        //$this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Banco') ], [ $banco_nome ] );
       /* $this->form->addFields( [ new TLabel('Carteira') ], [ $carteira ] );
        $this->form->addFields( [ new TLabel('Nossonumero') ], [ $nossoNumero ] );
        $this->form->addFields( [ new TLabel('Numerodocumento') ], [ $numeroDocumento ] );
        $this->form->addFields( [ new TLabel('Numerocontrole') ], [ $numeroControle ] );
        $this->form->addFields( [ new TLabel('Ocorrencia') ], [ $ocorrencia ] );
        $this->form->addFields( [ new TLabel('Ocorrenciatipo') ], [ $ocorrenciaTipo ] );
        $this->form->addFields( [ new TLabel('Ocorrenciadescricao') ], [ $ocorrenciaDescricao ] );
        $this->form->addFields( [ new TLabel('Dataocorrencia') ], [ $dataOcorrencia ] );
        $this->form->addFields( [ new TLabel('Datavencimento') ], [ $dataVencimento ] );
        $this->form->addFields( [ new TLabel('Datacredito') ], [ $dataCredito ] );
        $this->form->addFields( [ new TLabel('Valor') ], [ $valor ] );
        $this->form->addFields( [ new TLabel('Valortarifa') ], [ $valorTarifa ] );
        $this->form->addFields( [ new TLabel('Valoriof') ], [ $valorIOF ] );
        $this->form->addFields( [ new TLabel('Valorabatimento') ], [ $valorAbatimento ] );
        $this->form->addFields( [ new TLabel('Valordesconto') ], [ $valorDesconto ] );
        $this->form->addFields( [ new TLabel('Valorrecebido') ], [ $valorRecebido ] );
        $this->form->addFields( [ new TLabel('Valormora') ], [ $valorMora ] );
        $this->form->addFields( [ new TLabel('Valormulta') ], [ $valorMulta ] );*/

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('BoletoRetorno_filter_data') );
        
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Show results', new TAction([$this, 'showResults']), 'fa:check-circle-o green');
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_banco_nome = new TDataGridColumn('banco_nome', 'Banco', 'left');
        $column_carteira = new TDataGridColumn('carteira', 'Carteira', 'left');
        $column_nossoNumero = new TDataGridColumn('nossoNumero', 'Nosso número', 'right');
        $column_numeroDocumento = new TDataGridColumn('numeroDocumento', 'Número do documento', 'right');
        $column_numeroControle = new TDataGridColumn('numeroControle', 'Controle', 'right');
        $column_ocorrencia = new TDataGridColumn('ocorrencia', 'Ocorrencia', 'right');
        $column_ocorrenciaTipo = new TDataGridColumn('ocorrenciaTipo', 'Tipo de Ocorrência', 'left');
        $column_ocorrenciaDescricao = new TDataGridColumn('ocorrenciaDescricao', 'Descrição', 'left');
        $column_dataOcorrencia = new TDataGridColumn('dataOcorrencia', 'Data da ocorrência', 'center');
        $column_dataVencimento = new TDataGridColumn('dataVencimento', 'Data do vencimento', 'center');
        $column_dataCredito = new TDataGridColumn('dataCredito', 'Data do crédito', 'center');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');
        $column_valorTarifa = new TDataGridColumn('valorTarifa', 'Tarifa', 'left');
        $column_valorIOF = new TDataGridColumn('valorIOF', 'IOF', 'left');
        $column_valorAbatimento = new TDataGridColumn('valorAbatimento', 'Abatimento', 'left');
        $column_valorDesconto = new TDataGridColumn('valorDesconto', 'Desconto', 'left');
        $column_valorRecebido = new TDataGridColumn('valorRecebido', 'Recebido', 'left');
        $column_valorMora = new TDataGridColumn('valorMora', 'Mora', 'left');
        $column_valorMulta = new TDataGridColumn('valorMulta', 'Multa', 'left');


        $column_dataOcorrencia->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $column_dataVencimento->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $column_dataCredito->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };     

        $column_valor->setTransformer( $format_value );


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_banco_nome);
        //$this->datagrid->addColumn($column_carteira);
        $this->datagrid->addColumn($column_nossoNumero);
        $this->datagrid->addColumn($column_numeroDocumento);
        //$this->datagrid->addColumn($column_numeroControle);
        $this->datagrid->addColumn($column_ocorrencia);
        //$this->datagrid->addColumn($column_ocorrenciaTipo);
        $this->datagrid->addColumn($column_ocorrenciaDescricao);
        $this->datagrid->addColumn($column_dataOcorrencia);
        $this->datagrid->addColumn($column_dataVencimento);
        $this->datagrid->addColumn($column_dataCredito);
        $this->datagrid->addColumn($column_valor);
        /*$this->datagrid->addColumn($column_valorTarifa);
        $this->datagrid->addColumn($column_valorIOF);
        $this->datagrid->addColumn($column_valorAbatimento);
        $this->datagrid->addColumn($column_valorDesconto);
        $this->datagrid->addColumn($column_valorRecebido);
        $this->datagrid->addColumn($column_valorMora);
        $this->datagrid->addColumn($column_valorMulta);*/

        $column_id->setTransformer([$this, 'formatRow'] );
        
        // creates the datagrid actions
        $action1 = new TDataGridAction([$this, 'onSelect']);
        $action1->setUseButton(TRUE);
        $action1->setButtonClass('btn btn-default');
        $action1->setLabel(AdiantiCoreTranslator::translate('Select'));
        $action1->setImage('fa:check-circle-o blue');
        $action1->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Save the object reference in session
     */
    public function onSelect($param)
    {
        // get the selected objects from session 
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        
        TTransaction::open('sample');
        $object = new BoletoRetorno($param['key']); // load the object
        if (isset($selected_objects[$object->id]))
        {
            unset($selected_objects[$object->id]);
        }
        else
        {
            $selected_objects[$object->id] = $object->toArray(); // add the object inside the array
        }
        TSession::setValue(__CLASS__.'_selected_objects', $selected_objects); // put the array back to the session
        TTransaction::close();
        
        // reload datagrids
        $this->onReload( func_get_arg(0) );
    }
    
    /**
     * Highlight the selected rows
     */
    public function formatRow($value, $object, $row)
    {
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        
        if ($selected_objects)
        {
            if (in_array( (int) $value, array_keys( $selected_objects ) ) )
            {
                $row->style = "background: #FFD965";
            }
        }
        
        return $value;
    }
    
    /**
     * Show selected records
     */
    public function showResults()
    {
        $datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        
        $datagrid->addQuickColumn('Id', 'id', 'right');
        $datagrid->addQuickColumn('Banco Nome', 'banco_nome', 'left');
        $datagrid->addQuickColumn('Carteira', 'carteira', 'left');
        $datagrid->addQuickColumn('Nossonumero', 'nossoNumero', 'left');
        $datagrid->addQuickColumn('Numerodocumento', 'numeroDocumento', 'left');
        $datagrid->addQuickColumn('Numerocontrole', 'numeroControle', 'left');
        $datagrid->addQuickColumn('Ocorrencia', 'ocorrencia', 'left');
        $datagrid->addQuickColumn('Ocorrenciatipo', 'ocorrenciaTipo', 'left');
        $datagrid->addQuickColumn('Ocorrenciadescricao', 'ocorrenciaDescricao', 'left');
        $datagrid->addQuickColumn('Dataocorrencia', 'dataOcorrencia', 'left');
        $datagrid->addQuickColumn('Datavencimento', 'dataVencimento', 'left');
        $datagrid->addQuickColumn('Datacredito', 'dataCredito', 'left');
        $datagrid->addQuickColumn('Valor', 'valor', 'left');
        $datagrid->addQuickColumn('Valortarifa', 'valorTarifa', 'left');
        $datagrid->addQuickColumn('Valoriof', 'valorIOF', 'left');
        $datagrid->addQuickColumn('Valorabatimento', 'valorAbatimento', 'left');
        $datagrid->addQuickColumn('Valordesconto', 'valorDesconto', 'left');
        $datagrid->addQuickColumn('Valorrecebido', 'valorRecebido', 'left');
        $datagrid->addQuickColumn('Valormora', 'valorMora', 'left');
        $datagrid->addQuickColumn('Valormulta', 'valorMulta', 'left');
        
        // create the datagrid model
        $datagrid->createModel();
        
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        ksort($selected_objects);
        if ($selected_objects)
        {
            $datagrid->clear();
            foreach ($selected_objects as $selected_object)
            {
                $datagrid->addItem( (object) $selected_object );
            }
        }
        
        $win = TWindow::create('Results', 0.6, 0.6);
        $win->add($datagrid);
        $win->show();
    }
}
