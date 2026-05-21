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

    private static function getHeader()
    {
        return View::render('pages/professor/header');
    }

    private static function getAside()
    {
        return View::render('pages/professor/aside');
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> ce4075f (feat: Implement teacher panel features including profile, dashboard, and schedule views; update navigation and styles)
    private static function getProfile()
    {
        return View::render('pages/professor/profile');
    }
<<<<<<< HEAD
=======
>>>>>>> d20d959 (feat: Enhance professor dashboard with new panel routing and improved schedule layout)
=======
>>>>>>> ce4075f (feat: Implement teacher panel features including profile, dashboard, and schedule views; update navigation and styles)

    private static function getDashboard()
    {
        return View::render('pages/professor/dashboard');
    }

    private static function getSchedule()
    {
        return View::render('pages/professor/schedule');
    }

<<<<<<< HEAD
<<<<<<< HEAD
    private static array $panels = [
        'profile' => 'getProfile',
<<<<<<< HEAD
=======
    private static array $panels = [
>>>>>>> d20d959 (feat: Enhance professor dashboard with new panel routing and improved schedule layout)
=======
>>>>>>> ce4075f (feat: Implement teacher panel features including profile, dashboard, and schedule views; update navigation and styles)
        'dashboard' => 'getDashboard',
        'schedule' => 'getSchedule',
    ];


<<<<<<< HEAD
=======
>>>>>>> bb422a5 (feat: Update professor dashboard with schedule functionality and styling)
=======
>>>>>>> d20d959 (feat: Enhance professor dashboard with new panel routing and improved schedule layout)

    /**
     * Retorna o painel do professor e aloca sua página em específico
     *
     * @param  string $page
     * @return string
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public static function getPainelProfessor($page)
=======
    public static function getPainelProfessor($page = 'dashboard')
>>>>>>> bb422a5 (feat: Update professor dashboard with schedule functionality and styling)
=======
    public static function getPainelProfessor($page)
>>>>>>> d20d959 (feat: Enhance professor dashboard with new panel routing and improved schedule layout)
    {

        if (!key_exists($page, self::$panels)) {
            throw new \Exception('Painel '. $page .' não existe', 404);
        }

        // retorna view da sobre
        $content = View::render('pages/professor/index', array_merge(
            [
                'aside' => self::getAside(),
<<<<<<< HEAD
<<<<<<< HEAD
                'content' => self::{self::$panels[$page]}(),
=======
                'content' => self::getSchedule()
>>>>>>> bb422a5 (feat: Update professor dashboard with schedule functionality and styling)
=======
                'content' => self::{self::$panels[$page]}(),
>>>>>>> d20d959 (feat: Enhance professor dashboard with new panel routing and improved schedule layout)
            ],
            Organization::getOrganizationData(),
            self::$dados_professor
        ));

        //retorna a view da página
        return parent::getPage('Zuni', $content, 'teacher-panel', self::getHeader(), '');
    }
    
    /**
     * Metodo que requisita as informações do professor para visualização no painel (perfil, dashboard, agenda, etc)
     *
     * @param  mixed $request
     */
    public static function requestProfessorInfo($request)
    {

        $postVars = $request->getPostVars();
    
    }
<<<<<<< HEAD

=======
>>>>>>> ce4075f (feat: Implement teacher panel features including profile, dashboard, and schedule views; update navigation and styles)



}

?>