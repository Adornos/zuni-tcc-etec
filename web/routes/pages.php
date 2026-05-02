<?php

use \App\Http\Response;
use \App\Controller\Pages;

//ROTA Home
$obRouter->get('/', [
    function () {
        return new Response(200, Pages\Home::getHome());
    }
]);

//ROTA sobre
$obRouter->get('/sobre', [
    function () {
        return new Response(200, Pages\About::getAbout());
    }
]);

//ROTA alunoMatricula
$obRouter->get('/matricula', [
    function () {
        return new Response(200, Pages\StudentEnroll::getStudentEnroll());
    }
]);

//ROTA alunoMatricula (insert)

$obRouter->post('/matricula', [
    function ($request) {
        return new Response(200, Pages\StudentEnroll::insertEnroll($request));
    }
]);

//ROTA DINÂMICA TESTE
$obRouter->get('/pagina/{idPagina}/{acao}', [
    function ($idPagina, $acao) {
        return new Response(200, 'Pagina '.$idPagina.' - '.$acao);
    }
]);

?>