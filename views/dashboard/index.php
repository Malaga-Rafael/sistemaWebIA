<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">

    <section class="seccion-ordenes">

        <h2 class="titulo-seccion">En Sitio</h2>

        <table class="contenedor-tabla" id="tabla-sitio">
            <thead class="encabezado-tabla">
                <tr>
                    <th>No.</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody id="listado-sitio" class="cuerpo-tabla listado-ordenes"></tbody>
        </table>

    </section>

    <section class="seccion-ordenes">

        <h2 class="titulo-seccion">A Domicilio</h2>

        <table class="contenedor-tabla" id="tabla-domicilio">
            <thead class="encabezado-tabla">
                <tr>
                    <th>No.</th>
                    <th>Cliente</th>
                    <th>Direccion</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody id="listado-domicilio" class="cuerpo-tabla listado-ordenes"></tbody>
        </table>

    </section>

    <section class="seccion-ordenes">

        <h2 class="titulo-seccion">Anticipadas</h2>

        <table class="contenedor-tabla" id="tabla-anticipado">
            <thead class="encabezado-tabla">
                <tr>
                    <th>No.</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Llegada</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody id="listado-anticipado" class="listado-ordenes"></tbody>
        </table>

    </section>

</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>

<?php
$script .= '
    <script src="build/js/app.js"</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="build/js/ordenes.js"></script>
    <script src="build/js/socket-client.js"></script>
';
?>