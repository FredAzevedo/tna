<?php
/**
 * RelFrequenciaFiltro Form
 * @author  Fred Azc
 */
class RelFrequenciaFiltro extends TPage
{
    protected $form; // form
    
    public function __construct( $param )
    {
        parent::__construct();
        
        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder('form_RelFrequenciaFiltro');
        $this->form->setFormTitle('Declaração de Frequência');
        $this->form->setFieldSizes('100%');

        $aluno_id = new TDBCombo('aluno_id', 'sample', 'Aluno', 'id', 'nome');
        $aluno_id->setValue($param['id']);
        $aluno_id->setEditable(FALSE);
        $unit_id = new THidden('unit_id');
        $unit_id->setValue(TSession::getValue('userunitid'));
        $anoletivo_id = new TDBCombo('anoletivo_id', 'sample', 'AnoLetivo', 'id', 'ano');
        $anoletivo_id->addValidation( 'Ano Letivo', new TRequiredValidator );

        $serie = new TDBCombo('serie', 'sample', 'Serie', 'id', 'nome');
        $serie->addValidation( 'Série', new TRequiredValidator );

        $turno = new TCombo('turno');
        $turno_array = [];
        $turno_array["matutino"] = "Matutino";
        $turno_array["vespertino"] = "Vespertino";
        $turno_array["noturno"] = "Noturno";
        $turno->addItems($turno_array);

        $row = $this->form->addFields( [ new TLabel('Aluno'), $aluno_id ]);
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Ano Letivo'), $anoletivo_id ]);
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Série'), $serie ]);
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Turno'), $turno ]);
        $row->layout = ['col-sm-12'];
         
        $btn = $this->form->addAction( 'Gerar PDF', new TAction([$this, 'onGerar']), 'fas:search');
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onShow($param)
    {
        //var_dump($param['id']);
    }
    
    public function onGerar($param)
    {   
        try
        {
            
            $this->form->validate();
            $data = $this->form->getData();
            $this->form->setData($data);
            
            $gerar = new RelFrequencia($param);

            $relatorio = $gerar->get_arquivo();
            if($relatorio)
            {
                parent::openFile($relatorio);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    
    }
}

