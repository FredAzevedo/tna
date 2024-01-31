<?php
/**
 * MovimentacaoBancariaForm Form
 * @author  Fred Azv.
 */
class MovimentacaoBancariaForm extends TPage
{
    protected $form; // form

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_MovimentacaoBancaria');
        $this->form->setFormTitle('Movimentação Bancaria (Manutenção)');
        $this->form->setFieldSizes('100%');
        
        // create the form fields
        $id = new TEntry('id');
        $valor_movimentacao = new TNumeric('valor_movimentacao', 2, ',', '.', true);

        $data_lancamento = new TDate('data_lancamento');
        $data_lancamento->setValue(date('d/m/Y'));
        $data_lancamento->setDatabaseMask('yyyy-mm-dd');
        $data_lancamento->setMask('dd/mm/yyyy');
        $data_lancamento->addValidation('Valor', new TRequiredValidator);

        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->setValue(date('d/m/Y'));
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');
        $data_vencimento->addValidation('Valor', new TRequiredValidator);

        $data_baixa = new TDate('data_baixa');
        $data_baixa = new TDate('data_baixa');
        $data_baixa->setValue(date('d/m/Y'));
        $data_baixa->setDatabaseMask('yyyy-mm-dd');
        $data_baixa->setMask('dd/mm/yyyy');
        $data_baixa->addValidation('Valor', new TRequiredValidator);
        
        $historico = new TEntry('historico');
        $documento = new TEntry('documento');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $tipo = new TCombo('tipo');
        $tipo->setChangeAction(new TAction(array($this, 'onChangeTipo')));
        $combo_tipo = array();
        $combo_tipo['0'] = 'Despesa';
        $combo_tipo['1'] = 'Receita';
        $tipo->addItems($combo_tipo);

        

        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
    
        $pc_receita_id = new TDBSeekButton('pc_receita_id', 'sample', $this->form->getName(), 'PcReceita', 'nome', 'pc_receita_id', 'pc_receita_nome');
        $pc_receita_nome = new TEntry('pc_receita_nome');
        $pc_receita_nome->setEditable(FALSE);

        $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'nome_fantasia');

        $pc_despesa_id = new TDBSeekButton('pc_despesa_id', 'sample', $this->form->getName(), 'PcDespesa', 'nome', 'pc_despesa_id', 'pc_despesa_nome');
        $pc_despesa_nome = new TEntry('pc_despesa_nome');
        $pc_despesa_nome->setEditable(FALSE);

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);

        $departamento_id = new TDBCombo('departamento_id', 'sample', 'Departamento', 'id', 'nome');
        $departamento_id->addValidation('Departamento', new TRequiredValidator);

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Valor'), $valor_movimentacao ],
                                       [ new TLabel('Vencimento'), $data_vencimento ],
                                       [ new TLabel('Data da Baixa'), $data_baixa ],
                                       [ new TLabel('Competência'), $data_lancamento ],
                                       [ new TLabel('Unidade'), $unit_id ]);
        $row->layout = ['col-sm-2','col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Histórico'), $historico ],
                                       [ new TLabel('Documento'), $documento ],
                                       [ new TLabel('Tipo'), $tipo ]);
        $row->layout = ['col-sm-6','col-sm-4','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Departamento'), $departamento_id ]
        );
        $row->layout = ['col-sm-4','col-sm-4','col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Cliente'), $cliente_id ],
                                       [ new TLabel('Plano de Contas'), $pc_receita_id ],
                                       [ new TLabel('Descrição'), $pc_receita_nome ]);
        $row->layout = ['col-sm-5','col-sm-2','col-sm-5'];

        $row = $this->form->addFields( [ new TLabel('Fornecedor'), $fornecedor_id ],
                                       [ new TLabel('Plano de Contas'), $pc_despesa_id ],
                                       [ new TLabel('Descrição'), $pc_despesa_nome ]);
        $row->layout = ['col-sm-5','col-sm-2','col-sm-5'];

        $row = $this->form->addFields( [ new TLabel('Conta Bancaria'), $conta_bancaria_id ]);
        $row->layout = ['col-sm-12'];


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'MovimentacaoBancariaList'));
        $container->add($this->form);
        
        parent::add($container);
    }

    public function onReload($param)
    {
        $this->onChangeTipo($param);
        $this->loaded = TRUE;
    }

    public static function onChangeTipo($param)
    {
        if ($param['tipo'] == '0')
        {
            TQuickForm::showField('form_MovimentacaoBancaria', 'fornecedor_id');
            TQuickForm::showField('form_MovimentacaoBancaria', 'pc_despesa_id');
            TQuickForm::showField('form_MovimentacaoBancaria', 'pc_despesa_nome');

            TQuickForm::hideField('form_MovimentacaoBancaria', 'cliente_id');
            TQuickForm::hideField('form_MovimentacaoBancaria', 'pc_receita_id');
            TQuickForm::hideField('form_MovimentacaoBancaria', 'pc_receita_nome');
        }
        else
        {
            TQuickForm::hideField('form_MovimentacaoBancaria', 'fornecedor_id');
            TQuickForm::hideField('form_MovimentacaoBancaria', 'pc_despesa_id');
            TQuickForm::hideField('form_MovimentacaoBancaria', 'pc_despesa_nome');

            TQuickForm::showField('form_MovimentacaoBancaria', 'cliente_id');
            TQuickForm::showField('form_MovimentacaoBancaria', 'pc_receita_id');
            TQuickForm::showField('form_MovimentacaoBancaria', 'pc_receita_nome');
        }
    }

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
            
            $object = new MovimentacaoBancaria;  // create an empty object
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
    
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
        $this->onReload( $param );
    }
    
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('sample'); // open a transaction
                $object = new MovimentacaoBancaria($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                
                if($object->conta_pagar_id){
                    
                    $update_cp = new ContaPagar($object->conta_pagar_id);
                    $update_cp->conta_bancaria_id = $object->conta_bancaria_id;
                    $update_cp->pc_despesa_id = $object->pc_despesa_id;
                    $update_cp->pc_despesa_nome = $object->pc_despesa_nome;
                    $update_cp->fornecedor_id = $object->fornecedor_id;
                    $update_cp->data_vencimento = $object->data_vencimento;
                    $update_cp->data_baixa = $object->data_baixa;
                    $update_cp->valor_pago = $object->valor_movimentacao;
                    $update_cp->valor = $object->valor_movimentacao;
                    $update_cp->store();
    
                }

                if($object->conta_receber_id){
                    
                    $update_cr = new ContaReceber($object->conta_receber_id);
                    $update_cr->conta_bancaria_id = $object->conta_bancaria_id;
                    $update_cr->pc_receita_id = $object->pc_receita_id;
                    $update_cr->pc_receita_nome = $object->pc_receita_nome;
                    $update_cr->cliente_id = $object->cliente_id;
                    $update_cr->data_vencimento = $object->data_vencimento;
                    $update_cr->data_baixa = $object->data_baixa;
                    $update_cr->valor_pago = $object->valor_movimentacao;
                    $update_cr->valor = $object->valor_movimentacao;
                    $update_cr->store();
    
                }

                TTransaction::close(); // close the transaction
                self::onChangeTipo(['tipo' => $object->tipo]);
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
