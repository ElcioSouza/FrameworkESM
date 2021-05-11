<?php
namespace App\Controllers;
use App\DAO\Mysql\UserDAO;
use App\Models\Mysql\UserModel;

class ControllerUser extends Controller {

    private $userModel;
    private $userDAO;
    
   function __construct() {
       $this->userModel = new UserModel();
       $this->userDAO = new UserDAO();
    }
   
   public function user(){
      echo "Usuário";
   } 
   
   public function login($request,$response){
      // recuperando os dados da requisição
      $dados = $request->getBody();
      $password = $dados["password"];
      $email = $dados["email"];
         
      $usuario = $this->userModel->setEmail($email)
                                  ->setPassword($password); 
      
      if(!empty($email) && !empty($password)) {
         if($this->userDAO->LoginUser($usuario)) {
            return $response->json(['sucesso' => 'Logado com sucesso']);
         }
      if(!$this->userDAO->LoginUser($usuario)) {
             return $response->json(["bug"=> "Usuário ou Senha incorretos"]);
        }
     }
   }
}