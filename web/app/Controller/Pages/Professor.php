<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Professor extends Page
{

    private static $dados_professor = [
        "id_professor" => 1,
        "nome" => "Carlos Henrique Almeida",
        "email" => "carlos.almeida@escola.com",
        "telefone" => "(11) 98765-4321",
        "cpf" => "12345678901",
        "sexo" => "M",
        "curriculo" => "Professor de Matemática com mais de 10 anos de experiência em ensino médio e preparatórios para vestibulares."
    ];


    private static function getHeader()
    {
        return View::render('pages/professor/header');
    }

    private static function getAside()
    {
        return View::render('pages/professor/aside');
    }


    private static function getDashboard()
    {
        return View::render('pages/professor/dashboard');
    }

    private static function getSchedule()
    {
        return View::render('pages/professor/schedule');
    }

    private static array $panels = [
        'dashboard' => 'getDashboard',
        'schedule' => 'getSchedule',
    ];



    /**
     * Retorna o painel do professor e aloca sua página em específico
     *
     * @param  string $page
     * @return string
     */
    public static function getPainelProfessor($page)
    {

        if (!key_exists($page, self::$panels)) {
            throw new \Exception('Painel '. $page .' não existe', 404);
        }

        // retorna view da sobre
        $content = View::render('pages/professor/index', array_merge(
            [
                'aside' => self::getAside(),
                'content' => self::{self::$panels[$page]}(),
            ],
            Organization::getOrganizationData(),
            self::$dados_professor
        ));

        //retorna a view da página
        return parent::getPage('Zuni', $content, 'teacher-panel', self::getHeader(), '');
    }



}

?>