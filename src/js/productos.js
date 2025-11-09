(function () {

    obtenerProductos();
    let productos = [];

    const nuevoProductoBtn = document.querySelector('#agregar-producto');
    if (nuevoProductoBtn) {
        nuevoProductoBtn.addEventListener('click', function () {
            mostrarFormulario();
        });
    }

    async function obtenerProductos() {
        try {
            const id = obtenerCategoria();
            const url = `/api/productos?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            productos = resultado.productos || [];

            console.log(resultado.productos);
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

        const disponible = { 0: 'Indisponible', 1: 'Disponible' };

        productos.forEach(producto => {
            const contenedorProducto = document.createElement('LI');
            contenedorProducto.classList.add('producto');

            const nombreProducto = document.createElement('P');
            nombreProducto.textContent = producto.nombre;
            nombreProducto.ondblclick = () => mostrarFormulario(true, { ...producto });

            const precioProducto = document.createElement('P');
            precioProducto.textContent = `$ ${producto.precio}`;

            const opcionesDiv = document.createElement('DIV');
            opcionesDiv.classList.add('opciones');

            const btnDisponibleProducto = document.createElement('BUTTON');
            btnDisponibleProducto.classList.add('disponible-producto', disponible[producto.disponible].toLowerCase());
            btnDisponibleProducto.textContent = disponible[producto.disponible];
            btnDisponibleProducto.ondblclick = () => cambiarDiponibilidadProducto({ ...producto });

            const btnEliminarProducto = document.createElement('BUTTON');
            btnEliminarProducto.classList.add('eliminar-producto');
            btnEliminarProducto.dataset.idProducto = producto.id;
            btnEliminarProducto.textContent = 'Eliminar';
            btnEliminarProducto.ondblclick = () => confirmarEliminarProducto({ ...producto });

            opcionesDiv.appendChild(btnDisponibleProducto);
            opcionesDiv.appendChild(btnEliminarProducto);

            contenedorProducto.appendChild(nombreProducto);
            contenedorProducto.appendChild(precioProducto);

            contenedorProducto.appendChild(opcionesDiv);

            document.querySelector('#listado-productos')?.appendChild(contenedorProducto);
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
                    <label>Descripción</label>
                    <input
                        type="text"
                        name="descripcion"
                        placeholder="${producto.nombre ? 'Editar descripción' : 'Añadir una descripción al producto'}"
                        id="descripcion"
                        value="${producto.descripcion ? producto.descripcion : ''}"
                    />
                </div>
                <div class="campo">
                    <label>Precio</label>
                    <input
                        type="text"
                        name="precio"
                        placeholder="${producto.precio ? 'Editar precio' : 'Añadir un precio al producto'}"
                        id="precio"
                        value="${producto.precio ? producto.precio : ''}"
                    / >
                </div>

                <div class="subir-imagen">
                    <div class="campo imagen">
                        <input type="file" id="imagen1" accept="image/jpeg, image/png" />
                        <label class="btn-imagen" for="imagen1">Imagen 1</label>
                    </div>
                    <div class="campo imagen">
                        <input type="file" id="imagen2" accept="image/jpeg, image/png" />
                        <label class="btn-imagen" for="imagen2">Imagen 2</label>
                    </div>
                </div>
                

                <div class="opciones">
                    <input 
                        type="submit" 
                        class="submit-nuevo-producto" 
                        value="${editar ? 'Guardar Cambios' : 'Añadir Producto'}" 
                    />
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;

        // Mostrar miniaturas si es edición
        if (editar) {
            const campoImagen1 = modal.querySelector('[for="imagen1"]').parentElement;
            const campoImagen2 = modal.querySelector('[for="imagen2"]').parentElement;

            if (producto.imagen1) {
                const img1 = document.createElement('img');
                img1.classList.add('preview-img')
                img1.src = producto.imagen1;
                img1.style.width = '80px'; img1.style.marginTop = '5px';
                campoImagen1.appendChild(img1);
            }

            if (producto.imagen2) {
                const img2 = document.createElement('img');
                img2.classList.add('preview-img')
                img2.src = producto.imagen2;
                img2.style.width = '80px'; img2.style.marginTop = '5px';
                campoImagen2.appendChild(img2);
            }
        }

        setTimeout(() => {
            const formulario = modal.querySelector('.formulario');
            if (formulario) formulario.classList.add('animar');
        }, 0);

        modal.addEventListener('click', function (e) {
            if (e.target.classList.contains('cerrar-modal')) {
                e.preventDefault();
                const formulario = modal.querySelector('.formulario');
                if (formulario) {
                    formulario.classList.add('cerrar');
                    setTimeout(() => modal.remove(), 400);
                }
                return;
            }

            if (e.target.classList.contains('submit-nuevo-producto')) {
                e.preventDefault();
                const nombre = document.querySelector('#producto').value.trim();
                const descripcion = document.querySelector('#descripcion').value.trim();
                const precio = document.querySelector('#precio').value.trim();

                if (!nombre) {
                    mostrarAlerta('El nombre del producto es obligatorio', 'error', modal.querySelector('legend'));
                    return;
                }

                if (editar) {
                    producto.nombre = nombre;
                    producto.descripcion = descripcion;
                    producto.precio = precio;
                    actualizarProducto(producto);
                } else {
                    agregarProducto(nombre, descripcion, precio);
                }
            }
        });

        document.querySelector('.dashboard')?.appendChild(modal);

        setupImagePreview(modal, 'imagen1');
        setupImagePreview(modal, 'imagen2');

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

    async function agregarProducto(nombre, descripcion, precio) {
        const datos = new FormData();
        datos.append('nombre', nombre);
        datos.append('descripcion', descripcion);
        datos.append('precio', precio);
        datos.append('categoriaId', obtenerCategoria());

        const imagen1 = document.querySelector('#imagen1').files[0];
        const imagen2 = document.querySelector('#imagen2').files[0];
        if (imagen1) datos.append('imagen1', imagen1);
        if (imagen2) datos.append('imagen2', imagen2);

        try {
            const url = '/api/producto'; // Usa ruta relativa
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();

            mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.modal legend'));

            if (resultado.tipo === 'exito') {
                // Limpiar formulario
                document.querySelector('#producto').value = '';
                document.querySelector('#descripcion').value = '';
                document.querySelector('#precio').value = '';

                // Agregar a la lista local
                const nuevoProducto = {
                    id: String(resultado.id),
                    nombre,
                    descripcion,
                    precio,
                    disponible: "1",
                    imagen1: resultado.imagen1 || null,
                    imagen2: resultado.imagen2 || null,
                    categoriaId: resultado.categoriaId,
                    //fecha_creacion: resultado.fecha_creacion,
                    //fecha_actualizacion: resultado.fecha_actualizacion
                };

                productos.push(nuevoProducto);
                mostrarProductos();

                // Cerrar modal
                document.querySelector('.modal .cerrar-modal')?.click();
            }
        } catch (error) {
            console.log(error);
        }
    }

    function cambiarDiponibilidadProducto(producto) {
        const nuevaDisponibilidad = producto.disponible === "1" ? "0" : "1";
        producto.disponible = nuevaDisponibilidad;
        actualizarProducto(producto);
    }

    async function actualizarProducto(producto) {
        const datos = new FormData();
        datos.append('id', producto.id);
        datos.append('nombre', producto.nombre);
        datos.append('descripcion', producto.descripcion);
        datos.append('precio', producto.precio);
        datos.append('categoriaId', obtenerCategoria());
        datos.append('disponible', producto.disponible);

        const imagen1Input = document.querySelector('#imagen1');
        const imagen2Input = document.querySelector('#imagen2');
        if (imagen1Input?.files[0]) datos.append('imagen1', imagen1Input.files[0]);
        if (imagen2Input?.files[0]) datos.append('imagen2', imagen2Input.files[0]);

        try {
            const url = '/api/producto/actualizar';
            const respuesta = await fetch(url, { method: 'POST', body: datos });
            const resultado = await respuesta.json();

            if (resultado.respuesta?.tipo === 'exito') {
                Swal.fire('Éxito', resultado.respuesta.mensaje, 'success');

                // Actualizar en memoria
                productos = productos.map(p => {
                    if (p.id === producto.id) {
                        return {
                            ...p,
                            nombre: producto.nombre,
                            descripcion: producto.descripcion,
                            precio: producto.precio,
                            disponible: producto.disponible,
                            imagen1: resultado.respuesta.imagen1 ?? p.imagen1,
                            imagen2: resultado.respuesta.imagen2 ?? p.imagen2
                        };
                    }
                    return p;
                });

                document.querySelector('.modal .cerrar-modal')?.click();
                mostrarProductos();
            } else {
                mostrarAlerta(resultado.respuesta?.mensaje || 'Error al actualizar', 'error', document.querySelector('.modal legend'));
            }
        } catch (error) {
            console.log(error);
            mostrarAlerta('Error de conexión', 'error', document.querySelector('.modal legend'));
        }
    }

    function confirmarEliminarProducto(producto) {
        Swal.fire({
            title: "¿Eliminar Producto?",
            showCancelButton: true,
            confirmButtonText: "Sí",
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarProducto(producto);
            }
        });
    }

    async function eliminarProducto(producto) {
        const datos = new FormData();
        datos.append('id', producto.id);
        datos.append('categoriaId', obtenerCategoria());

        try {
            const url = '/api/producto/eliminar';
            const respuesta = await fetch(url, { method: 'POST', body: datos });
            const resultado = await respuesta.json();

            if (resultado.resultado) {
                Swal.fire('Eliminado', resultado.mensaje, 'success');
                productos = productos.filter(p => p.id !== producto.id);
                mostrarProductos();
            }
        } catch (error) {
            console.log(error);
        }
    }

    function obtenerCategoria() {
        const params = new URLSearchParams(window.location.search);
        return params.get('id');
    }

    function limpiarProductos() {
        const contenedor = document.querySelector('#listado-productos');
        while (contenedor?.firstChild) {
            contenedor.removeChild(contenedor.firstChild);
        }
    }

    function setupImagePreview(modal, inputId) {
        const input = modal.querySelector(`#${inputId}`);
        if (!input) {
            console.warn(`Input ${inputId} no encontrado`);
            return;
        }

        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            const container = input.closest('.campo');

            if (!container) {
                console.error('Contenedor .campo no encontrado');
                return;
            }

            // Eliminar preview anterior
            const oldPreview = container.querySelector('.preview-img');
            if (oldPreview) oldPreview.remove();

            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.classList.add('preview-img'); // ← ahora se controla desde CSS
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 👇 Exponer función para que otros scripts (como socket-client.js) puedan usarla


})();