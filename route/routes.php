<?php
use src\Route as Route;

Route::get("/produto",function(){
    echo "Produto";
});

Route::get("/user","ControllerUser@user");

// paginas home e login
Route::get("/","ControllerPage@index");
Route::post("/login","ControllerPage@index");