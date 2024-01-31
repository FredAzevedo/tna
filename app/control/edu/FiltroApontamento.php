<?php
/**
 * ApontamentoList Listing
 * @author  Fred Azv.
 */
class FiltroApontamento extends TPage
{
    private $form; // form

    public function __construct()
    {
        parent::__construct();
        
        // creates the form
         // creates the form
        $this->form = new BootstrapFormBuilder('form_FiltroApontamento');
        $this->form->setFormTitle('Filtro de Apontamentos');
        $this->form->setFieldSizes('100%');
        
        // master fields
        $disciplina_id = new TDBCombo('disciplina_id', 'sample', 'Disciplina', 'id', 'nome');
        $serie_id = new TDBCombo('serie_id', 'sample', 'Serie', 'id', 'nome');
        $turma_id = new TDBCombo('turma_id', 'sample', 'Turma', 'id', 'nome');
        $turno_id = new TDBCombo('turno_id', 'sample', 'Turno', 'id', 'nome');
        $anoletivo_id = new TDBCombo('anoletivo_id', 'sample', 'AnoLetivo', 'id', 'ano');

        $unit_id = new THidden('unit_id');
        $unit_id->setValue(TSession::getValue('userunitid'));
        $this->form->addFields( [$unit_id]);

        $row = $this->form->addFields( [ new TLabel('Disciplina'), $disciplina_id ],
                                       [ new TLabel('Série'), $serie_id ],    
                                       [ new TLabel('Turma'), $turma_id ],
                                       [ new TLabel('Turma'), $turno_id ],
                                       [ new TLabel('Turma'), $anoletivo_id ]
                                    );
        $row->layout = ['col-sm-3','col-sm-3', 'col-sm-2','col-sm-2','col-sm-2'];

        $this->form->addAction( 'Apontar',  new TAction([$this, 'onApontamento']), 'fa:serach');
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }

    public function onApontamento($param)
    {
        try
        {     
            TTransaction::open('sample');
            if(isset($param['disciplina_id'])){

                $disciplina_id = $param['disciplina_id'];
                $serie_id = $param['serie_id'];
                $turma_id = $param['turma_id'];
                $turno_id = $param['turno_id'];
                $anoletivo_id = $param['anoletivo_id'];
                $unit_id = $param['unit_id'];

                AdiantiCoreApplication::loadPage('ApontamentoList', 'onReload', array(
                    'disciplina_id'  => $disciplina_id,
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

}
