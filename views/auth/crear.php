<div class="contenedor crear">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>
    <div class="contenedor-sm">
        <p class="descripcion_pagina">Crea tu cuenta en Clic&Eat</p>
        
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/crear">
            
            <div class="campo">
                <label for="restaurante">Nombre del Restaurante</label>
                <input
                    type="text"
                    id="restaurante"
                    placeholder="Nombre del Restaurante"
                    name="restaurante" 
                    value="<?php echo $restaurante->nombre; ?>"
                />
            </div>
            
            <div class="campo">
                <label for="nombre">Encargado</label>
                <input
                    type="text"
                    id="nombre"
                    placeholder="Nombre del Encargado"
                    name="nombre" 
                    value="<?php echo $usuario->nombre; ?>"
                />
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    placeholder="Email de Contacto"
                    name="email" 
                    value="<?php echo $usuario->email; ?>"
                />
            </div>

            <div class="campo">
                <label for="telefono">Teléfono</label>
                <input
                    type="text"
                    id="telefono"
                    placeholder="Telefono"
                    name="telefono" 
                    value="<?php echo $usuario->telefono; ?>"
                />
            </div>

            <div class="campo">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    placeholder="Tu Password"
                    name="password" 
                />
            </div>

            <div class="campo">
                <label for="password2">Repetir Password</label>
                <input
                    type="password"
                    id="password2"
                    placeholder="Repite Tu Password"
                    name="password2" 
                />
            </div>

            <input type="submit" class="boton" value="Crear Cuenta">
        </form>

        <div class="acciones">
            <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
            <a href="/olvide">¿Olvidaste tu Password?</a>
        </div>
    </div> <!--. Contenedor-sm -->
</div>