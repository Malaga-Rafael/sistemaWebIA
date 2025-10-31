<div class="campo">
    <label for="nombre">Nombre</label>
    <input
        type="text"
        name="nombre"
        id="nombre"
        placeholder="Nombre de la Categoría" />
</div>

<div class="campo">
    <label for="descripcion">Descripción</label>
    <input
        type="text"
        name="descripcion"
        id="descripcion"
        placeholder="Decripción" />
</div>

<div class="subir-imagen">
    <div class="campo imagen">
        <label class="btn-imagen" for="imagen1">Imagen</label>
        <input
            type="file"
            name="imagen1"
            id="imagen1"
            accept="image/jpeg, image/png" />
    </div>
</div>

<!-- ✅ Scripts específicos para esta página -->

<?php
$script .= '
<script src="build/js/previsualizacion.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        activarPrevisualizacion("imagen1");
    });
</script>
';
?>