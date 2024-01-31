<?php

use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Wrapper\TDBCombo;

/**
 * AssociacaoItens Form
 * @author  Fred Azv.
 */
class AssociacaoItens extends TPage
{
    protected $form; // form

    public function __construct( $param )
    {
        parent::__construct();
            
        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder('form_NfeItemsLote');
        $this->form->setFormTitle('Associar Produtos');
        $this->form->setFieldSizes('100%');

        $id  = new THidden('id');
        $id->setValue($param['id']);

        $nfe_entrada_id  = new THidden('nfe_entrada_id');
        $nfe_entrada_id->setValue($param['nfe_entrada_id']);

        //var_dump($param);
        $produto_id = new TDBUniqueSearch('produto_id', 'sample', 'ProdutoNome', 'id', 'nome_produto', 'id');
        $produto_id->setMask('{nome_produto} - {fabricante} - {modelo}');
        $produto_id->setMinlength(0);

        $cfop_entrada = new TEntry('cfop_entrada');

        $vUnCom = new TNumeric('vUnCom', 2, ',', '.', true);

        $precoFator = new TNumeric('precoFator', 2, ',', '.', true);
        $precoFator->setEditable(FALSE);
        
        $produto_fator_id = new TDBCombo('produto_fator_id','sample','ProdutoFator','id','descricao','descricao');

        $precoFat = new TAction(array($this, 'onChangeAction'));
        $produto_fator_id->setChangeAction($precoFat);

        $lote = new TEntry('lote');
        $validade_lote = new TDate('validade_lote');

        $produto_tipo = new TCombo('produto_tipo');
        $combo_produto_tipo = array();
        $combo_produto_tipo['1'] = 'Produto para Revenda';
        $combo_produto_tipo['2'] = 'Produto para Imobilizado';
        $combo_produto_tipo['3'] = 'Produto para Consumo';
        $produto_tipo->addItems($combo_produto_tipo);

        $this->form->addFields( [$id] );
        $this->form->addFields( [$nfe_entrada_id] );

        $row = $this->form->addFields( [ new TLabel('Produto cadastrado'), $produto_id ]
        );
        $row->layout = ['col-sm-11'];
        
        $row = $this->form->addFields( [ new TLabel('CFOP'), $cfop_entrada ],
                                       [ new TLabel('Lote'), $lote ],
                                       [ new TLabel('Validade'), $validade_lote ]
        );
        $row->layout = ['col-sm-2','col-sm-6','col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Fator de conversão'),  $produto_fator_id ],
                                       [ new TLabel('Preço de Compra'),  $vUnCom  ]
        );
        $row->layout = ['col-sm-8','col-sm-4'];

        $row = $this->form->addFields( [ new TLabel(''), ],
                                       [ new TLabel('Preço pelo Fator'),  $precoFator  ]
        );
        $row->layout = ['col-sm-8','col-sm-4'];

        $row = $this->form->addFields( [ new TLabel('Tipo de Produto'),  $produto_tipo ]
        );
        $row->layout = ['col-sm-12'];

        $button = $this->createButton('new', ['ProdutoFormWindow', 'onClear'], '', 'fa:plus-circle green', 
        ['class_return' => 'form_NfeItemsLote', 'field_return' => 'fornecedor_id']);
        $button->class = 'btn btn-default inline-button';
        $button->title = 'Novo Produto';
        $produto_id->after($button);
        $this->form->addField($button);

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }

        $btn = $this->form->addAction('Salvar Associação do produto', new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Fechar', new TAction([$this,'onClose']), 'fa:angle-double-left');
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        
        parent::add($container);

    }

    public static function onChangeAction($param)
    {   
        if( isset( $param['produto_fator_id'] ) )
        {
            try
            {
                TTransaction::open('sample');
                
                $fator = new ProdutoFator($param['produto_fator_id']);
                
                $val = 0.00;
                if($fator->tipo == 'M'){
                    $val = Utilidades::to_number($param['vUnCom']) * (float)$fator->valor;
                }else{
                    $val = Utilidades::to_number($param['vUnCom']) / (float)$fator->valor;
                }

                $obj = new StdClass;
                $obj->precoFator = Utilidades::formatar_valor($val);
                TForm::sendData('form_NfeItemsLote', $obj);

                TTransaction::close();
            
            }
            catch (Exception $e)
            {
                new TMessage('error', $e->getMessage());
            }
        }
    }

    public function createButton($name, $callback, $label, $image, $param = null)
    {
        $button = new TButton( $name );
        $button->setAction(new TAction( $callback, $param ), $label);
        $button->setImage( $image );
        return $button;
    }
    

    public function onSave( $param )
    {
        try
        {   
            $data = $this->form->getData();
            $this->form->validate();

            TTransaction::open('sample');
            $save = new NfeEntradaItens;
            $save->fromArray( (array) $data);
            $save->store();

            $updateProduto = new Produto($data->produto_id);
            $updateProduto->preco_ultima_compra = $data->precoFator;
            $updateProduto->store();

            TTransaction::close();

            $action = new TAction(['NfeEntradaForm', 'onEdit'],[
                'id'  => $param['nfe_entrada_id'],
                'key'  => $param['nfe_entrada_id'],
            ]);
            new TMessage('info', 'Registro Salvo',$action);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();
        }
    }

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }

    public function onEdit($param)
    {
        try
        {
            TTransaction::open('sample');
            
            if (isset($param['id']))
            {
                $key = $param['id'];
                
                $object = new NfeEntradaItens($key);
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
        AdiantiCoreApplication::loadPage('NfeEntradaForm', 'onEdit', array(
            'id'  => $param['nfe_entrada_id'],
            'key'  => $param['nfe_entrada_id'],
        ));
    }
    

}