<?php
namespace src;

use src\Request;
use src\Dispacher;
use src\RouterCollection;

class Router {
    
    protected $router_collection;
    
    public function __construct() {
        
        $this->router_collection = new RouterCollection();
        $this->dispacher = new Dispacher();
        
    }
    
    public function get($pattern,$callback) {
        $this->router_collection->add("get",$pattern,$callback);
        return $this;
    }
    
    public function post($pattern,$callback) {
        $this->router_collection->add("post",$pattern,$callback);
        return $this;
    } 
    
    public function put($pattern,$callback) {
        $this->router_collection->add("put",$pattern,$callback);
        return $this;
    }
    
    public function delete($pattern,$callback) {
        $this->router_collection->add("delete",$pattern,$callback);
    }
    /**
    *  @description: Este metodo é responsavel, por procurar na coleção onde está uma rota, que seja condizente, com aquele padrão.
     * O método find() sabe como obter uma rota por informar o método de 
     * requisição (post, get, put, delete) e a URI. Se encontrar uma rota definida, 
     * manda para o Dispacher. Senão, invoca o método que responde com erro 404.
    *
    */
    public function find($request_type,$pattern) {
        return $this->router_collection->where($request_type,$pattern);
    }
    
    protected function dispach($route, $params, $namespace = "App\\") {
        return $this->dispacher->dispach($route->callback, $params, $namespace);
    }
    
    protected function notFound() {
       http_response_code(404);
       require_once("404.php");
    }
    
    /**
    * @description: o método responsável por ajudar o Router
    * a informar os parâmetros corretamente para Dispacher. 
    * Este método getValues recebe um padrão, 
    * converte-o para array e usando as posições
    * salvas junto a * * * rota definida, faz a substituição
    * dos espaços pelos valores recebidos na requisição (request):
    * @param $pattern
    * @param $positions
    * @return array
    */
    protected function getValues($pattern, $positions) {
        $result = [];

        $pattern = array_filter(explode('/', $pattern));

        foreach($pattern as $key => $value) {
            if(in_array($key, $positions)) {
                $result[array_search($key, $positions)] = $value;
            }
        }
        return $result;
    }
    public function translate($name, $params) {
        $pattern = $this->router_collection->isThereAnyHow($name);
    
        if($pattern) {
          
            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $server = $_SERVER['SERVER_NAME'] . '/';
            $uri = [];

            foreach(array_filter(explode('/', $_SERVER['REQUEST_URI'])) as $key => $value) {
                if($value == 'public') {
                    $uri[] = $value;
                    
                    break;
                }
                $uri[] = $value;
                
            }
            $uri = implode('/', array_filter($uri)) . '/';
          
            return $protocol . $server . $uri . $this->router_collection->convert($pattern, $params);
        }
        return false;
    }
    
    public function resolve($request){
   
        $route = $this->find($request->method(), $request->uri());
        
        if($route) {
          
            $params = $route->callback['values'] ? $this->getValues($request->uri(), $route->callback['values']) : [];

            return $this->dispach($route, $params);
        }
        return $this->notFound();
    }
}