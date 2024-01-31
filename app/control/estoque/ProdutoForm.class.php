<?php

use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Wrapper\TDBCombo;

/**
 * ProdutoForm Master/Detail
 * @author  Fred Azv.
 */
class ProdutoForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Produto');
        $this->form->setFormTitle('Produto');
        $this->form->setFieldSizes('100%');
        
        // master fields
        $id = new TEntry('id');

        $produto_grupo_id = new TDBCombo('produto_grupo_id', 'sample', 'ProdutoGrupo', 'id', 'nome');

        $produto_subgrupo_id = new TDBCombo('produto_subgrupo_id', 'sample', 'ProdutoSubgrupo', 'id', 'nome');
        //$produto_subgrupo_id->addItems($produto_grupo_id);
        
        $grupo_id = new TAction(array($this, 'onChangeAction'));
        $produto_grupo_id->setChangeAction($grupo_id);

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $ncm = new TEntry('ncm');
        $barras = new TEntry('barras');
        $cod_referencia = new TEntry('cod_referencia');
        $padrao = new TEntry('padrao');

        $unidade_medida_id = new TDBCombo('unidade_medida_id', 'sample', 'ProdutoUnidadeMedida', 'id', 'cod' );

        $nome_produto = new TEntry('nome_produto');
        $local = new TEntry('local');
        $preco_venda = new TEntry('preco_venda');
        $estoque_min = new TEntry('estoque_min');
        $estoque_max = new TEntry('estoque_max');
        $serial = new TEntry('serial');
        $preco_ultima_compra = new TNumeric('preco_ultima_compra', 2, ',', '.', true);
        $obs = new TText('obs');
        $tipo_produto = new TEntry('tipo_produto');
        $image_produto = new TFile('image_produto');
        $image_produto->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $image_produto->enableFileHandling();

        $produto_fabricante_id = new TDBUniqueSearch('produto_fabricante_id', 'sample', 'ProdutoFabricante', 'id', 'nome');

        $produto_modelo_id = new TDBUniqueSearch('produto_modelo_id', 'sample', 'ProdutoModelo', 'id', 'nome');

        $produto_complexidade_id = new TDBUniqueSearch('produto_complexidade_id', 'sample', 'ProdutoComplexidade', 'id', 'descricao');

        $composicao = new TCombo('composicao');
        $composicao->addItems(Utilidades::sim_nao());

        $kit = new TCombo('kit');
        $kit->addItems(Utilidades::sim_nao());

        $nve = new TEntry('nve');
        $cEANTrib = new TEntry('cEANTrib');
        $CEST = new TEntry('CEST');
        $vFrete = new TNumeric('vFrete', 2, ',', '.', true);
        $vSeg = new TNumeric('vSeg', 2, ',', '.', true);
        $vOutro = new TNumeric('vOutro', 2, ',', '.', true);
        $extipi = new TEntry('extipi');

        $orig = new TDBCombo('orig', 'sample', 'NfeIcmsOrigem', 'cod', 'descricao');

        $tipo_produto = new TCombo('tipo_produto');
        $combo_tipo_produto = [];
        $combo_tipo_produto['00'] = '00 - Mercadoria para Revenda';
        $combo_tipo_produto['01'] = '01 - Materia Prima';
        $combo_tipo_produto['02'] = '02 - Embalagem';
        $combo_tipo_produto['03'] = '03 - Produto em Processo';
        $combo_tipo_produto['04'] = '04 - Produto Acabado';
        $combo_tipo_produto['05'] = '05 - Subproduto';
        $combo_tipo_produto['06'] = '06 - Produto Intermediário';
        $combo_tipo_produto['07'] = '07 - Material de Uso e Consumo';
        $combo_tipo_produto['08'] = '08 - Ativo Imobilizado';
        $combo_tipo_produto['09'] = '09 - Serviços';
        $combo_tipo_produto['10'] = '10 - Outros insumos';
        $combo_tipo_produto['99'] = '99 - Outras';
        $tipo_produto->addItems($combo_tipo_produto);

        $tributacao_id = new TDBCombo('tributacao_id','sample','NfTributacao','id','nome','nome');

        $MVA = new TEntry('MVA');

        $impostos_venda = new TDBCombo('impostos_venda', 'sample', 'NfeRegra', 'id', 'nome');
        $impostos_compra = new TDBCombo('impostos_compra', 'sample', 'NfeRegra', 'id', 'nome');

        $anvisa = new TEntry('anvisa');
        $pis = new TNumeric('pis', 2, ',', '.', true);
        $cofins = new TNumeric('cofins', 2, ',', '.', true);
        $icms = new TNumeric('icms', 2, ',', '.', true);
        $iss = new TNumeric('iss', 2, ',', '.', true);
        $ipi = new TNumeric('ipi', 2, ',', '.', true);

        $tipo = new THidden('tipo');
        $tipo->setValue('P');
        //$tipo->addItems(Utilidades::tipo_produto_servico());

        $comissao = new TCombo('comissao');
        $comissao->addItems(Utilidades::sim_nao());

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        $this->form->appendPage('Dados Principais');
        // master fields
        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Descrição do produto/serviço'), $nome_produto ]
        );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-8'];
        
        $row = $this->form->addFields( [ new TLabel('Grupo'), $produto_grupo_id ],    
                                       [ new TLabel('Sub-grupo'), $produto_subgrupo_id ],
                                       [ new TLabel('Código de Barras'), $barras ],
                                       [ new TLabel('COD/REF'), $cod_referencia ]);
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-2', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Serial'), $serial ],    
                                       [ new TLabel('Unidade de medida'), $unidade_medida_id ],
                                       [ new TLabel('Local'), $local ],
                                       [ new TLabel('Estoque Min'), $estoque_min ],
                                       [ new TLabel('Estoque Max'), $estoque_max ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-4', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Fabricante'), $produto_fabricante_id ],    
                                       [ new TLabel('Modelo'), $produto_modelo_id ],
                                       [ new TLabel('Complexidade'), $produto_complexidade_id ]);
        $row->layout = ['col-sm-4', 'col-sm-4', 'col-sm-4'];
        
        $row = $this->form->addFields([ new TLabel('Última Compra'), $preco_ultima_compra ]);
        $row->layout = ['col-sm-2'];
        
        $this->form->addContent( ['<h4><b>Informação Complementares</b></h4><hr>'] );
        
        $row = $this->form->addFields( [ new TLabel('É um Kit?'), $kit ],    
                                       [ new TLabel('Anvisa'), $anvisa ],
                                       [ new TLabel('NVE'), $nve ],
                                       [ new TLabel('Cód.EAN'), $cEANTrib ],
                                       [ new TLabel('NCM'), $ncm ],
                                       [ new TLabel('CEST'), $CEST ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Regra de Imposto de Venda'), $impostos_venda ],    
                                       [ new TLabel('Regra de Imposto de Compra'), $impostos_compra ]);
        $row->layout = ['col-sm-6', 'col-sm-6'];
        
        $row = $this->form->addFields( [ new TLabel('Comissiona?'), $comissao ]);
        $row->layout = ['col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Origem'), $orig ],
                                       [ new TLabel('Tipo Produto'), $tipo_produto],
                                       [ new TLabel('Tipo de Tributação'), $tributacao_id]
        );
        $row->layout = ['col-sm-4','col-sm-4','col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Observação'), $obs ],    
                                       [ new TLabel('Imagem do produto'), $image_produto ]);
        $row->layout = ['col-sm-6', 'col-sm-3'];


        //composição do kit

        $det_uniqid = new THidden('det_uniqid');
        $det_id = new THidden('det_id');
        $unit_produto = new TCriteria();
        $unit_produto->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $det_composicao_id = new TDBUniqueSearch('det_composicao_id', 'sample', 'Produto', 'id','nome_produto','nome_produto', $unit_produto);
        $det_quantidade = new TNumeric('det_quantidade', 2, ',', '.', true);
        $det_valor_unidade = new TNumeric('det_valor_unidade', 2, ',', '.', true);
        $det_valor_total = new TNumeric('det_valor_total', 2, ',', '.', true);
        $det_valor_total->setEditable(FALSE);

        $this->form->addContent( ['<h4><b>Composição</b></h4><hr>'] );
        $this->form->addFields( [$det_uniqid] );
        $this->form->addFields( [$det_id] );

        $row = $this->form->addFields( [ new TLabel('Produto Composição'), $det_composicao_id ],  
                                       [ new TLabel('Valor Unidade'), $det_valor_unidade ],    
                                       [ new TLabel('Quantidade'), $det_quantidade ],
                                       [ new TLabel('Valor Total'), $det_valor_total ]
        );
        $row->layout = ['col-sm-6','col-sm-2','col-sm-2','col-sm-2'];

        $add2 = TButton::create('add2', [$this, 'onDetailAddComposicao'], 'Adicionar Composição', 'fa:plus-circle green');
        $add2->getAction()->setParameter('static','1');
        $this->form->addFields( [], [$add2] );

        $this->det_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->det_list->setId('ProdutoComposicao_list');
        $this->det_list->generateHiddenFields();
        $this->det_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";

        $this->det_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
        $this->det_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
        $this->det_list->addColumn( $col_produto_id = new TDataGridColumn('composicao_id', 'Composição', 'left', 100) );
        $this->det_list->addColumn( $col_valor_unidade = new TDataGridColumn('valor_unidade', 'Valor Unitário', 'right', 100) );
        $this->det_list->addColumn( $col_quantidade = new TDataGridColumn('quantidade', 'Quantidade', 'right', 100) );
        $this->det_list->addColumn( $col_valor_total = new TDataGridColumn('valor_total', 'Valor Total', 'right', 100) );

        $col_produto_id->setTransformer(function($value) {
            return ProdutoNome::findInTransaction('sample', $value)->nome_concatenado;
        });

        $format_value = function($value) {
            if (is_numeric($value)) {
                return number_format($value, 2, ',', '.');
            }
            return $value;
        };

        $col_valor_unidade->setTransformer( $format_value );
        $col_quantidade->setTransformer( $format_value );
        $col_valor_total->setTransformer( $format_value );

        $action1 = new TDataGridAction([$this, 'onDetailEditComposicao'] );
        $action1->setFields( ['uniqid', '*'] );

        $action2 = new TDataGridAction([$this, 'onDetailDeleteComposicao']);
        $action2->setField('uniqid');

        $this->det_list->addAction($action1, _t('Edit'), 'fa:edit blue');
        $this->det_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->det_list->createModel();
        
        $panel2 = new TPanelGroup;
        $panel2->add($this->det_list);
        $panel2->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel2] );

        //
        $this->form->appendPage('Tebela de preço');

        // detail fields
        $detail_id = new THidden('detail_id');
        $detail_tabela_preco_id = new TDBCombo('detail_tabela_preco_id', 'sample', 'TabelaPrecos', 'id', 'nome' );
        $detail_preco = new TNumeric('detail_preco', 2, ',', '.', true);
        $detail_controla_validade = new TCombo('detail_controla_validade');
        $combo_detail_controla_validades = array();
        $combo_detail_controla_validades['S'] = 'Sim';
        $combo_detail_controla_validades['N'] = 'Não';
        $detail_controla_validade->addItems($combo_detail_controla_validades);
        $detail_data_validade = new TDate('detail_data_validade');
        $detail_descontoMax = new TEntry('detail_descontoMax');
        $detail_tem_comissao = new TEntry('detail_tem_comissao');
        $detail_comissao = new TEntry('detail_comissao');
        $detail_tem_promocao = new TEntry('detail_tem_promocao');
        $detail_promocao = new TEntry('detail_promocao');
        $detail_promocao_validade = new TDate('detail_promocao_validade');
        $detail_markup_preco_custo = new TNumeric('detail_markup_preco_custo', 2, ',', '.', true);
        $detail_markup_despesa_variavel = new TNumeric('detail_markup_despesa_variavel', 2, ',', '.', true);
        $detail_markup_despesa_fixa = new TNumeric('detail_markup_despesa_fixa', 2, ',', '.', true);
        $detail_markup_lucro_desejado = new TNumeric('detail_markup_lucro_desejado', 2, ',', '.', true);
        $detail_markup_preco_venda = new TEntry('detail_markup_preco_venda');
        $detail_markup_preco_venda->setEditable(FALSE);
        $detail_markup_comissao_tecnico = new TNumeric('detail_markup_comissao_tecnico', 2, ',', '.');
        $detail_markup_comissao_parceiro = new TNumeric('detail_markup_comissao_parceiro', 2, ',', '.');

        // detail fields
        $this->form->addContent( ['<h4><b>Formação de preço</b></h4><hr>'] );
        $this->form->addFields( [$detail_id] );

        //$this->form->addFields( [new TLabel('Tem Comissao')], [$detail_tem_comissao] );
        //$this->form->addFields( [new TLabel('Comissao')], [$detail_comissao] );
        //$this->form->addFields( [new TLabel('Tem Promoção?')], [$detail_tem_promocao] );
        //$this->form->addFields( [new TLabel('Promocao')], [$detail_promocao] );
        //$this->form->addFields( [new TLabel('Promocao Validade')], [$detail_promocao_validade] );

        $row = $this->form->addFields( [ new TLabel('Nome da tabela'), $detail_tabela_preco_id ],    
                                       [ new TLabel('Preço do Produto'), $detail_preco ],
                                       [ new TLabel('Validade?'), $detail_controla_validade ],
                                       [ new TLabel('Data Validade'), $detail_data_validade ],
                                       [ new TLabel('Desconto max.'), $detail_descontoMax ]);
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Preço de Custo'), $detail_markup_preco_custo ],    
                                       [ new TLabel('Despesa Var.(%)'), $detail_markup_despesa_variavel ],
                                       [ new TLabel('Despesa Fix.(%)'), $detail_markup_despesa_fixa ],
                                       [ new TLabel('Lucro desejado(%)'), $detail_markup_lucro_desejado ],
                                       [ new TLabel('Comissão Técnico'), $detail_markup_comissao_tecnico ],
                                       [ new TLabel('Comissão Parceiria'), $detail_markup_comissao_parceiro ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Preço do sugerido'), $detail_markup_preco_venda ]);
        $row->layout = ['col-sm-2'];

        $add = TButton::create('add', [$this, 'onSaveDetail'], 'Register', 'fa:save');
        $this->form->addFields( [], [$add] )->style = 'background: whitesmoke; padding: 5px; margin: 1px;';
        
        $this->detail_list = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->detail_list->setId('Produto_list');
        
        // items
        $this->detail_list->addQuickColumn('Tabela', 'tabela_preco_id', 'left', 100);
        $this->detail_list->addQuickColumn('Preço', 'preco', 'left', 100);
        /*$this->detail_list->addQuickColumn('C. Validade?', 'controla_validade', 'left', 100);
        $this->detail_list->addQuickColumn('Validade', 'data_validade', 'left', 50);*/
        $this->detail_list->addQuickColumn('Custo', 'markup_preco_custo', 'left', 50);
        $this->detail_list->addQuickColumn('Var.(%)', 'markup_despesa_variavel', 'left', 50);
        $this->detail_list->addQuickColumn('Fix.(%)', 'markup_despesa_fixa', 'left', 50);
        $this->detail_list->addQuickColumn('Lucro(%)', 'markup_lucro_desejado', 'left', 50);
        $this->detail_list->addQuickColumn('C. Técnico', 'markup_comissao_tecnico', 'left', 50);
        $this->detail_list->addQuickColumn('C. Parceiria', 'markup_comissao_parceiro', 'left', 50);
        //$this->detail_list->addQuickColumn('Desconto max.', 'descontoMax', 'left', 50);
        //$this->detail_list->addQuickColumn('Tem Comissao', 'tem_comissao', 'left', 100);
        //$this->detail_list->addQuickColumn('Comissao', 'comissao', 'left', 100);
        //$this->detail_list->addQuickColumn('Tem Promocao', 'tem_promocao', 'left', 100);
        //$this->detail_list->addQuickColumn('Promocao', 'promocao', 'left', 100);
        //$this->detail_list->addQuickColumn('Promocao Validade', 'promocao_validade', 'left', 50);

        // detail actions
        $this->detail_list->addQuickAction( 'Edit',   new TDataGridAction([$this, 'onEditDetail']),   'id', 'fa:edit blue');
        $this->detail_list->addQuickAction( 'Delete', new TDataGridAction([$this, 'onDeleteDetail']), 'id', 'fa:trash red');
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );

        $btn = $this->form->addAction( _t('Save'),  new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction( _t('Clear'), new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction(['ProdutoList','onReload']), 'fa:arrow-circle-left blue');
        $this->form->addActionLink(_t('New'), new TAction(['ProdutoForm', 'onEdit']), 'fa:plus green');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ProdutoList'));
        $container->add($this->form);
        parent::add($container);

        $detail_markup_preco_custo->onBlur   = 'markup()';
        $detail_markup_despesa_variavel->onBlur   = 'markup()';
        $detail_markup_despesa_fixa->onBlur   = 'markup()';
        $detail_markup_lucro_desejado->onBlur   = 'markup()';
        $detail_markup_comissao_tecnico->onBlur   = 'markup()';
        $detail_markup_comissao_parceiro->onBlur   = 'markup()';

        $det_quantidade->onBlur   = 'markup()';
        $det_valor_unidade->onBlur   = 'markup()';
        

        TScript::create('markup = function() {

                let valorComposicao;
                valorComp = convertToFloatNumber(form_Produto.det_quantidade.value) * convertToFloatNumber(form_Produto.det_valor_unidade.value);
                valorComposicao = convertToFloatNumber(valorComp);
                form_Produto.det_valor_total.value = formatMoney(valorComposicao);
                console.log(valorComp);

                let markup;    
                markup = 100/[100 -(
                    convertToFloatNumber(form_Produto.detail_markup_despesa_variavel.value) +
                    convertToFloatNumber(form_Produto.detail_markup_despesa_fixa.value) +
                    convertToFloatNumber(form_Produto.detail_markup_lucro_desejado.value) +
                    convertToFloatNumber(form_Produto.detail_markup_comissao_tecnico.value) +
                    convertToFloatNumber(form_Produto.detail_markup_comissao_parceiro.value)

                )];

                let valorMarkup = 
                convertToFloatNumber(form_Produto.detail_markup_preco_custo.value) * markup;

                 let valor = convertToFloatNumber(valorMarkup);
                 form_Produto.detail_markup_preco_venda.value = formatMoney(valor);
            };
        ');
    }
    
    public function onDetailAddComposicao( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
                        
            $uniqid = !empty($data->det_uniqid) ? $data->det_uniqid : uniqid();
            
            $grid_data = [];
            $grid_data['uniqid'] = $uniqid;
            $grid_data['id'] = $data->det_id;
            $grid_data['composicao_id'] = $data->det_composicao_id;
            $grid_data['valor_unidade'] = Utilidades::formatar_valor($data->det_valor_unidade);
            $grid_data['quantidade'] = Utilidades::formatar_valor($data->det_quantidade);
            $grid_data['valor_total'] = Utilidades::formatar_valor($data->det_valor_total);
            
            // insert row dynamically
            $row = $this->det_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('ProdutoComposicao_list', $uniqid, $row);
            
            // clear det form fields
            $data->det_uniqid = '';
            $data->det_id = '';
            $data->det_composicao_id = '';
            $data->det_valor_unidade = '';
            $data->det_quantidade = '';
            $data->det_valor_total = '';
            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_Produto', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onDetailEditComposicao( $param )
    {
        $data = new stdClass;
        $data->det_uniqid = $param['uniqid'];
        $data->det_id = $param['id'];
        $data->det_composicao_id = $param['composicao_id'];
        $data->det_quantidade = Utilidades::formatar_valor($param['quantidade']);
        $data->det_valor_unidade = Utilidades::formatar_valor($param['valor_unidade']);
        $data->det_valor_total = Utilidades::formatar_valor($param['valor_total']);

        TForm::sendData( 'form_Produto', $data, false, false );
    }

    public static function onDetailDeleteComposicao( $param )
    {
        $data = new stdClass;
        $data->det_uniqid = '';
        $data->det_id = '';
        $data->det_composicao_id = '';
        $data->det_quantidade = '';
        $data->det_valor_unidade = '';
        $data->det_valor_total = '';

        TForm::sendData( 'form_Produto', $data, false, false );

        TDataGrid::removeRowById('ProdutoComposicao_list', $param['uniqid']);
    }

    public static function onChangeAction($param)
    {   
        if( isset( $param['produto_grupo_id'] ) )
        {
            try
             {
                TTransaction::open('sample');
                
                $criteria = new TCriteria; 
                $criteria->add(new TFilter('produto_grupo_id', '=', $param['produto_grupo_id'] )); 
                
                TTransaction::close();
                TDBCombo::reloadFromModel('form_Produto', 'produto_subgrupo_id', 'sample','ProdutoSubgrupo','id','nome','', $criteria);
                
             }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage());
            }
        }
    }


    public function onClear($param)
    {
        $this->form->clear(TRUE);
        TSession::setValue(__CLASS__.'_items', array());
        $this->onReload( $param );
    }
    

    public function onSaveDetail( $param )
    {
        try
        {
            TTransaction::open('sample');
            $data = $this->form->getData();
            
            $items = TSession::getValue(__CLASS__.'_items');
            $key = empty($data->detail_id) ? 'X'.mt_rand(1000000000, 1999999999) : $data->detail_id;
            
            $items[ $key ] = array();
            $items[ $key ]['id'] = $key;
            $items[ $key ]['tabela_preco_id'] = $data->detail_tabela_preco_id;
            $items[ $key ]['preco'] = $data->detail_preco;
            $items[ $key ]['controla_validade'] = $data->detail_controla_validade;
            $items[ $key ]['data_validade'] = $data->detail_data_validade;
            $items[ $key ]['descontoMax'] = $data->detail_descontoMax;
            $items[ $key ]['tem_comissao'] = $data->detail_tem_comissao;
            $items[ $key ]['comissao'] = $data->detail_comissao;
            $items[ $key ]['tem_promocao'] = $data->detail_tem_promocao;
            $items[ $key ]['promocao'] = $data->detail_promocao;
            $items[ $key ]['promocao_validade'] = $data->detail_promocao_validade;
            $items[ $key ]['markup_preco_custo'] = $data->detail_markup_preco_custo;
            $items[ $key ]['markup_despesa_variavel'] = $data->detail_markup_despesa_variavel;
            $items[ $key ]['markup_despesa_fixa'] = $data->detail_markup_despesa_fixa;
            $items[ $key ]['markup_lucro_desejado'] = $data->detail_markup_lucro_desejado;
            $items[ $key ]['markup_preco_venda'] = $data->detail_markup_preco_venda;
            $items[ $key ]['markup_comissao_tecnico'] = $data->detail_markup_comissao_tecnico;
            $items[ $key ]['markup_comissao_parceiro'] = $data->detail_markup_comissao_parceiro;
            
            TSession::setValue(__CLASS__.'_items', $items);
            
            // clear detail form fields
            $data->detail_id = '';
            $data->detail_tabela_preco_id = '';
            $data->detail_preco = '';
            $data->detail_controla_validade = '';
            $data->detail_data_validade = '';
            $data->detail_descontoMax = '';
            $data->detail_tem_comissao = '';
            $data->detail_comissao = '';
            $data->detail_tem_promocao = '';
            $data->detail_promocao = '';
            $data->detail_promocao_validade = '';
            $data->detail_markup_preco_custo = '';
            $data->detail_markup_despesa_variavel = '';
            $data->detail_markup_despesa_fixa = '';
            $data->detail_markup_lucro_desejado = '';
            $data->detail_markup_preco_venda = '';
            $data->detail_markup_comissao_tecnico = '';
            $data->detail_markup_comissao_parceiro = '';
            
            TTransaction::close();
            $this->form->setData($data);
            
            $this->onReload( $param ); // reload the items
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Load an item from session list to detail form
     * @param $param URL parameters
     */
    public static function onEditDetail( $param )
    {
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        // get the session item
        $item = $items[ $param['key'] ];
        
        $data = new stdClass;
        $data->detail_id = $item['id'];
        $data->detail_tabela_preco_id = $item['tabela_preco_id'];
        $data->detail_preco = Utilidades::formatar_valor($item['preco']);
        $data->detail_controla_validade = $item['controla_validade'];
        $data->detail_data_validade = $item['data_validade'];
        $data->detail_descontoMax = Utilidades::formatar_valor($item['descontoMax']);
        $data->detail_tem_comissao = $item['tem_comissao'];
        $data->detail_comissao = $item['comissao'];
        $data->detail_tem_promocao = $item['tem_promocao'];
        $data->detail_promocao = $item['promocao'];
        $data->detail_promocao_validade = $item['promocao_validade'];
        $data->detail_markup_preco_custo = Utilidades::formatar_valor($item['markup_preco_custo']);
        $data->detail_markup_despesa_variavel = $item['markup_despesa_variavel'];
        $data->detail_markup_despesa_fixa = $item['markup_despesa_fixa'];
        $data->detail_markup_lucro_desejado = $item['markup_lucro_desejado'];
        $data->detail_markup_preco_venda = Utilidades::formatar_valor($item['markup_preco_venda']);
        $data->detail_markup_comissao_tecnico = Utilidades::formatar_valor($item['markup_comissao_tecnico']);
        $data->detail_markup_comissao_parceiro = Utilidades::formatar_valor($item['markup_comissao_parceiro']);
        
        // fill detail fields
        TForm::sendData( 'form_Produto', $data );
    }
    

    public static function onDeleteDetail( $param )
    {
        // reset items
        $data = new stdClass;
            $data->detail_tabela_preco_id = '';
            $data->detail_preco = '';
            $data->detail_controla_validade = '';
            $data->detail_data_validade = '';
            $data->detail_descontoMax = '';
            $data->detail_tem_comissao = '';
            $data->detail_comissao = '';
            $data->detail_tem_promocao = '';
            $data->detail_promocao = '';
            $data->detail_promocao_validade = '';
            $data->detail_markup_preco_custo = '';
            $data->detail_markup_despesa_variavel = '';
            $data->detail_markup_despesa_fixa = '';
            $data->detail_markup_lucro_desejado = '';
            $data->detail_markup_preco_venda = '';
            $data->detail_markup_comissao_tecnico = '';
            $data->detail_markup_comissao_parceiro = '';
        
        // clear form data
        TForm::sendData('form_Produto', $data );
        
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        // get detail id
        $detail_id = $param['key'];
        
        // delete the item from session
        unset($items[ $detail_id ] );
        
        // rewrite session items
        TSession::setValue(__CLASS__.'_items', $items);
        
        // delete item from screen
        TScript::create("ttable_remove_row_by_id('Produto_list', '{$detail_id}')");
    }
    

    public function onReload($param)
    {
        // read session items
        $items = TSession::getValue(__CLASS__.'_items');
        
        $this->detail_list->clear(); // clear detail list
        
        if ($items)
        {
            foreach ($items as $list_item)
            {
                $item = (object) $list_item;
                
                $row = $this->detail_list->addItem( $item );
                $row->id = $list_item['id'];
            }
        }

        $this->onChangeAction($param);
        $this->loaded = TRUE;
    }
    
    /**
     * Load Master/Detail data from database to form/session
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('sample');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Produto($key);

                $itemsComposicao  = ProdutoComposicao::where('produto_id', '=', $key)->load();
                
                foreach( $itemsComposicao as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->det_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);

                $items  = ProdutoTabelaPreco::where('produto_id', '=', $key)->load();
                
                $session_items = array();
                foreach( $items as $item )
                {
                    $item_key = $item->id;
                    $session_items[$item_key] = $item->toArray();
                    $session_items[$item_key]['id'] = $item->id;
                    $session_items[$item_key]['tabela_preco_id'] = $item->tabela_preco_id;
                    $session_items[$item_key]['preco'] = number_format($item->preco, 2, ',', '.');
                    $session_items[$item_key]['controla_validade'] = $item->controla_validade;
                    $session_items[$item_key]['data_validade'] = $item->data_validade;
                    $session_items[$item_key]['descontoMax'] = number_format($item->descontoMax, 2, ',', '.');
                    $session_items[$item_key]['tem_comissao'] = $item->tem_comissao;
                    $session_items[$item_key]['comissao'] = $item->comissao;
                    $session_items[$item_key]['tem_promocao'] = $item->tem_promocao;
                    $session_items[$item_key]['promocao'] = $item->promocao;
                    $session_items[$item_key]['promocao_validade'] = $item->promocao_validade;
                    $session_items[$item_key]['markup_preco_custo'] = number_format($item->markup_preco_custo, 2, ',', '.');
                    $session_items[$item_key]['markup_despesa_variavel'] = number_format($item->markup_despesa_variavel, 2, ',', '.');
                    $session_items[$item_key]['markup_despesa_fixa'] = number_format($item->markup_despesa_fixa, 2, ',', '.');
                    $session_items[$item_key]['markup_lucro_desejado'] = number_format($item->markup_lucro_desejado, 2, ',', '.');
                    $session_items[$item_key]['markup_preco_venda'] = number_format($item->markup_preco_venda, 2, ',', '.');
                    $session_items[$item_key]['markup_comissao_tecnico'] = number_format($item->markup_comissao_tecnico, 2, ',', '.');
                    $session_items[$item_key]['markup_comissao_parceiro'] = number_format($item->markup_comissao_parceiro, 2, ',', '.');
                }
                TSession::setValue(__CLASS__.'_items', $session_items);
                
                $this->form->setData($object); // fill the form with the active record data
                $this->onChangeAction($param);
                $this->onReload( $param ); // reload items list
                TTransaction::close(); // close transaction
            }
            else
            {
                $this->form->clear(TRUE);
                $this->onChangeAction($param);
                TSession::setValue(__CLASS__.'_items', null);
                $this->onReload( $param );
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form/session to database
     */
    public function onSave($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('sample');
            
            $data = $this->form->getData();
            $master = new Produto;
            $master->fromArray( (array) $data);
            $this->form->validate(); // form validation
            
            $master->store(); // save master object
            // delete details
            $old_items = ProdutoTabelaPreco::where('produto_id', '=', $master->id)->load();
            
            $keep_items = array();
            
            // get session items
            $items = TSession::getValue(__CLASS__.'_items');
            
            if( $items )
            {
                foreach( $items as $item )
                {
                    if (substr($item['id'],0,1) == 'X' ) // new record
                    {
                        $detail = new ProdutoTabelaPreco;
                    }
                    else
                    {
                        $detail = ProdutoTabelaPreco::find($item['id']);
                    }
                    $detail->tabela_preco_id  = $item['tabela_preco_id'];
                    $detail->preco  = number_format($item['preco'], 2, '.', '');
                    $detail->controla_validade  = $item['controla_validade'];
                    $detail->data_validade  = $item['data_validade'];
                    $detail->descontoMax  = number_format($item['descontoMax'], 2, '.', '');
                    $detail->tem_comissao  = $item['tem_comissao'];
                    $detail->comissao  = number_format($item['comissao'], 2, '.', '');
                    $detail->tem_promocao  = $item['tem_promocao'];
                    $detail->promocao  = $item['promocao'];
                    $detail->promocao_validade  = $item['promocao_validade'];
                    $detail->produto_id = $master->id;
                    $detail->markup_preco_custo  = number_format($item['markup_preco_custo'], 2, '.', '');
                    $detail->markup_despesa_variavel  = number_format($item['markup_despesa_variavel'], 2, '.', '');
                    $detail->markup_despesa_fixa  = number_format($item['markup_despesa_fixa'], 2, '.', '');
                    $detail->markup_lucro_desejado  = number_format($item['markup_lucro_desejado'], 2, '.', '');
                    $detail->markup_preco_venda  = number_format($item['markup_preco_venda'], 2, '.', '');
                    $detail->markup_comissao_tecnico  = number_format($item['markup_comissao_tecnico'], 2, '.', '');
                    $detail->markup_comissao_parceiro  = number_format($item['markup_comissao_parceiro'], 2, '.', '');
                    $detail->store();
                    
                    $keep_items[] = $detail->id;
                }
            }
            
            if ($old_items)
            {
                foreach ($old_items as $old_item)
                {
                    if (!in_array( $old_item->id, $keep_items))
                    {
                        $old_item->delete();
                    }
                }
            }

            ProdutoComposicao::where('produto_id', '=', $master->id)->delete();
            
            if( $param['ProdutoComposicao_list_composicao_id'] )
            {
                foreach( $param['ProdutoComposicao_list_composicao_id'] as $key => $item_id )
                {
                    $detail = new ProdutoComposicao;
                    $detail->composicao_id  = $param['ProdutoComposicao_list_composicao_id'][$key];
                    $detail->quantidade  = Utilidades::to_number($param['ProdutoComposicao_list_quantidade'][$key]);
                    $detail->valor_unidade  = Utilidades::to_number($param['ProdutoComposicao_list_valor_unidade'][$key]);
                    $detail->valor_total  = Utilidades::to_number($param['ProdutoComposicao_list_valor_total'][$key]);
                    $detail->produto_id = $master->id;
                    $detail->store();
                }
            }
            
            TTransaction::close(); // close the transaction
            
            $this->onEdit(array('key'=>$master->id));
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
    
    /**
     * Show the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}
