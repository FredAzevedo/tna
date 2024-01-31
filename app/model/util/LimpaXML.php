<?php

class LimpaXML
{
    public static function __limpaString($texto){
        $aFind = array('&','á','à','ã','â','é','ê','í','ó','ô','õ','ú','ü','ç','Á','À','Ã','Â','É','Ê','Í','Ó','Ô','Õ','Ú','Ü','Ç');
        $aSubs = array('e','a','a','a','a','e','e','i','o','o','o','u','u','c','A','A','A','A','E','E','I','O','O','O','U','U','C');
        $novoTexto = str_replace($aFind,$aSubs,$texto);
        $novoTexto = preg_replace("/[^a-zA-Z0-9 @,-.;:]/", "", $novoTexto);
        return $novoTexto;
    }
}