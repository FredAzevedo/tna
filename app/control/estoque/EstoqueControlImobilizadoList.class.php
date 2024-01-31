<?php
/**
 * EstoqueControlImobilizadoList Listing
 * @author Fred Azv.
 */
class EstoqueControlImobilizadoList extends TPage
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
        parent::setTargetContainer('adianti_right_panel');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_EstoqueControlImobilizado');
        $this->form->setFormTitle('Rastreamento de Imobilizados');

        // create the form fields
        $produto_id = new TDBUniqueSearch('produto_id', 'sample', 'Produto', 'id', 'produto_grupo_id');
        $local = new TDBCombo('local','sample','Viewlocal','id','nome_fantasia','nome_fantasia');
        $saldo = new TEntry('saldo');


        $row = $this->form->addFields( [ new TLabel('Local'), $local ],
                                       [ new TLabel('Saldo'), $saldo ]
        );
        $row->layout = ['col-sm-10','col-sm-2'];

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Fechar', new TAction(['EstoqueControlImobilizadoList', 'onExit']), 'fa:door green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        // $column_id = new TDataGridColumn('id', 'Id', 'right');
        // $column_unit_id = new TDataGridColumn('unit_id', 'Unit Id', 'right');
        $column_produto_id = new TDataGridColumn('produto->nome_produto', 'Produto', 'left');
        $column_local = new TDataGridColumn('localidade->nome_fantasia', 'Local', 'left');
        $column_saldo = new TDataGridColumn('saldo', 'Saldo', 'right');

        // add the columns to the DataGrid
        // $this->datagrid->addColumn($column_id);
        // $this->datagrid->addColumn($column_unit_id);
        // $this->datagrid->addColumn($column_produto_id);
        $this->datagrid->addColumn($column_local);
        $this->datagrid->addColumn($column_saldo);

        $column_saldo->setTotalFunction( function($values) {
            $total = array_sum((array) $values);
            //$total = Utilidades::formatar_valor($total); //(is_numeric($total)) ? round($total,2) : 0;
            return '<div id="saldo_total"> <b>Total: </b> ' . $total  . '</div>';
        });


        $action1 = new TDataGridAction(['EstoqueControlmovImobilizadoList', 'onReload'], ['produto_id'=>'{produto_id}', 'local'=>'{local}']);
        $this->datagrid->addAction($action1, 'Emplacamentos',   'far:search blue');

        
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
    
    public static function onCalcularTotal() {

        $items = (array) TSession::getValue(__CLASS__.'_items');

        $saldo = array_reduce($items, function ($carry, $item) {
            $carry += Utilidades::to_number($item['saldo']);
            return $carry;
        }, 0);

        $saldo_str = Utilidades::formatar_valor($saldo);

        TScript::create(" $('#saldo_total').text('{$saldo_str}') ");

        $data = new stdClass();
        $data->saldo = $saldo_str;

        TForm::sendData( 'form_search_EstoqueControlImobilizado', $data );

        $vlr = $saldo;
    }

    public function onInlineEdit($param)
    {
        try
        {
           
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('sample'); 
            $object = new EstoqueControlImobilizado($key); 
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
    

    public function onSearch()
    {
        
        $data = $this->form->getData();
        
        
        TSession::setValue(__CLASS__.'_filter_produto_id',   NULL);
        TSession::setValue(__CLASS__.'_filter_local',   NULL);
        TSession::setValue(__CLASS__.'_filter_saldo',   NULL);

        if (isset($data->produto_id) AND ($data->produto_id)) {
            $filter = new TFilter('produto_id', '=', $data->produto_id); 
            TSession::setValue(__CLASS__.'_filter_produto_id',   $filter); 
        }


        if (isset($data->local) AND ($data->local)) {
            $filter = new TFilter('local', 'like', "%{$data->local}%");
            TSession::setValue(__CLASS__.'_filter_local',   $filter); 
        }


        if (isset($data->saldo) AND ($data->saldo)) {
            $filter = new TFilter('saldo', 'like', "%{$data->saldo}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_saldo',   $filter); 
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
            
            // creates a repository for EstoqueControlImobilizado
            $repository = new TRepository('EstoqueControlImobilizado');
            $limit = 100;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('produto_id', '=', $param['produto_id']));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_produto_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_produto_id')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_local')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_local')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_saldo')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_saldo')); // add the session filter
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

    public static function onExit()
    {
        AdiantiCoreApplication::loadPage('EstoqueImobilizadoList', 'onReload');
    }
}
