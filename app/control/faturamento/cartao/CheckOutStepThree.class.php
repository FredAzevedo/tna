<?php

use Adianti\Widget\Dialog\TToast;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TNumeric;
use Adianti\Widget\Form\TText;

/**
 * CheckOutStepThree
 * @author  Fred Azv.
 */
class CheckOutStepThree extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    public function __construct($param)
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_CheckOutStepThree');
        $this->form->addContent( ['<h4><b>Pagamento</b></h4><hr>'] );

        TPage::include_css('app/resources/public.css');

        $bandeira = new TDBCombo('bandeira', 'sample', 'cartao', 'id', 'nome_cartao');
        $nome_cartao  = new TEntry('nome_cartao');
        $nome_cartao->onKeyUp = 'somenteLetras(this)';
        $nome_cartao->forceUpperCase();
        $numero_cartao  = new TEntry('numero_cartao');
        $mes_vencimento  = new TEntry('mes_vencimento');
        $mes_vencimento->setMask('99');
        $ano_vencimento  = new TEntry('ano_vencimento');
        $ano_vencimento->setMask('9999');
        $cpf_cnpj  = new TEntry('cpf_cnpj');
        $cpf_cnpj->onKeyUp = 'fwFormatarCpfCnpj(this)';
        $cpf_cnpj->onBlur = 'validaCpf(this,\'form_CheckOutStepThree\')';
        $email_cartao  = new TEntry('email_cartao');
        $celular_cartao  = new TEntry('celular_cartao');
        $codigo_cvv  = new TEntry('codigo_cvv');
        $codigo_cvv->setMask('999');

        $valor = new TNumeric('valor', 2, ',', '.', true);
        $valor->setEditable(FALSE);
        $pedido_numero = new TEntry('pedido_numero');
        $pedido_numero->setEditable(FALSE);

        $row = $this->form->addFields( [ new TLabel('Bandeira'), $bandeira ],
                                       [ new TLabel('Nome no <b>igual escrito no</b> cartão'), $nome_cartao ]
        );
        $row->layout = ['col-sm-3','col-sm-9'];

        $row = $this->form->addFields( [ new TLabel('Número do cartão'), $numero_cartao ],
                                       [ new TLabel('Mês vencimento'), $mes_vencimento ],
                                       [ new TLabel('Ano vencimento'), $ano_vencimento ],
                                       [ new TLabel('CVV'), $codigo_cvv ],
                                       [ new TLabel('CPF/CNPJ do Cartão'), $cpf_cnpj ]
        );
        $row->layout = ['col-sm-3','col-sm-2','col-sm-2','col-sm-1','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('E-mail do cartão'), $email_cartao ],
                                       [ new TLabel('Celular do cartão'), $celular_cartao ],
                                       [ new TLabel('Valor do Serviço'), $valor ],
                                       [ new TLabel('Código da Venda'), $pedido_numero ]
        );
        $row->layout = ['col-sm-5','col-sm-2','col-sm-2','col-sm-3'];

        //$row->style = '/* display: inline-block; *//* vertical-align:top; *//* float:left; *//* padding-right: 10px; */margin-top: 5%;';

        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade');
        $unit_id->setValue(TSession::getValue('userunitid'));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name');
        $user_id->setValue(TSession::getValue('userid'));
        $cliente_id = new TDBCombo('cliente_id','sample','Cliente','id','razao_social','razao_social');
        $cliente_id->setEditable(FALSE);
        $parcelas = '1';
        $descricao_pagamento = new TText('descricao_pagamento');
        $descricao_pagamento->setEditable(FALSE);
        

        $row = $this->form->addFields( [ new TLabel('Nome do Cliente'), $cliente_id ]
        );
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Descrição do Serviço'), $descricao_pagamento ]
        );
        $row->layout = ['col-sm-12'];

        $row->style = '/* display: inline-block; *//* vertical-align:top; *//* float:left; *//* padding-right: 10px; */margin-top: 5%;';        

        $pagestep = new TPageStep;
        $pagestep->addItem('Escolha o seu Plano');
        $pagestep->addItem('Dados Principais');
        $pagestep->addItem('Pagamento');        
        $pagestep->addItem('Confirmação');
        $pagestep->style = 'margin-bottom: 2%; background-color: white;';

        $pagestep->select('Pagamento');

        $row = $this->form->addAction( 'Finalizar',  new TAction([$this, 'onFinish'], ['register_state' => 'false']), 'fa:arrow-right white' );
        $row->class = 'btn btn-primary';
        $row->layout = ['col-sm-12'];
        $row->style = 'float: right; margin-bottom: 2%;';

        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', 'PessoaList'));
        $container->add($pagestep);
        $container->add($this->form);
        parent::add($container);

    }

    public function onFinish($param) {

        TTransaction::open('sample');

        $data = $this->form->getData();

        $unit_id = TSession::getValue('unidade');
        $user_id = TSession::getValue('usuario');
        $valor_total = TSession::getValue('valor_total');
        
        try
        { 
            //$credencialUnit = ApiIntegracao::where('unit_id','=',$unit_id)->first();
            //4012001037141112 05/2027 123
            $param['credencial'] = 'e0727263cc7a983f0aae5411ad86c5a144b8ed28';//$credencialUnit->credencial;
            $param['ambiente'] = 'https://sandbox.pjbank.com.br';//$credencialUnit->producao;
            $param['chave'] = 'e9db986de751de918ca19a1c377f0b7c313915f8';//$credencialUnit->chave;

            $api = PJBankApi::criarTokenCartao($param);
            $return = json_decode($api);
            $form = $this->form->getData();

            if($return->status == "201"){

                CartaoCliente::where('cliente_id', '=', $param['cliente_id'])->delete();

                $cartao = new CartaoCliente;
                $cartao->unit_id = $unit_id;
                $cartao->user_id = $user_id;
                $cartao->cartao_id = $param['bandeira'];
                $cartao->cliente_id = $param['cliente_id'];
                $cartao->mes_vencimento = $param['mes_vencimento'];
                $cartao->ano_vencimento = $param['ano_vencimento'];
                $cartao->token_cartao = $return->token_cartao;
                $cartao->cartao_truncado = $return->cartao_truncado;
                $cartao->msg = $return->msg;
                $cartao->status = $return->status;
                $cartao->store();
                
                if($return->token_cartao){

                    $param['unit_id'] = $unit_id;
                    $param['user_id'] = $user_id;
                    $param['token_cartao'] = $return->token_cartao;
                    $param['valor'] = $valor_total->valor_total;
                    $param['parcelas'] = '1';
                    $param['webhook'] = "https://macroerp.com.br/";

                    $sale = PJBankApi::transacaoTokenCartao($param);
                    $salereturn = json_decode($sale);
                    //var_dump($salereturn);
                    if($salereturn->statuscartao == '200'){

                        $cartaoApi = new CartaoApi();
                        $cartaoApi->unit_id = $unit_id;
                        $cartaoApi->user_id = $user_id;
                        $cartaoApi->cliente_id = $param['cliente_id'];
                        $cartaoApi->data_compra = date('Y-m-d');
                        $cartaoApi->descricao_pagamento = $param['descricao_pagamento'];
                        $cartaoApi->pedido_numero = $param['pedido_numero'];
                        $cartaoApi->status = $salereturn->status;
                        $cartaoApi->tid = $salereturn->tid;
                        $cartaoApi->tid_conciliacao = $salereturn->tid_conciliacao;
                        $datePre = explode("/",$salereturn->previsao_credito);//"07/24/2018",
                        $cartaoApi->previsao_credito = $datePre[2] . "" . $datePre[0] . "" . $datePre[1];
                        //$cartaoApi->{'codigo-token'} = $salereturn->{'codigo-token'};
                        $cartaoApi->bandeira = $salereturn->bandeira;
                        $cartaoApi->msg = $salereturn->msg;
                        $cartaoApi->statuscartao = $salereturn->statuscartao;
                        $cartaoApi->autorizacao = $salereturn->autorizacao;
                        $cartaoApi->cartao_truncado = $salereturn->cartao_truncado;
                        $dateV = explode("/",$salereturn->data_transacao);//"07/24/2018",
                        $data_transacao = $dateV[2] . "" . $dateV[0] . "" . $dateV[1];
                        $cartaoApi->data_transacao = $data_transacao;
                        $cartaoApi->hora_transacao = $salereturn->hora_transacao;
                        $cartaoApi->tarifa = $salereturn->tarifa;
                        $cartaoApi->taxa = $salereturn->taxa;
                        $cartaoApi->autorizada = $salereturn->autorizada;
                        $cartaoApi->parcelas = '1';
                        $cartaoApi->valor = $param['valor'];
                        $cartaoApi->valor_liquido = $salereturn->dados_parcela[0]->valor_liquido;
                        $cartaoApi->store();

                        TSession::setValue('msg', $salereturn->msg);
                        TSession::setValue('valor', $param['valor']);
                        TSession::setValue('pedido_numero', $param['pedido_numero']);
                        TSession::setValue('autorizacao', $salereturn->autorizacao);
                        TSession::setValue('descricao_pagamento', $param['descricao_pagamento']);

                        TApplication::loadPage('CheckOutStepFour', 'onLoad', ['register_state' => 'false']);

                    }elseif($salereturn->statuscartao == '501'){

                        $errorlog = new CartaoError();
                        $errorlog->msg = $salereturn->msg;
                        $errorlog->codigo = $salereturn->status;
                        $errorlog->cliente_id = $param['cliente_id'];
                        $errorlog->store();
                        new TMessage('error', 'Falha permantente, cartão vencido.');

                    }elseif($salereturn->statuscartao == '502'){

                        $errorlog = new CartaoError();
                        $errorlog->msg = $salereturn->msg;
                        $errorlog->codigo = $salereturn->status;
                        $errorlog->cliente_id = $param['cliente_id'];
                        $errorlog->store();
                        new TMessage('error', 'Falha permantente, não retentar.');

                    }elseif($salereturn->statuscartao == '503'){

                        $errorlog = new CartaoError();
                        $errorlog->msg = $salereturn->msg;
                        $errorlog->codigo = $salereturn->status;
                        $errorlog->cliente_id = $param['cliente_id'];
                        $errorlog->store();
                        new TMessage('error', 'Falha temporária, retentar até 1x ao dia.');

                    }elseif($salereturn->statuscartao == '504'){

                        $errorlog = new CartaoError();
                        $errorlog->msg = $salereturn->msg;
                        $errorlog->codigo = $salereturn->status;
                        $errorlog->cliente_id = $param['cliente_id'];
                        $errorlog->store();
                        new TMessage('error', 'Retentar imediatamente.');
                    
                    }else{


                    }
                    
                }else{

                    
                }

            }else{

                new TMessage('error', 'Status: '.$return->status.". Mensagem: ".$return->msg);

                $errorlog = new CartaoError();
                $errorlog->msg = $return->msg;
                $errorlog->codigo = $return->status;
                $errorlog->cliente_id = $param['cliente_id'];
                $errorlog->store();

                self::onLoad($param);
            }
            $this->form->setData($data);
            //self::onLoad($param);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        TTransaction::close();
    }

    function onLoad($param = NULL)
    {   
        //pegar os campos para atribuir valores vindo da session
        $data = $this->form->getData();

        $data_one    = TSession::getValue('form_one');
        $valor_total = TSession::getValue('valor_total');
        $itens = TSession::getValue('itens');

        if ($data_one) {

            $data->cliente_id = $data_one->cliente_id;
            $data->email_cartao = $data_one->email;
            $data->celular_cartao = $data_one->celular;
            $data->valor = $valor_total->valor_total;

            $items_plano = '';
            foreach($itens as $item){   
                $items_plano .= $item.". ";
            }
            $data->descricao_pagamento .= $items_plano;
            $data->pedido_numero = rand(0, 9999999999999);
            $this->form->setData($data);
        }
        return;
    }


    function show()
    {
        parent::show();
    }

}