<?php

//define('SUPABASE_URL', 'https://udpxrrllqezfgdseetrj.supabase.co'); 
//define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InVkcHhycmxscWV6Zmdkc2VldHJqIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTkyNTE0NjksImV4cCI6MjA3NDgyNzQ2OX0.EoR0noNXdDa5p15ARNt-iwXfm8k0lVmitN3o1g2fEwk');
//define('SUPABASE_BUCKET', 'productos'); // nombre del bucket que crearás en Supabase

define('SUPABASE_URL', 'https://cvmoqmramfokunnngjrq.supabase.co'); 
define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImN2bW9xbXJhbWZva3Vubm5nanJxIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjA3MDM3NjQsImV4cCI6MjA3NjI3OTc2NH0.u2YD1c8uP847eX3zwg_QiXzB4cp9sO62ktC2vcLRLuo');
define('SUPABASE_BUCKET', 'clients'); // nombre del bucket que crearás en Supabase

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
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        header('Location: /');
        exit();
    }
}


// CAMBIOS
/**
 * Sube un archivo a Supabase Storage y devuelve el nombre del archivo guardado.
 * @param array $archivo Archivo de $_FILES
 * @return string|false Nombre del archivo en Supabase o false si falla
 */
function subirArchivoASupabase($archivo, $carpeta = '') {
    if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $nombreOriginal = basename($archivo['name']);
    $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
    //Asegura extension en minusculas y evita nombres vacios
    $extension = $extension ? strtolower($extension) : 'jpg';
    $nombreUnico = uniqid() . '.' . strtolower($extension);

    //Construir la ruta con la carpeta
    $rutaArchivo = $carpeta ? rtrim($carpeta, '/') . '/' . $nombreUnico : $nombreUnico;

    $url = SUPABASE_URL . "/storage/v1/object/" . SUPABASE_BUCKET . "/" . rawurlencode($rutaArchivo);

    $contenido = file_get_contents($archivo['tmp_name']);
    if ($contenido === false) {
        return false;
    }

    $headers = [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY,
        'Content-Type: ' . ($archivo['type'] ?: 'application/octet-stream')
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $contenido);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Solo desarrollo
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $respuesta = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 201) {
        //Devolver la URL publica completa
        $publicUrl = SUPABASE_URL . "/storage/v1/object/public/" . SUPABASE_BUCKET . "/" . rawurlencode($rutaArchivo);
        return $publicUrl; 
    }

    error_log("Error al subir a Supabase: HTTP $httpCode, respuesta: $respuesta");
    return false;
}

/**
 * Elimina un archivo de Supabase Storage
 * @param string $nombreArchivo Nombre del archivo en el bucket
 * @return bool true si se eliminó, false si falló
 */
function eliminarArchivoDeSupabase($nombreArchivo) {
    if (empty($nombreArchivo)) return false;

    $url = SUPABASE_URL . "/storage/v1/object/" . SUPABASE_BUCKET . "/" . rawurlencode($nombreArchivo);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $respuesta = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === 200;
}