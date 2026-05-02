<?php

namespace App\Utils;

class View{

    /**
     * Variaveis padrões da View
     * @var array
     */
    private static $vars = [];
    
    /**
     * Definir os dados iniciais da classe
     * @param  array $vars
     */
    public static function init($vars = []){
        self::$vars = $vars;
    }
        
    /**
     * Retorna o conteudo de um view
     *
     * @param  string $view
     * @return string
     */
    private static function getContentView($view){

        $file = __DIR__.'/../../resources/view/'.$view.'.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    /**
     * Retornar o conteudo da view já renderizado
     *
     * @param  string $view
     * @param array $vars (string/numeric)
     * @return string
     */

    public static function render($view, $vars = []){
        // Conteudo da view
        $contentView = self::getContentView($view);

        // MERGE VARIÁVEIS DA VIEW
        $vars = array_merge(self::$vars, $vars);

        //Chaves das variaveis
        $keys = array_keys($vars);
        $keys = array_map(function ($item) {
            return '{{'.$item.'}}';
        }, $keys);
        

        // Retorna o conteudo renderizado, substituindo as variaveis
        return str_replace($keys,array_values($vars), $contentView);
    }

}

?>