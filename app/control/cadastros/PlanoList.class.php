<?php
/**
 * PlanoList Form List
 * @author  Fred Azv
 */
class PlanoList extends TPage
{
    protected $form; // form
    protected $datagrid; // datagrid
    protected $pageNavigation;
    protected $loaded;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Plano');
        $this->form->setFormTitle('Planos');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        
        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade');
        

        $pc_receita_id = new TDBSeekButton('pc_receita_id', 'sample', $this->form->getName(), 'PcReceita', 'nome', 'pc_receita_id', 'pc_receita_nome');
        $pc_receita_id->addValidation('Plano de Contas', new TRequiredValidator);
        $pc_receita_nome = new TEntry('pc_receita_nome');
        $pc_receita_nome->setEditable(FALSE);

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                    //    [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Plano de Contas'), $pc_receita_id ],
                                       [ new TLabel('Descrição do Plano de Contas'), $pc_receita_nome ]);
        $row->layout = ['col-sm-2','col-sm-2','col-sm-8'];

        
        
        // create the form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['PlanoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'left');
        $column_pc_receita_nome = new TDataGridColumn('pc_receita_nome', 'Descrição do Plano de contas', 'left');
        $column_unidades = new TDataGridColumn('id', 'Unidade(s)', 'left');

        $get_column_unidades = function($plano_id) {

            $plano = new Plano($plano_id);
      
            $lista_plano_unidade = $plano->getPlanoUnidade();
            $unidades = '';
            if(count($lista_plano_unidade) > 0)
            {
                for ($i = 0; $i < count($lista_plano_unidade); $i++) {
                    if($i != 0)
                        $unidades = $unidades . ', ' . $lista_plano_unidade[$i]->system_unit->unidade;
                    else
                        $unidades = $lista_plano_unidade[$i]->system_unit->unidade;
                }
                return $unidades;
            }
            else 
                return 'Nenhuma unidade vinculada';

            
        };

        $column_unidades->setTransformer( $get_column_unidades );

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        //$this->datagrid->addColumn($column_unidades);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_pc_receita_nome);
        
        // creates two datagrid actions
        $action1 = new TDataGridAction(['PlanoForm', 'onEdit']);
        //$action1->setUseButton(TRUE);
        //$action1->setButtonClass('btn btn-default');
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue fa-lg');
        $action1->setField('id');
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        //$action2->setUseButton(TRUE);
        //$action2->setButtonClass('btn btn-default');
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red fa-lg');
        $action2->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
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
        $container->add(TPanelGroup::pack('', $this->datagrid));
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }


    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('PlanoList_filter_id',   NULL);
        TSession::setValue('PlanoList_filter_unit_id',   NULL);
        TSession::setValue('PlanoList_filter_pc_receita_id',   NULL);

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', $data->id); // create the filter
            TSession::setValue('PlanoList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->unit_id) AND ($data->unit_id)) {
            $filter = new TFilter('unit_id', '=', $data->unit_id); // create the filter
            TSession::setValue('PlanoList_filter_unit_id',   $filter); // stores the filter in the session
        }


        if (isset($data->pc_receita_id) AND ($data->pc_receita_id)) {
            $filter = new TFilter('pc_receita_id', '=', $data->pc_receita_id); // create the filter
            TSession::setValue('PlanoList_filter_pc_receita_id',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Plano_filter_data', $data);
        
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
            
            // creates a repository for Plano
            $repository = new TRepository('Plano');
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


            if (TSession::getValue('PlanoList_filter_id')) {
                $criteria->add(TSession::getValue('PlanoList_filter_id')); // add the session filter
            }

            if (TSession::getValue('PlanoList_filter_unit_id')) {
                $criteria->add(TSession::getValue('PlanoList_filter_unit_id')); // add the session filter
            }

            if (TSession::getValue('PlanoList_filter_pc_receita_id')) {
                $criteria->add(TSession::getValue('PlanoList_filter_pc_receita_id')); // add the session filter
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
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
            $key = $param['key']; // get the parameter $key
            TTransaction::open('sample'); // open a transaction with database
            $object = new Plano($key, FALSE); // instantiates the Active Record
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
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
