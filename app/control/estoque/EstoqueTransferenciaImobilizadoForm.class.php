<?php
/**
 * EstoqueTransferenciaImobilizadoForm Master/Detail
 * @author  Fred Azv.
 */
class EstoqueTransferenciaImobilizadoForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_EstoqueTransferenciaImobilizado');
        $this->form->setFormTitle('Transferência de Imobilizado');
        $this->form->setFieldSizes('100%');
        
        // master fields
        $id = new TEntry('id');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $local_origem = new TDBCombo('local_origem','sample','Viewlocal','id','nome_fantasia','nome_fantasia');
        $local_origem->enableSearch();
        $local_origem->addValidation('Local de origem', new TRequiredValidator);

        $local_destino = new TDBCombo('local_destino','sample','Viewlocal','id','nome_fantasia','nome_fantasia');
        $local_destino->enableSearch();
        $local_destino->addValidation('Local de destino', new TRequiredValidator);

        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userid'));
        $user_id->setEditable(FALSE);

        // detail fields
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');

        //$detail_produto_id = new TDBUniqueSearch('detail_produto_id', 'sample', 'Produto', 'id', 'nome_produto');
        $detail_produto_id = new TDBCombo('detail_produto_id', 'sample', 'Produto', 'id','{nome_produto} - {cod_referencia}','nome_produto');
        //$detail_produto_id->setMask('{cod_referencia} - {nome_produto}');
        $detail_produto_id->enableSearch();
        //$detail_produto_id->addValidation('Produto', new TRequiredValidator);

        $detail_estado = new TCombo('detail_estado');
        $combo['1'] = 'Novo';
        $combo['2'] = 'Usado';
        $combo['3'] = 'Depreciado';
        $detail_estado->addItems($combo);
        //$detail_estado->addValidation('Estado do produto', new TRequiredValidator);

        $detail_data_avaliacao = new TDate('detail_data_avaliacao');
        $detail_data_avaliacao->setValue(date("d-m-Y hh:ii"));
        $detail_data_avaliacao->setDatabaseMask('yyyy-mm-dd');
        $detail_data_avaliacao->setMask('dd/mm/yyyy');

        $detail_valor_justo = new TNumeric('detail_valor_justo', 2, ',', '.', true);

        $detail_emplacamento = new TEntry('detail_emplacamento');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Usuário'), $user_id ]
        );
        $row->layout = ['col-sm-2','col-sm-5', 'col-sm-5'];

        $row = $this->form->addFields( [ new TLabel('Local de origem'), $local_origem ],
                                       [ new TLabel('Local de destino'), $local_destino ]);
        $row->layout = ['col-sm-6','col-sm-6'];
        
        // detail fields
        $this->form->addContent( ['<h4>Itens do Imobilizado</h4><hr>'] );
        $this->form->addFields( [$detail_uniqid] );
        $this->form->addFields( [$detail_id] );
        
        $row = $this->form->addFields( [ new TLabel('Produto'), $detail_produto_id ]
        );
        $row->layout = ['col-sm-12'];


        $row = $this->form->addFields( [ new TLabel('Emplacamento / Tombo'), $detail_emplacamento ],
                                       [ new TLabel('Estado de conservação'), $detail_estado ],
                                       [ new TLabel('Data Avaliação'), $detail_data_avaliacao ],
                                       [ new TLabel('Valor Justo'), $detail_valor_justo ]
        );
        $row->layout = ['col-sm-6','col-sm-2','col-sm-2','col-sm-2'];


        $add = TButton::create('add', [$this, 'onDetailAdd'], 'Register', 'fa:plus-circle green');
        $add->getAction()->setParameter('static','1');
        $add->style = 'background-color: #b3d9ff';
        $row = $this->form->addFields([$add])->style = 'padding: 5px 0px 0px 0px;';
        
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->setId('EstoqueTransferenciaImobilizadoItem_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        
        // items
        $this->detail_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
        $this->detail_list->addColumn($col_produto_id = new TDataGridColumn('produto_id', 'Produto', 'left', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('emplacamento', 'Emplacamento / Tombo', 'left', 100) );
        $this->detail_list->addColumn($col_estado =  new TDataGridColumn('estado', 'Estado', 'left', 100) );
        $this->detail_list->addColumn($column_data_avaliacao = new TDataGridColumn('data_avaliacao', 'Data Avaliação', 'center', 50) );
        $this->detail_list->addColumn( new TDataGridColumn('valor_justo', 'Valor Justo', 'right', 100) );
        
        $col_produto_id->setTransformer(function($value) {
            TTransaction::open('sample');
            $name = new ProdutoNome($value);
            TTransaction::close();
            return $name->nome_concatenado;
            //return ProdutoNome::findInTransaction('sample', $value)->nome_concatenado;
        });

        $col_estado->setTransformer(function($value) {
            
            if($value == 1){
                return 'Novo';
            }elseif ($value == 2) {
                return 'Usado';
            }else{
                return 'Depreciado';
            }
        });

        $column_data_avaliacao->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        // detail actions
        $action1 = new TDataGridAction([$this, 'onDetailEdit'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDetailDelete']);
        $action2->setField('uniqid');
        
        // add the actions to the datagrid
        $this->detail_list->addAction($action1, _t('Edit'), 'fa:edit blue');
        $this->detail_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
        $this->form->addAction( 'Save',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        $this->form->addAction( 'Voltar', new TAction(['EstoqueTransferenciaImobilizadoList', 'onReload']), 'fa:eraser red');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    

    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    
 
    public function onDetailAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
          
            if (empty($data->detail_estado))
            {
                throw new Exception('O campo Estado é obrigatório!');
            }

            if (empty($data->detail_produto_id))
            {
                throw new Exception('O campo Produto é obrigatório!');
            }
            
            if (empty($data->detail_data_avaliacao))
            {
                throw new Exception('O campo Data Avaliação é obrigatório!');
            }

            if (empty($data->detail_valor_justo))
            {
                throw new Exception('O campo Valor Justo é obrigatório!');
            }

            if (empty($data->detail_emplacamento))
            {
                throw new Exception('O campo Emplacamento é obrigatório!');
            }
            
            $uniqid = !empty($data->detail_uniqid) ? $data->detail_uniqid : uniqid();
            
            $grid_data = [];
            $grid_data['uniqid'] = $uniqid;
            $grid_data['id'] = $data->detail_id;
            $grid_data['produto_id'] = $data->detail_produto_id;
            $grid_data['estado'] = $data->detail_estado;
            $grid_data['data_avaliacao'] = $data->detail_data_avaliacao;
            $grid_data['valor_justo'] = $data->detail_valor_justo;
            $grid_data['emplacamento'] = $data->detail_emplacamento;
            
            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('EstoqueTransferenciaImobilizadoItem_list', $uniqid, $row);
            
            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_produto_id = '';
            $data->detail_estado = '';
            $data->detail_data_avaliacao = '';
            $data->detail_valor_justo = '';
            $data->detail_emplacamento = '';
            
            TForm::sendData( 'form_EstoqueTransferenciaImobilizado', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    

    public static function onDetailEdit( $param )
    {
        $data = new stdClass;
        $data->detail_uniqid = $param['uniqid'];
        $data->detail_id = $param['id'];
        $data->detail_produto_id = $param['produto_id'];
        $data->detail_estado = $param['estado'];
        $data->detail_data_avaliacao = $param['data_avaliacao'];
        $data->detail_valor_justo =Utilidades::formatar_valor($param['valor_justo']);
        $data->detail_emplacamento = $param['emplacamento'];
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_EstoqueTransferenciaImobilizado', $data, false, false );
    }
    

    public static function onDetailDelete( $param )
    {
        // clear detail form fields
        $data = new stdClass;
        $data->detail_uniqid = '';
        $data->detail_id = '';
        $data->detail_produto_id = '';
        $data->detail_estado = '';
        $data->detail_data_avaliacao = '';
        $data->detail_valor_justo = '';
        $data->detail_emplacamento = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_EstoqueTransferenciaImobilizado', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('EstoqueTransferenciaImobilizadoItem_list', $param['uniqid']);
    }
    

    public function onEdit($param)
    {
        try
        {
            TTransaction::open('sample');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new EstoqueTransferenciaImobilizado($key);
                $items  = EstoqueTransferenciaImobilizadoItem::where('estoque_transferencia_imobilizado_id', '=', $key)->load();
                
                foreach( $items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public function onSave($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('sample');
            
            $data = $this->form->getData();
            $this->form->validate();
            
            $master = new EstoqueTransferenciaImobilizado;
            $master->fromArray( (array) $data);
            $master->store();
            
            EstoqueTransferenciaImobilizadoItem::where('estoque_transferencia_imobilizado_id', '=', $master->id)->delete();
            
            if( $param['EstoqueTransferenciaImobilizadoItem_list_produto_id'] )
            {
                foreach( $param['EstoqueTransferenciaImobilizadoItem_list_produto_id'] as $key => $item_id )
                {
                    $detail = new EstoqueTransferenciaImobilizadoItem;
                    $detail->produto_id  = $param['EstoqueTransferenciaImobilizadoItem_list_produto_id'][$key];
                    $detail->estado  = $param['EstoqueTransferenciaImobilizadoItem_list_estado'][$key];
                    $detail->data_avaliacao  = $param['EstoqueTransferenciaImobilizadoItem_list_data_avaliacao'][$key];
                    $detail->valor_justo  = $param['EstoqueTransferenciaImobilizadoItem_list_valor_justo'][$key];
                    $detail->emplacamento  = $param['EstoqueTransferenciaImobilizadoItem_list_emplacamento'][$key];
                    $detail->estoque_transferencia_imobilizado_id = $master->id;
                    $detail->store();
                }
            }
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_EstoqueTransferenciaImobilizado', (object) ['id' => $master->id]);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
}
