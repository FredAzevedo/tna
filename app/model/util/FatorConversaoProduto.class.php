<?php
/**
 * Classe para converter quantitativos de unidades de produtos de entradas (Compras)
 * Author: Fred Azevedo
 * **/
class FatorConversaoProduto{
    
    public static function fator( $tipo, $fator, $qtd ){
        
        if($tipo == 'M')
        {
            $totalizador = $qtd * $fator;
            return $totalizador;
        }
        
        if($tipo == 'D')
        {
            $totalizador = $qtd / $fator;
            return $totalizador;
        }
    }
}