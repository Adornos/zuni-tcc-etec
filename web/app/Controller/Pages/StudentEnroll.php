<?php

namespace App\Controller\Pages;

use \App\Http\Request;
use \App\Utils\View;
use \App\Model\Entity\StudentEnroll as EntityStudentEnroll;

class StudentEnroll extends Page{    

    private static function getSeriesOptions(){
        $seriesOptions = '';

        $results = EntityStudentEnroll::getSeries();

        foreach ($results as $key => $value) {
            $seriesOptions .= '<option value=' . $key . '>' . $value . '</option>';
        }

        return $seriesOptions; 
    }

    /**
     * Retorna View da pagina de matricular o aluno
     * @return string
     */
    public static function getStudentEnroll(){

        //Retorna View da Página de matricular o aluno
        $content = View::render('pages/enroll', [
            'serieOptions' => self::getSeriesOptions(),
        ]);

        // Retorna a View da página renderizada.
        return parent::getPage('Matricular Aluno', $content);
    }
    
    /**
     * Solicita a matricula
     *
     * @param Request $request
     * @return string
     */
    public static function insertEnroll($request){
        //Dados do Post
        $postVars = $request->getPostVars();
        //Nova instancia de cadastro
        $obEnroll = new EntityStudentEnroll;
        $obEnroll->hydratate($postVars);
        $obEnroll->cadastrar();

        return self::getStudentEnroll();


    }

}