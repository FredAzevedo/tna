<?php
/**
 * HistoricoList Listing
 * @author  Fred Azv.
 */
class HistoricoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_search_Historico');
        $this->form->setFormTitle('Histórico');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        $aluno_id = new TDBUniqueSearch('aluno_id', 'sample', 'Aluno', 'id', 'nome');
        $serie_id = new TDBCombo('serie_id', 'sample', 'Serie', 'id', 'nome');
        $ano_letivo_id = new TDBCombo('ano_letivo_id', 'sample', 'AnoLetivo', 'id', 'ano');
        $situacao = new TEntry('situacao');

        $row = $this->form->addFields( [ new TLabel('Id'), $id ],
                                       [ new TLabel('Aluno'), $aluno_id ],    
                                       [ new TLabel('Série'), $serie_id ],
                                       [ new TLabel('Ano Letivo'), $ano_letivo_id ]
                                    );
        $row->layout = ['col-sm-2','col-sm-6', 'col-sm-2','col-sm-2'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['HistoricoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        
        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_aluno_id = new TDataGridColumn('aluno->nome', 'Aluno', 'left');
        $column_serie_id = new TDataGridColumn('serie->nome', 'Série', 'left');
        $column_ano_letivo_id = new TDataGridColumn('ano_letivo->ano', 'Ano Letivo', 'left');
        $column_situacao = new TDataGridColumn('situacao', 'Situacao', 'right');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_aluno_id);
        $this->datagrid->addColumn($column_serie_id);
        $this->datagrid->addColumn($column_ano_letivo_id);
        $this->datagrid->addColumn($column_situacao);


        $action1 = new TDataGridAction(['HistoricoForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        $action3 = new TDataGridAction([$this, 'onPrint'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        //$this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        $this->datagrid->addAction($action3 ,'Imprimir', 'far:file-pdf ');
        
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
    
    public function onPrint( $param )
    {
        try
        {
            $gerar = new RelHistorico( $param['id'] );
            $relatorio = $gerar->get_arquivo();
            if($relatorio)
            {
                parent::openFile($relatorio);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback(); 
        }
    }

    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('sample'); 
            $object = new Historico($key); 
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
    

    public function onSearch( $param )
    {
        
        $data = $this->form->getData();

        TSession::setValue(__CLASS__.'_filter_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_aluno_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_serie_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_ano_letivo_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_situacao',   NULL);

        if($param['id']){
            $filter = new TFilter('aluno_id', '=',$param['id']); 
            TSession::setValue(__CLASS__.'_filter_aluno_id',   $filter); 
        }

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', $data->id); 
            TSession::setValue(__CLASS__.'_filter_id',   $filter); 
        }

        if (isset($data->aluno_id) AND ($data->aluno_id)) {
            $filter = new TFilter('aluno_id', '=', $data->aluno_id); 
            TSession::setValue(__CLASS__.'_filter_aluno_id',   $filter); 
        }

        if (isset($data->serie_id) AND ($data->serie_id)) {
            $filter = new TFilter('serie_id', '=', $data->serie_id); 
            TSession::setValue(__CLASS__.'_filter_serie_id',   $filter); 
        }

        if (isset($data->ano_letivo_id) AND ($data->ano_letivo_id)) {
            $filter = new TFilter('ano_letivo_id', '=', $data->ano_letivo_id); 
            TSession::setValue(__CLASS__.'_filter_ano_letivo_id',   $filter); 
        }

        if (isset($data->situacao) AND ($data->situacao)) {
            $filter = new TFilter('situacao', 'like', "%{$data->situacao}%"); 
            TSession::setValue(__CLASS__.'_filter_situacao',   $filter); 
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
            
            TTransaction::open('sample');

            // creates a repository for Historico
            $repository = new TRepository('Historico');
            $limit = 10;
            
            $criteria = new TCriteria;
            
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_id'));
            }


            if (TSession::getValue(__CLASS__.'_filter_aluno_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_aluno_id'));
            }


            if (TSession::getValue(__CLASS__.'_filter_serie_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_serie_id'));
            }


            if (TSession::getValue(__CLASS__.'_filter_ano_letivo_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_ano_letivo_id'));
            }


            if (TSession::getValue(__CLASS__.'_filter_situacao')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_situacao'));
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
            TTransaction::open('sample'); 
            $object = new Historico($key, FALSE); 
            $object->delete(); // deletes the object from the database
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
