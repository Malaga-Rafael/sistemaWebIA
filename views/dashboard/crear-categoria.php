<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
    
    <form class="formulario" method="POST" enctype="multipart/form-data" action="/crear-categoria">

        <?php include_once __DIR__ . '/formulario-categoria.php'; ?>
        
        <input type="submit" value="Crear Categoria">
    </form>

</div>
<?php include_once __DIR__ . '/footer-dashboard.php'; ?>

<?php
    $script .= '<script src="build/js/app.js"</script>';
?>