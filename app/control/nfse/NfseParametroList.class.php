<?php
/**
 * NfseParametroList Listing
 * @author  <your name here>
 */
class NfseParametroList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_NfseParametro');
        $this->form->setFormTitle('Parâmetros da NFS-e');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        $apikey = new TEntry('apikey');
        $unico_servico = new TEntry('unico_servico');
        $nome_servico = new TEntry('nome_servico');
        $DeducaoTipo = new TEntry('DeducaoTipo');
        $DeducaoDescricao = new TEntry('DeducaoDescricao');
        $EventoTipo = new TEntry('EventoTipo');
        $EventoDescricao = new TEntry('EventoDescricao');
        $IssAliquota = new TEntry('IssAliquota');
        $IssExigibilidade = new TEntry('IssExigibilidade');
        $IssProcessoSuspensao = new TEntry('IssProcessoSuspensao');
        $IssValor = new TEntry('IssValor');
        $IssRetido = new TEntry('IssRetido');
        $IssValorRetido = new TEntry('IssValorRetido');
        $RetCofins = new TEntry('RetCofins');
        $RetCsll = new TEntry('RetCsll');
        $RetInss = new TEntry('RetInss');
        $RetIrrf = new TEntry('RetIrrf');
        $RetOutrasRetencoes = new TEntry('RetOutrasRetencoes');
        $RetPis = new TEntry('RetPis');
        $ServCnae = new TEntry('ServCnae');
        $ServCodigo = new TEntry('ServCodigo');
        $ServCodigoCidadeIncidencia = new TEntry('ServCodigoCidadeIncidencia');
        $ServCodigoTributacao = new TEntry('ServCodigoTributacao');
        $ServDescricaoCidadeIncidencia = new TEntry('ServDescricaoCidadeIncidencia');
        $ServDiscriminacao = new TEntry('ServDiscriminacao');
        $ServIdIntegracao = new TEntry('ServIdIntegracao');
        $unit_id = new TEntry('unit_id');


        $row = $this->form->addFields( [ new TLabel('Discriminação'), $ServDiscriminacao ],    
                                       [ new TLabel('ID Integração'), $nome_servico ],
                                       [ new TLabel('Código'), $ServCodigo ]);
        $row->layout = ['col-sm-6', 'col-sm-4','col-sm-2'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('NfseParametro_filter_data') );
        $this->form->setData( TSession::setValue('NfseParametroList', parse_url($_SERVER['REQUEST_URI'])) );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['NfseParametroForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_apikey = new TDataGridColumn('apikey', 'API-Key', 'left');
        $column_unico_servico = new TDataGridColumn('unico_servico', 'Unico Servico', 'left');
        $column_nome_servico = new TDataGridColumn('nome_servico', 'Nome Servico', 'left');
        $column_DeducaoTipo = new TDataGridColumn('DeducaoTipo', 'Dedução', 'right');
        $column_DeducaoDescricao = new TDataGridColumn('DeducaoDescricao', 'Deducaodescricao', 'left');
        $column_EventoTipo = new TDataGridColumn('EventoTipo', 'Evento', 'right');
        $column_EventoDescricao = new TDataGridColumn('EventoDescricao', 'Eventodescricao', 'left');
        $column_IssAliquota = new TDataGridColumn('IssAliquota', 'Alíquota', 'left');
        $column_IssExigibilidade = new TDataGridColumn('IssExigibilidade', 'Exigibilidade', 'right');
        $column_IssProcessoSuspensao = new TDataGridColumn('IssProcessoSuspensao', 'Issprocessosuspensao', 'left');
        $column_IssValor = new TDataGridColumn('IssValor', 'Valor', 'left');
        $column_IssRetido = new TDataGridColumn('IssRetido', 'Retido', 'left');
        $column_IssValorRetido = new TDataGridColumn('IssValorRetido', 'Valor retido', 'left');
        $column_RetCofins = new TDataGridColumn('RetCofins', 'Retcofins', 'left');
        $column_RetCsll = new TDataGridColumn('RetCsll', 'Retcsll', 'left');
        $column_RetInss = new TDataGridColumn('RetInss', 'Retinss', 'left');
        $column_RetIrrf = new TDataGridColumn('RetIrrf', 'Retirrf', 'left');
        $column_RetOutrasRetencoes = new TDataGridColumn('RetOutrasRetencoes', 'Retoutrasretencoes', 'left');
        $column_RetPis = new TDataGridColumn('RetPis', 'Retpis', 'left');
        $column_ServCnae = new TDataGridColumn('ServCnae', 'Servcnae', 'left');
        $column_ServCodigo = new TDataGridColumn('ServCodigo', 'Servcodigo', 'left');
        $column_ServCodigoCidadeIncidencia = new TDataGridColumn('ServCodigoCidadeIncidencia', 'Servcodigocidadeincidencia', 'left');
        $column_ServCodigoTributacao = new TDataGridColumn('ServCodigoTributacao', 'Servcodigotributacao', 'left');
        $column_ServDescricaoCidadeIncidencia = new TDataGridColumn('ServDescricaoCidadeIncidencia', 'Servdescricaocidadeincidencia', 'left');
        $column_ServDiscriminacao = new TDataGridColumn('ServDiscriminacao', 'Servdiscriminacao', 'left');
        $column_ServIdIntegracao = new TDataGridColumn('ServIdIntegracao', 'Servidintegracao', 'left');
        $column_unit_id = new TDataGridColumn('unit_id', 'Unit Id', 'right');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_apikey);
        //$this->datagrid->addColumn($column_unico_servico);
        //$this->datagrid->addColumn($column_nome_servico);
        $this->datagrid->addColumn($column_DeducaoTipo);
        //$this->datagrid->addColumn($column_DeducaoDescricao);
        $this->datagrid->addColumn($column_EventoTipo);
        //$this->datagrid->addColumn($column_EventoDescricao);
        $this->datagrid->addColumn($column_IssAliquota);
        $this->datagrid->addColumn($column_IssExigibilidade);
        //$this->datagrid->addColumn($column_IssProcessoSuspensao);
        $this->datagrid->addColumn($column_IssValor);
        $this->datagrid->addColumn($column_IssRetido);
        $this->datagrid->addColumn($column_IssValorRetido);
        /*$this->datagrid->addColumn($column_RetCofins);
        $this->datagrid->addColumn($column_RetCsll);
        $this->datagrid->addColumn($column_RetInss);
        $this->datagrid->addColumn($column_RetIrrf);*/
        //$this->datagrid->addColumn($column_RetOutrasRetencoes);
        /*$this->datagrid->addColumn($column_RetPis);
        $this->datagrid->addColumn($column_ServCnae);
        $this->datagrid->addColumn($column_ServCodigo);
        $this->datagrid->addColumn($column_ServCodigoCidadeIncidencia);
        $this->datagrid->addColumn($column_ServCodigoTributacao);
        $this->datagrid->addColumn($column_ServDescricaoCidadeIncidencia);
        $this->datagrid->addColumn($column_ServDiscriminacao);
        $this->datagrid->addColumn($column_ServIdIntegracao);
        $this->datagrid->addColumn($column_unit_id);*/

        
        // create EDIT action
        $action_edit = new TDataGridAction(['NfseParametroForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
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
    
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('sample'); // open a transaction with database
            $object = new NfseParametro($key); // instantiates the Active Record
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
        TSession::setValue('NfseParametroList_filter_id',   NULL);
        TSession::setValue('NfseParametroList_filter_apikey',   NULL);
        TSession::setValue('NfseParametroList_filter_unico_servico',   NULL);
        TSession::setValue('NfseParametroList_filter_nome_servico',   NULL);
        TSession::setValue('NfseParametroList_filter_DeducaoTipo',   NULL);
        TSession::setValue('NfseParametroList_filter_DeducaoDescricao',   NULL);
        TSession::setValue('NfseParametroList_filter_EventoTipo',   NULL);
        TSession::setValue('NfseParametroList_filter_EventoDescricao',   NULL);
        TSession::setValue('NfseParametroList_filter_IssAliquota',   NULL);
        TSession::setValue('NfseParametroList_filter_IssExigibilidade',   NULL);
        TSession::setValue('NfseParametroList_filter_IssProcessoSuspensao',   NULL);
        TSession::setValue('NfseParametroList_filter_IssValor',   NULL);
        TSession::setValue('NfseParametroList_filter_IssRetido',   NULL);
        TSession::setValue('NfseParametroList_filter_IssValorRetido',   NULL);
        TSession::setValue('NfseParametroList_filter_RetCofins',   NULL);
        TSession::setValue('NfseParametroList_filter_RetCsll',   NULL);
        TSession::setValue('NfseParametroList_filter_RetInss',   NULL);
        TSession::setValue('NfseParametroList_filter_RetIrrf',   NULL);
        TSession::setValue('NfseParametroList_filter_RetOutrasRetencoes',   NULL);
        TSession::setValue('NfseParametroList_filter_RetPis',   NULL);
        TSession::setValue('NfseParametroList_filter_ServCnae',   NULL);
        TSession::setValue('NfseParametroList_filter_ServCodigo',   NULL);
        TSession::setValue('NfseParametroList_filter_ServCodigoCidadeIncidencia',   NULL);
        TSession::setValue('NfseParametroList_filter_ServCodigoTributacao',   NULL);
        TSession::setValue('NfseParametroList_filter_ServDescricaoCidadeIncidencia',   NULL);
        TSession::setValue('NfseParametroList_filter_ServDiscriminacao',   NULL);
        TSession::setValue('NfseParametroList_filter_ServIdIntegracao',   NULL);
        TSession::setValue('NfseParametroList_filter_unit_id',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('NfseParametroList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->apikey) AND ($data->apikey)) {
            $filter = new TFilter('apikey', 'like', "%{$data->apikey}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_apikey',   $filter); // stores the filter in the session
        }


        if (isset($data->unico_servico) AND ($data->unico_servico)) {
            $filter = new TFilter('unico_servico', 'like', "%{$data->unico_servico}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_unico_servico',   $filter); // stores the filter in the session
        }


        if (isset($data->nome_servico) AND ($data->nome_servico)) {
            $filter = new TFilter('nome_servico', 'like', "%{$data->nome_servico}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_nome_servico',   $filter); // stores the filter in the session
        }


        if (isset($data->DeducaoTipo) AND ($data->DeducaoTipo)) {
            $filter = new TFilter('DeducaoTipo', 'like', "%{$data->DeducaoTipo}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_DeducaoTipo',   $filter); // stores the filter in the session
        }


        if (isset($data->DeducaoDescricao) AND ($data->DeducaoDescricao)) {
            $filter = new TFilter('DeducaoDescricao', 'like', "%{$data->DeducaoDescricao}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_DeducaoDescricao',   $filter); // stores the filter in the session
        }


        if (isset($data->EventoTipo) AND ($data->EventoTipo)) {
            $filter = new TFilter('EventoTipo', 'like', "%{$data->EventoTipo}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_EventoTipo',   $filter); // stores the filter in the session
        }


        if (isset($data->EventoDescricao) AND ($data->EventoDescricao)) {
            $filter = new TFilter('EventoDescricao', 'like', "%{$data->EventoDescricao}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_EventoDescricao',   $filter); // stores the filter in the session
        }


        if (isset($data->IssAliquota) AND ($data->IssAliquota)) {
            $filter = new TFilter('IssAliquota', 'like', "%{$data->IssAliquota}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_IssAliquota',   $filter); // stores the filter in the session
        }


        if (isset($data->IssExigibilidade) AND ($data->IssExigibilidade)) {
            $filter = new TFilter('IssExigibilidade', 'like', "%{$data->IssExigibilidade}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_IssExigibilidade',   $filter); // stores the filter in the session
        }


        if (isset($data->IssProcessoSuspensao) AND ($data->IssProcessoSuspensao)) {
            $filter = new TFilter('IssProcessoSuspensao', 'like', "%{$data->IssProcessoSuspensao}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_IssProcessoSuspensao',   $filter); // stores the filter in the session
        }


        if (isset($data->IssValor) AND ($data->IssValor)) {
            $filter = new TFilter('IssValor', 'like', "%{$data->IssValor}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_IssValor',   $filter); // stores the filter in the session
        }


        if (isset($data->IssRetido) AND ($data->IssRetido)) {
            $filter = new TFilter('IssRetido', 'like', "%{$data->IssRetido}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_IssRetido',   $filter); // stores the filter in the session
        }


        if (isset($data->IssValorRetido) AND ($data->IssValorRetido)) {
            $filter = new TFilter('IssValorRetido', 'like', "%{$data->IssValorRetido}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_IssValorRetido',   $filter); // stores the filter in the session
        }


        if (isset($data->RetCofins) AND ($data->RetCofins)) {
            $filter = new TFilter('RetCofins', 'like', "%{$data->RetCofins}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_RetCofins',   $filter); // stores the filter in the session
        }


        if (isset($data->RetCsll) AND ($data->RetCsll)) {
            $filter = new TFilter('RetCsll', 'like', "%{$data->RetCsll}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_RetCsll',   $filter); // stores the filter in the session
        }


        if (isset($data->RetInss) AND ($data->RetInss)) {
            $filter = new TFilter('RetInss', 'like', "%{$data->RetInss}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_RetInss',   $filter); // stores the filter in the session
        }


        if (isset($data->RetIrrf) AND ($data->RetIrrf)) {
            $filter = new TFilter('RetIrrf', 'like', "%{$data->RetIrrf}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_RetIrrf',   $filter); // stores the filter in the session
        }


        if (isset($data->RetOutrasRetencoes) AND ($data->RetOutrasRetencoes)) {
            $filter = new TFilter('RetOutrasRetencoes', 'like', "%{$data->RetOutrasRetencoes}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_RetOutrasRetencoes',   $filter); // stores the filter in the session
        }


        if (isset($data->RetPis) AND ($data->RetPis)) {
            $filter = new TFilter('RetPis', 'like', "%{$data->RetPis}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_RetPis',   $filter); // stores the filter in the session
        }


        if (isset($data->ServCnae) AND ($data->ServCnae)) {
            $filter = new TFilter('ServCnae', 'like', "%{$data->ServCnae}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_ServCnae',   $filter); // stores the filter in the session
        }


        if (isset($data->ServCodigo) AND ($data->ServCodigo)) {
            $filter = new TFilter('ServCodigo', 'like', "%{$data->ServCodigo}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_ServCodigo',   $filter); // stores the filter in the session
        }


        if (isset($data->ServCodigoCidadeIncidencia) AND ($data->ServCodigoCidadeIncidencia)) {
            $filter = new TFilter('ServCodigoCidadeIncidencia', 'like', "%{$data->ServCodigoCidadeIncidencia}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_ServCodigoCidadeIncidencia',   $filter); // stores the filter in the session
        }


        if (isset($data->ServCodigoTributacao) AND ($data->ServCodigoTributacao)) {
            $filter = new TFilter('ServCodigoTributacao', 'like', "%{$data->ServCodigoTributacao}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_ServCodigoTributacao',   $filter); // stores the filter in the session
        }


        if (isset($data->ServDescricaoCidadeIncidencia) AND ($data->ServDescricaoCidadeIncidencia)) {
            $filter = new TFilter('ServDescricaoCidadeIncidencia', 'like', "%{$data->ServDescricaoCidadeIncidencia}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_ServDescricaoCidadeIncidencia',   $filter); // stores the filter in the session
        }


        if (isset($data->ServDiscriminacao) AND ($data->ServDiscriminacao)) {
            $filter = new TFilter('ServDiscriminacao', 'like', "%{$data->ServDiscriminacao}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_ServDiscriminacao',   $filter); // stores the filter in the session
        }


        if (isset($data->ServIdIntegracao) AND ($data->ServIdIntegracao)) {
            $filter = new TFilter('ServIdIntegracao', 'like', "%{$data->ServIdIntegracao}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_ServIdIntegracao',   $filter); // stores the filter in the session
        }


        if (isset($data->unit_id) AND ($data->unit_id)) {
            $filter = new TFilter('unit_id', 'like', "%{$data->unit_id}%"); // create the filter
            TSession::setValue('NfseParametroList_filter_unit_id',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('NfseParametro_filter_data', $data);
        
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
            
            // creates a repository for NfseParametro
            $repository = new TRepository('NfseParametro');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('NfseParametroList_filter_id')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_id')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_apikey')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_apikey')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_unico_servico')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_unico_servico')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_nome_servico')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_nome_servico')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_DeducaoTipo')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_DeducaoTipo')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_DeducaoDescricao')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_DeducaoDescricao')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_EventoTipo')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_EventoTipo')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_EventoDescricao')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_EventoDescricao')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_IssAliquota')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_IssAliquota')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_IssExigibilidade')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_IssExigibilidade')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_IssProcessoSuspensao')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_IssProcessoSuspensao')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_IssValor')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_IssValor')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_IssRetido')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_IssRetido')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_IssValorRetido')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_IssValorRetido')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_RetCofins')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_RetCofins')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_RetCsll')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_RetCsll')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_RetInss')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_RetInss')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_RetIrrf')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_RetIrrf')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_RetOutrasRetencoes')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_RetOutrasRetencoes')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_RetPis')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_RetPis')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_ServCnae')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_ServCnae')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_ServCodigo')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_ServCodigo')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_ServCodigoCidadeIncidencia')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_ServCodigoCidadeIncidencia')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_ServCodigoTributacao')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_ServCodigoTributacao')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_ServDescricaoCidadeIncidencia')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_ServDescricaoCidadeIncidencia')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_ServDiscriminacao')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_ServDiscriminacao')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_ServIdIntegracao')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_ServIdIntegracao')); // add the session filter
            }


            if (TSession::getValue('NfseParametroList_filter_unit_id')) {
                $criteria->add(TSession::getValue('NfseParametroList_filter_unit_id')); // add the session filter
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
            $object = new NfseParametro($key, FALSE); // instantiates the Active Record
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
