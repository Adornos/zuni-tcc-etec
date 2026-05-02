<?php


namespace App\Http;

class Request{    
    /**
     * Metodo HTTP da requisição
     * @var string
     */
    private $httpMethod;
    
    /**
     * Uri da Pagina
     * @var string
     */
    private $uri;
    
    /**
     * Parametros da URL ($_GET)
     * @var array
     */
    private $queryParams = [];
    
    /**
     * Variaveis recebidas no POST ($_POST)
     *
     * @var array
     */
    private $postVars = [];
    
    /**
     * Cabeçalho da requisição
     *
     * @var array
     */
    private $headers = [];

    public function __construct(){
        $this->queryParams  = $_GET ?? [];
        $this->postVars     = $_POST ?? [];
        $this->headers      = getallheaders();
        $this->httpMethod   = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->uri          = $_SERVER['REQUEST_URI'] ?? '';
    }
    
    /**
     * Retorna o método HTTP da requisição
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Retorna a URI da requisição
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
    
    /**
     * Retorna os headers da requisição
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * Retorna os parametros da URL da requisição
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Retorna as variaveis POST da requisição
     *
     * @return array
     */
    public function getPostVars()
    {
        return $this->postVars;
    }
}


?>