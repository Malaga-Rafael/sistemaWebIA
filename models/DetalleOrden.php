<?php

namespace Model;

class DetalleOrden extends ActiveRecord{
    protected static $tabla = 'detalles_ordenes';
    protected static $columnasDB = ['ordenId', 'productoId', 'cantidad', 'precio_unitario'];

    public function __construct($args=[])
    {
        $this->ordenId = $args['ordenId'] ?? null;
        $this->productoId = $args['productoId'] ?? null;
        $this->cantidad = $args['cantidad'] ?? 0;
        $this->precio_unitario = $args['precio_unitario'] ?? 0.00;
    }
}