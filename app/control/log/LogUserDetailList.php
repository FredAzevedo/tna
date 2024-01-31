<?php
/**
 * LogUserDetailList Listing
 * @author  Fred Azv.
 */
class LogUserDetailList extends TPage
{
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_JuridicoAtendimento');
        $this->form->setFormTitle('Lista de Campos do Log');
        
        parent::setTargetContainer('adianti_right_panel');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_pkvalue = new TDataGridColumn('pkvalue', 'ID', 'left');
        $column_operation = new TDataGridColumn('trad_acooes', 'Ação', 'left');
        $column_columnname = new TDataGridColumn('columnname', 'Nome do Campo', 'left');
        $column_oldvalue = new TDataGridColumn('oldvalue', 'Valor Antigo', 'left');
        $column_newvalue = new TDataGridColumn('newvalue', 'Valor Novo', 'left');

        $column_operation->setTransformer( function($value, $object, $row) {
            $div = new TElement('span');
            $div->style="text-shadow:none; font-size:12px";
            if ($value == 'Criado')
            {
                $div->class="label label-success";
            }
            else if ($value == 'Apagado')
            {
                $div->class="label label-danger";
            }
            else if ($value == 'Alterado')
            {
                $div->class="label label-info";
            }
            $div->add($value);
            return $div;
        });


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_pkvalue);
        $this->datagrid->addColumn($column_operation);
        $this->datagrid->addColumn($column_columnname);
        $this->datagrid->addColumn($column_oldvalue);
        $this->datagrid->addColumn($column_newvalue);


        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $this->form->addHeaderActionLink( _t('Close'), new TAction([$this, 'onClose']), 'fa:times red');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        
        parent::add($container);
    }
    
    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'sample'
            TTransaction::open('sample');
            
            // creates a repository for SystemChangeLog
            $repository = new TRepository('SystemChangeLog');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->add(new TFilter('transaction_id','=',$param['transaction_id']));
            $criteria->add(new TFilter('columnname','<>','created_at'));
            $criteria->add(new TFilter('columnname','<>','updated_at'));
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            
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

    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload')))) )
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
