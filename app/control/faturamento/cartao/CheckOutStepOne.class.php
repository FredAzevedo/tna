<?php

use Adianti\Widget\Dialog\TToast;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TNumeric;

/**
 * CheckOutStepOne
 * @author  Fred Azv.
 */
class CheckOutStepOne extends TPage
{
    protected $form; // form
    protected $datagrid;

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct($param)
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_CheckOutStepOne');
        $this->form->addContent( ['<h4><b>Escolha o seu Plano</b></h4><hr>'] );

        TPage::include_css('app/resources/public.css');
    
        $this->setDatabase('sample');
        $this->setActiveRecord('Plano');
        $this->setDefaultOrder('id', 'asc');
        //$this->addFilterField('description', 'like', 'description'); // filter (filter field, operator, form field)
        $this->setLimit(100);

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_nome = new TDataGridColumn('nome', 'Descrição do Plano', 'left');
        $column_valor = new TDataGridColumn('valor', 'Preço', 'left');
        
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_valor);
        
        $format_value = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };
        
        $column_valor->setTransformer( $format_value );
        
        $column_id->setTransformer([$this, 'formatRow'] );
        
        $action1 = new TDataGridAction([$this, 'onSelect'], ['id' => '{id}', 'register_state' => 'false']);
        $this->datagrid->addAction($action1, 'Select', 'far:square fa-fw black');

        $column_id->setVisibility(false);

        $this->datagrid->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid);
        $panel->getBody()->style = 'overflow-x:auto';
        $panel->addFooter('<h4>VALOR TOTAL: <b id="valor"></b></h4>');
        //$panel->addHeaderActionLink( 'Show results', new TAction([$this, 'showResults']));
        $this->form->addContent( [$panel] );
        
        $pagestep = new TPageStep;
        $pagestep->addItem('Escolha o seu Plano');
        $pagestep->addItem('Dados Principais');
        $pagestep->addItem('Pagamento');   
        $pagestep->addItem('Confirmação');    
        $pagestep->style = 'margin-bottom: 2%; background-color: white;';

        $pagestep->select('Escolha o seu Plano');

        $row = $this->form->addAction( 'Próximo',  new TAction([$this, 'onNextPage'], ['register_state' => 'false']), 'fa:arrow-right white' );
        $row->class = 'btn btn-success';
        $row->layout = ['col-sm-12'];
        $row->style = 'float: right; margin-bottom: 2%;';

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($pagestep);
        $container->add($this->form);
        parent::add($container);

        TPage::include_js('app/resources/CheckOutStepOne.js');

    }

    public function onSelect($param)
    {
        
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        
        TTransaction::open('sample');
        $object = new Plano($param['id']); // load the object
        if (isset($selected_objects[$object->id]))
        {
            unset($selected_objects[$object->id]);
        }
        else
        {
            $selected_objects[$object->id] = $object->toArray(); // add the object inside the array
        }
        TSession::setValue(__CLASS__.'_selected_objects', $selected_objects); // put the array back to the session
        TTransaction::close();
        
        // reload datagrids
        $this->onReload( func_get_arg(0) );
    }

    public function formatRow($value, $object, $row)
    {
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        
        if ($selected_objects)
        {
            if (in_array( (int) $value, array_keys( $selected_objects ) ) )
            {
                $row->style = "background: #abdef9";
                
                $button = $row->find('i', ['class'=>'far fa-square fa-fw black'])[0];
                
                if ($button)
                {
                    $button->class = 'far fa-check-square fa-lg black';
                }
            }
        }
        self::showResults();
        
        return $value;
    }

    public function showResults()
    {
        
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
        //ksort($selected_objects);
        if ($selected_objects)
        {   
            $soma = 0.00;
            foreach ($selected_objects as $selected_object)
            {
                $soma += $selected_object['valor'];
            }
            TScript::create('$( "#valor" ).html(\'R$ ' . number_format($soma, 2, ',', '.') . '\');');
        }else{
            $soma = 0.00;
            TScript::create('$( "#valor" ).html(\'R$ ' . number_format($soma, 2, ',', '.') . '\');');
        }
        return $soma;
    }

    public function onNextPage($param) {

        $valor_total = new StdClass;
        $valor_total->valor_total = self::showResults();
        
        if($valor_total->valor_total > 0.00){


            $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');
            if($selected_objects){
                foreach($selected_objects as $tens){
                    $itens[] = $tens['nome']." ";
                }
            }
            TSession::setValue('valor_total', $valor_total);
            TSession::setValue('itens', $itens);
            TSession::setValue('form_two', $param);
            TApplication::loadPage('CheckOutStepTwo', 'onLoadStatic', ['register_state' => 'false']);

        }else{
            new TMessage('error', 'Escolha pelo menos um plano!');
        }
    }

    function onLoad($param = NULL)
    {
        TSession::setValue('unidade', $param['unit_id']);
        TSession::setValue('usuario', $param['user_id']);

        $data_one = TSession::getValue('form_one');
        //var_dump($data);
        if ($data_one) {
            $this->form->setData($data_one);
            return;
        }
        
        $data_two = TSession::getValue('form_two');
        if ($data_two) {
            $this->form->setData($data_two);
            return;
        }
    }

    function show()
    {
        $this->onReload();
        parent::show();
    }

}