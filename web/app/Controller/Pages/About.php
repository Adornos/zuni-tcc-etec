<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class About extends Page{
    /**
     * Retorna (view) da sobre
     * @return string
     */
    public static function getAbout(){

        // retorna view da sobre

        $content = View::render('pages/about', array_merge([],Organization::getOrganizationData()));

            //retorna a view da página
            return parent::getPage('Zuni', $content);
    }
    

}

?>