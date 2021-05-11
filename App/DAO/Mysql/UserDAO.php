<?php
namespace App\DAO\Mysql;
use App\Models\Mysql\UserModel;
use \PDO;
class UserDAO extends Conexao {

    public function __construct() {
        parent::__construct();
    }
    
    public function LoginUser(UserModel $usuario): bool {
      
      try {
        
        $LoginUser = $this->pdo
        ->prepare('select * from usuario where email=?');
        $LoginUser->bindValue(1,$usuario->getEmail(),\PDO::PARAM_STR);
        $LoginUser->execute();
                
        if($LoginUser->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
       
        } catch(\PDOException $erro) {
            echo $erro->getMessage();
        }
    }
}