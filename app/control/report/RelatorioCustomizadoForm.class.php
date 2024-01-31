<?php
/**
 * RelatorioCustomizadoForm Form
 * @author  Fred Azv.
 */
class RelatorioCustomizadoForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_RelatorioCustomizado');
        $this->form->setFormTitle('Relatório Customizado');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $nome = new TEntry('nome');
        $conteudo = new THtmlEditor('conteudo');
        $conteudo->setSize( 100, 350 );

        $pc_receita_id = new TDBSeekButton('pc_receita_id', 'sample', $this->form->getName(), 'PcReceita', 'nome', 'pc_receita_id', 'pc_receita_nome');
        $pc_receita_nome = new TEntry('pc_receita_nome');
        $pc_receita_nome->setEditable(FALSE);


        $pc_despesa_id = new TDBSeekButton('pc_despesa_id', 'sample', $this->form->getName(), 'PcDespesa', 'nome', 'pc_despesa_id', 'pc_despesa_nome');
        $pc_despesa_nome = new TEntry('pc_despesa_nome');
        $pc_despesa_nome->setEditable(FALSE);

        $head = new TCombo('head');
        $items_head['S'] = 'Sim';
        $items_head['N'] = 'Não';
        $head->addItems($items_head);

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Nome do Relatório'), $nome ],
                                       [ new TLabel('Ativar Cabeçalho'), $head ]
                                    
        );
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-4','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Conteudo do Relatório'), $conteudo ]);
        $row->layout = ['col-sm-12'];

        $nome->addValidation('Nome do Relatório', new TRequiredValidator);  

        // $this->form->addContent( ['<h4>Referenciar a um Plano de Contas a Receber</h4><hr style="height:2px; border:none; color:#bcbcbc; background-color:#bcbcbc; margin-top: 0px; margin-bottom: 0px;">'] );

        // $row = $this->form->addFields( [ new TLabel('Plano de Contas'), $pc_receita_id ],
        //     [ new TLabel('Nome do Plano'), $pc_receita_nome ]);
        // $row->layout = ['col-sm-2','col-sm-10'];

        // $this->form->addContent( ['<h4>Referenciar a um Plano de Contas a Pagar</h4><hr style="height:2px; border:none; color:#bcbcbc; background-color:#bcbcbc; margin-top: 0px; margin-bottom: 0px;">'] );

        // $row = $this->form->addFields( [ new TLabel('Plano de Contas'), $pc_despesa_id ],
        //     [ new TLabel('Nome do Plano'), $pc_despesa_nome ]);
        // $row->layout = ['col-sm-2','col-sm-10'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
           
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar',  new TAction([$this, 'onExit']), 'fa:arrow-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'RelatorioCustomizadoList'));
        $container->add($this->form);

        /*$container->adianti_target_container = 'RelatorioCustomizadoList';
        $container->adianti_target_title = 'Relatorio Customizado';*/
        
        parent::add($container);
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
            
            $object = new RelatorioCustomizado;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
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
                $object = new RelatorioCustomizado($key); // instantiates the Active Record
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

    public function onExit()
    {
        $result = TSession::getValue('RelatorioCustomizadoList');

        $query = isset($result['query']) ? $result['query'] : null;

        if (!empty($query))
        {
            TScript::create("
                Adianti.waitMessage = 'Listando...';__adianti_post_data('RelatorioCustomizadoForm', '$query');                                 
        ");
        }
    }
}
