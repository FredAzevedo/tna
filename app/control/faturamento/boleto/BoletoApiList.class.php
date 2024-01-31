<?php
/**
 * BoletoApiList 
 * @author  Fred Azv.
 */
class BoletoApiList extends TPage
{
    private $form;
    private $datagrid; 
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_search_BoletoApi');
        $this->form->setFormTitle('BoletoApi');
        $this->form->setFieldSizes('100%');
    
        $vencimento = new TDate('vencimento');
        $vencimento->setDatabaseMask('yyyy-mm-dd');
        $vencimento->setMask('dd/mm/yyyy');
        $valor = new TEntry('valor');
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'razao_social');
        $pedido_numero = new TEntry('pedido_numero');
        $status = new TEntry('status');

        $user_id = new TDBUniqueSearch('user_id', 'sample', 'SystemUser', 'id', 'name');

        $row = $this->form->addFields( [ new TLabel('Vencimento'), $vencimento ],    
                                       [ new TLabel('Valor'), $valor ],
                                       [ new TLabel('Cliente'), $cliente_id ],
                                       [ new TLabel('Referência'), $pedido_numero ],
                                       [ new TLabel('Nº do Status'), $status ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-4', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        // $row = $this->form->addFields( [ new TLabel('Usuário'), $user_id ]
        // );
        // $row->layout = ['col-sm-12'];

        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['BoletoApiForm', 'onEdit']), 'fa:plus green');
        $this->form->addActionLink('Consulta', new TAction([$this, 'onConsultar']), 'fa:plus green');

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        //$column_unit_id = new TDataGridColumn('unit_id', 'Unit Id', 'right');
        $column_vencimento = new TDataGridColumn('vencimento', 'Vencimento', 'center');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');
        $column_valor_pago = new TDataGridColumn('valor_pago', 'Pago', 'right');
        $column_juros = new TDataGridColumn('juros', 'Juros', 'right');
        $column_multa = new TDataGridColumn('multa', 'Multa', 'right');
        $column_cliente_id = new TDataGridColumn('cliente->razao_social', 'Cliente', 'left');
        $column_pedido_numero = new TDataGridColumn('pedido_numero', 'Referência', 'right');
        $column_status = new TDataGridColumn('status', 'Status', 'center');
        $column_msg = new TDataGridColumn('msg', 'Msg', 'left');
        $column_nossonumero = new TDataGridColumn('nossonumero', 'Nosso Número', 'left');
        $column_contrato = new TDataGridColumn('contrato', 'Nº', 'left');

        $column_id->setTransformer(array($this, 'formatColor'));
        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_cliente_id);
        //$this->datagrid->addColumn($column_unit_id);
        $this->datagrid->addColumn($column_vencimento);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_valor_pago);
        $this->datagrid->addColumn($column_juros);
        $this->datagrid->addColumn($column_multa);
        $this->datagrid->addColumn($column_pedido_numero);
        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_msg);
        $this->datagrid->addColumn($column_nossonumero);
        $this->datagrid->addColumn($column_contrato);

        $column_vencimento->setTransformer( function($value, $object, $row) {
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
        $column_valor_pago->setTransformer( $format_value );
        // $column_juros->setTransformer( $format_value );
        // $column_multa->setTransformer( $format_value );

        $btn1 = new TDataGridAction(array($this, 'onGerar'));
        $btn1->setLabel('Gerar Boleto ou 2º via');
        //$btn1->setImage('fas:sort-amount-up-alt black');
        $btn1->setField('id');

        // $btn2 = new TDataGridAction(array($this, 'onCancelarBoleto'));
        // $btn2->setLabel('Cancelar Boleto');
        // //$btn2->setImage('fas:sort-amount-up-alt black');
        // $btn2->setField('id');

        $btn3 = new TDataGridAction(array('ApiImprimeBoleto', 'onSavePDF'));
        $btn3->setLabel('Imprimir Boleto');
        //$btn3->setImage('fas:sort-amount-up-alt black');
        $btn3->setField('id');

        $btn4 = new TDataGridAction(array($this, 'onLinkExterno'));
        $btn4->setLabel('Enviar por WhatsApp');
        //$btn4->setImage('fas:sort-amount-up-alt black');
        $btn4->setField('id');

        $btn5 = new TDataGridAction(array($this, 'onConsultarBoleto'));
        $btn5->setLabel('Consultar Situação');
        //$btn5->setImage('fas:sort-amount-up-alt black');
        $btn5->setField('id');

        $action_group = new TDataGridActionGroup('Ações', 'fas:cog');

        $action_group->addHeader('Opções');
        $action_group->addAction($btn1);
        
        $action_group->addAction($btn3);
        $action_group->addAction($btn4);
        $action_group->addAction($btn5);
        //$action_group->addAction($btn2);

        $this->datagrid->addActionGroup($action_group);

        $action1 = new TDataGridAction(['BoletoApiForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
    
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public function formatColor($column_id, $object, $row)
    {
        $data = new BoletoApi($column_id);
        //var_dump($data->valor_pago);
        if ($data->valor_pago != "0.00")
        {
            $row->style = "background: #97f498";
            return "<span>$column_id</span>";
        }else{
            return "<span>$column_id</span>";
        }
    }

    public static function onConsultar()
    {
        try
        {   
            TTransaction::open('sample');

            $credencialUnit = ApiIntegracao::where('unit_id','=',TSession::getValue('userunitid'))->where('tipo','=','1')->first();
            $boletos = BoletoApi::where('valor_pago','=','0.00')->load();
    
            if($boletos){
                
                foreach( $boletos as $boleto) {
                    
                    $b = new BoletoApi($boleto->id);
                    $b->credencial = $credencialUnit->credencial;
                    $b->ambiente = $credencialUnit->producao;
                    $b->chave = $credencialUnit->chave;

                    $api = PJBankApi::consultarBoletos($b);
                    $return = json_decode($api);

                    if($return){
                        foreach( $return as $item) {
                            if($item->valor_pago != null){
                                $b->valor_pago = $item->valor_pago;
                                $b->valor_liquido = $item->valor_liquido;
                                $b->valor_tarifa = $item->valor_tarifa;

                                $date1 = explode("/",$item->data_pagamento);
                                $data_pagamento = $date1[2] . "" . $date1[0] . "" . $date1[1];
                                $b->data_pagamento = $data_pagamento;

                                $data_credito = $item->data_credito;//"07/26/2018",
                                $date2 = explode("/",$item->data_credito);
                                $data_credito = $date2[2] . "" . $date2[0] . "" . $date2[1];
                                $b->data_credito = $data_credito;
                                $b->registro_sistema_bancario = $item->registro_sistema_bancario;
                                $b->store();
                            }
                        }
                    }
                }

                $action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', 'Consulta gerada com sucesso!',  $action);
            }

            TTransaction::close();

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    

    }

    public static function onConsultarBoleto($param)
    {

        $id = $param['id'];
        TTransaction::open('sample');
        
        try
        {
            $boleto = new BoletoApi($id);
            if($boleto->nossonumero){
                $credencialUnit = ApiIntegracao::where('unit_id','=',$boleto->unit_id)->where('tipo','=','1')->first();

                $boleto->credencial = $credencialUnit->credencial;
                $boleto->ambiente = $credencialUnit->producao;
                $boleto->chave = $credencialUnit->chave;
                
                $api = PJBankApi::consultarBoletos($boleto);
                $return = json_decode($api);
                //VarPre::onTest($boleto);
                if($return[0]->valor_pago != 0.00){

                    $boleto->valor_pago = $return[0]->valor_pago;
                    $boleto->valor_liquido = $return[0]->valor_liquido;
                    $boleto->valor_tarifa = $return[0]->valor_tarifa;

                    $date1 = explode("/",$return[0]->data_pagamento);
                    $data_pagamento = $date1[2] . "" . $date1[0] . "" . $date1[1];
                    $boleto->data_pagamento = $data_pagamento;

                    $data_credito = $return[0]->data_credito;//"07/26/2018",
                    $date2 = explode("/",$return[0]->data_credito);
                    $data_credito = $date2[2] . "" . $date2[0] . "" . $date2[1];
                    $boleto->data_credito = $data_credito;

                    $boleto->registro_sistema_bancario = $registro_sistema_bancario;

                    $boleto->store();

                    //TToast::show('show', 'VALOR PAGO: '.$return[0]->valor_pago.'</br> DATA DO PAGAMENTO: '.$date1[1].'/'.$date1[0].'/'.$date1[2], 'top right', 'far:check-circle' );

                    new TMessage('warning', 
                        '<b>VALOR DO BOLETO:</b> R$'.number_format($return[0]->valor_original, 2, ',', '.').
                        '</br> <b>VALOR PAGO:</b> R$'.number_format($return[0]->valor_pago, 2, ',', '.').
                        '</br> <b>VALOR A CREDITAR:</b> R$'.number_format($return[0]->valor_liquido, 2, ',', '.').
                        '</br> <b>VALOR DA TARIFA:</b> R$'.number_format($return[0]->valor_tarifa, 2, ',', '.').
                        '</br> <b>DATA DO PAGAMENTO: </b>'.$date1[1].'/'.$date1[0].'/'.$date1[2].
                        '</br> <b>DATA DO CRÉDITO: </b>'.$date2[1].'/'.$date2[0].'/'.$date2[2]);
                    
                }elseif($return[0]->registro_sistema_bancario){

                    new TMessage('warning', '<b>SITUAÇÃO: </b>'.$return[0]->registro_sistema_bancario);

                }else{

                    new TMessage('warning', '<b>SITUAÇÃO: </b>Boleto pendente. Tente novamente mais tarde.');
                }
            }else{
                
                new TMessage('warning', '<b>ATENÇÃO: </b>Boleto não gerado. Gere primeiro o boleto para antes consulta-lo.');
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        TTransaction::close();


    }
    
    public static function onGerar($param)
    {
        $id = $param['id'];
        TTransaction::open('sample');

        $boleto = new BoletoApi($id);

        try
        {
            
            $datavencimentoDB = $boleto->vencimento;
            $date = explode("-",$boleto->vencimento);
            $dataVencimento = $date[1] . "/" . $date[2] . "/" . $date[0];

            $boleto->vencimento =  $dataVencimento; 
            $boleto->valor =  $boleto->valor;
            $boleto->juros =  $boleto->juros; 
            $boleto->multa =  $boleto->multa;
            $boleto->desconto = $boleto->desconto;
            $boleto->nome_cliente = $boleto->nome_cliente; 
            $boleto->cpf_cliente = Utilidades::limpaCaracter($boleto->cpf_cliente);
            $boleto->endereco_cliente = $boleto->endereco_cliente; 
            $boleto->numero_cliente =  $boleto->numero_cliente;
            $boleto->complemento_cliente =  $boleto->complemento_cliente; 
            $boleto->bairro_cliente =  $boleto->bairro_cliente;
            $boleto->cidade_cliente =  $boleto->cidade_cliente;
            $boleto->estado_cliente =  $boleto->estado_cliente;
            $boleto->cep_cliente =  Utilidades::limpaCaracter($boleto->cep_cliente);
            $boleto->email_cliente =  $boleto->email_cliente;
            $boleto->telefone_cliente =  Utilidades::limpaCaracter($boleto->telefone_cliente);
            $boleto->logo_url =  "https://fidelize.macroerp.com.br/app/images/logo.png";
            $boleto->texto =  $boleto->texto;
            $boleto->grupo =  $boleto->grupo; //Quando um valor é informado neste campo, é retornado um link adicional para impressão de todos os boletos do mesmo grupo.
            $boleto->pedido_numero =  $boleto->pedido_numero;
            $boleto->juros_fixo = $boleto->juros_fixo;
            $boleto->multa_fixo = $boleto->multa_fixo;
            $boleto->diasdesconto1 = $boleto->diasdesconto1;
            $boleto->desconto2 = $boleto->desconto2;
            $boleto->diasdesconto2 = $boleto->diasdesconto2;
            $boleto->desconto3 = $boleto->desconto3;
            $boleto->diasdesconto3 = $boleto->diasdesconto3;
            $boleto->nunca_atualizar_boleto = 0; //0 - 1
            $boleto->instrucao_adicional = $boleto->instrucao_adicional; //nclusão do texto adicional abaixo da instrução referente a juros e descontos. length (0-255).
            $boleto->webhook = "https://fidelize.macroerp.com.br/engine.php?class=WebhookPJBank&method=onHook&static=1";//informe uma URL de Webhook. Iremos chamá-la com as novas informações sempre que a cobrança for atualizada.
            $boleto->especie_documento = $boleto->especie_documento;

            $credencialUnit = ApiIntegracao::where('unit_id','=','1')->first();
            $boleto->credencial = $credencialUnit->credencial;
            $boleto->ambiente = $credencialUnit->producao;
            $boleto->chave = $credencialUnit->chave;

            //var_dump($boleto);
            $api = PJBankApi::emitirBoleto($boleto);
            $return = json_decode($api);
            //VarPre::onTest($return);
            if($return->status == '201'){

                $boleto->vencimento =  $datavencimentoDB;
                $boleto->status = $return->status;
                $boleto->msg = $return->msg;
                $boleto->nossonumero = $return->nossonumero;
                $boleto->id_unico = $return->id_unico;
                $boleto->banco_numero = $return->banco_numero;
                $boleto->token_facilitador = $return->token_facilitador;
                $boleto->credencial = $return->credencial;
                $boleto->linkBoleto = $return->linkBoleto;
                $boleto->linkGrupo = $return->linkGrupo;
                $boleto->linhaDigitavel = $return->linhaDigitavel;
                $boleto->store();

                
                $action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', $return->msg,  $action);

            }elseif($return->status == '200'){

                $boleto->vencimento =  $datavencimentoDB;
                $boleto->status = $return->status;
                $boleto->msg = $return->msg;
                $boleto->nossonumero = $return->nossonumero;
                $boleto->id_unico = $return->id_unico;
                $boleto->banco_numero = $return->banco_numero;
                $boleto->token_facilitador = $return->token_facilitador;
                $boleto->credencial = $return->credencial;
                $boleto->linkBoleto = $return->linkBoleto;
                $boleto->linkGrupo = $return->linkGrupo;
                $boleto->linhaDigitavel = $return->linhaDigitavel;
                $boleto->store();

                $action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', $return->msg,  $action);

            }elseif($return->status == '400'){

                $boleto->status = $return->status;
                $boleto->msg = $return->msg;
                $boleto->store();

                $action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', $return->msg,  $action);

            }

            // if(is_array($return)){

            //     if($return[0]->status == "500"){

            //         $boleto->status = $return[0]->status;
            //         $boleto->msg = $return[0]->msg;
            //         $boleto->store();
                        
            //         $action = new TAction([__CLASS__, 'onReload']);
            //         new TMessage('info', $return[0]->msg,  $action);
            //     }
            // }
            
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        TTransaction::close();
    }

    public function onLinkExterno($param)
    {

        TTransaction::open('sample');
        $id = $param['id'];
        $boleto = new BoletoApi($id);
        TScript::create('window.open("https://wa.me/55'.$boleto->telefone_cliente.'?text=Olá%20estimado%20cliente.%20segue%20o%20link%20do%20boleto%20referente%20a%20nossos%20serviços.%20Para%20ter%20acesso,%20basta%20clicar:%20'.$boleto->linkBoleto.'","_blank")'); 
        TTransaction::close();
    }
    

    public function onCancelarBoleto($param)
    {
        TTransaction::open('sample');
        try
        {   
            $id = $param['id'];
            $boleto = new BoletoApi($id);
            $boleto->pedido_numero;

            $credencialUnit = ApiIntegracao::where('unit_id','=',$boleto->unit_id)->first();

            $boleto->credencial = $credencialUnit->credencial;
            $boleto->ambiente = $credencialUnit->producao;
            $boleto->chave = $credencialUnit->chave;

            $api = PJBankApi::invalidarBoleto($boleto);
            $return = json_decode($api);

            if($return->status == "200"){

                $this->onReload($param); 
                new TMessage('info', "Cancelado com sucesso");

                $boleto->status = $return->status;
                $boleto->msg = $return->msg;
                $boleto->store();
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        TTransaction::close();
    }

    public function onInlineEdit($param)
    {
        try
        {
            
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('communication'); 
            $object = new BoletoApi($key); 
            $object->{$field} = $value;
            $object->store(); 
            TTransaction::close(); 
            
            $this->onReload($param); 
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    

    public function onSearch()
    {
        
        $data = $this->form->getData();
        
        TSession::setValue(__CLASS__.'_filter_vencimento',   NULL);
        TSession::setValue(__CLASS__.'_filter_valor',   NULL);
        TSession::setValue(__CLASS__.'_filter_cliente_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_pedido_numero',   NULL);
        TSession::setValue(__CLASS__.'_filter_status',   NULL);
        TSession::setValue(__CLASS__.'_filter_user_id',   NULL);

        if (isset($data->vencimento) AND ($data->vencimento)) {
            $filter = new TFilter('vencimento', 'like', "%{$data->vencimento}%"); 
            TSession::setValue(__CLASS__.'_filter_vencimento',   $filter); 
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); 
            TSession::setValue(__CLASS__.'_filter_valor',   $filter); 
        }


        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', '=', $data->cliente_id); 
            TSession::setValue(__CLASS__.'_filter_cliente_id',   $filter); 
        }


        if (isset($data->pedido_numero) AND ($data->pedido_numero)) {
            $filter = new TFilter('pedido_numero', 'like', "%{$data->pedido_numero}%"); 
            TSession::setValue(__CLASS__.'_filter_pedido_numero',   $filter); 
        }


        if (isset($data->status) AND ($data->status)) {
            $filter = new TFilter('status', 'like', "%{$data->status}%"); 
            TSession::setValue(__CLASS__.'_filter_status',   $filter); 
        }

        if (isset($data->user_id) AND ($data->user_id)) {
            $filter = new TFilter('user_id', '=', $data->user_id); 
            TSession::setValue(__CLASS__.'_filter_user_id',   $filter); 
        }

        
        
        $this->form->setData($data);
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            
            TTransaction::open('sample');
        
            $repository = new TRepository('BoletoApi');
            $limit = 20;
            
            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            //$criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));

            // $checkUser = SystemUserGroup::where('system_user_id','=',TSession::getValue('userid'))->first();
            // if($checkUser->system_group_id !== '1'){
            //     $criteria->add(new TFilter('user_id',  '= ', TSession::getValue('userid')));
            // }
            

            if (TSession::getValue(__CLASS__.'_filter_vencimento')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_vencimento')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_valor')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_valor')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_cliente_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cliente_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_pedido_numero')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_pedido_numero')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_status')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_status')); // add the session filter
            }

            if (TSession::getValue(__CLASS__.'_filter_user_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_user_id')); // add the session filter
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); 
            $this->pageNavigation->setProperties($param); 
            $this->pageNavigation->setLimit($limit); 
            
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    

    public static function onDelete($param)
    {
        
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);
    
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; 
            TTransaction::open('communication'); 
            $object = new BoletoApi($key, FALSE); 
            $object->delete(); 
            TTransaction::close(); 
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }
    
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
