<?php

use Carbon\Carbon;
/**
 * AlunoContratoForm Master/Detail
 * @author  Fred Azv
 */
class AlunoContratoForm extends TPage
{
    protected $form; // form
    protected $fieldlist;

    function __construct($param)
    {
        parent::__construct($param);
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_AlunoContrato');
        $this->form->setFormTitle('Contrato');
        $this->form->setFieldSizes('100%');
        
        // master fields
        $id = new TEntry('id');
        $primeiro_responsavel_id = new TDBCombo('primeiro_responsavel_id','sample','Responsavel','id','nome','nome');
        $primeiro_responsavel_id->enableSearch(10);
        $segundo_responsavel_id = new TDBCombo('segundo_responsavel_id','sample','Responsavel','id','nome','nome');
        $segundo_responsavel_id->enableSearch(10);
        $ano_letivo = new TDBCombo('ano_letivo', 'sample', 'AnoLetivo', 'ano', 'ano','ano desc');
        $prazo_meses = new TEntry('prazo_meses');
        $prazo_inicio = new TDate('prazo_inicio');
        $prazo_inicio->setDatabaseMask('yyyy-mm-dd');
        $prazo_inicio->setMask('dd/mm/yyyy');
        $prazo_fim = new TDate('prazo_fim');
        $prazo_fim->setDatabaseMask('yyyy-mm-dd');
        $prazo_fim->setMask('dd/mm/yyyy');
        $preco_valor_integral = new TNumeric('preco_valor_integral',2,',','.',true);
        $preco_parcelas = new TDBCombo('preco_parcelas','sample','TipoFormaPgto','id','nome','nome');
        $preco_parcela_valor = new TNumeric('preco_parcela_valor',2,',','.',true);
        $preco_parcela_valor->setEditable(FALSE);
        $preco_desconto = new TNumeric('preco_desconto',2,',','.',true);
        $preco_parcela_valor_desconto = new TNumeric('preco_parcela_valor_desconto',2,',','.',true);
        $preco_parcela_valor_desconto->setEditable(FALSE);
        $preco_valor_total = new TNumeric('preco_valor_total',2,',','.',true);
        $preco_valor_total->setEditable(FALSE);
        $vencimento_parcela = new TDate('vencimento_parcela');
        $vencimento_parcela->setDatabaseMask('yyyy-mm-dd');
        $vencimento_parcela->setMask('dd/mm/yyyy');

        $tipo_pgto_id = new TDBCombo('tipo_pgto_id','sample','TipoPgto','id','nome','nome');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

        $this->form->addFields( [new TFormSeparator('<hr><b>Dados para o contrato</b>') ] );

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Responsável Principal'), $primeiro_responsavel_id ],
                                       [ new TLabel('Segundo Responsável'), $segundo_responsavel_id ]
                                    );
        $row->layout = ['col-sm-2','col-sm-5','col-sm-5'];

        $row = $this->form->addFields( [ new TLabel('Ano Letivo'), $ano_letivo ],
                                       [ ],
                                       [ new TLabel('Prazo Meses'), $prazo_meses ],
                                       [ new TLabel('Prazo Início'), $prazo_inicio ],
                                       [ new TLabel('Prazo Fim'), $prazo_fim ],
                                       [ new TLabel('Valor Integral'), $preco_valor_integral ]
                                    );
        $row->layout = ['col-sm-2','col-sm-2','col-sm-2','col-sm-2','col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Tipo de Pagamento'), $tipo_pgto_id ]
                                    );
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Parcelas'), $preco_parcelas ],
                                       [ new TLabel('Valor da Parcela'), $preco_parcela_valor ],
                                       [ new TLabel('Desconto Total'), $preco_desconto ],
                                       [ new TLabel('Parcela com Desconto'), $preco_parcela_valor_desconto ],
                                       [ new TLabel('Total com Descontos'), $preco_valor_total ],
                                       [ new TLabel('Primeiro Vencimento'), $vencimento_parcela ]
                                    );
        $row->layout = ['col-sm-2','col-sm-2','col-sm-2','col-sm-2','col-sm-2','col-sm-2'];
        
        
        // detail fields
        $this->fieldlist = new TFieldList;
        $this->fieldlist-> width = '100%';
        $this->fieldlist->enableSorting();

        $aluno_id = new TDBUniqueSearch('list_aluno_id[]', 'sample', 'Aluno', 'id', 'nome','nome');

        $aluno_id->setSize('100%');

        $this->fieldlist->addField( '<b>Aluno</b>', $aluno_id, ['width' => '100%']);

        $this->form->addField($aluno_id);
        
        $this->form->addFields( [new TFormSeparator('<hr><b>Benificiário (Aluno)</b>') ] );
        $this->form->addFields( [$this->fieldlist] );
        
        // create actions
        $this->form->addAction( _t('Save'),  new TAction( [$this, 'onSave'] ),  'fa:save green' );
        $this->form->addAction( 'Voltar', new TAction( [$this, 'onExit'] ), 'fa:arrow-left' );
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);


        $preco_parcelas->onChange = 'calc()';
        $preco_desconto->onBlur = 'calc()';
                                    
        TScript::create('calc = function() {
                
                let preco_valor_integral = convertToFloatNumber(form_AlunoContrato.preco_valor_integral.value);
                let preco_parcelas = form_AlunoContrato.preco_parcelas.value;
                let desconto = convertToFloatNumber(form_AlunoContrato.preco_desconto.value);

                if(!desconto && desconto !== 0){
                    desconto = 0;
                }
                
                let precoParcelaValorSemDesconto = parseFloat(preco_valor_integral) / preco_parcelas;
                let valor_liq = parseFloat(preco_valor_integral) - parseFloat(desconto);
                let precoParcelaValor = valor_liq / preco_parcelas;
                let total = precoParcelaValor * preco_parcelas;     

                form_AlunoContrato.preco_parcela_valor.value = formatMoney(precoParcelaValorSemDesconto);
                form_AlunoContrato.preco_parcela_valor_desconto.value = formatMoney(precoParcelaValor);
                form_AlunoContrato.preco_valor_total.value = formatMoney(total);
             
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
    
    /**
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            TTransaction::open('sample');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new AlunoContrato($key);
                $this->form->setData($object);
                
                $items  = AlunoContratoBeneficiario::where('aluno_contrato_id', '=', $key)->load();
                
                if ($items)
                {
                    $this->fieldlist->addHeader();
                    foreach($items  as $item )
                    {
                        $detail = new stdClass;
                        $detail->list_aluno_id = $item->aluno_id;
                        $this->fieldlist->addDetail($detail);
                    }
                    
                    $this->fieldlist->addCloneAction();
                }
                else
                {
                    $this->onClear($param);
                }
                
                TTransaction::close(); // close transaction
	    }
	    else
            {
                $this->onClear($param);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Clear form
     */
    public function onClear($param)
    {
        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addCloneAction();
    }
    
    /**
     * Save the AlunoContrato and the AlunoContratoBeneficiario's
     */
    public static function onSave($param)
    {
        try
        {
            TTransaction::open('sample');
     
            $id = (int) $param['id'];
            $master = new AlunoContrato;
            $master->fromArray( $param);
            $master->preco_valor_integral = Utilidades::to_number($master->preco_valor_integral);
            $master->preco_parcela_valor = Utilidades::to_number($master->preco_parcela_valor);
            $master->preco_desconto = Utilidades::to_number($master->preco_desconto);
            $master->preco_parcela_valor_desconto = Utilidades::to_number($master->preco_parcela_valor_desconto);
            $master->preco_valor_total = Utilidades::to_number($master->preco_valor_total);
            $master->prazo_inicio = Carbon::createFromFormat('d/m/Y', $param['prazo_inicio'])->format('Y-m-d');
            $master->prazo_fim = Carbon::createFromFormat('d/m/Y', $param['prazo_fim'])->format('Y-m-d');
            $master->vencimento_parcela = Carbon::createFromFormat('d/m/Y', $param['vencimento_parcela'])->format('Y-m-d');
            $master->store(); // save master object
            
            // delete details
            AlunoContratoBeneficiario::where('aluno_contrato_id', '=', $master->id)->delete();
            
            if( !empty($param['list_aluno_id']) AND is_array($param['list_aluno_id']) )
            {
                foreach( $param['list_aluno_id'] as $row => $aluno_id)
                {
                    if (!empty($aluno_id))
                    {
                        $detail = new AlunoContratoBeneficiario;
                        $detail->aluno_contrato_id = $master->id;
                        $detail->aluno_id = $param['list_aluno_id'][$row];
                        $detail->store();
                    }
                }
            }
            
            $data = new stdClass;
            $data->id = $master->id;
            TForm::sendData('form_AlunoContrato', $data);
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onExit()
    {
        $result = TSession::getValue('AlunoContratoList');

        $query = isset($result['query']) ? $result['query'] : null;

        if (!empty($query))
        {
            TScript::create("
                Adianti.waitMessage = 'Listando...';__adianti_post_data('AlunoContratoForm', '$query');                                 
        ");
        }
    }
}
