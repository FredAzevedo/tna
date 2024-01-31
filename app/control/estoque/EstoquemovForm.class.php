<?php
/**
 * EstoquemovForm Master/Detail
 * @author  Fred Azv.
 */
class EstoquemovForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Estoquemov');
        $this->form->setFormTitle('Inventário (Lançamentos de movimentação em estoque)');
        $this->form->setFieldSizes('100%');
        
        // master fields
        $id = new TEntry('id');

        $controla_lote = new TCombo('controla_lote');
        $combo['S'] = 'Sim';
        $combo['N'] = 'Não';
        $controla_lote->addItems($combo);
        $controla_lote->addValidation('controla_lote', new TRequiredValidator);

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $unit_produto = new TCriteria();
        $unit_produto->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $produto_id = new TDBUniqueSearch('produto_id', 'sample', 'Produto', 'id','nome_produto','nome_produto', $unit_produto);
        $produto_id->setMask('{cod_referencia} - {nome_produto}');
        $produto_id->addValidation('Produto', new TRequiredValidator);

        $local = new TDBCombo('local','sample','SystemUnit','cnpj','unidade','unidade',$id_unit_session);
        $local->setValue(TSession::getValue('userunitid'));
        $local->addValidation('Local', new TRequiredValidator);

        $tipo = new TCombo('tipo');
        $combo_tipos = array();
        $combo_tipos['E'] = 'Entrada';
        $combo_tipos['S'] = 'Saída';
        $tipo->addItems($combo_tipos);
        $tipo->addValidation('Tipo', new TRequiredValidator);

        $quantidade = new TNumeric('quantidade',0,'','.',true);
        $quantidade->addValidation('Quantidade', new TRequiredValidator);
        $saldo = new TEntry('saldo');
        $valor = new TNumeric('valor', 2, ',', '.', true);
        $referencia = new TEntry('referencia');

        // add the fields
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Controla Lotes?'), $controla_lote ]);
        $row->layout = ['col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Tipo'), $tipo ],
                                       [ new TLabel('Local'), $local ],
                                       [ new TLabel('Referência / Observação'), $referencia ]);
        $row->layout = ['col-sm-3', 'col-sm-2', 'col-sm-4', 'col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Produto'), $produto_id ],
                                       [ new TLabel('Quantidade'), $quantidade ],
                                       [ new TLabel('Valor'), $valor ]);
        $row->layout = ['col-sm-8', 'col-sm-2', 'col-sm-2'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // detail fields
        $detail_id = new THidden('detail_id');
        $detail_lote = new TEntry('detail_lote');
        $detail_vencimento = new TDate('detail_vencimento');
        $detail_vencimento->setDatabaseMask('yyyy-mm-dd');
        $detail_vencimento->setMask('dd/mm/yyyy');
        $detail_quantidade = new TNumeric('detail_quantidade',0,'','.',true);
        
        // detail fields
        $this->form->addContent( ['<h4>Lançamentos de Lotes</h4><hr>'] );
        $this->form->addFields( [$detail_id] );

        $row = $this->form->addFields( [ new TLabel('Lote'), $detail_lote ],
                                       [ new TLabel('Vencimento'), $detail_vencimento ],
                                       [ new TLabel('Quantidade'), $detail_quantidade ]
                                       );
        $row->layout = ['col-sm-6', 'col-sm-2', 'col-sm-4'];

        $add = TButton::create('add', [$this, 'onSaveDetail'], 'Register', 'fa:save');
        $this->form->addFields( [], [$add] )->style = 'background: whitesmoke; padding: 5px; margin: 1px;';
        
        $this->detail_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->detail_list->setId('Estoquemov_list');
        
        // items
        $this->detail_list->addQuickColumn('Lote', 'lote', 'left', 100);
        $this->detail_list->addQuickColumn('Vencimento', 'vencimento', 'left', 50);
        $this->detail_list->addQuickColumn('Quantidade', 'quantidade', 'left', 100);

        // detail actions
        $this->detail_list->addQuickAction( 'Edit',   new TDataGridAction([$this, 'onEditDetail']),   'id', 'fa:edit blue');
        $this->detail_list->addQuickAction( 'Delete', new TDataGridAction([$this, 'onDeleteDetail']), 'id', 'fa:trash red');
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );

        $btn = $this->form->addAction( _t('Save'),  new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction( _t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction(['EstoquemovList','onReload']), 'fa:arrow-circle-left red');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml','EstoquemovList'));
        $container->add($this->form);
        parent::add($container);

        //JS Responsável por desabilitar o campo quantidade.
        $controla_lote->onChange   = 'lote()';

        TScript::create('lote = function() {

            let tipo;
            tipo = form_Estoquemov.controla_lote.value;
                if(tipo == "S"){
                    form_Estoquemov.quantidade.readOnly = true;
                }else{
                    form_Estoquemov.quantidade.readOnly = false;
                }
            };
            
            lote();
        ');

        $detail_quantidade->onBlur = 'somaQuantidade()';

        TScript::create('somaQuantidade = function() {

                let quantidadeLote = form_Estoquemov.detail_quantidade.value;
                let quantidadeTotal = form_Estoquemov.quantidade.value;

                totalSoma = parseInt(quantidadeLote) + parseInt(quantidadeTotal);

                form_Estoquemov.quantidade.value = totalSoma;
                form_Estoquemov.quantidade.readOnly = true;
            };
        ');
    }
    
    public static function onSomarTotal() {
        
        $items = (array) TSession::getValue(__CLASS__.'_items');
        $detail_quantidade = array_reduce($items, function ($carry, $item) {
            $carry += $item['quantidade'];
            return $carry;
        }, 0);

        $data = new stdClass();
        $data->quantidade = $detail_quantidade;

        TForm::sendData( 'form_Estoquemov', $data );

    }
    
    /**
     * Clear form
     * @param $param URL parameters
     */
    public function onClear($param)
    {
        $this->form->clear(TRUE);
        TSession::setValue(__CLASS__.'_items', array());
        $this->onReload( $param );
    }
    
    /**
     * Save an item from form to session list
     * @param $param URL parameters
     */
    public function onSaveDetail( $param )
    {
        try
        {
            TTransaction::open('sample');
            $data = $this->form->getData();
            
            /** validation sample
            if (empty($data->fieldX))
            {
                throw new Exception('The field fieldX is required');
            }
            **/
            
            $items = TSession::getValue(__CLASS__.'_items');
            $key = empty($data->detail_id) ? 'X'.mt_rand(1000000000, 1999999999) : $data->detail_id;
            
            $items[ $key ] = array();
            $items[ $key ]['id'] = $key;
            $items[ $key ]['lote'] = $data->detail_lote;
            $items[ $key ]['vencimento'] = $data->detail_vencimento;
            $items[ $key ]['quantidade'] = $data->detail_quantidade;
            $items[ $key ]['unit_id'] = $param['unit_id'];
            $items[ $key ]['produto_id'] = $param['produto_id'];
            $items[ $key ]['local'] = $param['local'];
            $items[ $key ]['tipo'] = $param['tipo'];

            TSession::setValue(__CLASS__.'_items', $items);
            
            // clear detail form fields 
            $data->detail_id = '';
            $data->detail_lote = '';
            $data->detail_vencimento = '';
            $data->detail_quantidade = '';
            
            TTransaction::close();
            $this->form->setData($data);
            
            $this->onReload( $param ); // reload the items
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Load an item from session list to detail form
     * @param $param URL parameters
     */
    public static function onEditDetail( $param )
    {
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        // get the session item
        $item = $items[ $param['key'] ];
        
        $data = new stdClass;
        $data->detail_id = $item['id'];
        $data->detail_lote = $item['lote'];
        $data->detail_vencimento = $item['vencimento'];
        $data->detail_quantidade = $item['quantidade'];
        
        // fill detail fields
        TForm::sendData( 'form_Estoquemov', $data );
    }
    
    /**
     * Delete an item from session list
     * @param $param URL parameters
     */
    public static function onDeleteDetail( $param )
    {
        // reset items
        $data = new stdClass;
            $data->detail_lote = '';
            $data->detail_vencimento = '';
            $data->detail_quantidade = '';
        
        // clear form data
        TForm::sendData('form_Estoquemov', $data );
        
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        // get detail id
        $detail_id = $param['key'];
        
        // delete the item from session
        unset($items[ $detail_id ] );
        
        // rewrite session items
        TSession::setValue(__CLASS__.'_items', $items);
        
        // delete item from screen
        TScript::create("ttable_remove_row_by_id('Estoquemov_list', '{$detail_id}')");
        self::onSomarTotal();
    }
    
    /**
     * Load the items list from session
     * @param $param URL parameters
     */
    public function onReload($param)
    {
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        $this->detail_list->clear(); // clear detail list
        
        if ($items)
        {
            foreach ($items as $list_item)
            {
                $item = (object) $list_item;
                
                $row = $this->detail_list->addItem( $item );
                $row->id = $list_item['id'];
            }
        }
        self::onSomarTotal();
        $this->loaded = TRUE;
    }
    
    /**
     * Load Master/Detail data from database to form/session
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('sample');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Estoquemov($key);
                $items  = EstoquemovLote::where('estoquemov_id', '=', $key)->load();
                
                $session_items = array();
                foreach( $items as $item )
                {
                    $item_key = $item->id;
                    $session_items[$item_key] = $item->toArray();
                    $session_items[$item_key]['id'] = $item->id;
                    $session_items[$item_key]['lote'] = $item->lote;
                    $session_items[$item_key]['vencimento'] = $item->vencimento;
                    $session_items[$item_key]['quantidade'] = $item->quantidade;

                    $session_items[$item_key]['unit_id'] = $param['unit_id'];
                    $session_items[$item_key]['produto_id'] = $param['produto_id'];
                    $session_items[$item_key]['local'] = $param['local'];
                    $session_items[$item_key]['tipo'] = $param['tipo'];
                }
                TSession::setValue(__CLASS__.'_items', $session_items);
                
                $this->form->setData($object); // fill the form with the active record data
                $this->onReload( $param ); // reload items list
                TTransaction::close(); // close transaction
            }
            else
            {
                $this->form->clear(TRUE);
                TSession::setValue(__CLASS__.'_items', null);
                $this->onReload( $param );
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form/session to database
     */
    public function onSave()
    {
        try
        {
            // open a transaction with database
            TTransaction::open('sample');
            
            $data = $this->form->getData();
            $master = new Estoquemov;
            $master->fromArray( (array) $data);
            $this->form->validate(); // form validation
            
            if($data->tipo == 'E')
            {
                $comp = ProdutoComposicao::where('produto_id','=',$data->produto_id)->load();
                if($comp)
                {
                    foreach ($comp as $comp_item)
                    {   
                        // VERIFICA SALDO
                        $quant = $data->quantidade * $comp_item->quantidade;
                        $sal = Estoque::where('produto_id','=',$comp_item->composicao_id)->first();

                        if (($sal->saldo != null) && ($sal->saldo >= $quant))
                        {
                            $composicao = new Estoquemov;
                            $composicao->unit_id = $data->unit_id;
                            $composicao->produto_id = $comp_item->composicao_id;
                            $composicao->local = $data->local;
                            $composicao->tipo = 'S';
                            $composicao->quantidade = $quant;
                            $composicao->valor = 0.00;
                            $composicao->referencia = "Composição para o produto ID: ".$data->produto_id;
                            $composicao->controla_lote = 'N';
                            $composicao->store();
                        }
                        else
                        {   
                            $action = new TAction([__CLASS__, 'onEdit']);
                            new TMessage('error', "Saldo insuficiente para o Produto com ID: ".$comp_item->composicao_id, $action);
                            exit;
                        }
                    }
                }
            }

            $master->store(); // save master object
            // delete details
            $old_items = EstoquemovLote::where('estoquemov_id', '=', $master->id)->load();
            
            $keep_items = array();
            
            // get session items
            $items = TSession::getValue(__CLASS__.'_items');
            
            if( $items )
            {
                foreach( $items as $item )
                {
                    if (substr($item['id'],0,1) == 'X' ) // new record
                    {
                        $detail = new EstoquemovLote;
                    }
                    else
                    {
                        $detail = EstoquemovLote::find($item['id']);
                    }
                    $detail->lote  = $item['lote'];
                    $detail->vencimento  = $item['vencimento'];
                    $detail->quantidade  = $item['quantidade'];
                    $detail->estoquemov_id = $master->id;

                    $detail->unit_id = $master->unit_id;
                    $detail->produto_id = $master->produto_id;
                    $detail->local = $master->local;
                    $detail->tipo = $master->tipo;

                    $detail->store();
                    
                    $keep_items[] = $detail->id;
                }
            }
            
            if ($old_items)
            {
                foreach ($old_items as $old_item)
                {
                    if (!in_array( $old_item->id, $keep_items))
                    {
                        $old_item->delete();
                    }
                }
            }

            TTransaction::close(); // close the transaction
            
            // reload form and session items
            $this->onEdit(array('key'=>$master->id));
            
            $action = new TAction([__CLASS__, 'onEdit']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $action);
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
    
    /**
     * Show the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}