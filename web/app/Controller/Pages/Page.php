<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Page{


    /**
     * Renderiza o Header
     * @return string
     */
    private static function getHeader(){
        return View::render('pages/header', Organization::getOrganizationData());
    }
        
    /**
     * Renderiza o footer
     * @return string
     */
    private static function getFooter(){
        return View::render('pages/footer', Organization::getOrganizationData());
    }
    
    /**
     * Retorna (view) duma página
     * @return string
     */
    public static function getPage($title = '', $content = '', $namespace = '', $header = null, $footer = null){
    return View::render('pages/page', [
        'title' => $title,
        'namespace' => $namespace,
        'header' => $header ?? self::getHeader(),
        // 'header' => '',

        'content'=> $content,
        'footer' => $footer ?? self::getFooter(),
        ]);
    }

}

?>