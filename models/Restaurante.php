<?php

namespace Model;

class Restaurante extends ActiveRecord
{
    protected static $tabla = 'restaurantes';
    protected static $columnasDB = ['id', 'nombre', 'direccion', 'email', 'telefono', 'horario_apertura', 'horario_cierre', 'logo', 'estatus'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->horario_apertura = $args['horario_apertura'] ?? null;
        $this->horario_cierre = $args['horario_cierre'] ?? null;
        $this->logo = $args['logo'] ?? '';
        $this->estatus = $args['estatus'] ?? 0;
    }

    public function validarRestauranteCuentaNueva()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre del Restaurante no puede ir vacio';
        }
        
        return self::$alertas;
    }

    public function validarEmail()
    {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }

        return self::$alertas;
    }
}
