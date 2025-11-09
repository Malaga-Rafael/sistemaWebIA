<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">

    <section class="seccion-ordenes">

        <table class="contenedor-tabla" id="tabla-sitio">
            <thead class="encabezado-tabla">
                <tr>
                    <th>No.</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Método de Pago</th>
                    <th>Estado del Pago</th>
                    <th>Fecha</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody id="listado-sitio" class="cuerpo-tabla listado-ordenes">
                
                <?php if(count($ordenes) === 0) { ?>
                    <tr > 
                        <td class="no-ordenes" colspan=8> No hay ordenes </td>
                    </tr>
                <?php } else { ?>
                    
                    <?php foreach($ordenes as $orden) { ?>
                        <tr class="orden">
                            <td> <?php echo $orden->id;?></td>
                            <td> <?php echo $orden->nombreCliente;?></td>
                            <td> <?php echo ucfirst($orden->tipo_pedido);?></td>
                            <td > 
                                <div class="contenedor-estatus">
                                    <p  class="<?php echo strtolower($orden->nombreEstatus);?>"> <?php echo $orden->nombreEstatus;?> </p>
                                </div>
                            </td>
                            <td> <?php echo ucfirst($orden->metodoPago);?></td>
                            <td> 
                                <div class="contenedor-estatus">
                                    <p class="<?php echo strtolower($orden->estadoPago);?>"> <?php echo ucfirst($orden->estadoPago);?></p>
                                </div>
                            </td>
                            <td> <?php echo $orden->fecha_creacion;?></td>
                            <td> 
                                <div class="opciones">
                                    <button class="detalle-orden" data-id-orden="<?php echo $orden->id;?>">Ver detalle</button>
                                </div> 
                            </td>
                        </tr>

                    <?php } ?>
                <?php } ?>

            </tbody>
        </table>

    </section>

</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>

<?php
$script .= '
    <script src="build/js/app.js"</script>
    <script src="build/js/historial.js"></script>
';
?>