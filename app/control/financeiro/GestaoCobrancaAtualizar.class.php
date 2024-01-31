<?php
/**
 * GestaoCobrancaAtualizar Registration
 * @author  Fred Azevedo
 */
class GestaoCobrancaAtualizar extends TPage
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

        $id = new TEntry('id');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));

        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userunitid'));
        $user_id->addValidation('Usuário', new TRequiredValidator);
        //$user_id->setEditable(FALSE);
        
        $cliente_id = new TDBCombo('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $cliente_id->setEditable(FALSE);

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);

        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->addValidation('Data de Vencimento', new TRequiredValidator);
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');
        $data_vencimento->setEditable(FALSE);

        $data_conta = new TDate('data_conta');
        $data_conta->setValue(date('d/m/Y'));
        $data_conta->addValidation('Competência', new TRequiredValidator);
        $data_conta->setDatabaseMask('yyyy-mm-dd');
        $data_conta->setMask('dd/mm/yyyy');
        $data_conta->setEditable(FALSE);

        $documento = new TEntry('documento');
        $documento->setEditable(FALSE);

        $data_baixa = new TDate('data_baixa');
        $data_baixa->setValue(date('d/m/Y'));
        $data_baixa->addValidation('Data da Baixa', new TRequiredValidator);
        $data_baixa->setDatabaseMask('yyyy-mm-dd');
        $data_baixa->setMask('dd/mm/yyyy');

        $multa = new TNumeric('multa',2,',','.',true);
        $multa->setValue(date('0.00'));
        $multa->setEditable(FALSE);
        $juros = new TNumeric('juros',2,',','.',true);
        $juros->setEditable(FALSE);
        $juros->setValue(date('0.00'));
        $valor = new TNumeric('valor',2,',','.',true);
        $valor->addValidation('Valor', new TRequiredValidator);
        $valor->setEditable(FALSE);

        $valor_pago = new TNumeric('valor_pago',2,',','.',true);
        $valor_pago->addValidation('Valor Pago', new TRequiredValidator);
        $valor_pago->setEditable(FALSE);

        $desconto = new TNumeric('desconto',2,',','.',true);
        $desconto->setValue(date('0.00'));
        $desconto->setEditable(FALSE);
        $observacao = new TText('observacao');

        $juridico = new TCombo('juridico');
        $combo_juridico = array();
        $combo_juridico['S'] = 'Sim';
        $combo_juridico['N'] = 'Não';
        $juridico->addItems($combo_juridico);

        $row = $this->form->addFields( [ new TLabel('Cliente'), $cliente_id ],
                                       [ new TLabel('Usuário'), $user_id ]
        );
        $row->layout = ['col-sm-6','col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Conta Bancária'), $conta_bancaria_id ]);
        $row->layout = ['col-sm-2','col-sm-10'];


        $row = $this->form->addFields( [ new TLabel('Vencimento'), $data_vencimento ],
                                       [ new TLabel('Data da Conta'), $data_conta ],
                                       [ new TLabel('Data da Baixa'), $data_baixa ],
                                       [ new TLabel('Documento'), $documento ]);
        $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Multa'), $multa ],
                                       [ new TLabel('Juros'), $juros ],
                                       [ new TLabel('Desconto'), $desconto ],
                                       [ new TLabel('Valor da Conta'), $valor ],
                                       [ new TLabel('Valor Pago'), $valor_pago ]
        );
        $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Jurídico ?'), $juridico ]
        );
        $row->layout = ['col-sm-2'];


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
         
        $this->form->addAction('Atualizar Valor',  new TAction([$this, 'onAtualizar']), 'fa:eraser red');

        $this->form->addAction('Voltar',  new TAction(['GestaoCobrancaList', 'onReload']), 'fa:angle-double-left');

        //$this->form->addAction('Gerar Histórico',  new TAction(['ContaReceberCobrancasForm', 'onEdit']), 'fa:angle-double-left');

        $btn_infocontareceber = TButton::create('infocontareceber', [$this, 'onHistory'], 'Gerar Histórico', 'fa:plus white');
        $btn_infocontareceber->class = 'btn btn-warning btn-lg';

        $this->form->addFields( [$btn_infocontareceber]);

        //======================================================================

        $this->form->setData( TSession::getValue('GestaoCobrancaAtualizar_filter_data') );

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

        $multa->onBlur = 'calc()';
        $juros->onBlur = 'calc()';
        $desconto->onBlur = 'calc()';
        
        TScript::create('calc = function() {
                
                let multa = convertToFloatNumber(form_GestaoCobrancaAtualizar.multa.value);
                let juros = convertToFloatNumber(form_GestaoCobrancaAtualizar.juros.value);
                let desconto = convertToFloatNumber(form_GestaoCobrancaAtualizar.desconto.value);
                let valor = convertToFloatNumber(form_GestaoCobrancaAtualizar.valor.value);

                valorTitulo = parseFloat(multa) + parseFloat(juros) - parseFloat(desconto);
                
                let total = valor + valorTitulo;

                total = formatMoney(total);        
                form_GestaoCobrancaAtualizar.valor_pago.value = total;
            };    

            function formatMoney (number, decimal, separatord, separatort) {
                var n = number,
                    c = isNaN(decimal = Math.abs(decimal)) ? 2 : decimal,
                    d = separatord == undefined ? "," : separatord,
                    t = separatort == undefined ? "." : separatort,
                    s = n < 0 ? "-" : "",
                    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };

            function convertToFloatNumber(value) {
                value = value.toString();
                if (value.indexOf(\'.\') !== -1 || value.indexOf(\',\') !== -1) {
                    if (value.indexOf(\'.\') >  value.indexOf(\',\')) {
                        return parseFloat(value.replace(/,/gi,\'\'));
                    } else {
                        return parseFloat(value.replace(/\./gi,\'\').replace(/,/gi,\'.\'));
                    }
                } else {
                    return parseFloat(value);
                }
            };

        ');
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

        $conta_receber_id = $param['id'];
        $cliente_id = $param['cliente_id'];
        
        if ($conta_receber_id == null) {
            new TMessage('error', 'Não foi possível encontrar o número do Contas a Receber');
            return;
        }

        TSession::setValue('contasreceber', $conta_receber_id);
        TSession::setValue('clienteid', $cliente_id);
        Adianti\Core\AdiantiCoreApplication::loadPage('ContaReceberCobrancasForm');
        
    }

    public function onAtualizar( $param )
    {   
        try
        {
            TTransaction::open('sample'); // open a transaction

            $key = $param['id'];  // get the parameter $key
            $cliente_id = $param['cliente_id'];
            $data_vencimento_input = $param['data_vencimento'];
            $data_parte = explode("/", $data_vencimento_input);
            $data_vencimento = $data_parte[2] . "-" . $data_parte[1] . "-" . $data_parte[0];
            $data_atual = date('Y-m-d');

            if($data_vencimento < date('Y-m-d'))
            {

            TTransaction::open('sample'); // open a transaction

            $cr = ContaReceber::where('cliente_id','=',$cliente_id)
                              ->where('data_vencimento','<',$data_atual)->load();

                if($cr)
                {

                    foreach ($cr as $value) {
                    
                            $pegarBanco =  BoletoRegra::where('conta_bancaria_id', '=', $param['conta_bancaria_id'])->first();

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
                else
                {
                    new TMessage('info', 'A esse título não esta vencido!');
                }
            }   
            $object = new ContaReceber($key);
            $this->form->setData($object);
            TTransaction::close();  
            self::onReload($param);

        }
        catch (Exception $e) // in case of exception
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
                $object = new ContaReceber($key); // instantiates the Active Record
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
            $id = $param['key'] ?? $param['id'];
            // open a transaction with database 'communication'
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

            $conta = ContaReceber::where('id','=',$id)->first();
            $cliente_id = $conta->cliente_id;

            if (!$cliente_id) {
                TTransaction::close();
                $this->loaded = true;
                return;
            }
            $data_atual = date('Y-m-d');
            $criteria->add(new TFilter('cliente_id', '=', $cliente_id)); // add the session
            $criteria->add(new TFilter('data_vencimento', '<', $data_atual));
            //$criteria->add(TSession::getValue('GestaoCobrancaAtualizar_filter_data')); // add the session

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
