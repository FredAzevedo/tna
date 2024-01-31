<?php

class DashboardFinanceiroView extends TPage
{

    private $form;
    private $html;

    public function __construct()
    {
        parent::__construct();
        
        //TPage::include_css('app/resources/catalog.css');
        $this->html = new THtmlRenderer('app/view/financeiro/dashboardFinanceiro.html');

        $this->form = new BootstrapFormBuilder('form_ContaPagar');
        $this->form->setFormTitle('Filtro do Dashboard Financeiro');
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

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml','DashboardFinanceiroView'));
        $vbox->add($this->form);
        $vbox->add($this->html);

        parent::add($vbox);
    }

    public function onReload($param = null)
    {
        try
        {

            if($param){
                $result1 = explode("/",$param['de']);
                $de = $result1[2].'-'.$result1[1].'-'.$result1[0];
                $result2 = explode("/",$param['ate']);
                $ate = $result2[2].'-'.$result2[1].'-'.$result2[0];
            }
            
            if(isset($param['de'])){

                $alert = new TAlert('warning', "<b>O Filtro aplicado foi de: {$param['de']} até {$param['ate']}</b>.");
                TScript::create('$( "#filtro-alerta" ).html(\'' . $alert->getContents() . '\');');
                
            }else{

                $de = date('Y-m-01');
                $ate = date('Y-m-t');
            }
            
            
            $unitid = TSession::getValue('userunitid');

            TTransaction::open('sample');

            //financeiro
            $totalReceitas = MovimentacaoBancaria::where('deleted_at','is', null)
            ->where('unit_id','=',$unitid)
            ->where('data_baixa','>=',$de)
            ->where('data_baixa','<=',$ate)
            ->where('tipo','=','1')
            ->load();

            $valorReceita = 0.00;
            if($totalReceitas){
                foreach($totalReceitas as $item){
                    $valorReceita = $valorReceita + $item->valor_movimentacao;
                }
            }
            $totalReceitasMonetario = $valorReceita;

            $totalDespesas = MovimentacaoBancaria::where('deleted_at','is', null)
            ->where('unit_id','=',$unitid)
            ->where('data_baixa','>=',$de)
            ->where('data_baixa','<=',$ate)
            ->where('tipo','=','0')
            ->load();

            $valorDespesa = 0.00;
            if($totalDespesas){
                foreach($totalDespesas as $item){
                    $valorDespesa = $valorDespesa + $item->valor_movimentacao;
                }
            }
            $totalDespesasMonetario = $valorDespesa;
            

            $chart1 = new THtmlRenderer('app/resources/google_pie_chart.html');
            $data1 = [];
        
            $data1 = [

                '0' => [
                    '0' => 'Receitas',
                    '1' => 'Despesas'
                ],
                '1' => [
                    '0' => 'Receitas',
                    '1' => (int)$totalReceitasMonetario
                ],
                '2' => [
                    '0' => 'Despesas',
                    '1' => (int)$totalDespesasMonetario
                ]
            ];

            // replace the main section variables
            $chart1->enableSection('main', ['data'   => json_encode($data1),
                                            'width'  => '100%',
                                            'height'  => '300px',
                                            'title'  => 'Razão entre Receitas e Despesa',
                                            'ytitle' => 'Contratos', 
                                            'xtitle' => 'Valores',
                                            'is3D' => true,
                                            'uniqid' => uniqid()]);


            $totalReceitasNfse = ContaReceber::where('deleted_at','is', null)
            ->where('unit_id','=',$unitid)
            ->where('data_baixa','>=',$de)
            ->where('data_baixa','<=',$ate)
            ->where('gera_nfse','=','S')
            ->where('baixa','=','S')
            ->load();

            $valorReceitaNfse = 0.00;
            if($totalReceitasNfse){
                foreach($totalReceitasNfse as $item){
                    $valorReceitaNfse = $valorReceitaNfse + $item->valor_pago;
                }
            }
            $totalReceitasMonetarioNfse = $valorReceitaNfse;

            $totalPrevisionadoReceitaNfse = ContaReceber::where('deleted_at','is', null)
            ->where('unit_id','=',$unitid)
            ->where('data_vencimento','>=',$de)
            ->where('data_vencimento','<=',$ate)
            ->where('baixa','=','N')
            ->where('gera_nfse','=','S')
            ->load();

            $valorReceitaPrevNfse = 0.00;
            if($totalPrevisionadoReceitaNfse){
                foreach($totalPrevisionadoReceitaNfse as $item){
                    $valorReceitaPrevNfse = $valorReceitaPrevNfse + $item->valor;
                }
            }
            $totalPrevisionadoReceitaMonetarioNfse = $valorReceitaPrevNfse;
          
            
            $hj = date('Y-m-d');
            if($de < $hj){
                $ini = $hj;
            }else{
                $ini = $de;
            }
        
            $totalPrevisionadoReceita = ContaReceber::where('deleted_at','is', null)
            ->where('unit_id','=',$unitid)
            ->where('data_vencimento','>=',$ini)
            ->where('data_vencimento','<=',$ate)
            ->where('baixa','=','N')
            ->load();

            $valorReceitaPrev = 0.00;
            if($totalPrevisionadoReceita){
                foreach($totalPrevisionadoReceita as $item){
                    $valorReceitaPrev = $valorReceitaPrev + $item->valor;
                }
            }
            $totalPrevisionadoReceitaMonetario = $valorReceitaPrev;

            $totalPrevisionadoDespesa = ContaPagar::where('deleted_at','is', null)
            ->where('unit_id','=',$unitid)
            ->where('data_vencimento','>=',$ini)
            ->where('data_vencimento','<=',$ate)
            ->where('baixa','=','N')
            ->load();

            $valorDespesaPrev = 0.00;
            if($totalPrevisionadoDespesa){
                foreach($totalPrevisionadoDespesa as $item){
                    $valorDespesaPrev = $valorDespesaPrev + $item->valor;
                }
            }
            $totalPrevisionadoDespesaMonetario = $valorDespesaPrev;


            $chart2 = new THtmlRenderer('app/resources/google_bar_chart.html');
            $data2 = [];
        
            $data2 = [

                '0' => [
                    '0' => 'Razão',
                    '1' => 'Valor'
                ],
                '1' => [
                    '0' => 'Receitas',
                    '1' => (int)$totalPrevisionadoReceitaMonetario
                ],
                '2' => [
                    '0' => 'Despesas',
                    '1' => (int)$totalPrevisionadoDespesaMonetario
                ]
            ];
            // replace the main section variables
            $chart2->enableSection('main', ['data'   => json_encode($data2),
                                            'width'  => '100%',
                                            'height'  => '300px',
                                            'title'  => 'Razão de Previsionamentos Receitas e Despesas',
                                            'ytitle' => 'Boletos', 
                                            'xtitle' => 'Valores',
                                            'uniqid' => uniqid()]);

            
            $conn = TTransaction::get();
            //total do mes em monetário
            //$sthVM = $conn->prepare("SELECT SUM(valor) as valor_mes FROM conta_receber  WHERE MONTH(data_vencimento) = MONTH(NOW()) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ?"); 
            $sthVM = $conn->prepare("SELECT SUM(valor) as valor_mes FROM conta_receber  WHERE MONTH(data_vencimento) = MONTH(NOW()) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? AND gera_nfse = 'S'"); 

            $sthVM->execute([TSession::getValue('userunitid')]);
            $result1 = $sthVM->fetchAll();

            foreach ($result1 as $row1) 
            { 
                $valorMes = $row1['valor_mes'];
            } 


            //total do mes em monetário
            //$sthContMes = $conn->prepare("SELECT COUNT(valor) as valor_mes FROM conta_receber  WHERE MONTH(data_vencimento) = MONTH(NOW()) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 
            $sthContMes = $conn->prepare("SELECT COUNT(valor) as valor_mes FROM conta_receber  WHERE MONTH(data_vencimento) = MONTH(NOW()) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? AND gera_nfse = 'S'"); 
            $sthContMes->execute([TSession::getValue('userunitid')]);
            $result2 = $sthContMes->fetchAll();

            foreach ($result2 as $row2) 
            { 
                $contadorMes = $row2['valor_mes'];
            } 

            //Select em conta a receber do dia
            $sthVD = $conn->prepare("SELECT SUM( valor ) as valor_dia FROM conta_receber WHERE data_vencimento = CURDATE( ) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthVD->execute([TSession::getValue('userunitid')]);
            $result3 = $sthVD->fetchAll();

            foreach ($result3 as $row3) 
            { 
                $valorDia = $row3['valor_dia'];
            }

            $sthContDia = $conn->prepare("SELECT COUNT( valor ) as valor_dia FROM conta_receber WHERE data_vencimento = CURDATE( ) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthContDia->execute([TSession::getValue('userunitid')]);
            $result4 = $sthContDia->fetchAll();

            foreach ($result4 as $row4) 
            { 
                $contadorDia = $row4['valor_dia'];
            }


            //Select em conta a receber inadiplentes
            $sthVI = $conn->prepare("SELECT SUM(valor) as valor FROM  `conta_receber` 
            WHERE data_vencimento < CURDATE( ) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? AND gera_nfse = 'S'"); 

            $sthVI->execute([TSession::getValue('userunitid')]);
            $result5 = $sthVI->fetchAll();

            foreach ($result5 as $row5) 
            { 
                $valorInadiplencia = $row5['valor'];
            }

            $sthContInadiplencia = $conn->prepare("SELECT COUNT(valor) as valor FROM  conta_receber 
            WHERE data_vencimento < CURDATE( ) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? AND gera_nfse = 'S'"); 

            $sthContInadiplencia->execute([TSession::getValue('userunitid')]);
            $result6 = $sthContInadiplencia->fetchAll();

            foreach ($result6 as $row6) 
            { 
                $contadorInadiplencia = $row6['valor'];
            }

            // movimentações de despesas

            //Select em conta a pagar do mês
            $sthDespMes = $conn->prepare("SELECT SUM( valor ) as despesa_mes FROM conta_pagar WHERE MONTH(data_vencimento) = MONTH(NOW()) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthDespMes->execute([TSession::getValue('userunitid')]);
            $resultD1 = $sthDespMes->fetchAll();

            foreach ($resultD1 as $rowD1) 
            { 
                $valorCPmes = $rowD1['despesa_mes'];
            }

            $sthContCPmes = $conn->prepare("SELECT COUNT( valor ) as despesa_mes  FROM conta_pagar WHERE MONTH(data_vencimento) = MONTH(NOW()) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthContCPmes->execute([TSession::getValue('userunitid')]);
            $resultCP1 = $sthContCPmes->fetchAll();

            foreach ($resultCP1 as $rowD2) 
            { 
                $contadorCPmes = $rowD2['despesa_mes'];
            }

            //Select em conta a pagar do dia
            $sthDespDia = $conn->prepare("SELECT SUM( valor ) as valor FROM conta_pagar WHERE data_vencimento = CURDATE( ) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthDespDia->execute([TSession::getValue('userunitid')]);
            $resultD3 = $sthDespDia->fetchAll();

            foreach ($resultD3 as $rowD3) 
            { 
                $valorCPdia = $rowD3['valor'];
            }

            //Select em conta a pagar a QTD do dia
            $sthContCPdia = $conn->prepare("SELECT COUNT( valor ) as valor FROM conta_pagar WHERE data_vencimento = CURDATE( ) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthContCPdia->execute([TSession::getValue('userunitid')]);
            $resultCP2 = $sthContCPdia->fetchAll();

            foreach ($resultCP2 as $rowD4) 
            { 
                $contadorCPdia = $rowD4['valor'];
            }

            //Select em conta a receber para trazer valores atrazados
            $sthDespAtrasadas = $conn->prepare("SELECT SUM(valor) as valor FROM conta_pagar WHERE data_vencimento < CURDATE( ) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthDespAtrasadas->execute([TSession::getValue('userunitid')]);
            $resultD5 = $sthDespAtrasadas->fetchAll();

            foreach ($resultD5 as $rowD5) 
            { 
                $valorCPAtrasadas = $rowD5['valor'];
            }
            //Select em conta a pagar para trazer QTD de valores atrazados
            $sthContCPAtrasadas = $conn->prepare("SELECT COUNT(valor) as valor FROM  conta_pagar WHERE data_vencimento < CURDATE( ) AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthContCPAtrasadas->execute([TSession::getValue('userunitid')]);
            $resultCP6 = $sthContCPAtrasadas->fetchAll();

            foreach ($resultCP6 as $rowD6) 
            { 
                $contadorCPAtrasadas = $rowD6['valor'];
            }

            //Select em conta a receber jurídico
            $sthVJ = $conn->prepare("SELECT SUM(valor) as valor FROM  `conta_receber` 
            WHERE juridico = 'S' AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthVJ->execute([TSession::getValue('userunitid')]);
            $resultJ = $sthVJ->fetchAll();

            foreach ($resultJ as $row) 
            { 
                $valorJuridico = $row['valor'];
            }

            $sthContJur = $conn->prepare("SELECT COUNT(valor) as valor FROM conta_receber 
            WHERE juridico = 'S' AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthContJur->execute([TSession::getValue('userunitid')]);
            $resultJur = $sthContJur->fetchAll();

            foreach ($resultJur as $jur) 
            { 
                $contadorJur = $jur['valor'];
            } 

            //Select em conta a receber previsão boleto / cartão
            $sthPre = $conn->prepare("SELECT SUM(valor) as valor FROM  `conta_receber` 
            WHERE previsao > now() AND baixa = 'N' AND deleted_at IS NULL AND previsao IS NOT NULL AND unit_id = ? "); 

            $sthPre->execute([TSession::getValue('userunitid')]);
            $resultPre = $sthPre->fetchAll();

            foreach ($resultPre as $row) 
            { 
                $valorPrevisaoBoletoCartao = $row['valor'];
            }

            $sthContPre = $conn->prepare("SELECT COUNT(valor) as valor FROM conta_receber 
            WHERE previsao > now() AND baixa = 'N' AND deleted_at IS NULL AND previsao IS NOT NULL AND unit_id = ? "); 

            $sthContPre->execute([TSession::getValue('userunitid')]);
            $resultPre = $sthContPre->fetchAll();

            foreach ($resultPre as $pre) 
            { 
                $contadorPre = $pre['valor'];
            } 


            //Select em conta a receber das notas a serem emitidas
            $sthNfse = $conn->prepare("SELECT SUM(valor) as valor FROM  `conta_receber` 
            WHERE gera_nfse = 'N' AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthNfse->execute([TSession::getValue('userunitid')]);
            $resultNfseSum = $sthNfse->fetchAll();

            foreach ($resultNfseSum as $row) 
            { 
                $valorNotasaEmitir = $row['valor'];
            }

            $sthContNfse = $conn->prepare("SELECT COUNT(valor) as valor FROM conta_receber 
            WHERE gera_nfse = 'N' AND baixa = 'N' AND deleted_at IS NULL AND unit_id = ? "); 

            $sthContNfse->execute([TSession::getValue('userunitid')]);
            $resultPreNfse = $sthContNfse->fetchAll();

            foreach ($resultPreNfse as $pre) 
            { 
                $contadorNFse = $pre['valor'];
            } 

            //adicionando filtros
            $replaces = [];
            $replaces['totalReceitas']      = number_format($totalReceitasMonetario, 2, ',','.');
            $replaces['totalDespesas']      = number_format($totalDespesasMonetario, 2, ',','.');
            $replaces['totalPrevisionadoReceita']  = number_format($totalPrevisionadoReceitaMonetario, 2, ',','.');
            $replaces['totalPrevisionadoDespesa']  = number_format($totalPrevisionadoDespesaMonetario, 2, ',','.');
            $replaces['chart1']             = $chart1;
            $replaces['chart2']             = $chart2;

            $replaces['totalReceitasNfse']      = number_format($totalReceitasMonetarioNfse, 2, ',','.');
            $replaces['totalPrevisionadoReceitaNfse']  = number_format($totalPrevisionadoReceitaMonetarioNfse, 2, ',','.');
            //adicionando os estáticos
            $replaces['valorMes'] = number_format($valorMes, 2, ',','.');
            $replaces['valorDia'] = number_format($valorDia, 2, ',','.');
            $replaces['contadorMes'] = $contadorMes;
            $replaces['contadorDia'] = $contadorDia;
            $replaces['valorInadiplencia'] = number_format($valorInadiplencia, 2, ',','.');
            $replaces['contadorInadiplencia'] = $contadorInadiplencia;
            $replaces['valorCPmes'] = number_format($valorCPmes, 2, ',','.');
            $replaces['contadorCPmes'] = $contadorCPmes;
            $replaces['valorCPdia'] = number_format($valorCPdia, 2, ',','.');
            $replaces['contadorCPdia'] = $contadorCPdia;
            $replaces['valorCPAtrasadas'] = number_format($valorCPAtrasadas, 2, ',','.');
            $replaces['contadorCPAtrasadas'] = $contadorCPAtrasadas;
            $replaces['contadorJur']  = $contadorJur;
            $replaces['valorJuridico']  = number_format($valorJuridico, 2, ',','.');
            $replaces['contadorPre']  = $contadorPre;
            $replaces['valorPrevisaoBoletoCartao']  = number_format($valorPrevisaoBoletoCartao, 2, ',','.');
            $replaces['contadorNFse']  = $contadorNFse;
            $replaces['valorNotasaEmitir']  = number_format($valorNotasaEmitir, 2, ',','.');
            
            $contas = ContaBancaria::where('banco_id','is not',null)->load();
            $count = 0.00;
            
            $dt_mes_fim = date('Y-m-t');

            foreach($contas as $conta){
                
                $total = 0.00;
                $receita = 0.00;
                $despesa = 0.00;
                
                $receita = MovimentacaoBancaria::where('conta_bancaria_id','=',$conta->id)
                ->where('data_baixa', '>', '2000-01-01')
                ->where('data_baixa', '<', $dt_mes_fim)
                ->where('tipo','=',1)
                ->where('data_baixa','is not',null)
                ->where('deleted_at','is',null)
                ->sumBy('valor_movimentacao');
                
                $despesa = MovimentacaoBancaria::where('conta_bancaria_id','=',$conta->id)
                ->where('data_baixa', '>', '2000-01-01')
                ->where('data_baixa', '<', $dt_mes_fim)
                ->where('tipo','=',0)
                ->where('data_baixa','is not',null)
                ->where('deleted_at','is',null)
                ->sumBy('valor_movimentacao');
            
                $total = $receita - $despesa;

                $banco = $conta->banco->nome_banco;
                $value = number_format($total,2,',','.');
                $count = $count = $value;
                
                $arrayDeContas[] = ['banco'=>$banco, 'value'=>$value ];
            }

            if(!empty($arrayDeContas))
            {
            $replaces['SALDOS'] = $arrayDeContas;
            }

            $countSaldototais = floatval($count);
            $replaces['countSaldototais'] = number_format($countSaldototais,2,',','.');
            

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
