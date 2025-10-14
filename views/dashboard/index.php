<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">

    <section class="seccion-ordenes">

        <h2 class="titulo-seccion">En Sitio</h2>

        <table class="contenedor-tabla" id="tabla-sitio">
            <thead class="encabezado-tabla">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody id="listado-ordenes" class="cuerpo-tabla listado-ordenes"></tbody>
        </table>

    </section>

    <section class="seccion-ordenes">

        <h2 class="titulo-seccion">A Domicilio</h2>

        <table class="contenedor-tabla" id="tabla-domicilio">
            <thead class="encabezado-tabla">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Direccion</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody id="listado-ordenes" class="listado-ordenes"></tbody>
        </table>

    </section>

    <section class="seccion-ordenes">

        <h2 class="titulo-seccion">Anticipadas</h2>

        <table class="contenedor-tabla" id="tabla-anticipado">
            <thead class="encabezado-tabla">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody id="listado-ordenes" class="listado-ordenes"></tbody>
        </table>

    </section>

</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>

<?php
$script = '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="build/js/ordenes.js"></script>
';
?>