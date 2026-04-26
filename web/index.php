<?php

require __DIR__.'/vendor/autoload.php';

use \App\Http\Router;
use \App\Utils\View;

define('URL', 'http://zuni.tcc');

$obRouter = new Router(URL);

//INCLUI AS ROTAS
include __DIR__.'/routes/pages.php';

//Define valor padrão das variáveis
View::init([
    'URL' => URL
]);

//IMPRIME O RESPONSE DA ROTA
$obRouter->run()
         ->sendResponse();
?>