<?php
/**
 * NfseParametroForm Form
 * @author  <your name here>
 */
class NfseParametroForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_NfseParametro');
        $this->form->setFormTitle('Parâmetros de NFse');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');
        $apikey = new TEntry('apikey');
        $ultimoNumeroNfse = new TEntry('ultimoNumeroNfse');
        $ultimoNumeroLote = new TEntry('ultimoNumeroLote');
        $tipoTributacao = new TDBCombo('tipoTributacao','sample','NfseTipotributacao','id','descricao','id');

        $tipo_ambiente = new TCombo('tipo_ambiente');
        $combo_tipo_ambientes = array();
        $combo_tipo_ambientes['TYPE_ENVIRONMENT_PRODUCTION'] = 'Produção';
        $combo_tipo_ambientes['TYPE_ENVIRONMENT_SANDBOX'] = 'Sandbox';
        $tipo_ambiente->addItems($combo_tipo_ambientes);

        $unico_servico = new TCombo('unico_servico');
        $combo_unico_servico['S'] = 'Sim';
        $combo_unico_servico['N'] = 'Não';
        $unico_servico->addItems($combo_unico_servico);

        $enviarEmail = new TCombo('enviarEmail');
        $combo_enviarEmails = array();
        $combo_enviarEmails['1'] = 'Sim';
        $combo_enviarEmails['0'] = 'Não';
        $enviarEmail->addItems($combo_enviarEmails);
        

        $DeducaoTipo = new TDBCombo('DeducaoTipo','sample','NfseTipodeducao','id','descricao','descricao');

        $DeducaoDescricao = new TEntry('DeducaoDescricao');

        $EventoTipo = new TEntry('EventoTipo');

        $EventoDescricao = new TEntry('EventoDescricao');

        $IssAliquota = new TEntry('IssAliquota');
        $IssExigibilidade = new TDBCombo('IssExigibilidade','sample','NfseExigibilidade','id','descricao','id');
        $IssProcessoSuspensao = new TEntry('IssProcessoSuspensao');
        $IssValor = new TEntry('IssValor');

        $IssRetido = new TCombo('IssRetido');
        $combo_IssRetidos = array();
        $combo_IssRetidos['1'] = 'Sim';
        $combo_IssRetidos['0'] = 'Não';
        $IssRetido->addItems($combo_IssRetidos);

        $IssValorRetido = new TEntry('IssValorRetido');

        $RetCofins = new TEntry('RetCofins');
        $RetCsll = new TEntry('RetCsll');
        $RetInss = new TEntry('RetInss');
        $RetIrrf = new TEntry('RetIrrf');
        $RetOutrasRetencoes = new TEntry('RetOutrasRetencoes');
        $RetPis = new TEntry('RetPis');

        $ServCnae = new TEntry('ServCnae');
        $ServCodigo = new TDBSeekButton('ServCodigo', 'sample', $this->form->getName(), 'CodigoServicos', 'descricao', 'ServCodigo', 'ServDiscriminacao');
        $ServCodigoCidadeIncidencia = new TEntry('ServCodigoCidadeIncidencia');
        $ServCodigoTributacao = new TEntry('ServCodigoTributacao');
        $ServDescricaoCidadeIncidencia = new TEntry('ServDescricaoCidadeIncidencia');
        $ServDiscriminacao = new TEntry('ServDiscriminacao');
        $ServIdIntegracao = new TEntry('ServIdIntegracao');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userid'));
        $user_id->addValidation('Usuário', new TRequiredValidator);


        $row = $this->form->addFields( [ new TLabel('ID'), $id ],    
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('API-Key'), $apikey ],
                                       [ new TLabel('Único serviço?'), $unico_servico ],
                                       [ new TLabel('Ambiente'), $tipo_ambiente ]
        );  
        $row->layout = ['col-sm-2', 'col-sm-3','col-sm-3','col-sm-2','col-sm-2'];

        $this->form->addContent( ['<h4><b>Serviço</b></h4><hr>'] );

        $row = $this->form->addFields( 
                                       [ new TLabel('Último Nº da NFse'), $ultimoNumeroNfse ],
                                       [ new TLabel('Último Nº do Lote'), $ultimoNumeroLote ],
                                       [ new TLabel('Envia E-mail?'), $enviarEmail ]
                                       
        );
        $row->layout = ['col-sm-2','col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Código do Serviço'), $ServCodigo ],
                                       [ new TLabel('Discriminação do Serviço'), $ServDiscriminacao ]
        );
        $row->layout = ['col-sm-2','col-sm-10'];

        /*$row = $this->form->addFields( [ new TLabel('ID Integração'), $ServIdIntegracao ],
                                       
        );
        $row->layout = ['col-sm-2','col-sm-2'];*/

        $this->form->addContent( ['<h4><b>ISS</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('Alíquota ISS'), $IssAliquota ],    
                                       [ new TLabel('Exigibilidade'), $IssExigibilidade ],
                                       [ new TLabel('Pro. Suspensão'), $IssProcessoSuspensao ],
                                       [ new TLabel('Valor'), $IssValor ],
                                       [ new TLabel('Retido p/ Tomador?'), $IssRetido ],
                                       [ new TLabel('Valor Retido'), $IssValorRetido ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $this->form->addContent( ['<h4><b>Dedução</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('Tipo de Dedução'), $DeducaoTipo ],
                                       [ new TLabel('Descrição da Dedução'), $DeducaoDescricao ]);
        $row->layout = ['col-sm-3','col-sm-9'];

        $this->form->addContent( ['<h4><b>Retenção</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('COFINS'), $RetCofins ],    
                                       [ new TLabel('CSLL'), $RetCsll ],
                                       [ new TLabel('INSS'), $RetInss ],
                                       [ new TLabel('IRRF'), $RetIrrf ],
                                       [ new TLabel('PIS'), $RetPis ],
                                       [ new TLabel('Outras Ret.'), $RetOutrasRetencoes ]);
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $this->form->addContent( ['<h4><b>Evento</b></h4><hr>'] );

        $row = $this->form->addFields( [ new TLabel('Código do Evento'), $EventoTipo ],    
                                       [ new TLabel('Descrição do Evento'), $EventoDescricao ]);
        $row->layout = ['col-sm-3', 'col-sm-9'];


        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addAction(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction('Voltar', new TAction( [$this, 'onExit'] ), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        ////$container->add(new TXMLBreadCrumb('menu.xml', 'NfseParametroList'));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new NfseParametro;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('sample'); // open a transaction
                $object = new NfseParametro($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onExit()
    {
        $result = TSession::getValue('NfseParametroList');

        $query = isset($result['query']) ? $result['query'] : null;

        if (!empty($query))
        {
            TScript::create("
                Adianti.waitMessage = 'Listando...';__adianti_post_data('NfseParametroForm', '$query');                                 
        ");
        }
    }
}
