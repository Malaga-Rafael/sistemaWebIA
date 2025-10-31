<?php

namespace Controllers;

use Model\Rol;
use MVC\Router;

class RolController {

    public static function index(Router $router) {
        session_start();
        isAuth();

        $restauranteId = $_SESSION['restauranteId'];

        //Extraer los roles existentes
        $roles = Rol::whereOrIsNull('restauranteId', $restauranteId);

        echo json_encode(['roles' => $roles]);

    }
}