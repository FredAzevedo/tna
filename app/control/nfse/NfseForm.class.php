<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TNumeric;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TQuickGrid;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;
use Eduardokum\LaravelBoleto\Util;

/**
 * NfseForm Master/Detail
 * @author  <your name here>
 */
class NfseForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_NFSe');
        $this->form->setFormTitle('NFSe');
        $this->form->setFieldSizes('100%');
        
        // master fields
        $id = new TEntry('id');

        $serie = new TEntry('serie');
        $serie->setValue('1');
        $tipoRPS = new TEntry('tipoRPS');
        $tipoRPS->setValue('1');
        $statusRPS = new TEntry('statusRPS');
        $statusRPS->setValue('1');

        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userid'));
        $user_id->addValidation('Usuário', new TRequiredValidator());

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'tipo');

        //$lote = new TEntry('lote');

        $numeroNfse = new TEntry('numeroNfse');
        $numeroNfse->setEditable(FALSE);

        $enviarEmail = new TCombo('enviarEmail');
        $combo_enviarEmails = array();
        $combo_enviarEmails['1'] = 'Sim';
        $combo_enviarEmails['0'] = 'Não';
        $enviarEmail->addItems($combo_enviarEmails);

        $dataEmissao = new TDate('dataEmissao');
        $dataEmissao->setDatabaseMask('yyyy-mm-dd');
        $dataEmissao->setMask('dd/mm/yyyy');

        $competencia = new TDate('competencia');
        $competencia->setDatabaseMask('yyyy-mm-dd');
        $competencia->setMask('dd/mm/yyyy');

        $substituicao = new TEntry('substituicao');
        $substituicao->setEditable(FALSE);

        $cliente_id = new TDBSeekButton('cliente_id', 'sample', $this->form->getName(),'Cliente', 'razao_social','id', 'TrazaoSocial');
        $TrazaoSocial = new TEntry('TrazaoSocial');

        $TcpfCnpj = new TEntry('TcpfCnpj');
        $Temail = new TEntry('Temail');
        $Tlogradouro = new TEntry('Tlogradouro');
        $Tnumero = new TEntry('Tnumero');
        $Tbairro = new TEntry('Tbairro');
        $Tcomplemento = new TEntry('Tcomplemento');
        $Tcidade = new TEntry('Tcidade');
        $Tuf = new TEntry('Tuf');
        $TcodigoCidade = new TEntry('TcodigoCidade');
        $Tcep = new TEntry('Tcep');

        $Scodigo = new TDBSeekButton('Scodigo', 'sample', $this->form->getName(), 'CodigoServicos', 'descricao', 'Scodigo', 'Sdiscriminacao');
        $Sdiscriminacao = new TEntry('Sdiscriminacao');

        $Scnae = new TEntry('Scnae');
        $ISSaliquota = new TNumeric('ISSaliquota', 2, ',', '.', true);

        $ISStipoTributacao = new TCombo('ISStipoTributacao');
        $combo_ISStipoTributacao = array();
        $combo_ISStipoTributacao['1'] = 'Isento de ISS';
        $combo_ISStipoTributacao['2'] = 'Imune';
        $combo_ISStipoTributacao['3'] = 'Não Incidência no Município';
        $combo_ISStipoTributacao['4'] = 'Não Tributável';
        $combo_ISStipoTributacao['5'] = 'Retido';
        $combo_ISStipoTributacao['6'] = 'Tributável Dentro do Município';
        $combo_ISStipoTributacao['7'] = 'Tributável Fora do Município';
        $ISStipoTributacao->addItems($combo_ISStipoTributacao);

        $ISSretido = new TCombo('ISSretido');
        $ISSretido->setChangeAction(new TAction([$this, 'onChangeISSretido']));
        $combo_ISSretidos = array();
        $combo_ISSretidos['1'] = 'Sim';
        $combo_ISSretidos['0'] = 'Não';
        $ISSretido->addItems($combo_ISSretidos);

        $ISSvalor = new TNumeric('ISSvalor', 2, ',', '.', true);
        $ISSvalor->setEditable(FALSE);

        $total_servico = new TNumeric('total_servico', 2, ',', '.', true);
        $total_servico->setEditable(false);
        $deducoes = new TNumeric('deducoes', 2, ',', '.', true);
        $base_calculo = new TNumeric('base_calculo', 2, ',', '.', true);
        $base_calculo->setEditable(false);
        
        $ISSexigibilidade = new TDBCombo('ISSexigibilidade','sample','NfseExigibilidade','id','descricao','id');
        $ISSProcessoSuspencao = new TEntry('ISSProcessoSuspencao');
        $status = new TEntry('status');
        $statusCode = new TEntry('statusCode');
        $protocolo = new TEntry('protocolo');
        $protocolo_cancelamento = new TEntry('protocolo_cancelamento');
        $pdf = new TEntry('pdf');
        $xml = new TEntry('xml');
        $tipo = new TEntry('tipo');
        $id_retorno = new TEntry('id_retorno');
        $id_retorno->setEditable(FALSE);
        $ServCodigo = new TEntry('ServCodigo');
        $ServDescricao = new TEntry('ServDescricao');
        $RetCofins = new TNumeric('RetCofins', 2, ',', '.', true);
        $RetCsll = new TNumeric('RetCsll', 2, ',', '.', true);
        $RetInss = new TNumeric('RetInss', 2, ',', '.', true);
        $RetIrrf = new TNumeric('RetIrrf', 2, ',', '.', true);
        $RetPis = new TNumeric('RetPis', 2, ',', '.', true);
        $RetOutros = new TNumeric('RetOutros', 2, ',', '.', true);

        $vRetCofins = new TNumeric('vRetCofins', 2, ',', '.', true);
        $vRetCsll = new TNumeric('vRetCsll', 2, ',', '.', true);
        $vRetInss = new TNumeric('vRetInss', 2, ',', '.', true);
        $vRetIrrf = new TNumeric('vRetIrrf', 2, ',', '.', true);
        $vRetPis = new TNumeric('vRetPis', 2, ',', '.', true);

        $vRetCofins->setEditable(false);
        $vRetCsll->setEditable(false);
        $vRetInss->setEditable(false);
        $vRetIrrf->setEditable(false);
        $vRetPis->setEditable(false);

        $EventoTipo = new TEntry('EventoTipo');
        $EventoDescricao = new TEntry('EventoDescricao');
        $observacao = new TText('observacao');

        // detail fields
        $detail_id = new THidden('detail_id');
        $detail_descricao = new TEntry('detail_descricao');
        $detail_valor = new TNumeric('detail_valor', 2, ',', '.', true);
        $detail_quantidade = new TNumeric('detail_quantidade', 2, ',', '.', true);
        $detail_total_item = new TEntry('detail_total_item');
        $detail_total_item->setValue('0,00');
        $detail_total_item->setEditable(FALSE);

//        $total_servico = new TEntry('total_servico');
//        $total_servico->setEditable(false);

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

        $this->form->appendPage('Dados Principais');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],   
                                       [ new TLabel('Serie'), $serie ],
                                       [ new TLabel('Tipo RPS'), $tipoRPS ], 
                                       [ new TLabel('Status RPS'), $statusRPS ], 
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Usuário'), $user_id ]);
        $row->layout = ['col-sm-2','col-sm-2','col-sm-2','col-sm-2','col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Cliente'), $cliente_id ],    
                                       [ new TLabel('Nome do Cliente'), $TrazaoSocial ],
                                       [ new TLabel('CNPJ/CPF'), $TcpfCnpj ]
        );
        $row->layout = ['col-sm-2', 'col-sm-8','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Email'), $Temail ],
                                       [ new TLabel('Logradouro'), $Tlogradouro ],
                                       [ new TLabel('Número'), $Tnumero ],
                                       [ new TLabel('Bairro'), $Tbairro ]
        );
        $row->layout = ['col-sm-3', 'col-sm-5','col-sm-1','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Complemto'), $Tcomplemento ],
                                       [ new TLabel('Cidade'), $Tcidade ],
                                       [ new TLabel('UF'), $Tuf ],
                                       [ new TLabel('CEP'), $Tcep ],    
                                       [ new TLabel('IBGE'), $TcodigoCidade ]
        );
        $row->layout = ['col-sm-4','col-sm-3','col-sm-1','col-sm-2','col-sm-2'];
       
        $row = $this->form->addFields( [ new TLabel('Envia Email?'), $enviarEmail ],    
                                       [ new TLabel('Emissão'), $dataEmissao ],
                                       [ new TLabel('Competência'), $competencia ],
                                       [ new TLabel('Substituição'), $substituicao ],
                                       [ new TLabel('Retorno'), $id_retorno ]
        );
        $row->layout = ['col-sm-2', 'col-sm-2','col-sm-2','col-sm-2','col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Observações'), $observacao ]
        );
        $row->layout = ['col-sm-12'];

        $this->form->appendPage('Serviços');

        $row = $this->form->addFields( [ new TLabel('Cód. Serviço'), $Scodigo ],    
                                       [ new TLabel('Descrição do Serviço'), $Sdiscriminacao ],
                                       [ new TLabel('CNAE do Serviço'), $Scnae ]
        );
        $row->layout = ['col-sm-2','col-sm-8','col-sm-2'];
        
        // detail fields
        $this->form->addContent( ['<h3></h3><hr>'] );
        $this->form->addFields( [$detail_id] );

        $row = $this->form->addFields( [ new TLabel('Descrição do Serviço'), $detail_descricao ],
                                       [ new TLabel('Valor Unitário'), $detail_valor ],
                                       [ new TLabel('Quantidade'), $detail_quantidade ],
                                       [ new TLabel('Total'), $detail_total_item ]
        );
        $row->layout = ['col-sm-6','col-sm-2','col-sm-2','col-sm-2'];

        $add = TButton::create('add', [$this, 'onSaveDetail'], 'Register', 'fa:save');
        $this->form->addFields( [], [$add] )->style = 'background: whitesmoke; padding: 5px; margin: 1px;';
        
        $this->detail_list = new BootstrapDatagridWrapper(new TQuickGrid());
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->detail_list->setId('NFSe_list');
        
        // items
        $this->detail_list->addQuickColumn('Descrição', 'descricao', 'left', 100);
        $col_valor = $this->detail_list->addQuickColumn('Valor', 'valor', 'left', 100);
        $col_quantidade = $this->detail_list->addQuickColumn('Quantidade', 'quantidade', 'left', 100);
        $col_total = $this->detail_list->addQuickColumn('Total', 'total_item', 'left', 100);
         //$column_detail_valor = $this->detail_list->addQuickColumn('Total', 'total_item', 'left', 100);

        $transform_valor_format = function($value, $object, $row) {
            return Utilidades::formatar_valor($value);
        };

        $col_valor->setTransformer($transform_valor_format);
        $col_quantidade->setTransformer($transform_valor_format);
//        $col_total->setTransformer($transform_valor_format);

        $col_total->setTransformer( function($value, $object, $row) {
            return (is_numeric($value)) ? Utilidades::formatar_valor($value) : $value ;
        });

        $col_total->setTotalFunction( function($values) {
            $total = array_sum((array) $values);
            $total = Utilidades::formatar_valor($total); //(is_numeric($total)) ? round($total,2) : 0;
            return '<div id="total_service"> <b>Total:</b> ' . $total  . '</div>';
        });

        // detail actions
        $this->detail_list->addQuickAction( 'Edit',   new TDataGridAction([$this, 'onEditDetail']),   'id', 'fa:edit blue');
        $this->detail_list->addQuickAction( 'Delete', new TDataGridAction([$this, 'onDeleteDetail']), 'id', 'fa:trash red');
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
//        $row = $this->form->addFields([new TLabel('Total dos Serviços'), $total_servico]);
//        $row->layout = ['col-sm-offset-10 col-sm-2'];

        $btn = $this->form->addAction( _t('Save'),  new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(['NfseList', 'onReload']), 'fa:eraser red');

        $row = $this->form->addFields( [ new TLabel('Base de Cálculo'), $base_calculo ],
                                       [ new TLabel('Dedução'), $deducoes ],
                                       [ new TLabel('Total do Serviço'), $total_servico ]
        );
        $row->layout = ['col-sm-2','col-sm-2','col-sm-2'];

        $this->form->addContent( ['<h4><b>ISS</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('Retido p/ Tomador?'), $ISSretido ],
                                       [ new TLabel('Alíquota ISS'), $ISSaliquota ],
                                       [ new TLabel('Exigibilidade'), $ISSexigibilidade ],
                                       [ new TLabel('Pro. Suspensão'), $ISSProcessoSuspencao ],
                                       [ new TLabel('Valor do ISS'), $ISSvalor ]
        );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-4', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Tipo de Tributação do Serviço'), $ISStipoTributacao ]
        );
        $row->layout = ['col-sm-4'];

        /*$this->form->addContent( ['<h4><b>Dedução</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('Tipo de Dedução'),  ],
                                       [ new TLabel('Descrição da Dedução'),  ]);
        $row->layout = ['col-sm-3','col-sm-9'];*/

        $this->form->addContent( ['<h4><b>Retenção</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('COFINS (%)'), $RetCofins ],    
                                       [ new TLabel('CSLL (%)'), $RetCsll ],
                                       [ new TLabel('INSS (%)'), $RetInss ],
                                       [ new TLabel('IRRF (%)'), $RetIrrf ],
                                       [ new TLabel('PIS (%)'), $RetPis ]
        );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('COFINS (R$)'), $vRetCofins ],    
                                       [ new TLabel('CSLL (R$)'), $vRetCsll ],
                                       [ new TLabel('INSS (R$)'), $vRetInss ],
                                       [ new TLabel('IRRF (R$)'), $vRetIrrf ],
                                       [ new TLabel('PIS (R$)'), $vRetPis ],
                                       [ new TLabel('Outras Ret. (R$)'), $RetOutros ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $this->form->addContent( ['<h4><b>Evento</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('Código do Evento'), $EventoTipo ],    
                                       [ new TLabel('Descrição do Evento'), $EventoDescricao ]);
        $row->layout = ['col-sm-3', 'col-sm-9'];


        /*$this->form->addFields( [new TLabel('Isstipotributacao')], [$ISStipoTributacao] );
        $this->form->addFields( [new TLabel('Servcodigo')], [$ServCodigo] );
        $this->form->addFields( [new TLabel('Servdescricao')], [$ServDescricao] );*/
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'NfseList'));
        $container->add($this->form);
        parent::add($container);
        TPage::include_js('app/resources/NfseForm.js');
    }
    
    public static function onCalcularTotal() {

        $items_service = (array) TSession::getValue(__CLASS__.'_items');

        $total_service = array_reduce($items_service, function ($carry, $item) {
            $carry += Utilidades::to_number($item['total_item']);
            return $carry;
        }, 0);

        $total_service_str = Utilidades::formatar_valor($total_service);

        TScript::create(" $('#total_service').text('{$total_service_str}') ");

        $data = new stdClass();
        $data->total_item = 'R$ '. $total_service_str;

        TForm::sendData( 'form_NFSe', $data );

        $vlr = $total_service;
    }

    public static function onChangeISSretido($param) {
        $iss_retido = $param['key'] ?? null;

        if (is_null($iss_retido) || $iss_retido === '') {
            return;
        }



    }


    public function onClear($param)
    {
        $this->form->clear(TRUE);
        TSession::setValue(__CLASS__.'_items', array());
        $this->onReload( $param );
    }
    
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

//            var_dump($data);
            
            $items = TSession::getValue(__CLASS__.'_items');
            $key = empty($data->detail_id) ? 'X'.mt_rand(1000000000, 1999999999) : $data->detail_id;

            $valor = Utilidades::to_number($data->detail_valor);
            $quantidade = Utilidades::to_number($data->detail_quantidade);

            $deducoes = Utilidades::to_number($data->deducoes);

            // forçar 2 decimal no resultado.
            $total = Utilidades::to_number($valor * $quantidade,2);
            
            $items[ $key ] = array();
            $items[ $key ]['id'] = $key;
            $items[ $key ]['descricao'] = $data->detail_descricao;
            $items[ $key ]['valor'] = $valor;
            $items[ $key ]['quantidade'] = $quantidade;
            $items[ $key ]['total_item'] = $total;//$data->detail_total_item;
            
            TSession::setValue(__CLASS__.'_items', $items);

            $base_calculo = 0;
            foreach ($items as $item) {
                $base_calculo += Utilidades::to_number($item["total_item"]);
            }

            $total_servico = $base_calculo - $deducoes;

            // clear detail form fields
            $data->detail_id = '';
            $data->detail_descricao = '';
            $data->detail_valor = '';
            $data->detail_quantidade = '';
            $data->detail_total_item = '0,00';
            $data->base_calculo = Utilidades::formatar_valor($base_calculo);
            if ($total_servico >= 0 && $base_calculo > 0) {
                $data->total_servico = Utilidades::formatar_valor($total_servico);
            } else {
                $data->total_servico = '0,00';
            }

            TTransaction::close();
            $this->form->setData($data);
            $this->form->setCurrentPage(1);
            
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
        $data->detail_descricao = $item['descricao'];
        $data->detail_valor = Utilidades::formatar_valor($item['valor']);
        $data->detail_quantidade = Utilidades::formatar_valor($item['quantidade']);
        $data->detail_total_item = Utilidades::formatar_valor($item['total_item']);
        
        // fill detail fields
        TForm::sendData( 'form_NFSe', $data );
    }
    
    /**
     * Delete an item from session list
     * @param $param URL parameters
     */
    public static function onDeleteDetail( $param )
    {
        // reset items
        $data = new stdClass;
            $data->detail_descricao = '';
            $data->detail_valor = '';
            $data->detail_quantidade = '';
            $data->detail_total_item = '';
        
        // clear form data
        TForm::sendData('form_NFSe', $data );
        
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        // get detail id
        $detail_id = $param['key'];
        
        // delete the item from session
        unset($items[ $detail_id ] );
        
        // rewrite session items
        TSession::setValue(__CLASS__.'_items', $items);
        
        // delete item from screen
        TScript::create("ttable_remove_row_by_id('NFSe_list', '{$detail_id}')");

        self::onCalcularTotal();
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
        
        self::onCalcularTotal();

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
                
                $object = new NFSe($key);
                $items  = NfseItens::where('nfse_id', '=', $key)->load();
                
                $session_items = array();
                foreach( $items as $item )
                {
                    $item_key = $item->id;
                    $session_items[$item_key] = $item->toArray();
                    $session_items[$item_key]['id'] = $item->id;
                    $session_items[$item_key]['descricao'] = $item->descricao;
                    $session_items[$item_key]['valor'] = $item->valor;
                    $session_items[$item_key]['quantidade'] = $item->quantidade;
                    $session_items[$item_key]['total_item'] = $item->total_item;
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
            $master = new NFSe;
            $master->fromArray( (array) $data);
            $this->form->validate(); // form validation
            
            $master->store(); // save master object
            // delete details
            $old_items = NfseItens::where('nfse_id', '=', $master->id)->load();
            
            $keep_items = array();
            
            // get session items
            $items = TSession::getValue(__CLASS__.'_items');
            
            if( $items )
            {
                foreach( $items as $item )
                {
                    if (substr($item['id'],0,1) == 'X' ) // new record
                    {
                        $detail = new NfseItens;
                    }
                    else
                    {
                        $detail = NfseItens::find($item['id']);
                    }
                    $detail->descricao  = $item['descricao'];
                    $detail->valor  = $item['valor'];
                    $detail->quantidade  = $item['quantidade'];
                    $detail->total_item  = $item['total_item'];
                    $detail->nfse_id = $master->id;
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
