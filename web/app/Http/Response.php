<?php


namespace App\Http;

class Response{
    
    /**
     * Codigo do status
     * @var int
     */
    private $httpCode = 200;    
    /**
     * Cabeçalho do Response
     * @var array
     */
    private $headers = [];
    
    /**
     * Tipo de Conteúdo que está sendo retornado
     * @var string
     */
    private $contentType = 'text/html';
    
    /**
     * Conteudo do response
     * @var mixed
     */
    private $content;

    
    /**
     * Iniciar a classe e definir valores
     *
     * @param  integer $httpCode
     * @param  mixed $content
     * @param  string $contentType
     * @return void
     */
    public function __construct($httpCode, $content, $contentType = 'text/html'){
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->setContentType($contentType);
    }
    
    /**
     * Alterar o content type de response
     *
     * @param  mixed $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
    }
    
    /**
     * Adicionar um registro no cabeçalho do response
     *
     * @param  string $key
     * @param  string $value
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }
    
    /**
     * Envia os headers para o navegador
     */
    private function sendHeaders(){
        //STATUS
        http_response_code($this->httpCode);

        //ENVIAR
        foreach ($this->headers as $key => $value) {
            header($key .''. $value);
        }
    }
    
    /**
     * Envia a resposta para o usuario
     */
    public function sendResponse()
    {
        //Envia os headers
        $this->sendHeaders();

        //Imprime o conteúdo
        switch ($this->contentType) {
            case 'text/html':
                echo $this->content;
                exit;
        }
    }
}


?>