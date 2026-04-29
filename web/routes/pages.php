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
$obRouter->get('/alunoMatricula', [
    function () {
        return new Response(200, Pages\StudentEnroll::getStudentEnroll());
    }
]);

//ROTA DINÂMICA TESTE
$obRouter->get('/pagina/{idPagina}/{acao}', [
    function ($idPagina, $acao) {
        return new Response(200, 'Pagina '.$idPagina.' - '.$acao);
    }
]);

?>