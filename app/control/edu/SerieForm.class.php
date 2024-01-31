<?php

use Adianti\Widget\Wrapper\TDBCombo;

/**
 * SerieForm Master/Detail
 * @author  <your name here>
 */
class SerieForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setTargetContainer('adianti_right_panel');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Serie');
        $this->form->setFormTitle('Série');
        $this->form->setFieldSizes('100%');
        
        // master fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Nome da Série'), $nome ]
        );
        $row->layout = ['col-sm-2','col-sm-10'];

        // detail fields
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');
        $detail_disciplina_id = new TDBCombo('detail_disciplina_id', 'sample', 'Disciplina', 'id', 'nome');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // detail fields
        $this->form->addContent( ['<hr><h5>Adicione abaixo a Grade de disciplinas associadas a essa série</h5>'] );
        $this->form->addFields( [$detail_uniqid] );
        $this->form->addFields( [$detail_id] );
  
        $row = $this->form->addFields( [ new TLabel('Disciplinas'), $detail_disciplina_id ]);
        $row->layout = ['col-sm-12'];

        $adicionar = TButton::create('adicionar', [$this, 'onDetailAdd'], 'Adicionar grade a Disciplina', 'fa:save');
        $adicionar->getAction()->setParameter('static','1');
        $adicionar->style = 'background-color: #b3d9ff';
        $row = $this->form->addFields([$adicionar])->style = 'padding: 5px 0px 0px 0px;';
        
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->setId('Grade_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = "min-width: 200px; width:100%;margin-bottom: 10px;";
        
        // items
        $this->detail_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( $column_nome = new TDataGridColumn('disciplina_id', 'Grade', 'left', '100%') );

        $column_nome->setTransformer(function($value) {
            TTransaction::open('sample');
            $obj = new Disciplina($value);
            TTransaction::close();
            return $obj->nome;
        });

        // detail actions
        $action1 = new TDataGridAction([$this, 'onDetailEdit'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDetailDelete']);
        $action2->setField('uniqid');
        
        // add the actions to the datagrid
        $this->detail_list->addAction($action1, _t('Edit'), 'fa:edit blue');
        $this->detail_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
        $this->form->addAction( 'Salvar',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        $this->form->addAction( 'Fechar', new TAction(['SerieList', 'onReload']), 'fa:eraser red');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    
    /**
     * Clear form
     * @param $param URL parameters
     */
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Add detail item
     * @param $param URL parameters
     */
    public function onDetailAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            /** validation sample
            if (empty($data->fieldX))
            {
                throw new Exception('The field fieldX is required');
            }
            **/
            
            $uniqid = !empty($data->detail_uniqid) ? $data->detail_uniqid : uniqid();
            
            $grid_data = [];
            $grid_data['uniqid'] = $uniqid;
            $grid_data['id'] = $data->detail_id;
            $grid_data['disciplina_id'] = $data->detail_disciplina_id;
            
            // insert row dynamically
            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('Grade_list', $uniqid, $row);
            
            // clear detail form fields
            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_disciplina_id = '';
            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_Serie', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Edit detail item
     * @param $param URL parameters
     */
    public static function onDetailEdit( $param )
    {
        $data = new stdClass;
        $data->detail_uniqid = $param['uniqid'];
        $data->detail_id = $param['id'];
        $data->detail_disciplina_id = $param['disciplina_id'];
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Serie', $data, false, false );
    }
    
    /**
     * Delete detail item
     * @param $param URL parameters
     */
    public static function onDetailDelete( $param )
    {
        // clear detail form fields
        $data = new stdClass;
        $data->detail_uniqid = '';
        $data->detail_id = '';
        $data->detail_disciplina_id = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Serie', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('Grade_list', $param['uniqid']);
    }
    
    /**
     * Load Master/Detail data from database to form
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('sample');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Serie($key);
                $items  = Grade::where('serie_id', '=', $key)->load();
                
                foreach( $items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form to database
     */
    public function onSave($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('sample');
            
            $data = $this->form->getData();
            $this->form->validate();
            
            $master = new Serie;
            $master->fromArray( (array) $data);
            $master->store();
            
            Grade::where('serie_id', '=', $master->id)->delete();
            
            if( $param['Grade_list_disciplina_id'] )
            {
                foreach( $param['Grade_list_disciplina_id'] as $key => $item_id )
                {
                    $detail = new Grade;
                    $detail->disciplina_id  = $param['Grade_list_disciplina_id'][$key];
                    $detail->serie_id = $master->id;
                    $detail->store();
                }
            }
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_Serie', (object) ['id' => $master->id]);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
}
