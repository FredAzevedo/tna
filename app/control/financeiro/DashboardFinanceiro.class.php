<?php

class DashboardFinanceiro extends TPage
{
    private $form;
    private $html;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_ContaPagar');
        $this->form->setFormTitle('Dashboard de Contratos');
        $this->form->setFieldSizes('100%');

        $de = new TDate('de');
        $de->setDatabaseMask('yyyy-mm-dd');
        $de->setMask('dd/mm/yyyy');
        $ate = new TDate('ate');
        $ate->setDatabaseMask('yyyy-mm-dd');
        $ate->setMask('dd/mm/yyyy');
        
        $this->form->addAction('Filtar Período', new TAction(array($this, 'onReload')), 'fas:filter green');

        $row = $this->form->addFields( [ new TLabel('Da data'), $de ],
                                       [ new TLabel('Até a data'), $ate ]
        );
        $row->layout = ['col-sm-2','col-sm-2'];
        
        $this->form->addContent(['<div id="filtro-alerta"></div>']);

        $this->html = new THtmlRenderer('app/view/financeiro/dashboardContratos.html');
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml','DashboardContrato'));
        $vbox->add($this->form);
        $vbox->add($this->html);

        parent::add($vbox);
    }

    public function onReload($param = null)
    {

        $result1 = explode("/",$param['de']);
        $de = $result1[2].'-'.$result1[1].'-'.$result1[0];
        $result2 = explode("/",$param['ate']);
        $ate = $result2[2].'-'.$result2[1].'-'.$result2[0];

        if($param['de']){
            $alert = new TAlert('warning', "<b>O Filtro aplicado foi de: {$param['de']} até {$param['ate']}</b>.");
            TScript::create('$( "#filtro-alerta" ).html(\'' . $alert->getContents() . '\');');
        }

        $mes = new TCombo('mes');
        $options = [
            '01' => 'Janeiro', 
            '02' => 'Fevereiro', 
            '03' => 'Março',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro'
        ];
        $mes->addItems($options);

        try
        {
            TTransaction::open('sample');

            
            $month = date('m');
            $year = date('Y');

            //financeiro
            $totalReceitas = ClienteContrato::where('contrato_situacao_id','=','2')
            ->where('unit_id','=',$unitid)
            ->where('inicio_vigencia','>=',$de)
            ->where('inicio_vigencia','<=',$ate)
            ->count();
            $totalDespesas = ClienteContrato::where('contrato_situacao_id','=','1')
            ->where('unit_id','=',$unitid)
            ->where('inicio_vigencia','>=',$de)
            ->where('inicio_vigencia','<=',$ate)
            ->count();
            $totalInadiplencia = ClienteContrato::where('contrato_situacao_id','=','3')
            ->where('unit_id','=',$unitid)
            ->where('inicio_vigencia','>=',$de)
            ->where('inicio_vigencia','<=',$ate)
            ->count();
            $totalPrevisionado = ClienteContrato::where('contrato_situacao_id','IN',array('4','5'))
            ->where('unit_id','=',$unitid)
            ->where('inicio_vigencia','>=',$de)
            ->where('inicio_vigencia','<=',$ate)
            ->count();

            $contratoDependente = ContratoDependente::count();

            $chart1 = new THtmlRenderer('app/resources/google_pie_chart.html');
            $data1 = [];
            $data1[] = [ 'Contratos', 'Valores' ];
            
            $clicon = ClienteContrato::groupBy('contrato_situacao_id')
            ->where('inicio_vigencia','>=',$de)
            ->where('inicio_vigencia','<=',$ate)
            ->countBy('id', 'count');
            
            if ($clicon)
            {
                foreach ($clicon as $row)
                {
                    $data1[] = [ ContratoSituacao::find($row->contrato_situacao_id)->descricao, (int) $row->count];
                }
            }

            // replace the main section variables
            $chart1->enableSection('main', ['data'   => json_encode($data1),
                                            'width'  => '100%',
                                            'height'  => '300px',
                                            'title'  => 'Indicadores de Contratos',
                                            'ytitle' => 'Contratos', 
                                            'xtitle' => 'Valores',
                                            'is3D' => true,
                                            'uniqid' => uniqid()]);
            //boletos
            $boletosPagos = BoletoApi::where('valor_pago','<>',0.00)
            ->where('vencimento','>=',$de)
            ->where('vencimento','<=',$ate)
            ->where('unit_id','=',$unitid)
            ->count();

            $boletosPagosV = BoletoApi::where('valor_pago','<>',0.00)
            ->where('vencimento','>=',$de)
            ->where('vencimento','<=',$ate)
            ->where('unit_id','=',$unitid)
            ->load();

            if($boletosPagosV){
                $valor = 0.00;
                $taxa = 0.00;
                foreach($boletosPagosV as $pagos){
                    $valor = $valor + $pagos->valor_pago;
                    $taxa = $taxa + $pagos->valor_tarifa;
                }
            }
            $boletosPagosValor = $valor;
            $boletosPagosTaxa = $taxa;
            
            $boletosEmAbertos = BoletoApi::where('valor_pago','=',0.00)
            ->where('vencimento','>=',$de)
            ->where('vencimento','<=',$ate)
            ->where('unit_id','=',$unitid)
            ->count();

            $boletosEmAbertosV = BoletoApi::where('valor_pago','=',0.00)
            ->where('vencimento','>=',$de)
            ->where('vencimento','<=',$ate)
            ->where('unit_id','=',$unitid)
            ->load();

            if($boletosEmAbertosV){
                $valorAberto = 0.00;
                foreach($boletosEmAbertosV as $pagos){
                    $valorAberto = $valorAberto + $pagos->valor;
                }
            }
            $boletosEmAbertosValor = $valorAberto;
 
            $boletosEmitidos = BoletoApi::where('unit_id','=',1)
            ->where('vencimento','>=',$de)
            ->where('vencimento','<=',$ate)
            ->where('unit_id','=',$unitid)
            ->count();


            $boletosEmitidosV = BoletoApi::where('unit_id','=',1)
            ->where('vencimento','>=',$de)
            ->where('vencimento','<=',$ate)
            ->where('unit_id','=',$unitid)
            ->load();

            if($boletosEmitidosV){
                $valor = 0.00;
                foreach($boletosEmitidosV as $pagos){
                    $valor = $valor + $pagos->valor;
                }
            }
            $boletosEmitidosValor = $valor;
        


            $chart2 = new THtmlRenderer('app/resources/google_bar_chart.html');
            $data2 = [];
        
            $data2 = [

                '0' => [
                    '0' => 'Boletos',
                    '1' => 'Quantidade'
                ],
                '1' => [
                    '0' => 'Boletos Emitidos',
                    '1' => (int)$boletosEmitidos
                ],
                '2' => [
                    '0' => 'Boletos em Abertos',
                    '1' => (int)$boletosEmAbertos
                ],
                '3' => [
                    '0' => 'Boletos Pagos',
                    '1' => (int)$boletosPagos
                ]
            ];

            // replace the main section variables
            $chart2->enableSection('main', ['data'   => json_encode($data2),
                                            'width'  => '100%',
                                            'height'  => '300px',
                                            'title'  => 'Indicadores de Boleto',
                                            'ytitle' => 'Boletos', 
                                            'xtitle' => 'Valores',
                                            'uniqid' => uniqid()]);

            //vencimento de cartões de crédito
            $conn = TTransaction::get();

            $mes_atual = date('m');
            $ano_atual = date('Y');
            $timestamp = strtotime("+1 month");
            $mes_proximo = date('m',$timestamp);

            $sth = $conn->prepare("SELECT c.razao_social as cliente, 
            (SELECT tc.telefone FROM telefones_cliente tc WHERE tc.cliente_id = c.id group by tc.cliente_id LIMIT 1) as telefone,
            cc2.id as contrato, cc.mes_vencimento, cc.ano_vencimento
            FROM cartao_cliente cc
            INNER JOIN cliente c on (c.id = cc.cliente_id)
            INNER JOIN cliente_contrato cc2 on (cc2.cliente_id = c.id)
            WHERE cc.mes_vencimento IN (?,?) AND cc.ano_vencimento = ?");

            $sth->execute([$mes_atual,$mes_proximo,$ano_atual]);
            $result = $sth->fetchAll();

            foreach ($result as $row) 
            { 
                $cliente = $row['cliente'];
                $telefone = $row['telefone'];
                $contrato = $row['contrato'];
                $mes_vencimento = $row['mes_vencimento'];
                $ano_vencimento = $row['ano_vencimento'];

                $arrayReplace[] = ['cliente'=>$cliente, 'telefone'=>$telefone, 'contrato'=>$contrato, 'mes_vencimento'=>$mes_vencimento, 'ano_vencimento'=>$ano_vencimento ];
            } 
 
            $replaces = [];
            $replaces['totalReceitas']      = $totalReceitas;
            $replaces['totalDespesas']      = $totalDespesas;
            $replaces['totalInadiplencia']   = $totalInadiplencia;
            $replaces['totalPrevisionado']  = $totalPrevisionado;
            $replaces['contratoDependente'] = $contratoDependente;
            $replaces['boletosEmitidos']    = $boletosEmitidos;
            $replaces['boletosPagos']       = $boletosPagos;
            $replaces['boletosEmAbertos']   = $boletosEmAbertos;
            $replaces['boletosPagosValor']  = $boletosPagosValor;
            $replaces['boletosPagosTaxa']  = $boletosPagosTaxa;
            $replaces['boletosEmitidosValor'] = $boletosEmitidosValor;
            $replaces['boletosEmAbertosValor'] = $boletosEmAbertosValor;
            $replaces['chart1']             = $chart1;
            $replaces['chart2']             = $chart2;
            $replaces['mes'] = $mes;

            if(!empty($arrayReplace))
            {
                $replaces['CARTOES'] = $arrayReplace;
            }

            $this->html->enableSection('main',$replaces);
            

            TTransaction::close();
         }
         catch (Exception $e)
         {
             new TMessage('error', $e->getMessage());
         }
    }

    function show()
    {
        $this->onReload();
        parent::show();
    }

}
