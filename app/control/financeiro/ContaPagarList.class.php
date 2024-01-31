<?php
/**
 * ContaPagarList Listing
 * @author  Fred Azv.
 */
class ContaPagarList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ContaPagar');
        $this->form->setFormTitle('Contas a Pagar');
        $this->form->setFieldSizes('100%');
        
        // create the form fields
        $id = new TEntry('id');
        $descricao = new TEntry('descricao');
        $documento = new TEntry('documento');
        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');
        $valor = new TNumeric('valor',2,',','.',true);
        $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'nome_fantasia');
        $pc_despesa_id = new TDBUniqueSearch('pc_despesa_id', 'sample', 'PcDespesa', 'id', 'nome');
        
        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        
        $baixa = new TCombo('baixa');
        $combo_baixa = array();
        $combo_baixa['S'] = 'Sim';
        $combo_baixa['N'] = 'Não';
        $baixa->addItems($combo_baixa);
        $baixa->setValue('N');
        
        $de = new TDate('de');
        $de->setDatabaseMask('yyyy-mm-dd');
        $de->setMask('dd/mm/yyyy');
        $ate = new TDate('ate');
        $ate->setDatabaseMask('yyyy-mm-dd');
        $ate->setMask('dd/mm/yyyy');

        $row = $this->form->addFields( [ new TLabel('ID Conta Pagar'), $id ],
                                       [ new TLabel('Descrição'), $descricao ],
                                       [ new TLabel('Documento'), $documento ],
                                       [ new TLabel('Vencimento'), $data_vencimento ],
                                       [ new TLabel('Baixa'), $baixa ]);
        $row->layout = ['col-sm-2', 'col-sm-4','col-sm-2', 'col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Valor'), $valor ],
                                       [ new TLabel('Fornecedor'), $fornecedor_id ],
                                       [ new TLabel('Plano de Contas'), $pc_despesa_id ],
                                       [ new TLabel('Conta Bancária'), $conta_bancaria_id ]);
        $row->layout = ['col-sm-2','col-sm-4', 'col-sm-4', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Da data'), $de ],
                                       [ new TLabel('Até a data'), $ate ]);
        $row->layout = ['col-sm-2','col-sm-2'];

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('ContaPagar_filter_data') );
        $this->form->setData( TSession::setValue('ContaPagarList', parse_url($_SERVER['REQUEST_URI'])) );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ContaPagarForm', 'onEdit']), 'fa:plus green');
        
        $btn2 = $this->form->addAction('Baixar Títulos', new TAction([$this, 'onBaixar']), 'fa:plus green');
        $btn3 = $this->form->addAction('Juntar Títulos', new TAction([$this, 'onJuntar']), 'fa:plus pink');
        $btn4 = $this->form->addAction('PDF', new TAction(['RelContaPagar', 'onViewPDF']), 'fa:table');
        $btn5 = $this->form->addAction('CSV', new TAction([$this, 'onExportCSV']), 'fa:table');

        // $this->form->addExpandButton();
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        
        //adicionando o botão checkbox ao form pra passar dados via post
        $this->formGrid = new TForm;
        $this->formGrid->add($this->datagrid);
        $this->formGrid->addField($btn2);
        $this->formGrid->addField($btn3);
        $this->formGrid->addField($btn4);
        $this->formGrid->addField($btn5);


        // creates the datagrid columns
        $column_check = new TDataGridColumn('checkbox', ' ', 'center'); 
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_data_conta = new TDataGridColumn('data_conta', 'Data Conta', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left');
        $column_documento = new TDataGridColumn('documento', 'Documento', 'left');
        $column_previsao = new TDataGridColumn('previsao', 'Previsao', 'left');
        $column_data_vencimento = new TDataGridColumn('data_vencimento', 'Vencimento', 'left');
        $column_multa = new TDataGridColumn('multa', 'Multa', 'left');
        $column_juros = new TDataGridColumn('juros', 'Juros', 'left');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');
        $column_desconto = new TDataGridColumn('desconto', 'Desconto', 'left');
        $column_portador = new TDataGridColumn('portador', 'Portador', 'left');
        $column_observacao = new TDataGridColumn('observacao', 'Observacao', 'left');
        $column_baixa = new TDataGridColumn('baixa', 'Baixa', 'left');
        $column_data_baixa = new TDataGridColumn('data_baixa', 'Data Baixa', 'left');
        $column_valor_pago = new TDataGridColumn('valor_pago', 'Valor Pago', 'left');
        $column_valor_parcial = new TDataGridColumn('valor_parcial', 'Valor Parcial', 'left');
        $column_valor_real = new TDataGridColumn('valor_real', 'Valor Real', 'left');
        $column_replica = new TDataGridColumn('replica', 'Replica', 'left');
        $column_parcelas = new TDataGridColumn('parcelas', 'Parcelas', 'right');
        $column_nparcelas = new TDataGridColumn('nparcelas', 'Nparcelas', 'right');
        $column_intervalo = new TDataGridColumn('intervalo', 'Intervalo', 'right');
        $column_responsavel = new TDataGridColumn('responsavel', 'Responsavel', 'left');
        $column_unit_id = new TDataGridColumn('unit_id', 'Unit Id', 'right');
        $column_tipo_pgto_id = new TDataGridColumn('tipo_pgto->nome', 'Tipo Pgto', 'right');
        $column_fornecedor_id = new TDataGridColumn('fornecedor->nome_fantasia', 'Fornecedor', 'left');
        $column_pc_despesa_id = new TDataGridColumn('pc_despesa->nome', 'Plano de Contas', 'left');
        $column_departamento_id = new TDataGridColumn('departamento_id', 'Departamento Id', 'right');
        $column_conta_bancaria_id = new TDataGridColumn('conta_bancaria_id', 'Conta Bancaria Id', 'right');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_check);
        $this->datagrid->addColumn($column_id);
        //$this->datagrid->addColumn($column_data_conta);
        $this->datagrid->addColumn($column_fornecedor_id);
        $this->datagrid->addColumn($column_pc_despesa_id);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_documento);
        //$this->datagrid->addColumn($column_previsao);
        $this->datagrid->addColumn($column_data_vencimento);
        //$this->datagrid->addColumn($column_multa);
        //$this->datagrid->addColumn($column_juros);
        /*$this->datagrid->addColumn($column_desconto);
        $this->datagrid->addColumn($column_portador);
        $this->datagrid->addColumn($column_observacao);
        $this->datagrid->addColumn($column_baixa);
        $this->datagrid->addColumn($column_data_baixa);
        $this->datagrid->addColumn($column_valor_pago);
        $this->datagrid->addColumn($column_valor_parcial);
        $this->datagrid->addColumn($column_valor_real);
        $this->datagrid->addColumn($column_replica);
        $this->datagrid->addColumn($column_parcelas);
        $this->datagrid->addColumn($column_nparcelas);
        $this->datagrid->addColumn($column_intervalo);
        $this->datagrid->addColumn($column_responsavel);
        $this->datagrid->addColumn($column_unit_id);*/
        //$this->datagrid->addColumn($column_tipo_pgto_id);
        $this->datagrid->addColumn($column_valor);
        /*$this->datagrid->addColumn($column_departamento_id);
        $this->datagrid->addColumn($column_conta_bancaria_id);*/

        $column_id->setTransformer(array($this, 'formatColor'));

        $column_data_vencimento->setTransformer( function($value, $object, $row) {
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

        $column_valor->setTotalFunction( function($values) {
            $total = array_sum((array) $values);
            $total = Utilidades::formatar_valor($total); //(is_numeric($total)) ? round($total,2) : 0;
            return '<div id="total_devido"> <b>Total: R$</b> ' . $total  . '</div>';
        });

        $action1 = new TDataGridAction(array('ContaPagarFormParcial', 'onEdit'));
        $action1->setLabel('Baixa Parcial');
        $action1->setImage('fas:sort-amount-up-alt black');
        $action1->setField('id');
        $action1->setDisplayCondition( array($this, 'displayColumn') );

        $action2 = new TDataGridAction(array('ContaPagarFormBaixa', 'onEdit'));
        $action2->setLabel('Baixar Individual');
        $action2->setImage('fas:angle-double-down black');
        $action2->setField('id');
        $action2->setDisplayCondition( array($this, 'displayColumn') );

        $action3 = new TDataGridAction(array('RelReciboContaPagar', 'onViewReciboContaPagar'));
        $action3->setLabel('Recido de Receitas');
        $action3->setImage('fas:receipt');
        $action3->setField('id');
        $action3->setField('fornecedor_id');
        
        $action_group = new TDataGridActionGroup('', 'fas:cog');

        $action_group->addHeader('Opções');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action3);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);

        // create EDIT actions
        $action_edit = new TDataGridAction(['ContaPagarForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $action_edit->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        $action_del->setDisplayCondition( array($this, 'displayColumn') );
        $this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // $botao = $this->form->addHeaderActionLink( 'Expandir',  new TAction([$this, 'onClose'], ['register_state' => 'false']), 'fa:search' );
        // $botao->class = "btn btn-info btn-sm";
        // $botao->id = 'custom-id-botao';

        // TScript::create('
        //     $(document).ready(function(){
        //       $("form .panel-body").toggleClass("collapse");
        //       $("#custom-id-botao").click(function(){
        //         event.preventDefault();
        //         $(".card-body.panel-body").toggleClass("collapse show");    
        //       });
        //     });
        // ');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->formGrid, $this->pageNavigation));
        
        parent::add($container);

    }

    public static function onClose(){}

    public function displayColumn( $object )
    {
        
        if($object->baixa == 'N')
        {
            return TRUE;
        }
        return FALSE;
    }

    public function formatColor($column_id, $object, $row)
    {
        $data = new ContaPagar($column_id);

        if ($data->baixa == "S")
        {
            $row->style = "background: #97f498";
            return "<span>$column_id</span>";
        }else{
            return "<span>$column_id</span>";
        }
    }
    
    public static function onCalcularTotal() {

        $items_devido = (array) TSession::getValue(__CLASS__.'_items');

        $total_devido = array_reduce($items_devido, function ($carry, $item) {
            $carry += Utilidades::to_number($item['valor_pago']);
            return $carry;
        }, 0);

        $total_devido_str = Utilidades::formatar_valor($total_devido);

        TScript::create(" $('#total_devido').text('{$total_devido_str}') ");

        $data = new stdClass();
        $data->total_devido = 'R$ '. $total_devido_str;

        TForm::sendData( 'form_ContaPagar', $data );

        $vlr = $total_devido;
    }

    public function onJuntar( $param )
    {
        $data = $this->form->getData();

        $selected = TSession::getValue(__CLASS__.'_selected_objects');
        if (!(is_array($selected) && count($selected))){
            new TMessage('info', 'Nenhum registro selecionado');
            return;
        }

        TTransaction::open('sample');

        try {

            $this->form->setData($data);
            $baix = '';
            $valorSplit = 0.00;

            foreach ($selected as $id) {

                $cp = new ContaPagar($id);
                if($cp->baixa == "N")
                {
                    $cp->data_conta = date('Y-m-d');
                    //$cp->data_vencimento = date('Y-m-d');
                    $cp->baixa = 'S';
                    $cp->split = 'S';
                    $cp->data_baixa = date('Y-m-d');
                    $cp->store();
                    $valorSplit = $valorSplit + $cp->valor;
                }else{

                    new TMessage('error', 'Não pode juntar títulos já baixados.',$this->onReload($param));
                   
                }
            }   

            $split = new ContaPagar();
            $split->data_conta = date('Y-m-d');
            $split->descricao = $cp->descricao;
            $split->documento = $cp->documento;
            $split->data_vencimento = date('Y-m-d');
            $split->baixa = 'N';
            $split->data_baixa = date('Y-m-d');
            $split->valor = $valorSplit;
            $split->valor_real = $valorSplit;
            $split->responsavel = $cp->responsavel;
            $split->unit_id = $cp->unit_id;
            $split->user_id = $cp->user_id;
            $split->tipo_pgto_id = 1;
            $split->tipo_forma_pgto_id = 1;
            $split->fornecedor_id = $cp->fornecedor_id;
            $split->pc_despesa_id = $cp->pc_despesa_id;
            $split->pc_despesa_nome = $cp->pc_despesa_nome;
            $split->departamento_id = $cp->departamento_id;
            $split->conta_bancaria_id = $cp->conta_bancaria_id;
            $split->store();

            TTransaction::close();

            TSession::delValue(__CLASS__.'_selected_objects');

            } catch (Exception $e) {
                TTransaction::rollback();
                new TMessage('error', 'Houve um problema ao juntar os Títulos. <br>' . $e->getMessage());
                $this->form->setData($data);
                return;
            }
    }

    public function onBaixar( $param )
    { 

        $data = $this->form->getData();

        $selected = TSession::getValue(__CLASS__.'_selected_objects');
        if (!(is_array($selected) && count($selected))){
            new TMessage('info', 'Nenhum registro selecionado');
            return;
        }

        TTransaction::open('sample');
        try {

            $this->form->setData($data);
            
            $baix = '';
            foreach ($selected as $id) 
            {
                $selected[] = $id;
                $cp = new ContaPagar($id);

                if($cp->baixa == "N")
                {
                    $baix = $cp->baixa = 'S';
                    $cp->data_baixa = date('Y-m-d');
                    $cp->store();

                    $movBancaria = new MovimentacaoBancaria();
                    $movBancaria->valor_movimentacao = $cp->valor;
                    $movBancaria->data_lancamento = $cp->data_conta;
                    $movBancaria->data_vencimento = $cp->data_vencimento;
                    $movBancaria->data_baixa = date('Y-m-d');
                    $movBancaria->status = 'Débito';
                    $movBancaria->historico = $cp->descricao;
                    $movBancaria->baixa = 'S';
                    $movBancaria->tipo = 0;
                    $movBancaria->documento = $cp->documento;
                    $movBancaria->unit_id = $cp->unit_id;
                    $movBancaria->fornecedor_id = $cp->fornecedor_id;
                    $movBancaria->pc_despesa_id = $cp->pc_despesa_id;
                    $movBancaria->pc_despesa_nome = $cp->pc_despesa_nome;
                    $movBancaria->conta_pagar_id = $id;
                    $movBancaria->conta_bancaria_id = $cp->conta_bancaria_id;
                    $movBancaria->store();
                }
            }   

            TTransaction::close();

            if($baix == 'S')
            {   
                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', 'Baixa realizada com sucesso. <br>',$pos_action);
            }
            else
            {   
                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('erro', '<b>Atenção!</b> Algo do tipo pode estar acontecendo:<br>Você não marcou nenhum título!<br>Ou ele já foi baixado. <br><b>Escolha outro.</b><br>',$pos_action);
            }
            
            TSession::delValue(__CLASS__.'_selected_objects');

            } catch (Exception $e) {
                TTransaction::rollback();
                new TMessage('error', 'Houve um problema a dar baixa no Título. <br>' . $e->getMessage());
                $this->form->setData($data);
                return;
            }

    }

    

    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('sample'); // open a transaction with database
            $object = new ContaPagar($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('ContaPagarList_filter_id',   NULL);
        TSession::setValue('ContaPagarList_filter_descricao',   NULL);
        TSession::setValue('ContaPagarList_filter_documento',   NULL);
        TSession::setValue('ContaPagarList_filter_data_vencimento',   NULL);
        TSession::setValue('ContaPagarList_filter_valor',   NULL);
        TSession::setValue('ContaPagarList_filter_fornecedor_id',   NULL);
        TSession::setValue('ContaPagarList_filter_pc_despesa_id',   NULL);
        TSession::setValue('ContaPagarList_filter_conta_bancaria_id',   NULL);
        TSession::setValue('ContaPagarList_filter_baixa',   NULL);
        TSession::setValue('ContaPagarList_filter_de',   NULL);
        TSession::setValue('ContaPagarList_filter_ate',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "{$data->id}"); // create the filter
            TSession::setValue('ContaPagarList_filter_id',   $filter); // stores the filter in the session
        }

        if (isset($data->descricao) AND ($data->descricao)) {
            $filter = new TFilter('descricao', 'like', "%{$data->descricao}%"); // create the filter
            TSession::setValue('ContaPagarList_filter_descricao',   $filter); // stores the filter in the session
        }


        if (isset($data->documento) AND ($data->documento)) {
            $filter = new TFilter('documento', 'like', "%{$data->documento}%"); // create the filter
            TSession::setValue('ContaPagarList_filter_documento',   $filter); // stores the filter in the session
        }


        if (isset($data->data_vencimento) AND ($data->data_vencimento)) {
            $filter = new TFilter('data_vencimento', 'like', "%{$data->data_vencimento}%"); // create the filter
            TSession::setValue('ContaPagarList_filter_data_vencimento',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue('ContaPagarList_filter_valor',   $filter); // stores the filter in the session
        }


        if (isset($data->fornecedor_id) AND ($data->fornecedor_id)) {
            $filter = new TFilter('fornecedor_id', '=', "$data->fornecedor_id"); // create the filter
            TSession::setValue('ContaPagarList_filter_fornecedor_id',   $filter); // stores the filter in the session
        }


        if (isset($data->pc_despesa_id) AND ($data->pc_despesa_id)) {
            $filter = new TFilter('pc_despesa_id', '=', "$data->pc_despesa_id"); // create the filter
            TSession::setValue('ContaPagarList_filter_pc_despesa_id',   $filter); // stores the filter in the session
        }


        if (isset($data->conta_bancaria_id) AND ($data->conta_bancaria_id)) {
            $filter = new TFilter('conta_bancaria_id', '=', "$data->conta_bancaria_id"); // create the filter
            TSession::setValue('ContaPagarList_filter_conta_bancaria_id',   $filter); // stores the filter in the session
        }

        if (isset($data->baixa) AND ($data->baixa)) {
            $filter = new TFilter('baixa', '=', "{$data->baixa}"); // create the filter
            TSession::setValue('ContaPagarList_filter_baixa',   $filter); // stores the filter in the session
        }
        
        if (isset($data->de) AND ($data->de)) {
            
            $filter = new TFilter('data_vencimento', '>=', "{$data->de}");
            TSession::setValue('ContaPagarList_filter_de',   $filter); 
        }

        if (isset($data->ate) AND ($data->ate)) {

            $filter = new TFilter('data_vencimento', '<=', "{$data->ate}");
            TSession::setValue('ContaPagarList_filter_ate',   $filter); 
        }

        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ContaPagar_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }

    public static function onSelect($param) {
        // get the selected objects from session
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');

        $check = $param['check'];
        $id = $param['id'];
        if ($check == 'false'){
            if (isset($selected_objects[$id])){
                unset($selected_objects[$id]);
            }
        }
        else
        {
            $selected_objects[$id] = $id; // add the object inside the array
        }
        TSession::setValue(__CLASS__.'_selected_objects', $selected_objects); // put the array back to the sessio
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'sample'
            TTransaction::open('sample');
            
            // creates a repository for ContaPagar
            $repository = new TRepository('ContaPagar');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_vencimento';
                $param['data_vencimento'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));
            //$criteria->add(new TFilter('baixa',  '= ', 'N'));
            $criteria->add(new TFilter('deleted_at', 'is', null));
            
            if (TSession::getValue('ContaPagarList_filter_id')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_id')); // add the session filter
            }

            if (TSession::getValue('ContaPagarList_filter_descricao')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_descricao')); // add the session filter
            }


            if (TSession::getValue('ContaPagarList_filter_documento')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_documento')); // add the session filter
            }


            if (TSession::getValue('ContaPagarList_filter_data_vencimento')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_data_vencimento')); // add the session filter
            }

            if (TSession::getValue('ContaPagarList_filter_valor')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_valor')); // add the session filter
            }


            if (TSession::getValue('ContaPagarList_filter_fornecedor_id')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_fornecedor_id')); // add the session filter
            }


            if (TSession::getValue('ContaPagarList_filter_pc_despesa_id')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_pc_despesa_id')); // add the session filter
            }


            if (TSession::getValue('ContaPagarList_filter_conta_bancaria_id')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_conta_bancaria_id')); // add the session filter
            }

            if (TSession::getValue('ContaPagarList_filter_baixa')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_baixa')); // add the session filter
            }

            if (TSession::getValue('ContaPagarList_filter_de')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_de')); // add the session filter
            }

            if (TSession::getValue('ContaPagarList_filter_ate')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_ate')); // add the session filter
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
           
            $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');

            if ($objects)
            {
                $total = count($objects);
                $atual = 0;
                foreach ($objects as $object)
                {
                    $atual++;


                    $chk_selecionar = new TCheckButton("chkcheckbutton");
                    $chk_selecionar->id = "chkcheckbutton{$object->id}";
                    $chk_selecionar->code = $object->id;
                    $chk_selecionar->setIndexValue('on');

                    if (isset($selected_objects[$object->id])){
                        $chk_selecionar->setValue('on');
                    }

                    $c = new TElement('div');
                    $c->add($chk_selecionar);

                    if ($total == $atual){

                        $selected = '';
                        foreach ((array)$selected_objects as $s) {
                            $selected .= $s . ',';
                        }

                        $selected = "[{$selected}]";

                        $script = TScript::create('$(document).ready(function () {
                            window.boleto_selected = '. $selected .';
                            
                            $("input[name=chkcheckbutton]").off("change").change(function () {
                            var value_check = $(this).is(\':checked\');
                            var code = $(this).attr("code");
                            if (value_check) {
                                if (window.boleto_selected.indexOf(code) === -1) {
                                    window.boleto_selected.push(code);
                                }
                            } else {
                                var index = window.boleto_selected.indexOf(code);
                                if (index > -1) {
                                    window.boleto_selected.splice(index,1);
                                }
                            }
                            __adianti_ajax_exec(\'class=ContaPagarList&method=onSelect&id=\'+code+\'&check=\'+value_check);
                            });});',false);

                        $c->add($script);
                    }

//                    $object->checkbox = new TCheckButton('checkbox'.$object->id);
//                    $object->checkbox->setIndexValue($object->id);
                    $object->checkbox = $c;
//                    $this->form->addField($object->checkbox);
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('sample'); // open a transaction with database
            $object = new ContaPagar($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {   
                $filter = new TFilter('baixa', '=', "N");
                TSession::setValue('ContaPagarList_filter_baixa',   $filter); 
                $this->onReload( func_get_arg(0) );
            }
            else
            {   
                $filter = new TFilter('baixa', '=', "N");
                TSession::setValue('ContaPagarList_filter_baixa',   $filter); 
                $this->onReload();
            }
        }
        parent::show();
    }

    public function onExportCSV()
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('sample');
                
            // creates a repository for Customer
            $repository = new TRepository('ContaPagar');
                
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));

            if (TSession::getValue('ContaPagarList_filter_fornecedor_id')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_fornecedor_id'));
            }

            if (TSession::getValue('ContaPagarList_filter_pc_despesa_id')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_pc_despesa_id'));
            }

            if (TSession::getValue('ContaPagarList_filter_descricao')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_descricao'));
            }

            if (TSession::getValue('ContaPagarList_filter_documento')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_documento')); 
            }

            if (TSession::getValue('ContaPagarList_filter_data_vencimento')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_data_vencimento')); 
            }

            if (TSession::getValue('ContaPagarList_filter_valor')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_valor')); 
            }

            if (TSession::getValue('ContaPagarList_filter_baixa')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_baixa')); 
            }

            if (TSession::getValue('ContaPagarList_filter_de')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_de')); 
            }

            if (TSession::getValue('ContaPagarList_filter_ate')) {
                $criteria->add(TSession::getValue('ContaPagarList_filter_ate')); 
            }

            $csv = '';
            // load the objects according to criteria
            $customers = $repository->load($criteria, false);
            if ($customers)
            {
                $csv .= 'Id'.';'.'Fornecedor'.';'.'Plano de contas'.';'.'Descrição'.';'.'Documento'.';'.
                'Vencimento'.';'.'Valor'."\n";
                $valorTotal = 0;
                foreach ($customers as $customer)
                {
                    $partes = explode(" ", $customer->data_vencimento);
                    $data = explode('-', $partes[0]);

                    $csv .= $customer->id.';'.
                            $customer->fornecedor->nome_fantasia.';'.
                            $customer->pc_despesa->nome.';'.
                            $customer->descricao.';'.
                            $customer->documento.';'.
                            $data[2].'/'.$data[1].'/'.$data[0].';'.
                            $customer->valor."\n";
                            $valorTotal += $customer->valor;
                }

                $csv .= ' '.';'.' '.';'.' '.';'.' '.';'.' '.';'.
                ' '.';'.$valorTotal."\n";

                file_put_contents('app/output/contaspagar.csv', $csv);
                TPage::openFile('app/output/contaspagar.csv');
            }
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}
