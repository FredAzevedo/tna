<?php
/**
 * DisciplinaAulasForm Master/Detail
 * @author  Fred Azv.
 */
class DisciplinaAulasForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct($param)
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DisciplinaAulasForm');
        $this->form->setFormTitle('Disciplinas x Aulas');
        
        // master fields
        $id = new TDBCombo('id', 'sample', 'Disciplina', 'id', 'nome');
        $id->setValue($param['id']);

        // detail fields
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');
        $detail_serie_id = new TDBCombo('detail_serie_id', 'sample', 'Serie', 'id', 'nome');
        $detail_a_1bim = new TEntry('detail_a_1bim');
        $detail_a_2bim = new TEntry('detail_a_2bim');
        $detail_a_3bim = new TEntry('detail_a_3bim');
        $detail_a_4bim = new TEntry('detail_a_4bim');
        $detail_ta_anual = new TEntry('detail_ta_anual');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // master fields
        $this->form->addFields( [new TLabel('Disciplina')], [$id] );
        
        // detail fields
        //$this->form->addContent( ['<h4>Details</h4><hr>'] );
        $this->form->addFields( [$detail_uniqid] );
        $this->form->addFields( [$detail_id] );

        $row = $this->form->addFields( [ new TLabel('Série'), $detail_serie_id ]
        );
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields(  [ new TLabel('1ºBIM'), $detail_a_1bim ],
                                        [ new TLabel('2ºBIM'), $detail_a_2bim ]
        );
        $row->layout = ['col-sm-6','col-sm-6'];

        $row = $this->form->addFields(  [ new TLabel('3ºBIM'), $detail_a_3bim ],
                                        [ new TLabel('4ºBIM'), $detail_a_4bim ]
        );
        $row->layout = ['col-sm-6','col-sm-6'];

        $row = $this->form->addFields(  [ new TLabel(''),  ],
                                        [ new TLabel('Anual'), $detail_ta_anual ]
        );
        $row->layout = ['col-sm-6','col-sm-6'];

        $add = TButton::create('add', [$this, 'onDetailAdd'], 'Registrar', 'fa:plus-circle green');
        $add->getAction()->setParameter('static','1');
        $this->form->addFields( [], [$add] );
        
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->setId('DisciplinaAulas_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        
        // items
        $this->detail_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( $col_serie_id = new TDataGridColumn('serie_id', 'Série', 'rigth', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('a_1bim', '1ºBim', 'left', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('a_2bim', '2ºBim', 'left', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('a_3bim', '3ºBim', 'left', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('a_4bim', '4ºBim', 'left', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('ta_anual', 'Anual', 'left', 100) );

        $col_serie_id->setTransformer(function($value) {
            TTransaction::open('sample');
            $ano = new Serie($value);
            TTransaction::close();
            return $ano->nome;
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
        
        $this->form->addAction( 'Save',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        //$this->form->addAction( 'Clear', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction('Fechar', new TAction([$this,'onClose']), 'fa:angle-double-left');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
        TPage::include_js('app/resources/DisciplinaAulasForm.js');
    }
    
    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
        AdiantiCoreApplication::loadPage('DisciplinaList', 'onEdit');
    }

    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    

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
            $grid_data['serie_id'] = $data->detail_serie_id;
            $grid_data['a_1bim'] = $data->detail_a_1bim;
            $grid_data['a_2bim'] = $data->detail_a_2bim;
            $grid_data['a_3bim'] = $data->detail_a_3bim;
            $grid_data['a_4bim'] = $data->detail_a_4bim;
            $grid_data['ta_anual'] = $data->detail_ta_anual;
            
            // insert row dynamically
            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('DisciplinaAulas_list', $uniqid, $row);
            
            // clear detail form fields
            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_serie_id = '';
            $data->detail_a_1bim = '';
            $data->detail_a_2bim = '';
            $data->detail_a_3bim = '';
            $data->detail_a_4bim = '';
            $data->detail_ta_anual = '';
            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_DisciplinaAulasForm', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    

    public static function onDetailEdit( $param )
    {
        $data = new stdClass;
        $data->detail_uniqid = $param['uniqid'];
        $data->detail_id = $param['id'];
        $data->detail_serie_id = $param['serie_id'];
        $data->detail_a_1bim = $param['a_1bim'];
        $data->detail_a_2bim = $param['a_2bim'];
        $data->detail_a_3bim = $param['a_3bim'];
        $data->detail_a_4bim = $param['a_4bim'];
        $data->detail_ta_anual = $param['ta_anual'];
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_DisciplinaAulasForm', $data, false, false );
    }
    

    public static function onDetailDelete( $param )
    {
        // clear detail form fields
        $data = new stdClass;
        $data->detail_uniqid = '';
        $data->detail_id = '';
        $data->detail_serie_id = '';
        $data->detail_a_1bim = '';
        $data->detail_a_2bim = '';
        $data->detail_a_3bim = '';
        $data->detail_a_4bim = '';
        $data->detail_ta_anual = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_DisciplinaAulasForm', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('DisciplinaAulas_list', $param['uniqid']);
    }
    

    public function onEdit($param)
    {
        try
        {
            TTransaction::open('sample');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Disciplina($key);
                $items  = DisciplinaAulas::where('disciplina_id', '=', $key)->load();
                
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
    

    public function onSave($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('sample');
            TTransaction::setLogger(new TLoggerSTD); // debugar sql
            $data = $this->form->getData();
            $this->form->validate();
            
            // $master = new Disciplina;
            // $master->fromArray( (array) $data);
            // $master->store();
            
            DisciplinaAulas::where('disciplina_id', '=', $master->id)->delete();
            
            if( $param['DisciplinaAulas_list_serie_id'] )
            {
                foreach( $param['DisciplinaAulas_list_serie_id'] as $key => $item_id )
                {
                    $detail = new DisciplinaAulas;
                    $detail->serie_id  = $param['DisciplinaAulas_list_serie_id'][$key];
                    $detail->a_1bim  = $param['DisciplinaAulas_list_a_1bim'][$key];
                    $detail->a_2bim  = $param['DisciplinaAulas_list_a_2bim'][$key];
                    $detail->a_3bim  = $param['DisciplinaAulas_list_a_3bim'][$key];
                    $detail->a_4bim  = $param['DisciplinaAulas_list_a_4bim'][$key];
                    $detail->ta_anual  = $param['DisciplinaAulas_list_ta_anual'][$key];
                    $detail->disciplina_id = $data->id;
                    $detail->store();
                }
            }
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_DisciplinaAulasForm', (object) ['id' => $data->id]);
            
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
