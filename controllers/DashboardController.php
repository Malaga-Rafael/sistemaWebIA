<?php

namespace Controllers;

use Model\Categoria;
use Model\Orden;
use MVC\Router;

class DashboardController
{

    public static function index(Router $router)
    {
        session_start();
        isAuth();

        $restauranteId = $_SESSION['restauranteId'];

        $router->render('dashboard/index', [
            'titulo' => 'ORDENES',
            'restauranteId' => $restauranteId
        ]);
    }


    public static function existencia(Router $router)
    {

        session_start();
        isAuth();

        $restauranteId = $_SESSION['restauranteId'];

        $categorias = Categoria::belongsTo('restauranteId', $restauranteId);

        $router->render('dashboard/existencia', [
            'titulo' => 'PRODUCTOS',
            'categorias' => $categorias
        ]);
    }

    public static function crear_categoria(Router $router)
    {

        session_start();
        isAuth();

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $categoria = new Categoria($_POST);

            //Alertas
            $alertas = $categoria->validarCategoria();

            if (empty($alertas)) {

                $categoria->restauranteId = $_SESSION['restauranteId'];

                $resultado = $categoria->guardar();

                header('Location: /categoria?id=' . $resultado['id']);
            }
        }

        $router->render('dashboard/crear-categoria', [
            'alertas' => $alertas,
            'titulo' => 'AGREGAR CATEGORIA'
        ]);
    }

    public static function categoria(Router $router)
    {

        session_start();
        isAuth();

        $id = $_GET['id'];

        if (!$id) header('Location: /dashboard');

        $categoria = Categoria::where('id', $id);


        if ($categoria->restauranteId !== $_SESSION['restauranteId']) {
            header('Location: /dashboard');
        }

        $router->render('dashboard/categoria', [
            'titulo' => $categoria->nombre
        ]);
    }

    public static function perfil(Router $router)
    {

        $router->render('dashboard/perfil', [
            'titulo' => 'PERFIL'
        ]);
    }
}
