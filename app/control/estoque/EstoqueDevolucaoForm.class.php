<?php
/**
 * EstoqueDevolucaoForm Master/Detail
 * @author  Fred Azv.
 */
class EstoqueDevolucaoForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_EstoqueDevolucao');
        $this->form->setFormTitle('Devolução de Produtos');
        $this->form->setFieldSizes('100%');
        
        // master fields
        $id = new TEntry('id');

        $data_devolucao = new TDate('data_devolucao');
        $data_devolucao->setValue(date("d-m-Y hh:ii"));
        $data_devolucao->setDatabaseMask('yyyy-mm-dd');
        $data_devolucao->setMask('dd/mm/yyyy');

        $hora_devolucao = new TTime('hora_devolucao');
        $hora_devolucao->setValue(date("h:i"));
        
        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $responsavel_id = new TDBCombo('responsavel_id','sample','SystemUser','id','name','name',$id_user_session);
        $responsavel_id->setValue(TSession::getValue('userid'));
        $responsavel_id->setEditable(FALSE);
        
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
        $user_id->addValidation('Técnico que esta devolvendo', new TRequiredValidator);

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);
        $observacao = new TText('observacao');

        // detail fields
        $detail_id = new THidden('detail_id');

        $unit_produto = new TCriteria();
        $unit_produto->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $detail_produto_id = new TDBUniqueSearch('detail_produto_id', 'sample', 'Produto', 'id','nome_produto','cod_referencia', $unit_produto);
        $detail_produto_id->setMask('{nome_produto} - {fabricante} - {modelo}');
        //$detail_produto_id->addValidation('Produto', new TRequiredValidator);

        $detail_quantidade = new TEntry('detail_quantidade');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // master fields
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Data Devolução'), $data_devolucao ],
                                       [ new TLabel('Hora Devolução'), $hora_devolucao ]);
        $row->layout = ['col-sm-2','col-sm-4','col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Responsavel pelo Estoque'), $responsavel_id ],
                                       [ new TLabel('Técnico que esta devolvendo'), $user_id ]);
        $row->layout = ['col-sm-6','col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Observação'), $observacao ]);
        $row->layout = ['col-sm-12'];
        
        // detail fields
        $this->form->addContent( ['<h4>Produtos</h4><hr>'] );
        $this->form->addFields( [$detail_id] );

        $row = $this->form->addFields( [ new TLabel('Produto devolvido'), $detail_produto_id ],
                                       [ new TLabel('Quantidade'), $detail_quantidade ]);
        $row->layout = ['col-sm-10','col-sm-2'];

        $add = TButton::create('add', [$this, 'onSaveDetail'], 'Adicionar Produtos', 'fa:save');
        $this->form->addFields( [], [$add] )->style = 'background: whitesmoke; padding: 5px; margin: 1px;';
        
        $this->detail_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->detail_list->setId('EstoqueDevolucao_list');
        
        // items
        $this->detail_list->addQuickColumn('Produto', 'produto_nome', 'left', 100);
        $this->detail_list->addQuickColumn('Quantidade', 'quantidade', 'right', 100);

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
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'EstoqueDevolucaoList'));
        $container->add($this->form);
        parent::add($container);
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

            $items = TSession::getValue(__CLASS__.'_items');
            $key = empty($data->detail_id) ? 'X'.mt_rand(1000000000, 1999999999) : $data->detail_id;
            
            $produto = new Produto($data->detail_produto_id);    
            $items[ $key ] = array();   
            $items[ $key ]['id'] = $key;
            $items[ $key ]['produto_nome'] = $produto->nome_produto;
            $items[ $key ]['produto_id'] = $data->detail_produto_id;
            $items[ $key ]['quantidade'] = $data->detail_quantidade;
            
            TSession::setValue(__CLASS__.'_items', $items);
            
            // clear detail form fields
            $data->detail_id = '';
            $data->detail_produto_id = '';
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
        $data->detail_produto_id = $item['produto_id'];
        $data->detail_quantidade = $item['quantidade'];
        
        // fill detail fields
        TForm::sendData( 'form_EstoqueDevolucao', $data );
    }
    
    /**
     * Delete an item from session list
     * @param $param URL parameters
     */
    public static function onDeleteDetail( $param )
    {
        // reset items
        $data = new stdClass;
            $data->detail_produto_id = '';
            $data->detail_quantidade = '';
        
        // clear form data
        TForm::sendData('form_EstoqueDevolucao', $data );
        
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        // get detail id
        $detail_id = $param['key'];
        
        // delete the item from session
        unset($items[ $detail_id ] );
        
        // rewrite session items
        TSession::setValue(__CLASS__.'_items', $items);
        
        // delete item from screen
        TScript::create("ttable_remove_row_by_id('EstoqueDevolucao_list', '{$detail_id}')");
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
                
                $object = new EstoqueDevolucao($key);
                $items  = EstoqueDevolucaoItens::where('estoque_devolucao_id', '=', $key)->load();
                
                $session_items = array();
                foreach( $items as $item )
                {
                    $produto = new Produto($item->produto_id); 
                    $item_key = $item->id;
                    $session_items[$item_key] = $item->toArray();
                    $session_items[$item_key]['produto_nome'] = $item->produto->nome_produto;
                    $session_items[$item_key]['id'] = $item->id;
                    $session_items[$item_key]['produto_id'] = $item->produto_id;
                    $session_items[$item_key]['quantidade'] = $item->quantidade;
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
            $master = new EstoqueDevolucao;
            $master->fromArray( (array) $data);
            $this->form->validate(); // form validation
            
            $master->store(); // save master object
            // delete details
            $old_items = EstoqueDevolucaoItens::where('estoque_devolucao_id', '=', $master->id)->load();
            
            $keep_items = array();
            
            // get session items
            $items = TSession::getValue(__CLASS__.'_items');
            
            if( $items )
            {
                foreach( $items as $item )
                {
                    if (substr($item['id'],0,1) == 'X' ) // new record
                    {
                        $detail = new EstoqueDevolucaoItens;
                    }
                    else
                    {
                        $detail = EstoqueDevolucaoItens::find($item['id']);
                    }
                    $detail->produto_id  = $item['produto_id'];
                    $detail->quantidade  = $item['quantidade'];
                    $detail->estoque_devolucao_id = $master->id;
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
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
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
