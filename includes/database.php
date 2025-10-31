<?php

$db = mysqli_connect('ballast.proxy.rlwy.net', 'root', 'wEKuqgIzteFPNwvkktXDlrLjQUrEgiVU', 'railway',33845);
//$db = mysqli_connect('centerbeam.proxy.rlwy.net', 'root', 'jKaOeyPuPkQMpqSnQVUbWwpgLnkLLShw', 'railway',13384);
//$db = mysqli_connect('localhost', 'root', 'root', 'restaurantedb');

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}
