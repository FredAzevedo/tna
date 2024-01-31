<?php
/**
 * AlunoForm Master/Detail
 * @author  Fred Azv.
 */
class AlunoFormApontamento extends TPage
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
        $this->form = new BootstrapFormBuilder('form_Aluno');
        $this->form->setFormTitle('Disciplinas Matriculadas');
        
        // master fields
        $id = new THidden('id');
        $id->setValue($param['aluno_id']);
        
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->disableDefaultClick();
        $this->detail_list->setId('Apontamento_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = "min-width: 100%; width:100%;margin-bottom: 10px";
        
        // items
        $this->detail_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( $col_disciplina_id =new TDataGridColumn('disciplina_id', 'Disciplina', 'left', 100) );
        $this->detail_list->addColumn( $col_matricula_id = new TDataGridColumn('matricula_id', 'Matricula Id', 'left', 100) );
        $this->detail_list->addColumn( $col_serie_id = new TDataGridColumn('serie_id', 'Serie Id', 'left', 100) );
        $this->detail_list->addColumn( $col_turma_id = new TDataGridColumn('turma_id', 'Turma Id', 'left', 100) );
        $this->detail_list->addColumn( $col_turno_id = new TDataGridColumn('turno_id', 'Turno Id', 'left', 100) );
        $this->detail_list->addColumn( $col_ano_id = new TDataGridColumn('anoletivo_id', 'Ano', 'right', 100) );
        $this->detail_list->addColumn( $col_f_1bim = new TDataGridColumn('f_1bim', 'F1ºBIM', 'right', 100) );
        $this->detail_list->addColumn( $col_f_2bim = new TDataGridColumn('f_2bim', 'F2ºBIM', 'right', 100) );
        $this->detail_list->addColumn( $col_f_3bim = new TDataGridColumn('f_3bim', 'F3ºBIM', 'right', 100) );
        $this->detail_list->addColumn( $col_f_4bim = new TDataGridColumn('f_4bim', 'F4ºBIM', 'right', 100) );    
        $this->detail_list->addColumn( $col_tf_anual = new TDataGridColumn('tf_anual', 'TFA', 'right', 100) );    
        $this->detail_list->addColumn( $col_p_1bim = new TDataGridColumn('p_1bim', 'P1ºBIM', 'right', 100) );
        $this->detail_list->addColumn( $col_p_2bim = new TDataGridColumn('p_2bim', 'P2ºBIM', 'right', 100) );
        $this->detail_list->addColumn( $col_p_3bim = new TDataGridColumn('p_3bim', 'P3ºBIM', 'right', 100) );
        $this->detail_list->addColumn( $col_p_4bim = new TDataGridColumn('p_4bim', 'P4ºBIM', 'right', 100) );
        $this->detail_list->addColumn( $col_p_4bim = new TDataGridColumn('tp_anual', 'TPA', 'right', 100) );
        $this->detail_list->addColumn( $col_n_1bim = new TDataGridColumn('n_1bim', 'N1ºBIM', 'right', 100) );
        $this->detail_list->addColumn( $col_n_2bim = new TDataGridColumn('n_2bim', 'N2ºBIM', 'right', 100) );
        $this->detail_list->addColumn( $col_n_3bim = new TDataGridColumn('n_3bim', 'N3ºBIM', 'right', 100) );
        $this->detail_list->addColumn( $col_n_4bim = new TDataGridColumn('n_4bim', 'N4ºBIM', 'right', 100) );
        $col_matricula_id->setVisibility(false);
        $col_serie_id->setVisibility(false);
        $col_turma_id->setVisibility(false);
        $col_turno_id->setVisibility(false);
    

        $col_disciplina_id->setTransformer(function($value) {
            TTransaction::open('sample');
            $serie = new Disciplina($value);
            TTransaction::close();
            return $serie->nome;
        });

        $col_matricula_id->setTransformer(function($value) {
            TTransaction::open('sample');
            $serie = new Matricula($value);
            TTransaction::close();
            return $serie->referencia;
        });

        $col_serie_id->setTransformer(function($value) {
            TTransaction::open('sample');
            $serie = new Serie($value);
            TTransaction::close();
            return $serie->nome;
        });

        $col_turma_id->setTransformer(function($value) {
            TTransaction::open('sample');
            $turma = new Turma($value);
            TTransaction::close();
            return $turma->nome;
        });

        $col_turno_id->setTransformer(function($value) {
            TTransaction::open('sample');
            $turno = new Turno($value);
            TTransaction::close();
            return $turno->nome;
        });

        $col_ano_id->setTransformer(function($value) {
            TTransaction::open('sample');
            $ano = new AnoLetivo($value);
            TTransaction::close();
            return $ano->ano;
        });

        // detail actions
        $action1 = new TDataGridAction([$this, 'onDetailEdit'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDetailDelete']);
        $action2->setField('uniqid');
        
        // add the actions to the datagrid
        //$this->detail_list->addAction($action1, _t('Edit'), 'fa:edit blue');
        //$this->detail_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
        //$this->form->addAction( 'Save',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        //$this->form->addAction( 'Clear', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction('Fechar', new TAction([$this,'onClose']), 'fa:angle-double-left');
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
            $grid_data['matricula_id'] = $data->detail_matricula_id;
            $grid_data['serie_id'] = $data->detail_serie_id;
            $grid_data['turma_id'] = $data->detail_turma_id;
            $grid_data['turno_id'] = $data->detail_turno_id;
            $grid_data['anoletivo_id'] = $data->detail_anoletivo_id;
            
            // insert row dynamically
            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('Apontamento_list', $uniqid, $row);
            
            // clear detail form fields
            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_disciplina_id = '';
            $data->detail_matricula_id = '';
            $data->detail_serie_id = '';
            $data->detail_turma_id = '';
            $data->detail_turno_id = '';
            $data->detail_anoletivo_id = '';
            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_Aluno', $data, false, false );
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
        $data->detail_matricula_id = $param['matricula_id'];
        $data->detail_serie_id = $param['serie_id'];
        $data->detail_turma_id = $param['turma_id'];
        $data->detail_turno_id = $param['turno_id'];
        $data->detail_anoletivo_id = $param['anoletivo_id'];
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Aluno', $data, false, false );
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
        $data->detail_matricula_id = '';
        $data->detail_serie_id = '';
        $data->detail_turma_id = '';
        $data->detail_turno_id = '';
        $data->detail_anoletivo_id = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Aluno', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('Apontamento_list', $param['uniqid']);
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
                
                $object = new Aluno($key);
                $items  = Apontamento::where('aluno_id', '=', $key)
                ->where('anoletivo_id', '=', $param['anoletivo_id'])
                ->load();
                
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
            
            $master = new Aluno;
            $master->fromArray( (array) $data);
            $master->store();
            
            Apontamento::where('aluno_id', '=', $master->id)->delete();
            
            if( $param['Apontamento_list_disciplina_id'] )
            {
                foreach( $param['Apontamento_list_disciplina_id'] as $key => $item_id )
                {
                    $detail = new Apontamento;
                    $detail->disciplina_id  = $param['Apontamento_list_disciplina_id'][$key];
                    $detail->matricula_id  = $param['Apontamento_list_matricula_id'][$key];
                    $detail->serie_id  = $param['Apontamento_list_serie_id'][$key];
                    $detail->turma_id  = $param['Apontamento_list_turma_id'][$key];
                    $detail->turno_id  = $param['Apontamento_list_turno_id'][$key];
                    $detail->anoletivo_id  = $param['Apontamento_list_anoletivo_id'][$key];
                    $detail->aluno_id = $master->id;
                    $detail->store();
                }
            }
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_Aluno', (object) ['id' => $master->id]);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
        AdiantiCoreApplication::loadPage('AlunoForm', 'onEdit', array(
            'id'  => $param['id'],
            'key'  => $param['id'],
        ));
    }
}
