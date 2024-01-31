<?php

use PHPUnit\Framework\TestCase;
use TecnoSpeed\Plugnotas\Common\Endereco;
use TecnoSpeed\Plugnotas\Common\Telefone;
use TecnoSpeed\Plugnotas\Common\ValorAliquota;
use TecnoSpeed\Plugnotas\Configuration;
use TecnoSpeed\Plugnotas\Nfse as Plugnotas;
use TecnoSpeed\Plugnotas\Nfse\CidadePrestacao;
use TecnoSpeed\Plugnotas\Nfse\Impressao;
use TecnoSpeed\Plugnotas\Nfse\Prestador;
use TecnoSpeed\Plugnotas\Nfse\Rps;
use TecnoSpeed\Plugnotas\Nfse\Servico;
use TecnoSpeed\Plugnotas\Nfse\Servico\Deducao;
use TecnoSpeed\Plugnotas\Nfse\Servico\Evento;
use TecnoSpeed\Plugnotas\Nfse\Servico\Iss;
use TecnoSpeed\Plugnotas\Nfse\Servico\Obra;
use TecnoSpeed\Plugnotas\Nfse\Servico\Retencao;
use TecnoSpeed\Plugnotas\Nfse\Servico\Valor;
use TecnoSpeed\Plugnotas\Nfse\Tomador;
use TecnoSpeed\Plugnotas\Error\RequiredError;
use TecnoSpeed\Plugnotas\Error\ValidationError;

use TecnoSpeed\Plugnotas\Builders\NfseBuilder;
use TecnoSpeed\Plugnotas\Communication\CallApi;


/**
 * NfseList Listing
 * @author  Fred Azv.
 */
class NfseList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Nfse');
        $this->form->setFormTitle('Gestão de Nfse');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        $unit_id = new TEntry('unit_id');
        $numeroNfse = new TEntry('numeroNfse');
        $enviarEmail = new TEntry('enviarEmail');
        $dataEmissao = new TEntry('dataEmissao');
        $competencia = new TEntry('competencia');
        $substituicao = new TEntry('substituicao');
        $TcpfCnpj = new TEntry('TcpfCnpj');
        $TrazaoSocial = new TEntry('TrazaoSocial');
        $Temail = new TEntry('Temail');
        $Scodigo = new TEntry('Scodigo');
        $Sdiscriminacao = new TEntry('Sdiscriminacao');
        $Scnae = new TEntry('Scnae');
        $ISSaliquota = new TEntry('ISSaliquota');
        $ISStipoTributacao = new TEntry('ISStipoTributacao');
        $ISSretido = new TEntry('ISSretido');
        $ISSvalor = new TEntry('ISSvalor');

        $row = $this->form->addFields( [ new TLabel('NFS-e'), $numeroNfse ],    
                                       [ new TLabel('Cliente'), $TrazaoSocial ],
                                       [ new TLabel('Valor'), $ISSvalor ]);
        $row->layout = ['col-sm-2', 'col-sm-8','col-sm-2'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Nfse_filter_data') );
        $this->form->setData( TSession::setValue('NfseList', parse_url($_SERVER['REQUEST_URI'])) );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Nova Nota', new TAction(['NfseForm', 'onEdit']), 'fa:plus green');

        $btn2 = $this->form->addAction('Gerar NFse do CR', new TAction([$this, 'onGerar']), 'fa:spinner');
        $btn2->class = 'btn btn-sm btn-success';

        /*$btn3 = $this->form->addAction('Transmitir Todas', new TAction([$this, 'onTransmitir']), 'fa:space-shuttle');
        $btn3->class = 'btn btn-sm btn-warning';

        $btn4 = $this->form->addAction('Consultar Todas', new TAction([$this, 'onConsultar']), 'fa:reply-all');
        $btn4->class = 'btn btn-sm btn-info';*/

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_unit_id = new TDataGridColumn('unit_id', 'Unit Id', 'left');
        $column_numeroNfse = new TDataGridColumn('numeroNfse', 'NFse', 'left');
        $column_enviarEmail = new TDataGridColumn('enviarEmail', 'Enviaremail', 'right');
        $column_dataEmissao = new TDataGridColumn('dataEmissao', 'Dataemissao', 'left');
        $column_competencia = new TDataGridColumn('competencia', 'Competencia', 'left');
        $column_substituicao = new TDataGridColumn('substituicao', 'Substituicao', 'right');
        $column_TcpfCnpj = new TDataGridColumn('TcpfCnpj', 'Tcpfcnpj', 'left');
        $column_TrazaoSocial = new TDataGridColumn('TrazaoSocial', 'Cliente', 'left');
        $column_Temail = new TDataGridColumn('Temail', 'Email', 'left');
        $column_Scodigo = new TDataGridColumn('Scodigo', 'Scodigo', 'left');
        $column_Sdiscriminacao = new TDataGridColumn('Sdiscriminacao', 'Sdiscriminacao', 'left');
        $column_Scnae = new TDataGridColumn('Scnae', 'Scnae', 'right');
        $column_ISSaliquota = new TDataGridColumn('ISSaliquota', 'Issaliquota', 'right');
        $column_ISStipoTributacao = new TDataGridColumn('ISStipoTributacao', 'Isstipotributacao', 'right');
        $column_ISSretido = new TDataGridColumn('ISSretido', 'Issretido', 'right');
        $column_ISSvalor = new TDataGridColumn('ISSvalor', 'Valor', 'left');
        $column_total_servico = new TDataGridColumn('total_servico', 'Valor', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        //$this->datagrid->addColumn($column_unit_id);
        $this->datagrid->addColumn($column_numeroNfse);
        /*$this->datagrid->addColumn($column_enviarEmail);
        $this->datagrid->addColumn($column_dataEmissao);
        $this->datagrid->addColumn($column_competencia);
        $this->datagrid->addColumn($column_substituicao);
        $this->datagrid->addColumn($column_TcpfCnpj);*/
        $this->datagrid->addColumn($column_TrazaoSocial);
        $this->datagrid->addColumn($column_Temail);
       /* $this->datagrid->addColumn($column_Scodigo);
        $this->datagrid->addColumn($column_Sdiscriminacao);
        $this->datagrid->addColumn($column_Scnae);
        $this->datagrid->addColumn($column_ISSaliquota);
        $this->datagrid->addColumn($column_ISStipoTributacao);
        $this->datagrid->addColumn($column_ISSretido);*/
        $this->datagrid->addColumn($column_total_servico);
        $this->datagrid->addColumn($column_status);


        $action1 = new TDataGridAction(array($this, 'onTransmitir'));
        $action1->setLabel('Transmitir');
        $action1->setImage('fa:space-shuttle black');
        $action1->setField('id');

        $action2 = new TDataGridAction(array($this, 'onConsultar'));
        $action2->setLabel('Consultar');
        $action2->setImage('fa:reply-all  black');
        $action2->setField('id');

        $action3 = new TDataGridAction(array($this, 'onCancelar'));
        $action3->setLabel('Cancelar');
        $action3->setImage('fa:recycle  black');
        $action3->setField('id');

        $action4 = new TDataGridAction(array($this, 'onDownload'));
        $action4->setLabel('Baixar NFse');
        $action4->setImage('fas:cloud-download-alt green');
        $action4->setField('id');
        
        $action_group = new TDataGridActionGroup('Ações ', 'fas:sync');

        $action_group->addHeader('Opções');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action4);
        $action_group->addAction($action3);
        
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);
        
        // create EDIT action
        $action_edit = new TDataGridAction(['NfseForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fas:trash-alt-alt red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);

        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
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

    public function onDownload($param)
    {
        TTransaction::open('sample');

        $key = $param['id'];

        $arq = new NFSe($key);
        
        $dataEXP = explode("-", $arq->dataEmissao);
        
        $destino = "tmp/nfse/".$dataEXP[1].$dataEXP[0];

        if(!file_exists($destino)){
            mkdir($destino, 0777, true);
        }

        $pdf = base64_decode($arq->pdf);
        file_put_contents($destino."/".$arq->id_retorno.".pdf", $pdf);

        $caminho = $destino."/".$arq->id_retorno.".pdf";
        //$lerxml = simplexml_load_file($caminho);
        TPage::openFile($caminho);

        TTransaction::close();

    }
    
    public function onGerar( $param )
    {
        TTransaction::open('sample');
        //TTransaction::setLogger(new TLoggerSTD());
        $receitas = ContaReceber::where('nfse','=','N')
                            ->where('gera_nfse','=','S')
                            ->where('MONTH(data_vencimento)','=','NOESC:MONTH(NOW())')
                            ->where('unit_id','=',TSession::getValue('userunitid'))
                            ->load();

        $unidade = new SystemUnit(TSession::getValue('userunitid'));

        if($receitas)
        {
            $nfseParametro = new NfseParametro(1);
            $lote =  $nfseParametro->ultimoNumeroLote + 1;

            foreach ($receitas as $dados) {
            
            $numero = $nfseParametro->ultimoNumeroNfse + 1;
            $nfseParametro->ultimoNumeroNfse = $numero;
            $nfseParametro->store();

            $nfse = new NFSe();
            $nfse->unit_id = $dados->unit_id;
            $nfse->enviarEmail = $nfseParametro->enviarEmail;
            $nfse->dataEmissao = date('Y-m-d H:i:s');
            $nfse->competencia = $dados->data_vencimento;

            $cliente = new Cliente($dados->cliente_id);

            $nfse->TcpfCnpj = $cliente->cpf_cnpj;
            $nfse->TrazaoSocial = $cliente->razao_social;

            $Endereco = ClienteEndereco::where('cliente_id', '=', $dados->cliente_id)->first();

            if($Endereco){

                $nfse->Tlogradouro = $Endereco->logradouro;
                $nfse->Tnumero = $Endereco->numero;
                $nfse->Tbairro = $Endereco->cidade;
                $nfse->Tbairro = $Endereco->bairro;
                $nfse->Tuf = $Endereco->uf;
                $nfse->Tcomplemento = $Endereco->complemento;
                $nfse->TcodigoCidade = $Endereco->codMuni;
                $nfse->Tcep = $Endereco->cep;

            }else{

                new TMessage('error', 'Cliente '.$cliente->razao_social.' não tem endereço cadastrado!');
                die;
            }

            $Email = EmailCliente::where('cliente_id', '=', $dados->cliente_id)->first();
            
            if($Email == null)
            {
                new TMessage('error', 'Cliente '.$cliente->razao_social.' não tem email cadastrado!');
                die;
            }

            $nfse->Temail = $Email->email;

            $nfse->Scodigo = $nfseParametro->ServCodigo;
            $nfse->Sdiscriminacao = $nfseParametro->ServDiscriminacao;

            $nfse->Scnae = $unidade->cnae;
            $nfse->ISSaliquota = $nfseParametro->IssAliquota;
            $nfse->ISStipoTributacao = $nfseParametro->tipoTributacao; //6 - Tributável Dentro do Município
            //$nfse->ISSretido = $nfseParametro->IssRetido;

            $nfse->total_servico = $dados->valor_pago;
            $nfse->base_calculo = $dados->valor_pago;

            $nfse->RetCofins = $nfseParametro->RetCofins;
            $nfse->RetCsll = $nfseParametro->RetCsll;
            $nfse->RetInss = $nfseParametro->RetInss;
            $nfse->RetIrrf = $nfseParametro->RetIrrf;
            $nfse->RetPis = $nfseParametro->RetPis;
            $nfse->RetOutros = $nfseParametro->RetOutros;

            //$nfse->ISSvalor = $nfseParametro->IssValor;
            //colocar caso tiver IssValorRetido no model
            $nfse->status = 'NFSe Gerada pronta para Transmitir';
            $nfse->numeroNfse = $numero;
            $nfse->lote = $lote;
            $nfse->conta_receber_id = $dados->id;
            $nfse->cliente_id = $dados->cliente_id;
            $nfse->tipo = "G";
            $nfse->observacao = $dados->observacao;
            $nfse->store();

            $nfseItem = new NfseItens();
            $nfseItem->nfse_id = $nfse->id;
            $nfseItem->descricao = $dados->descricao;
            $nfseItem->valor = $dados->valor_pago;
            $nfseItem->quantidade = 1;
            $nfseItem->total_item = $dados->valor_pago;
            $nfseItem->store();

            $updateCR = new ContaReceber($dados->id);
            $updateCR->nfse = 'S';
            $updateCR->store();

            }

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', 'Notas Geradas com Sucesso!', $pos_action);
        }

        TTransaction::close();
    }

    public function onTransmitir( $param )
    {
        TTransaction::open('sample');

        $key = (!empty($param['id'])) ? $param['id'] : null;

        $unidade = new SystemUnit(TSession::getValue('userunitid'));
        $integracao =  ApiIntegracao::where('gatway','=','4')->where('unit_id','=',$unidade->id)->first();
        $transmitir = new CloudDfe($integracao);
        $enviarNfse = $transmitir->enviarNfse($param);

        $salvarRetorno = new Nfse($key);

        if($enviarNfse->codigo == '9999' || $enviarNfse->codigo == '5023'){ //apenas para a Tinus

            $salvarRetorno->id_retorno = $enviarNfse->chave;
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info',$enviarNfse->mensagem, $pos_action);
        }

        if($enviarNfse->codigo == '5001'){ //apenas para a Tinus

            //$salvarRetorno->id_retorno = $enviarNfse->chave;
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info',$enviarNfse->mensagem, $pos_action);
        }

        if($enviarNfse->codigo == '406'){

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', $enviarNfse->mensagem, $pos_action);
        }

        if($enviarNfse->codigo == '100'){

            $salvarRetorno->id_retorno = $enviarNfse->chave;
            $salvarRetorno->status = $enviarNfse->mensagem;
            $salvarRetorno->store();

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info',$enviarNfse->mensagem, $pos_action);
        }

        TTransaction::close();
    }

    public function onConsultar( $param )
    {
        TTransaction::open('sample');

        $key = (!empty($param['id'])) ? $param['id'] : null;

        try {
            
            $objetoNfse = new Nfse($key);

            $unidade = new SystemUnit(TSession::getValue('userunitid'));
            $integracao =  ApiIntegracao::where('gatway','=','4')->where('unit_id','=',$unidade->id)->first();
            $transmitir = new CloudDfe($integracao);
            $consultarNfse = $transmitir->consultaNfse($objetoNfse->id_retorno);
            //var_dump($consultarNfse);
        
            if($consultarNfse->codigo == '100' || $consultarNfse->codigo == '101' || $consultarNfse->codigo == '1' || $consultarNfse->codigo == '9999'){

                $objetoNfse->pdf = $consultarNfse->pdf;
                $objetoNfse->xml = $consultarNfse->xml;
                $objetoNfse->id_retorno = $consultarNfse->chave;
                $objetoNfse->numeroNfse = $consultarNfse->numero;
                $objetoNfse->status = $consultarNfse->mensagem;
                $objetoNfse->statusCode = $consultarNfse->codigo;
                $objetoNfse->store();

                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', $consultarNfse->mensagem, $pos_action);

            }elseif($consultarNfse->codigo == '400' || $consultarNfse->codigo == '401' || $consultarNfse->codigo == '404' || $consultarNfse->codigo == '5023'){

                $objetoNfse->status = $consultarNfse->mensagem;
                $objetoNfse->statusCode = $consultarNfse->codigo;
                $objetoNfse->store();

                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', $consultarNfse->mensagem, $pos_action);

            }else{

                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', $consultarNfse->mensagem, $pos_action);

            }
            

        } catch (\Exception $e) {
          new TMessage('error', $e);
        }

        TTransaction::close();
        //var_dump($consulta);
    }


    public function onCancelar( $param )
    {

        TTransaction::open('sample');

        $key = $param['id'];
        $objetoNfse = new Nfse($key);

        try {

            $unidade = new SystemUnit(TSession::getValue('userunitid'));
            $integracao =  ApiIntegracao::where('gatway','=','4')->where('unit_id','=',$unidade->id)->first();
            $cancelar = new CloudDfe($integracao);
            $returnoNfse = $cancelar->cancelarNfse($objetoNfse->id_retorno, '1');
            
            if($returnoNfse->codigo == '101'){

                $objetoNfse->xml = $returnoNfse->xml;
                $objetoNfse->status = $returnoNfse->mensagem;
                $objetoNfse->statusCode = $returnoNfse->codigo;
                $objetoNfse->store();

                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', $returnoNfse->mensagem, $pos_action);

            }elseif($returnoNfse->codigo == '404'){

                $objetoNfse->status = $returnoNfse->mensagem;
                $objetoNfse->statusCode = $returnoNfse->codigo;
                $objetoNfse->store();

                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', $returnoNfse->mensagem, $pos_action);

            }else{

                $pos_action = new TAction([__CLASS__, 'onReload']);
                new TMessage('info', "Não foi retornado nenhum status 101 e 404!", $pos_action);

            }
    

        } catch (\Exception $e) {
          var_dump($e);
        }

        TTransaction::close();
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
            $object = new NFSe($key); // instantiates the Active Record
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
        TSession::setValue('NfseList_filter_id',   NULL);
        TSession::setValue('NfseList_filter_unit_id',   NULL);
        TSession::setValue('NfseList_filter_numeroNfse',   NULL);
        TSession::setValue('NfseList_filter_enviarEmail',   NULL);
        TSession::setValue('NfseList_filter_dataEmissao',   NULL);
        TSession::setValue('NfseList_filter_competencia',   NULL);
        TSession::setValue('NfseList_filter_substituicao',   NULL);
        TSession::setValue('NfseList_filter_TcpfCnpj',   NULL);
        TSession::setValue('NfseList_filter_TrazaoSocial',   NULL);
        TSession::setValue('NfseList_filter_Temail',   NULL);
        TSession::setValue('NfseList_filter_Scodigo',   NULL);
        TSession::setValue('NfseList_filter_Sdiscriminacao',   NULL);
        TSession::setValue('NfseList_filter_Scnae',   NULL);
        TSession::setValue('NfseList_filter_ISSaliquota',   NULL);
        TSession::setValue('NfseList_filter_ISStipoTributacao',   NULL);
        TSession::setValue('NfseList_filter_ISSretido',   NULL);
        TSession::setValue('NfseList_filter_ISSvalor',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('NfseList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->unit_id) AND ($data->unit_id)) {
            $filter = new TFilter('unit_id', 'like', "%{$data->unit_id}%"); // create the filter
            TSession::setValue('NfseList_filter_unit_id',   $filter); // stores the filter in the session
        }


        if (isset($data->numeroNfse) AND ($data->numeroNfse)) {
            $filter = new TFilter('numeroNfse', 'like', "%{$data->numeroNfse}%"); // create the filter
            TSession::setValue('NfseList_filter_numeroNfse',   $filter); // stores the filter in the session
        }


        if (isset($data->enviarEmail) AND ($data->enviarEmail)) {
            $filter = new TFilter('enviarEmail', 'like', "%{$data->enviarEmail}%"); // create the filter
            TSession::setValue('NfseList_filter_enviarEmail',   $filter); // stores the filter in the session
        }


        if (isset($data->dataEmissao) AND ($data->dataEmissao)) {
            $filter = new TFilter('dataEmissao', 'like', "%{$data->dataEmissao}%"); // create the filter
            TSession::setValue('NfseList_filter_dataEmissao',   $filter); // stores the filter in the session
        }


        if (isset($data->competencia) AND ($data->competencia)) {
            $filter = new TFilter('competencia', 'like', "%{$data->competencia}%"); // create the filter
            TSession::setValue('NfseList_filter_competencia',   $filter); // stores the filter in the session
        }


        if (isset($data->substituicao) AND ($data->substituicao)) {
            $filter = new TFilter('substituicao', 'like', "%{$data->substituicao}%"); // create the filter
            TSession::setValue('NfseList_filter_substituicao',   $filter); // stores the filter in the session
        }


        if (isset($data->TcpfCnpj) AND ($data->TcpfCnpj)) {
            $filter = new TFilter('TcpfCnpj', 'like', "%{$data->TcpfCnpj}%"); // create the filter
            TSession::setValue('NfseList_filter_TcpfCnpj',   $filter); // stores the filter in the session
        }


        if (isset($data->TrazaoSocial) AND ($data->TrazaoSocial)) {
            $filter = new TFilter('TrazaoSocial', 'like', "%{$data->TrazaoSocial}%"); // create the filter
            TSession::setValue('NfseList_filter_TrazaoSocial',   $filter); // stores the filter in the session
        }


        if (isset($data->Temail) AND ($data->Temail)) {
            $filter = new TFilter('Temail', 'like', "%{$data->Temail}%"); // create the filter
            TSession::setValue('NfseList_filter_Temail',   $filter); // stores the filter in the session
        }


        if (isset($data->Scodigo) AND ($data->Scodigo)) {
            $filter = new TFilter('Scodigo', 'like', "%{$data->Scodigo}%"); // create the filter
            TSession::setValue('NfseList_filter_Scodigo',   $filter); // stores the filter in the session
        }


        if (isset($data->Sdiscriminacao) AND ($data->Sdiscriminacao)) {
            $filter = new TFilter('Sdiscriminacao', 'like', "%{$data->Sdiscriminacao}%"); // create the filter
            TSession::setValue('NfseList_filter_Sdiscriminacao',   $filter); // stores the filter in the session
        }


        if (isset($data->Scnae) AND ($data->Scnae)) {
            $filter = new TFilter('Scnae', 'like', "%{$data->Scnae}%"); // create the filter
            TSession::setValue('NfseList_filter_Scnae',   $filter); // stores the filter in the session
        }


        if (isset($data->ISSaliquota) AND ($data->ISSaliquota)) {
            $filter = new TFilter('ISSaliquota', 'like', "%{$data->ISSaliquota}%"); // create the filter
            TSession::setValue('NfseList_filter_ISSaliquota',   $filter); // stores the filter in the session
        }


        if (isset($data->ISStipoTributacao) AND ($data->ISStipoTributacao)) {
            $filter = new TFilter('ISStipoTributacao', 'like', "%{$data->ISStipoTributacao}%"); // create the filter
            TSession::setValue('NfseList_filter_ISStipoTributacao',   $filter); // stores the filter in the session
        }


        if (isset($data->ISSretido) AND ($data->ISSretido)) {
            $filter = new TFilter('ISSretido', 'like', "%{$data->ISSretido}%"); // create the filter
            TSession::setValue('NfseList_filter_ISSretido',   $filter); // stores the filter in the session
        }


        if (isset($data->ISSvalor) AND ($data->ISSvalor)) {
            $filter = new TFilter('ISSvalor', 'like', "%{$data->ISSvalor}%"); // create the filter
            TSession::setValue('NfseList_filter_ISSvalor',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Nfse_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
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
            
            // creates a repository for Nfse
            $repository = new TRepository('NFSe');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('NfseList_filter_id')) {
                $criteria->add(TSession::getValue('NfseList_filter_id')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_unit_id')) {
                $criteria->add(TSession::getValue('NfseList_filter_unit_id')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_numeroNfse')) {
                $criteria->add(TSession::getValue('NfseList_filter_numeroNfse')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_enviarEmail')) {
                $criteria->add(TSession::getValue('NfseList_filter_enviarEmail')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_dataEmissao')) {
                $criteria->add(TSession::getValue('NfseList_filter_dataEmissao')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_competencia')) {
                $criteria->add(TSession::getValue('NfseList_filter_competencia')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_substituicao')) {
                $criteria->add(TSession::getValue('NfseList_filter_substituicao')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_TcpfCnpj')) {
                $criteria->add(TSession::getValue('NfseList_filter_TcpfCnpj')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_TrazaoSocial')) {
                $criteria->add(TSession::getValue('NfseList_filter_TrazaoSocial')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_Temail')) {
                $criteria->add(TSession::getValue('NfseList_filter_Temail')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_Scodigo')) {
                $criteria->add(TSession::getValue('NfseList_filter_Scodigo')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_Sdiscriminacao')) {
                $criteria->add(TSession::getValue('NfseList_filter_Sdiscriminacao')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_Scnae')) {
                $criteria->add(TSession::getValue('NfseList_filter_Scnae')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_ISSaliquota')) {
                $criteria->add(TSession::getValue('NfseList_filter_ISSaliquota')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_ISStipoTributacao')) {
                $criteria->add(TSession::getValue('NfseList_filter_ISStipoTributacao')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_ISSretido')) {
                $criteria->add(TSession::getValue('NfseList_filter_ISSretido')); // add the session filter
            }


            if (TSession::getValue('NfseList_filter_ISSvalor')) {
                $criteria->add(TSession::getValue('NfseList_filter_ISSvalor')); // add the session filter
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
            $object = new NFSe($key, FALSE); // instantiates the Active Record
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
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
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