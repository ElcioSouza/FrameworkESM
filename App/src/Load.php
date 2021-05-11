<?php
namespace App\src;

class Load{

    public static function file_($file){
       $file = path().$file;

        if(!file_exists($file)){
            throw new \Exception("Esse arquivo não existe: {$file}");
        }

        return require $file;

    }

}