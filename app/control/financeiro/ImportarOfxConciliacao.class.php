<?php
error_reporting(E_ALL ^ E_NOTICE);
/**
 * ImportarOfxConciliacao Form
 * @author  Fred Azv.
 */
class ImportarOfxConciliacao extends TPage
{
    protected $form; // form

    public function __construct( $param )
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_ImportarOfxConciliacao');
        $this->form->setFormTitle('Conciliação de dados bancários');

        $this->datagrid_arquivo = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid_arquivo->style = 'width: 100%';
        $this->datagrid_arquivo->datatable = 'true';

        $this->datagrid_arquivo->setHeight(400);
        $this->datagrid_arquivo->makeScrollable();
        
        $column_dataMov = new TDataGridColumn('dataMov', 'Data', 'center','20%');
        $column_descricao = new TDataGridColumn('descricao', 'Descrição', 'left','50%');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right','20%');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'center','10%');

        $this->datagrid_arquivo->addColumn($column_dataMov);
        $this->datagrid_arquivo->addColumn($column_descricao);
        $this->datagrid_arquivo->addColumn($column_valor);
        $this->datagrid_arquivo->addColumn($column_tipo);

        $column_dataMov->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $format_value = function($value) {
            if (is_numeric($value)) {
                return number_format($value, 2, ',', '.');
            }
            return $value;
        };

        $column_valor->setTransformer( $format_value );

        $this->datagrid_arquivo->createModel();

        $extrato = new TVBox;
        $extrato->add($this->datagrid_arquivo);
        
        //Datagrid para a listagem de consiliação com os dados do sistema
        $this->datagrid_sistema = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid_sistema->style = 'width: 100%';
        $this->datagrid_sistema->datatable = 'true';

        $this->datagrid_sistema->setHeight(400);
        $this->datagrid_sistema->makeScrollable();
        
        $column_dataMov2 = new TDataGridColumn('data_vencimento', 'Data', 'center','20%');
        $column_descricao2 = new TDataGridColumn('descricao', 'Descrição', 'left','50%');
        $column_valor2 = new TDataGridColumn('valor', 'Valor', 'right','20%');
        $column_tipo2 = new TDataGridColumn('tipo', 'Tipo', 'center','10%');

        $this->datagrid_sistema->addColumn($column_dataMov2);
        $this->datagrid_sistema->addColumn($column_descricao2);
        $this->datagrid_sistema->addColumn($column_valor2);
        $this->datagrid_sistema->addColumn($column_tipo2);

        $column_dataMov2->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $format_value = function($value) {
            if (is_numeric($value)) {
                return number_format($value, 2, ',', '.');
            }
            return $value;
        };

        $column_valor2->setTransformer( $format_value );

        $this->datagrid_sistema->createModel();

        $sistema = new TVBox;
        $sistema->add($this->datagrid_sistema);

        $row = $this->form->addFields( [ new TLabel('Extrato do Banco'), $extrato ],
                                       [ new TLabel('Dados do Sistema'), $sistema ]);
        $row->layout = ['col-sm-6','col-sm-6'];

        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'ImportarOfxConciliacao'));
        $container->add($this->form);
        
        parent::add($container);

    }

    public function onReload($param = NULL)
    {
        try
        {
            
            $datainicio = TSession::getValue('datainicio');
            $datafim = TSession::getValue('datafim');

            $parametros = new stdClass;
            $parametros->datainicio = $datainicio;
            $parametros->datafim = $datafim;

            $this->onLoad($parametros);
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        $this->onLoad();
    }

    public function onLoad($param = null) {

        try {

            TTransaction::open('sample');

            $dados = ConciliacaoBancaria::where('unit_id', '=', 1)->first();

            if ($dados !== null) {
                $this->fill_database($param);
            }

            TTransaction::close();

        } catch (Exception $e) {

            TTransaction::rollback();
            new TMessage('error', 'Ocorreu um erro ao carregar os dados. Contate o suporte!');

        }

    }

    private function fill_database($param = null) {
        
        $criteria1 = new TCriteria;
        $criteria1->add(new TFilter('unit_id','=',1));
        $rep1 = new TRepository('ConciliacaoBancaria'); 
        $sistema = $rep1->load($criteria1);

        $extrato = ConciliacaoBancaria::where('unit_id', '=', 1)->load();
        foreach( $extrato as $item1 )
        {
            $row = $this->datagrid_arquivo->addItem( $item1 );
        }

        $criteria2 = new TCriteria;
        $criteria2->add(new TFilter('data_vencimento', 'BETWEEN', $param->datainicio, $param->datafim));
        $criteria2->add(new TFilter('unit_id','=',1));
        //echo $criteria2->dump();
        $par['order'] = 'data_vencimento';
        $par['data_vencimento'] = 'asc';
        $criteria2->setProperties($par);
        $rep2 = new TRepository('ConciliacaoBancariaSistema'); 
        $sistema = $rep2->load($criteria2);

        foreach( $sistema as $item2 )
        {
            $row = $this->datagrid_sistema->addItem( $item2 );
        }
    }

    function show()
    {
        $this->onReload();
        parent::show();
    }

}
