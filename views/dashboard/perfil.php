<?php include_once __DIR__ . '/header-dashboard.php'; ?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

    <a href="/cambiar-password" class="enlace">Cambiar Password</a>

    <form class="formulario" method="POST" action="/perfil">

        <fieldset class="mi-fieldset info-usuario">

            <legend>Información del Usuario</legend>
    
            <div class="campo form-group">
                <label for="usuario">Usuario</label>
                <input
                    type="text"
                    value="<?php echo $usuario->usuario ?>"
                    name="usuario"
                    id="usuario"
                    placeholder="Tu usuario" 
                    disabled />
            </div>

            <div class="campo form-group">
                <label for="nombre">Nombre</label>
                <input
                    type="text"
                    value="<?php echo $usuario->nombre ?>"
                    name="nombre"
                    id="nombre"
                    placeholder="Tu nombre" />
            </div>

            <div class="campo form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    value="<?php echo $usuario->email ?>"
                    name="email"
                    id="email"
                    placeholder="Tu Email" />
            </div>

            <div class="campo form-group">
                <label for="telefono">Teléfono</label>
                <input
                    type="text"
                    value="<?php echo $usuario->telefono ?>"
                    name="telefono"
                    id="telefono"
                    placeholder="Tu Número de Teléfono" />
            </div>

        </fieldset>

        <fieldset class="mi-fieldset info-restaurante">

            <legend>Información Restaurante</legend>

            <div class="campo form-group campo-restaurante">
                <label for="nombre-restaurante ">Restaurante</label>
                <input
                    type="text"
                    value="<?php echo $restaurante->nombre ?>"
                    name="nombre-restaurante"
                    id="nombre-restaurante"
                    placeholder="Nombre del Negocio" />
            </div>

            <div class="campo form-group campo-apertura">
                <label for="apertura-restaurante">Apertura</label>
                <input
                    type="time"
                    value="<?php echo $restaurante->horario_apertura ?>"
                    id="apertura-restaurante"
                    name="apertura-restaurante" />
            </div>

            <div class="campo form-group campo-cierre">
                <label for="cierre-restaurante">Cierre</label>
                <input
                    type="time"
                    value="<?php echo $restaurante->horario_cierre ?>"
                    id="cierre-restaurante"
                    name="cierre-restaurante" />
            </div>

            <div class="campo form-group campo-email">
                <label for="email-restaurante">Correo de Contacto</label>
                <input
                    type="email"
                    value="<?php echo $restaurante->email ?>"
                    name="email-restaurante"
                    id="email-restaurante"
                    placeholder="Email de Contacto" />
            </div>

            <div class="campo form-group campo-telefono">
                <label for="telefono-restaurante">Telefono de Contacto</label>
                <input
                    type="text"
                    value="<?php echo $restaurante->telefono ?>"
                    name="telefono-restaurante"
                    id="telefono-restaurante"
                    placeholder="Teléfono de Contacto" />
            </div>

            <div class="campo form-group campo-ubicacion">
                <label for="direccion-restaurante">Ubicación</label>
                <input
                    type="text"
                    value="<?php echo $restaurante->direccion ?>"
                    name="direccion-restaurante"
                    id="direccion-restaurante"
                    placeholder="Dirección del Restaurante" />
            </div>

            <div class="campo form-group campo-cuenta">
                <label for="cuenta-restaurante">Número de Cuenta</label>
                <input
                    type="text"
                    value="<?php echo $restaurante->numero_cuenta ?>"
                    name="cuenta-restaurante" 
                    id="cuenta-restaurante"
                    placeholder="Número de Cuenta para Depositos" />
            </div>

            <div class="campo form-group campo-clabe">
                <label for="clabe-restaurante">Cuenta CLABE</label>
                <input
                    type="text"
                    value="<?php echo $restaurante->clabe ?>"
                    name="clabe-restaurante" 
                    id="clabe-restaurante"
                    placeholder="Cuenta CLABE para Depositos" />
            </div>

        </fieldset>

        <input
            type="submit"
            value="Guardar Cambios" />
    </form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php'; ?>

<?php
$script .= '<script src="build/js/app.js"</script>';
?>