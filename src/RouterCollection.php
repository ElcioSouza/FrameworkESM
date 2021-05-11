<?php
namespace src;

//Coleção de roteadores - informações sobre a rota
/* 
* @description: A classe RouterCollection cria um objeto de 
* coleções de rotas que serão adicionadas quando 
* são declaradas no arquivo routes.php
*/
class RouterCollection {
    protected $routes_post = [];    
    protected $routes_get = [];    
    protected $routes_put = [];    
    protected $routes_delete = [];  
    protected $route_names = [];  

     // Triagem do tipo de rota
    public function add($request_type, $pattern, $callback) {
        switch($request_type) {
            case "post":
                return $this->addPost($pattern,$callback);
                break;
            case "get":
              
                return $this->addGet($pattern,$callback);
                break;
            case "put":
                return $this->addPut($pattern,$callback);
                break;
            case "delete":
                 return $this->addDelete($pattern,$callback);
                 break;
            default:
                throw  new \Exception("Tipo de requisição não implementada");

        } 
    }
    
    /**
    * @description: Este método sera usado pela classe roteadora para obter a rota
    *
    */ 
    public function where($request_type,$pattern) {
        switch($request_type) {
            case "post":
              return $this->findPost($pattern);
              break;
            case "get":
              return $this->findGet($pattern);
              break;  
            case "put":
              return $this->findPut($pattern);
              break;    
           case "delete":
              return $this->findDelete($pattern);
              break; 
           default:
                throw  new \Exception("Tipo de requisição não implementada");    
        }
        
    }
    
    protected function parseUri($uri) {
        return implode("/",array_filter(explode("/",$uri)));
    }
    
    protected function findPost($pattern_sent) {
        
        $pattern_sent = $this->parseUri($pattern_sent);
        
        foreach($this->routes_post as $pattern => $callback) {
          
            if(preg_match($pattern,$pattern_sent,$pieces))  {
                return (object) ["callback" => $callback, "uri" => $pieces];
            }
        }
        
        return false;
    }
    
    protected function findGet($pattern_sent) {
        
        $pattern_sent = $this->parseUri($pattern_sent);
        
        foreach($this->routes_get as $pattern => $callback) {
          if(preg_match($pattern,$pattern_sent,$pieces)) {
            return (object) ["callback" => $callback, "uri" => $pieces];
          }
        }
        
        return false;
    }
    
    protected function findPut($pattern_sent) {
            
            $pattern_sent = $this->parseUri($pattern_sent);
        
        foreach($this->routes_put as $pattern => $callback) {
          if(preg_match($pattern,$pattern_sent,$pieces)) {
            return (object) ["callback" => $callback, "uri" => $pieces];
          }
        }
            
        return false;
    }
    
    protected function findDelete($pattern_sent) {
        
        $pattern_sent = $this->parseUri($pattern_sent);
        
        foreach($this->routes_delete as $pattern => $callback) {
            if(preg_match($pattern,$pattern_sent,$pieces)) { // pieces e o resultado
                return (object) ["callback" => $callback, "uri" => $pieces];
            }
        }
        
        return false;
    }

    // Este método está armazenando o padrão como um padrão regex
    protected function definePattern($pattern) {

        $pattern = implode('/', array_filter(explode('/', $pattern)));
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';

        if (preg_match("/\{[A-Za-z0-9\_\-]{1,}\}/", $pattern)) {
            $pattern = preg_replace("/\{[A-Za-z0-9\_\-]{1,}\}/", "[A-Za-z0-9]{1,}", $pattern);
        }
        return $pattern;
    }
    
    /*
    * @description: responsável por entender o padrão quando chegar em forma de array.
    * Você poderá dar nome para rotas
    * poderão ser referenciados para tradução automática em views, controllers, etc.
    * redefinir o namespace do controller alvo
    * @param $pattern
    * @return Array
    */
    protected function parsePattern(array $pattern) {
        // Defina o padrão
        $result['set'] = $pattern['set'] ?? null;
        // Permite configurações de nome de rota
        $result['as'] = $pattern['as'] ?? null;
        // Permite nova definição de namespace para controladores
        $result['namespace'] = $pattern['namespace'] ?? null;
        return $result;
    }
    
    //Este método verifica os nomes mapeados para padrões e adicionados na propriedade $route_names
    //Se encontrar, devolve o padrão
    public function verifyAndAdd($name) { 
        $this->route_names[$name] = $name;
        return $this->route_names[$name] ?? false;
    }
    /*
    * @description:Esse metodo converter do padrão para URI
    * O método Convert entende o padrão recebido, recebe os parâmetros
    * e converte tudo para um formato de URI contendo
    * os valores no lugar dos espaços de meta-caracteres ou coringas
    * @param $pattern
    * @param $params
    * @return Array ou bollean
    */
    
    public function convert($pattern,$params) {

        if (!is_array($params)) {
            $params = array($params);
        }
        $positions = $this->toMap($pattern);

        if ($positions === false) {
            $positions = [];
        }
        $pattern = array_filter(explode( '/', $pattern));

        if ( count($positions) < count($pattern)) {
            $uri = [];
            foreach ($pattern as $key => $element) {

                if (in_array($key - 1, $positions)) {
                    $uri[] = array_shift($params);
                } else {
                    $uri[] = $element;
                }
            }
            return implode('/', array_filter($uri));
        }
        return false;
    }
    
    
    protected function addPost($pattern, $callback) {

        if(is_array($pattern)) {
            $settings = $this->parsePattern($pattern);
            $pattern = $settings['set'];
        } else {
            $settings = [];
        }
        
        $values = $this->toMap($pattern);

        $this->routes_post[$this->definePattern($pattern)] = 
        ['callback' => $callback, 'values' => $values,'namespace' => $settings['namespace'] ?? null];

        if(isset($settings['as'])) {
            $this->route_names[$settings['as']] = $pattern;
        }
        return $this;
    }

    
    protected function addGet($pattern, $callback) {
               
        if(is_array($pattern)) {
            $settings = $this->parsePattern($pattern);
            $pattern = $settings['set'];
        } else {
            $settings = [];
        }

        $values = $this->toMap($pattern);
        
        $this->routes_get[$this->definePattern($pattern)] = 
        ['callback' => $callback,'values' => $values,'namespace' => $settings['namespace'] ?? null];
                          
        if(isset($settings['as'])) {
            $this->route_names[$settings['as']] = $pattern; // receber todas as rotas route_names
        }
        return $this;
    }
    
    protected function addPut($pattern, $callback){

        if(is_array($pattern)) {
            $settings = $this->parsePattern($pattern);
            $pattern = $settings['set'];
        } else {
            $settings = [];
        }

        $values = $this->toMap($pattern);

        $this->routes_put[$this->definePattern($pattern)] = 
        ['callback' => $callback,'values' => $values,'namespace' => $settings['namespace'] ?? null];
                                                             
                                                             
        if(isset($settings['as']))
        {
            $this->route_names[$settings['as']] = $pattern;
        }
        return $this;

    }
    
    protected function addDelete($pattern, $callback){

        if(is_array($pattern)) {
            $settings = $this->parsePattern($pattern);
            $pattern = $settings['set'];
        } else {
            $settings = [];
        }

        $values = $this->toMap($pattern);

        $this->routes_delete[$this->definePattern($pattern)] = 
        ['callback' => $callback,'values' => $values,'namespace' => $settings['namespace'] ?? null];
                                                             
                                                             
        if(isset($settings['as'])) {
            $this->route_names[$settings['as']] = $pattern;
        }
        return $this;
    }
    /*
     *@description: Este método verifica os nomes mapeados para padrões
     *e adicionados na propriedade $route_names. 
     *Se a referencia for encontrada, devolve o padrão. 
     */
  public function isThereAnyHow($name) {
    $this->route_names[$name] = $name;
    return $this->route_names[$name] ?? false;
  }
    /*
    * @description: encontrar a posição na string.
    */
    protected function strposarray(string $haystack, array $needles, int $offset = 0) {
        $result = false;
        if(strlen($haystack) > 0 && count($needles) > 0) {
            foreach($needles as $element){
                $result = strpos($haystack, $element, $offset);
                if($result !== false) {
                    break;
                }
            }
        }
        return $result;
    }
    
    /*
    * @description: O segundo método toMap(), cria devolve um array com propriedades 
    * que mapeiam as posições dos coringas e meta-caracteres encontrados na rota. 
    * Por Exemplo: $router->get('teste/{id}', 'Controller@method');
    * @param {string} $pattern
    * @return array or bollean
    */
    protected function toMap($pattern) {
    
        $result = [];
    
        $needles = ['{', '[', '(', "\\"];
    
        $pattern = array_filter(explode('/', $pattern));
    
        foreach($pattern as $key => $element) {
            $found = $this->strposarray($element, $needles);
    
            if($found !== false) {
                if(substr($element, 0, 1) === '{') {
                    $result[preg_filter('/([\{\}])/', '', $element)] = $key - 1;
                } else {
                    $index = 'value_' . !empty($result) ? count($result) + 1 : 1;
                    array_merge($result, [$index => $key - 1]);
                }
            }
        }
        return count($result) > 0 ? $result : false;
    }
}