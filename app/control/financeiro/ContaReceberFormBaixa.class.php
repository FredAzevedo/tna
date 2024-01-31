<?php

use Adianti\Control\TWindow;

/**
 * ContaReceberFormBaixa Registration
 * @author  Fred Azevedo
 */
class ContaReceberFormBaixa extends TWindow
{
    protected $form;
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('sample');
        $this->setActiveRecord('ContaReceber');
        parent::setTitle('Baixa a Receber de Título Individual');
        parent::setSize(0.7, 0.5);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ContaReceberFormBaixa');
        $this->form->setFormTitle('');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new TEntry('id');

        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userid'));
        
        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);

        $tipo_pgto_id = new TDBCombo('tipo_pgto_id', 'sample', 'TipoPgto', 'id', 'nome');
        $tipo_pgto_id->addValidation('Tipo de Pagamento', new TRequiredValidator);

        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->addValidation('Data de Vencimento', new TRequiredValidator);
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');

        $data_conta = new TDate('data_conta');
        $data_conta->setValue(date('d/m/Y'));
        $data_conta->addValidation('Competência', new TRequiredValidator);
        $data_conta->setDatabaseMask('yyyy-mm-dd');
        $data_conta->setMask('dd/mm/yyyy');

        $documento = new TEntry('documento');

        $data_baixa = new TDate('data_baixa');
        $data_baixa->setValue(date('d/m/Y'));
        $data_baixa->addValidation('Data de Vencimento', new TRequiredValidator);
        $data_baixa->setDatabaseMask('yyyy-mm-dd');
        $data_baixa->setMask('dd/mm/yyyy');

        $multa = new TNumeric('multa',2,',','.',true);
        $multa->setValue(date('0.00'));
        $juros = new TNumeric('juros',2,',','.',true);
        $taxas = new TNumeric('taxas',2,',','.',true);
        $taxas->setValue(date('0.00'));
        $juros->setValue(date('0.00'));
        $valor = new TNumeric('valor',2,',','.',true);
        $valor->addValidation('Valor', new TRequiredValidator);

        $valor_pago = new TNumeric('valor_pago',2,',','.',true);
        $valor_pago->addValidation('Valor Pago', new TRequiredValidator);

        $desconto = new TNumeric('desconto',2,',','.',true);
        $desconto->setValue(date('0.00'));
        $observacao = new TText('observacao');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Conta Bancária'), $conta_bancaria_id ],
                                       [ new TLabel('Tipo de Pagamento'), $tipo_pgto_id ]);
        $row->layout = ['col-sm-2','col-sm-5','col-sm-5'];


        $row = $this->form->addFields( [ new TLabel('Vencimento'), $data_vencimento ],
                                       [ new TLabel('Data da Conta'), $data_conta ],
                                       [ new TLabel('Data da Baixa'), $data_baixa ],
                                       [ new TLabel('Documento'), $documento ]);
        $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Multa'), $multa ],
                                       [ new TLabel('Juros'), $juros ],
                                       [ new TLabel('Taxas'), $taxas ],
                                       [ new TLabel('Desconto'), $desconto ],
                                       [ new TLabel('Valor da Conta'), $valor ],
                                       [ new TLabel('Valor Pago'), $valor_pago ]
        );
        $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2','col-sm-2'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

        $this->form->addAction('Atualizar Valor',  new TAction([$this, 'onAtualizar']), 'fa:eraser red');
         
        // create the form actions
        $btn = $this->form->addAction('Baixar', new TAction([$this, 'onBaixar']), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        
        parent::add($container);

        $multa->onBlur = 'calc()';
        $juros->onBlur = 'calc()';
        $taxas->onBlur = 'calc()';
        $desconto->onBlur = 'calc()';
        
        TScript::create('calc = function() {
                
                let multa = convertToFloatNumber(form_ContaReceberFormBaixa.multa.value);
                let juros = convertToFloatNumber(form_ContaReceberFormBaixa.juros.value);
                let taxas = convertToFloatNumber(form_ContaReceberFormBaixa.taxas.value);
                let desconto = convertToFloatNumber(form_ContaReceberFormBaixa.desconto.value);
                let valor = convertToFloatNumber(form_ContaReceberFormBaixa.valor.value);

                valorTitulo = parseFloat(multa) + parseFloat(juros) - parseFloat(desconto) - parseFloat(taxas);
                
                let total = valor + valorTitulo;

                total = formatMoney(total);        
                form_ContaReceberFormBaixa.valor_pago.value = total;
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

    public function onBaixar($param)
    {   

        $key =  $param['id'];
        $taxas = $param['taxas'];

        $data = $this->form->getData();

        try
        {

            TTransaction::open('sample'); // open a transaction
            $object = new ContaReceber($key);
            $object->data_vencimento = $data->data_vencimento;
            $object->data_conta = $data->data_conta;
            $object->data_baixa = $data->data_baixa;
            $object->documento = $data->documento;
            $object->conta_bancaria_id = $data->conta_bancaria_id;
            $object->multa = $data->multa;
            $object->juros = $data->juros;
            $object->taxas = $data->taxas;
            $object->desconto = $data->desconto;
            $object->valor = $data->valor;
            $object->valor_pago = $data->valor_pago;
            $object->baixa = 'S';
            $object->store();
            TTransaction::close();
            
            
            if($object->taxas != 0.00){

                //TTransaction::setLogger(new TLoggerSTD);
                TTransaction::open('sample');
                $taxa = new ContaPagar;  
                $taxa->data_lancamento = date('Y-m-d');
                $taxa->data_vencimento = date('Y-m-d');
                $taxa->data_conta = date('Y-m-d');
                $taxa->documento = "Taxas referente ao Contas a Receber Nº: ".$data->id;
                $taxa->unit_id = $object->unit_id;
                $taxa->conta_bancaria_id = 0;
                $taxa->tipo_pgto_id = 0;
                $taxa->fornecedor_id = 0;
                $taxa->valor = $data->taxas;
                $taxa->valor_pago = $data->taxas;
                $taxa->baixa = 'N';
                $taxa->store();
                TTransaction::close();
                
            }

            TTransaction::open('sample');
            $movBancaria = new MovimentacaoBancaria();
            $movBancaria->valor_movimentacao = $data->valor_pago;
            $movBancaria->data_lancamento = $object->data_conta;
            $movBancaria->data_vencimento = $object->data_vencimento;
            $movBancaria->data_baixa = $data->data_baixa;
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
            $movBancaria->departamento_id = $object->departamento_id;
            $movBancaria->centro_custo_id = $object->centro_custo_id;
            $movBancaria->store();
            TTransaction::close(); // close the transaction

            // if($object->fornecedor_id){
            //     ComissionamentoQuant::gerarComissao($object->cliente_id, $object->id,$object->fornecedor_id);
            // }
            
            $pos_action = new TAction(['ContaReceberList', 'onReload']);
            new TMessage('info', 'Baixa realizada com sucesso', $pos_action);

            self::closeWindow();
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }

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

                parent::add(new TAlert('danger',$diasAtrasados->days.' dia(s) de atraso e o valor de R$ '.number_format($totalgeral, 2, ',', '.')));

                $object->multa = $valorMulta;
                $object->juros = $valorJurosAtual;
                $object->valor_pago = $totalgeral;
                $object->data_cobranca = date('Y-m-d');
                $object->store();

                //$pos_action = new TAction(['GestaoCobrancaList', 'onReload']);
                new TMessage('info', 'Atualização de valor realizada com Sucesso!');

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
}
