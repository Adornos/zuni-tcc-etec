<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Login extends Page{
    /**
     * Retorna (view) da sobre
     * @return string
     */
    public static function getLogin(){

        // retorna view da sobre

        $content = View::render('pages/login', array_merge([], Organization::getOrganizationData()));

            //retorna a view da página
            return parent::getPage('Login', $content);
    }
    

}

?>