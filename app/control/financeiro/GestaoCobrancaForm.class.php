<?php
/**
 * GestaoCobrancaForm Registration
 * @author  Fred Azevedo
 */
class GestaoCobrancaForm extends TPage
{
    protected $form;
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('sample');
        $this->setActiveRecord('ContaReceber');
        //parent::setTitle('Gerar Recibo');
        //parent::setSize(0.7, 0.7);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_GestaoCobrancaAtualizar');
        $this->form->setFormTitle('');
        $this->form->setFieldSizes('100%');

        $id = new TDBCombo('id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $id->setEditable(FALSE);

        $row = $this->form->addFields( [ new TLabel('Cliente'), $id ],
                                       [ new TLabel(''),  ]
        
        );
        $row->layout = ['col-mg-6','col-mg-6'];

        $this->form->addAction('Atualizar',  new TAction([$this, 'onAtualizar']), 'fas:eraser red');

        $this->form->addAction('Voltar',  new TAction(['GestaoCobrancaList', 'onReload']), 'fa:angle-double-left');

    
        $btn_infocontareceber = TButton::create('infocontareceber', [$this, 'onHistory'], 'Gerar Histórico', 'fas:plus white');
        $btn_infocontareceber->class = 'btn btn-warning btn-lg';

        $this->form->addFields( [$btn_infocontareceber]);

        //======================================================================

        $this->form->setData( TSession::getValue('GestaoCobrancaForm_filter_data') );

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid());
        $this->datagrid->setHeight(300);
        $this->datagrid->makeScrollable();
        $this->datagrid->style = 'width: 100%';

        //$column_select = new TDataGridColumn('select', '', 'left', '0%');
        $column_contas_receber_id = new TDataGridColumn('id', 'Conta Receber', 'left','10%');
        //$column_id->setTransformer([$this, 'formatRow'] );
        $column_data_vencimento = new TDataGridColumn('data_vencimento', 'Vencimento', 'right', '20%');
        $column_multa = new TDataGridColumn('multa', 'Multa', 'right', '20%');
        $column_juros = new TDataGridColumn('juros', 'Juros', 'right', '20%');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right', '20%');
        $column_valor_pago = new TDataGridColumn('valor_pago', 'Valor Devido', 'right', '20%');

        //$this->datagrid->addColumn($column_select);
        $this->datagrid->addColumn($column_contas_receber_id);
        $this->datagrid->addColumn($column_data_vencimento);
        $this->datagrid->addColumn($column_multa);
        $this->datagrid->addColumn($column_juros);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_valor_pago);
//      $this->datagrid->addAction($action1);

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

        $column_multa->setTransformer( $format_value );
        $column_juros->setTransformer( $format_value );
        $column_valor->setTransformer( $format_value );
        $column_valor_pago->setTransformer( $format_value );

        $column_valor_pago->setTotalFunction( function($values) {
            $total = array_sum((array) $values);
            $total = Utilidades::formatar_valor($total); //(is_numeric($total)) ? round($total,2) : 0;
            return '<div id="total_devido"> <b>Total devido: R$</b> ' . $total  . '</div>';
        });

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        //======================================================================
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add($this->form);
        $container->add(TPanelGroup::pack($this->form, $this->datagrid));
        
        parent::add($container);

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

        TForm::sendData( 'form_GestaoCobrancaAtualizar', $data );

        $vlr = $total_devido;
    }

    public function onHistory( $param )
    {

        $cliente_id = $param['cliente_id'] ?? $param['id'];

        TSession::setValue('clienteid', $cliente_id);
        Adianti\Core\AdiantiCoreApplication::loadPage('ClienteCobrancaForm');
        
    }

    public function onAtualizar( $param )
    {   
        try
        {
            TTransaction::open('sample'); // open a transaction

            $cliente_id = $param['id'];
            $data_atual = date('Y-m-d');

            TTransaction::open('sample'); // open a transaction

            $cr = ContaReceber::where('cliente_id','=',$cliente_id)
                              ->where('data_vencimento','<',$data_atual)->load();

            if($cr)
            {

                foreach ($cr as $value) {
                
                    $pegarBanco =  BoletoRegra::where('conta_bancaria_id', '=', $value->conta_bancaria_id)->first();

                    $multa = $pegarBanco->valor_multa / 100;
                    $valorReplace = $value->valor;
                    $valorMulta = $multa * $value->valor;
                    

                    $valorJuros = $pegarBanco->valor_juros;
                    $valorJurosDia = $valorJuros / 30;

                    //diferença de dias em atraso
                    $datavencimento = $value->data_vencimento;
                    $datahoje = date('Y-m-d');

                    $data1 = new DateTime($datavencimento); 
                    $data2 = new DateTime($datahoje); 

                    $diasAtrasados = $data1->diff ($data2); 
                    //var_dump($diasAtrasados);
                    $valorJurosAtual = $valorJurosDia * $diasAtrasados->days;

                    $totalgeral = $valorMulta + $valorJurosAtual + $valorReplace;

                    //parent::add(new TAlert('danger',$diasAtrasados->days.' dia(s) de atraso e o valor de R$ '.number_format($totalgeral, 2, ',', '.')));

                    $up = new ContaReceber($value->id);
                    $up->multa = $valorMulta;
                    $up->juros = $valorJurosAtual;
                    $up->valor_pago = $totalgeral;
                    $up->data_cobranca = date('Y-m-d');
                    $up->store();

                    //$pos_action = new TAction(['GestaoCobrancaList', 'onReload']);
                    //new TMessage('info', 'Atualização de valor realizada com Sucesso!');
                }
            }
        
            $object = new Cliente($cliente_id);
            $this->form->setData($object);
            TTransaction::close();  
            self::onReload($param);

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }

    }

    public function onEdit( $param )
    {   

        try
        {
            //TQuickForm::hideField('form_GestaoCobrancaAtualizar', 'contas_receber_id');

            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('sample'); // open a transaction
                $object = new Cliente($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
                $this->onReload( $param );
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
        
    }

    public function onReload($param)
    { 
        try
        {   
            $id = ($param['cliente_id']) ?? $param['id'];
            
            TTransaction::open('sample');
            
            // creates a repository for SystemDocument
            $repository = new TRepository('ContaReceber');
            $limit = 100;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'data_vencimento';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            $conta = ContaReceber::where('cliente_id','=',$id)->first();
            $cliente_id = $conta->cliente_id;

            if (!$cliente_id) {
                TTransaction::close();
                $this->loaded = true;
                return;
            }
            $data_atual = date('Y-m-d');
            $criteria->add(new TFilter('cliente_id', '=', $cliente_id)); // add the session
            $criteria->add(new TFilter('data_vencimento', '<', $data_atual));
            $criteria->add(new TFilter('juridico','=',"N"));
            $criteria->add(new TFilter('baixa', '=', "N"));
            //$criteria->add(TSession::getValue('GestaoCobrancaForm_filter_data')); // add the session

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }

            $fields = $this->form->getFields();
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            $this->form->setFields($fields);
            
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

}
