<?php

use Adianti\Control\TWindow;

/**
 * EstoqueControlmovImobilizadoList Listing
 * @author  Fred Azv
 */
class EstoqueControlmovImobilizadoList extends TWindow
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
        //parent::setTitle('');
        parent::setSize(0.7, 0.7);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_EstoqueControlmovImobilizado');
        $this->form->setFormTitle('Movimentação de Imobilizado no Local');
        
        
        // create the form fields
        $alocado = new TEntry('alocado');
        $estado = new TEntry('estado');
        $validade_mes = new TEntry('validade_mes');
        $data_avaliacao = new TEntry('data_avaliacao');
        $valor_justo = new TEntry('valor_justo');
        $emplacamento = new TEntry('emplacamento');
        $tipo = new TEntry('tipo');
        $saldo = new TEntry('saldo');

        $this->form->addFields( [ new TLabel('Nº do Emplacamento') ], [ $emplacamento ] );
        $emplacamento->setSize('100%');

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        // $this->form->addActionLink(_t('New'), new TAction(['EstoqueControlmovImobilizadoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_alocado = new TDataGridColumn('alocado', 'Alocado', 'left');
        $column_estado = new TDataGridColumn('tipoEstado', 'Estado', 'left');
        $column_validade_mes = new TDataGridColumn('validade_mes', 'Validade Mes', 'right');
        $column_data_avaliacao = new TDataGridColumn('data_avaliacao', 'Data Avaliacao', 'center');
        $column_valor_justo = new TDataGridColumn('valor_justo', 'Valor Justo', 'right');
        $column_emplacamento = new TDataGridColumn('emplacamento', 'Emplacamento', 'left');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'left');
        $column_saldo = new TDataGridColumn('saldo', 'Saldo', 'right');
        $column_created_at = new TDataGridColumn('created_at', 'Movimentação', 'center');

        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };

        $column_valor_justo->setTransformer( $format_value );

        $column_data_avaliacao->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $column_created_at->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_alocado);
        $this->datagrid->addColumn($column_emplacamento);
        $this->datagrid->addColumn($column_estado);
        $this->datagrid->addColumn($column_validade_mes);
        $this->datagrid->addColumn($column_data_avaliacao);
        $this->datagrid->addColumn($column_valor_justo);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_saldo);
        $this->datagrid->addColumn($column_created_at);

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
            $object = new EstoqueControlmovImobilizado($key); // instantiates the Active Record
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
        TSession::setValue(__CLASS__.'_filter_alocado',   NULL);
        TSession::setValue(__CLASS__.'_filter_estado',   NULL);
        TSession::setValue(__CLASS__.'_filter_validade_mes',   NULL);
        TSession::setValue(__CLASS__.'_filter_data_avaliacao',   NULL);
        TSession::setValue(__CLASS__.'_filter_valor_justo',   NULL);
        TSession::setValue(__CLASS__.'_filter_emplacamento',   NULL);
        TSession::setValue(__CLASS__.'_filter_tipo',   NULL);
        TSession::setValue(__CLASS__.'_filter_saldo',   NULL);

        if (isset($data->alocado) AND ($data->alocado)) {
            $filter = new TFilter('alocado', 'like', "%{$data->alocado}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_alocado',   $filter); // stores the filter in the session
        }


        if (isset($data->estado) AND ($data->estado)) {
            $filter = new TFilter('estado', 'like', "%{$data->estado}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_estado',   $filter); // stores the filter in the session
        }


        if (isset($data->validade_mes) AND ($data->validade_mes)) {
            $filter = new TFilter('validade_mes', 'like', "%{$data->validade_mes}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_validade_mes',   $filter); // stores the filter in the session
        }


        if (isset($data->data_avaliacao) AND ($data->data_avaliacao)) {
            $filter = new TFilter('data_avaliacao', 'like', "%{$data->data_avaliacao}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_data_avaliacao',   $filter); // stores the filter in the session
        }


        if (isset($data->valor_justo) AND ($data->valor_justo)) {
            $filter = new TFilter('valor_justo', 'like', "%{$data->valor_justo}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_valor_justo',   $filter); // stores the filter in the session
        }


        if (isset($data->emplacamento) AND ($data->emplacamento)) {
            $filter = new TFilter('emplacamento', 'like', "%{$data->emplacamento}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_emplacamento',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo) AND ($data->tipo)) {
            $filter = new TFilter('tipo', 'like', "%{$data->tipo}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_tipo',   $filter); // stores the filter in the session
        }


        if (isset($data->saldo) AND ($data->saldo)) {
            $filter = new TFilter('saldo', 'like', "%{$data->saldo}%"); // create the filter
            TSession::setValue(__CLASS__.'_filter_saldo',   $filter); // stores the filter in the session
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
            
            // creates a repository for EstoqueControlmovImobilizado
            $repository = new TRepository('EstoqueControlmovImobilizado');
            $limit = 20;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('produto_id', '=', $param['produto_id']));
            $criteria->add(new TFilter('local', '=', $param['local']));
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue(__CLASS__.'_filter_alocado')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_alocado')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_estado')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_estado')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_validade_mes')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_validade_mes')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_data_avaliacao')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_data_avaliacao')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_valor_justo')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_valor_justo')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_emplacamento')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_emplacamento')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_tipo')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_tipo')); // add the session filter
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
}
