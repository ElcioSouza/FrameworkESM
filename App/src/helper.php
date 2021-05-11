<?php
function ddd($data){
    print_r($data);
    exit;
}
function path(){
    $vendorDir = dirname(dirname(__FILE__));
    return dirname($vendorDir);
}