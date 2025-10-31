<aside class="sidebar">
    <div class="contenedor-sidebar">
        <h2>Clic&Eat</h2>

        <div class="cerrar-menu">
            <img id="cerrar-menu" src="build/img/cerrar.svg" alt="imagen-cerrar-menu">
        </div>
    </div>

    <nav class="sidebar-nav">
        <a class="<?php echo ($titulo === 'ORDENES') ? 'activo' : ''; ?>" href="/dashboard">ORDENES</a>
        <a class="<?php echo ($titulo === 'PRODUCTOS') ? 'activo' : ''; ?>" href="/existencia">PRODUCTOS</a>
        <a class="<?php echo ($titulo === 'AGREGAR CATEGORIA') ? 'activo' : ''; ?>" href="/crear-categoria">CATEGORIAS</a>
        <a class="<?php echo ($titulo === 'ROLES') ? 'activo' : ''; ?>" href="/rol">ROLES</a>
        <a class="<?php echo ($titulo === 'USUARIOS') ? 'activo' : ''; ?>" href="/usuario">USUARIOS</a>
        <a class="<?php echo ($titulo === 'PERFIL') ? 'activo' : ''; ?>" href="/perfil">Perfil</a>
    </nav>

    <div class="cerrar-sesion-mobile">
        <a href="/logout" class="cerrar-sesion">Cerrar Sesión</a>
    </div>

</aside>