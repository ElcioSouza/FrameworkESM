<?php
namespace App\DAO\Mysql;
use \PDO;
abstract class Conexao {
    
    protected $pdo;
    
    public function __construct() {
        $host   = getenv("NOMEDOBANCO_MYSQL_HOST");
        $port   = getenv("NOMEDOBANCO_MYSQL_PORT");
        $user   = getenv("NOMEDOBANCO_MYSQL_USER");
        $pass   = getenv("NOMEDOBANCO_MYSQL_PASSWORD");
        $dbname = getenv("NOMEDOBANCO_MYSQL_DBNAME"); 
        $dsn    = "mysql:host={$host};dbname={$dbname};port={$port}";
        
        $this->pdo = new PDO($dsn,$user,$pass);
        $this->pdo->setAttribute(
                \PDO::ATTR_ERRMODE,
                \PDO::ERRMODE_EXCEPTION
            );
    }   
}