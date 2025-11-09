(function () {

    const botones = document.querySelectorAll('button[data-id-orden]');

    botones.forEach(boton => {
        boton.addEventListener('dblclick', function () {
            const ordenId = this.getAttribute('data-id-orden');
            console.log('Mostrando detalle de la orden:', ordenId);
            mostrarFormularioDetalle(ordenId);
        });
    });

    async function mostrarFormularioDetalle(ordenId) {
        try {
            const url = `/api/orden?id=${ordenId}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json()

            console.log(resultado);

            const { orden, productos } = resultado.respuesta;

            console.log(respuesta.resultado);

            const modal = document.createElement('DIV');
            modal.classList.add('modal', 'modal--grande');

            let botonComprobante = ``;

            if (orden.metodoPago === 'transferencia') {
                botonComprobante = `
                    <a href="${orden.comprobantePago}" target="_blank" rel="noopenernoreferrer" class="ver-comprobante-btn"aria-label="Ver comprobante de pago">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                `;
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
                            <input type="text" value="${orden.montoPago ? orden.montoPago : '0.00'}" disabled/>
                    </div>

                    <div class="campo">
                            <label>Metodo de Pago</label>
                            <div class="comprobante">
                                <input type="text" value="${orden.metodoPago ? orden.metodoPago.charAt(0).toUpperCase() + orden.metodoPago.slice(1) : 'Pendiente'}" disabled/>
                                ${botonComprobante}
                            </div>
                    </div >

                    <div class="campo">
                        <label>Estado de Pago</label>
                        <p
                            class="${orden.estadoPago}"
                        >
                                ${orden.estadoPago.charAt(0).toUpperCase() + orden.estadoPago.slice(1)}
                        </p>
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

        } catch (error) {
            console.log(error);
        }
    }
})();