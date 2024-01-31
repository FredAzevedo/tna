<?php
/**
 * PcDespesaSeek Listing
 * @author  <your name here>
 */
class PcDespesaSeek extends TWindow
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        parent::setTitle( AdiantiCoreTranslator::translate('Search record') );
        parent::setSize(0.7, null);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_PcDespesa');
        //$this->form->setFormTitle('PcDespesa');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        /*$nivel1 = new TEntry('nivel1');
        $nivel2 = new TEntry('nivel2');
        $nivel3 = new TEntry('nivel3');
        $nivel4 = new TEntry('nivel4');*/
        $nome = new TDBUniqueSearch('nome','sample','PcDespesa','nome','nome','nome');


        /*$row = $this->form->addFields( [ new TLabel('1º Nível'), $nivel1 ],
                                       [ new TLabel('2º Nível'), $nivel2 ],
                                       [ new TLabel('3º Nível'), $nivel3 ],
                                       [ new TLabel('4º Nível'), $nivel4 ]);
        $row->layout = ['col-sm-3','col-sm-3', 'col-sm-3', 'col-sm-3'];*/

        $row = $this->form->addFields( [ new TLabel('Plano de Contas'), $nome ]);
        $row->layout = ['col-sm-12'];                                
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('PcDespesa_filter_data') );
        
        // add the search form actions
        $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_nivel1 = new TDataGridColumn('nivel1', '1º Nível', 'left');
        $column_nivel2 = new TDataGridColumn('nivel2', '2º Nível', 'left');
        $column_nivel3 = new TDataGridColumn('nivel3', '3º Nível', 'left');
        $column_nivel4 = new TDataGridColumn('nivel4', '4º Nível', 'left');
        $column_nome = new TDataGridColumn('nome', 'Plano de Contas', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nivel1);
        $this->datagrid->addColumn($column_nivel2);
        $this->datagrid->addColumn($column_nivel3);
        $this->datagrid->addColumn($column_nivel4);
        $this->datagrid->addColumn($column_nome);

        
        // create SELECT action
        $action_select = new TDataGridAction(array($this, 'onSelect'));
        $action_select->setUseButton(TRUE);
        $action_select->setButtonClass('nopadding');
        $action_select->setLabel('');
        $action_select->setImage('fa:hand-pointer-o green');
        $action_select->setField('id');
        $this->datagrid->addAction($action_select);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%;margin-bottom:0;border-radius:0';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('PcDespesaSeek_filter_nivel1',   NULL);
        TSession::setValue('PcDespesaSeek_filter_nivel2',   NULL);
        TSession::setValue('PcDespesaSeek_filter_nivel3',   NULL);
        TSession::setValue('PcDespesaSeek_filter_nivel4',   NULL);
        TSession::setValue('PcDespesaSeek_filter_nome',   NULL);

        if (isset($data->nivel1) AND ($data->nivel1)) {
            $filter = new TFilter('nivel1', 'like', "%{$data->nivel1}%"); // create the filter
            TSession::setValue('PcDespesaSeek_filter_nivel1',   $filter); // stores the filter in the session
        }


        if (isset($data->nivel2) AND ($data->nivel2)) {
            $filter = new TFilter('nivel2', 'like', "%{$data->nivel2}%"); // create the filter
            TSession::setValue('PcDespesaSeek_filter_nivel2',   $filter); // stores the filter in the session
        }


        if (isset($data->nivel3) AND ($data->nivel3)) {
            $filter = new TFilter('nivel3', 'like', "%{$data->nivel3}%"); // create the filter
            TSession::setValue('PcDespesaSeek_filter_nivel3',   $filter); // stores the filter in the session
        }


        if (isset($data->nivel4) AND ($data->nivel4)) {
            $filter = new TFilter('nivel4', 'like', "%{$data->nivel4}%"); // create the filter
            TSession::setValue('PcDespesaSeek_filter_nivel4',   $filter); // stores the filter in the session
        }


        if (isset($data->nome) AND ($data->nome)) {
            $filter = new TFilter('nome', 'like', "%{$data->nome}%"); // create the filter
            TSession::setValue('PcDespesaSeek_filter_nome',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('PcDespesa_filter_data', $data);
        
        $param=array();
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
            
            // creates a repository for PcDespesa
            $repository = new TRepository('PcDespesa');
            $limit = 5;
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
            

            if (TSession::getValue('PcDespesaSeek_filter_nivel1')) {
                $criteria->add(TSession::getValue('PcDespesaSeek_filter_nivel1')); // add the session filter
            }


            if (TSession::getValue('PcDespesaSeek_filter_nivel2')) {
                $criteria->add(TSession::getValue('PcDespesaSeek_filter_nivel2')); // add the session filter
            }


            if (TSession::getValue('PcDespesaSeek_filter_nivel3')) {
                $criteria->add(TSession::getValue('PcDespesaSeek_filter_nivel3')); // add the session filter
            }


            if (TSession::getValue('PcDespesaSeek_filter_nivel4')) {
                $criteria->add(TSession::getValue('PcDespesaSeek_filter_nivel4')); // add the session filter
            }


            if (TSession::getValue('PcDespesaSeek_filter_nome')) {
                $criteria->add(TSession::getValue('PcDespesaSeek_filter_nome')); // add the session filter
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
     * Executed when the user chooses the record
     */
    public static function onSelect($param)
    {
        try
        {
            $key = $param['key'];
            TTransaction::open('sample');
            
            // load the active record
            $object = PcDespesa::find($key);
            
            // closes the transaction
            TTransaction::close();
            
            $send = new StdClass;
            $send->pcdespesa_id = $object->id;
            TForm::sendData('form_name_REPLACE_HERE', $send);
            
            parent::closeWindow(); // closes the window
        }
        catch (Exception $e)
        {
            $send = new StdClass;
            $send->pcdespesa_id = '';
            TForm::sendData('form_name_REPLACE_HERE', $send);
            
            // undo pending operations
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
