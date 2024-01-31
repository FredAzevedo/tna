<?php
/**
 * ApiIntegracaoForm Form
 * @author  Fred Azv.
 */
class ApiIntegracaoForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_ApiIntegracao');
        $this->form->setFormTitle('ApiIntegracao');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);
        $gateway = new TCombo('gateway');
        $combo_gateways = [];
        $combo_gateways['1'] = 'PJBank';
        $combo_gateways['2'] = 'PagSeguro';
        $combo_gateways['3'] = 'Iugu';
        $combo_gateways['4'] = 'Cloud-DFE';
        $gateway->addItems($combo_gateways);

        $split = new TCombo('split');
        $combo_splits = [];
        $combo_splits['S'] = 'Sim';
        $combo_splits['N'] = 'Não';
        $split->addItems($combo_splits);
        
        $combo_producao = new TCombo('producao');
        $combo_producao_itens = [];
        $combo_producao_itens['1'] = 'Sim';
        $combo_producao_itens['0'] = 'Não';
        $combo_producao->addItems($combo_producao_itens);

        $url = new TEntry('url');
        $chave = new TEntry('chave');
        $credencial = new TEntry('credencial');
        $tipo = new TCombo('tipo');
        $combo_tipos = [];
        $combo_tipos['1'] = 'Boleto';
        $combo_tipos['2'] = 'Cartão';
        $combo_tipos['3'] = 'SERASA';
        $combo_tipos['4'] = 'PJE';
        $tipo->addItems($combo_tipos);


        // add the fields
        $this->form->addFields( [ new TLabel('Id'), $id ] );
        $this->form->addFields( [ new TLabel('Unit Id'), $unit_id ] );
        $this->form->addFields( [ new TLabel('Gateway'), $gateway ] );
        $this->form->addFields( [ new TLabel('Split?'), $split ] );
        $this->form->addFields( [ new TLabel('Tipo'), $tipo ] );
        $this->form->addFields( [ new TLabel('Ambiente de Produção?'), $combo_producao ] );
        $this->form->addFields( [ new TLabel('URL'), $url ] );
        $this->form->addFields( [ new TLabel('Chave'), $chave ] );
        $this->form->addFields( [ new TLabel('Credencial'), $credencial ] );


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
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction( ['ApiIntegracaoList', 'onReload'] ), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        
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
            
            $object = new ApiIntegracao;  // create an empty object
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
                $object = new ApiIntegracao($key); // instantiates the Active Record
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
