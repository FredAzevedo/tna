<?php
/**
 * ContaReceberFormParcial Registration
 * @author  Fred Azevedo
 */
class ContaReceberFormParcial extends TWindow
{
    protected $form;
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('sample');
        $this->setActiveRecord('ContaReceber');
        parent::setTitle('Baixa Parcial de Título');
        parent::setSize(0.8, 0.4);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ContaReceberParcial');
        $this->form->setFormTitle('');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');
        
        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);

        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->addValidation('Data de Vencimento', new TRequiredValidator);
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');

        $valor = new TNumeric('valor',2,',','.',true);
        $valor->setEditable(FALSE);
        $valor_pago = new TNumeric('valor_pago',2,',','.',true);
        $valor_parcial = new TNumeric('valor_parcial',2,',','.',true);
        $valor_parcial->setEditable(FALSE);

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Conta Bancária'), $conta_bancaria_id ]);
        $row->layout = ['col-sm-2','col-sm-10'];

        $row = $this->form->addFields( [ new TLabel('Vencimento'), $data_vencimento ],
                                       [ new TLabel('Valor Atual'), $valor ],
                                       [ new TLabel('Valor Pago'), $valor_pago ],
                                       [ new TLabel('Valor Parcial'), $valor_parcial ]);
        $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-2'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onBaixarParcial']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        
        parent::add($container);

        $valor_pago->onBlur = 'calc()';
        
        TScript::create('calc = function() {
                
                let valor = convertToFloatNumber(form_ContaReceberParcial.valor.value);
                let valor_pago = convertToFloatNumber(form_ContaReceberParcial.valor_pago.value);
                
                let total = parseFloat(valor) - parseFloat(valor_pago);

                total = formatMoney(total);        
                form_ContaReceberParcial.valor_parcial.value = total;
            };    

            function formatMoney (number, decimal, separatord, separatort) {
                var n = number,
                    c = isNaN(decimal = Math.abs(decimal)) ? 2 : decimal,
                    d = separatord == undefined ? "," : separatord,
                    t = separatort == undefined ? "." : separatort,
                    s = n < 0 ? "-" : "",
                    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };

            function convertToFloatNumber(value) {
                value = value.toString();
                if (value.indexOf(\'.\') !== -1 || value.indexOf(\',\') !== -1) {
                    if (value.indexOf(\'.\') >  value.indexOf(\',\')) {
                        return parseFloat(value.replace(/,/gi,\'\'));
                    } else {
                        return parseFloat(value.replace(/\./gi,\'\').replace(/,/gi,\'.\'));
                    }
                } else {
                    return parseFloat(value);
                }
            };

        ');
    }

    /*public static function onBaixarParcial( $param )
    {
    
        $action = new TAction([__CLASS__, 'baixarParcial']);
        $action->setParameters( $param );
        
        new TQuestion('Tem certeza que deseja baixar esse título parcialmente?', $action);
    }*/

    public function onBaixarParcial( $param )
    {   

        $key =  $param['id'];

        $data = $this->form->getData();

        try
        {
            TTransaction::open('sample'); // open a transaction

            $object = new ContaReceber($key);
            $object->data_vencimento = $data->data_vencimento;
            $object->valor = $data->valor_parcial; 
            $object->store();

            $movBancaria = new MovimentacaoBancaria();
            $movBancaria->valor_movimentacao = $data->valor_pago;
            $movBancaria->data_lancamento = $object->data_conta;
            $movBancaria->data_vencimento = $object->data_vencimento;
            $movBancaria->data_baixa = date('Y-m-d');
            $movBancaria->status = 'Crédito';
            $movBancaria->historico = $object->descricao;
            $movBancaria->baixa = 'S';
            $movBancaria->tipo = 1;
            $movBancaria->documento = $object->documento;
            $movBancaria->unit_id = $object->unit_id;
            $movBancaria->cliente_id = $object->cliente_id;
            $movBancaria->pc_receita_id = $object->pc_receita_id;
            $movBancaria->pc_receita_nome = $object->pc_receita_nome;
            $movBancaria->conta_receber_id = $key;
            $movBancaria->conta_bancaria_id = $object->conta_bancaria_id;
            $movBancaria->store();

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
}
