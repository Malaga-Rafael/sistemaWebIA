<?php

// $db = mysqli_connect('trolley.proxy.rlwy.net', 'root', 'BrZDeGvtxiroDHATSJUhtVeMaGtmMmif', 'railway',29145);
$db = mysqli_connect('localhost', 'root', 'root', 'restaurantedb');

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}
