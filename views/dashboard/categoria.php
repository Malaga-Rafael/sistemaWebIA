<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">

    <div class="contenedor-nuevo-producto">

        <button
            type="button"
            class="agregar-producto"
            id="agregar-producto">&#43; Nuevo Producto</button>
    </div>

    <ul id="listado-productos" class="listado-productos">

    </ul>

</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>

<?php
$script = '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
    
    <script src="build/js/socket-client.js"></script>
    <script src="build/js/productos.js"></script>
';
?>