<?php

use Carbon\Carbon;
use Carbon\CarbonImmutable;

/**
 * Class FormaPagamento
 */
class FormaPagamento {

    protected $total_conta;
    protected $data_vencimento;
    protected $regra;



    // métodos publicos, para manter compatibilidade
    public $numero_parcelas;
    public $valor_parcela;
    public $vencimentos;
    public $vencimentobd;



    /**
     * FormaPagamento constructor.
     *
     * @param $valor number|string Valor total a ser parcelado
     * @param $texto string regras de parcelamento. As regras são separadas em 3 parametros
     *      dia do vencimento: que pode ser "vencimento" ou especificar um dia.
     * @param $vencimento
     *
     * @throws Exception
     */
    public function __construct($valor, $texto, $vencimento) {
        $this->total_conta = $valor;
        $this->data_vencimento = $vencimento;
        if (!$this->regra_valida($texto)) {
            throw new Exception('A regra "'. $texto .'" não é valida');
        }
        $this->regra = explode(';', $texto);;
        $this->numero_parcelas = $this->getNumeroParcela();
        $this->valor_parcela = $this->getValorParcela();
        // chamando o método para gerar as parcelas e manter a compatibilidade.
        $this->getVencimentos();
    }

    /**
     * Caso o dia inicia tenha sido especificado nas regras, retorna ele
     * caso contrato, retorna false
     *
     * @return bool|int
     */
    private function getDay() {
        if (is_numeric($this->regra[0])) {
            return (int)$this->regra[0];
        }
        return false;
    }

    /**
     * Função para retornar o multiplicador mensal de acordo com
     * o que foi solicitado no contrutor da classe
     *
     * @return int
     */
    private function getQuantidadeMesAdd() {
        switch ($this->regra[2]) {
            case 'mensal': return 1;
            case 'bimestral': return 2;
            case 'trimestral': return 3;
            default: return 1;
        }
    }
 
    
    public function __destruct() {}
 

    public function getVencimentos() {

        // cria uma instancia imutavel do carbon com a data de vencimento
        $data_inicio = CarbonImmutable::parse($this->data_vencimento);

        // se um dia foi especificado, atribui ele no vencimento
        if ($this->getDay() !== false) {
            $data_inicio = $data_inicio->setDay($this->getDay());
        }

        // recebe o multiplicado de mes.
        $quantidade_mes_add = $this->getQuantidadeMesAdd();

        $datas = [];
        $this->vencimentos = [];
        $this->vencimentobd = [];

        // loop com a quantidade de parcelas para montar as datas
        for ($parcela = 0; $parcela < $this->getNumeroParcela(); $parcela++) {

            // adiciona a quantidade de mes multiplicando a parcela pela quantidade.
            // se for mensal, a quantidade_mes_add é 1, se for trimestral, é 3 e assim por diante.
            // A parcela é (n - 1) ou seja, a parcela 1, na variavel $parcela é 0.
            // então, na 3° parcela (valor variavel = 2) de um recebimento trimestral,
            // a data será de 6 meses a partir do vencimento
            $datas[] = $data_inicio->addMonthsNoOverflow($quantidade_mes_add * $parcela)->format('Y-m-d');
            $this->vencimentobd[] = $data_inicio->addMonthsNoOverflow($quantidade_mes_add * $parcela)->format('Y-m-d');
            $this->vencimentos[] = $data_inicio->addMonthsNoOverflow($quantidade_mes_add * $parcela)->format('d/m/Y');

        }

        return $datas;

    }

    /**
     * Retorna a quantidade de parcelas
     * @return int
     */
    public function getNumeroParcela() {
        return $this->regra[1];
    }

    /**
     * Retorna o valor da parcela
     *
     * @return float|int
     */
    public function getValorParcela() {
        return ($this->total_conta / $this->numero_parcelas);
    }

    /**
     * Verifica se a regra é valida
     * @param $regra
     */
    private function regra_valida($regra) {
        $arr = explode(';', $regra);
        // se não tiver os 3 campos definidis
        if (!isset($arr[0]) || !isset($arr[1]) || !isset($arr[2])) {
            return false;
        }

        // verifica se o texto é vencimento ou se é numero
        if ($arr['0'] != 'vencimento' && !is_numeric($arr[0])) {
            return false;
        }

        // verifica se o numero está entre 1 e 31.
        if (is_numeric($arr[0]) && !($arr[0] > 0 && $arr[0] <= 31)) {
            return false;
        }

        // verifica se a quantidade de parcela é numero e manior que 1
        if (!is_numeric($arr[1]) || $arr[1] < 1) {
            return false;
        }

        // verifica o tipo, se é mensal ou trimestral
        if (!in_array($arr[2], ['mensal', 'trimestral'])) {
            return false;
        }

        return true;
    }

}
