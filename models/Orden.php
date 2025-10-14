<?php

namespace Model;

class Orden extends ActiveRecord {
    protected static $tabla = 'ordenes';
    protected static $columnasDB = ['id', 'fecha_creacion', 'tipo_pedido', 'nota', 'clienteId', 'restauranteId', 'estatusId', 'direccionId'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->fecha_creacion = $args['fecha_creacion'] ?? ($this->id ? null : date('Y-m-d H:i:s'));
        $this->tipo_pedido = $args['tipo_pedido'] ?? null;
        $this->nota = $args['nota'] ?? '';
        $this->clienteId = $args['clienteId'] ?? '';
        $this->restauranteId = $args['restauranteId'] ?? '';
        $this->estatusId = $args['estatusId'] ?? '';
        $this->direccionId = $args['direccionId'] ?? NULL;
    }

}