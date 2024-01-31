<?php /** @noinspection PhpParamsInspection */

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridActionGroup;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TCheckButton;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
//use Eduardokum\LaravelBoleto\Boleto\Banco\Bancoob;
//use Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Bancoob;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Bb;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Sicredi;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto as BoletoContract;
use Eduardokum\LaravelBoleto\Contracts\Boleto\Boleto;
use Eduardokum\LaravelBoleto\Pessoa;

/**
 * BoletosList Listing
 * @author  <your name here>
 */
class BoletosList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Boletos');
        $this->form->setFormTitle('Gestão de Boletos');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $dataVencimento = new TDate('dataVencimento');
        $dataVencimento->setDatabaseMask('yyyy-mm-dd');
        $dataVencimento->setMask('dd/mm/yyyy');

        $valor = new TNumeric('valor', 2,',','.',true);

        $numeroDocumento = new TEntry('numeroDocumento');

        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'nome_fantasia');

        $cod_banco = new TCombo('cod_banco');
        $cod_banco->setDefaultOption(false);
        $cod_banco->addItems(Banco::getBancos());

        if (TSession::getValue('BoletosList_filter_cod_banco') === null) {
            $filter = new TFilter('cod_banco', '=', '"001"'); // pega o primeiro item, que é o banco do brasil
            TSession::setValue('BoletosList_filter_cod_banco',   $filter);
        }

        $cancelados = new TCombo('ativo');
        $cancelados->setDefaultOption(false);
        $cancelados->addItems([
            '-1' => 'Sim',
            '1' => 'Não',
        ]);

        $gerado_remessa = new TCombo('remessa');
        $gerado_remessa->setDefaultOption(false);
        $gerado_remessa->setValue('N');
        $gerado_remessa->addItems(['N' => 'Não', 'S' => 'Sim']);



        // master fields        
        $row = $this->form->addFields( [ new TLabel('Vencimento'),  $dataVencimento],
                                       [ new TLabel('Valor'), $valor ],
                                       [ new TLabel('Documento/Nº'), $numeroDocumento ],
                                       [ new TLabel('Cliente'),  $cliente_id],
                                       [ new TLabel('Remessa gerada?'), $gerado_remessa]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-4', 'col-sm-2'];


        $row = $this->form->addFields([ new TLabel('Banco'), $cod_banco ],
                                      [ new TLabel('Cancelados'), $cancelados] );
        $row->layout = ['col-sm-4', 'col-sm-3'];
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Boletos_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $btn2 = $this->form->addAction('Gerar Remessa', new TAction([$this, 'onGeraRemessa']), 'fa:plus green');

        $btn_retorno = $this->form->addAction('Importar Retorno', new TAction([$this, 'onImportarRetorno']), 'fa:search');

        $onGerarBoleto = new TDataGridAction(array($this, 'onGerarBoleto'));
        $onGerarBoleto->setLabel('Boleto');
        $onGerarBoleto->setImage('fa:file-pdf-o red');
        $onGerarBoleto->setField('id');
//
//        $action2 = new TDataGridAction(array($this, 'gerarCarne'));
//        $action2->setLabel('Gerar Carnê');
//        $action2->setImage('fa:file-pdf-o red');
//        $action2->setField('id');
//        $action2->setField('cliente_id');

        $action_group = new TDataGridActionGroup('Ações ', 'bs:th');

        $action_group->addHeader('Opções');
        $action_group->addAction($onGerarBoleto);
//        $action_group->addAction($action2);

        // add the actions to the datagrid


        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid());
        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        
        //adicionando o botão checkbox ao form pra passar dados via post
        $this->formGrid = new TForm;
        $this->formGrid->add($this->datagrid);
        $this->formGrid->addField($btn2);

        // creates the datagrid columns
        $column_check = new TDataGridColumn('checkbox', ' ', 'center'); 
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_dataVencimento = new TDataGridColumn('dataVencimento', 'Vencimento', 'center');
        $column_valor = new TDataGridColumn('valor', 'Valor', 'right');
        $column_numeroDocumento = new TDataGridColumn('numero', 'Nosso número', 'right');
        $column_conta_bancaria_id = new TDataGridColumn('conta_bancaria->conta', 'Conta', 'right');
        $column_cliente_id = new TDataGridColumn('cliente->razao_social', 'Cliente', 'left');
        $column_num_parcela = new TDataGridColumn('num_parcela', 'Parcela', 'right');
        $column_remessa = new TDataGridColumn('remessa', 'Remessa', 'center');


        // add the columns to the DataGrid
        $this->datagrid->addActionGroup($action_group);

        $this->datagrid->addColumn($column_check);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_dataVencimento);
        $this->datagrid->addColumn($column_valor);
        $this->datagrid->addColumn($column_numeroDocumento);
        $this->datagrid->addColumn($column_conta_bancaria_id);
        $this->datagrid->addColumn($column_cliente_id);
        $this->datagrid->addColumn($column_num_parcela);
        $this->datagrid->addColumn($column_remessa);


        // inline editing
        /*$dataVencimento_edit = new TDataGridAction(array($this, 'onInlineEdit'));
        $dataVencimento_edit->setField('id');
        $column_dataVencimento->setEditAction($dataVencimento_edit);*/

        $column_dataVencimento->setTransformer( function($value, $object, $row) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });
        
        $valor = function($value) {
            if (is_numeric($value)) {
                return 'R$ '.number_format($value, 2, ',', '.');
            }
            return $value;
        };

        $column_valor->setTransformer( $valor );

        
        // create EDIT action
        //$action_edit = new TDataGridAction(['BoletosForm', 'onEdit']);
        //$action_edit->setUseButton(TRUE);
        //$action_edit->setButtonClass('btn btn-default');
        /*$action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('far:edit blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);*/
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('far:trash-alt red fa-lg');
        $action_del->setField('id');
        //$this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

    
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'BoletosList'));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->formGrid, $this->pageNavigation));
        
        parent::add($container);
    }

    public static function onGerarBoleto($param) {

        $id = $param["key"];

        try {

            TTransaction::open('sample');

//            $_b = Boletos::where('ativo','=',true)
//                ->where('conta_receber_id','=',$data->id)->load();

            //se nao for array e tiver um item
//            if (!(is_array($_b) && (count($_b) > 0))){
//                throw new Exception('Gere um boleto antes de gerar a segunda via.');
//            }

//            $db_boleto = $_b[0];
            $db_boleto = new Boletos($id);

            $target_folder_carne = $db_boleto->path_pdf;

            if ($target_folder_carne && file_exists($target_folder_carne)) {
                parent::openFile($target_folder_carne);
                return;
            }


            $banco = $db_boleto->cod_banco;

            $image_name = $banco . '.png';
            $image_path = realpath(PATH . '/vendor/eduardokum/laravel-boleto/logos/') . DIRECTORY_SEPARATOR . $image_name;

            $dataVencimento    = new \Carbon\Carbon($db_boleto->dataVencimento);
            $dataDesconto      = new \Carbon\Carbon($db_boleto->dataDesconto);
            $dataDocumento     = new \Carbon\Carbon($db_boleto->dataDocumento);
            $dataProcessamento = new \Carbon\Carbon($db_boleto->dataProcessamento);

//            $valor = $db_boleto->valor;

            $instrucao = [];
            for ($i = 1; $i <= 4; $i++) {
                $inst = "instrucao{$i}";
                if ($db_boleto->$inst) {
                    array_push($instrucao, $db_boleto->$inst);
                }
            }

            $beneficiario = new Pessoa(
                [
                    'nome'      => $db_boleto->ben_nome,
                    'endereco'  => $db_boleto->ben_endereco,
                    'bairro'    => $db_boleto->ben_bairro,
                    'cep'       => $db_boleto->ben_cep,
                    'uf'        => $db_boleto->ben_uf,
                    'cidade'    => $db_boleto->ben_cidade,
                    'documento' => $db_boleto->ben_documento
                ]
            );
            $pagador = new Pessoa(
                [
                    'nome'      => $db_boleto->pag_nome,
                    'endereco'  => $db_boleto->pag_endereco,
                    'bairro'    => $db_boleto->pag_bairro,
                    'cep'       => $db_boleto->pag_cep,
                    'uf'        => $db_boleto->pag_uf,
                    'cidade'    => $db_boleto->pag_cidade,
                    'documento' => $db_boleto->pag_documento
                ]
            );

            $dados_boleto = [
                'logo'                   => $image_path,
                'dataVencimento'         => $dataVencimento,
                'dataDesconto'           => $dataDesconto,
                'dataDocumento'          => $dataDocumento,
                'dataProcessamento'      => $dataProcessamento,
                'valor'                  => $db_boleto->valor,
                'multa'                  => $db_boleto->multa,
                'juros'                  => $db_boleto->juros,
                'numero'                 => $db_boleto->numero,
                'numeroDocumento'        => $db_boleto->numeroDocumento,
                'pagador'                => $pagador,
                'beneficiario'           => $beneficiario,
                'carteira'               => $db_boleto->carteira,
                'agencia'                => $db_boleto->agencia,
                'convenio'               => $db_boleto->convenio,
                'conta'                  => $db_boleto->conta,
                'descricaoDemonstrativo' => [],
                'instrucoes'             => $instrucao,
                'aceite'                 => $db_boleto->aceite,
                'especieDoc'             => $db_boleto->especieDoc,
                'variacaoCarteira'       => $db_boleto->variacaoCarteira,
                'byte'                   => $db_boleto->byte,
                'posto'                  => $db_boleto->posto,
                'cip'                    => $db_boleto->cip,
                'codigoCliente'          => $db_boleto->codigoCliente,
                'range'                  => $db_boleto->campo_range,
                'contaDv'                => $db_boleto->contaDv,
                'diasProtesto'           => $db_boleto->diasProtesto,
            ];

            $banco_class = self::getBancoClass($banco);

//            var_dump($banco_class);

            $boleto = new $banco_class($dados_boleto);
//            $boleto = new Banco\Bancoob($dados_boleto);

            $pdf = new Pdf();
            $pdf->addBoleto($boleto);

            $date_name = new DateTime();
            $pdf_name = substr(md5($date_name->format('Y-m-d H:i:s-u') . $db_boleto->id),5,10);

            $nome_boleto = "{$pdf_name}-{$db_boleto->id}.pdf";

            $pdf->gerarBoleto($pdf::OUTPUT_SAVE, PATH . '/tmp/' . $nome_boleto);

            $target_folder_carne = '/tmp/' . $nome_boleto;

            $source_folder = 'tmp/' . $nome_boleto;

            if (file_exists($source_folder)) { //AND $finfo->file($source_file) == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                if (file_exists($target_folder_carne)) {
                    unlink($target_folder_carne);
                }
                rename($source_folder, $target_folder_carne);
            }

            $db_boleto->path_pdf = $target_folder_carne;

            $db_boleto->store();


            parent::openFile($target_folder_carne);

            new TMessage('info', 'Boleto gerado com sucesso');

            TTransaction::close();

        } catch (Exception $e) {
            new TMessage('error', "Erro ao gerar o boleto: {$e->getMessage()}");
            TTransaction::rollback();
        }

    }
    
    public function onGeraRemessa( $param )
    {

        $data = $this->form->getData();

        $selected = TSession::getValue(__CLASS__.'_selected_objects');
        if (!(is_array($selected) && count($selected))){
            new TMessage('info', 'Nenhum registro selecionado');
            return;
        }

        TTransaction::open('sample');
        try {

            $db_boleto = new Boletos(array_values($selected)[0]);
            $banco = $db_boleto->cod_banco;

            $tipo_remessa = $db_boleto->conta_bancaria->tipo_remessa;

            $beneficiario = new Pessoa(
                [
                    'nome'      => $db_boleto->ben_nome,
                    'endereco'  => $db_boleto->ben_endereco,
                    'bairro'    => $db_boleto->ben_bairro,
                    'cep'       => $db_boleto->ben_cep,
                    'uf'        => $db_boleto->ben_uf,
                    'cidade'    => $db_boleto->ben_cidade,
                    'documento' => $db_boleto->ben_documento
                ]
            );


            $remessa_class = self::getRemessaClass($banco, $tipo_remessa);

            $remessa = new $remessa_class([
                'beneficiario' => $beneficiario
            ]);

            $remessa->setCarteira($db_boleto->carteira);
            $remessa->setAgencia($db_boleto->agencia);
            $remessa->setConta($db_boleto->conta);

            if ($banco === Boleto::COD_BANCO_BANCOOB) {
                /** @var Bancoob $remessa */
                $remessa->setConvenio($db_boleto->convenio);
            } else if ($banco === Boleto::COD_BANCO_SICREDI) {

                $conta = $db_boleto->conta_bancaria;
                $id_remessa = $conta->ultima_remessa + 1;
                $conta->ultima_remessa = $id_remessa;
                $conta->store();

                /** @var Sicredi $remessa */
                $remessa->setIdremessa($id_remessa);
            } else if ($banco === Boleto::COD_BANCO_BB) {
                /** @var Bb $remessa */
                $remessa->setConvenio($db_boleto->convenio);
                $remessa->setVariacaoCarteira($db_boleto->variacaoCarteira);
            } else {
                throw new Exception('Banco utilizado não é suportado ainda. ' . $banco);
            }

            $dia = date('d');
            $mes = date('n');
            $nome_cnab = $db_boleto->conta.$mes.$dia.'.CRM';


            foreach ($selected as $id) {
//                var_dump($id);
                $db_boleto = new Boletos($id);

                $banco = $db_boleto->cod_banco;

                $image_name = $banco . '.png';
                $image_path = realpath(PATH . '/vendor/eduardokum/laravel-boleto/logos/') . DIRECTORY_SEPARATOR . $image_name;

                $dataVencimento    = new \Carbon\Carbon($db_boleto->dataVencimento);
                $dataDesconto      = new \Carbon\Carbon($db_boleto->dataDesconto);
                $dataDocumento     = new \Carbon\Carbon($db_boleto->dataDocumento);
                $dataProcessamento = new \Carbon\Carbon($db_boleto->dataProcessamento);

                $instrucao = [];
                for ($i = 1; $i <= 4; $i++) {
                    $inst = "instrucao{$i}";
                    if ($db_boleto->$inst) {
                        array_push($instrucao, $db_boleto->$inst);
                    }
                }

                $pagador = new Pessoa(
                    [
                        'nome'      => $db_boleto->pag_nome,
                        'endereco'  => $db_boleto->pag_endereco,
                        'bairro'    => $db_boleto->pag_bairro,
                        'cep'       => $db_boleto->pag_cep,
                        'uf'        => $db_boleto->pag_uf,
                        'cidade'    => $db_boleto->pag_cidade,
                        'documento' => $db_boleto->pag_documento
                    ]
                );

                $dados_boleto = [
                    'logo'                   => $image_path,
                    'dataVencimento'         => $dataVencimento,
                    'dataDesconto'           => $dataDesconto,
                    'dataDocumento'          => $dataDocumento,
                    'dataProcessamento'      => $dataProcessamento,
                    'valor'                  => $db_boleto->valor,
                    'multa'                  => $db_boleto->multa,
                    'juros'                  => $db_boleto->juros,
                    'numero'                 => $db_boleto->numero,
                    'numeroDocumento'        => $db_boleto->numeroDocumento,
                    'pagador'                => $pagador,
                    'beneficiario'           => $beneficiario,
                    'carteira'               => $db_boleto->carteira,
                    'agencia'                => $db_boleto->agencia,
                    'convenio'               => $db_boleto->convenio,
                    'conta'                  => $db_boleto->conta,
                    'descricaoDemonstrativo' => [],
                    'instrucoes'             => $instrucao,
                    'aceite'                 => $db_boleto->aceite,
                    'especieDoc'             => $db_boleto->especieDoc,
                    'variacaoCarteira'       => $db_boleto->variacaoCarteira,
                    'byte'                   => $db_boleto->byte,
                    'posto'                  => $db_boleto->posto,
                    'cip'                    => $db_boleto->cip,
                    'codigoCliente'          => $db_boleto->codigoCliente,
                    'range'                  => $db_boleto->campo_range,
                    'contaDv'                => $db_boleto->contaDv,
                    'diasProtesto'           => $db_boleto->diasProtesto,
                ];

                $banco_class = self::getBancoClass($banco);

                /** @var \Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi $boleto */
                $boleto = new $banco_class($dados_boleto);

                if ($db_boleto->ativo == '0') {
                    $boleto->baixarBoleto();
                }

                $remessa->addBoleto($boleto);

                $db_boleto->remessa = 'S';
                $db_boleto->store();
            }


            $remessa->save(PATH . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $nome_cnab);

            TSession::delValue(__CLASS__.'_selected_objects');

            TTransaction::close();
            new TMessage('info', 'Remessa gerada');
            TPage::openFile('tmp/' . $nome_cnab);

            $this->onReload($param);

        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', 'Problema ao gerar o CNAB. <br>' . $e->getMessage());
            $this->form->setData($data);
            $this->onReload($param);
        }

    }

    private static function getRemessaClass($banco, $tipo_remessa) {
        if (!$tipo_remessa) {
            $tipo_remessa = 'Cnab400';
        }

        $aBancos = [
            BoletoContract::COD_BANCO_BB => 'Eduardokum\\LaravelBoleto\\Cnab\\Remessa\\'.$tipo_remessa.'\\Banco\\Bb',
            BoletoContract::COD_BANCO_SANTANDER => 'Eduardokum\\LaravelBoleto\\Cnab\\Remessa\\'.$tipo_remessa.'\\Banco\\Santander',
            BoletoContract::COD_BANCO_CEF => 'Eduardokum\\LaravelBoleto\\Cnab\\Remessa\\'.$tipo_remessa.'\\Banco\\Caixa',
            BoletoContract::COD_BANCO_BRADESCO => 'Eduardokum\\LaravelBoleto\\Cnab\\Remessa\\'.$tipo_remessa.'\\Banco\\Bradesco',
            BoletoContract::COD_BANCO_ITAU => 'Eduardokum\\LaravelBoleto\\Cnab\\Remessa\\'.$tipo_remessa.'\\Banco\\Itau',
            BoletoContract::COD_BANCO_HSBC => 'Eduardokum\\LaravelBoleto\\Cnab\\Remessa\\'.$tipo_remessa.'\\Banco\\Hsbc',
            BoletoContract::COD_BANCO_SICREDI => 'Eduardokum\\LaravelBoleto\\Cnab\\Remessa\\'.$tipo_remessa.'\\Banco\\Sicredi',
            BoletoContract::COD_BANCO_BANRISUL => 'Eduardokum\\LaravelBoleto\\Cnab\\Remessa\\'.$tipo_remessa.'\\Banco\\Banrisul',
            BoletoContract::COD_BANCO_BANCOOB => 'Eduardokum\\LaravelBoleto\\Cnab\\Remessa\\'.$tipo_remessa.'\\Banco\\Bancoob',
            BoletoContract::COD_BANCO_BNB => 'Eduardokum\\LaravelBoleto\\Cnab\\Remessa\\'.$tipo_remessa.'\\Banco\\Bnb'
        ];

        if (array_key_exists($banco, $aBancos)) {
            return $aBancos[$banco];
        }

        throw new \Exception("Banco: $banco, inválido");
    }

    private static function getBancoClass($banco) {

        $aBancos = [
            BoletoContract::COD_BANCO_BB => 'Eduardokum\\LaravelBoleto\\Boleto\\Banco\\Bb',
            BoletoContract::COD_BANCO_SANTANDER => 'Eduardokum\\LaravelBoleto\\Boleto\\Banco\\Santander',
            BoletoContract::COD_BANCO_CEF => 'Eduardokum\\LaravelBoleto\\Boleto\\Banco\\Caixa',
            BoletoContract::COD_BANCO_BRADESCO => 'Eduardokum\\LaravelBoleto\\Boleto\\Banco\\Bradesco',
            BoletoContract::COD_BANCO_ITAU => 'Eduardokum\\LaravelBoleto\\Boleto\\Banco\\Itau',
            BoletoContract::COD_BANCO_HSBC => 'Eduardokum\\LaravelBoleto\\Boleto\\Banco\\Hsbc',
            BoletoContract::COD_BANCO_SICREDI => 'Eduardokum\\LaravelBoleto\\Boleto\\Banco\\Sicredi',
            BoletoContract::COD_BANCO_BANRISUL => 'Eduardokum\\LaravelBoleto\\Boleto\\Banco\\Banrisul',
            BoletoContract::COD_BANCO_BANCOOB => 'Eduardokum\\LaravelBoleto\\Boleto\\Banco\\Bancoob',
            BoletoContract::COD_BANCO_BNB => 'Eduardokum\\LaravelBoleto\\Boleto\\Banco\\Bnb',
        ];

        if (array_key_exists($banco, $aBancos)) {
            return $aBancos[$banco];
        }

        throw new \Exception("Banco: $banco, inválido");
    }
    
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('sample'); // open a transaction with database
            $object = new Boletos($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onImportarRetorno($param) {
        Adianti\Core\AdiantiCoreApplication::loadPage('ImportarRetornoForm');

    }
    
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('BoletosList_filter_dataVencimento',   NULL);
        TSession::setValue('BoletosList_filter_valor',   NULL);
        TSession::setValue('BoletosList_filter_numeroDocumento',   NULL);
        TSession::setValue('BoletosList_filter_cliente_id',   NULL);
        TSession::setValue('BoletosList_filter_cod_banco',   NULL);
        TSession::setValue('BoletosList_filter_remessa',   NULL);
        TSession::setValue('BoletosList_filter_ativo',   NULL);

        if (isset($data->ativo) && $data->ativo !== '-1') {
            $filter = new TFilter('ativo', '=', (int) $data->ativo);
            TSession::setValue('BoletosList_filter_ativo', $filter);
        }

        if (isset($data->dataVencimento) AND ($data->dataVencimento)) {
            $filter = new TFilter('dataVencimento', 'like', "%{$data->dataVencimento}%"); // create the filter
            TSession::setValue('BoletosList_filter_dataVencimento',   $filter); // stores the filter in the session
        }


        if (isset($data->valor) AND ($data->valor)) {
            $filter = new TFilter('valor', 'like', "%{$data->valor}%"); // create the filter
            TSession::setValue('BoletosList_filter_valor',   $filter); // stores the filter in the session
        }


        if (isset($data->numeroDocumento) AND ($data->numeroDocumento)) {
            $filter = new TFilter('numeroDocumento', 'like', "%{$data->numeroDocumento}%"); // create the filter
            TSession::setValue('BoletosList_filter_numeroDocumento',   $filter); // stores the filter in the session
        }


        if (isset($data->cliente_id) AND ($data->cliente_id)) {
            $filter = new TFilter('cliente_id', '=', "$data->cliente_id"); // create the filter
            TSession::setValue('BoletosList_filter_cliente_id',   $filter); // stores the filter in the session
        }

        if (isset($data->remessa) and ($data->remessa)) {
            $filter = new TFilter('remessa', '=', $data->remessa);
            TSession::setValue('BoletosList_filter_remessa', $filter);
        }

        if (isset($data->cod_banco) AND ($data->cod_banco)) {
            $filter = new TFilter('cod_banco', '=', "$data->cod_banco"); // create the filter
            TSession::setValue('BoletosList_filter_cod_banco',   $filter); // stores the filter in the session
        } else {
            $filter = new TFilter('cod_banco', '=', '001'); // create the filter
            TSession::setValue('BoletosList_filter_cod_banco',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Boletos_filter_data', $data);
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }

    public static function onSelect($param) {
        // get the selected objects from session
        $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');

        $check = $param['check'];
        $id = $param['id'];
        if ($check == 'false'){
            if (isset($selected_objects[$id])){
                unset($selected_objects[$id]);
            }
        }
        else
        {
            $selected_objects[$id] = $id; // add the object inside the array
        }
        TSession::setValue(__CLASS__.'_selected_objects', $selected_objects); // put the array back to the sessio
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'sample'
            TTransaction::open('sample');
            
            // creates a repository for Boletos
            $repository = new TRepository('Boletos');
            $limit = 200;
            // creates a criteria
            $criteria = new TCriteria();
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'desc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            $criteria->add(new TFilter('unit_id',  '= ', TSession::getValue('userunitid')));
//            $criteria->add(new TFilter('ativo',  '= ', true));
//            $criteria->add(new TFilter('remessa',  '= ', 'N'));

            if (TSession::getValue('BoletosList_filter_ativo')) {
                $criteria->add(TSession::getValue('BoletosList_filter_ativo'));
            }

            if (TSession::getValue('BoletosList_filter_remessa')) {
                $criteria->add(TSession::getValue('BoletosList_filter_remessa'));
            }

            if (TSession::getValue('BoletosList_filter_dataVencimento')) {
                $criteria->add(TSession::getValue('BoletosList_filter_dataVencimento')); // add the session filter
            }


            if (TSession::getValue('BoletosList_filter_valor')) {
                $criteria->add(TSession::getValue('BoletosList_filter_valor')); // add the session filter
            }


            if (TSession::getValue('BoletosList_filter_numeroDocumento')) {
                $criteria->add(TSession::getValue('BoletosList_filter_numeroDocumento')); // add the session filter
            }


            if (TSession::getValue('BoletosList_filter_cliente_id')) {
                $criteria->add(TSession::getValue('BoletosList_filter_cliente_id')); // add the session filter
            }

            if (TSession::getValue('BoletosList_filter_cod_banco')) {
                $criteria->add(TSession::getValue('BoletosList_filter_cod_banco')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();

            $selected_objects = TSession::getValue(__CLASS__.'_selected_objects');

            if ($objects)
            {
                $total = count($objects);
                $atual = 0;
                foreach ($objects as $object)
                {
                    $atual++;


                    $chk_selecionar = new TCheckButton("chkcheckbutton");
                    $chk_selecionar->id = "chkcheckbutton{$object->id}";
                    $chk_selecionar->code = $object->id;
                    $chk_selecionar->setIndexValue('on');

                    if (isset($selected_objects[$object->id])){
                        $chk_selecionar->setValue('on');
                    }

                    $c = new TElement('div');
                    $c->add($chk_selecionar);

                    if ($total == $atual){

                        $selected = '';
                        foreach ((array)$selected_objects as $s) {
                            $selected .= $s . ',';
                        }

                        $selected = "[{$selected}]";

                        $script = TScript::create('$(document).ready(function () {
                            window.boleto_selected = '. $selected .';
                            
                            $("input[name=chkcheckbutton]").off("change").change(function () {
                            var value_check = $(this).is(\':checked\');
                            var code = $(this).attr("code");
                            if (value_check) {
                                if (window.boleto_selected.indexOf(code) === -1) {
                                    window.boleto_selected.push(code);
                                }
                            } else {
                                var index = window.boleto_selected.indexOf(code);
                                if (index > -1) {
                                    window.boleto_selected.splice(index,1);
                                }
                            }
                            __adianti_ajax_exec(\'class=BoletosList&method=onSelect&id=\'+code+\'&check=\'+value_check);
                            });});',false);

                        $c->add($script);
                    }

//                    $object->checkbox = new TCheckButton('checkbox'.$object->id);
//                    $object->checkbox->setIndexValue($object->id);
                    $object->checkbox = $c;
//                    $this->form->addField($object->checkbox);
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('sample'); // open a transaction with database
            $object = new Boletos($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    



    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
