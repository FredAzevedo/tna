<?php
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBMultiSearch;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
/**
 * FornecedorList Listing
 * @author  Fred Aze.
 */
class FornecedorList extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Fornecedor');
        $this->form->setFormTitle('Fornecedor');
        

        // create the form fields
        $nome_fantasia = new TDBUniqueSearch('nome_fantasia','sample','Fornecedor','nome_fantasia','nome_fantasia');
        $razao_social = new TDBUniqueSearch('razao_social','sample','Fornecedor','razao_social','razao_social');
        $cpf_cnpj = new TEntry('cpf_cnpj');
        $cidade = new TEntry('cidade');
        $uf = new TEntry('uf');
        $parceria = new TCombo('parceria');
        //$combo[''] = '';
        $combo['S'] = 'Sim';
        $combo['N'] = 'Não';
        $parceria->addItems($combo);


        // add the fields        
        $row = $this->form->addFields( [ new TLabel('Nome Fantasia'), $nome_fantasia ],    
                                       [ new TLabel('CPF/CNPJ'), $cpf_cnpj ],
                                       [ new TLabel('Cidade'), $cidade ],
                                       [ new TLabel('UF'), $uf ],
                                       [ new TLabel('Parceria?'), $parceria ]);
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-3', 'col-sm-1', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Razão Solcial'), $razao_social ]);
        $row->layout = ['col-sm-4'];
    
        // set sizes
        $nome_fantasia->setSize('100%');
        $cpf_cnpj->setSize('100%');
        $cidade->setSize('100%');
        $uf->setSize('100%');
        $parceria->setSize('100%');

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Fornecedor_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['FornecedorForm', 'onEdit']), 'fa:plus green');
        
        //$this->form->addExpandButton();

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        


        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'ID', 'right');
        $column_nome_fantasia = new TDataGridColumn('nome_fantasia', 'Nome Fantasia', 'left');
        $column_razao_social = new TDataGridColumn('razao_social', 'Razão Social', 'left');
        $column_cpf_cnpj = new TDataGridColumn('cpf_cnpj', 'CPF/CNPJ', 'left');
        $column_cidade = new TDataGridColumn('cidade', 'Cidade', 'left');
        $column_uf = new TDataGridColumn('uf', 'UF', 'left');
        $column_parceria = new TDataGridColumn('parceria', 'Parceria', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome_fantasia);
        $this->datagrid->addColumn($column_razao_social);
        $this->datagrid->addColumn($column_cpf_cnpj);
        $this->datagrid->addColumn($column_cidade);
        $this->datagrid->addColumn($column_uf);
        $this->datagrid->addColumn($column_parceria);

        
        // create EDIT action
        $action_edit = new TDataGridAction(['FornecedorForm', 'onEdit']);
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
        
        /*$container->adianti_target_container = 'FornecedorList';
        $container->adianti_target_title = 'Fornecedor ';*/
        
        parent::add($container);
    }
    
    public static function onClose(){}

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
            $object = new Fornecedor($key); // instantiates the Active Record
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
        TSession::setValue('FornecedorList_filter_nome_fantasia',   NULL);
        TSession::setValue('FornecedorList_filter_razao_social',   NULL);
        TSession::setValue('FornecedorList_filter_cpf_cnpj',   NULL);
        TSession::setValue('FornecedorList_filter_cidade',   NULL);
        TSession::setValue('FornecedorList_filter_uf',   NULL);
        TSession::setValue('FornecedorList_filter_parceria',   NULL);

        if (isset($data->nome_fantasia) AND ($data->nome_fantasia)) {
            $filter = new TFilter('nome_fantasia', 'like', "%{$data->nome_fantasia}%"); // create the filter
            TSession::setValue('FornecedorList_filter_nome_fantasia',   $filter); // stores the filter in the session
        }

        if (isset($data->razao_social) AND ($data->razao_social)) {
            $filter = new TFilter('razao_social', 'like', "%{$data->razao_social}%"); // create the filter
            TSession::setValue('FornecedorList_filter_razao_social',   $filter); // stores the filter in the session
        }

        if (isset($data->cpf_cnpj) AND ($data->cpf_cnpj)) {
            $filter = new TFilter('cpf_cnpj', 'like', "%{$data->cpf_cnpj}%"); // create the filter
            TSession::setValue('FornecedorList_filter_cpf_cnpj',   $filter); // stores the filter in the session
        }


        if (isset($data->cidade) AND ($data->cidade)) {
            $filter = new TFilter('cidade', 'like', "%{$data->cidade}%"); // create the filter
            TSession::setValue('FornecedorList_filter_cidade',   $filter); // stores the filter in the session
        }


        if (isset($data->uf) AND ($data->uf)) {
            $filter = new TFilter('uf', 'like', "%{$data->uf}%"); // create the filter
            TSession::setValue('FornecedorList_filter_uf',   $filter); // stores the filter in the session
        }


        if (isset($data->parceria) AND ($data->parceria)) {
            $filter = new TFilter('parceria', 'like', "%{$data->parceria}%"); // create the filter
            TSession::setValue('FornecedorList_filter_parceria',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Fornecedor_filter_data', $data);
        
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
            
            // creates a repository for Fornecedor
            $repository = new TRepository('Fornecedor');
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
            
            TSession::setValue('FornecedorList', parse_url($_SERVER['REQUEST_URI']));
            
            if (TSession::getValue('FornecedorList_filter_nome_fantasia')) {
                $criteria->add(TSession::getValue('FornecedorList_filter_nome_fantasia')); // add the session filter
            }

            if (TSession::getValue('FornecedorList_filter_razao_social')) {
                $criteria->add(TSession::getValue('FornecedorList_filter_razao_social')); // add the session filter
            }

            if (TSession::getValue('FornecedorList_filter_cpf_cnpj')) {
                $criteria->add(TSession::getValue('FornecedorList_filter_cpf_cnpj')); // add the session filter
            }


            if (TSession::getValue('FornecedorList_filter_cidade')) {
                $criteria->add(TSession::getValue('FornecedorList_filter_cidade')); // add the session filter
            }


            if (TSession::getValue('FornecedorList_filter_uf')) {
                $criteria->add(TSession::getValue('FornecedorList_filter_uf')); // add the session filter
            }


            if (TSession::getValue('FornecedorList_filter_parceria')) {
                $criteria->add(TSession::getValue('FornecedorList_filter_parceria')); // add the session filter
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
            $object = new Fornecedor($key, FALSE); // instantiates the Active Record
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
