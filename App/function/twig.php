<?php
$message = new \Twig\TwigFunction('message');

$destroy = new \Twig\TwigFunction('destruirSessao', function(){
   
});

return [$message,$destroy];
    