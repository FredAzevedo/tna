<?php
/**
 * ReciboContaReceberFormBaixa Registration
 * @author  Fred Azevedo
 */
class ReciboContaReceberBaixaForm extends TPage
{
    protected $form;

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ReciboContaReceberFormBaixa');
        $this->form->setFormTitle('Recibo');
        $this->form->setFieldSizes('100%');

        $id = new TEntry('id');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));

        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userid'));
        $user_id->addValidation('Usuário', new TRequiredValidator);
        //$user_id->setEditable(FALSE);
        
        $cliente_id = new TDBCombo('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');
        $cliente_id->setEditable(FALSE);

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);

        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->addValidation('Data de Vencimento', new TRequiredValidator);
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');
        $data_vencimento->setEditable(FALSE);

        $data_conta = new TDate('data_conta');
        $data_conta->setValue(date('d/m/Y'));
        $data_conta->addValidation('Competência', new TRequiredValidator);
        $data_conta->setDatabaseMask('yyyy-mm-dd');
        $data_conta->setMask('dd/mm/yyyy');
        $data_conta->setEditable(FALSE);

        $documento = new TEntry('documento');
        $documento->setEditable(FALSE);

        $data_baixa = new TDate('data_baixa');
        $data_baixa->setValue(date('d/m/Y'));
        $data_baixa->addValidation('Data da Baixa', new TRequiredValidator);
        $data_baixa->setDatabaseMask('yyyy-mm-dd');
        $data_baixa->setMask('dd/mm/yyyy');

        $multa = new TNumeric('multa',2,',','.',true);
        $multa->setValue(date('0.00'));
        $multa->setEditable(FALSE);
        $juros = new TNumeric('juros',2,',','.',true);
        $juros->setEditable(FALSE);
        $juros->setValue(date('0.00'));
        $valor = new TNumeric('valor',2,',','.',true);
        $valor->addValidation('Valor', new TRequiredValidator);
        $valor->setEditable(FALSE);

        $valor_pago = new TNumeric('valor_pago',2,',','.',true);
        $valor_pago->addValidation('Valor Pago', new TRequiredValidator);
        $valor_pago->setEditable(FALSE);

        $desconto = new TNumeric('desconto',2,',','.',true);
        $desconto->setValue(date('0.00'));
        $desconto->setEditable(FALSE);
        $observacao = new TText('observacao');

        $row = $this->form->addFields( [ new TLabel('Cliente'), $cliente_id ],
                                       [ new TLabel('Usuário'), $user_id ]
        );
        $row->layout = ['col-sm-6','col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Conta Bancária'), $conta_bancaria_id ]);
        $row->layout = ['col-sm-2','col-sm-10'];


        $row = $this->form->addFields( [ new TLabel('Vencimento'), $data_vencimento ],
                                       [ new TLabel('Data da Conta'), $data_conta ],
                                       [ new TLabel('Data da Baixa'), $data_baixa ],
                                       [ new TLabel('Documento'), $documento ]);
        $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Multa'), $multa ],
                                       [ new TLabel('Juros'), $juros ],
                                       [ new TLabel('Desconto'), $desconto ],
                                       [ new TLabel('Valor da Conta'), $valor ],
                                       [ new TLabel('Valor Pago'), $valor_pago ]
        );
        $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
         

        $this->form->addAction('Atualizar Valor',  new TAction([$this, 'onAtualizar']), 'fa:eraser red');

        $btn2 = $this->form->addAction('Gerar Recibo',  new TAction([$this, 'onGerarRecibo']), 'fa:eraser red');
        $btn2->class = 'btn btn-sm btn-primary';

        $this->form->addAction('Voltar',  new TAction(['ReciboList', 'onReload']), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);

        $multa->onBlur = 'calc()';
        $juros->onBlur = 'calc()';
        $desconto->onBlur = 'calc()';
        
        TScript::create('calc = function() {
                
                let multa = convertToFloatNumber(form_ReciboContaReceberFormBaixa.multa.value);
                let juros = convertToFloatNumber(form_ReciboContaReceberFormBaixa.juros.value);
                let desconto = convertToFloatNumber(form_ReciboContaReceberFormBaixa.desconto.value);
                let valor = convertToFloatNumber(form_ReciboContaReceberFormBaixa.valor.value);

                valorTitulo = parseFloat(multa) + parseFloat(juros) - parseFloat(desconto);
                
                let total = valor + valorTitulo;

                total = formatMoney(total);        
                form_ReciboContaReceberFormBaixa.valor_pago.value = total;
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

    public function onGerarRecibo( $param )
    {
        $key = $param['id'];
        TTransaction::open('sample'); 
        $object = new ContaReceber($key);
        $object->recibo = 'S';
        $object->store();
        $this->form->setData($object); 
        //var_dump($this->form->getData());
        TTransaction::close();

        $pos_action = new TAction(['RelReciboReceita', 'onViewRecibo'], $param);
        new TMessage('info', 'Ação processada com sucesso!', $pos_action);

    }

    public function onAtualizar( $param )
    {   

        try
        {
            TTransaction::open('sample'); // open a transaction

            $key = $param['id'];  // get the parameter $key
            TTransaction::open('sample'); // open a transaction
            $object = new ContaReceber($key);

            if($object->data_vencimento < date('Y-m-d'))
            {
                $pegarBanco =  BoletoRegra::where('conta_bancaria_id', '=', $param['conta_bancaria_id'])->first();

                $multa = $pegarBanco->valor_multa / 100;
                $valorReplace = str_replace(',','.', $param['valor']);
                $valorMulta = $multa * $valorReplace;
                //var_dump($valorMulta);

                $valorJuros = $pegarBanco->valor_juros;
                $valorJurosDia = $valorJuros / 30;

                //diferença de dias em atraso
                $datavencimento = $object->data_vencimento;
                $datahoje = date('Y-m-d');

                $data1 = new DateTime($datavencimento); 
                $data2 = new DateTime($datahoje); 

                $diasAtrasados = $data1->diff ($data2); 
                //var_dump($diasAtrasados);
                $valorJurosAtual = $valorJurosDia * $diasAtrasados->days;

                $totalgeral = $valorMulta + $valorJurosAtual + $valorReplace;

                parent::add(new TAlert('danger', 'Esta atrasado '.$diasAtrasados->y.' ano(s) '.$diasAtrasados->m.' mês(s) e '.$diasAtrasados->d.' dia(s) totalizando hoje '.date('d/m/Y').', '.$diasAtrasados->days.' dia(s) de atraso e o valor de R$ '.number_format($totalgeral, 2, ',', '.')));

                $object->multa = $valorMulta;
                $object->juros = $valorJurosAtual;
                $object->valor_pago = $totalgeral;
                $object->recibo = 'S';
                $object->store();
            }else{

                new TMessage('info', 'A esse título não esta vencido!');
            }
            $this->form->setData($object); // fill the form

            TTransaction::close();  

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }

    }

    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample'); // open a transaction
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new ContaReceber;  // create an empty object
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

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('sample'); // open a transaction
                $object = new ContaReceber($key); // instantiates the Active Record
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
