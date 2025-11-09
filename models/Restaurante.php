<?php

namespace Model;

class Restaurante extends ActiveRecord
{
    protected static $tabla = 'restaurantes';
    protected static $columnasDB = ['id', 'nombre', 'direccion', 'email', 'telefono', 'horario_apertura', 'horario_cierre', 'logo', 'estatus', 'numero_cuenta', 'clabe'];

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
        $this->numero_cuenta = $args['numero_cuenta'] ?? '';
        $this->clabe = $args['clabe'] ?? '';
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

    public function validarInformacion() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre del Restaurante es Obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email de Contacto es Obligatorio';
        }
        if(!$this->telefono) {
            self::$alertas['error'][] = 'El Teléfono de Contacto es Obligatorio';
        }
        if(!$this->hora_apertura) {
            self::$alertas['error'][] = 'El Horario de Apertura es Obligatorio';
        }
        if(!$this->hora_cierre) {
            self::$alertas['error'][] = 'El Horario de Cierre es Obligatorio';
        }
        if(!$this->numero_cuenta || !$this->clabe) {
            self::$alertas['error'][] = 'Los Datos para pagos por transferencia son Obligatorios';
        }
        
        return self::$alertas;
    }
}
