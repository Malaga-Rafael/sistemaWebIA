<?php

namespace Model;

class Cliente extends ActiveRecord {

    protected static $tabla = 'clientes';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'telefono', 'password', 'fecha_creacion', 'fecha_actualizacion'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'] ?? ($this->id ? null : date('Y-m-d H:i:s'));
        $this->fecha_actualizacion = $args['fecha_actualizacion'] ?? date('Y-m-d H:i:s');
    }
}