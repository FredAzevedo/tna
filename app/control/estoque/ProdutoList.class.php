<?php
/**
 * ProdutoList Listing
 * @author  <your name here>
 */
class ProdutoList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Produto');
        $this->form->setFormTitle('Produto / Serviço');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        
        $tipo = new TCombo('tipo');
        $tipo->addItems(Utilidades::tipo_produto_servico());

        $nome_produto = new TEntry('nome_produto');

        $produto_grupo_id = new TDBUniqueSearch('produto_grupo_id', 'sample', 'ProdutoGrupo', 'id', 'nome');
        $produto_subgrupo_id = new TDBUniqueSearch('produto_subgrupo_id', 'sample', 'ProdutoSubgrupo', 'id', 'produto_grupo_id');


        // add the fields
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Tipo'), $tipo ],
                                       [ new TLabel('Descrição do produto/serviço'), $nome_produto]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-8'];

        $row = $this->form->addFields( [ new TLabel('Grupo'), $produto_grupo_id ],
                                       [ new TLabel('Sub-Grupo'), $produto_subgrupo_id ]);
        $row->layout = ['col-sm-6', 'col-sm-6'];

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Produto_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['ProdutoForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_tipo = new TDataGridColumn('produtoServico', 'Tipo', 'left');
        $column_nome_produto = new TDataGridColumn('nome_produto', 'Descrição do Produto', 'left');
        $column_produto_grupo_id = new TDataGridColumn('produto_grupo->nome', 'Grupo', 'right');
        $column_produto_subgrupo_id = new TDataGridColumn('produto_subgrupo->nome', 'Sub-Grupo', 'right');
        

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_nome_produto);
        $this->datagrid->addColumn($column_produto_grupo_id);
        $this->datagrid->addColumn($column_produto_subgrupo_id);


        
        // create EDIT action
        $action_edit = new TDataGridAction(['ProdutoForm', 'onEdit']);
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
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $botao = $this->form->addHeaderActionLink( 'Expandir',  new TAction([$this, 'onClose'], ['register_state' => 'false']), 'fa:search' );
        $botao->class = "btn btn-info btn-sm";
        $botao->id = 'custom-id-botao';

        TScript::create('
            $(document).ready(function(){
              $("form .panel-body").toggleClass("collapse");
              $("#custom-id-botao").click(function(){
                event.preventDefault();
                $(".card-body.panel-body").toggleClass("collapse show");    
              });
            });
        ');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    public static function onClose(){}

    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('sample'); // open a transaction with database
            $object = new Produto($key); // instantiates the Active Record
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
        TSession::setValue('ProdutoList_filter_id',   NULL);
        TSession::setValue('ProdutoList_filter_tipo',   NULL);
        TSession::setValue('ProdutoList_filter_nome_produto',   NULL);
        TSession::setValue('ProdutoList_filter_produto_grupo_id',   NULL);
        TSession::setValue('ProdutoList_filter_produto_subgrupo_id',   NULL);
        TSession::setValue('ProdutoList_filter_barras',   NULL);
        TSession::setValue('ProdutoList_filter_preco_venda',   NULL);
        TSession::setValue('ProdutoList_filter_unit_id',   NULL);

        $filter = new TFilter('unit_id','=', TSession::getvalue('userunitid'));

        if (isset($data->id) AND ($data->id)) {
            $filter = new TFilter('id', '=', "$data->id"); // create the filter
            TSession::setValue('ProdutoList_filter_id',   $filter); // stores the filter in the session
        }


        if (isset($data->tipo) AND ($data->tipo)) {
            $filter = new TFilter('tipo', '=', "$data->tipo"); // create the filter
            TSession::setValue('ProdutoList_filter_tipo',   $filter); // stores the filter in the session
        }


        if (isset($data->nome_produto) AND ($data->nome_produto)) {
            $filter = new TFilter('nome_produto', 'like', "%{$data->nome_produto}%"); // create the filter
            TSession::setValue('ProdutoList_filter_nome_produto',   $filter); // stores the filter in the session
        }


        if (isset($data->produto_grupo_id) AND ($data->produto_grupo_id)) {
            $filter = new TFilter('produto_grupo_id', '=', "$data->produto_grupo_id"); // create the filter
            TSession::setValue('ProdutoList_filter_produto_grupo_id',   $filter); // stores the filter in the session
        }


        if (isset($data->produto_subgrupo_id) AND ($data->produto_subgrupo_id)) {
            $filter = new TFilter('produto_subgrupo_id', '=', "$data->produto_subgrupo_id"); // create the filter
            TSession::setValue('ProdutoList_filter_produto_subgrupo_id',   $filter); // stores the filter in the session
        }


        if (isset($data->barras) AND ($data->barras)) {
            $filter = new TFilter('barras', 'like', "%{$data->barras}%"); // create the filter
            TSession::setValue('ProdutoList_filter_barras',   $filter); // stores the filter in the session
        }


        if (isset($data->preco_venda) AND ($data->preco_venda)) {
            $filter = new TFilter('preco_venda', 'like', "%{$data->preco_venda}%"); // create the filter
            TSession::setValue('ProdutoList_filter_preco_venda',   $filter); // stores the filter in the session
        }


        if (isset($data->unit_id) AND ($data->unit_id)) {
            $filter = new TFilter('unit_id', 'like', "%{$data->unit_id}%"); // create the filter
            TSession::setValue('ProdutoList_filter_unit_id',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Produto_filter_data', $data);
        
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
            
            // creates a repository for Produto
            $repository = new TRepository('Produto');
            $limit = 15;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));
            $criteria->add(new TFilter('tipo', '=', "P"));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('ProdutoList_filter_id')) {
                $criteria->add(TSession::getValue('ProdutoList_filter_id')); // add the session filter
            }


            if (TSession::getValue('ProdutoList_filter_tipo')) {
                $criteria->add(TSession::getValue('ProdutoList_filter_tipo')); // add the session filter
            }


            if (TSession::getValue('ProdutoList_filter_nome_produto')) {
                $criteria->add(TSession::getValue('ProdutoList_filter_nome_produto')); // add the session filter
            }


            if (TSession::getValue('ProdutoList_filter_produto_grupo_id')) {
                $criteria->add(TSession::getValue('ProdutoList_filter_produto_grupo_id')); // add the session filter
            }


            if (TSession::getValue('ProdutoList_filter_produto_subgrupo_id')) {
                $criteria->add(TSession::getValue('ProdutoList_filter_produto_subgrupo_id')); // add the session filter
            }


            if (TSession::getValue('ProdutoList_filter_barras')) {
                $criteria->add(TSession::getValue('ProdutoList_filter_barras')); // add the session filter
            }


            if (TSession::getValue('ProdutoList_filter_preco_venda')) {
                $criteria->add(TSession::getValue('ProdutoList_filter_preco_venda')); // add the session filter
            }


            if (TSession::getValue('ProdutoList_filter_unit_id')) {
                $criteria->add(TSession::getValue('ProdutoList_filter_unit_id')); // add the session filter
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
            $key=$param['key']; // get the parameter $key
            TTransaction::open('sample'); // open a transaction with database
            $object = new Produto($key, FALSE); // instantiates the Active Record
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
