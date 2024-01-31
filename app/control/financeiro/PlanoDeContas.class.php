<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;

class PlanoDeContas extends TPage
{
    private $html;
    private $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_plano_contas');
        $this->form->setFormTitle('Plano de Contas do Financeiro');
        $this->form->setFieldSizes('100%');

        $id_unit = TSession::getValue('userunitid');
       
        //Dados de Receitas
        try
        {
            if(isset($id_unit))
            {
                TTransaction::open('permission');

                $conm = TTransaction::get();
                $dadosReceita = $conm->query("SELECT id, nivel1, nivel2, nivel3, nivel4, nome FROM pc_receita");

                TTransaction::close();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        // Dados de Despesas
        try
        {
            if(isset($id_unit))
            {
                TTransaction::open('permission');

                $conm = TTransaction::get();
                $dadosDespesa = $conm->query("SELECT id, nivel1, nivel2, nivel3, nivel4, nome FROM pc_despesa");

                TTransaction::close();
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }

        //Arvore de Receitas
        $dataReceitas = [];
        foreach ($dadosReceita as $valor) {
            $id = $valor["id"];

            if (isset($valor["nivel1"]) && isset($valor["nivel2"]) && isset($valor["nivel3"]) && isset($valor["nivel4"])) {
                $dataReceitas[$valor["nivel1"]][$valor["nivel2"]][$valor["nivel3"]][$valor["nivel4"]][$id] = $valor["nome"];
            }

            elseif (isset($valor["nivel1"]) && isset($valor["nivel2"]) && isset($valor["nivel3"])) {
                $dataReceitas[$valor["nivel1"]][$valor["nivel2"]][$valor["nivel3"]][$id] = $valor["nome"];
            }
            elseif (isset($valor["nivel1"]) && isset($valor["nivel2"])) {
                $dataReceitas[$valor["nivel1"]][$valor["nivel2"]][$id] = $valor["nome"];
            }
            elseif (isset($valor["nivel1"])) {
                $dataReceitas[$valor["nivel1"]][$id] = $valor["nome"];
            }

        }

        //Arvore de Despesas
        $dataDespesas = [];
        foreach ($dadosDespesa as $valor) {
            $id = $valor["id"];

            if (isset($valor["nivel1"]) && isset($valor["nivel2"]) && isset($valor["nivel3"]) && isset($valor["nivel4"])) {
                $dataDespesas[$valor["nivel1"]][$valor["nivel2"]][$valor["nivel3"]][$valor["nivel4"]][$id] = $valor["nome"];
            }

            elseif (isset($valor["nivel1"]) && isset($valor["nivel2"]) && isset($valor["nivel3"])) {
                $dataDespesas[$valor["nivel1"]][$valor["nivel2"]][$valor["nivel3"]][$id] = $valor["nome"];
            }
            elseif (isset($valor["nivel1"]) && isset($valor["nivel2"])) {
                $dataDespesas[$valor["nivel1"]][$valor["nivel2"]][$id] = $valor["nome"];
            }
            elseif (isset($valor["nivel1"])) {
                $dataDespesas[$valor["nivel1"]][$id] = $valor["nome"];
            }

        }


        // scroll around the treeview receitas e despesas
        $scrollReceitas = new TScroll;
        $scrollDespesas = new TScroll;
        //$scroll->setSize(300, 400);

        // creates the treeview
        $receitas = new TTreeView;
        $receitas->setSize('100%'); 
        $receitas->setItemIcon('ico_file.png');
        $receitas->style = 'border: none';
        $receitas->setItemAction(new TAction(array($this, 'onSelectReceitas')));
        $receitas->fromArray($dataReceitas);
        $scrollReceitas->add($receitas);

        // creates the treeview
        $despesas = new TTreeView;
        $despesas->setSize('100%');
        $despesas->setItemIcon('ico_file.png');
        $despesas->style = 'border: none';
        $despesas->setItemAction(new TAction(array($this, 'onSelectDespesas')));
        $despesas->fromArray($dataDespesas);
        $scrollDespesas->add($despesas);

        /*$row = $this->form->addFields( [ new TLabel('RECEITAS'), $scrollReceitas ],
                                       [ new TLabel('DESPESAS'), $scrollDespesas ]
        );
        $row->layout = ['col-md','col-md'];*/

        $row = $this->form->addFields( [ new TLabel('RECEITAS'), $scrollReceitas ]);
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields([ new TLabel('DESPESAS'), $scrollDespesas ]);
        $row->layout = ['col-sm-12'];

        $this->form->addAction('Nova Receita',  new TAction([$this, 'onSelectReceitas']), 'fa:plus blue');
        $this->form->addAction('Nova Despesa',  new TAction([$this, 'onSelectDespesas']), 'fa:plus red');

        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'PlanoDeContas'));
        //$this->form->add($this->html);
        $container->add($this->form);

        parent::add($container); 

    }

    public static function onSelectReceitas($param)
    {
        
        try
        {
            if(isset($param['key'])){
                $obj = new StdClass;
                $obj->key = $param['key']; // get node key (index)
                //$obj->value = $param['value']; // get node value (contend)
                AdiantiCoreApplication::loadPage('PcReceitaForm', 'onEdit', array('key' => $obj->key,'id' => $obj->key));
            }else{

                AdiantiCoreApplication::loadPage('PcReceitaForm');
            }

        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onSelectDespesas($param)
    {
        try
        {
            if(isset($param['key'])){
                $obj = new StdClass;
                $obj->key = $param['key']; // get node key (index)
                AdiantiCoreApplication::loadPage('PcDespesaForm', 'onEdit', array('key' => $obj->key,'id' => $obj->key));
            }else{

                AdiantiCoreApplication::loadPage('PcDespesaForm');
            }
        /*$win = TWindow::create('Despesas', 0.6, 0.8);
        $win->add( '<pre>'.str_replace("\n", '<br>', print_r($param, true) ).'</pre>'  );
        $win->show();*/
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }

    }

    public static function onReload(){

    }

    function show()
    {
        $this->onReload();
        parent::show();
    }

}