<?php
namespace src;
use src\Router;
 
/* @descrition: Existe um padrão de projetos conhecido com Singleton. 
 * Este padrão de projeto é um método usado para preservar uma única
 * 0 instancia de objetos durante toda execução do nosso aplicativo.
 * Contudo, agora eles poderão ser invocados estaticamente. 
 * Além disso, preservamos a nível de classe o Router,
 * não precisamos mais instanciar!
 */
final class Route
{
    protected static $router;
 
    private function __construct(){
    }
     
    protected static function getRouter() {
        if(empty(self::$router)) {
            self::$router = new Router;
        }
        return self::$router;
    }
 
    public static function post($pattern, $callback){
        return self::getRouter()->post($pattern, $callback);
    }
     
    public static function get($pattern, $callback){
        return self::getRouter()->get($pattern, $callback);
    }
 
    public static function put($pattern, $callback){
        return self::getRouter()->put($pattern, $callback);
    }
 
    public static function delete($pattern, $callback){
        return self::getRouter()->delete($pattern, $callback);
    }
     
    public static function resolve($pattern){
        return self::getRouter()->resolve($pattern);
    }
 
    public static function translate($pattern, $params){
        return self::getRouter()->translate($pattern, $params);
    }
    
}