<?php

use TecnoSpeed\Plugnotas\Common\Endereco;
use TecnoSpeed\Plugnotas\Common\Telefone;
use TecnoSpeed\Plugnotas\Common\ValorAliquota;
use TecnoSpeed\Plugnotas\Configuration;
use TecnoSpeed\Plugnotas\Nfse;
use TecnoSpeed\Plugnotas\Nfse\CidadePrestacao;
use TecnoSpeed\Plugnotas\Nfse\Impressao;
use TecnoSpeed\Plugnotas\Nfse\Prestador;
use TecnoSpeed\Plugnotas\Nfse\Rps;
use TecnoSpeed\Plugnotas\Nfse\Servico;
use TecnoSpeed\Plugnotas\Nfse\Servico\Deducao;
use TecnoSpeed\Plugnotas\Nfse\Servico\Evento;
use TecnoSpeed\Plugnotas\Nfse\Servico\Iss;
use TecnoSpeed\Plugnotas\Nfse\Servico\Obra;
use TecnoSpeed\Plugnotas\Nfse\Servico\Retencao;
use TecnoSpeed\Plugnotas\Nfse\Servico\Valor;
use TecnoSpeed\Plugnotas\Nfse\Tomador;
use TecnoSpeed\Plugnotas\Error\RequiredError;
use TecnoSpeed\Plugnotas\Error\ValidationError;

use TecnoSpeed\Plugnotas\Builders\NfseBuilder;
use TecnoSpeed\Plugnotas\Communication\CallApi;

class testeNfse extends TPage
{
    protected $form;
    
    public function __construct( $param )
    {
        parent::__construct();

        /*try {

		    $nfse = (new NfseBuilder)
	        ->withPrestador([
	            'cpfCnpj' => '15.581.977/0001-17',
	            'inscricaoMunicipal' => '123456',
	            'razaoSocial' => 'WIZARD SYSTEM TECNOLOGIA DA INFORMAÇÃO ME',
	            'simplesNacional' => '0',
	            'endereco' => [
	                'logradouro' => 'Rua Brasilia',
	                'numero' => '656',
	                'codigoCidade' => '2408102',
	                'cep' => '59.030-060'
	            ]
	        ])
	        ->withTomador([
	            'cpfCnpj' => '051.318.884-33',
	            'razaoSocial' => 'Frederick kesyeter C Azevedo',
	            'email' => 'fred@macroerp.com.br'
	        ])
	        ->withServico([
	            'codigo' => '1.02',
	            //'idIntegracao' => '1',
	            'discriminacao' => 'Exemplo',
	            'cnae' => '4751201',
	            'iss' => [
	                'aliquota' => '3',
	                'tipoTributacao' => '1',
	                'retido' => '0'
	            ],
	            'valor' => [
	                'servico' => 1500.03
	            ]
	        ])
	        ->build([]);

	    $nfse->validate();

	    $configuration = new Configuration();
	    $response = $nfse->send($configuration);

	    var_dump($response);*/

	    $configuration = new Configuration(
	        Configuration::TYPE_ENVIRONMENT_PRODUCTION, // Ambiente a ser enviada a requisição
	        '31ad662e-2b94-4fc8-82ec-0bdff5cd246b' // API-Key
		);

	    //Download da NFe
	    /*$configuration->setNfseDownloadDirectory('app/control/nfse/notas/');
	    $nfse = new Nfse();
	    $nfse->setConfiguration($configuration);
	    $download = $nfse->downloadPdf('5d4dbb42ec534de349b0d386');
	    var_dump($download);*/

	    //CONSULTAR A NFSE PELO PROTOCOLO
	    $nfse = new Nfse();
	    $nfse->setConfiguration($configuration);
	    $consulta = $nfse->findByIdOrProtocol('5b8682fe-666d-4272-b8fa-de25481b7a4e');
	    var_dump($consulta);
		    
		/*} catch (ValidationError $e) {
		    // Algum campo foi informado no formato errado
		    var_dump($e);
		} catch (RequiredError $e) {
		    // Campos requeridos não foram informados
		    var_dump($e);
		} catch (\Exception $e) {
		    // Algum erro não esperado
		    var_dump($e);
		}*/

		/*$configuration = new Configuration(
        Configuration::TYPE_ENVIRONMENT_SANDBOX, // Ambiente a ser enviada a requisição
        '2da392a6-79d2-4304-a8b7-959572c7e44d' // API-Key
	    );

	    $nfse = new Nfse();
	    $nfse->setConfiguration($configuration);
	    $cancelation = $nfse->cancel('5c3118127ab98414de5e2bd6');
	    var_dump($cancelation);*/

    }

}