<?php
namespace App\traits;

use App\src\Load;
use src\Request;
use  App\src\System;

trait View {

    protected $twig;

    public function twig($file) {
        $loader = new \Twig\Loader\FilesystemLoader($file); 
        $this->twig = new \Twig\Environment($loader, [
            'debug' => false,
            'cache' => false
        ]);
    }
// serve para usar funções dentro do html
    protected function functions($file) {
        $functions = Load::file_($file);
        foreach ($functions as $function) {
            $this->twig->addFunction($function);
        }
    }
    
   public function start($getOS) {
    switch($getOS) {
       case 2:
           $this->twig('App\\Views');
           $this->functions('\\App\\function\\twig.php');
       break;
       default:
          $this->twig('App/Views');
          $this->functions('/App/function/twig.php');
      }
  }

    public function view($view,$data) {
        
        $System = new System(); 
        $this->start($System->getOS());
        $request = new Request();
         
        $defaul = [
            'host' => $request->host(),
            'public' => $request->public()
        ];
        
        $template = $this->twig->load(str_replace( '.', '/', $view ).'.html');
        $data = array_merge($data, $defaul);
        return $template->display($data);
    }
}