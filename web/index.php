<?php

require __DIR__ .'/includes/app.php';
use \App\Http\Router;



$obRouter = new Router(URL);

// echo '<pre>';
// print_r($obRouter);
// echo '</pre>';
// exit;

//INCLUI AS ROTAS
include __DIR__.'/routes/pages.php';


//IMPRIME O RESPONSE DA ROTA
$obRouter->run()
         ->sendResponse();
?>