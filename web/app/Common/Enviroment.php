<?php


namespace App\Common;

class Enviroment{    
    /**
     * Carrega as variaveis ambientes
     * @param  string $dir Caminho absoluto do arquivo de variáveis
     */
    public static function load($dir){
        //VERIFICA A EXISTENCIA DO ARQUIVO .env
        if (!file_exists($dir.'\.env')) {
            return false;
        }

        //DEFINE AS VARIÁVEIS DE AMBIENTE
        $lines = file($dir.'\.env');
        foreach ($lines as $line) {
            putenv(trim($line));
        }
    }
}

?>