<?php

use Adianti\Widget\Wrapper\TDBCombo;

/**
 * ResponsavelList Listing
 * @author  Fred Azv.
 */
class ResponsavelList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_Responsavel');
        $this->form->setFormTitle('Responsavel');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        $responsavel_tipo_id = new TDBCombo('responsavel_tipo_id', 'sample', 'ResponsavelTipo', 'id', 'tipo');
        $nome = new TEntry('nome');
        $cpf = new TEntry('cpf');
        $telefone1 = new TEntry('telefone1');
        $telefone2 = new TEntry('telefone2');
        $email = new TEntry('email');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Tipo'), $responsavel_tipo_id ],
                                       [ new TLabel('Nome'), $nome ],
                                       [ new TLabel('CPF'), $cpf ]
        );
        $row->layout = ['col-sm-2','col-sm-2','col-sm-6','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Telefone 1'), $telefone1 ],
                                       [ new TLabel('Telefone 2'), $telefone2 ],
                                       [ new TLabel('E-mail'), $email ]
        );
        $row->layout = ['col-sm-2','col-sm-2','col-sm-8'];

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ResponsavelForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_unit_id = new TDataGridColumn('unit_id', 'Unit Id', 'left');
        $column_responsavel_tipo_id = new TDataGridColumn('responsavel_tipo->tipo', 'Tipo', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_cpf = new TDataGridColumn('cpf', 'Cpf', 'left');
        $column_rg = new TDataGridColumn('rg', 'Rg', 'left');
        $column_nascimento = new TDataGridColumn('nascimento', 'Nascimento', 'left');
        $column_nacionalidade = new TDataGridColumn('nacionalidade', 'Nacionalidade', 'left');
        $column_estado_civil = new TDataGridColumn('estado_civil', 'Estado Civil', 'left');
        $column_profissao = new TDataGridColumn('profissao', 'Profissao', 'left');
        $column_local_trabalho = new TDataGridColumn('local_trabalho', 'Local Trabalho', 'left');
        $column_telefone1 = new TDataGridColumn('telefone1', 'Telefone1', 'left');
        $column_telefone2 = new TDataGridColumn('telefone2', 'Telefone2', 'left');
        $column_email = new TDataGridColumn('email', 'Email', 'left');
        $column_cep = new TDataGridColumn('cep', 'Cep', 'left');
        $column_logradouro = new TDataGridColumn('logradouro', 'Logradouro', 'left');
        $column_bairro = new TDataGridColumn('bairro', 'Bairro', 'left');
        $column_cidade = new TDataGridColumn('cidade', 'Cidade', 'left');
        $column_uf = new TDataGridColumn('uf', 'Uf', 'left');
        $column_complemento = new TDataGridColumn('complemento', 'Complemento', 'left');
        $column_codMuni = new TDataGridColumn('codMuni', 'Codmuni', 'left');
        $column_contrato_responsavel = new TDataGridColumn('contrato_responsavel', 'Contrato Responsavel', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        // $this->datagrid->addColumn($column_unit_id);
        $this->datagrid->addColumn($column_responsavel_tipo_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cpf);
        // $this->datagrid->addColumn($column_rg);
        // $this->datagrid->addColumn($column_nascimento);
        // $this->datagrid->addColumn($column_nacionalidade);
        // $this->datagrid->addColumn($column_estado_civil);
        // $this->datagrid->addColumn($column_profissao);
        // $this->datagrid->addColumn($column_local_trabalho);
        $this->datagrid->addColumn($column_telefone1);
        $this->datagrid->addColumn($column_telefone2);
        $this->datagrid->addColumn($column_email);
        // $this->datagrid->addColumn($column_cep);
        // $this->datagrid->addColumn($column_logradouro);
        // $this->datagrid->addColumn($column_bairro);
        // $this->datagrid->addColumn($column_cidade);
        // $this->datagrid->addColumn($column_uf);
        // $this->datagrid->addColumn($column_complemento);
        // $this->datagrid->addColumn($column_codMuni);
        // $this->datagrid->addColumn($column_contrato_responsavel);


        $action1 = new TDataGridAction(['ResponsavelForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
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

    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('sample'); // open a transaction with database
            $object = new Responsavel($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_responsavel_tipo_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_nome',   NULL);
        TSession::setValue(__CLASS__.'_filter_cpf',   NULL);
        TSession::setValue(__CLASS__.'_filter_telefone1',   NULL);
        TSession::setValue(__CLASS__.'_filter_telefone2',   NULL);
        TSession::setValue(__CLASS__.'_filter_email',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', $data->id); // create the filter
            TSession::setValue(__CLASS__.'_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->responsavel_tipo_id) AND ($data->responsavel_tipo_id)) {
            $filter = new TFilter('responsavel_tipo_id', '=', $data->responsavel_tipo_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_responsavel_tipo_id',   $filter); // stores the filter in the session
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_nome',   $filter); // stores the filter in the session
        }


        if (isset($data->cpf) AND ($data->cpf)) {
            $filter = new TFilter('cpf', 'like', "%{$data->cpf}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_cpf',   $filter); // stores the filter in the session
        }


        if (isset($data->telefone1) AND ($data->telefone1)) {
            $filter = new TFilter('telefone1', 'like', "%{$data->telefone1}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_telefone1',   $filter); // stores the filter in the session
        }


        if (isset($data->telefone2) AND ($data->telefone2)) {
            $filter = new TFilter('telefone2', 'like', "%{$data->telefone2}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_telefone2',   $filter); // stores the filter in the session
        }


        if (isset($data->email) AND ($data->email)) {
            $filter = new TFilter('email', 'like', "%{$data->email}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_email',   $filter); // stores the filter in the session
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
            
            // creates a repository for Responsavel
            $repository = new TRepository('Responsavel');
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


            if (TSession::getValue(__CLASS__.'_filter_responsavel_tipo_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_responsavel_tipo_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_nome')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_cpf')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cpf')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_telefone1')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_telefone1')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_telefone2')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_telefone2')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_email')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_email')); // add the session filter
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
            $object = new Responsavel($key, FALSE); // instantiates the Active Record
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
