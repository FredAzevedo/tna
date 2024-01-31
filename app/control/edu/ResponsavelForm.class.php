<?php

use Adianti\Widget\Form\TEntry;

/**
 * ResponsavelForm Form
 * @author  Fred Azv
 */
class ResponsavelForm extends TPage
{
    protected $form; 
    

    public function __construct( $param )
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Responsavel');
        $this->form->setFormTitle('Responsavel');
        $this->form->setFieldSizes('100%');

        $id = new TEntry('id');
        $unit_id = new THidden('unit_id');
        $unit_id->setValue(TSession::getValue('userunitid'));
        $responsavel_tipo_id = new TDBCombo('responsavel_tipo_id', 'sample', 'ResponsavelTipo', 'id', 'tipo');
        $nome = new TEntry('nome');
        $nome->forceUpperCase();
        $cpf = new TEntry('cpf');
        $cpf->setMask('999.999.999-99');
        $rg = new TEntry('rg');
        $nascimento = new TDate('nascimento');
        $nascimento->setDatabaseMask('yyyy-mm-dd');
        $nascimento->setMask('dd/mm/yyyy');
        $nacionalidade = new TEntry('nacionalidade');
        $nacionalidade->forceUpperCase();
        $estado_civil = new TEntry('estado_civil');
        $estado_civil->forceUpperCase();
        $profissao = new TEntry('profissao');
        $profissao->forceUpperCase();
        $local_trabalho = new TEntry('local_trabalho');
        $local_trabalho->forceUpperCase();
        $telefone1 = new TEntry('telefone1');
        $telefone1->setMask('(99) 99999-9999');
        $telefone2 = new TEntry('telefone2');
        $telefone2->setMask('(99) 99999-9999');
        $email = new TEntry('email');
        $cep = new TEntry('cep');
        $buscaCep = new TAction(array($this, 'onCep2'));
        $cep->setExitAction($buscaCep);
        $cep->setMask('99.999-999');
        $logradouro = new TEntry('logradouro');
        $logradouro->forceUpperCase();
        $numero = new TEntry('numero');
        $numero->forceUpperCase();
        $bairro = new TEntry('bairro');
        $bairro->forceUpperCase();
        $cidade = new TEntry('cidade');
        $cidade->forceUpperCase();
        $uf = new TEntry('uf');
        $uf->forceUpperCase();
        $complemento = new TEntry('complemento');
        $codMuni = new TEntry('codMuni');
        
        $this->form->addFields( [$unit_id] );


        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Tipo do Responsável'), $responsavel_tipo_id ],
                                       [ new TLabel('Nome do Responsável'), $nome ],    
                                       [ new TLabel('CPF'), $cpf ],
                                       [ new TLabel('RG'), $rg ]
        );
        $row->layout = ['col-sm-2','col-sm-2','col-sm-4', 'col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Nascimento'), $nascimento ],
                                       [ new TLabel('Nacionalidade'), $nacionalidade ],    
                                       [ new TLabel('Estado Civíl'), $estado_civil ],
                                       [ new TLabel('Profissão'), $profissao ]
        );
        $row->layout = ['col-sm-2','col-sm-2', 'col-sm-2','col-sm-6'];

        $this->form->addContent( ['<hr><h4>Endereço</h4>'] );

        $row = $this->form->addFields( [ new TLabel('CEP'), $cep ],    
                                       [ new TLabel('Logradouro'), $logradouro ],
                                       [ new TLabel('Número'), $numero ],
                                       [ new TLabel('Bairro'), $bairro ]);
        $row->layout = ['col-sm-2', 'col-sm-4', 'col-sm-2', 'col-sm-4'];
        
        $row = $this->form->addFields( [ new TLabel('Complemento'), $complemento ],
                                       [ new TLabel('Cidade'), $cidade ],    
                                       [ new TLabel('UF'), $uf ],
                                       [ new TLabel('Código do IBGE'), $codMuni ]);
        $row->layout = ['col-sm-5','col-sm-4', 'col-sm-1', 'col-sm-2'];

        $this->form->addContent( ['<hr><h4>Dados Complementares</h4>'] ); 

        $row = $this->form->addFields( [ new TLabel('Local de Trabalho'), $local_trabalho ],
                                       [ new TLabel('Telefone 1'), $telefone1 ],    
                                       [ new TLabel('Telefone 2'), $telefone2 ],
                                       [ new TLabel('E-Mail'), $email ]);
        $row->layout = ['col-sm-5','col-sm-2', 'col-sm-2', 'col-sm-3'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    
    public static function onCep2($param)
    {
        try {
            $retorno = Utilidades::onCep($param['cep']);
            $objeto  = json_decode($retorno);
            
            if (isset($objeto->logradouro)){
                $obj                    = new stdClass();
                $obj->logradouro = $objeto->logradouro;
                $obj->bairro   = $objeto->bairro;
                $obj->cidade   = $objeto->localidade;
                $obj->uf       = $objeto->uf;
                $obj->codMuni  = $objeto->ibge;

                TForm::sendData('form_Responsavel',$obj, false, false );
                unset($obj);
            }else{
                //new TMessage('info', 'Erro ao buscar endereço por este CEP.');
            }
        }catch (Exception $e){
            new TMessage('error', '<b>Error:</b> ' . $e->getMessage());
        }
    }

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
            
            $object = new Responsavel;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
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
                $object = new Responsavel($key); // instantiates the Active Record
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
}
