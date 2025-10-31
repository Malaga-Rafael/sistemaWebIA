(function () {

    obtenerRoles();
    let ordenes = [];

    const nuevoRolBtn = document.querySelector('#agregar-rol');
    if (nuevoRolBtn) {
        nuevoRolBtn.addEventListener('click', function () {
            mostrarFormulario();
        });
    }

    async function obtenerRoles() {
        try {
            const url = `/api/roles`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            roles = resultado.roles;

            console.log(roles);

            mostrarRoles();

        } catch (error) {
            console.log(error);
        }
    }

    function mostrarRoles() {
        limpiarRoles();

        if (roles.length === 0) {
            const contenedorRoles = document.querySelector('#listado-roles');
            const textoNoRoles = document.createElement('LI');
            textoNoRoles.textContent = 'No hay roles';
            textoNoRoles.classList.add('no-roles');

            contenedorRoles.appendChild(textoNoRoles);

            return;
        }

        roles.forEach(rol => {
            const contenedorRol = document.createElement('LI');
            contenedorRol.classList.add('rol');

            const nombreRol = document.createElement('P');
            nombreRol.textContent = rol.nombre;
            nombreRol.ondblclick = function () {
                mostrarFormulario(true, { ...rol });
            }

            contenedorRol.appendChild(nombreRol);

            const listadoRoles = document.querySelector('#listado-roles');
            listadoRoles.appendChild(contenedorRol);
        });
    }

    function mostrarFormulario(editar = false, rol = {}) {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario nuevo-rol">
                <legend>${editar ? 'Editar Rol' : 'Añade un Nuevo Rol'}</legend>
                <div class="campo">
                    <label>Rol</label>
                    <input 
                        type="text" 
                        name="rol" 
                        placeholder="${rol.nombre ? 'Editar Rol' : 'Añadir Rol'}"
                        id="rol" 
                        value="${rol.nombre ? rol.nombre : ''}"
                    />
                </div>

                <div class="opciones">
                    <input 
                        type="submit" 
                        class="submit-nuevo-rol" 
                        value="${editar ? 'Guardar Cambios' : 'Añadir Rol'}" 
                    />
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;

        setTimeout(() => {
            const formulario = modal.querySelector('.formulario');
            if (formulario) formulario.classList.add('animar');
        }, 0);

        modal.addEventListener('click', function (e) {
            e.preventDefault();

            if (e.target.classList.contains('cerrar-modal')) {
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                    modal.remove();
                }, 400);
            }

            if (e.target.classList.contains('submit-nuevo-rol')) {
                const rolNombre = document.querySelector('#rol').value.trim();

                if (rolNombre === '') {
                    //mostrar alerta de error
                    mostrarAlerta('El nombre del rol es obligatorio', 'error', document.querySelector('.formulario legend'));
                    return;
                }

                if (editar) {
                    rol.nombre = rolNombre;
                    actualizarRol(rol);
                } else {
                    agregarRol(rolNombre);
                }
            }

        })

        document.querySelector('.dashboard').appendChild(modal);
    }

    function mostrarAlerta(mensaje, tipo, referencia) {
        const alertaPrevia = document.querySelector('.alerta');

        if (alertaPrevia) alertaPrevia.remove();

        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;

        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);
        
        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }

    function limpiarRoles() {
        const listadoRoles = document.querySelector('#listado-roles');

        while (listadoRoles.firstChild) {
            listadoRoles.removeChild(listadoRoles.firstChild);
        }
    }

})();