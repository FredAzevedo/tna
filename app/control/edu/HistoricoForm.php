<?php
/**
 * HistoricoForm Master/Detail
 * @author  Fred Azevedo
 */
class HistoricoForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Historico');
        $this->form->setFormTitle('Histórico');
        $this->form->setFieldSizes('100%');

        // master fields
        $id = new TEntry('id');
        $aluno_id = new TDBUniqueSearch('aluno_id', 'sample', 'Aluno', 'id', 'nome');
        $observacao = new TText('observacao');
        $serie_id = new TDBCombo('serie_id', 'sample', 'Serie', 'id', 'nome');
        $ano_letivo_id = new TDBCombo('ano_letivo_id', 'sample', 'AnoLetivo', 'id', 'ano');
        $situacao = new TEntry('situacao');

        // detail fields
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');
        $detail_serie_id = new TDBCombo('detail_serie_id', 'sample', 'Serie', 'id', 'nome');
        $detail_ano = new TEntry('detail_ano');
        $detail_estabelecimento = new TEntry('detail_estabelecimento');
        $detail_municipio = new TEntry('detail_municipio');
        $detail_uf = new TEntry('detail_uf');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        

        $row = $this->form->addFields( [ new TLabel('Id'), $id ],
                                       [ new TLabel('Aluno'), $aluno_id ],    
                                       [ new TLabel('Série'), $serie_id ]
                                    );
        $row->layout = ['col-sm-2','col-sm-5', 'col-sm-5'];

        $row = $this->form->addFields( [ ],
                                       [ ],    
                                       [ ],
                                       [ new TLabel('Ano Letivo'), $ano_letivo_id ],
                                       [ new TLabel('Situação'), $situacao ]
                                    );
        $row->layout = ['col-sm-2','col-sm-4', 'col-sm-2','col-sm-2','col-sm-2'];

        
         // detail fields
         $this->fieldlist = new TFieldList;
         $this->fieldlist-> width = '100%';
         $this->fieldlist->enableSorting();
 
         $disciplina_id = new TDBUniqueSearch('list_disciplina_id[]', 'sample', 'Disciplina', 'id', 'nome');
         $n1_ano = new TEntry('list_n1_ano[]');
         $n2_ano = new TEntry('list_n2_ano[]');
         $n3_ano = new TEntry('list_n3_ano[]');
         $n4_ano = new TEntry('list_n4_ano[]');
         $n5_ano = new TEntry('list_n5_ano[]');
         $n6_ano = new TEntry('list_n6_ano[]');
         $n7_ano = new TEntry('list_n7_ano[]');
         $n8_ano = new TEntry('list_n8_ano[]');
         $n9_ano = new TEntry('list_n9_ano[]');
         $n1_serie = new TEntry('list_n1_serie[]');
         $n2_serie = new TEntry('list_n2_serie[]');
         $n3_serie = new TEntry('list_n3_serie[]');
 
         $disciplina_id->setSize('100%');
         $n1_ano->setSize('100%');
         $n2_ano->setSize('100%');
         $n3_ano->setSize('100%');
         $n4_ano->setSize('100%');
         $n5_ano->setSize('100%');
         $n6_ano->setSize('100%');
         $n7_ano->setSize('100%');
         $n8_ano->setSize('100%');
         $n9_ano->setSize('100%');
         $n1_serie->setSize('100%');
         $n2_serie->setSize('100%');
         $n3_serie->setSize('100%');
 
         $this->fieldlist->addField( '<b>Disciplina</b>', $disciplina_id,['width' => '20%']);
         $this->fieldlist->addField( '<b>1º Ano</b>', $n1_ano);
         $this->fieldlist->addField( '<b>2º Ano</b>', $n2_ano);
         $this->fieldlist->addField( '<b>3º Ano</b>', $n3_ano);
         $this->fieldlist->addField( '<b>4º Ano</b>', $n4_ano);
         $this->fieldlist->addField( '<b>5º Ano</b>', $n5_ano);
         $this->fieldlist->addField( '<b>6º Ano</b>', $n6_ano);
         $this->fieldlist->addField( '<b>7º Ano</b>', $n7_ano);
         $this->fieldlist->addField( '<b>8º Ano</b>', $n8_ano);
         $this->fieldlist->addField( '<b>9º Ano</b>', $n9_ano);
         $this->fieldlist->addField( '<b>1º Série</b>', $n1_serie);
         $this->fieldlist->addField( '<b>2º Série</b>', $n2_serie);
         $this->fieldlist->addField( '<b>3º Série</b>', $n3_serie);
 
         $this->form->addField($disciplina_id);
         $this->form->addField($n1_ano);
         $this->form->addField($n2_ano);
         $this->form->addField($n3_ano);
         $this->form->addField($n4_ano);
         $this->form->addField($n5_ano);
         $this->form->addField($n6_ano);
         $this->form->addField($n7_ano);
         $this->form->addField($n8_ano);
         $this->form->addField($n9_ano);
         $this->form->addField($n1_serie);
         $this->form->addField($n2_serie);
         $this->form->addField($n3_serie);
         
         $this->form->addFields( [new TFormSeparator('<hr><b>HÍSTÓRICO DE NOTAS</b>') ] );
         $this->form->addFields( [$this->fieldlist] );

          // detail fields
        $this->fieldlistTotais = new TFieldList;
        $this->fieldlistTotais-> width = '100%';
        $this->fieldlistTotais->enableSorting();

        $totais = new TEntry('listTotais_totais[]');
        $n1_ano = new TEntry('listTotais_n1_ano[]');
        $n2_ano = new TEntry('listTotais_n2_ano[]');
        $n3_ano = new TEntry('listTotais_n3_ano[]');
        $n4_ano = new TEntry('listTotais_n4_ano[]');
        $n5_ano = new TEntry('listTotais_n5_ano[]');
        $n6_ano = new TEntry('listTotais_n6_ano[]');
        $n7_ano = new TEntry('listTotais_n7_ano[]');
        $n8_ano = new TEntry('listTotais_n8_ano[]');
        $n9_ano = new TEntry('listTotais_n9_ano[]');
        $n1_serie = new TEntry('listTotais_n1_serie[]');
        $n2_serie = new TEntry('listTotais_n2_serie[]');
        $n3_serie = new TEntry('listTotais_n3_serie[]');

        $totais->setSize('100%');
        $n1_ano->setSize('100%');
        $n2_ano->setSize('100%');
        $n3_ano->setSize('100%');
        $n4_ano->setSize('100%');
        $n5_ano->setSize('100%');
        $n6_ano->setSize('100%');
        $n7_ano->setSize('100%');
        $n8_ano->setSize('100%');
        $n9_ano->setSize('100%');
        $n1_serie->setSize('100%');
        $n2_serie->setSize('100%');
        $n3_serie->setSize('100%');

        $this->fieldlistTotais->addField( '<b>Totais</b>', $totais,['width' => '14%']);
        $this->fieldlistTotais->addField( '<b>1º Ano</b>', $n1_ano);
        $this->fieldlistTotais->addField( '<b>2º Ano</b>', $n2_ano);
        $this->fieldlistTotais->addField( '<b>3º Ano</b>', $n3_ano);
        $this->fieldlistTotais->addField( '<b>4º Ano</b>', $n4_ano);
        $this->fieldlistTotais->addField( '<b>5º Ano</b>', $n5_ano);
        $this->fieldlistTotais->addField( '<b>6º Ano</b>', $n6_ano);
        $this->fieldlistTotais->addField( '<b>7º Ano</b>', $n7_ano);
        $this->fieldlistTotais->addField( '<b>8º Ano</b>', $n8_ano);
        $this->fieldlistTotais->addField( '<b>9º Ano</b>', $n9_ano);
        $this->fieldlistTotais->addField( '<b>1º Serie</b>', $n1_serie);
        $this->fieldlistTotais->addField( '<b>2º Serie</b>', $n2_serie);
        $this->fieldlistTotais->addField( '<b>3º Serie</b>', $n3_serie);

        $this->form->addField($totais);
        $this->form->addField($n1_ano);
        $this->form->addField($n2_ano);
        $this->form->addField($n3_ano);
        $this->form->addField($n4_ano);
        $this->form->addField($n5_ano);
        $this->form->addField($n6_ano);
        $this->form->addField($n7_ano);
        $this->form->addField($n8_ano);
        $this->form->addField($n9_ano);
        $this->form->addField($n1_serie);
        $this->form->addField($n2_serie);
        $this->form->addField($n3_serie);
        
        $this->form->addFields( [new TFormSeparator('<hr><b>RESULTADO FINAL (TOTAIS)</b>') ] );
        $this->form->addFields( [$this->fieldlistTotais] );

        // detail fields
        $this->fieldlistComplementar = new TFieldList;
        $this->fieldlistComplementar-> width = '100%';
        $this->fieldlistComplementar->enableSorting();

        $serie_id = new TDBUniqueSearch('listComplementar_serie_id[]', 'sample', 'Serie', 'id', 'nome');
        $ano = new TEntry('listComplementar_ano[]');
        $estabelecimento = new TEntry('listComplementar_estabelecimento[]');
        $municipio = new TEntry('listComplementar_municipio[]');
        $uf = new TEntry('listComplementar_uf[]');

        $serie_id->setSize('100%');
        $ano->setSize('10px');
        $estabelecimento->setSize('60%');
        $municipio->setSize('60%');
        $uf->setSize('30%');

        $this->fieldlistComplementar->addField( '<b>Série</b>', $serie_id,['width' => '30%']);
        $this->fieldlistComplementar->addField( '<b>Ano</b>', $ano,['width' => '10%']);
        $this->fieldlistComplementar->addField( '<b>Estabelecimento</b>', $estabelecimento,['width' => '30%']);
        $this->fieldlistComplementar->addField( '<b>Municipio</b>', $municipio,['width' => '20%']);
        $this->fieldlistComplementar->addField( '<b>Uf</b>', $uf,['width' => '10%']);

        $this->form->addField($serie_id);
        $this->form->addField($ano);
        $this->form->addField($estabelecimento);
        $this->form->addField($municipio);
        $this->form->addField($uf);
        
        $this->form->addFields( [new TFormSeparator('<hr><b>REGISTROS COMPLEMENTARES</b>') ] );
        $this->form->addFields( [$this->fieldlistComplementar] );

        $row = $this->form->addFields( [ new TLabel('Observação'), $observacao ]
                                    );
        $row->layout = ['col-sm-12'];
         
         // create actions
         $this->form->addAction( _t('Save'),  new TAction( [$this, 'onSave'] ),  'fa:save green' );
         $this->form->addAction( _t('Clear'), new TAction( [$this, 'onClear'] ), 'fa:eraser red' );
         
         // create the page container
         $container = new TVBox;
         $container->style = 'width: 100%';
         //$container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
         $container->add($this->form);
         parent::add($container);
     }
     
     /**
      * Executed whenever the user clicks at the edit button da datagrid
      */
     function onEdit($param)
     {
         try
         {
             TTransaction::open('sample');
             
             if (isset($param['key']))
             {
                 $key = $param['key'];
                 
                 $object = new Historico($key);
                 $this->form->setData($object);
                 
                 $items  = HistoricoNotas::where('historico_id', '=', $key)->load();
                 
                 if ($items)
                 {
                     $this->fieldlist->addHeader();
                     foreach($items  as $item )
                     {
                         $detail = new stdClass;
                         $detail->list_disciplina_id = $item->disciplina_id;
                         $detail->list_n1_ano = $item->n1_ano;
                         $detail->list_n2_ano = $item->n2_ano;
                         $detail->list_n3_ano = $item->n3_ano;
                         $detail->list_n4_ano = $item->n4_ano;
                         $detail->list_n5_ano = $item->n5_ano;
                         $detail->list_n6_ano = $item->n6_ano;
                         $detail->list_n7_ano = $item->n7_ano;
                         $detail->list_n8_ano = $item->n8_ano;
                         $detail->list_n9_ano = $item->n9_ano;
                         $detail->list_n1_serie = $item->n1_serie;
                         $detail->list_n2_serie = $item->n2_serie;
                         $detail->list_n3_serie = $item->n3_serie;
                         $this->fieldlist->addDetail($detail);
                     }
                     
                     $this->fieldlist->addCloneAction();
                 }
                 else
                 {
                     $this->onClear($param);
                 }

                 $itemstotais  = HistoricoResultadoFinal::where('historico_id', '=', $key)->load();
                
                if ($itemstotais)
                {
                    $this->fieldlistTotais->addHeader();
                    foreach($itemstotais  as $itemTotais )
                    {
                        $detailTotais = new stdClass;
                        $detailTotais->listTotais_totais = $itemTotais->totais;
                        $detailTotais->listTotais_n1_ano = $itemTotais->n1_ano;
                        $detailTotais->listTotais_n2_ano = $itemTotais->n2_ano;
                        $detailTotais->listTotais_n3_ano = $itemTotais->n3_ano;
                        $detailTotais->listTotais_n4_ano = $itemTotais->n4_ano;
                        $detailTotais->listTotais_n5_ano = $itemTotais->n5_ano;
                        $detailTotais->listTotais_n6_ano = $itemTotais->n6_ano;
                        $detailTotais->listTotais_n7_ano = $itemTotais->n7_ano;
                        $detailTotais->listTotais_n8_ano = $itemTotais->n8_ano;
                        $detailTotais->listTotais_n9_ano = $itemTotais->n9_ano;
                        $detailTotais->listTotais_n1_serie = $itemTotais->n1_serie;
                        $detailTotais->listTotais_n2_serie = $itemTotais->n2_serie;
                        $detailTotais->listTotais_n3_serie = $itemTotais->n3_serie;
                        $this->fieldlistTotais->addDetail($detailTotais);
                    }
                    
                    $this->fieldlistTotais->addCloneAction();
                }
                else
                {
                    $this->onClear($param);
                }

                $itemsComplementar  = HistoricoRegistroComplementar::where('historico_id', '=', $key)->load();
                
                if ($itemsComplementar)
                {
                    $this->fieldlistComplementar->addHeader();
                    foreach($itemsComplementar  as $itemComplementar )
                    {
                        $detailComplementar = new stdClass;
                        $detailComplementar->listComplementar_serie_id = $itemComplementar->serie_id;
                        $detailComplementar->listComplementar_ano = $itemComplementar->ano;
                        $detailComplementar->listComplementar_estabelecimento = $itemComplementar->estabelecimento;
                        $detailComplementar->listComplementar_municipio = $itemComplementar->municipio;
                        $detailComplementar->listComplementar_uf = $itemComplementar->uf;
                        $this->fieldlistComplementar->addDetail($detailComplementar);
                    }
                    
                    $this->fieldlistComplementar->addCloneAction();
                }
                else
                {
                    $this->onClear($param);
                }
                 
                 TTransaction::close(); // close transaction
         }
         else
             {
                 $this->onClear($param);
             }
         }
         catch (Exception $e) // in case of exception
         {
             new TMessage('error', $e->getMessage());
             TTransaction::rollback();
         }
     }
     
     /**
      * Clear form
      */
     public function onClear($param)
     {
        $this->fieldlist->addHeader();
        $this->fieldlist->addDetail( new stdClass );
        $this->fieldlist->addCloneAction();

        $this->fieldlistTotais->addHeader();
        $this->fieldlistTotais->addDetail( new stdClass );
        $this->fieldlistTotais->addCloneAction();

        $this->fieldlistComplementar->addHeader();
        $this->fieldlistComplementar->addDetail( new stdClass );
        $this->fieldlistComplementar->addCloneAction();
     }
     
     /**
      * Save the Historico and the HistoricoNotas's
      */
     public static function onSave($param)
     {
         try
         {
             TTransaction::open('sample');
             
             $id = (int) $param['id'];
             $master = new Historico;
             $master->fromArray( $param);
             $master->store(); // save master object
             
             // delete details
             HistoricoNotas::where('historico_id', '=', $master->id)->delete();
             
             if( !empty($param['list_disciplina_id']) AND is_array($param['list_disciplina_id']) )
             {
                 foreach( $param['list_disciplina_id'] as $row => $disciplina_id)
                 {
                     if (!empty($disciplina_id))
                     {
                         $detail = new HistoricoNotas;
                         $detail->historico_id = $master->id;
                         $detail->disciplina_id = $param['list_disciplina_id'][$row];
                         $detail->n1_ano = $param['list_n1_ano'][$row];
                         $detail->n2_ano = $param['list_n2_ano'][$row];
                         $detail->n3_ano = $param['list_n3_ano'][$row];
                         $detail->n4_ano = $param['list_n4_ano'][$row];
                         $detail->n5_ano = $param['list_n5_ano'][$row];
                         $detail->n6_ano = $param['list_n6_ano'][$row];
                         $detail->n7_ano = $param['list_n7_ano'][$row];
                         $detail->n8_ano = $param['list_n8_ano'][$row];
                         $detail->n9_ano = $param['list_n9_ano'][$row];
                         $detail->n1_serie = $param['list_n1_serie'][$row];
                         $detail->n2_serie = $param['list_n2_serie'][$row];
                         $detail->n3_serie = $param['list_n3_serie'][$row];
                         $detail->store();
                     }
                 }
             }

             HistoricoResultadoFinal::where('historico_id', '=', $master->id)->delete();
            
             if( !empty($param['listTotais_totais']) AND is_array($param['listTotais_totais']) )
             {
                 foreach( $param['listTotais_totais'] as $row => $totais)
                 {
                     if (!empty($totais))
                     {
                         $detailTotais = new HistoricoResultadoFinal;
                         $detailTotais->historico_id = $master->id;
                         $detailTotais->totais = $param['listTotais_totais'][$row];
                         $detailTotais->n1_ano = $param['listTotais_n1_ano'][$row];
                         $detailTotais->n2_ano = $param['listTotais_n2_ano'][$row];
                         $detailTotais->n3_ano = $param['listTotais_n3_ano'][$row];
                         $detailTotais->n4_ano = $param['listTotais_n4_ano'][$row];
                         $detailTotais->n5_ano = $param['listTotais_n5_ano'][$row];
                         $detailTotais->n6_ano = $param['listTotais_n6_ano'][$row];
                         $detailTotais->n7_ano = $param['listTotais_n7_ano'][$row];
                         $detailTotais->n8_ano = $param['listTotais_n8_ano'][$row];
                         $detailTotais->n9_ano = $param['listTotais_n9_ano'][$row];
                         $detailTotais->n1_serie = $param['listTotais_n1_serie'][$row];
                         $detailTotais->n2_serie = $param['listTotais_n2_serie'][$row];
                         $detailTotais->n3_serie = $param['listTotais_n3_serie'][$row];
                         $detailTotais->store();
                     }
                 }
             }

             HistoricoRegistroComplementar::where('historico_id', '=', $master->id)->delete();
            
            if( !empty($param['listComplementar_serie_id']) AND is_array($param['listComplementar_serie_id']) )
            {
                foreach( $param['listComplementar_serie_id'] as $row => $serie_id)
                {
                    if (!empty($serie_id))
                    {
                        $detailComplementar = new HistoricoRegistroComplementar;
                        $detailComplementar->historico_id = $master->id;
                        $detailComplementar->serie_id = $param['listComplementar_serie_id'][$row];
                        $detailComplementar->ano = $param['listComplementar_ano'][$row];
                        $detailComplementar->estabelecimento = $param['listComplementar_estabelecimento'][$row];
                        $detailComplementar->municipio = $param['listComplementar_municipio'][$row];
                        $detailComplementar->uf = $param['listComplementar_uf'][$row];
                        $detailComplementar->store();
                    }
                }
            }
             
             $data = new stdClass;
             $data->id = $master->id;
             TForm::sendData('form_Historico', $data);
             TTransaction::close(); // close the transaction
             
             new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
         }
         catch (Exception $e) // in case of exception
         {
             new TMessage('error', $e->getMessage());
             TTransaction::rollback();
         }
     }
 }
 