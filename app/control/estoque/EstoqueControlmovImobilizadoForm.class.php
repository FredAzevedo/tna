<?php

use Adianti\Widget\Form\TNumeric;

/**
 * EstoqueControlmovImobilizadoForm Form
 * @author  <your name here>
 */
class EstoqueControlmovImobilizadoForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_EstoqueControlmovImobilizado');
        $this->form->setFormTitle('Emplaquetamento de Imobilizado');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new TEntry('id');

        $id_unit_session = new TCriteria();
        $id_unit_session->add(new TFilter('id','=',TSession::getValue('userunitid')));
        $unit_id = new TDBCombo('unit_id','sample','SystemUnit','id','unidade','unidade',$id_unit_session);
        $unit_id->setValue(TSession::getValue('userunitid'));
        $unit_id->setEditable(FALSE);

        $unit_produto = new TCriteria();
        $unit_produto->add(new TFilter('unit_id','=',TSession::getValue('userunitid')));
        $unit_produto->add(new TFilter('id','=',$param['produto_id']));
        $produto_id = new TDBCombo('produto_id', 'sample', 'Produto', 'id','{cod_referencia} - {nome_produto}','nome_produto', $unit_produto);
        //$produto_id->setMask('{cod_referencia} - {nome_produto}');
        $produto_id->addValidation('Produto', new TRequiredValidator);

        $local = new TDBCombo('local','sample','Viewlocal','id','nome_fantasia','nome_fantasia');
        $local->setValue(TSession::getValue('userunitid'));
        $local->addValidation('Local', new TRequiredValidator);

        $alocado = new TText('alocado');

        $id_user_session = new TCriteria();
        $id_user_session->add(new TFilter('id','=',TSession::getValue('userid')));
        $user_id = new TDBCombo('user_id','sample','SystemUser','id','name','name',$id_user_session);
        $user_id->setValue(TSession::getValue('userid'));
        $user_id->setEditable(FALSE);

        $estado = new TCombo('estado');
        $combo['1'] = 'Novo';
        $combo['2'] = 'Usado';
        $combo['3'] = 'Depreciado';
        $estado->addItems($combo);
        $estado->addValidation('estado', new TRequiredValidator);

        $validade_mes = new TEntry('validade_mes');

        $data_avaliacao = new TDate('data_avaliacao');
        $data_avaliacao->setValue(date("d-m-Y hh:ii"));
        $data_avaliacao->setDatabaseMask('yyyy-mm-dd');
        $data_avaliacao->setMask('dd/mm/yyyy');

        $valor_justo = new TNumeric('valor_justo', 2, ',', '.', true);

        $emplacamento = new TEntry('emplacamento');
        $emplacamento->addValidation('Emplacamento / Tombo', new TRequiredValidator);

        $tipo = new TCombo('tipo');
        $combo_tipos = array();
        $combo_tipos['E'] = 'Entrada';
        $combo_tipos['S'] = 'Saída';
        $tipo->addItems($combo_tipos);
        $tipo->setvalue('E');
        $tipo->addValidation('Tipo', new TRequiredValidator);
        $tipo->setEditable(FALSE);

        $quantidade = new TNumeric('quantidade',2,'','.',true);
        $quantidade->setvalue('1');
        $quantidade->addValidation('Quantidade', new TRequiredValidator);
        $quantidade->setEditable(FALSE);



        $row = $this->form->addFields( [ new TLabel('ID'), $id ],
                                       [ new TLabel('Unidade'), $unit_id ],
                                       [ new TLabel('Usuário'), $user_id ]
        );
        $row->layout = ['col-sm-2','col-sm-5', 'col-sm-5'];

        $row = $this->form->addFields( [ new TLabel('Produto'), $produto_id ],
                                       [ new TLabel('Local / Empresa ou Cliente'), $local ]);
        $row->layout = ['col-sm-6','col-sm-6'];

        $row = $this->form->addFields( [ new TLabel('Local de Alocação / Lugar físico em um ambiente'), $alocado ]);
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields( [ new TLabel('Estado de Conservação'), $estado ],
                                       [ new TLabel('Validade / em meses'), $validade_mes ],
                                       [ new TLabel('Data Avaliação'), $data_avaliacao ],
                                       [ new TLabel('Valor Justo'), $valor_justo ]
        );
        $row->layout = ['col-sm-3','col-sm-2','col-sm-2','col-sm-2'];

        $row = $this->form->addFields( [ new TLabel('Emplacamento / Tombo'), $emplacamento ],
                                       [ new TLabel('Tipo de movimento'), $tipo ],
                                       [ new TLabel('Quantidade'), $quantidade ]
        );
        $row->layout = ['col-sm-5','col-sm-2','col-sm-2'];

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Novo Lançamento',  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    
    public function getProduto($param)
    {
        
    }


    public function onSave( $param )
    {
        try
        {
            TTransaction::open('sample'); // open a transaction
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new EstoqueControlmovImobilizado;  // create an empty object
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
                $object = new EstoqueControlmovImobilizado($key); // instantiates the Active Record
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
