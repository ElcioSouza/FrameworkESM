<?php
namespace App\Controllers;
use App\DAO\Mysql\UserDAO;

class ControllerPage extends Controller {
      
   function __construct() {
        $this->UserDAO = new UserDAO();
    }
   
   public function index($request,$response) {
       $data = $request->getBody(); 
            $this->view('Nomedoprojeto.home', [
                  'title' => 'Home'
           ]);  
     }
}