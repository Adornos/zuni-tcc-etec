<?php

//INICIALIZAÇÃO DAS COISAS COMUNS NO PROJETO
//Inicialização do composer - Autoload
require __DIR__.'/vendor/autoload.php';

//Carregamento das variaveis ambientes
\App\Common\Enviroment::load(__DIR__);

use \App\Http\Router;
use \App\Utils\View;
use \App\Common\Enviroment;

Enviroment::load(__DIR__);


// Define a contante URL
define('URL', getenv('URL'));

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