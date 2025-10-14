const socket = io("https://realtime-svc.onrender.com");

socket.on("connect", () => {
  console.log("Conectado con ID:", socket.id);
  socket.emit("mensaje", { origen: "web", texto: "Hola desde el sistema web" });
});

socket.on("mensaje", (data) => {
  console.log("Mensaje recibido en la web:", data);
});

socket.on("producto_nuevo", (producto) => {
  console.log("Nuevo producto recibido en la web", producto);

  //Inyectar el producto a la lista:
  productos.push(producto);
  mostrarProductos();
});