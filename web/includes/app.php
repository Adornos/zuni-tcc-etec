<?php

//INICIALIZAÇÃO DAS COISAS COMUNS NO PROJETO
//Inicialização do composer - Autoload
require __DIR__.'/../vendor/autoload.php';

use \App\Utils\View;
use \App\Common\Enviroment;
use \App\Db\Database;

//Carregamento das variaveis ambientes
Enviroment::load(__DIR__.'/../');


//DEFINE AS CONFIGURAÇÕES DO BANCO
Database::config(
    getenv('DB_HOST'),
    getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_PORT')
);

// Define a contante URL
define('URL', getenv('URL'));

//Define valor padrão das variáveis
View::init([
    'URL' => URL
]);