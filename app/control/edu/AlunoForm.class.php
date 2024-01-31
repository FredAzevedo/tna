<?php

use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;

/**
 * AlunoForm Master/Detail
 * @author  <your name here>
 */
class AlunoForm extends TPage
{
    use Adianti\Base\AdiantiFileSaveTrait;
    protected $form; 
    protected $detail_list;
    
    public function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Aluno');
        $this->form->setFormTitle('Aluno');
        $this->form->setFieldSizes('100%');
        
        // master fields
        $id = new TEntry('id');

        $unit_id = new THidden('unit_id');
        $unit_id->setValue(TSession::getValue('userunitid'));

        $nome = new TEntry('nome');
        $nome->forceUpperCase();

        $cpf = new TEntry('cpf');
        $cpf->setMask('999.999.999-99');
        $rg = new TEntry('rg');
        $orgao_emissor = new TEntry('orgao_emissor');
        $orgao_emissor->forceUpperCase();
        $nascimento = new TDate('nascimento');
        $nascimento->setDatabaseMask('yyyy-mm-dd');
        $nascimento->setMask('dd/mm/yyyy');
        $cidade_nascimento = new TEntry('cidade_nascimento');
        $cidade_nascimento->forceUpperCase();
        $uf_nascimento = new TCombo('uf_nascimento');
        $uf_nascimento->addItems(Utilidades::uf());
        $sexo = new TCombo('sexo');
        $combo_sexo['M'] = 'MASCULINO';
        $combo_sexo['F'] = 'FEMININO';
        $sexo->addItems($combo_sexo);
        $data_registro = new TDate('data_registro');
        $data_registro->setDatabaseMask('yyyy-mm-dd');
        $data_registro->setMask('dd/mm/yyyy');

        $cep = new TEntry('cep');
        $buscaCep = new TAction(array($this, 'onCep'));
        $cep->setExitAction($buscaCep);
        $cep->setMask('99.999-999');
        $logradouro = new TEntry('logradouro');
        $logradouro->forceUpperCase();
        $numero = new TEntry('numero');
        $bairro = new TEntry('bairro');
        $bairro->forceUpperCase();
        $cidade = new TEntry('cidade');
        $cidade->forceUpperCase();
        $uf = new TEntry('uf');
        $uf = new TCombo('uf');
        $uf->addItems(Utilidades::uf());
        $complemento = new TEntry('complemento');
        $folha = new TEntry('folha');
        $folha->forceUpperCase();
        $livro = new TEntry('livro');
        $livro->forceUpperCase();
        $numero_registro = new TEntry('numero_registro');
        $numero_registro->forceUpperCase();
        $cartorio_nome = new TEntry('cartorio_nome');
        $cartorio_nome->forceUpperCase();
        $cartorio_municipio = new TEntry('cartorio_municipio');
        $cartorio_municipio->forceUpperCase();
        $cartorio_uf = new TCombo('cartorio_uf');
        $cartorio_uf->addItems(Utilidades::uf());
        $observacao = new TText('observacao');
        $instagram = new TEntry('instagram');
        $facebook = new TEntry('facebook');
        $tweeter = new TEntry('tweeter');
        $email = new TEntry('email');
        $tipo_sanguineo = new TCombo('tipo_sanguineo');
        $combo_tipo_sanguineo['A+'] = 'A+';
        $combo_tipo_sanguineo['A-'] = 'A-';
        $combo_tipo_sanguineo['B+'] = 'B+';
        $combo_tipo_sanguineo['B-'] = 'B-';
        $combo_tipo_sanguineo['AB+'] = 'AB+';
        $combo_tipo_sanguineo['AB-'] = 'AB-';
        $combo_tipo_sanguineo['O+'] = 'O+';
        $combo_tipo_sanguineo['O-'] = 'O-';
        $tipo_sanguineo->addItems($combo_tipo_sanguineo);
        $telefone = new TEntry('telefone');
        $telefone->setMask('(99) 99999-9999');

        $foto = new TImageCapture('foto');
        $foto->enableFileHandling();
        $foto->setAllowedExtensions(["jpg","jpeg","png","gif"]);
        $foto->setImagePlaceholder(new TImage("fas:camera #dde5ec"));
        $foto->setSize(200, 260);
        $foto->setCropSize(200, 260);

        $this->form->appendPage('Dados Principais');

        $this->form->addFields( [$unit_id] );

        // master fields
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Nome do Aluno'), $nome ],
                                       [ new TLabel('CPF'), $cpf ],
                                       [ new TLabel('Orgão Emissor'), $orgao_emissor ]
                                    );
        $row->layout = ['col-sm-2','col-sm-6','col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Nascimento'), $nascimento ],
                                       [ new TLabel('Naturalidade'), $cidade_nascimento ],
                                       [ new TLabel('UF'), $uf_nascimento ],
                                       [ new TLabel('Sexo'), $sexo ],
                                       [ new TLabel('RG'), $rg ]
                                    );
        $row->layout = ['col-sm-2','col-sm-4','col-sm-2','col-sm-2','col-sm-2'];

        
    

        $this->form->addContent( ['<hr><h4>Endereço</h4>'] );

        $row = $this->form->addFields( [ new TLabel('CEP'), $cep ],    
                                       [ new TLabel('Logradouro'), $logradouro ],
                                       [ new TLabel('Número'), $numero ],
                                       [ new TLabel('Bairro'), $bairro ]);
        $row->layout = ['col-sm-2', 'col-sm-5', 'col-sm-1', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Cidade'), $cidade ],    
                                       [ new TLabel('UF'), $uf ]);
        $row->layout = ['col-sm-5','col-sm-4', 'col-sm-3'];

        $this->form->addContent( ['<hr><h4>Responsáveis pelo aluno</h4>'] );  

        $mae_responsavel_id = new TDBUniqueSearch('mae_responsavel_id', 'sample', 'Responsavel', 'id', 'nome', 'nome');
        $mae_responsavel_id->setMinLength(0);
        //$mae_responsavel_id->setId('mae_responsavel_id');
        
        $pai_responsavel_id = new TDBUniqueSearch('pai_responsavel_id', 'sample', 'Responsavel', 'id', 'nome', 'nome');
        $pai_responsavel_id->setMinLength(0);
        //$pai_responsavel_id->setId('pai_responsavel_id');

        $row = $this->form->addFields( [ new TLabel('Nome da Mãe'), $mae_responsavel_id ],
                                       [ new TLabel('Nome do Pai'), $pai_responsavel_id ]
        );
        $row->layout = ['col-sm-5','col-sm-5'];

        $mae = $this->createButton('mae', ['ResponsavelFormView', 'onClear'], '', 'fa:plus-circle green', 
        ['class_return' => 'form_Aluno', 'field_return' => 'mae_responsavel_id']);
        $mae->class = 'btn btn-default inline-button';
        $mae->title = _t('New');
        $mae_responsavel_id->after($mae);
        $this->form->addField($mae);

        $pai = $this->createButton('pai', ['ResponsavelFormView', 'onClear'], '', 'fa:plus-circle green', 
        ['class_return' => 'form_Aluno', 'field_return' => 'pai_responsavel_id']);
        $pai->class = 'btn btn-default inline-button';
        $pai->title = _t('New');
        $pai_responsavel_id->after($pai);
        $this->form->addField($pai);

        $this->form->addContent( ['<hr><h4>Responsável pelo contrato</h4>'] );  

        $contrato_responsavel_id = new TDBUniqueSearch('contrato_responsavel_id', 'sample', 'Responsavel', 'id', 'nome', 'nome');
        $contrato_responsavel_id->setMinLength(0);
        //$contrato_responsavel_id->setId('contrato_responsavel_id');

        $row = $this->form->addFields( [ new TLabel('Nome do responsável pelo contrato'), $contrato_responsavel_id ],
                                       [ new TLabel(''),  ]
        );
        $row->layout = ['col-sm-5','col-sm-7'];

        $contrato = $this->createButton('contrato', ['ResponsavelFormView', 'onClear'], '', 'fa:plus-circle green', 
        ['class_return' => 'form_Aluno', 'field_return' => 'contrato_responsavel_id']);
        $contrato->class = 'btn btn-default inline-button';
        $contrato->title = _t('New');
        $contrato_responsavel_id->after($contrato);
        $this->form->addField($contrato);

        $this->form->appendPage('Dados Secundários');

        // $this->form->addContent( ['<hr><h4>Dados Complementares</h4>'] );   

        $row = $this->form->addFields( [ new TLabel('Folha'), $folha ],
                                       [ new TLabel('Livro'), $livro ],    
                                       [ new TLabel('Nº do Registro'), $numero_registro ],
                                       [ new TLabel('Data do Registro'), $data_registro ]
        );
        $row->layout = ['col-sm-4','col-sm-3', 'col-sm-3', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Nome do Cartório'), $cartorio_nome ],
                                       [ new TLabel('Município do Cartório'), $cartorio_municipio ],    
                                       [ new TLabel('UF Cartório'), $cartorio_uf ]
        );
        $row->layout = ['col-sm-5','col-sm-5', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Instagram'), $instagram ],
                                       [ new TLabel('Facebook'), $facebook ],
                                       [ new TLabel('Tweeter'), $tweeter ],
                                       [ new TLabel('Tipo Sanguíneo'), $tipo_sanguineo ]

        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Telefone'), $telefone ],
                                       [ new TLabel('Email'), $email ]
        );
        $row->layout = ['col-sm-2','col-sm-10'];

        $row = $this->form->addFields( [ new TLabel('Observação'), $observacao ]
        );
        $row->layout = ['col-sm-12'];

        $this->form->appendPage('Matrículas');
        // detail fields
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');
        $detail_serie_id = new TDBCombo('detail_serie_id', 'sample', 'Serie', 'id', 'nome');
        $detail_turma_id = new TDBCombo('detail_turma_id', 'sample', 'Turma', 'id', 'nome');
        $detail_turno_id = new TDBCombo('detail_turno_id', 'sample', 'Turno', 'id', 'nome');
        $detail_unit_id = new THidden('detail_unit_id');
        $detail_unit_id->setValue(TSession::getValue('userunitid'));
        $detail_anoletivo_id = new TDBCombo('detail_anoletivo_id', 'sample', 'AnoLetivo', 'id', 'ano');
        $detail_referencia = new TEntry('detail_referencia');
        $detail_referencia->setEditable(FALSE);
        $detail_status = new THidden('detail_status');
        $detail_status->setValue('A');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // detail fields
        //$this->form->addContent( ['<hr><h4>Matrícula</h4>'] );
        $this->form->addFields( [$detail_uniqid] );
        $this->form->addFields( [$detail_id] );
        $this->form->addFields( [$detail_unit_id]);
        $this->form->addFields( [$detail_status]);
        //$this->form->addFields( [new TLabel('Referencia')], [$detail_referencia] );

        $row = $this->form->addFields( [ new TLabel('Série'), $detail_serie_id ],
                                       [ new TLabel('Turma'), $detail_turma_id ],    
                                       [ new TLabel('Turno'), $detail_turno_id ],
                                       [ new TLabel('Ano Letivo'), $detail_anoletivo_id ],
                                       [ new TLabel('Referência'), $detail_referencia ]
        );
        $row->layout = ['col-sm-4','col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2'];

        $adicionar = TButton::create('adicionar', [$this, 'onDetailAdd'], 'Adicionar', 'fa:save');
        $adicionar->getAction()->setParameter('static','1');
        $adicionar->style = 'background-color: #b3d9ff';
        $row = $this->form->addFields([$adicionar])->style = 'padding: 5px 0px 0px 0px;';
        
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->disableDefaultClick();
        $this->detail_list->setId('Matricula_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        
        $this->detail_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( $col_serie_id = new TDataGridColumn('serie_id', 'Série', 'left', 100) );
        $this->detail_list->addColumn( $col_turma_id = new TDataGridColumn('turma_id', 'Turma', 'left', 100) );
        $this->detail_list->addColumn( $col_turno_id = new TDataGridColumn('turno_id', 'Turno', 'left', 100) );
        //$this->detail_list->addColumn( new TDataGridColumn('unit_id', 'Unit Id', 'left', 100) );
        $this->detail_list->addColumn( $col_ano_id = new TDataGridColumn('anoletivo_id', 'Ano letivo', 'left', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('referencia', 'Referencia', 'left', 100) );
        $this->detail_list->addColumn( $col_status = new TDataGridColumn('status', 'status', 'left', 100) );

        $col_status->setTransformer(array($this, 'changerColor'));
        $col_status->setVisibility(false);

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

        $action1 = new TDataGridAction([$this, 'onDetailEdit'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onMatricular']);
        $action2->setDisplayCondition( array($this, 'matricular') );
        $action2->setField('id');
        $action2->setField('serie_id');
        $action2->setField('aluno_id');
        $action2->setField('turma_id');
        $action2->setField('turno_id');
        $action2->setField('anoletivo_id');

        $action3 = new TDataGridAction([$this, 'onEditarMatricular']);
        $action3->setDisplayCondition( array($this, 'editarMatricula') );
        $action3->setField('id');
        $action3->setField('serie_id');
        $action3->setField('aluno_id');
        $action3->setField('turma_id');
        $action3->setField('turno_id');
        $action3->setField('anoletivo_id');
        
        $action3->setUseButton(TRUE);

        $this->detail_list->addAction($action1, _t('Edit'), 'fa:edit blue');
        $this->detail_list->addAction($action2, 'Matricular', 'fas:chalkboard-teacher red');
        $this->detail_list->addAction($action3, 'Visualizar', 'fas:eye black');
        
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
        $this->form->appendPage('Foto do Aluno');

        $row = $this->form->addFields(  [ new TLabel('Foto do Aluno'), $foto ],
                                        [ new TLabel(''),  ],
                                        [ new TLabel(''),  ],
                                        [ new TLabel(''),  ],
                                        [ new TLabel(''),  ]
        );
        $row->layout = ['col-sm-3','col-sm-2','col-sm-2','col-sm-2','col-sm-3'];

        $this->form->addAction( 'Save',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        // $this->form->addAction( 'Clear', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction( [$this, 'onExit'] ), 'fa:angle-double-left');
 
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }

    public function matricular( $object )
    {
        if ($object->status == "A")
        {
            return TRUE;
        }
        return FALSE;
    }

    public function editarMatricula( $object )
    {
        if ($object->status == "F")
        {
            return TRUE;
        }
        return FALSE;
    }

    public static function onMatricular($param)
    {   
        $aluno_id = $param['aluno_id'];
        $matricula_id = $param['id'];
        $serie_id = $param['serie_id'];
        $turma_id = $param['turma_id'];
        $turno_id = $param['turno_id'];
        $anoletivo_id = $param['anoletivo_id'];
        $unit_id = TSession::getValue('userunitid');

        //pegar disciplinas
        TTransaction::open('sample');
        $disciplinas = Grade::where('serie_id','=',$param['serie_id'])->load();
        foreach($disciplinas as $disciplina){

            GerarApontamento::apontar($aluno_id, $disciplina->disciplina_id, $matricula_id, $serie_id, $turma_id, $turno_id, $anoletivo_id, $unit_id);
        }

        //atualuzar tabela matricula para status = F
        $mat = new Matricula($matricula_id);
        $mat->status = 'F';
        $mat->store();

        $pos_action = new TAction(['AlunoList', 'onReload']);
        new TMessage('info', '<b>Matrícula </b>realizada com sucesso! ',$pos_action);
        TTransaction::close();
    }

    public static function onEditarMatricular($param)
    {
        try
        {   
            TTransaction::open('sample');
            if(isset($param['aluno_id'])){
                
                $aluno_id = $param['aluno_id'];
                $matricula_id = $param['id'];
                $serie_id = $param['serie_id'];
                $turma_id = $param['turma_id'];
                $turno_id = $param['turno_id'];
                $anoletivo_id = $param['anoletivo_id'];
                $unit_id = TSession::getValue('userunitid');

                AdiantiCoreApplication::loadPage('AlunoFormApontamento', 'onEdit', array(
                    'id'  => $aluno_id,
                    'key'  => $aluno_id,
                    'aluno_id'  => $aluno_id,
                    'matricula_id'  => $matricula_id,
                    'serie_id' => $serie_id,
                    'turma_id' => $turma_id,
                    'turno_id' => $turno_id,
                    'anoletivo_id' => $anoletivo_id,
                    'unit_id' => $unit_id
                ));

            }

            TTransaction::close();

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function changerColor($col_status, $object, $row)
    {
        if ($col_status == "A")
        {
            $row->style = "background: #FFF9A7";
        }
        else
        {
            $row->style = "background: #67D83A";
        }
    }

    public function createButton($name, $callback, $label, $image, $param = null)
    {
        $button = new TButton( $name );
        $button->setAction(new TAction( $callback, $param ), $label);
        $button->setImage( $image );
        return $button;
    }
    
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }

    public static function onCep($param)
    {
        try {
            $retorno = Utilidades::onCep($param['cep']);
            $objeto  = json_decode($retorno);
            
            if (isset($objeto->logradouro)){
                $obj                    = new stdClass();
                $obj->logradouro = $objeto->logradouro;
                $obj->bairro   = $objeto->bairro;
                $obj->cidade   = $objeto->localidade;
                $obj->uf       = $objeto->uf;
                $obj->codMuni  = $objeto->ibge;

                TForm::sendData('form_Aluno',$obj, false, false );
                unset($obj);
            }else{
                //new TMessage('info', 'Erro ao buscar endereço por este CEP.');
            }
        }catch (Exception $e){
            new TMessage('error', '<b>Error:</b> ' . $e->getMessage());
        }
    }

    public function onDetailAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
        
            if (empty($data->detail_serie_id) || empty($data->detail_turma_id) || empty($data->detail_turno_id) || empty($data->detail_anoletivo_id))
            {
                throw new Exception('Opa! Você esqueceu algum campo em branco. Verefique e tente novamente!');
            }
            
            if(empty($data->detail_referencia))
            {
                $data->detail_referencia = Utilidades::referencia(); 
            }
            
            $uniqid = !empty($data->detail_uniqid) ? $data->detail_uniqid : uniqid();
            
            $grid_data = [];
            $grid_data['uniqid'] = $uniqid;
            $grid_data['id'] = $data->detail_id;
            $grid_data['serie_id'] = $data->detail_serie_id;
            $grid_data['turma_id'] = $data->detail_turma_id;
            $grid_data['turno_id'] = $data->detail_turno_id;
            $grid_data['unit_id'] = $data->unit_id;
            $grid_data['anoletivo_id'] = $data->detail_anoletivo_id;
            $grid_data['referencia'] = $data->detail_referencia;
            $grid_data['status'] = $data->detail_status;

            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('Matricula_list', $uniqid, $row);

            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_serie_id = '';
            $data->detail_turma_id = '';
            $data->detail_turno_id = '';
            $data->detail_unit_id = '';
            $data->detail_anoletivo_id = '';
            $data->detail_referencia = '';
            $data->detail_status = '';
            
            TForm::sendData( 'form_Aluno', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function onDetailEdit( $param )
    {   
        if($param['status'] == 'A'){

            $data = new stdClass;
            $data->detail_uniqid = $param['uniqid'];
            $data->detail_id = $param['id'];
            $data->detail_serie_id = $param['serie_id'];
            $data->detail_turma_id = $param['turma_id'];
            $data->detail_turno_id = $param['turno_id'];
            $data->detail_unit_id = $param['unit_id'];
            $data->detail_anoletivo_id = $param['anoletivo_id'];
            $data->detail_referencia = $param['referencia'];
            $data->detail_status = $param['status'];

            // send data, do not fire change/exit events
            TForm::sendData( 'form_Aluno', $data, false, false );

        }else{
            new TMessage('info', 'ATENÇÃO: Esse registro não pode ser editado após efetivado a Matrícula!');
        }
    }
    
    public static function onDetailDelete( $param )
    {
        // clear detail form fields
        $data = new stdClass;
        $data->detail_uniqid = '';
        $data->detail_id = '';
        $data->detail_serie_id = '';
        $data->detail_turma_id = '';
        $data->detail_turno_id = '';
        $data->detail_unit_id = '';
        $data->detail_anoletivo_id = '';
        $data->detail_referencia = '';
        $data->detail_status = '';
        
        TForm::sendData( 'form_Aluno', $data, false, false );
        
        TDataGrid::removeRowById('Matricula_list', $param['uniqid']);
    }

   
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('sample');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Aluno($key);

                $matricula  = Matricula::where('aluno_id', '=', $key)->load();
                
                foreach( $matricula as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                
                TTransaction::close();
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public function onSave($param)
    {
        try
        {

            TTransaction::open('sample');
            
            $data = $this->form->getData();
            $this->form->validate();
            
            $master = new Aluno;
            $master->fromArray( (array) $data);
            $master->store();
            
            $foto_dir = 'files';
            $this->saveFile($master, $data, 'foto', $foto_dir);

            Matricula::where('aluno_id', '=', $master->id)->delete();
            
            if(isset($param['Matricula_list_serie_id'] ))
            {
                foreach( $param['Matricula_list_serie_id'] as $key => $item_id )
                {
                    $detail = new Matricula;
                    $detail->serie_id  = $param['Matricula_list_serie_id'][$key];
                    $detail->turma_id  = $param['Matricula_list_turma_id'][$key];
                    $detail->turno_id  = $param['Matricula_list_turno_id'][$key];
                    $detail->unit_id  = $param['unit_id'];
                    $detail->anoletivo_id  = $param['Matricula_list_anoletivo_id'][$key];
                    $detail->referencia  = $param['Matricula_list_referencia'][$key];
                    $detail->status  = $param['Matricula_list_status'][$key];
                    $detail->aluno_id = $master->id;
                    $detail->store();
                }
            }
            TTransaction::close(); 
            
            TForm::sendData('form_Aluno', (object) ['id' => $master->id]);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }
    }

    public function onExit()
    {
        $result = TSession::getValue('AlunoList');

        $query = isset($result['query']) ? $result['query'] : null;

        if (!empty($query))
        {
            TScript::create("
                Adianti.waitMessage = 'Listando...';__adianti_post_data('AlunoForm', '$query');                                 
        ");
        }
    }
}
