<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Home extends Page{
    /**
     * Retorna (view) da home
     * @return string
     */
    public static function getHome(){
        // retorna view da home

        $content = View::render('pages/home', array_merge([],Organization::getOrganizationData()));

            //retorna a view da página
            return parent::getPage('Zuni', $content);
    }
    

}

?>