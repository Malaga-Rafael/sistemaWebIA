<?php

namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id' , 'nombre' , 'email', 'telefono', 'usuario', 'password', 'token', 'confirmado', 'activo', 'restauranteId', 'rolId'];

    public function __construct($args =  []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->usuario = $args['usuario'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->activo = $args['activo'] ?? null;
        $this->restauranteId = $args['restauranteId'] ?? null;
        $this->rolId = $args['rolId'] ?? null;
    }

    // Validar Login

    public function validarLogin() {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio';
        }
        

        return self::$alertas;
    }

    //Validacion de cuentas nuevas
    public function validarUsuarioNuevaCuenta() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre del Encargado es Obligatorio';
        }

        if (!$this->email) {
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }

        if (!$this->telefono) {
            self::$alertas['error'][] = 'El Telefono del Usuario es Obligatorio';
        }
        
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio';
        }

        if (strlen($this->password) < 6 ) {
            self::$alertas['error'][] = 'El Password debe de contener al menos 6 caracteres';
        }

        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = "Los Password son diferentes";
        }
        return self::$alertas;
    }


    //Validar el Email
    public function validarEmail() {

        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio'; 
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        
        return self::$alertas;
    }

    public function validarPassword() {

        if (!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio';
        }

        if (strlen($this->password) < 6 ) {
            self::$alertas['error'][] = 'El Password debe de contener al menos 6 caracteres';
        }

        return self::$alertas;

    }

    // Hashea el password
    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this->token = uniqid();
    }
}