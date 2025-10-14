<?php

define('SUPABASE_URL', 'https://udpxrrllqezfgdseetrj.supabase.co'); 
define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVkcHhycmxscWV6Zmdkc2VldHJqIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTkyNTE0NjksImV4cCI6MjA3NDgyNzQ2OX0.EoR0noNXdDa5p15ARNt-iwXfm8k0lVmitN3o1g2fEwk');
define('SUPABASE_BUCKET', 'productos'); // nombre del bucket que crearás en Supabase


// Ver el contenido de una variable en ejecución
function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "<pre>";
    exit;
}

// Sanitiza cadenas de HTML - 
//Convierte caracteres peligrosos en entidades HTML seguras
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// Revisar que el usuario este autenticado
function isAuth() : void {
    if (!isset($_SESSION['login'])) {
        header('Location: /');
    }
}