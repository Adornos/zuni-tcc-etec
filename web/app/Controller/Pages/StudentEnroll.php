<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class StudentEnroll extends Page{    
    /**
     * Retorna View da pagina de matricular o aluno
     * @return string
     */
    public static function getStudentEnroll(){
        //Retorna View da Página de matricular o aluno
        $content = View::render('pages/studentEnroll');

        // Retorna a View da página renderizada.
        return parent::getPage('Matricular Aluno', $content);
    }


}