(function () {

    obtenerProductos();
    let productos = [];

    //Boton para mostrar el modal para agregar producto
    const nuevoProductoBtn = document.querySelector('#agregar-producto');
    nuevoProductoBtn.addEventListener('click', function () {
        mostrarFormulario();
    });

    async function obtenerProductos() {

        try {
            const id = obtenerCategoria();
            const url = `/api/productos?id=${id}`;
            const respuesta = await fetch(url);

            const resultado = await respuesta.json();

            productos = resultado.productos;

            console.log(resultado);

            mostrarProductos();

        } catch (error) {
            console.log(error);
        }
    }

    function mostrarProductos() {

        limpiarProductos();

        if (productos.length === 0) {
            const contenedorProductos = document.querySelector('#listado-productos');

            const textoNoProductos = document.createElement('LI');
            textoNoProductos.textContent = 'No hay productos';
            textoNoProductos.classList.add('no-productos');

            contenedorProductos.appendChild(textoNoProductos);

            return;
        }

        const disponible = {
            0: 'Indisponible',
            1: 'Disponible'
        }
        productos.forEach(producto => {
            const contenedorProducto = document.createElement('LI');
            contenedorProducto, DataTransferItem.productoId = producto.id;
            contenedorProducto.classList.add('producto');

            const nombreProducto = document.createElement('P');
            nombreProducto.textContent = producto.nombre;
            nombreProducto.ondblclick = function () {
                mostrarFormulario(true, { ...producto });
            }

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            //botones
            const btnDisponibleProducto = document.createElement('BUTTON');
            btnDisponibleProducto.classList.add('disponible-producto');
            btnDisponibleProducto.classList.add(`${disponible[producto.disponible].toLowerCase()}`)
            btnDisponibleProducto.textContent = disponible[producto.disponible];
            btnDisponibleProducto.dataset.disponibleProducto = producto.disponible;
            btnDisponibleProducto.ondblclick = function () {
                cambiarDiponibilidadProducto({ ...producto });
            }

            const btnEliminarProducto = document.createElement('BUTTON');
            btnEliminarProducto.classList.add('eliminar-producto');
            btnEliminarProducto.dataset.idProducto = producto.id;
            btnEliminarProducto.textContent = 'Eliminar';
            btnEliminarProducto.ondblclick = function () {
                confirmarEliminarProducto({ ...producto });
            }

            opcionesDiv.appendChild(btnDisponibleProducto);
            opcionesDiv.appendChild(btnEliminarProducto);

            contenedorProducto.appendChild(nombreProducto);
            contenedorProducto.appendChild(opcionesDiv);

            const listadoProductos = document.querySelector('#listado-productos');
            listadoProductos.appendChild(contenedorProducto);

        });
    }

    function mostrarFormulario(editar = false, producto = {}) {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario nuevo-producto">
                <legend>${editar ? 'Editar Producto' : 'Añade un nuevo producto'}</legend>
                <div class="campo">
                    <label>Producto</label>
                    <input
                        type="text"
                        name="producto"
                        placeholder="${producto.nombre ? 'Editar Producto' : 'Añadir producto a la categoría actual'}"
                        id="producto"
                        value="${producto.nombre ? producto.nombre : ''}"
                    />
                </div>
                <div class="campo">
                    <label>Descripcion</label>
                    <input
                        type="text"
                        name="descripcion"
                        placeholder="${producto.nombre ? 'Editar descripción' : 'Añadir una descripción al producto'}"
                        id="descripcion"
                        value="${producto.descripcion ? producto.descripcion : ''}"
                    />
                </div>

                <div class="campo">
                    <label for="imagen1">Imagen</label>
                    <input 
                        type="file"
                        id="imagen1"
                        accept="image/jpeg, image/png" 
                    />
                </div>

                <div class="campo">
                    <label for="imagen2">Imagen</label>
                    <input 
                        type="file" 
                        id="imagen2"
                        accept="image/jpeg, image/png" 
                    />
                </div>
                <div class="opciones">
                    <input 
                        type="submit" 
                        class="submit-nuevo-producto" 
                        value="${producto.nombre ? 'Guardar Cambios' : 'Añadir Producto'}" />
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;

        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');

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

            if (e.target.classList.contains('submit-nuevo-producto')) {
                const productoNombre = document.querySelector('#producto').value.trim();
                const productoDescripcion = document.querySelector('#descripcion').value.trim();

                if (productoNombre === '') {
                    //mostrar alerta de error
                    mostrarAlerta('El nombre del producto es obligatorio', 'error', document.querySelector('.formulario legend'));
                    return;
                }

                if (editar) {
                    producto.nombre = productoNombre;
                    producto.descripcion = productoDescripcion;
                    actualizarProducto(producto);
                } else {
                    agregarProducto(productoNombre, productoDescripcion);
                }
            }

        })

        document.querySelector('.dashboard').appendChild(modal);

    }

    //Muestra mensaje en la interfaz
    function mostrarAlerta(mensaje, tipo, referencia) {

        //Prevenir muchas alertas
        const alertaPrevia = document.querySelector('.alerta');
        if (alertaPrevia) {
            alertaPrevia.remove();
        }
        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;

        // Inserta la alerta antes del legend
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);

        //Eliminar alerta
        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }

    //Consultar el servidor para añadir un nuevo producto
    async function agregarProducto(producto, descripcion) {
        // Construir la peticion
        const datos = new FormData();
        datos.append('nombre', producto);
        datos.append('descripcion', descripcion);
        datos.append('categoriaId', obtenerCategoria());

        try {
            const url = 'http://localhost:3000/api/producto';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'));

            if (resultado.tipo === 'exito') {
                const nombreProducto = document.querySelector('#producto');
                nombreProducto.value = ''; // limpia producto
                nombreProducto.focus();

                const descripcionProducto = document.querySelector('#descripcion');
                descripcionProducto.value = ''; // limpia descripcion

                //Agregar el objeto de producto al global de productos
                const productoObj = {
                    id: String(resultado.id),
                    nombre: producto,
                    descripcion: descripcion,
                    precio: "0.00",
                    imagen1: "",
                    imagen2: "",
                    disponible: "1",
                    fecha_creacion: resultado.fecha_creacion,
                    fecha_actualizacion: resultado.fecha_actualizacion,
                    categoriaId: resultado.categoriaId
                }
                //console.log(productoObj);

                if (typeof socket !== "undefined") {
                    socket.emit("producto_nuevo", productoObj);
                }

                productos = [...productos, productoObj];
                mostrarProductos();
            }

        } catch (error) {
            console.log(error);
        }

    }

    function cambiarDiponibilidadProducto(producto) {
        const nuevaDiponibilidad = producto.disponible === "1" ? "0" : "1";
        producto.disponible = nuevaDiponibilidad;

        actualizarProducto(producto);

    }

    async function actualizarProducto(producto) {
        const { disponible, id, nombre, descripcion, imagen_url, precio, categoriaId, fecha_creacion, fecha_actualizacion } = producto;
        console.log(producto);

        const datos = new FormData();

        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('descripcion', descripcion);
        datos.append('precio', precio);
        datos.append('imagen_url', imagen_url);
        datos.append('disponible', disponible);
        datos.append('fecha_creacion', fecha_creacion);
        datos.append('fecha_actualizacion', fecha_actualizacion);
        datos.append('categoriaId', obtenerCategoria());

        try {
            const url = "http://localhost:3000/api/producto/actualizar";

            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();


            if (resultado.respuesta.tipo === 'exito') {

                Swal.fire(
                    resultado.respuesta.mensaje,
                    resultado.respuesta.mensaje,
                    'success'
                );

                const modal = document.querySelector('.modal');
                if (modal) {
                    modal.remove();
                }


                productos = productos.map(productoMemoria => {
                    if (productoMemoria.id === id) {
                        productoMemoria.disponible = disponible;
                        productoMemoria.nombre = nombre;
                        productoMemoria.descripcion = descripcion
                    }

                    return productoMemoria;
                });

                mostrarProductos();
            }
        } catch (error) {
            console.log(error);
        }
    }

    function confirmarEliminarProducto(producto) {
        Swal.fire({
            title: "¿Eliminar Producto?",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarProducto(producto);
            }
        });
    }

    async function eliminarProducto(producto) {
        const { disponible, id, nombre, descripcion, imagen_url, precio, fecha_creacion, fecha_actualizacion } = producto;

        const datos = new FormData();

        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('descripcion', descripcion);
        datos.append('precio', precio);
        datos.append('imagen_url', imagen_url);
        datos.append('disponible', disponible);
        datos.append('fecha_creacion', fecha_creacion);
        datos.append('fecha_actualizacion', fecha_actualizacion);
        datos.append('categoriaId', obtenerCategoria());

        try {
            const url = 'http://localhost:3000/api/producto/eliminar'
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            if (resultado.resultado) {
                //mostrarAlerta(resultado.mensaje,
                //    resultado.tipo,
                //    document.querySelector('.contenedor-nuevo-producto')
                //);

                Swal.fire('Eliminado', resultado.mensaje, 'success');
                productos = productos.filter(productoMemoria => productoMemoria.id !== producto.id);
                mostrarProductos();
            }

        } catch (error) {
            console.log(error);
        }
    }

    function obtenerCategoria() {
        const categoriaParams = new URLSearchParams(window.location.search);
        const categoria = Object.fromEntries(categoriaParams.entries());
        return categoria.id;
    }

    function limpiarProductos() {
        const listadoProductos = document.querySelector('#listado-productos');

        while (listadoProductos.firstChild) {
            listadoProductos.removeChild(listadoProductos.firstChild);
        }
    }

})();