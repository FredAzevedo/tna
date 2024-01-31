<?php
/**
 * PagSeguroForm Form
 * @author  Fred Azv.
 */

use Adianti\Widget\Base\TElement;
use CWG\PagSeguro\PagSeguroAssinaturas;

class PagSeguroForm extends TPage
{
    function __construct()
    {
        
        parent::__construct();

        $email = "fredkeyster@hotmail.com";
        $token = "A2E71C3E86C7449B84FF3236C7E98623";
        $sandbox = PAGSEGURO_SANDBOX;

        $pagseguro = new PagSeguroAssinaturas($email, $token, $sandbox);

        //Sete apenas TRUE caso queira importa o Jquery também. Caso já possua, não precisa
        $js = $pagseguro->preparaCheckoutTransparente(true);

        $script = $js['completo'];
        //echo $js['completo']; //Importa todos os javascripts necessários

        $html1 = new THtmlRenderer('app/view/financeiro/pagseguro.html');
        
        $replace = array();
        $replace['script'] = $script;

        // replace the main section variables
        $html1->enableSection('main', $replace);
        
        $panel1 = new TPanelGroup('Olá! Você esta no Pagamento Recorrente com PagSeguro.');
        $panel1->add($html1);

        $vbox = TVBox::pack($panel1);
        $vbox->style = 'display:block; width: 100%';
        
        // add the template to the page
        parent::add( $vbox );

    }

    public static function onConsulta()
    {
        $email = "fredkeyster@hotmail.com";
        $token = "A2E71C3E86C7449B84FF3236C7E98623";
        $sandbox = PAGSEGURO_SANDBOX;
        
        $pagseguro = new PagSeguroAssinaturas($email, $token, $sandbox);

        //Caso seja uma notificação de uma assinatura (preApproval)
        if ($_POST['notificationType'] == 'preApproval') {
            $codigoNotificacao = $_POST['notificationCode']; //Recebe o código da notificação e busca as informações de como está a assinatura
            $response = $pagseguro->consultarNotificacao($codigoNotificacao);
            print_r($response); //Aqui é possível obter informações como se a assinatura está ativa ou não
        }
    }

    public static function onFinish($param) {

        $hash_cartao =  $param['hash'] ?? null;
        $token_cartao = $param['token'] ?? null;

        $email = "fredkeyster@hotmail.com";
        $token = "A2E71C3E86C7449B84FF3236C7E98623";
        $sandbox = PAGSEGURO_SANDBOX;

        $pagseguro = new PagSeguroAssinaturas($email, $token, $sandbox);

//Nome do comprador igual a como esta no CARTÂO
        $pagseguro->setNomeCliente("CARLOS W GAMA");
//Email do comprovador
        $pagseguro->setEmailCliente("c45325657310828827832@sandbox.pagseguro.com.br");
//Informa o telefone DD e número
        $pagseguro->setTelefone('11', '999999999');
//Informa o CPF
        $pagseguro->setCPF('11111111111');
//$pagseguro->setCNPJ('74345378000163'); //Ou CPNJ
//Informa o endereço RUA, NUMERO, COMPLEMENTO, BAIRRO, CIDADE, ESTADO, CEP
        $pagseguro->setEnderecoCliente('Rua C', '99', 'COMPLEMENTO', 'BAIRRO', 'São Paulo', 'SP', '57000000');
//Informa o ano de nascimento
        $pagseguro->setNascimentoCliente('01/01/1990');
//Infora o Hash  gerado na etapa anterior (assinando.php), é obrigatório para comunicação com checkoutr transparente
        $pagseguro->setHashCliente($hash_cartao);
//Informa o Token do Cartão de Crédito gerado na etapa anterior (assinando.php)
        $pagseguro->setTokenCartao($token_cartao);
//Código usado pelo vendedor para identificar qual é a compra
        $pagseguro->setReferencia("CWG004");
//Plano usado (Esse código é criado durante a criação do plano)
        $pagseguro->setPlanoCode('DAE2ADEE6969DF1FF4F60F9DC1859CF9');

        //B4635C246363F0322495AFB1422824A1

        try{
            $codigoAssinatura = $pagseguro->assinaPlano();
            echo 'O código unico da assinatura é: ' . $codigoAssinatura;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function onCriarPlano() {

        $email = "fredkeyster@hotmail.com";
        $token = "A2E71C3E86C7449B84FF3236C7E98623";
        $sandbox = PAGSEGURO_SANDBOX;

        $pagseguro = new PagSeguroAssinaturas($email, $token, $sandbox);

//Cria um nome para o plano
        $pagseguro->setReferencia('Plano_CWG_01');

//Cria uma descrição para o plano
        $pagseguro->setDescricao('Libera o acesso ao portal por 3 meses. A assinatura voltará a ser cobrada a cada 3 meses.');

//Valor a ser cobrado a cada renovação
        $pagseguro->setValor(30.00);

//De quanto em quanto tempo será realizado uma nova cobrança (MENSAL, BIMESTRAL, TRIMESTRAL, SEMESTRAL, ANUAL)
        $pagseguro->setPeriodicidade(PagSeguroAssinaturas::TRIMESTRAL);

//Após quanto tempo a assinatura irá expirar após a contratação = valor inteiro + (DAYS||MONTHS||YEARS). Exemplo, após 5 anos
        $pagseguro->setExpiracao(5, 'YEARS');

//=== Campos Opcionais ===//
//URL para redicionar a pessoa do portal PagSeguro para uma página de cancelamento no portal
        $pagseguro->setURLCancelamento('http://carloswgama.com.br/pagseguro/not/cancelando.php');

//Local para o comprador será redicionado após a compra com o código (code) identificador da assinatura
        $pagseguro->setRedirectURL('http://carloswgama.com.br/pagseguro/not/assinando.php');

//Máximo de pessoas que podem usar esse plano. Exemplo 10.000 pessoas podem usar esse plano
        $pagseguro->setMaximoUsuariosNoPlano(10000);

//=== Cria o plano ===//
        try {
            $codigoPlano = $pagseguro->criarPlano();
            echo "O Código do seu plano para realizar assinaturas é: " . $codigoPlano;
        } catch (Exception $e) {
            var_dump($e);
            echo "Erro: " . $e->getMessage();
        }
    }
}
