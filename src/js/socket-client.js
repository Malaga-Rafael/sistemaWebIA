// websocket.js

// Evita crear múltiples conexiones si el archivo se importa más de una vez
if (!window.orderSocket) {
  const socket = new WebSocket("wss://app-django-86x6.onrender.com/ws/orders/");

  socket.onopen = () => {
    console.log("✅ Conectado al WebSocket");
  };

socket.onmessage = (e) => {
    try {
        const data = JSON.parse(e.data);
        console.log("📦 Mensaje recibido:", data);

        const orden = data.data?.order;
        if (!orden) return;

        // ✅ Llamar a la función expuesta por productos.js
        if (typeof window.actualizarOrdenEnTiempoReal === 'function') {
            window.actualizarOrdenEnTiempoReal(orden);
        } else {
            console.warn("Función actualizarOrdenEnTiempoReal no disponible aún.");
            // Opcional: esperar un poco y reintentar (útil si socket se conecta antes que productos.js)
            setTimeout(() => {
                if (typeof window.actualizarOrdenEnTiempoReal === 'function') {
                    window.actualizarOrdenEnTiempoReal(orden);
                }
            }, 500);
        }

    } catch (err) {
        console.error("❌ Error al procesar mensaje:", err);
    }
};

  socket.onerror = (e) => {
    console.error("⚠ Error en WebSocket:", e);
  };

  socket.onclose = () => {
    console.log("❌ Conexión WebSocket cerrada");
    // Opcional: intentar reconectar
    // setTimeout(() => location.reload(), 5000); // recargar tras 5s
  };

  // Guarda la instancia globalmente si necesitas acceder a ella después
  window.orderSocket = socket;
}