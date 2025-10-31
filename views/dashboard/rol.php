<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">

    <div class="contenedor-nuevo-rol">

        <button
            type="button"
            class="agregar-rol"
            id="agregar-rol">&#43; Nuevo Rol</button>
    </div>

    <ul id="listado-roles" class="listado-roles">

    </ul>

</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>

<?php
$script .= '    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="build/js/roles.js"></script>
';
?>