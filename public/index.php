<?php

require_once __DIR__ . '/../includes/app.php';

use Controllers\LoginController;
use Controllers\DashboardController;
use Controllers\OrdenController;
use Controllers\ProductoController;
use MVC\Router;

$router =  new Router();

date_default_timezone_set('America/Mexico_City');

// Login
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// Crear cuenta
$router->get('/crear', [LoginController::class, 'crear']);
$router->post('/crear', [LoginController::class, 'crear']);

// Contraseña olvidada
$router->get('/olvide', [LoginController::class, 'olvide']);
$router->post('/olvide', [LoginController::class, 'olvide']);

// Nueva contraseña
$router->get('/reestablecer', [LoginController::class, 'reestablecer']);
$router->post('/reestablecer', [LoginController::class, 'reestablecer']);

// Confirmacion de cuenta
$router->get('/mensaje', [LoginController::class, 'mensaje']);
$router->get('/confirmar', [LoginController::class, 'confirmar']);


// APARTADOS DEL DASHBOARD

$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/existencia', [DashboardController::class, 'existencia']);

$router->get('/crear-categoria', [DashboardController::class, 'crear_categoria']);
$router->post('/crear-categoria', [DashboardController::class, 'crear_categoria']);

$router->get('/categoria', [DashboardController::class, 'categoria']);

//$router->get('/perfil', [DashboardController::class, 'perfil']);

// API para las tareas
$router->get('/api/productos', [ProductoController::class, 'index']);
$router->post('/api/producto', [ProductoController::class, 'crear']);
$router->post('/api/producto/actualizar', [ProductoController::class, 'actualizar']);
$router->post('/api/producto/eliminar', [ProductoController::class, 'eliminar']);

// API para las ordenes
$router->get('/api/ordenes', [OrdenController::class, 'index']);
$router->get('/api/orden', [OrdenController::class, 'detalle']);
$router->post('/api/orden/actualizar', [OrdenController::class, 'actualizar']);
//$router->get('api/orden/cancelar', [OrdenController::class,'cancelar']);

// Comprueba y valida las rutas, que existen y les asigna las funciones del Controlador
$router->comprobarRutas();