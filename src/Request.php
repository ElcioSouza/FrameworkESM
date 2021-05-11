<?php
namespace src;


class Request {
    
    protected $files; // coleta arquivo enviados
    protected $base; // é a URI original
    protected $port;
    protected $uri; // é a forma que vamos passar na rotas
    protected $method; // captura se é ‘post’,’get’, etc
    protected $protocol; // identifica se o protocolo que estamos usando é HTTPS/HTTP  
    protected $data = []; // coleta os dados enviados
    
    public function __construct() {
        
        $this->base = $_GET["uri"] ?? "/";
        $this->port = $_SERVER['SERVER_PORT'];
        $this->method = strtolower($_SERVER["REQUEST_METHOD"]);
        $this->protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
        $this->setData();
        $this->uri_parse();
        
        if(count($_FILES) > 0) {
          //  $this->setFile();
        }
    }
    
  protected function setData() {
        switch($this->method) {
            case "post":
                $json_response = $this->request_body();
                $this->data = $json_response ?? $_POST;
             break;
            case "get":
                $json_response = $this->request_body();
                $this->data = $json_response ?? $_GET;
             break;
            case "head":
            case "put":
            case "delete":
            case "options":
            case "patch":
            $json_response = $this->request_body();
            $this->data = $json_response;
        }
    }
    
    protected function sefFiles() {
        foreach($_FILES as $key => $value) {
            $this->files[$key] = $value;
        }    
    }
    
    protected function request_body() {
         $json = file_get_contents('php://input');
         $json_response  = json_decode($json, true)??null;
         return $json_response;
    }
    
    
    public function base() {
        return $this->base;
    }
    
    public function uri() {
        return $this->uri_parse();
    }
    
    public function method() {
        return $this->method;
    }

    public function param(){
 
        // Capturamos toda a query string
        $queryString = filter_input(INPUT_SERVER, 'QUERY_STRING');

        /**
         * Realizamos o parse da string
         * 
         * Passamos a query string como primeiro parâmetro. 
         * Como segundo parâmetro informamos a variável que desejamos
         * inserir o resultado.
         */
        parse_str($queryString, $parseQueryString);

        // Resultado da query string
        // var_dump($queryString);

        // Resultado do parse da query string
         echo '<pre>';
      //   var_dump($parseQueryString);
         echo '</pre>';  
     }   
    
    public function getBody() {
        return $this->data;
    }
    
    public function __isset($key) {
        return isset($this->data[$key]);
    }
    
    public function __get($key) {
        
        if(isset($this->data[$key])) {
            
            return $this->data[$key];
            
        }
    }
    
    public function hasFile($key) {
        
        return isset($this->files[$key]);
    }
    
    public function __file($key) {
        
        if(isset($this->files[$key])) {
            
            return $this->files[$key];
        }
    }

    public function hostLink() {

         $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
         $server = $_SERVER['SERVER_NAME'] . ':';
         $port = $this->port; 
         $uri = [];
        
         foreach(array_filter(explode('/', $_SERVER['REQUEST_URI'])) as $key => $value) {
            if($value == 'public') {
                $uri[] = $value;
                break;
            }
            $uri[] = $value;
         }
         
         $uri = implode('/', array_filter($uri)) . '/';
         return $protocol . $server . $port . $uri;
    }
    public function getHtmlRootFolder(string $root = '/var/www/') {

    $ret = str_replace(' ', '', $_SERVER['DOCUMENT_ROOT']);
    $ret = rtrim($ret, '/') . '/';
    if (!preg_match("#".$root."#", $ret)) {
      
      $root = rtrim($root, '/') . '/'; 
      $root_arr = explode("/", $root);
      $pwd_arr = explode("/", getcwd()); 
       
      $ret = $root . $pwd_arr[count($root_arr) - 4];
    
    }

        return (preg_match("#".$root."#", $ret)) ? rtrim($ret, '/') . '/' : null;
    }

    public function host() {
        $raiz = explode("\\",getcwd());
        
        $host = (isset($_SERVER["HTTPS"]) ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST']);
           if(preg_match("/^http(s)?:\/\/(www\.)?[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/",$host,$rsHost)) {
                 return $rsHost[0]."/";
         } else {
        
             return $host;
         }
     }
    
     public function public() {
        
        $dir = str_replace("src","public",__DIR__);
        $dir =  str_replace('\\', '/', $dir);
        $document_root =  str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        $dir =  str_replace($document_root,"",$dir);
        
        return $this->host().$dir;
        
     }
    
    public function root() {
        
        $dir = str_replace("src","public",__DIR__);
        $dir =  str_replace('\\', '/', $dir);
        $document_root =  str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        $dir =  str_replace($document_root,"",$dir);
        
        return $this->host().$dir;  
     }
    protected function uri_parse() {

        $dir = str_replace("src","",__DIR__);
        $dir =  str_replace('\\', '/', $dir);
        $document_root =  str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
        $dir =  str_replace($document_root,"",$dir);
        $dir = str_replace($dir,'/',$_SERVER["REQUEST_URI"]);    
        return $dir;
    }
}