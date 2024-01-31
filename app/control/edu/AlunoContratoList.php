<?php
/**
 * AlunoContratoList Listing
 * @author  Fred Azv.
 */
class AlunoContratoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_AlunoContrato');
        $this->form->setFormTitle('Gestão de Contratos');
        $this->form->setFieldSizes('100%');
        
        // create the form fields
        $id = new TEntry('id');
        $primeiro_responsavel_id = new TDBCombo('primeiro_responsavel_id','sample','Responsavel','id','nome','nome');
        $primeiro_responsavel_id->enableSearch(10);
        $ano_letivo = new TEntry('ano_letivo');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Responsável Principal'), $primeiro_responsavel_id ],
                                       [ new TLabel('Ano Letivo'), $ano_letivo ]
                                    );
        $row->layout = ['col-sm-2','col-sm-8','col-sm-2'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        $this->form->setData( TSession::setValue('AlunoContratoList', parse_url($_SERVER['REQUEST_URI'])) );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['AlunoContratoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_primeiro_responsavel_id = new TDataGridColumn('primeiro_responsavel->nome', 'Responsável', 'left');
        $column_ano_letivo = new TDataGridColumn('ano_letivo', 'Ano Letivo', 'center');
        $column_preco_valor_integral = new TDataGridColumn('preco_valor_integral', 'Valor Integral', 'right');
        $column_preco_parcelas = new TDataGridColumn('preco_parcelas', 'Parcelas', 'right');
        $column_preco_parcela_valor = new TDataGridColumn('preco_parcela_valor', 'Parcela Valor', 'right');
        $column_preco_desconto = new TDataGridColumn('preco_desconto', 'Desconto', 'right');
        $column_preco_valor_total = new TDataGridColumn('preco_valor_total', 'Valor Total', 'right');
        $column_financeiro = new TDataGridColumn('financeiro', 'Financeiro', 'center');
        $column_financeiro->setTransformer(array($this, 'formartarSituacao'));


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_primeiro_responsavel_id);
        $this->datagrid->addColumn($column_ano_letivo);
        $this->datagrid->addColumn($column_preco_valor_integral);
        $this->datagrid->addColumn($column_preco_parcelas);
        $this->datagrid->addColumn($column_preco_parcela_valor);
        $this->datagrid->addColumn($column_preco_desconto);
        $this->datagrid->addColumn($column_preco_valor_total);
        $this->datagrid->addColumn($column_financeiro);
        
        $action1 = new TDataGridAction(array($this, 'onPrint'));
        $action1->setLabel('Imprimir Contrato');
        $action1->setImage('fas:file-pdf blue');
        $action1->setField('id');

        $action2 = new TDataGridAction(array('RelLGPD', 'onViewContrato'));
        $action2->setLabel('Imprimir LGPD');
        $action2->setImage('fas:file-pdf red');
        $action2->setField('id');

        $action3 = new TDataGridAction(array($this, 'onFinanceiro'));
        $action3->setLabel('Gerar Boletos e Financeiro');
        $action3->setImage('fas:barcode');
        $action3->setField('id');
        $action3->setDisplayCondition( array($this, 'exibirActionGerarCarne') );

        $action4 = new TDataGridAction(array($this, 'onCarne'));
        $action4->setLabel('Imprimir Carnê Escolar');
        $action4->setImage('fas:barcode');
        $action4->setField('id');
        $action4->setDisplayCondition( array($this, 'exibirActionImprimirCarne') );
        
        $action_group = new TDataGridActionGroup('Ações', 'fas:cog');

        $action_group->addHeader('Opções');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action3);
        $action_group->addAction($action4);

        $this->datagrid->addActionGroup($action_group);

        $act1 = new TDataGridAction(['AlunoContratoForm', 'onEdit'], ['id'=>'{id}']);
        $act2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($act1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($act2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public static function onFinanceiro( $param )
    {
        try
        {
            TTransaction::open('sample');
            $alunocontrato = New AlunoContrato($param['id']);
            $alunocontrato->financeiro = "S";
            $alunocontrato->store();

            $cliente_id = $alunocontrato->primeiro_responsavel_id; 
            $contrato_id = $alunocontrato->id;  
            $valor = $alunocontrato->preco_valor_integral; 
            $conta_bancaria_id = 2;  
            $tipo_pgto_id = $alunocontrato->tipo_pgto_id;  
            $tipo_forma_pgto_id = $alunocontrato->preco_parcelas;;  
            $vencimento_primeira_parcela = $alunocontrato->vencimento_parcela; 
            $descricao = 'MENSALIDADE ESCOLAR DO CONTRATO DE Nº '.$alunocontrato->id;
            $documento = $alunocontrato->id; 
            $unit_id = 1;  
            $user_id = 1;  
            $previsao = null;  
            $gerar_boleto = 'N';  
            $replica = 'N';  
            $baixa = 'N';  
            $boleto_formato = null;  
            $competencia = null;  
            $pc_receita_id = 2; 
            $pc_receita_nome = '2.1.1 - Mensalidade'; 
            $contrato = $param['id'];
            $desconto_mes = $alunocontrato->preco_desconto / $alunocontrato->preco_parcelas;
       
            GerarBoleto::processar( $cliente_id, $contrato_id, $valor,
            $conta_bancaria_id, $tipo_pgto_id, $tipo_forma_pgto_id, $vencimento_primeira_parcela,
            $descricao, $documento, $unit_id = 1, $user_id = 1, $previsao = null, $gerar_boleto = 'N', 
            $replica = 'N', $baixa = 'N', $boleto_formato = null, $competencia = null, $pc_receita_id,$pc_receita_nome,$desconto_mes);
            TTransaction::close();

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback(); 
        }
    }

    public function exibirActionImprimirCarne($contrato):bool
    {
        if($contrato->financeiro == "S")
            return TRUE;
        return FALSE;
    }

    public function exibirActionGerarCarne($contrato):bool
    {
        if($contrato->financeiro == "N")
            return TRUE;
        return FALSE;
    }

    public function formartarSituacao($financeiro, $object, $row)
    {
        if ($financeiro == 'S') {
            $row->style = "background: rgb(86, 212, 86); color: black";
            return "<span>$financeiro</span>";
        }
        else if ($financeiro == 'N'){
            $row->style = "background: #EED01B; color: black";
            return "<span>$financeiro</span>";
        }
    }

    public static function onCarne( $param ){

        TTransaction::open('sample'); 
        $link_carne = AlunoContrato::where("id",'=',$param['id'] )->load();

        try {

            $criteria = new TCriteria; 
            $criteria->add(new TFilter('contrato', '=',$param['id']));
            $criteria->add(new TFilter('formato', 'LIKE','CARNE'));
        
            $repository = new TRepository('BoletoApi'); 
            $boleto = $repository->load($criteria);

            if($boleto)
            {
                foreach($boleto as $bol){
                    $boletos[] = $bol->pedido_numero;
                }
            }

            $dados_api_integracao = new ApiIntegracao(1);

            $obj = new stdClass;
            $obj->ambiente = $dados_api_integracao->url;
            $obj->credencial = $dados_api_integracao->credencial;
            $obj->chave = $dados_api_integracao->chave;
            $obj->pedido_numero = $boletos;

            $carne = new PJBankApi;
            $return = $carne->imprimirCarne($obj);
            $retorno = json_decode($return);
            
            if($retorno->status == '200'){

               $window = TWindow::create('Carnê Escolar', 0.8, 0.8);
               $object = new TElement('object');
               $object->data  = $retorno->linkBoleto;
               $object->type  = 'application/pdf';
               $object->style = "width: 100%; height:calc(100% - 10px)";
               $object->add('O navegador não suporta a exibição deste conteúdo, <a style="color:#007bff;" target=_newwindow href="'.$link_carne->linkGrupo.'"> clique aqui para baixar</a>...');
               
               $window->add($object);
               $window->show();

            }else{
                new TMessage('info', "Status: ".$retorno->status." Mensagem: ".$retorno->msg);
            }

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }

        TTransaction::close(); 

    }
    
    public function onPrint( $param )
    {
        try
        {
            
            $this->form->validate();
            $data = $this->form->getData();
            $this->form->setData($data);
            
            $gerar = new RelContrato($param);

            $relatorio = $gerar->get_arquivo();
            if($relatorio)
            {
                parent::openFile($relatorio);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
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
            $object = new AlunoContrato($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_primeiro_responsavel_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_ano_letivo',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', $data->id); // create the filter
            TSession::setValue(__CLASS__.'_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->primeiro_responsavel_id) AND ($data->primeiro_responsavel_id)) {
            $filter = new TFilter('primeiro_responsavel_id', 'like', "%{$data->primeiro_responsavel_id}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_primeiro_responsavel_id',   $filter); // stores the filter in the session
        }


        if (isset($data->ano_letivo) AND ($data->ano_letivo)) {
            $filter = new TFilter('ano_letivo', '=', $data->ano_letivo); // create the filter
            TSession::setValue(__CLASS__.'_filter_ano_letivo',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        
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
            
            // creates a repository for AlunoContrato
            $repository = new TRepository('AlunoContrato');
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
            

            if (TSession::getValue(__CLASS__.'_filter_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_primeiro_responsavel_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_primeiro_responsavel_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_ano_letivo')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_ano_letivo')); // add the session filter
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
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
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
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
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
            $object = new AlunoContrato($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
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
