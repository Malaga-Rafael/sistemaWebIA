<?php

namespace Model;

class Producto extends ActiveRecord
{

    protected static $tabla = 'productos';
    protected static $columnasDB = ['id', 'nombre', 'descripcion', 'precio', 'imagen1', 'imagen2', 'disponible', 'fecha_creacion', 'fecha_actualizacion', 'eliminado', 'categoriaId'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->precio = $args['precio'] ?? 0.00;
        $this->imagen1 = $args['imagen1'] ?? '';
        $this->imagen2 = $args['imagen2'] ?? '';
        $this->disponible = $args['disponible'] ?? 1;
        $this->fecha_creacion = $args['fecha_creacion'] ?? ($this->id ? null : date('Y-m-d H:i:s'));
        $this->fecha_actualizacion = $args['fecha_actualizacion'] ?? date('Y-m-d H:i:s');
        $this->eliminado = $args['eliminado'] ?? 0;
        $this->categoriaId = $args['categoriaId'] ?? '';
    }
}
