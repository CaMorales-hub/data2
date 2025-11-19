<?php
session_start();
if (!isset($_SESSION['cliente_id'])) {
  header("Location: login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Carrito de Compras - AutoGestión MX</title>
  <link rel="stylesheet" href="../CSS/carrito.css?v=6">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

<header>
  <div class="contenedor-header">
    <h1><i data-lucide="shopping-cart"></i> Carrito de Compras</h1>
    <nav class="nav-centro">
      <a href="perfil.php"><i data-lucide="user"></i>Perfil</a>
      <a href="login.html"><i data-lucide="log-out"></i>Salir</a>
    </nav>
    <div class="filtro-header">
      <button id="btn-producto" onclick="filtrarTipo('producto')" class="activo">Productos</button>
      <button id="btn-servicio" onclick="filtrarTipo('servicio')">Servicios</button>
    </div>
  </div>
</header>

<main class="contenedor-carrito">
  <!-- RESUMEN DEL CARRITO -->
  <section class="resumen-carrito">
    <h2>Resumen</h2>
    <div class="carrito-scroll">
      <ul id="cart-items"></ul>
    </div>
    <p id="total">Total: $0.00</p>
    <button onclick="abrirModalPago()">Finalizar compra</button>
  </section>

  <!-- PRODUCTOS -->
  <section class="productos" id="productos-container"></section>
</main>

<!-- MODAL DE PAGO -->
<div class="modal-pago" id="modalPago">
  <div class="modal-contenido form-azul">
    <h2>Datos de Pago</h2>
    <form id="formPago">
      <input type="text" name="nombre" placeholder="Nombre en la tarjeta" required />
      <input type="text" name="direccion" placeholder="Dirección" required />
      <input type="text" name="colonia" placeholder="Colonia" required />
      <input type="text" name="cp" placeholder="Código Postal" required />
      <input type="tel" name="telefono" placeholder="Teléfono" required />
      <input type="text" name="tarjeta" placeholder="Número de tarjeta" required />
      <div class="btn-form-group">
        <button type="submit">Confirmar Compra</button>
        <button type="button" onclick="cerrarModalPago()" class="cancelar">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL DE CONFIRMACIÓN -->
<div id="modalExito" class="modal-pago" style="display: none;">
  <div class="modal-contenido exito">
    <h2>¡Compra realizada con éxito!</h2>
    <p>Gracias por tu compra en AutoGestión MX.</p>
  </div>
</div>


<footer>
  &copy; 2025 AutoGestión MX | Todos los derechos reservados
</footer>

<script>
const contenedor = document.getElementById('productos-container');
const carrito = [];
const cartItems = document.getElementById('cart-items');
const total = document.getElementById('total');

let productos = [];
let tipoSeleccionado = 'producto';
function mostrarModalExito() {
  document.getElementById('modalExito').style.display = 'flex';
}
function cerrarModalExito() {
  document.getElementById('modalExito').style.display = 'none';
}


function cargarProductos() {
  fetch('../PHP/obtener_productos.php')
    .then(res => res.json())
    .then(data => {
      productos = data;
      renderProductos();
    })
    .catch(err => console.error("Error al cargar productos:", err));
}

function renderProductos() {
  contenedor.innerHTML = '';
  productos
    .filter(p => p.tipo === tipoSeleccionado)
    .forEach(producto => {
      const card = document.createElement('div');
      card.classList.add('producto-card');
      const stockDisponible = producto.stock > 0;

      card.innerHTML = `
        <img src="../IMG/${producto.imagen}" alt="${producto.nombre}">
        <h4>${producto.nombre}</h4>
        <p>${producto.descripcion}</p>
        <p>Precio: $${parseFloat(producto.precio).toFixed(2)}</p>
        ${stockDisponible ? `
          <input type="number" min="1" value="1" id="qty-${producto.id}">
          <button class="btn-add" data-id="${producto.id}">Añadir</button>
        ` : '<span class="agotado">Agotado</span>'}
      `;
      contenedor.appendChild(card);
    });

  document.querySelectorAll('.btn-add').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      agregarAlCarrito(id);
    });
  });
}

function agregarAlCarrito(id) {
  const producto = productos.find(p => p.id == id);
  const cantidadInput = document.getElementById(`qty-${id}`);
  const cantidad = parseInt(cantidadInput.value);
  if (!cantidad || cantidad <= 0) return;

  const existente = carrito.find(item => item.id == id);
  if (existente) {
    existente.cantidad += cantidad;
  } else {
    carrito.push({ ...producto, cantidad });
  }

  renderCarrito();
}

function renderCarrito() {
  cartItems.innerHTML = '';
  let totalCompra = 0;

  carrito.forEach((item, index) => {
    const subtotal = item.precio * item.cantidad;
    totalCompra += subtotal;
    const li = document.createElement('li');
    li.innerHTML = `
      ${item.nombre} x${item.cantidad} - $${subtotal.toFixed(2)}
      <button onclick="eliminarDelCarrito(${index})">Eliminar</button>
    `;
    cartItems.appendChild(li);
  });

  total.textContent = `Total: $${totalCompra.toFixed(2)}`;
}

function eliminarDelCarrito(index) {
  carrito.splice(index, 1);
  renderCarrito();
}

function abrirModalPago() {
  document.getElementById('modalPago').style.display = 'flex';
}

function cerrarModalPago() {
  document.getElementById('modalPago').style.display = 'none';
}

document.getElementById('formPago').addEventListener('submit', function (e) {
  e.preventDefault();
  if (carrito.length === 0) {
    alert("El carrito está vacío.");
    return;
  }

  const pago = Object.fromEntries(new FormData(this));
  const totalCompra = carrito.reduce((acc, item) => acc + item.precio * item.cantidad, 0);

  fetch('../PHP/guardar_pedido.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ total: totalCompra, productos: carrito, pago: pago })
  })
  .then(res => res.text())
.then(text => {
  try {
    const data = JSON.parse(text);
    if (data.success) {
      mostrarModalExito();
      cerrarModalPago();
      carrito.length = 0;
      renderCarrito();
    } else {
      alert("Error: " + data.message);
    }
  } catch (e) {
    console.error("Respuesta inválida:", text);
    alert("Error del servidor:\n" + text);
  }
})

  .catch(err => {
    console.error("Error de red:", err);
    alert("Error de red: " + err.message);
  });
});

function filtrarTipo(tipo) {
  tipoSeleccionado = tipo;
  renderProductos();
  document.getElementById('btn-producto').classList.remove('activo');
  document.getElementById('btn-servicio').classList.remove('activo');
  document.getElementById(`btn-${tipo}`).classList.add('activo');
}

cargarProductos();
</script>

<script>lucide.createIcons();</script>
</body>
</html>
