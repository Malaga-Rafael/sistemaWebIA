<?php

namespace Model;

use ReturnTypeWillChange;

class ActiveRecord
{

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];

    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database)
    {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje)
    {
        static::$alertas[$tipo][] = $mensaje;
    }

    public static function getAlertas()
    {
        return static::$alertas;
    }

    public function validar()
    {
        static::$alertas = [];
        return static::$alertas;
    }

    public function guardar()
    {
        $resultado = '';
        if (!is_null($this->id)) {
            $resultado = $this->actualizar();
        } else {
            $resultado = $this->crear();
        }

        return $resultado;
    }

    public static function all()
    {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    public static function find($id)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = ${id} ";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public static function get($limite)
    {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT ${limite} ";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    public static function where($columna, $valor)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE ${columna} = '${valor}' ";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    // Busca todos los registros que pertenecen a un ID;
    public static function belongsTo($columna, $valor)
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE ${columna} = '${valor}' ";
        $resultado = self::consultarSQL($query);
        return  $resultado;
    }

    // Busca todos los registros que pertenencen a un restaurante o son globales
    public static function whereOrIsNull($columna, $valor)
    {
        // Validar que la columna exista en las columnas permitidas (opcional pero seguro)
        if (!in_array($columna, static::$columnasDB)) {
            throw new \InvalidArgumentException("La columna '{$columna}' no está permitida en " . static::class);
        }

        // Escapar el valor para prevenir inyección SQL
        $valorEscapado = self::$db->escape_string($valor);

        $query = "SELECT * FROM " . static::$tabla . " 
              WHERE {$columna} = '{$valorEscapado}' 
                 OR {$columna} IS NULL";

        return self::consultarSQL($query);
    }

    // Ordenes activas de un restaurante:
    public static function ordenesActivas($restauranteId)
    {
        $id = self::$db->escape_string($restauranteId);
        $query = "SELECT * FROM " . static::$tabla . " 
              WHERE restauranteId = '{$id}'
                AND estatusId IN (1, 2)
                AND fecha_creacion >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
              ORDER BY fecha_creacion ASC";

        return self::consultarSQL($query);
    }

    public static function SQL($consulta)
    {
        $query = $consulta;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    public function crear()
    {
        $atributos = $this->sanitizarAtributos();

        $query = "INSERT INTO " . static::$tabla . " (";
        $query .= join(', ', array_keys($atributos));
        $query .= ") VALUES ('";
        $query .= join("', '", array_values($atributos));
        $query .= "')";

        $resultado = self::$db->query($query);

        return [
            'resultado' => $resultado,
            'id' => self::$db->insert_id
        ];
    }

    public function actualizar()
    {
        $atributos = $this->sanitizarAtributos();

        $valores = [];
        foreach ($atributos as $key => $value) {
    if ($value === null) {
        $valores[] = "{$key} = NULL";
    } else {
        $valores[] = "{$key} = '" . self::$db->escape_string($value) . "'";
    }
}

        $query = "UPDATE " . static::$tabla . " SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1";

        $resultado = self::$db->query($query);
        return $resultado;
    }

    public function eliminar()
    {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return $resultado;
    }

    public static function consultarSQL($query)
    {
        $resultado = self::$db->query($query);

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        $resultado->free();

        return $array;
    }

    //    protected static function crearObjeto($registro)
    //    {
    //        $objeto = new static;
    //
    //        foreach ($registro as $key => $value) {
    //            if (property_exists($objeto, $key)) {
    //                $objeto->$key = $value;
    //            }
    //        }
    //
    //        return $objeto;
    //    }

    // En Model/Orden.php
    protected static function crearObjeto($registro)
    {
        $objeto = new static;

        // Asignar columnas de la tabla 'ordenes'
        foreach (static::$columnasDB as $columna) {
            if (isset($registro[$columna])) {
                $objeto->$columna = $registro[$columna];
            }
        }

        // Asignar campos calculados o de JOIN
        $objeto->nombreCliente = $registro['nombreCliente'] ?? '';
        $objeto->nombreEstatus = $registro['nombreEstatus'] ?? 'Sin estatus';
        $objeto->direccionCompleta = $registro['direccionCompleta'] ?? 'No aplica';
        $objeto->direccionReferencia = $registro['direccionReferencia'] ?? '';

        return $objeto;
    }

    // En Model/Orden.php
public static function ordenesConDetalles($restauranteId)
{
    $id = self::$db->escape_string($restauranteId);
    $query = "
        SELECT 
            o.*,
            CONCAT(c.nombre, ' ', c.apellido) AS nombreCliente,
            e.nombre AS nombreEstatus,
            COALESCE(d.direccion, 'No aplica') AS direccionCompleta,
            COALESCE(d.referencia, '') AS direccionReferencia
        FROM ordenes o
        INNER JOIN clientes c ON o.clienteId = c.id
        INNER JOIN estatus_ordenes e ON o.estatusId = e.id
        LEFT JOIN direcciones d ON o.direccionId = d.id
        WHERE o.restauranteId = '{$id}'          
          AND o.fecha_creacion >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY o.fecha_creacion ASC
    ";

    //AND o.estatusId IN (1, 2, 3) -- ajusta según tus IDs de estatus activos

    return self::consultarSQL($query);
}

    public function atributos()
    {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            if ($columna === "id") continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarAtributos()
    {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            if ($value === null) {
                $sanitizado[$key] = NULL;
            } else {
                $sanitizado[$key] = self::$db->escape_string($value);
            }
            
        }
        return $sanitizado;
    }

    public function sincronizar($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }
}
