<?php
/**
 * ApontamentoForm Form
 * @author  Fred Azv.
 */
class ApontamentoForm extends TPage
{
    protected $form; // form
    
    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ApontamentoForm');
        $this->form->setFormTitle('Apontamentos');
        
        parent::setTargetContainer('adianti_right_panel');
        // create the form fields
        $id = new TEntry('id');
        $aluno_id = new TDBCombo('aluno_id', 'sample', 'Aluno', 'id', 'nome');
        $aluno_id->setEditable(FALSE);
        $disciplina_id = new TDBCombo('disciplina_id', 'sample', 'Disciplina', 'id', 'nome');
        $disciplina_id->setEditable(FALSE);
        $matricula_id = new THidden('matricula_id');
        $serie_id = new TDBCombo('serie_id', 'sample', 'Serie', 'id', 'nome');
        $serie_id->setEditable(FALSE);
        $turma_id = new TDBCombo('turma_id', 'sample', 'Turma', 'id', 'nome');
        $turma_id->setEditable(FALSE);
        $turno_id = new TDBCombo('turno_id', 'sample', 'Turno', 'id', 'nome');
        $turno_id->setEditable(FALSE);
        $anoletivo_id = new TDBCombo('anoletivo_id', 'sample', 'AnoLetivo', 'id', 'ano');
        $anoletivo_id->setEditable(FALSE);
        $unit_id = new THidden('unit_id');
        $a_1bim = new TEntry('a_1bim');
        $a_1bim->setMask('9!');
        //$a_1bim->setEditable(FALSE);
        $a_2bim = new TEntry('a_2bim');
        $a_2bim->setMask('9!');
        //$a_2bim->setEditable(FALSE);
        $a_3bim = new TEntry('a_3bim');
        $a_3bim->setMask('9!');
        //$a_3bim->setEditable(FALSE);
        $a_4bim = new TEntry('a_4bim');
        $a_4bim->setMask('9!');
        //$a_4bim->setEditable(FALSE);
        $ta_anual = new TEntry('ta_anual');
        $ta_anual->setEditable(FALSE);
        $f_1bim = new TEntry('f_1bim');
        $f_1bim->setEditable(FALSE);
        $f_1bim->setMask('9!');
        $f_2bim = new TEntry('f_2bim');
        $f_2bim->setEditable(FALSE);
        $f_2bim->setMask('9!');
        $f_3bim = new TEntry('f_3bim');
        $f_3bim->setEditable(FALSE);
        $f_3bim->setMask('9!');
        $f_4bim = new TEntry('f_4bim');
        $f_4bim->setEditable(FALSE);
        $f_4bim->setMask('9!');
        $tf_anual = new TEntry('tf_anual');
        $tf_anual->setEditable(FALSE);
        $p_1bim = new TEntry('p_1bim');
        $p_1bim->setMask('9!');
        $p_2bim = new TEntry('p_2bim');
        $p_2bim->setMask('9!');
        $p_3bim = new TEntry('p_3bim');
        $p_3bim->setMask('9!');
        $p_4bim = new TEntry('p_4bim');
        $p_4bim->setMask('9!');
        $tp_anual = new TEntry('tp_anual');
        $tp_anual->setEditable(FALSE);
        $ft_1bim = new TEntry('ft_1bim');
        $ft_1bim->setEditable(FALSE);
        $ft_2bim = new TEntry('ft_2bim');
        $ft_2bim->setEditable(FALSE);
        $ft_3bim = new TEntry('ft_3bim');
        $ft_3bim->setEditable(FALSE);
        $ft_4bim = new TEntry('ft_4bim');
        $ft_4bim->setEditable(FALSE);
        $ft_anual = new TEntry('ft_anual');
        $ft_anual->setEditable(FALSE);
        $n_1bim = new TEntry('n_1bim');
        $n_2bim = new TEntry('n_2bim');
        $n_3bim = new TEntry('n_3bim');
        $n_4bim = new TEntry('n_4bim');
        $MS1 = new TEntry('MS1');
        $MS1->setEditable(FALSE);
        $MS2 = new TEntry('MS2');
        $MS2->setEditable(FALSE);
        $MDS1 = new TEntry('MDS1');
        $MDS1->setEditable(FALSE);
        $MDS2 = new TEntry('MDS2');
        $MDS2->setEditable(FALSE);
        $REC12 = new TEntry('REC12');
        $REC34 = new TEntry('REC34');
        $MA = new TEntry('MA');
        $MA->setEditable(FALSE);
        $PF = new TEntry('PF');
        $MFA = new TEntry('MFA');
        $MFA->setEditable(FALSE);
        $resultado = new TCombo('resultado');
        $resultado_itens = [];
        $resultado_itens["AP"] = 'Aprovado';
        $resultado_itens["RP"] = 'Reprovado';
        $resultado_itens["TR"] = 'Transferido';
        $resultado_itens["EV"] = 'Evadido';
        $resultado->addItems($resultado_itens);

        //$resultado->setEditable(FALSE);


        $n_1bim->setNumericMask(1,',','.', true);
        $n_2bim->setNumericMask(1,',','.', true);
        $n_3bim->setNumericMask(1,',','.', true);
        $n_4bim->setNumericMask(1,',','.', true);
        $MS1->setNumericMask(1,',','.', true);
        $MS2->setNumericMask(1,',','.', true);
        $MDS1->setNumericMask(1,',','.', true);
        $MDS1->setNumericMask(1,',','.', true);
        $MDS2->setNumericMask(1,',','.', true);
        $MDS2->setNumericMask(1,',','.', true);
        $REC12->setNumericMask(1,',','.', true);
        $REC34->setNumericMask(1,',','.', true);
        $MA->setNumericMask(1,',','.', true);
        $MA->setNumericMask(1,',','.', true);
        $PF->setNumericMask(1,',','.', true);
        $MFA->setNumericMask(1,',','.', true);
        $MFA->setNumericMask(1,',','.', true);

        $this->form->appendPage('Parâmetros');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Aluno'), $aluno_id ]  
        );
        $row->layout = ['col-sm-2','col-sm-10'];

        $this->form->addFields( [$matricula_id, $unit_id] );

        $row = $this->form->addFields( [ new TLabel('Disciplina'), $disciplina_id ],
                                       [ new TLabel('Série'), $serie_id ],
                                       [ new TLabel('Turma'), $turma_id ],
                                       [ new TLabel('Turno'), $turno_id ],
                                       [ new TLabel('Ano Letivo'), $anoletivo_id ]

        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-2','col-sm-2','col-sm-2'];

        $this->form->addContent( ['<hr><h4>Total de Aulas</h4>'] );

        $row = $this->form->addFields( [ new TLabel('Total de aulas 1ºBim'), $a_1bim ],
                                       [ new TLabel('Total de aulas 2ºBim'), $a_2bim ],
                                       [ new TLabel('Total de aulas 3ºBim'), $a_3bim ],
                                       [ new TLabel('Total de aulas 4ºBim'), $a_4bim ]

        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $row = $this->form->addFields(  [ new TLabel(''),  ],
                                        [ new TLabel(''),  ],
                                        [ new TLabel(''),  ],
                                        [ new TLabel('Total de aulas do Ano'), $ta_anual ]
        
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $this->form->appendPage('Presenças e Faltas');

        $this->form->addContent( ['<hr><h4>Apontamentos de Presenças e Faltas</h4>'] );

        $row = $this->form->addFields( [ new TLabel('Total de Presenças 1ºBim'), $p_1bim ],
                                       [ new TLabel('Total de Presenças 2ºBim'), $p_2bim ],
                                       [ new TLabel('Total de Presenças 3ºBim'), $p_3bim ],
                                       [ new TLabel('Total de Presenças 4ºBim'), $p_4bim ]

        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel(''),  ],
                                       [ new TLabel(''),  ],
                                       [ new TLabel(''),  ],
                                       [ new TLabel('Total de Presenças do Ano'), $tp_anual ]
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        // $this->form->addContent( ['<hr><h4>Apontamentos de Faltas</h4>'] );

        $row = $this->form->addFields( [ new TLabel('Total de Faltas 1ºBim'), $f_1bim ],
                                       [ new TLabel('Total de Faltas 2ºBim'), $f_2bim ],
                                       [ new TLabel('Total de Faltas 3ºBim'), $f_3bim ],
                                       [ new TLabel('Total de Faltas 4ºBim'), $f_4bim ]

        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel(''),  ],
                                       [ new TLabel(''),  ],
                                       [ new TLabel(''),  ],
                                       [ new TLabel('Total de Faltas do Ano'), $tf_anual ]
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $this->form->addContent( ['<hr><h4>Frequências em %</h4>'] );

        $row = $this->form->addFields( [ new TLabel('Total de Frequências 1ºBim'), $ft_1bim ],
                                       [ new TLabel('Total de Frequências 2ºBim'), $ft_2bim ],
                                       [ new TLabel('Total de Frequências 3ºBim'), $ft_3bim ],
                                       [ new TLabel('Total de Frequências 4ºBim'), $ft_4bim ]

        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel(''),  ],
                                       [ new TLabel(''),  ],
                                       [ new TLabel(''),  ],
                                       [ new TLabel('Total de Frequências do Ano'), $ft_anual ]
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $this->form->appendPage('Notas');

        $this->form->addContent( ['<hr><h4>Apontamentos de Notas</h4>'] );

        $row = $this->form->addFields(  [ new TLabel('Total de Notas 1ºBim'), $n_1bim ],
                                        [ new TLabel('Total de Notas 2ºBim'), $n_2bim ]
                                    
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $row = $this->form->addFields(  [ new TLabel('Média do 1ª Semestre'), $MS1 ],
                                        [ new TLabel('Recuperação 1ª Semestre'), $REC12 ],
                                        [ new TLabel('Média definitiva do 1ª Semestre'), $MDS1 ],
                                        [ new TLabel(''),  ]
                                    
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];


        $row = $this->form->addFields(  [ new TLabel('Total de Notas 3ºBim'), $n_3bim ],
                                        [ new TLabel('Total de Notas 4ºBim'), $n_4bim ]
                                    
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $row = $this->form->addFields(  [ new TLabel('Média do 2ª Semestre'), $MS2 ],
                                        [ new TLabel('Recuperação 2ª Semestre'), $REC34 ],
                                        [ new TLabel('Média definitiva do 2ª Semestre'), $MDS2 ],
                                        [ new TLabel(''),  ]
                                    
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $row = $this->form->addFields(  [ new TLabel('Média Anual'), $MA ],
                                        [ new TLabel('Prova Final'), $PF ],
                                        [ new TLabel('Média Final Anual'), $MFA ],
                                        [ new TLabel('Resultado'), $resultado ]
                                    
        );
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
        TPage::include_js('app/resources/ApontamentoForm.js');
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new Apontamento;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
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
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('sample'); // open a transaction
                $object = new Apontamento($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
