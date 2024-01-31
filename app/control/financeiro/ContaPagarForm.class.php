<?php
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Wrapper\TDBCombo;
/**
 * ContaPagarForm Form
 * @author  Fred Az.
 */
class ContaPagarForm extends TPage
{
    protected $form; // form
    
    use Adianti\Base\AdiantiFileSaveTrait;

    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_ContaPagar');
        $this->form->setFormTitle('Contas a Pagar');
        $this->form->setFieldSizes('100%');
        
        // create the form fields
        $id = new TEntry('id');

        if (!empty($_GET['id']))
        {
            $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
            $user_id->setEditable(FALSE);
            $user_id->addValidation('Usuário de Criação', new TRequiredValidator);

            $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade');
            $unit_id->setEditable(FALSE);
            $unit_id->addValidation('Unidade de Criação', new TRequiredValidator);

        } else {

            $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
            $user_id->setValue(TSession::getValue('userid'));
            $user_id->setEditable(FALSE);
            $user_id->addValidation('Unidade de Criação', new TRequiredValidator);

            $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade');
            $unit_id->setValue(TSession::getValue('userunitid'));
            $unit_id->setEditable(FALSE);
            $unit_id->addValidation('Unidade de Criação', new TRequiredValidator);
        }

        $data_conta = new TDate('data_conta');
        $data_conta->setValue(date('d/m/Y'));
        $data_conta->addValidation('Competência', new TRequiredValidator);
        $data_conta->setDatabaseMask('yyyy-mm-dd');
        $data_conta->setMask('dd/mm/yyyy');

        $descricao = new TText('descricao');
        $descricao->addValidation('Descrição', new TRequiredValidator);
        $documento = new TEntry('documento');
        $data_vencimento = new TDate('data_vencimento');
        $data_vencimento->addValidation('Data de Vencimento', new TRequiredValidator);
        $data_vencimento->setDatabaseMask('yyyy-mm-dd');
        $data_vencimento->setMask('dd/mm/yyyy');
        $multa = new TNumeric('multa',2,',','.',true);
        $juros = new TNumeric('juros',2,',','.',true);
        $valor = new TNumeric('valor',2,',','.',true);
        $valor->addValidation('Valor', new TRequiredValidator);
        $desconto = new TNumeric('desconto',2,',','.',true);
        $observacao = new TText('observacao');

        /*$juros->onChange = 'calcularTotal()';
        $multa->onChange = 'calcularTotal()';
        $desconto->onChange = 'calcularTotal()';

        TScript::create('calcularTotal = function() {
                
                let valor = form_ContaPagar.valor.value;
                let juros = form_ContaPagar.juros.value;
                let multa = form_ContaPagar.multa.value;
                let desconto = form_ContaPagar.desconto.value;

                let total = parseFloat(valor) + parseFloat(juros) + parseFloat(multa) - parseFloat(desconto);

                total = formatMoney(total);        
                form_ContaPagar.valor.value = total;
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

        ');*/

        $replica = new TCombo('replica');
        $replica->addValidation('Replica ?', new TRequiredValidator);
        $combo_replica = array();
        $combo_replica['S'] = 'Sim - Divide o valor pelo nº de parcelas.';
        $combo_replica['N'] = 'Não - Mantem o valor pelo nº de parcelas.';
        //$combo_replica['I'] = 'Sim, com intervalo';
        $replica->addItems($combo_replica);

        $parcelas = new TEntry('parcelas');
        $intervalo = new TEntry('intervalo');
        $responsavel = new TEntry('responsavel');
        $tipo_pgto_id = new TDBCombo('tipo_pgto_id', 'sample', 'TipoPgto', 'id', 'nome');
        $tipo_pgto_id->addValidation('Tipo de Pagamento', new TRequiredValidator);

        $tipo_forma_pgto_id = new TDBCombo('tipo_forma_pgto_id','sample','TipoFormaPgto','id','nome');
        $tipo_forma_pgto_id->addValidation('Forma de Pagamento', new TRequiredValidator);

        $fornecedor_id = new TDBUniqueSearch('fornecedor_id', 'sample', 'Fornecedor', 'id', 'nome_fantasia');
        $fornecedor_id->setMask('{razao_social} - {cpf_cnpj} - {nome_fantasia}');
        $fornecedor_id->addValidation('Fornecedor', new TRequiredValidator);
        
        //$pc_despesa_id = new TDBUniqueSearch('pc_despesa_id', 'sample', 'PcDespesa', 'id', 'nivel1');
        $pc_despesa_id = new TDBSeekButton('pc_despesa_id', 'sample', $this->form->getName(), 'PcDespesa', 'nome', 'pc_despesa_id', 'pc_despesa_nome');
        $pc_despesa_id->addValidation('Plano de Contas', new TRequiredValidator);
        $pc_despesa_nome = new TEntry('pc_despesa_nome');
        $pc_despesa_nome->setEditable(FALSE);

        $departamento_id = new TDBCombo('departamento_id', 'sample', 'Departamento', 'id', 'nome');
        $departamento_id->addValidation('Departamento', new TRequiredValidator);

        $id_unit_session_conta_bancaria = new TCriteria();
        $id_unit_session_conta_bancaria->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $conta_bancaria_id = new TDBCombo('conta_bancaria_id', 'sample', 'ContaBancaria', 'id', '{banco->nome_banco} - AG: {agencia} - CC: {conta}','',$id_unit_session_conta_bancaria);
        $conta_bancaria_id->addValidation('Conta Bancária', new TRequiredValidator);

        $centro_custo_id = new TDBCombo('centro_custo_id','sample','CentroCusto','id','nome','nome');

        $cliente_contrato_id = new TEntry('cliente_contrato_id');
        
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Competência'), $data_conta ],
                                       [ new TLabel('Vencimento'), $data_vencimento ],
                                       [ new TLabel('Usuário'), $user_id ]);
        $row->layout = ['col-sm-2','col-sm-3', 'col-sm-2', 'col-sm-2','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Tipo de Pagamento'), $tipo_pgto_id ],
                                       [ new TLabel('Forma de Pagamento'), $tipo_forma_pgto_id ],
                                       [ new TLabel('Nº Documento/REF'), $documento ],
                                       [ new TLabel('Responsável'), $responsavel ]);
        $row->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $row = $this->form->addFields( [ new TLabel('Plano de Contas'), $pc_despesa_id ],
                                       [ new TLabel('Nome do Plano'), $pc_despesa_nome ],
                                       [ new TLabel('Fornecedor'), $fornecedor_id ]);
        $row->layout = ['col-sm-2','col-sm-5','col-sm-5'];
        
        $row = $this->form->addFields( [ new TLabel('Conta bancária para a baixa'), $conta_bancaria_id ],
                                       [ new TLabel('Departamento'), $departamento_id ]);
        $row->layout = ['col-sm-8','col-sm-4'];
        
        $row = $this->form->addFields(  [ new TLabel('Nº Contrato'), $cliente_contrato_id ],
                                        [  ],
                                        [ new TLabel('Centro de Custos'), $centro_custo_id ]
        );
        $row->layout = ['col-sm-2','col-sm-6','col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Descrição do título'), $descricao ]);
        $row->layout = ['col-sm-12'];

        
        $this->form->addContent( ['<h4>Valor</h4><hr style="height:2px; border:none; color:#bcbcbc; background-color:#bcbcbc; margin-top: 0px; margin-bottom: 0px;">'] );

        $row = $this->form->addFields( [ new TLabel('Valor'), $valor ],
                                       [ new TLabel('Divide valor?'), $replica]
        );
        $row->layout = ['col-sm-2','col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Observação'), $observacao ]);
        $row->layout = ['col-sm-6'];

        $arquivo = new TMultiFile('arquivo');
        $arquivo->setAllowedExtensions( ['xls', 'png', 'jpg', 'jpeg','pdf'] );
        $arquivo->enableFileHandling();
        //$arquivo->enableImageGallery();
        $arquivo->enablePopover('Preview', '<img style="max-width:300px" src="download.php?file={file_name}">');
        
        $row = $this->form->addFields( [ new TLabel('<b>Anexar um ou mais arquivos</b><p style="color:red;">Obs: extenções aceitas: Xls, Pdf, Jpg e Jpeg.</p>'), $arquivo ]);
        $row->layout = ['col-sm-6'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

        if (isset($param['id']) && $param['id']) {
            try {
                $key = $param['id'];
                TTransaction::open('sample');

                $cp = new ContaPagar($key);

                if(!empty($cp->baixa == 'S')){

                    $data_conta->setEditable(false);
                    $data_vencimento->setEditable(false);
                    $tipo_forma_pgto_id->setEditable(false);
                    $tipo_pgto_id->setEditable(false);
                    $user_id->setEditable(false);
                    $conta_bancaria_id->setEditable(false);
                    $fornecedor_id->setEditable(false);
                    $pc_despesa_id->setEditable(false);
                    $valor->setEditable(false);
                    $replica->setEditable(false);
                }

                TTransaction::close();
            } catch (Exception $exception) {
                TTransaction::rollback();
            }
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction( [$this, 'onExit'] ), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ContaPagarList'));
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
            $valorForm = number_format($data->valor, 2, '.', '');


             if (!$data->id) 
            {

                $forma = new TipoFormaPgto($data->tipo_forma_pgto_id);
                $formaPagamento = new FormaPagamento($valorForm,$forma->regra,$data->data_vencimento);

                if($forma->parcela > 1){

                    $count = 1;
                    for($i = 0; $i < $formaPagamento->numero_parcelas; ++$i) 
                    {

                        //var_dump($formaPagamento->valor_parcela);
                        $object = new ContaPagar();
                        $object->data_vencimento = $formaPagamento->vencimentobd[$i]; 

                        if($data->replica == "S"){
                           $object->valor = $formaPagamento->valor_parcela;
                           $object->valor_real = $formaPagamento->valor_parcela; 
                           $object->valor_pago = $formaPagamento->valor_parcela; 
                        }else{
                           $object->valor = $valorForm;
                           $object->valor_real = $valorForm; 
                           $object->valor_pago = $valorForm;
                        }

                        $object->baixa = 'N'; 
                        $object->replica = 'N'; 
                        $object->parcelas = $count++;
                        $object->nparcelas = $formaPagamento->numero_parcelas;
                        $object->data_conta = $data->data_conta;
                        $object->descricao = $data->descricao;
                        $object->documento = $data->documento;
                        $object->multa = $data->multa || 0;
                        $object->juros = $data->juros || 0;
                        $object->desconto = $data->desconto || 0;
                        $object->observacao = $data->observacao;
                        $object->responsavel = $data->responsavel;
                        $object->unit_id = $data->unit_id;
                        $object->user_id = $data->user_id;
                        $object->tipo_pgto_id = $data->tipo_pgto_id;
                        $object->tipo_forma_pgto_id = $data->tipo_forma_pgto_id;
                        $object->fornecedor_id = $data->fornecedor_id;
                        $object->pc_despesa_id = $data->pc_despesa_id;
                        $object->pc_despesa_nome = $data->pc_despesa_nome;
                        $object->departamento_id = $data->departamento_id;
                        $object->conta_bancaria_id = $data->conta_bancaria_id;
                        $object->store();


                        $this->saveFiles($object, $data, 'arquivo', 'files/documents', 'ContaPagarArquivo', 'arquivo', 'conta_pagar_id'); 
                    }
                }else{

                    $object = new ContaPagar;  // create an empty object
                    $object->fromArray( (array) $data); // load the object with data
                    
                    $this->saveFiles($object, $data, 'arquivo', 'files/documents', 'ContaPagarArquivo', 'arquivo', 'conta_pagar_id');

                    $object->valor_pago = $valorForm;
                    $object->valor_real = $valorForm;
                    $object->store(); // save the object
                }

            }else{

                $object = new ContaPagar;  // create an empty object
                $object->fromArray( (array) $data); // load the object with data

                $this->saveFiles($object, $data, 'arquivo', 'files/documents', 'ContaPagarArquivo', 'arquivo', 'conta_pagar_id');
            
                $object->valor_pago = $valorForm;
                $object->valor_real = $valorForm;
                $object->store(); // save the object

            }
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([$this, 'onEdit'],['key' => $object->id]);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $pos_action);
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
                $object = new ContaPagar($key); // instantiates the Active Record
                $object->arquivo = ContaPagarArquivo::where('conta_pagar_id', '=', $param['key'])->getIndexedArray('id', 'arquivo');
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
        $result = TSession::getValue('ContaPagarList');

        $query = isset($result['query']) ? $result['query'] : null;

        if (!empty($query))
        {
            TScript::create("
                Adianti.waitMessage = 'Listando...';__adianti_post_data('ContaPagarForm', '$query');                                 
        ");
        }
    }
}
