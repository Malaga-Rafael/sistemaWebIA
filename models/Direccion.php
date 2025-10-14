<?php

namespace Model;

class Direccion extends ActiveRecord {

    protected static $tabla = 'direcciones';
    protected static $columnasDB = ['id', 'alias', 'direccion', 'referencia', 'fecha_creacion', 'fecha_actualizacion', 'clienteId'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->alias = $args['alias'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->referencia = $args['referencia'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'] ?? ($this->id ? null : date('Y-m-d H:i:s'));
        $this->fecha_actualizacion = $args['fecha_actualizacion'] ?? date('Y-m-d H:i:s');
        $this->clienteId = $args['clienteId'] ?? '';
    }
}