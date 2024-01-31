<?php
/**
 * PlanoForm Form
 * @author  Fred Azv
 */
class PlanoForm extends TPage
{
    protected $form; // form

    private $fieldlist_unidades;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Plano');
        $this->form->setFormTitle('Novo Plano');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $valor = new TNumeric('valor', 2,',','.',true);

        $pc_receita_id = new TDBSeekButton('pc_receita_id', 'sample', $this->form->getName(), 'PcReceita', 'nome', 'pc_receita_id', 'pc_receita_nome');
        $pc_receita_id->addValidation('Plano de Contas', new TRequiredValidator);
        $pc_receita_nome = new TEntry('pc_receita_nome');
        $pc_receita_nome->setEditable(FALSE);

        $hash_plano = new TEntry('hash_plano');

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);


        $this->form->addContent( ['<h4><b>Dados do Plano</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                        [ new TLabel('Nome do Plano'), $nome ],
                                        [ new TLabel('Preço'), $valor ]
        );
        $row->layout = ['col-sm-1','col-sm-9','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Plano de Contas'), $pc_receita_id ],
                                       [ new TLabel('Descrição do Plano de Contas'), $pc_receita_nome ],
                                       [ new TLabel('Hash do Plano Cadastrado no PagSeguro'), $hash_plano ]);
        $row->layout = ['col-sm-2','col-sm-6','col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Conta Bancária Padrão'), $conta_bancaria_id ]);
        $row->layout = ['col-sm-12'];
        
        if (!empty($id))
        $id->setEditable(FALSE);
        
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction(['PlanoList','onReload']), 'fa:angle-double-left');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'PlanoList'));
        $container->add($this->form);


        
        parent::add($container);
    }

    public static function deletePlano($param)
    {
        $planos = TSession::getValue('plano_list');
        unset($planos[ $param['id'] ]);
        TSession::setValue('plano_list', $planos);
    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample'); // open a transaction
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new Plano;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            $plano_id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            $this->onEdit(array('key'=> $plano_id)); 

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
                $plano = new Plano($key); // instantiates the Active Record
                $this->form->setData($plano); // fill the form

                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);

                $this->fieldlist_unidades->addHeader();
                $this->fieldlist_unidades->addDetail( new stdClass );
                $this->fieldlist_unidades->addCloneAction();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onReload($param)
    {
        $this->loadTFieldListData($param);
        $this->loaded = TRUE;
    }
}
