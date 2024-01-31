<?php
/**
 * AlunoList Listing
 * @author  <your name here>
 */
class AlunoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Aluno');
        $this->form->setFormTitle('Alunos');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cpf = new TEntry('cpf');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Aluno'), $nome ],
                                       [ new TLabel('CPF'), $cpf ]
                                    );
        $row->layout = ['col-sm-2','col-sm-8','col-sm-2'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        $this->form->setData( TSession::setValue('AlunoList', parse_url($_SERVER['REQUEST_URI'])) );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['AlunoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_nome = new TDataGridColumn('nome', 'Aluno', 'left');
        $column_cpf = new TDataGridColumn('cpf', 'CPF', 'left');
        $column_telefone = new TDataGridColumn('telefone', 'Telefone', 'center');
        $column_email = new TDataGridColumn('email', 'E-mail', 'right');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cpf);
        $this->datagrid->addColumn($column_telefone);
        $this->datagrid->addColumn($column_email);
        
        $action1 = new TDataGridAction(array('BoletimEscolarFiltro', 'onShow'));
        $action1->setLabel('Boletim Escolar');
        $action1->setImage('fas:sort-amount-up-alt black');
        $action1->setField('id');
        //$action1->setDisplayCondition( array($this, 'displayColumn') );

        $action2 = new TDataGridAction(array('HistoricoList', 'onSearch'));
        $action2->setLabel('Histórico Escolar');
        $action2->setImage('fas:angle-double-down black');
        $action2->setField('id');
        //$action2->setDisplayCondition( array($this, 'displayColumn') );

        $action3 = new TDataGridAction(array($this, 'onReload'));
        $action3->setLabel('Imposto de renda');
        $action3->setImage('fas:receipt');
        $action3->setField('id');

        $action4 = new TDataGridAction(array('RelFrequenciaFiltro', 'onShow'));
        $action4->setLabel('Declaração de Fequência');
        $action4->setImage('fas:receipt');
        $action4->setField('id');
        
        $action5 = new TDataGridAction(array('RelDeclaracaoTransferenciaFiltro', 'onShow'));
        $action5->setLabel('Declaração de Transferência');
        $action5->setImage('fas:receipt');
        $action5->setField('id');

        $action6 = new TDataGridAction(array($this, 'onReload'));
        $action6->setLabel('Dociê único');
        $action6->setImage('fas:receipt');
        $action6->setField('id');

        $action7 = new TDataGridAction(array($this, 'onReload'));
        $action7->setLabel('Livro de Matrícula');
        $action7->setImage('fas:receipt');
        $action7->setField('id');

        $action7 = new TDataGridAction(array($this, 'onReload'));
        $action7->setLabel('Atas de Resultado');
        $action7->setImage('fas:receipt');
        $action7->setField('id');

        $action8 = new TDataGridAction(array('RelRequerimentoFiltro', 'onShow'));
        $action8->setLabel('Requerimentos');
        $action8->setImage('fas:receipt');
        $action8->setField('id');
        
        $action_group = new TDataGridActionGroup('Ações', 'fas:cog');

        $action_group->addHeader('Opções');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addAction($action3);
        $action_group->addAction($action4);
        $action_group->addAction($action5);
        $action_group->addAction($action6);
        $action_group->addAction($action7);
        $action_group->addAction($action8);

        $this->datagrid->addActionGroup($action_group);

        $act1 = new TDataGridAction(['AlunoForm', 'onEdit'], ['id'=>'{id}']);
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
            $object = new Aluno($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_nome',   NULL);
        TSession::setValue(__CLASS__.'_filter_cpf',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', $data->id); // create the filter
            TSession::setValue(__CLASS__.'_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_nome',   $filter); // stores the filter in the session
        }


        if (isset($data->cpf) AND ($data->cpf)) {
            $filter = new TFilter('cpf', 'like', "%{$data->cpf}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_cpf',   $filter); // stores the filter in the session
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
            
            // creates a repository for Aluno
            $repository = new TRepository('Aluno');
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
            

            if (TSession::getValue(__CLASS__.'_filter_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_nome')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_nome')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_cpf')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_cpf')); // add the session filter
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
            $object = new Aluno($key, FALSE); // instantiates the Active Record
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
