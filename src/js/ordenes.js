(function () {

    // 👉 Ejecutar al cargar la página
    let ordenes = [];
    //let sitio = [];
    //let domicilio = [];
    //let anticipado = [];

    cargarOrdenes();

    // 🔹 Función para traer órdenes según el tipo
    async function cargarOrdenes(tipoActualizar = null) {

        try {


            const url = `/api/ordenes`
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            ordenes = resultado.ordenes;

            const sitio = ordenes.filter(o => o.tipo_pedido === 'sitio');
            const domicilio = ordenes.filter(o => o.tipo_pedido === 'domicilio');
            const anticipado = ordenes.filter(o => o.tipo_pedido === 'anticipado');

            if (!tipoActualizar || tipoActualizar === 'sitio') {
                limpiarOrdenes('#tabla-sitio tbody');
                mostrarOrdenes(sitio, '#tabla-sitio tbody');
            }
            if (!tipoActualizar || tipoActualizar === 'domicilio') {
                limpiarOrdenes('#tabla-domicilio tbody');
                mostrarOrdenes(domicilio, '#tabla-domicilio tbody');
            }
            if (!tipoActualizar || tipoActualizar === 'anticipado') {
                limpiarOrdenes('#tabla-anticipado tbody');
                mostrarOrdenes(anticipado, '#tabla-anticipado tbody');
            }

        } catch (error) {
            console.error('Error cargando órdenes:', error);
        }
    }

    function mostrarOrdenes(ordenes, selectorTabla) {
        console.log(ordenes);
        if (ordenes.length === 0) {
            const contendorOrdenes = document.querySelector(selectorTabla);

            const textoNoOrdenes = document.createElement('TD');
            textoNoOrdenes.classList.add('no-ordenes');
            textoNoOrdenes.textContent = 'No hay ordenes';
            textoNoOrdenes.colSpan = 4;
            contendorOrdenes.appendChild(textoNoOrdenes);


            return;
        }

        ordenes.forEach(orden => {
            const contenedorOrden = document.createElement('TR');
            contenedorOrden, DataTransferItem.ordenId = orden.id;
            contenedorOrden.classList.add('orden');

            const numeroOrden = document.createElement('TD');
            numeroOrden.textContent = orden.id;

            const nombreCliente = document.createElement('TD');
            nombreCliente.textContent = orden.nombreCliente;

            const direccionEnvio = document.createElement('TD');
            direccionEnvio.textContent = orden.direccionCompleta;

            const estadoOrden = document.createElement('TD');
            estadoOrden.textContent = orden.nombreEstatus

            const fechaOrden = document.createElement('TD');
            fechaOrden.textContent = orden.fecha_creacion;

            //const horaCompleta = orden.fecha_creacion.split(' ')[1]; // "13:15:00"
            //const horaMinuto = horaCompleta.slice(0, 5); // "13:15"
            //fechaOrden.textContent = horaMinuto;

            const opcionesDiv = document.createElement('TD');
            opcionesDiv.classList.add('opciones');

            // BOTON DE CANCELAR ORDEN
            const btnCancelarOrden = document.createElement('BUTTON');
            btnCancelarOrden.classList.add('cancelar-orden');
            btnCancelarOrden.dataset.estatusOrden = orden.estatusId;
            btnCancelarOrden.textContent = 'cancelar';
            btnCancelarOrden.ondblclick = function () {
                confirmarCancelarOrden({ ...orden });
            }

            const btnDetalleOrden = document.createElement('BUTTON');
            btnDetalleOrden.classList.add('detalle-orden')
            btnDetalleOrden.dataset.idOrden = orden.Id;
            btnDetalleOrden.textContent = 'Ver Orden';
            btnDetalleOrden.ondblclick = function () {
                mostrarFormularioOrden({ ...orden });
            }

            opcionesDiv.appendChild(btnDetalleOrden);
            opcionesDiv.appendChild(btnCancelarOrden);

            contenedorOrden.appendChild(numeroOrden);
            contenedorOrden.appendChild(nombreCliente);

            if (orden.tipo_pedido !== 'sitio') {
                contenedorOrden.appendChild(direccionEnvio);
            }
            contenedorOrden.appendChild(estadoOrden);
            contenedorOrden.appendChild(fechaOrden);
            contenedorOrden.appendChild(opcionesDiv);

            const listadoOrdenes = document.querySelector(selectorTabla);

            listadoOrdenes.appendChild(contenedorOrden);

        })
    }

    async function mostrarFormularioOrden(orden = {}) {

        try {
            const respuesta = await fetch(`/api/orden?id=${orden.id}`);
            const resultado = await respuesta.json();

            const { productos } = resultado;

            const modal = document.createElement('DIV');
            modal.classList.add('modal', 'modal--grande');

            let productosHTML = '';

            if (productos.length > 0) {
                productosHTML = `
                <table class="contenedor-tabla">
                    <thead class="encabezado-tabla">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                        </tr>
                    </thead>
                    <tbody class="cuerpo-tabla listado-ordenes">
                        ${productos.map(p => `
                            <tr class="producto-orden">
                                <td>${p.nombreProducto}</td>
                                <td>${p.cantidadProducto}</td>
                                <td>$${parseFloat(p.precioProducto).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            } else {
                productosHTML = `<p class="sin-productos">No hay productos en esta orden.</p>`;
            }

            let direccionHTML = '';

            if (orden.tipo_pedido !== "sitio") {
                direccionHTML = `
                    <div class="campo campo-direccion">
                        <label>Dirección</label>
                        <input type="text" value="${orden.direccionCompleta}" disabled />
                    </div>

                    <div class="campo campo-referencia">
                        <label>Referencia</label>
                        <input type="text" value="${orden.direccionReferencia}" disabled />
                    </div>
            `;
            }

            modal.innerHTML = `
            <form class="formulario">
                <legend>Detalle de la Orden #${orden.id}</legend>
                <div class= "campos"> 
                    <div class="campo">
                        <label>Tipo Pedido</label>
                        <input type="text" value="${orden.tipo_pedido}" disabled />
                    </div>

                    <div class="campo">
                        <label>Estatus</label>
                        <input type="text" value="${orden.nombreEstatus}" disabled />
                    </div>

                    <div class="campo">
                        <label>Cliente</label>
                        <input type="text" value="${orden.nombreCliente}" disabled />
                    </div>

                    <div class="campo campo-nota">
                        <label>Nota</label>
                        <input type="text" value="${orden.nota || ''}" disabled />
                    </div>
                    
                    ${direccionHTML}
                    
                </div> 

                <div class="campo">
                        <label>Productos</label>
                </div>
                ${productosHTML}

                <div class="opciones">
                    <button type="button" class="cerrar-modal">Regresar</button>
                </div>
            </form>
        `;

            setTimeout(() => {
                document.querySelector('.formulario').classList.add('animar');
            }, 0);

            modal.addEventListener('click', e => {
                if (e.target.classList.contains('cerrar-modal')) {
                    document.querySelector('.formulario').classList.add('cerrar');
                    setTimeout(() => modal.remove(), 400);
                }
            });

            document.querySelector('.dashboard').appendChild(modal);

        } catch (error) {
            console.error('Error al cargar detalle de orden:', error);
        }
    }


    // Cancelar orden (POST a la API)
    function confirmarCancelarOrden(orden) {
        Swal.fire({
            title: "¿Cancelar Orden?",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                cancelarOrden(orden);
            }
        });
    }

    function cancelarOrden(orden) {
        orden.estatusId = '6';
        actualizarOrden(orden);
    }

    async function actualizarOrden(orden) {

        const { id, fecha_creacion, tipo_pedido, nota, clienteId, restauranteId, estatusId, direccionId } = orden
        const datos = new FormData();

        datos.append('id', id);
        datos.append('fecha_creacion', fecha_creacion);
        datos.append('tipo_pedido', tipo_pedido);
        datos.append('nota', nota);
        datos.append('clienteId', clienteId);
        datos.append('restauranteId', restauranteId);
        datos.append('estatusId', estatusId);
        datos.append('direccionId', direccionId || '');

        console.log(orden);
        try {

            //console.log(orden);

            const url = "http://localhost:3000/api/orden/actualizar";

            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            console.log(resultado);

            if (resultado.respuesta.tipo === 'exito') {
                Swal.fire(
                    resultado.respuesta.mensaje,
                    '',
                    'success'
                );

                ordenes = ordenes.map(ordenMemoria => {
                    if (ordenMemoria.id === id) {
                        ordenMemoria.fecha_creacion = fecha_creacion;
                        ordenMemoria.clienteId = clienteId;
                        ordenMemoria.estatusId = estatusId;
                    }

                    return ordenMemoria;
                });

                // Detectar el tipo de pedido para recargar solo esa tabla
                const tipo = orden.tipo_pedido;
                cargarOrdenes(tipo); // 🔹 Solo actualiza la tabla correspondiente

            }

        } catch (error) {
            console.log(error);
        }
    }

    function limpiarOrdenes(selectorTabla) {
        const listadoOrdenes = document.querySelector(selectorTabla)

        while (listadoOrdenes.firstChild) {
            listadoOrdenes.removeChild(listadoOrdenes.firstChild);
        }
    }


})();
