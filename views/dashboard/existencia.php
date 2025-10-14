<?php include_once __DIR__ . '/header-dashboard.php'; ?>

    <?php if(count($categorias) === 0) { ?>
        <p class="no-categorias"> No hay categorias aún <a href="/crear-categoria">Comienza creando uno</a></p>
    <?php } else { ?>
        <ul class="listado-categorias">
            <?php foreach($categorias as $categoria) { ?>
               <li class="categoria">
                    <a href="/categoria?id=<?php echo $categoria->id; ?>">
                        <?php echo $categoria->nombre; ?>
                    </a>
               </li> 
            <?php } ?>
        </ul>
    <?php } ?>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>