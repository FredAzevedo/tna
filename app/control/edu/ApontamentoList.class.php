<?php
/**
 * ApontamentoList Listing
 * @author  Fred Azv.
 */
class ApontamentoList extends TPage
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
        
        // // creates the form
        // $this->form = new BootstrapFormBuilder('form_search_Apontamento');
        // $this->form->setFormTitle('Apontamento');
        

        // // create the form fields
        // $aluno_id = new TDBUniqueSearch('aluno_id', 'sample', 'Aluno', 'id', 'unit_id');


        // // add the fields
        // $this->form->addFields( [ new TLabel('Aluno') ], [ $aluno_id ] );


        // // set sizes
        // $aluno_id->setSize('100%');

        
        // // keep the form filled during navigation with session data
        // $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // // add the search form actions
        // $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        // $btn->class = 'btn btn-sm btn-primary';
        // //$this->form->addActionLink(_t('New'), new TAction(['ApontamentoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_aluno_id = new TDataGridColumn('aluno->nome', 'Aluno', 'left','100%');
        $column_f_1bim = new TDataGridColumn('f_1bim', 'F1ºBIM', 'center','20%');
        $column_f_2bim = new TDataGridColumn('f_2bim', 'F2ºBIM', 'center','20%');
        $column_f_3bim = new TDataGridColumn('f_3bim', 'F3ºBIM', 'center','20%');
        $column_f_4bim = new TDataGridColumn('f_4bim', 'F4ºBIM', 'center','20%');
        $column_tf_anual = new TDataGridColumn('tf_anual', 'TFA', 'center','20%');
        $column_p_1bim = new TDataGridColumn('p_1bim', 'P1ºBIM', 'center','20%');
        $column_p_2bim = new TDataGridColumn('p_2bim', 'P2ºBIM', 'center','20%');
        $column_p_3bim = new TDataGridColumn('p_3bim', 'P3ºBIM', 'center','20%');
        $column_p_4bim = new TDataGridColumn('p_4bim', 'P4ºBIM', 'center','20%');
        $column_tp_anual = new TDataGridColumn('tp_anual', 'TPA', 'center','20%');
        $column_n_1bim = new TDataGridColumn('n_1bim', 'N1ºBIM', 'center','20%');
        $column_n_2bim = new TDataGridColumn('n_2bim', 'N2ºBIM', 'center','20%');
        $column_n_3bim = new TDataGridColumn('n_3bim', 'N3ºBIM', 'center','20%');
        $column_n_4bim = new TDataGridColumn('n_4bim', 'N4ºBIM', 'center','20%');
        $column_MS1 = new TDataGridColumn('MS1', 'MS1', 'center','20%');
        $column_MS2 = new TDataGridColumn('MS2', 'MS2', 'center','20%');
        $column_MDS1 = new TDataGridColumn('MDS1', 'MDS1', 'center','20%');
        $column_MDS2 = new TDataGridColumn('MDS2', 'MDS2', 'center','20%');
        $column_REC12 = new TDataGridColumn('REC12', 'REC12', 'center','20%');
        $column_REC34 = new TDataGridColumn('REC34', 'REC32', 'center','20%');
        $column_MA = new TDataGridColumn('MA', 'MA', 'center','20%');
        $column_PF = new TDataGridColumn('PF', 'PF', 'center','20%');
        $column_MFA = new TDataGridColumn('MFA', 'MFA', 'center','20%');
        $column_resultado = new TDataGridColumn('resultado', 'Resultado', 'right','20%');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $column_id->setVisibility(false);
        $this->datagrid->addColumn($column_aluno_id);
        $this->datagrid->addColumn($column_f_1bim);
        $this->datagrid->addColumn($column_f_2bim);
        $this->datagrid->addColumn($column_f_3bim);
        $this->datagrid->addColumn($column_f_4bim);
        $this->datagrid->addColumn($column_tf_anual);
        $this->datagrid->addColumn($column_p_1bim);
        $this->datagrid->addColumn($column_p_2bim);
        $this->datagrid->addColumn($column_p_3bim);
        $this->datagrid->addColumn($column_p_4bim);
        $this->datagrid->addColumn($column_tp_anual);
        $this->datagrid->addColumn($column_n_1bim);
        $this->datagrid->addColumn($column_n_2bim);
        $this->datagrid->addColumn($column_n_3bim);
        $this->datagrid->addColumn($column_n_4bim);
        $this->datagrid->addColumn($column_MS1);
        $this->datagrid->addColumn($column_MS2);
        $this->datagrid->addColumn($column_MDS1);
        $this->datagrid->addColumn($column_MDS2);
        $this->datagrid->addColumn($column_REC12);
        $this->datagrid->addColumn($column_REC34);
        $this->datagrid->addColumn($column_MA);
        $this->datagrid->addColumn($column_PF);
        $this->datagrid->addColumn($column_MFA);
        $this->datagrid->addColumn($column_resultado);

        $action1 = new TDataGridAction(['ApontamentoForm', 'onEdit'], ['id'=>'{id}']);
        //$action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        //$this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();

        // $panel = new TPanelGroup();
        // $panel->add($this->datagrid);
        // //$panel->addFooter('footer');
        
        // // turn on horizontal scrolling inside panel body
        // $panel->getBody()->style = "overflow-x:auto;";
        
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
            $object = new Apontamento($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_aluno_id',   NULL);

        if (isset($data->aluno_id) AND ($data->aluno_id)) {
            $filter = new TFilter('aluno_id', '=', $data->aluno_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_aluno_id',   $filter); // stores the filter in the session
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
            //var_dump($param);
            // creates a repository for Apontamento
            $repository = new TRepository('Apontamento');
            $limit = 100;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('disciplina_id', '=', $param['disciplina_id'])); 
            $criteria->add(new TFilter('serie_id', '=', $param['serie_id']));
            $criteria->add(new TFilter('turma_id', '=', $param['turma_id']));
            $criteria->add(new TFilter('turno_id', '=', $param['turno_id']));
            $criteria->add(new TFilter('anoletivo_id', '=', $param['anoletivo_id']));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_aluno_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_aluno_id')); // add the session filter
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
            $object = new Apontamento($key, FALSE); // instantiates the Active Record
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
