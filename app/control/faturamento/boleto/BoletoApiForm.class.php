<?php
/**
 * BoletoApiForm Form
 * @author  Frec Azv.
 */
class BoletoApiForm extends TPage
{
    protected $form; // form
    
    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_BoletoApi');
        $this->form->setFormTitle('Gestão de Boletos');
        $this->form->setFieldSizes('100%');
        
        // create the form fields
        $id = new TEntry('id');

        $user_id = new THidden('user_id');
        $user_id->setValue(TSession::getValue('userid'));

        $unit_id = new THidden('unit_id');
        $unit_id->setValue(TSession::getValue('userunitid'));

        $vencimento = new TDate('vencimento');
        //$vencimento->setValue(date('d/m/Y'));
        $vencimento->setMask('dd/mm/yyyy');
        $vencimento->setDatabaseMask('yyyy-mm-dd');
        $valor = new TNumeric('valor', 2, ',', '.', true);
        $juros = new TNumeric('juros', 2, ',', '.', true);
        $multa = new TNumeric('multa', 2, ',', '.', true);
        $desconto = new TNumeric('desconto', 2, ',', '.', true);
        $cliente_id = new TDBUniqueSearch('cliente_id', 'sample', 'Cliente', 'id', 'razao_social');
        $cliente_id->setMask('{razao_social} - {cpf_cnpj}');
        $cliente_id->setMinlength(1);
        $nome_cliente = new TEntry('nome_cliente');
        $cpf_cliente = new TEntry('cpf_cliente');
        //$cpf_cliente->setMask('99.999.999-99');
        $endereco_cliente = new TEntry('endereco_cliente');
        $numero_cliente = new TEntry('numero_cliente');
        $complemento_cliente = new TEntry('complemento_cliente');
        $bairro_cliente = new TEntry('bairro_cliente');
        $cidade_cliente = new TEntry('cidade_cliente');
        $estado_cliente = new TEntry('estado_cliente');
        $cep_cliente = new TEntry('cep_cliente');
        $email_cliente = new TEntry('email_cliente');
        $telefone_cliente = new TEntry('telefone_cliente');
        $texto = new TText('texto');
        $grupo = new TEntry('grupo');
        $pedido_numero = new TEntry('pedido_numero');
        $juros_fixo = new TNumeric('juros_fixo', 2, ',', '.', true);
        $multa_fixo = new TNumeric('multa_fixo', 2, ',', '.', true);
        $desconto1 = new TNumeric('desconto1', 2, ',', '.', true);
        $diasdesconto1 = new TNumeric('diasdesconto1', 1, '', '', true);
        $desconto2 = new TNumeric('desconto2', 2, ',', '.', true);
        $diasdesconto2 = new TNumeric('diasdesconto2', 1, '', '', true);
        $desconto3 = new TNumeric('desconto3', 2, ',', '.', true);
        $diasdesconto3 = new TNumeric('diasdesconto3', 1, '', '', true);
        $instrucao_adicional = new TText('instrucao_adicional');
        $especie_documento = new TEntry('especie_documento');

        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Vencimento'), $vencimento ],
                                       [ new TLabel('Referência'), $pedido_numero ],
                                       [ new TLabel(''), $user_id ],
                                       [ new TLabel(''), $unit_id ]

        );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-3', 'col-sm-3'];


        $row = $this->form->addFields( [ new TLabel('Valor'), $valor ],
                                       [ new TLabel('Juros'), $juros ],
                                       [ new TLabel('Multa'), $multa ],
                                       [ new TLabel('Desconto'), $desconto ],
                                       [ new TLabel('Juros Fixo'), $juros_fixo ],
                                       [ new TLabel('Multa Fixa'), $multa_fixo ]

        );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Cliente'), $cliente_id ],
                                       [ new TLabel('CPF/CNPJ'), $cpf_cliente ]

        );
        $row->layout = ['col-sm-10', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('CEP'), $cep_cliente ],
                                       [ new TLabel('Logradouro'), $endereco_cliente ],
                                       [ new TLabel('Nº'), $numero_cliente ]

        );
        $row->layout = ['col-sm-2', 'col-sm-8', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Complemento'), $complemento_cliente ],
                                       [ new TLabel('Bairro'), $bairro_cliente ],
                                       [ new TLabel('Cidade'), $cidade_cliente ],
                                       [ new TLabel('UF'), $estado_cliente ]

        );
        $row->layout = ['col-sm-5', 'col-sm-3', 'col-sm-3', 'col-sm-1'];

        $row = $this->form->addFields( [ new TLabel('E-mail'), $email_cliente ],
                                       [ new TLabel('Telefone'), $telefone_cliente ],
                                       [ new TLabel('Grupo'), $grupo ]

        );
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Texto da Fatura'), $texto ]);
        $row->layout = ['col-sm-12'];


        $row = $this->form->addFields( [ new TLabel('Desconto 1'), $desconto1 ],
                                       [ new TLabel('Dias 1'), $diasdesconto1 ],
                                       [ new TLabel('Desconto 2'), $desconto2 ],
                                       [ new TLabel('Dias 2'), $diasdesconto2 ],
                                       [ new TLabel('Desconto 3'), $desconto3 ],
                                       [ new TLabel('Dias 3'), $diasdesconto3 ]

        );
        $row->layout = ['col-sm-2', 'col-sm-2', 'col-sm-2', 'col-sm-2','col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Instrunção Adicional'), $instrucao_adicional ],
                                       [ new TLabel('Espécie de Boleto'), $especie_documento ]

        );
        $row->layout = ['col-sm-10', 'col-sm-2'];
        

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
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addAction( 'Voltar', new TAction(['BoletoApiList', 'onReload']), 'fa:angle-double-left');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'BoletoApiList'));
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
            
            
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $cli = new Cliente($data->cliente_id);
            $object = new BoletoApi;  // create an empty object
            $object->nome_cliente = $cli->razao_social;
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
                $object = new BoletoApi($key); // instantiates the Active Record
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
