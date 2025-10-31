<?php

namespace Model;

use Model\ActiveRecord;

class Rol extends ActiveRecord {
    protected static $tabla = 'roles';
    protected static $columnasDB = ['id', 'nombre', 'fecha_creacion', 'fecha_actualizacion', 'restauranteId'];

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'] ?? ($this->id ? null : date('Y-m-d H:i:s'));
        $this->fecha_actualizacion = $args['fecha_actualizacion'] ?? date('Y-m-d H:i:s');
        $this->restauranteId = $args['restauranteId'] ?? '';
    }
}