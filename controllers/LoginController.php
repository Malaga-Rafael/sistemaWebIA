<?php

namespace Controllers;

use Classes\Email;
use Model\Restaurante;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if (empty($alertas)) {
                // Verificar existencia de usuario

                $usuario = Usuario::where('email', $usuario->email);

                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'Usuario no existe o no esta confirmado');
                } else {
                    if (password_verify($_POST['password'], $usuario->password)) {
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['restauranteId'] = $usuario->restauranteId;
                        $_SESSION['login'] = true;

                        header('Location: /dashboard');
                    } else {
                        Usuario::setAlerta('error', 'Password incorrecto');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        session_start();

        // Destruir todas las variables de sesión
        $_SESSION = [];

        // Si se usa una cookie de sesión, eliminarla
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destruir la sesión
        session_destroy();

        // Redirigir al login (o a la raíz)
        header('Location: /');
        exit(); // ¡Importante!
    }

    public static function crear(Router $router)
    {
        $alertas = [];

        $restaurante = new Restaurante();
        $usuario = new Usuario();


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $datosRestaurante = [
                'nombre' => $_POST['restaurante'] ?? '',
            ];

            $datosUsuario = [
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'password' => $_POST['password'] ?? '',
                'password2' => $_POST['password2'] ?? '',
                'rolId' => 1
            ];

            $restaurante->sincronizar($datosRestaurante);
            $usuario->sincronizar($datosUsuario);

            $alertasRestaurante = $restaurante->validarRestauranteCuentaNueva();
            $alertasUsuario = $usuario->validarUsuarioNuevaCuenta();

            $alertas = array_merge($alertasRestaurante, $alertasUsuario);

            if (empty($alertas)) {

                // Generar Usuario
                $slugRestaurante = strtolower(str_replace(' ', '', $restaurante->nombre));
                $emailAdmin = 'admin@' . $slugRestaurante;

                $existeUsuario = Usuario::where('usuario', $emailAdmin);

                if ($existeUsuario) {
                    Usuario::setAlerta('error', 'Ya existe un usuario con ese email');
                    $alertas = Usuario::getAlertas();
                } else {
                    $resultadoRestaurante = $restaurante->guardar();

                    if ($resultadoRestaurante) {

                        $usuario->usuario = $emailAdmin;
                        $usuario->hashPassword();
                        unset($usuario->password2);

                        $usuario->crearToken();
                        //$usuario->confirmado = 1;
                        $usuario->activo = 1;
                        $usuario->restauranteId = $resultadoRestaurante['id'];
                        $usuario->rolId = 1;

                        $resultadoUsuario = $usuario->guardar();

                        // Enviar Email
                        $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                        $email->enviarConfirmacion();

                        if ($resultadoUsuario) {
                            header('Location: /mensaje');
                        }
                    }
                }
            }
        }

        // Render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta',
            'restaurante' => $restaurante,
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if (empty($alertas)) {
                $usuario = Usuario::where('email', $usuario->email);

                if ($usuario && $usuario->confirmado) {
                    // Nuevo Token 
                    $usuario->crearToken();
                    unset($usuario->password2);

                    $usuario->guardar();

                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Hemos enviado las intrucciones a tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/olvide', [
            'titulo' => 'Recuperar Cuenta',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router)
    {
        $token = s($_GET['token']);
        $mostrar = true;

        if (!$token) header('Location: /');

        // Identificar usuario

        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nuevo password
            $usuario->sincronizar($_POST);

            // Validar Password
            $alertas = $usuario->validarPassword();

            if (empty($alertas)) {
                // Hashear Password
                $usuario->hashPassword();

                // Eliminar Token
                $usuario->token = null;

                // Guardar
                $resultado = $usuario->guardar();

                if ($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Render a la vista

        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router)
    {

        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }

    public static function confirmar(Router $router)
    {
        $token = s($_GET['token']);

        if (!$token) header('Location: /');

        // Encontrar al usuario con el mismo token
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no valido');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);

            //Guardar la base de datos
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta',
            'alertas' => $alertas
        ]);
    }
}
