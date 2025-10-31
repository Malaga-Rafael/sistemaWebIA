(function () {

    // 👉 Ejecutar al cargar la página
    let ordenes = [];

    const estados = {
        pendiente: '1',
        confirmado: '2',
        en_proceso: '3',
        enviada: '4',
        entregada: '5'
    };

    const flujoDomicilio = [
        estados.pendiente,
        estados.confirmado,
        estados.en_proceso,
        estados.enviada,
        estados.entregada
    ];

    const flujoInterno = [
        estados.pendiente,
        estados.confirmado,
        estados.en_proceso,
        estados.entregada
    ];

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
            textoNoOrdenes.colSpan = 6;
            contendorOrdenes.appendChild(textoNoOrdenes);


            return;
        }

        ordenes.forEach(orden => {
            const contenedorOrden = document.createElement('TR');
            contenedorOrden.dataset.ordenId = orden.id; // ✅ Correcto
            contenedorOrden.classList.add('orden');

            const numeroOrden = document.createElement('TD');
            numeroOrden.textContent = orden.id;

            const nombreCliente = document.createElement('TD');
            nombreCliente.textContent = orden.nombreCliente;

            const direccionEnvio = document.createElement('TD');
            direccionEnvio.textContent = orden.direccionCompleta;

            const estado = document.createElement('TD');
            estado.classList.add('contenedor-estatus');

            const btnEstadoOrden = document.createElement('BUTTON');
            btnEstadoOrden.classList.add(`${orden.nombreEstatus.toLowerCase().replace(/\s+/g, '-')}`);
            btnEstadoOrden.dataset.estatusOrden = orden.estatusId;
            btnEstadoOrden.textContent = `${orden.nombreEstatus}`;
            btnEstadoOrden.ondblclick = function () {
                cambiarEstatusOrden({ ...orden });
            }

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
            btnDetalleOrden.dataset.idOrden = orden.id;
            btnDetalleOrden.textContent = 'Ver Orden';
            btnDetalleOrden.ondblclick = function () {
                mostrarFormularioOrden({ ...orden });
            }

            opcionesDiv.appendChild(btnDetalleOrden);
            opcionesDiv.appendChild(btnCancelarOrden);

            estado.appendChild(btnEstadoOrden);

            contenedorOrden.appendChild(numeroOrden);
            contenedorOrden.appendChild(nombreCliente);

            if (orden.tipo_pedido === 'domicilio') {
                contenedorOrden.appendChild(direccionEnvio);
            }
            contenedorOrden.appendChild(estado);
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

            const { pago, productos } = resultado.respuesta;

            const modal = document.createElement('DIV');
            modal.classList.add('modal', 'modal--grande');

            console.log(pago);

            let botonComprobante = ``;

            if (pago !== null) {
                console.log('pago es mayor que 0');
                if (pago.metodo_pago === 'transferencia' && pago.comprobante) {
                    botonComprobante = `
                        <a href="${pago.comprobante}" target="_blank" rel="noopener noreferrer" class="ver-comprobante-btn" aria-label="Ver comprobante de pago">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    `;
                }
            }

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
            if (orden.tipo_pedido === "domicilio") {
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
                        <label>Cliente</label>
                        <input type="text" value="${orden.nombreCliente}" disabled />
                    </div>

                    <div class="campo">
                        <label>Tipo Pedido</label>
                        <input type="text" value="${orden.tipo_pedido.charAt(0).toUpperCase() + orden.tipo_pedido.slice(1)}" disabled />
                    </div>

                    <div class="campo">
                        <label>Estatus</label>
                        <input type="text" value="${orden.nombreEstatus}" disabled />
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

                <div class= "campos-pago"> 

                    <div class="campo">
                            <label>Total a Pagar</label>
                            <input type="text" value="${pago === null ? '0.00' : pago.monto}" />
                    </div>

                    <div class="campo">
                            <label>Metodo de Pago</label>
                            <div class="comprobante">
                                <input type="text" value="${pago === null ? 'Pendiente' : pago.metodo_pago.charAt(0).toUpperCase() + pago.metodo_pago.slice(1)}" />
                                ${botonComprobante}
                            </div>
                    </div >

                    <div class="campo">
                        <label>Estado de Pago</label>
                        <button 
                            type="button" 
                            class="${pago.estatus}"
                            data-id-pago = "${pago.id}"
                            >
                                ${pago.estatus.charAt(0).toUpperCase() + pago.estatus.slice(1)}
                            </button>
                    </div>
                </div>

                <div class="opciones">
                    <button type="button" class="cerrar-modal">Regresar</button>
                </div>
            </form >
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

            const botonEstadoPago = modal.querySelector('button[data-id-pago]');
            if (botonEstadoPago) {
                botonEstadoPago.addEventListener('dblclick', function () {
                    //const ordenId = orden.id;
                    const estadoActual = this.textContent.trim().toLowerCase();

                    console.log("Estado Actual: ", pago.estatus);

                    // Solo permitir acción si está en estado "pendiente"
                    if (estadoActual !== 'pendiente') {
                        Swal.fire('Acción no disponible', 'Solo se puede confirmar o rechazar pagos pendientes.', 'warning');
                        return;
                    }

                    // Preguntar qué acción desea realizar
                    Swal.fire({
                        title: '¿Qué deseas hacer con este pago?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'question',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'Confirmar',
                        denyButtonText: 'Rechazar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            pago.estatus = 'confirmado';
                            actualizarEstatusPago({ ...orden }, { ...pago });
                        } else if (result.isDenied) {
                            pago.estatus = 'rechazado';
                            actualizarEstatusPago({ ...orden }, { ...pago });
                        } else {
                            return; // Cancelado por el usuario
                        }
                    });
                });
            }

        } catch (error) {
            console.error('Error al cargar detalle de orden:', error);
        }
    }

    async function actualizarEstatusPago(orden, pago) {
        const datos = new FormData();

        datos.append('id', pago.id);
        datos.append('estatus', pago.estatus);

        try {
            const url = "/api/pago/actualizar";

            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            if (resultado.tipo === 'exito') {
                Swal.fire(
                    resultado.mensaje, 
                    '', 
                    'success'
                );

                // Actualizar el botón directamente en el DOM (sin recargar el modal)
                const botonPago = document.querySelector(`button[data-id-pago="${pago.id}"]`);
                if (botonPago) {
                    botonPago.textContent = pago.estatus.charAt(0).toUpperCase() + pago.estatus.slice(1);
                    botonPago.className = pago.estatus; // actualiza la clase CSS
                }
            }
        } catch (error) {
            console.log(error);
        }

    }

    //Cambiar el estado de una orden
    function cambiarEstatusOrden(orden) {
        const { estatusId, tipo_pedido } = orden;

        // Seleccionar flujo según tipo de pedido
        const esDomicilio = tipo_pedido === 'domicilio';
        const flujo = esDomicilio ? flujoDomicilio : flujoInterno;

        const indiceActual = flujo.indexOf(estatusId);

        // Si no está en el flujo o ya está en el último estado, no hacer nada
        if (indiceActual === - 1 || indiceActual === flujo.length - 1) {
            // Opcional: mostrar mensaje al usuario
            console.log('No se puede avanzar más el estado de esta orden.');
            return;
        }

        // Avanzar al siguiente estado
        const nuevoEstatusId = flujo[indiceActual + 1];
        const ordenActualizada = { ...orden, estatusId: nuevoEstatusId };

        actualizarOrden(ordenActualizada);

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
        const datos = new FormData();

        datos.append('id', orden.id);
        datos.append('estatusId', orden.estatusId);


        try {
            //const url = "http://localhost:3000/api/orden/actualizar";
            const url = "/api/orden/actualizar";

            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            if (resultado.respuesta.tipo === 'exito') {
                Swal.fire(
                    resultado.respuesta.mensaje,
                    '',
                    'success'
                );

                ordenes = ordenes.map(ordenMemoria => {
                    if (ordenMemoria.id === orden.id) {
                        ordenMemoria.estatusId = orden.estatusId;
                    }

                    return ordenMemoria;
                });

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

    window.actualizarOrdenEnTiempoReal = function (ordenRecibida) {
        // 🔁 Mapear el objeto del WebSocket al formato esperado por mostrarOrdenes
        const clienteNombre = ordenRecibida.client
            ? `${ordenRecibida.client.name} ${ordenRecibida.client.lastname || ''} `.trim()
            : 'Cliente';

        const direccionCompleta = ordenRecibida.address
            ? `${ordenRecibida.address.address || ''} `.trim()
            : '';

        const ordenNormalizada = {
            id: ordenRecibida.id,
            //Id: ordenRecibida.id, // porque usas orden.Id en el botón
            nombreCliente: clienteNombre,
            direccionCompleta: direccionCompleta,
            nombreEstatus: ordenRecibida.status?.name,
            estatusId: ordenRecibida.status?.id?.toString(),
            fecha_creacion: ordenRecibida.created_at || new Date().toISOString(),
            tipo_pedido: ordenRecibida.order.type, // "sitio", "domicilio", etc.
            nota: ordenRecibida.order.note || '',
            clienteId: ordenRecibida.client?.id || null,
            restauranteId: ordenRecibida.restaurant?.id || null,
            // Si necesitas más campos, agrégalos aquí
        };

        console.log("Orden Recibida por el socket: ", ordenRecibida);
        console.log("ORDEN NORMALIZADA: ", ordenNormalizada);
        // 1. Actualizar el array global `ordenes`
        const indiceExistente = ordenes.findIndex(o => o.id === ordenNormalizada.id);
        if (indiceExistente >= 0) {
            ordenes[indiceExistente] = ordenNormalizada;
        } else {
            ordenes.push(ordenNormalizada);
        }

        // 2. Determinar el tipo de pedido
        const tipo = ordenNormalizada.tipo_pedido;

        // 3. Actualizar SOLO la tabla correspondiente
        if (tipo === 'sitio') {
            limpiarOrdenes('#tabla-sitio tbody');
            const sitio = ordenes.filter(o => o.tipo_pedido === 'sitio');
            mostrarOrdenes(sitio, '#tabla-sitio tbody');
        } else if (tipo === 'domicilio') {
            limpiarOrdenes('#tabla-domicilio tbody');
            const domicilio = ordenes.filter(o => o.tipo_pedido === 'domicilio');
            mostrarOrdenes(domicilio, '#tabla-domicilio tbody');
        } else if (tipo === 'anticipado') {
            limpiarOrdenes('#tabla-anticipado tbody');
            const anticipado = ordenes.filter(o => o.tipo_pedido === 'anticipado');
            mostrarOrdenes(anticipado, '#tabla-anticipado tbody');
        }
    };

})();
