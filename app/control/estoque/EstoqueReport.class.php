<?php
/**
 * EstoqueReport Report
 * @author  <your name here>
 */
class EstoqueReport extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Estoque_report');
        $this->form->setFormTitle('Estoque Report');
        

        // create the form fields
        $unit_id = new TEntry('unit_id');
        $produto_id = new TDBUniqueSearch('produto_id', 'sample', 'Produto', 'id', 'produto_grupo_id');
        $local = new TEntry('local');
        $saldo = new TEntry('saldo');
        $updated_at = new TEntry('updated_at');
        $output_type = new TRadioGroup('output_type');


        // add the fields
        $this->form->addFields( [ new TLabel('Unit Id') ], [ $unit_id ] );
        $this->form->addFields( [ new TLabel('Produto Id') ], [ $produto_id ] );
        $this->form->addFields( [ new TLabel('Local') ], [ $local ] );
        $this->form->addFields( [ new TLabel('Saldo') ], [ $saldo ] );
        $this->form->addFields( [ new TLabel('Updated At') ], [ $updated_at ] );
        $this->form->addFields( [ new TLabel('Output') ], [ $output_type ] );

        $output_type->addValidation('Output', new TRequiredValidator);


        // set sizes
        $unit_id->setSize('100%');
        $produto_id->setSize('100%');
        $local->setSize('100%');
        $saldo->setSize('100%');
        $updated_at->setSize('100%');
        $output_type->setSize('100%');


        
        $output_type->addItems(array('html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF', 'xls' => 'XLS'));
        $output_type->setLayout('horizontal');
        $output_type->setUseButton();
        $output_type->setValue('pdf');
        $output_type->setSize(70);
        
        // add the action button
        $btn = $this->form->addAction(_t('Generate'), new TAction(array($this, 'onGenerate')), 'fa:cog');
        $btn->class = 'btn btn-sm btn-primary';
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // ////$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Generate the report
     */
    function onGenerate()
    {
        try
        {
            // open a transaction with database 'sample'
            TTransaction::open('sample');
            
            // get the form data into an active record
            $data = $this->form->getData();
            
            $this->form->validate();
            
            $repository = new TRepository('Estoque');
            $criteria   = new TCriteria;
            
            if ($data->unit_id)
            {
                $criteria->add(new TFilter('unit_id', 'like', "%{$data->unit_id}%"));
            }
            if ($data->produto_id)
            {
                $criteria->add(new TFilter('produto_id', '=', "{$data->produto_id}"));
            }
            if ($data->local)
            {
                $criteria->add(new TFilter('local', 'like', "%{$data->local}%"));
            }
            if ($data->saldo)
            {
                $criteria->add(new TFilter('saldo', 'like', "%{$data->saldo}%"));
            }
            if ($data->updated_at)
            {
                $criteria->add(new TFilter('updated_at', 'like', "%{$data->updated_at}%"));
            }

           
            $objects = $repository->load($criteria, FALSE);
            $format  = $data->output_type;
            
            if ($objects)
            {
                $widths = array(100,100,100,100,100);
                
                switch ($format)
                {
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        break;
                    case 'xls':
                        $tr = new TTableWriterXLS($widths);
                        break;
                    case 'rtf':
                        $tr = new TTableWriterRTF($widths);
                        break;
                }
                
                // create the document styles
                $tr->addStyle('title', 'Arial', '10', 'B',   '#ffffff', '#9898EA');
                $tr->addStyle('datap', 'Arial', '10', '',    '#000000', '#EEEEEE');
                $tr->addStyle('datai', 'Arial', '10', '',    '#000000', '#ffffff');
                $tr->addStyle('header', 'Arial', '16', '',   '#ffffff', '#494D90');
                $tr->addStyle('footer', 'Times', '10', 'I',  '#000000', '#B1B1EA');
                
                // add a header row
                $tr->addRow();
                $tr->addCell('Estoque', 'center', 'header', 5);
                
                // add titles row
                $tr->addRow();
                $tr->addCell('Unit Id', 'right', 'title');
                $tr->addCell('Produto Id', 'right', 'title');
                $tr->addCell('Local', 'left', 'title');
                $tr->addCell('Saldo', 'left', 'title');
                $tr->addCell('Updated At', 'left', 'title');

                
                // controls the background filling
                $colour= FALSE;
                
                // data rows
                foreach ($objects as $object)
                {
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($object->unit_id, 'right', $style);
                    $tr->addCell($object->produto_id, 'right', $style);
                    $tr->addCell($object->local, 'left', $style);
                    $tr->addCell($object->saldo, 'left', $style);
                    $tr->addCell($object->updated_at, 'left', $style);

                    
                    $colour = !$colour;
                }
                
                // footer row
                $tr->addRow();
                $tr->addCell(date('Y-m-d h:i:s'), 'center', 'footer', 5);
                
                // stores the file
                if (!file_exists("app/output/Estoque.{$format}") OR is_writable("app/output/Estoque.{$format}"))
                {
                    $tr->save("app/output/Estoque.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/Estoque.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/Estoque.{$format}");
                
                // shows the success message
                new TMessage('info', 'Report generated. Please, enable popups.');
            }
            else
            {
                new TMessage('error', 'No records found');
            }
    
            // fill the form with the active record data
            $this->form->setData($data);
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
