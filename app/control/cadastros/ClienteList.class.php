<?php
/**
 * ClienteList Listing
 * @author Fred Azv.
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ini_set('display_errors',1);
// ini_set('display_startup_erros',1);
// error_reporting(E_ALL);
class ClienteList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('sample');         
        $this->setActiveRecord('Cliente');  
        $this->setDefaultOrder('id', 'desc');   
        //$this->setCriteria($criteria);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Cliente');
        //$this->form->setFormTitle('Cliente');
        $this->form->setFieldSizes('100%');
    
        //$this->form->addExpandButton();

        // create the form fields
        $razao_social = new TDBUniqueSearch('razao_social', 'sample', 'Cliente', 'razao_social', 'razao_social');
        $razao_social->setMinLength(0);
        $change_razao_social = new TAction(array($this, 'setarEnderecosPorCliente'));
        $razao_social->setChangeAction($change_razao_social);

        $telefone_id = new TDBCombo('telefone_id', 'sample', 'TelefonesCliente', 'telefone', 'telefone');
        $telefone_id->enableSearch();

        $cep_id = new TDBCombo('cep_id', 'sample', 'ClienteEndereco', 'cep', 'cep');
        $cep_id->enableSearch();

        $nome_fantasia = new TDBUniqueSearch('nome_fantasia', 'sample', 'Cliente', 'nome_fantasia', 'nome_fantasia');
        $nome_fantasia->setMinLength(0);
        $cpf_cnpj = new TDBUniqueSearch('cpf_cnpj', 'sample', 'Cliente', 'cpf_cnpj', 'cpf_cnpj'); 
        $cpf_cnpj->setMinLength(0);

        $logradouro = new TEntry('logradouro');

        $filhos = new TCombo('filhos');
        $combo_filhos['S'] = 'Sim';
        $combo_filhos['N'] = 'Não';
        $filhos->addItems($combo_filhos);

        $profissao_id = new TDBUniqueSearch('profissao_id', 'sample', 'Profissao', 'id', 'nome');
        $profissao_id->setMinLength(2);

        $cidade = new TEntry('cidade');
        $cidade->forceUpperCase();
        $uf = new TEntry('uf');
        $uf->forceUpperCase();

        $sexo = new TCombo('sexo');
        $combo_sexo['M'] = 'Masculino';
        $combo_sexo['F'] = 'Feminino';
        $sexo->addItems($combo_sexo);

        // add the fields
        $row = $this->form->addFields( [ new TLabel('Nome/Razão Social'), $razao_social ],
                                       [ new TLabel('Nome Fantasia'), $nome_fantasia ],
                                       [ new TLabel('CPF/CNPJ'), $cpf_cnpj ],
                                       [ new TLabel('Profissão'), $profissao_id ]
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-2','col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Telefone'), $telefone_id ],
                                       [ new TLabel('CEP'), $cep_id ],
                                       [ new TLabel('Logradouro'), $logradouro ],
                                       [ new TLabel('Tem filhos?'), $filhos ]
        );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-6','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Cidade'), $cidade ],
                                       [ new TLabel('UF'), $uf ],
                                       [ new TLabel('Sexo'), $sexo ]
        );
        $row->layout = ['col-sm-8', 'col-sm-2', 'col-sm-2',];

        // $row = $this->form->addFields( [ new TLabel('Telefone'), $telefones ]);
        // $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];

        $this->setAfterSearchCallback( [$this, 'closeWindow' ] );

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Cliente_filter_data') );
        $this->form->setData( TSession::setValue('ClienteList', parse_url($_SERVER['REQUEST_URI'])) );

        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        // $this->form->addActionLink(_t('New'), new TAction(['ClienteForm', 'onEdit']), 'fa:plus green');
        
        //$this->form->addExpandButton();

        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_razao_social = new TDataGridColumn('razao_social', 'Nome/Razão Social', 'left');
        $column_cpf_cnpj = new TDataGridColumn('cpf_cnpj', 'CPF/CNPJ', 'left');
        $column_telefone = new TDataGridColumn('telefone_principal', 'Telefone', 'left');
        $column_endereco = new TDataGridColumn('{logradouro} - {numero} - {bairro} - {cidade} / {uf}', 'Endereço', 'left');

        // $get_column_telefone = function($id_cliente) {
        //     $cliente = new Cliente($id_cliente);
        //     //Retorna o primeiro telefone cadastrado para um cliente
        //     $telefone = !empty($cliente->getTelefonesClientes()[0]->telefone) ? $cliente->getTelefonesClientes()[0]->telefone : ''; 
        //     if($telefone){
        //         return $telefone;
        //     } 
        //     return '<span style="color:red;"><b>Cliente sem telefone cadastrado</b></span>';
        // };

        // $column_telefone->setTransformer( $get_column_telefone );       

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_razao_social);
        $this->datagrid->addColumn($column_cpf_cnpj);
        $this->datagrid->addColumn($column_telefone);
        $this->datagrid->addColumn($column_endereco);

        $action1 = new TDataGridAction(array($this, 'onCopy'));
        $action1->setLabel('Copiar para Fornecedor');
        $action1->setImage('fas:sort-amount-up-alt black');
        $action1->setField('id');
       
        $action_group = new TDataGridActionGroup('', 'fas:cog');

        $action_group->addHeader('Opções');
        $action_group->addAction($action1);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);
        
        // create EDIT action
        $action_edit = new TDataGridAction(['ClienteForm', 'onEdit']);
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
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // search box
        $input_search = new TEntry('input_search');
        $input_search->placeholder = _t('Search');
        $input_search->setSize('100%');
        
        // enable fuse search by column name
        //$this->datagrid->enableSearch($input_search, 'razao_social, cpf_cnpj');
        
        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        // $panel->addHeaderWidget($input_search);
        $panel->addFooter($this->pageNavigation);

        $panel->addHeaderActionLink(_t('New'), new TAction(['ClienteForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green');
        $btn = $panel->addHeaderActionLink('Filtros', new TAction([$this, 'onShowWindowFilters']), 'fa:filter');
        $btn->class = 'btn btn-primary';

        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction('Excel', new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction('PDF', new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        //$container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }

    public static function onShowWindowFilters($param = null)
    {
        try
        {
            // create a window
            $page = TWindow::create('Filtros da listagem de clientes', 1200, 400);
            $page->removePadding();
            
            // instantiate self class, populate filters in construct
            $embed = new self;
            
            // embed form inside window
            $page->add($embed->form);
            $page->setIsWrapped(true);
            $page->show();
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function closeWindow($param = null)
    {
        TWindow::closeWindow();
    }

    public static function onCopy($param)
    {   
        try {

            TTransaction::open('sample'); 

            $cliente = new Cliente($param['id']);
            $check_fornecedor = Fornecedor::where('codigo_parceiro','=',$cliente->codigo_parceiro)->first();

            if(is_null($check_fornecedor)){

                $fornecedor = new Fornecedor;
                $fornecedor->nome_fantasia      = $cliente->nome_fantasia;
                $fornecedor->razao_social       = $cliente->razao_social;
                $fornecedor->cpf_cnpj           = $cliente->cpf_cnpj;
                $fornecedor->tipo               = $cliente->tipo;
                $fornecedor->cep                = $cliente->cep;
                $fornecedor->logradouro         = $cliente->logradouro;
                $fornecedor->numero             = $cliente->numero;
                $fornecedor->bairro             = $cliente->bairro;
                $fornecedor->complemento        = $cliente->complemento;
                $fornecedor->cidade             = $cliente->cidade;
                $fornecedor->uf                 = $cliente->uf;
                $fornecedor->codMuni            = $cliente->codMuni;
                $fornecedor->codigo_parceiro    = $cliente->codigo_parceiro;
                $fornecedor->unit_id            = $cliente->unit_id;
                $fornecedor->email_principal    = $cliente->email_principal;
                $fornecedor->telefone_principal = $cliente->telefone_principal;
                $fornecedor->store();

                new TMessage('info', 'Registro gravado em Fornecedores!');

            }else{

                new TMessage('error', 'Registro já existe em Fornecedores!');
            }
                
            TTransaction::close();
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e);
            TTransaction::close();
        }
    }

    public static function setarEnderecosPorCliente($param)
    {   
        try {
            
            TTransaction::open('sample'); 

            $criteria = new TCriteria; 
            $criteria->add(new TFilter('razao_social', '=', $param['razao_social'] )); 
            
            TDBCombo::reloadFromModel('form_Cliente', 'endereco_id', 'sample','ClienteEndereco','id','{tipo_endereco->nome} - {regiao}','', $criteria, TRUE);
            TDBCombo::enableField( 'form_Cliente', 'endereco_id' );

            TTransaction::close(); 
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            TTransaction::close();
        }

    }

    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param);
        
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key=$param['key'];
            TTransaction::open('sample'); 
            $object = new Cliente($key, FALSE);
            $object->delete();
            TTransaction::close();
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
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
        
        // clear session filters
        TSession::setValue('ClienteList_cliente_filtro_telefone',   NULL);
        TSession::setValue('ClienteList_filtro_nome_fantasia',   NULL);
        TSession::setValue('ClienteList_filtro_razao_social',   NULL);
        TSession::setValue('ClienteList_filtro_cpf_cnpj',   NULL);
        TSession::setValue('ClienteList_filtro_cep',   NULL);
        TSession::setValue('ClienteList_filtro_logradouro',   NULL);
        TSession::setValue('ClienteList_filtro_filhos',   NULL);
        TSession::setValue('ClienteList_filtro_profissao_id',   NULL);

        TSession::setValue('ClienteList_filtro_cidade',   NULL);
        TSession::setValue('ClienteList_filtro_uf',   NULL);
        TSession::setValue('ClienteList_filtro_sexo',   NULL);

        if (isset($data->cep_id) AND ($data->cep_id)) {
            $filter = new TFilter('id','in',
            "(SELECT cliente.id from cliente
            inner join cliente_endereco on (cliente_endereco.cliente_id = cliente.id)
            where cliente_endereco.cep like '{$data->cep_id}')");
            TSession::setValue('ClienteList_filtro_cep',   $filter);
        }

        if (isset($data->telefone_id) AND ($data->telefone_id)) {
            $filter = new TFilter('id','in',
            "(SELECT cliente.id from cliente
            inner join telefones_cliente on (telefones_cliente.cliente_id = cliente.id)
            where telefones_cliente.telefone like '{$data->telefone_id}')");
            TSession::setValue('ClienteList_cliente_filtro_telefone',   $filter);
        }

        if (isset($data->nome_fantasia) AND ($data->nome_fantasia)) {
            $filter = new TFilter('nome_fantasia', 'like', "%{$data->nome_fantasia}%");
            TSession::setValue('ClienteList_filtro_nome_fantasia',   $filter);
        }

        if (isset($data->razao_social) AND ($data->razao_social)) {
            $filter = new TFilter('razao_social', 'like', "%{$data->razao_social}%");
            TSession::setValue('ClienteList_filtro_razao_social',   $filter);
        }

        if (isset($data->cpf_cnpj) AND ($data->cpf_cnpj)) {
            $filter = new TFilter('cpf_cnpj', 'like', "%{$data->cpf_cnpj}%");
            TSession::setValue('ClienteList_filtro_cpf_cnpj',   $filter);
        }

        if (isset($data->logradouro) AND ($data->logradouro)) {
            $filter = new TFilter('logradouro', 'like', "%{$data->logradouro}%");
            TSession::setValue('ClienteList_filtro_logradouro',   $filter);
        }

        if (isset($data->filhos) AND ($data->filhos)) {
            $filter = new TFilter('filhos', '=', $data->filhos);
            TSession::setValue('ClienteList_filtro_filhos',   $filter);
        }
        
        if (isset($data->profissao_id) AND ($data->profissao_id)) {
            $filter = new TFilter('profissao_id', '=', $data->profissao_id);
            TSession::setValue('ClienteList_filtro_profissao_id',   $filter);
        }

        if (isset($data->cidade) AND ($data->cidade)) {
            $filter = new TFilter('cidade', 'like', "%{$data->cidade}%");
            TSession::setValue('ClienteList_filtro_cidade',   $filter);
        }

        if (isset($data->uf) AND ($data->uf)) {
            $filter = new TFilter('uf', 'like', "%{$data->uf}%");
            TSession::setValue('ClienteList_filtro_uf',   $filter);
        }

        if (isset($data->sexo) AND ($data->sexo)) {
            $filter = new TFilter('sexo', 'like', "%{$data->sexo}%");
            TSession::setValue('ClienteList_filtro_sexo',   $filter);
        }

        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('ClienteList_filter_data', $data);
        
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
            
            $repository = new TRepository('Cliente');
            $limit = 15;
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

            $criteria->add(new TFilter('unit_id',  '=', TSession::getValue('userunitid')));
            
            TSession::setValue('ClienteList', parse_url($_SERVER['REQUEST_URI']));

            if (TSession::getValue('ClienteList_cliente_filtro_telefone')) {
                $criteria->add(TSession::getValue('ClienteList_cliente_filtro_telefone')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_nome_fantasia')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_nome_fantasia')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_razao_social')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_razao_social')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_cpf_cnpj')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_cpf_cnpj')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_cep')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_cep')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_logradouro')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_logradouro')); // add the session filter
            }
            
            if (TSession::getValue('ClienteList_filtro_filhos')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_filhos')); // add the session filter
            }
            if (TSession::getValue('ClienteList_filtro_profissao_id')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_profissao_id')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_cidade')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_cidade')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_uf')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_uf')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_sexo')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_sexo')); // add the session filter
            }
            
            $objects = $repository->load($criteria, FALSE);

            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
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

    public function onExportCSV()
    {

        //$this->onSearch();

        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('sample');
                
            $repository = new TRepository('Cliente');
            $criteria = new TCriteria;

            if (TSession::getValue('ClienteList_cliente_filtro_telefone')) {
                $criteria->add(TSession::getValue('ClienteList_cliente_filtro_telefone')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_nome_fantasia')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_nome_fantasia')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_razao_social')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_razao_social')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_cpf_cnpj')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_cpf_cnpj')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_cep')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_cep')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_logradouro')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_logradouro')); // add the session filter
            }
            
            if (TSession::getValue('ClienteList_filtro_filhos')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_filhos')); // add the session filter
            }
            if (TSession::getValue('ClienteList_filtro_profissao_id')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_profissao_id')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_cidade')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_cidade')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_uf')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_uf')); // add the session filter
            }

            if (TSession::getValue('ClienteList_filtro_sexo')) {
                $criteria->add(TSession::getValue('ClienteList_filtro_sexo')); // add the session filter
            }
            

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();


            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'CLIENTE');
            $sheet->setCellValue('C1', 'CPF/CNPJ');
            $sheet->setCellValue('D1', 'ENDERECO');
            $sheet->setCellValue('E1', 'TELEFONE');
            $sheet->setCellValue('F1', 'E-MAIL');
            $sheet->setCellValue('G1', 'PROFISSÃO');


            $customers = $repository->load($criteria, false);
            if ($customers)
            {
                $linha = 2;
                foreach ($customers as $customer)
                {
                    // $partes = explode(" ", $customer->created_at);
                    // $data = explode('-', $partes[0]);
                    // $datacriacao = $data[2].'/'.$data[1].'/'.$data[0];
                    
                    $ln = $linha++;
                    
                    $sheet->setCellValue('A'.$ln, $customer->id);
                    $sheet->setCellValue('B'.$ln, $customer->razao_social);
                    $sheet->setCellValue('C'.$ln, $customer->cpf_cnpj);
                    $sheet->setCellValue('D'.$ln, $customer->logradouro." ".$customer->numero." Bairro: ".$customer->bairro);
                    $sheet->setCellValue('E'.$ln, $customer->telefone_principal);
                    $sheet->setCellValue('F'.$ln, $customer->email_principal);
                    $sheet->setCellValue('G'.$ln, $customer->profissao->nome);
                
                }

                $writer = new Xlsx($spreadsheet);
                $writer->save('app/output/cadastro_de_clientes.xlsx');
                TPage::openFile('app/output/cadastro_de_clientes.xlsx');
            }
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }
}
