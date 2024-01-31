<?php

use Adianti\Database\TTransaction;

class Utilidades {

    public static function removerCaracteresEspeciais($valor){
        $valor = trim($valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);
        $valor = str_replace("(", "", $valor);
        $valor = str_replace(")", "", $valor);
        return $valor;
   }

    public static function dateForDB($date){

        $data = explode('/',$date);
        return $data[2]."-".$data[1]."-".$data[0];
    }

    public static function numberFormatPrecision($number, $precision = 2, $separator = '.')
    {
        $numberParts = explode($separator, $number);
        $response = $numberParts[0];
        if(count($numberParts)>1){
            $response .= $separator;
            $response .= substr($numberParts[1], 0, $precision);
        }
        return $response;
    }

    public static function limpaCaracter($valor){
         $valor = trim($valor);
         $valor = str_replace(".", "", $valor);
         $valor = str_replace(",", "", $valor);
         $valor = str_replace("-", "", $valor);
         $valor = str_replace("/", "", $valor);
         $valor = str_replace("(", "", $valor);
         $valor = str_replace(")", "", $valor);
         $valor = str_replace(" ", "", $valor);
         return $valor;
    }

    public static function formatarReal($valor){
        if (is_numeric($valor)) {
            return 'R$ '.number_format($valor, 2, ',', '.');
        }
        return $valor;
    }

    public static function formatarDataHora($date, $object)
    {
            if($date){
                    $dt = new DateTime($date);
                    return $dt->format('d/m/Y H:i');
            }    
    }

    public static function formatarData($date)
    {
            if($date){
                    $dt = new DateTime($date);
                    return $dt->format('d/m/Y');
            }    
    }
    
    public static function to_number($value, $decimal = 2) {
        if (is_numeric($value)) {
            return round($value, $decimal);
        }

        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        if (is_numeric($value)) {
            return round($value, $decimal);
        } else {
            return round(0, $decimal);
        }
    }

    
    public static function formatar_valor($value, $decimal = 2) {
        if (is_numeric($value)) {
            return number_format($value, $decimal, ',', '.');
        }
        else{
            return number_format( self::to_number($value) , $decimal, ',', '.');
        }

    }

    public static function onDataAtual() {
        $dia = date('d');
        $mes = date('m');
        $ano = date('Y');
        $semana = date('w');
        $cidade = "Digite aqui sua cidade";

        // configuração mes

        switch ($mes) {
            case 1:
                $mes = "Janeiro";
                break;
            case 2:
                $mes = "Fevereiro";
                break;
            case 3:
                $mes = "Março";
                break;
            case 4:
                $mes = "Abril";
                break;
            case 5:
                $mes = "Maio";
                break;
            case 6:
                $mes = "Junho";
                break;
            case 7:
                $mes = "Julho";
                break;
            case 8:
                $mes = "Agosto";
                break;
            case 9:
                $mes = "Setembro";
                break;
            case 10:
                $mes = "Outubro";
                break;
            case 11:
                $mes = "Novembro";
                break;
            case 12:
                $mes = "Dezembro";
                break;
        }


        // configuração semana

        switch ($semana) {
            case 0:
                $semana = "Domingo";
                break;
            case 1:
                $semana = "Segunda Feira";
                break;
            case 2:
                $semana = "Terça Feira";
                break;
            case 3:
                $semana = "Quarta Feira";
                break;
            case 4:
                $semana = "Quinta Feira";
                break;
            case 5:
                $semana = "Sexta Feira";
                break;
            case 6:
                $semana = "Sábado";
                break;
        }

        //Agora basta imprimir na tela...
        return ("$semana, $dia de $mes de $ano");
    }

    public static function mes_extenso($mes) {
        switch ($mes) {
            case 1:
                $mes = "Janeiro";
                break;
            case 2:
                $mes = "Fevereiro";
                break;
            case 3:
                $mes = "Março";
                break;
            case 4:
                $mes = "Abril";
                break;
            case 5:
                $mes = "Maio";
                break;
            case 6:
                $mes = "Junho";
                break;
            case 7:
                $mes = "Julho";
                break;
            case 8:
                $mes = "Agosto";
                break;
            case 9:
                $mes = "Setembro";
                break;
            case 10:
                $mes = "Outubro";
                break;
            case 11:
                $mes = "Novembro";
                break;
            case 12:
                $mes = "Dezembro";
                break;
        }
        return $mes;
    }

    public static function calcula_prazo($datacal, $prazo) {
        $retorno = $datacal;

        if (is_null($datacal)) {
            return $retorno;
        }
        $I = 0;
        while ($I < $prazo) {
            $retorno = date('Y-m-d', strtotime($retorno . '+1 days'));
            $DIA_SEMANA = utilidades::dia_semana($retorno);

            while (($DIA_SEMANA == 0) or ($DIA_SEMANA == 6)) {
                $retorno = date('Y-m-d', strtotime($retorno . '+1 days'));

                $ACHOU = 1;

                while ($ACHOU == 1) {
                    $feriados = Calendario::where('cal_data', '=', $retorno)->load();
                    if (count($feriados) === 0) {
                        $ACHOU = 0;
                    } else {
                        $retorno = date('Y-m-d', strtotime($retorno . '+1 days'));
                    }
                }
            }

            $DIA_SEMANA = utilidades::dia_semana($retorno);

            $ACHOU = 1;

            while ($ACHOU === 1) {

                $feriados = Calendario::where('cal_data', '=', $retorno)->load();
                if (count($feriados) === 0) {
                    $ACHOU = 0;
                } else {
                    $retorno = date('Y-m-d', strtotime($retorno . '+1 days'));
                }
            }
            $DIA_SEMANA = utilidades::dia_semana($retorno);

            if (($DIA_SEMANA > 0) and ($DIA_SEMANA < 6)) {
                $I++;
            }
        }
        return $retorno;
    }

    public static function dia_semana($Data) {
        return date('w', strtotime($Data));
    }

    public static function sim_nao() {
        $sim_nao = array();
        $sim_nao['1'] = 'Não';
        $sim_nao['2'] = 'Sim';
        return $sim_nao;
    }

    public static function tipo_pessoa() {
        $tipopessoa = array();
        $tipopessoa['F'] = 'Física';
        $tipopessoa['J'] = 'Jurídica';
        return $tipopessoa;
    }

    public static function tipo_comissao() {
        $tipocomissao = array();
        $tipocomissao['D'] = '(R$) Dinheiro';
        $tipocomissao['P'] = '(%) Porcentagem';
        return $tipocomissao;
    }

    public static function tipo_produto_servico() {
        $tipoprodutoservico = array();
        $tipoprodutoservico['P'] = 'Produto';
        $tipoprodutoservico['S'] = 'Serviço';
        return $tipoprodutoservico;
    }

    public static function formataCPF_CNPJ($value) {
        // O valor formatado
        $formatado = false;

        $s = self::soNumero($value);

        // Valida CPF
        if (strlen($s) == 11) {
            // Verifica se o CPF é válido
            if (self::validar_cpf($s)) {
                // Formata o CPF ###.###.###-##
                $formatado = substr($s, 0, 3) . '.';
                $formatado .= substr($s, 3, 3) . '.';
                $formatado .= substr($s, 6, 3) . '-';
                $formatado .= substr($s, 9, 2) . '';
            }
        } // Valida CNPJ
        elseif (strlen($s) == 14) {
            // Verifica se o CPF é válido
            if (self::validar_cnpj($s)) {
                // Formata o CNPJ ##.###.###/####-##
                $formatado = substr($s, 0, 2) . '.';
                $formatado .= substr($s, 2, 3) . '.';
                $formatado .= substr($s, 5, 3) . '/';
                $formatado .= substr($s, 8, 4) . '-';
                $formatado .= substr($s, 12, 14) . '';
            }
        }

        // Retorna o valor
        return $formatado;
    }

    public static function soNumero($str) {
        return preg_replace("/[^0-9]/", "", $str);
    }

    public static function validar_cpf($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', (string)$cpf);
        // Valida tamanho
        if (strlen($cpf) != 11) {
            return false;
        }
        // Calcula e confere primeiro dígito verificador
        for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--) {
            $soma += $cpf[$i] * $j;
        }
        $resto = $soma % 11;
        if ($cpf[9] != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }
        // Calcula e confere segundo dígito verificador
        for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--) {
            $soma += $cpf[$i] * $j;
        }
        $resto = $soma % 11;
        return $cpf[10] == ($resto < 2 ? 0 : 11 - $resto);
    }

    public static function validar_cnpj($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', (string)$cnpj);
        // Valida tamanho
        if (strlen($cnpj) != 14) {
            return false;
        }
        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }
        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

    public static function uf() {
        return array(
            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas', 'BA' => 'Bahia', 'CE' => 'Ceara',
            'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo', 'GO' => 'Goiás', 'MA' => 'Maranhão',
            'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais', 'PA' => 'Pará',
            'PB' => 'Paraíba', 'PR' => 'Paraná', 'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte', 'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima',
            'SC' => 'Santa Catarina', 'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
        );

    }
    // String a ser limitada
    // $string = 'Como limitar caracteres sem cortar as palavras com PHP';
    // Mostrando a string limitada em 25 caracteres.
    //print(limitarTexto($string, $limite = 25));

    public static function existe_no_array($buscar, $array, $campo) {
        $retorno = false;
        foreach ($array as $obj) {
            if (is_object($obj)) {
                if ($buscar == $obj->$campo) {
                    $retorno = true;
                    break;
                }
            }
            if (is_array($obj)) {
                if ($buscar == $obj[$campo]) {
                    $retorno = true;
                    break;
                }
            }

        }
        return $retorno;
    }

    public static function limitarTexto($texto, $limite, $quebrar = true) {
        //corta as tags do texto para evitar corte errado
        $contador = strlen(strip_tags($texto));
        if ($contador <= $limite):
            //se o número do texto form menor ou igual o limite então retorna ele mesmo
            $newtext = $texto;
        else:
            if ($quebrar == true): //se for maior e $quebrar for true
                //corta o texto no limite indicado e retira o ultimo espaço branco
                $newtext = trim(mb_substr($texto, 0, $limite)) . "...";
            else:
                //localiza ultimo espaço antes de $limite
                $ultimo_espaço = strrpos(mb_substr($texto, 0, $limite), " ");
                //corta o $texto até a posição lozalizada
                $newtext = trim(mb_substr($texto, 0, $ultimo_espaço)) . "...";
            endif;
        endif;
        return $newtext;
    }

    public static function onCep($param) {
        $param = preg_replace("/\D/", "", $param);
        return @file_get_contents('https://viacep.com.br/ws/' . urlencode($param) . '/json');
    }

    public static function onCNPJ($param) {
        $param = preg_replace("/\D/", "", $param);
        return @file_get_contents('https://www.receitaws.com.br/v1/cnpj/' . urlencode($param));
    }

    public static function format($mask, $string) {
        $string = Utilidades::soNumero($string);
        return vsprintf($mask, str_split($string));
    }

    public static function Valor($valor) {
        $verificaPonto = ".";
        if (strpos("[" . $valor . "]", "$verificaPonto")):
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        else:
            $valor = str_replace(',', '.', $valor);
        endif;

        return $valor;
    }

    public static function extenso($valor = 0, $maiusculas = false) {

        if (!$maiusculas) {
            $singular = ["centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"];
            $plural = ["centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"];
            $u = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
        } else {
            $singular = ["CENTAVO", "REAL", "MIL", "MILHÃO", "BILHÃO", "TRILHÃO", "QUADRILHÃO"];
            $plural = ["CENTAVOS", "REAIS", "MIL", "MILHÕES", "BILHÕES", "TRILHÕES", "QUADRILHÕES"];
            $u = ["", "um", "dois", "TRÊS", "quatro", "cinco", "seis", "sete", "oito", "nove"];
        }

        $c = [
            "", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos",
            "novecentos"
        ];
        $d = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
        $d10 = ["dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"];

        $z = 0;
        $rt = "";

        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        for ($i = 0; $i < count($inteiro); $i++) {
            for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++) {
                $inteiro[$i] = "0" . $inteiro[$i];
            }
        }

        $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
        for ($i = 0; $i < count($inteiro); $i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
            $t = count($inteiro) - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000") {
                $z++;
            } elseif ($z > 0) {
                $z--;
            }
            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0)) {
                $r .= (($z > 1) ? " de " : "") . $plural[$t];
            }
            if ($r) {
                $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ", " : " e ") : " ") . $r;
            }
        }

        if (!$maiusculas) {
            $return = $rt ? $rt : "zero";
        } else {
            if ($rt) {
                $rt = preg_replace(" E ", " e ", ucwords($rt));
            }
            $return = ($rt) ? ($rt) : "Zero";
        }

        if (!$maiusculas) {
            //return preg_replace(" E "," e ",ucwords($return));
            return strtoupper($return);
        } else {
            return strtoupper($return);
        }
    }

    public static function getDataTableJSON($params) {

        //$table_name = self::TABLENAME;
        //$table_name = 'v_contrato_servico_list';
        $table_name = $params['table'];

        $primary_key = $params['primary_key'];

        $dump = '';
        if (isset($params['criteria'])) {
            $criteria = $params['criteria'];
            $dump = $criteria->dump();
        }

        $aColumns = $params['aColumns'];
        $aColumns_select = $params['aColumns_select'];
        $aColumns_where = $params['aColumns_where'];
        if (isset($params['aColumns_orderby'])) {
            $aColumns_orderby = $params['aColumns_orderby'];
        } else {
            $aColumns_orderby = $aColumns_where;
        }

        TTransaction::open('sample');
        //TTransaction::setLogger(new TLoggerTXT('log-sql.txt'));
        try {
            $db = TTransaction::get();
            /**
             * Paging
             */
            $sLimit = "";
            if (isset($params['iDisplayStart']) && $params['iDisplayLength'] != '-1') {
                $sLimit = " LIMIT " . intval($params['iDisplayStart']) . ", " . intval($params['iDisplayLength']);
            }

            /**
             * Ordering
             */
            $aOrderingRules = array();
            if (isset($params['iSortCol_0'])) {
                $iSortingCols = intval($params['iSortingCols']);
                for ($i = 0; $i < $iSortingCols; $i++) {
                    if ($params['bSortable_' . intval($params['iSortCol_' . $i])] == 'true') {
                        $aOrderingRules[] = $aColumns_orderby[intval($params['iSortCol_' . $i])] . " " . ($params['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc');
                    }
                }
            }

            if (!empty($aOrderingRules)) {
                $sOrder = " ORDER BY " . implode(", ", $aOrderingRules);
            } else {
                $sOrder = "";
            }

            /**
             * Filtering
             * NOTE this does not match the built-in DataTables filtering which does it
             * word by word on any field. It's possible to do here, but concerned about efficiency
             * on very large tables, and MySQL's regex functionality is very limited
             */
            $iColumnCount = count($aColumns_where);


            if (isset($params['sSearch']) && $params['sSearch'] != "") {
                $aFilteringRules = array();
                for ($i = 0; $i < $iColumnCount; $i++) {
                    if (isset($params['bSearchable_' . $i]) && $params['bSearchable_' . $i] == 'true') {
                        if ($aColumns_where[$i] != ' ') {
                            $aFilteringRules[] = $aColumns_where[$i] . " LIKE '%" . ($params['sSearch']) . "%'";
                        }
                    }
                }
                if (!empty($aFilteringRules)) {
                    $aFilteringRules = array('(' . implode(" OR ", $aFilteringRules) . ')');
                }
            }

            if ($dump) {
                $aFilteringRules[] = $dump;
            }

            // Individual column filtering
            for ($i = 0; $i < $iColumnCount; $i++) {
                if (isset($params['bSearchable_' . $i]) && $params['bSearchable_' . $i] == 'true' && $params['sSearch_' . $i] != '') {
                    if ($aColumns_where[$i] != ' ') {
                        $aFilteringRules[] = $aColumns_where[$i] . " LIKE '%" . ($params['sSearch_' . $i]) . "%'";
                    }
                }
            }

            if (!empty($aFilteringRules)) {
                $sWhere = " WHERE " . implode(" AND ", $aFilteringRules);
            } else {
                $sWhere = "";
            }

            /**
             * SQL queries
             * Get data to display
             */
            $aQueryColumns = array();
            foreach ($aColumns as $col) {
                if ($col != ' ') {
                    $aQueryColumns[] = $col;
                }
            }

            $sQuery = " SELECT SQL_CALC_FOUND_ROWS " . implode(", ", $aQueryColumns) . "
            FROM " . $table_name . $sWhere . $sOrder . $sLimit;
            //TTransaction::log($sQuery);

            $rResult = $db->query($sQuery) or die($db->error);

            // Data set length after filtering
            $sQuery = "SELECT FOUND_ROWS()";
            $rResultFilterTotal = $db->query($sQuery) or die($db->error);
            list($iFilteredTotal) = $rResultFilterTotal->fetch();

            // Total data set length
            $sQuery = "SELECT COUNT(" . $primary_key . ") FROM " . $table_name;
            //não contar filtros do sistema.
            if (trim($dump) !== '') {
                $sQuery .= " WHERE " . $dump;
            }

            $rResultTotal = $db->query($sQuery) or die($db->error);
            list($iTotal) = $rResultTotal->fetch();

            $total_itens = 0;
            if (isset($params['SUM_ALL_ITENS'])) {
                $total_itens = $params['SUM_ALL_ITENS'];
            }

            /**
             * Output
             */
            $output = array(
                "sEcho" => intval($params['sEcho']), "iTotalRecords" => $iTotal,
                "iTotalDisplayRecords" => $iFilteredTotal, "total_itens" => $total_itens, "aaData" => array(),
            );


            $fc = $params['callback'];

            //$params


            $output['aaData'] = call_user_func($fc, array_merge($params, [
                'rResult' => $rResult, 'iColumnCount' => $iColumnCount, 'aColumns' => $aColumns_select
            ]));

            echo json_encode($output);

            TTransaction::close();

        } catch (Exception $e) {

            TTransaction::rollback();
            echo $e->getMessage();
            echo '<br>';
            echo $e->getTraceAsString();

        }
    }

    public static function tirarCaracterEspecial($string) {

        // matriz de entrada
        $what = array( 'ä','ã','à','á','â','ê','ë','è','é','ï','ì','í','ö','õ','ò','ó','ô','ü','ù','ú','û','Ã','À','Á','É','Í','Ó','Ú','ñ','Ñ','ç','Ç','-','(',')',',',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','ª','º' );

        // matriz de saída
        $by   = array( 'a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','A','E','I','O','U','n','n','c','C','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_');

        // devolver a string
        return str_replace($what, $by, $string);
    }

    public static function referencia()
    {
        $random_string = chr(rand(65,90)) . rand(65,90) . chr(rand(65,90)) . chr(rand(65,90)) . rand(65,90) . chr(rand(65,90));
        return $random_string ;
    }

    public static function formata_cpf_cnpj($cpf_cnpj){
        /*
            Pega qualquer CPF e CNPJ e formata
            CPF: 000.000.000-00
            CNPJ: 00.000.000/0000-00
        */
        ## Retirando tudo que não for número.
        $cpf_cnpj = preg_replace("/[^0-9]/", "", $cpf_cnpj);
        $tipo_dado = NULL;
        if(strlen($cpf_cnpj)==11){
            $tipo_dado = "cpf";
        }
        if(strlen($cpf_cnpj)==14){
            $tipo_dado = "cnpj";
        }
        switch($tipo_dado){
            default:
                $cpf_cnpj_formatado = "Não foi possível definir tipo de dado";
            break;
    
            case "cpf":
                $bloco_1 = substr($cpf_cnpj,0,3);
                $bloco_2 = substr($cpf_cnpj,3,3);
                $bloco_3 = substr($cpf_cnpj,6,3);
                $dig_verificador = substr($cpf_cnpj,-2);
                $cpf_cnpj_formatado = $bloco_1.".".$bloco_2.".".$bloco_3."-".$dig_verificador;
            break;
    
            case "cnpj":
                $bloco_1 = substr($cpf_cnpj,0,2);
                $bloco_2 = substr($cpf_cnpj,2,3);
                $bloco_3 = substr($cpf_cnpj,5,3);
                $bloco_4 = substr($cpf_cnpj,8,4);
                $digito_verificador = substr($cpf_cnpj,-2);
                $cpf_cnpj_formatado = $bloco_1.".".$bloco_2.".".$bloco_3."/".$bloco_4."-".$digito_verificador;
            break;
        }
        return $cpf_cnpj_formatado;
    }

}
