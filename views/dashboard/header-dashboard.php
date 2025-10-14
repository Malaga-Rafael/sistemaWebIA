<div class="dashboard">
    <?php include_once __DIR__ . '/../templates/sidebard.php'; ?>

    <div class="principal">
        <?php include __DIR__ . '/../templates/barra.php'; ?>

        <div class="contenido <?php if ($titulo === 'ORDENES') echo 'separacion'?>">
            <h2 class="nombre-pagina"><?php echo $titulo; ?> </h2>