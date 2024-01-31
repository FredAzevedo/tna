<?php

use Carbon\Carbon;
use Http\Discovery\HttpAsyncClientDiscovery;

class RelLGPD extends TWindow
{
    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Contrato de Serviços');
        parent::setSize(0.8,0.8);    
        $object = new TElement('object');
        $object->data  = "tmp/LGPD.pdf";
        $object->style = "width: 100%; height:calc(100% - 10px)";
        parent::add($object);

    }

    function onViewContrato($param)
    {
        $key = $param['id'];

        include_once( 'vendor/autoload.php' );

        try
        {
            TTransaction::open('sample');

            $clientecontrato = AlunoContrato::where('id','=',$key)->first();
            $html = RelatorioCustomizado::where('id','=',2)->first();

            $numero_contrato = str_pad($key, 5, "0", STR_PAD_LEFT);
            $unit  = new SystemUnit(TSession::getValue('userunitid'));
            $endereco = $unit->logradouro." Nº: ".$unit->numero." Bairro: ".$unit->bairro.". ".$unit->complemento." Cidade: ".$unit->cidade." UF: ".$unit->uf." CEP: ".$unit->cep;
            
            //verifica se é para adicionar header
            if($html->head == "S")
            {
                $pdf = new ReportHeader();
                $pdf->set_param("LogoWS.png",$unit->razao_social,$unit->nome_fantasia,$endereco,$unit->cnpj,$clientecontrato->id,$unit->telefone,$unit->insc_estadual,$unit->insc_municipal);
                $pdf->addPage('P', 'A4');
            }
            else
            {
                $pdf = new TCPDF;
                $pdf->SetMargins(10,15,15);
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                $pdf->addPage('P', 'A4');
            }
            
            $aluno_contrato = new AlunoContrato($param['id']);
            $dataPorExtenso = $aluno_contrato->prazo_inicio;
            //dados do primeiro responsável
            $primeiroResponsavel    = new Responsavel($aluno_contrato->primeiro_responsavel_id);
            $segundoResponsavel     = new Responsavel($aluno_contrato->segundo_responsavel_id);
    
            if(!empty($aluno_contrato->primeiro_responsavel_id))
            {
                $pri_responsavel = $primeiroResponsavel->nome." Nacionalidade: ".$primeiroResponsavel->nacionalidade." Estado Civil: ".$primeiroResponsavel->estado_civil." Profissão: ".$primeiroResponsavel->profissao." Carteira de Identidade: ".$primeiroResponsavel->rg." CPF: ".$primeiroResponsavel->cpf." Residente e Domiciliado na Rua: ".$primeiroResponsavel->logradouro." Nº: ".$primeiroResponsavel->numero." Bairro: ".$primeiroResponsavel->bairro." CEP: ".$primeiroResponsavel->cep." Cidade: ".$primeiroResponsavel->cidade." Estado: ".$primeiroResponsavel->uf." Tel.: ".$primeiroResponsavel->telefone." E-mail: ".$primeiroResponsavel->email;
            }else{
                $pri_responsavel = "";
            }

            if(!empty($aluno_contrato->segundo_responsavel_id))
            {
                $seg_responsavel = $segundoResponsavel->nome."Nacionalidade: ".$segundoResponsavel->nacionalidade." Estado Civil: ".$segundoResponsavel->estado_civil." Profissão: ".$segundoResponsavel->profissao." Carteira de Identidade: ".$segundoResponsavel->rg." CPF: ".$segundoResponsavel->cpf." Residente e Domiciliado na Rua: ".$segundoResponsavel->logradouro." Nº: ".$segundoResponsavel->numero." Bairro: ".$segundoResponsavel->bairro." CEP: ".$segundoResponsavel->cep." Cidade: ".$segundoResponsavel->cidade." Estado: ".$segundoResponsavel->uf." Tel.: ".$segundoResponsavel->telefone." E-mail: ".$segundoResponsavel->email;
            }else{
                $seg_responsavel = "";
            }


            $benif = AlunoContratoBeneficiario::where("aluno_contrato_id","=", $param['id'])->load();
            if($benif)
            {
                foreach($benif as $beneficiario)
                {
                    $aluno = new Aluno($beneficiario->aluno_id);
                    $aluno_beneficiario .= $aluno->nome.". Residente e domiciliado na Rua: ".$aluno->logradouro." Nº: ".$aluno->numero." Bairro: ".$aluno->bairro." CEP: ".$aluno->cep." Cidade: ".$aluno->cidade." Estado: ". $aluno->uf.", ";

                }
            }

            $html = new RelatorioCustomizado(2);

            $html->conteudo = str_replace('[pri_responsavel]', $pri_responsavel, $html->conteudo);
            $html->conteudo = str_replace('[seg_responsavel]', $seg_responsavel, $html->conteudo);
            $html->conteudo = str_replace('[aluno_beneficiario]', $aluno_beneficiario, $html->conteudo);
            $html->conteudo = str_replace('[data_por_extenso]', self::exibirDataPorExtenso($dataPorExtenso), $html->conteudo);
 
            $pdf->writeHTML($html->conteudo, true, false, true, false, '');
                 
            $arq = PATH."/tmp/LGPD.pdf"; 
            $pdf->Output( $arq, "F");
            
            //END TCPDF
            TTransaction::close();

        }
        catch (Exception $e)
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }

    function exibirDataPorExtenso($data) {
        // Criar um objeto Carbon a partir da data fornecida
        $dataCarbon = Carbon::parse($data);
        setlocale(LC_TIME, 'pt_BR.utf-8');
        // Obter a data por extenso em português
        $dataPorExtenso = $dataCarbon->formatLocalized('%A, %d de %B de %Y');
    
        return $dataPorExtenso;
    }
}   