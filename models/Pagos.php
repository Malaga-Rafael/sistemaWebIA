<?php

namespace Model;

class Pagos extends ActiveRecord {

    protected static $tabla = 'pagos';
    protected static $columnasDB = ['id', 'metodo_pago', 'estatus', 'monto', 'comprobante', 'fecha_pago', 'ordenId'];

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->metodo_pago = $args['metodo_pago'] ?? null;
        $this->estatus = $args['estatus'] ?? null;
        $this->monto = $args['monto'] ?? 0.00;
        $this->comprobante = $args['comprobante'] ?? '';
        $this->fecha_pago = $args['fecha_pago'] ?? ($this->id ? null : date('Y-m-d H:i:s'));
        $this->ordenId = $args['ordenId'] ?? null;
    }
}