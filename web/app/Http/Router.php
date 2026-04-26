<?php


namespace App\Http;

use \Closure;
use \Exception;
use \ReflectionFunction;
class Router{    
    /**
     * URL completa do projeto (root)
     *
     * @var string
     */
    private $url = '';
    
    /**
     * Prefixo de todas as rotas
     *
     * @var string
     */
    private $prefix = '';
    
    /**
     * Índice de rotas
     *
     * @var array
     */
    private $routes = [];
    
    /**
     * Instancia o Request
     *
     * @var Request
     */
    private $request;
    
    /**
     * Inicia a classe
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->request = new Request();
        $this->url     = $url;
        $this->setPrefix();
    }
    
    /**
     * Define o prefixo das rotas
     *
     * @return void
     */
    private function setPrefix(){
        // Informações da URL
        $parseURL = parse_url($this->url);
        // Define o prefixo
        $this->prefix = $parseURL['path'] ?? '';
    }
    
    
    /**
     * addRoutes
     * @param  string $method
     * @param  string $route
     * @param  array $params
     */
    private function addRoute($method, $route, $params = []){

        //VALIDAÇÃO DE PARAMETROS
        foreach ($params as $key => $value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
            }

        //VARIÁVEIS DA ROTA
        $params['variables'] = [];

        //PADRÃO DE VALIDAÇÃO DAS VARIÁVEIS DAS ROTAS
            $patternVariable = '/{(.*?)}/';
            if (preg_match_all($patternVariable, $route, $matches)){
                $route = preg_replace($patternVariable,'(.*?)', $route);
                $params['variables'] = $matches[1];
            }

        //Padrão de validação
        $patternRoute = '/^'.str_replace('/', '\/', $route).'$/';



        //Adicionar Rota a Classe
        $this->routes[$patternRoute][$method] = $params;
        }
    }

    /**
     * Definir rota de GET
     * @param  string $route
     * @param  array $params
     */
    public function get($route, $params = []){
        return $this->addRoute('GET', $route, $params);
    }
            
    /**
     * Definir rota de POST
     * @param  string $route
     * @param  array $params
     */
    public function post($route, $params = []){
        return $this->addRoute('POST', $route, $params);
    }
            
    /**
     * Definir rota de PUT
     * @param  string $route
     * @param  array $params
     */
    public function put($route, $params = []){
        return $this->addRoute('PUT', $route, $params);
    }
            
    /**
     * Definir rota de DELETE
     * @param  string $route
     * @param  array $params
     */
    public function delete($route, $params = []){
        return $this->addRoute('DELETE', $route, $params);
    }

            
    /**
     * Retorna a URI sem o prefixo
     *
     * @return string
     */
    private function getUri(){

    //URI da request
        $uri = $this->request->getUri() ?? '';
    //UREI Fatiada com prefixo
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        return end($xUri);
    }

    /**
     * Retorna os dados da rota atual
     *
     * @return array
     */
    private function getRoute()
    {
        //URI
        $uri = $this->getUri();

        //METHOD
        $httpMethod = $this->request->getHttpMethod();

        //VALIDA AS ROTAS
        foreach($this->routes as $patternRoute=>$methods){
            //VRIFICA A CONCORDACIA DA URI COM O PADRÃO
            if(preg_match($patternRoute, $uri, $matches)){
                //VERIFICA O MÉTODO
                if(isset($methods[$httpMethod])){
                    // REMOVER A PRIMEIRA POSIÇÃO
                    unset($matches[0]);

                    //CHAVES
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    // RETORNO DOS PARÂMETROS DA ROTA
                    return $methods[$httpMethod];
                }
                //METODO NÃO DEFINIDO OU PERMITIDO
                throw new Exception('Método não permitido', 405);
            }
        }
        //URL não encontrada
        throw new Exception('URL não encontrada', 404);
    }

    /**
     * Executa a rota atual
     *
     * @return Response
     */
    public function run(){
        try {

            //OBTEM A ROTA ATUAL
            $route = $this->getRoute();

            // VERIFICA CONTROLADOR
            if (!isset($route['controller'])) {
                throw new Exception("A URL não pôde ser processada", 500);
            }

            //ARGUMENTOS DA FUNÇÃO
            $args = [];

            //REFLECTION
            $reflection = new ReflectionFunction($route["controller"]);
            foreach ($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            //RETORNA A EXECUÇÃO DA FUNÇÃO
            return call_user_func_array($route['controller'], $args);

        }catch (Exception $e) {
            return new Response($e->getCode(), $e->getMessage());
        }
    }
}


?>