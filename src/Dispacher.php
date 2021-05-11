<?php
namespace src;
//Expedidor - Esta Classe identifica se é uma função callback e se é controller passado
use src\Request;
use src\Response;
class Dispacher {
    /*
    * Este método,distinguir se o callback é de fato apenas um callback,
    * ou será um controller que precisa ser instanciado
    * Se for, ele fará testes para além de saber se é um objeto válido,
    * também saber se têm o método. 
    * Para fazer esse trabalho vai precisar da classe,ReflectionClass, 
    * que extrai informações de um classe fornecida como argumento.
    */ 
    public function dispach($callback, $params = [], $namespace = "App\\") {  
    
        $response = new Response();
        $request = new  Request();

        if(is_callable($callback['callback'])) {
            
            return call_user_func_array($callback['callback'], array($request,$response,$params));
        } elseif (is_string($callback['callback'])) {

            if(!!strpos($callback['callback'], '@') !== false) {

                if(!empty($callback['namespace'])) {
                    $callback["fold"] = "Controller";
                    $namespace = $callback['namespace'].$callback["fold"];
                }
                $callback["folder"] = "Controllers\\";
                $callback['callback'] = explode('@', $callback['callback']);
                $controller = $namespace.$callback["folder"].$callback['callback'][0]; // App\Controllers\arquivo
                $method = $callback['callback'][1];
                $rc = new \ReflectionClass($controller);

                if($rc->isInstantiable() && $rc->hasMethod($method)) {
                    return call_user_func_array(array(new $controller, $method), array($request, $response, $params));
                } else {
                    throw new \Exception("Erro ao despachar: controller não pode ser instanciado, ou método não exite");                
                }
            }
        }
        throw new \Exception("Erro ao despachar: método não implementado");
    }
}