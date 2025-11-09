<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/app.php';

use Controllers\LoginController;
use Controllers\DashboardController;
use Controllers\OrdenController;
use Controllers\PagoController;
use Controllers\ProductoController;
use Controllers\RolController;
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

//ORDENES
$router->get('/dashboard', [DashboardController::class, 'index']);

//CATEGORIA -> PRODUCTOS
$router->get('/existencia', [DashboardController::class, 'existencia']);
$router->get('/categoria', [DashboardController::class, 'categoria']);

//CATEGORIAS
$router->get('/rol', [DashboardController::class, 'rol']);

//ROLES
$router->get('/crear-categoria', [DashboardController::class, 'crear_categoria']);
$router->post('/crear-categoria', [DashboardController::class, 'crear_categoria']);

// HISTORIAL
$router->get('/historial', [DashboardController::class, 'historial']);

// PERFIL
$router->get('/perfil', [DashboardController::class, 'perfil']);
$router->post('/perfil', [DashboardController::class, 'perfil']);

$router->get('/cambiar-password', [ DashboardController::class, 'cambiar_password']);
$router->post('/cambiar-password', [ DashboardController::class, 'cambiar_password']);

// API DE LOS PRODUCTOS
$router->get('/api/productos', [ProductoController::class, 'index']);
$router->post('/api/producto', [ProductoController::class, 'crear']);
$router->post('/api/producto/actualizar', [ProductoController::class, 'actualizar']);
$router->post('/api/producto/eliminar', [ProductoController::class, 'eliminar']);

// API PARA LAS ORDENES
$router->get('/api/ordenes', [OrdenController::class, 'index']);
$router->get('/api/orden', [OrdenController::class, 'detalle']);
$router->post('/api/orden/actualizar', [OrdenController::class, 'actualizar']);
//$router->get('api/orden/cancelar', [OrdenController::class,'cancelar']);

// APLI PARA LOS PAGOS
$router->post('/api/pago/actualizar', [PagoController::class, 'actualizar']);

// API PARA LOS ROLES
$router->get('/api/roles', [RolController::class, 'index']);
$router->post('/api/rol', [RolController::class, 'crear']);
$router->post('/api/actualizar', [RolController::class, 'actualizar']);


// Comprueba y valida las rutas, que existen y les asigna las funciones del Controlador
$router->comprobarRutas();