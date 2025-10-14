<?php

namespace Model;

use Model\ActiveRecord;

class Categoria extends ActiveRecord {
    protected static $tabla = 'categorias';
    protected static $columnasDB = ['id', 'nombre', 'descripcion', 'fecha_creacion', 'fecha_actualizacion', 'restauranteId'];

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->fecha_creacion = date('Y-m-d H:i:s');
        $this->fecha_actualizacion = date('Y-m-d H:i:s');
        $this->restauranteId = $args['restauranteId'] ?? null;
    }

    // Validar Categoría
    public function validarCategoria() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre de la Categoría es Obligatoria';
        }

        return self::$alertas;
    }

}