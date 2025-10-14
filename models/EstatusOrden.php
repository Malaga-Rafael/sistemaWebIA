<?php

namespace Model;

class EstatusOrden extends ActiveRecord {

    protected static $tabla = 'estatus_ordenes';
    protected static $columnasDB = ['id', 'nombre', 'descripcion', 'fecha_creacion', 'fecha_actualizacion'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'] ?? ($this->id ? null : date('Y-m-d H:i:s'));
        $this->fecha_actualizacion = $args['fecha_actualizacion'] ?? date('Y-m-d H:i:s');
        
    }

}