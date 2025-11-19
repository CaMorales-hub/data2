const contenedor = document.getElementById('productos-container');
const carrito = [];
const cartItems = document.getElementById('cart-items');
const total = document.getElementById('total');

let productos = [];
let tipoSeleccionado = 'producto';

function cargarProductos() {
  fetch('../PHP/obtener_productos.php')
    .then(res => res.json())
    .then(data => {
      productos = data;
      renderProductos();
    })
    .catch(err => console.error("Error al cargar productos:", err));
}

cargarProductos();

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
        <div class="info">
          <h4>${producto.nombre}</h4>
          <p>${producto.descripcion}</p>
          <p>Precio: $${parseFloat(producto.precio).toFixed(2)}</p>
          ${stockDisponible ? `
            <input type="number" min="1" value="1" id="qty-${producto.id}">
            <button class="btn-add" data-id="${producto.id}">Añadir</button>
          ` : '<span class="agotado">Agotado</span>'}
        </div>
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
      <button class="btn-eliminar" data-index="${index}">Eliminar</button>
    `;
    cartItems.appendChild(li);
  });

  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', () => {
      const index = btn.getAttribute('data-index');
      eliminarDelCarrito(index);
    });
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
    body: JSON.stringify({
      total: totalCompra,
      productos: carrito,
      pago: pago
    })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        document.getElementById('mensaje-exito').style.display = 'block';
        setTimeout(() => {
          document.getElementById('mensaje-exito').style.display = 'none';
        }, 3000);

        cerrarModalPago();
        carrito.length = 0;
        renderCarrito();
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((err) => {
      console.error("Error completo:", err);
      alert("Error al procesar el pedido:\n" + err.message);
    });
});

function filtrarTipo(tipo) {
  tipoSeleccionado = tipo;
  renderProductos();

  document.getElementById('btn-producto').classList.remove('activo');
  document.getElementById('btn-servicio').classList.remove('activo');

  if (tipo === 'producto') {
    document.getElementById('btn-producto').classList.add('activo');
  } else {
    document.getElementById('btn-servicio').classList.add('activo');
  }
}
