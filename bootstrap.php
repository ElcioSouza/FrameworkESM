<?php
//error_reporting - Define quais erros serão reportados. E_All - Reportar todos os erros de php.
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once(__DIR__."/vendor/autoload.php");
require_once(__DIR__."/env.php");
session_start();
try { 
    require_once(__DIR__."/route/routes.php");
} catch(\Exception $e){
    echo $e->getMessage();
}