/**
 * ==============================================================================
 * JAVASCRIPT: MÓDULO 3 (NÚCLEO POS - VENTA RÁPIDA & KEYBOARD-FIRST)
 * ==============================================================================
 * Control del escáner con foco persistente, atajos globales de teclado (F2, F4, F8, F12),
 * carrito reactivo en memoria, cálculo de cambio y bucle de hard-reset (<500ms).
 * ==============================================================================
 */

// Estado global del carrito POS
let posState = {
  cart: [],
  descuentoGlobal: 0,
  metodoPago: 'EFECTIVO',
  selectedSearchIndex: -1,
  searchResults: []
};

document.addEventListener('DOMContentLoaded', () => {
  const scannerInput = document.getElementById('barcodeScanner');

  // 1. Enfoque persistente inicial en el lector de códigos de barras
  enfocarEscaner();

  // Re-enfocar automáticamente si el usuario hace clic fuera de inputs
  document.addEventListener('click', (e) => {
    const isInsideModal = e.target.closest('.modal-backdrop.active');
    const isInput = ['INPUT', 'TEXTAREA', 'SELECT', 'BUTTON'].includes(e.target.tagName);
    if (!isInsideModal && !isInput) {
      enfocarEscaner();
    }
  });

  // 2. Manejo de lectura del escáner (Enter automático o manual)
  if (scannerInput) {
    scannerInput.addEventListener('keydown', async (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        const codigo = scannerInput.value.trim();

        if (codigo !== '') {
          await buscarYAñadirPorCodigo(codigo);
        } else {
          // Si el input está vacío y se presiona ENTER, abrir checkout si hay items
          if (posState.cart.length > 0) {
            abrirModalCobro();
          }
        }
      }
    });
  }

  // 3. Atajos de teclado globales (F2, F4, F8, F12, ESC)
  document.addEventListener('keydown', (e) => {
    // F2: Búsqueda manual de productos
    if (e.key === 'F2') {
      e.preventDefault();
      abrirModalBusqueda();
      return;
    }

    // F4: Descuento global
    if (e.key === 'F4') {
      e.preventDefault();
      if (posState.cart.length > 0) {
        abrirModalDescuento();
      } else {
        showToast('Agregue productos al carrito antes de aplicar descuento', 'warning');
      }
      return;
    }

    // F6: Ver Estado de Gaveta / Saldo en Efectivo
    if (e.key === 'F6') {
      e.preventDefault();
      abrirModalEstadoGaveta();
      return;
    }

    // F9: Abrir Gaveta Física (Pulso ESC/POS)
    if (e.key === 'F9') {
      e.preventDefault();
      dispararAperturaGaveta();
      return;
    }

    // F8: Cancelar venta / vaciar carrito
    if (e.key === 'F8') {
      e.preventDefault();
      cancelarVenta();
      return;
    }

    // F12: Modal de Cobro / Checkout
    if (e.key === 'F12') {
      e.preventDefault();
      abrirModalCobro();
      return;
    }

    // ESC: Cerrar cualquier modal activo y devolver foco al escáner
    if (e.key === 'Escape') {
      cerrarTodosLosModales();
      return;
    }
  });

  // 4. Búsqueda manual (F2): Navegación con teclado en resultados
  const manualInput = document.getElementById('manualSearchInput');
  if (manualInput) {
    manualInput.addEventListener('input', debounce(ejecutarBusquedaManual, 200));

    manualInput.addEventListener('keydown', (e) => {
      const rows = document.querySelectorAll('#tbodyBusquedaManual tr[data-search-idx]');
      if (rows.length === 0) return;

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        posState.selectedSearchIndex = Math.min(posState.selectedSearchIndex + 1, rows.length - 1);
        actualizarFilaSeleccionadaBusqueda();
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        posState.selectedSearchIndex = Math.max(posState.selectedSearchIndex - 1, 0);
        actualizarFilaSeleccionadaBusqueda();
      } else if (e.key === 'Enter') {
        e.preventDefault();
        if (posState.selectedSearchIndex >= 0 && posState.searchResults[posState.selectedSearchIndex]) {
          const prod = posState.searchResults[posState.selectedSearchIndex];
          agregarProductoAlCarrito(prod);
          cerrarModalBusqueda();
        }
      }
    });
  }

  // 5. Modal de Cobro: Entrada de Monto Recibido y cálculo de cambio en tiempo real
  const montoRecibidoEl = document.getElementById('montoRecibidoInput');
  if (montoRecibidoEl) {
    montoRecibidoEl.addEventListener('input', calcularCambio);
    montoRecibidoEl.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        confirmarVenta();
      }
    });
  }
});

/**
 * Mantiene el foco en el input del escáner de códigos de barras
 */
function enfocarEscaner() {
  const scanner = document.getElementById('barcodeScanner');
  if (scanner && !document.querySelector('.modal-backdrop.active')) {
    scanner.focus();
    scanner.select();
  }
}

/**
 * Consulta la API y agrega el producto por código de barras
 */
async function buscarYAñadirPorCodigo(codigo) {
  const scanner = document.getElementById('barcodeScanner');

  try {
    const res = await fetchAPI(`${window.APP_URL}/pos/buscar-producto?codigo=${encodeURIComponent(codigo)}`);

    if (res.success && res.producto) {
      agregarProductoAlCarrito(res.producto);
      scanner.value = '';
      enfocarEscaner();
    } else {
      showToast(res.message || 'Producto no encontrado', 'danger');
      scanner.select();
    }
  } catch (err) {
    showToast('Error al consultar código de barras', 'danger');
  }
}

/**
 * Agrega un producto al carrito reactivo o incrementa su cantidad
 */
function agregarProductoAlCarrito(producto, cantidad = 1) {
  const prodId = parseInt(producto.id, 10);
  const itemExistente = posState.cart.find(it => it.id === prodId);

  const stockDisponible = parseInt(producto.stock, 10);

  if (itemExistente) {
    if (itemExistente.cantidad + cantidad > stockDisponible) {
      showToast(`Stock insuficiente. Máximo disponible: ${stockDisponible} unidades`, 'warning');
      return;
    }
    itemExistente.cantidad += cantidad;
  } else {
    if (cantidad > stockDisponible) {
      showToast(`Stock insuficiente. Disponible: ${stockDisponible} unidades`, 'warning');
      return;
    }

    posState.cart.push({
      id: prodId,
      codigo_barras: producto.codigo_barras,
      nombre: producto.nombre,
      precio: parseFloat(producto.precio_venta),
      stock: stockDisponible,
      cantidad: cantidad,
      categoria: producto.categoria || '',
      descuento: 0
    });
  }

  renderizarCarrito();
  showToast(`Añadido: ${producto.nombre}`, 'success', 1500);
}

/**
 * Modifica la cantidad de un ítem en el carrito
 */
function cambiarCantidad(prodId, delta) {
  const item = posState.cart.find(it => it.id === prodId);
  if (!item) return;

  const nuevaCant = item.cantidad + delta;

  if (nuevaCant <= 0) {
    eliminarItemCarrito(prodId);
    return;
  }

  if (nuevaCant > item.stock) {
    showToast(`Stock máximo alcanzado (${item.stock} unids)`, 'warning');
    return;
  }

  item.cantidad = nuevaCant;
  renderizarCarrito();
}

/**
 * Modifica directamente la cantidad tecleada
 */
function fijarCantidad(prodId, inputEl) {
  const item = posState.cart.find(it => it.id === prodId);
  if (!item) return;

  let val = parseInt(inputEl.value, 10);
  if (isNaN(val) || val <= 0) val = 1;

  if (val > item.stock) {
    showToast(`Stock máximo alcanzado (${item.stock} unids)`, 'warning');
    val = item.stock;
  }

  item.cantidad = val;
  renderizarCarrito();
}

/**
 * Elimina un ítem del carrito
 */
function eliminarItemCarrito(prodId) {
  posState.cart = posState.cart.filter(it => it.id !== prodId);
  renderizarCarrito();
  enfocarEscaner();
}

/**
 * Vacía el carrito completo
 */
function cancelarVenta() {
  if (posState.cart.length === 0) return;

  if (confirm('¿Está seguro de cancelar la venta y vaciar el carrito?')) {
    posState.cart = [];
    posState.descuentoGlobal = 0;
    renderizarCarrito();
    showToast('Venta cancelada. Carrito vacío.', 'info');
    enfocarEscaner();
  }
}

/**
 * Renderiza la tabla del carrito y actualiza todos los totales reactivamente
 */
function renderizarCarrito() {
  const tbody = document.getElementById('cartTableBody');
  const countBadge = document.getElementById('cartItemCountBadge');

  if (!tbody) return;

  if (posState.cart.length === 0) {
    tbody.innerHTML = `
      <tr id="emptyCartRow">
        <td colspan="5" style="text-align: center; padding: 4rem 1.5rem; color: var(--text-muted);">
          <div style="font-size: 3rem; margin-bottom: 0.5rem; opacity: 0.6;">📦</div>
          <div style="font-size: 1.1rem; font-weight: 600; color: var(--text-main);">El carrito está vacío</div>
          <div style="font-size: 0.88rem; margin-top: 0.25rem;">
            Escanee un código de barras o presione <kbd>F2</kbd> para buscar accesorios manualmente.
          </div>
        </td>
      </tr>
    `;
    if (countBadge) countBadge.textContent = '0 ítems';
    actualizarTotales(0, 0, 0);
    return;
  }

  let html = '';
  let subtotalBruto = 0;
  let totalItemsCount = 0;

  posState.cart.forEach((it, idx) => {
    const subtotalLinea = it.cantidad * it.precio;
    subtotalBruto += subtotalLinea;
    totalItemsCount += it.cantidad;

    html += `
      <tr id="cart-row-${it.id}">
        <td>
          <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-main);">${escapeHtml(it.nombre)}</div>
          <div style="font-size: 0.78rem; color: var(--text-muted); font-family: 'JetBrains Mono', monospace;">
            ${escapeHtml(it.codigo_barras)} ${it.categoria ? '• ' + escapeHtml(it.categoria) : ''}
          </div>
        </td>
        <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 600; font-size: 0.95rem;">
          $ ${formatearMoneda(it.precio)}
        </td>
        <td style="text-align: center;">
          <div class="qty-control">
            <button type="button" class="qty-btn" onclick="cambiarCantidad(${it.id}, -1)">−</button>
            <input type="text" class="qty-input" value="${it.cantidad}" onchange="fijarCantidad(${it.id}, this)" onclick="this.select()">
            <button type="button" class="qty-btn" onclick="cambiarCantidad(${it.id}, 1)">+</button>
          </div>
        </td>
        <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 800; font-size: 1rem; color: var(--primary);">
          $ ${formatearMoneda(subtotalLinea)}
        </td>
        <td style="text-align: center;">
          <button type="button" class="btn btn-outline" style="padding: 0.25rem 0.5rem; color: var(--danger); border-color: transparent;" onclick="eliminarItemCarrito(${it.id})" title="Quitar ítem">
            ✕
          </button>
        </td>
      </tr>
    `;
  });

  tbody.innerHTML = html;
  if (countBadge) countBadge.textContent = `${totalItemsCount} ítem${totalItemsCount > 1 ? 's' : ''}`;

  actualizarTotales(subtotalBruto, posState.descuentoGlobal, 0);
}

/**
 * Calcula y renderiza el resumen financiero
 */
function actualizarTotales(subtotal, descuento, impuesto) {
  const total = Math.max(0, subtotal - descuento + impuesto);

  const subtotalEl = document.getElementById('posSubtotal');
  const descuentoEl = document.getElementById('posDescuento');
  const grandTotalEl = document.getElementById('posGrandTotal');
  const modalTotalEl = document.getElementById('modalCobroTotal');

  if (subtotalEl) subtotalEl.textContent = `$ ${formatearMoneda(subtotal)}`;
  if (descuentoEl) descuentoEl.textContent = `- $ ${formatearMoneda(descuento)}`;
  if (grandTotalEl) grandTotalEl.textContent = `$ ${formatearMoneda(total)}`;
  if (modalTotalEl) modalTotalEl.textContent = `$ ${formatearMoneda(total)}`;

  return { subtotal, descuento, impuesto, total };
}

/**
 * Retorna el total numérico neto a pagar
 */
function obtenerGranTotal() {
  const subtotal = posState.cart.reduce((acc, it) => acc + (it.cantidad * it.precio), 0);
  return Math.max(0, subtotal - posState.descuentoGlobal);
}

// ------------------------------------------------------------------------------
// MODAL F2: BÚSQUEDA MANUAL DE PRODUCTOS
// ------------------------------------------------------------------------------
function abrirModalBusqueda() {
  openModal('modalBusquedaManual');
  const input = document.getElementById('manualSearchInput');
  if (input) {
    input.value = '';
    input.focus();
    ejecutarBusquedaManual();
  }
}

function cerrarModalBusqueda() {
  closeModal('modalBusquedaManual');
  enfocarEscaner();
}

async function ejecutarBusquedaManual() {
  const input = document.getElementById('manualSearchInput');
  const q = input ? input.value.trim() : '';
  const tbody = document.getElementById('tbodyBusquedaManual');

  if (!tbody) return;

  try {
    const res = await fetchAPI(`${window.APP_URL}/pos/buscar-producto?q=${encodeURIComponent(q)}`);
    if (res.success && res.productos) {
      posState.searchResults = res.productos;
      posState.selectedSearchIndex = res.productos.length > 0 ? 0 : -1;

      if (res.productos.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 2rem; color: var(--text-muted);">No se encontraron accesorios coincidentes.</td></tr>`;
        return;
      }

      let html = '';
      res.productos.forEach((p, idx) => {
        html += `
          <tr data-search-idx="${idx}" class="${idx === 0 ? 'search-row-selected' : ''}" style="cursor: pointer; ${idx === 0 ? 'background: var(--primary-light);' : ''}" onclick="seleccionarProductoDeBusqueda(${idx})">
            <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem;">${escapeHtml(p.codigo_barras)}</td>
            <td style="font-weight: 600;">${escapeHtml(p.nombre)}</td>
            <td><span class="badge badge-info">${escapeHtml(p.categoria)}</span></td>
            <td style="text-align: center; font-weight: 700; color: ${p.stock <= 5 ? 'var(--warning)' : 'var(--success)'};">${p.stock}</td>
            <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700;">$ ${formatearMoneda(p.precio_venta)}</td>
            <td style="text-align: center;">
              <button type="button" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">+ Añadir</button>
            </td>
          </tr>
        `;
      });
      tbody.innerHTML = html;
    }
  } catch (e) {
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color: var(--danger); padding: 1.5rem;">Error al buscar</td></tr>`;
  }
}

function actualizarFilaSeleccionadaBusqueda() {
  const rows = document.querySelectorAll('#tbodyBusquedaManual tr[data-search-idx]');
  rows.forEach((row, idx) => {
    if (idx === posState.selectedSearchIndex) {
      row.style.backgroundColor = 'var(--primary-light)';
      row.scrollIntoView({ block: 'nearest' });
    } else {
      row.style.backgroundColor = '';
    }
  });
}

function seleccionarProductoDeBusqueda(idx) {
  if (posState.searchResults[idx]) {
    agregarProductoAlCarrito(posState.searchResults[idx]);
    cerrarModalBusqueda();
  }
}

// ------------------------------------------------------------------------------
// MODAL F4: DESCUENTO GLOBAL
// ------------------------------------------------------------------------------
function abrirModalDescuento() {
  openModal('modalDescuento');
  const input = document.getElementById('descuentoValor');
  if (input) {
    input.value = posState.descuentoGlobal || '';
    input.focus();
    input.select();
  }
}

function aplicarDescuentoGlobal() {
  const input = document.getElementById('descuentoValor');
  const val = parseFloat(input.value) || 0;
  const subtotal = posState.cart.reduce((acc, it) => acc + (it.cantidad * it.precio), 0);

  if (val < 0 || val > subtotal) {
    showToast('El descuento no puede ser negativo ni superar el subtotal', 'warning');
    return;
  }

  posState.descuentoGlobal = val;
  renderizarCarrito();
  closeModal('modalDescuento');
  showToast(`Descuento de $ ${formatearMoneda(val)} aplicado`, 'success');
  enfocarEscaner();
}

// ------------------------------------------------------------------------------
// MODAL F12: CHECKOUT / COBRO RÁPIDO & HARD RESET LOOP (<500ms)
// ------------------------------------------------------------------------------
function abrirModalCobro() {
  if (posState.cart.length === 0) {
    showToast('El carrito está vacío. Agregue productos antes de cobrar.', 'warning');
    enfocarEscaner();
    return;
  }

  openModal('modalCobro');
  posState.metodoPago = 'EFECTIVO';
  cambiarMetodoPago('EFECTIVO');

  const total = obtenerGranTotal();
  const inputMonto = document.getElementById('montoRecibidoInput');

  if (inputMonto) {
    inputMonto.value = '';
    inputMonto.placeholder = `$ ${formatearMoneda(total)}`;
    setTimeout(() => {
      inputMonto.focus();
      calcularCambio();
    }, 100);
  }
}

function cerrarModalCobro() {
  closeModal('modalCobro');
  enfocarEscaner();
}

function cambiarMetodoPago(metodo) {
  posState.metodoPago = metodo;
  const seccionEfectivo = document.getElementById('seccionEfectivo');
  
  if (seccionEfectivo) {
    seccionEfectivo.style.display = (metodo === 'EFECTIVO') ? 'block' : 'none';
  }

  if (metodo !== 'EFECTIVO') {
    const inputMonto = document.getElementById('montoRecibidoInput');
    if (inputMonto) inputMonto.value = obtenerGranTotal();
  }
}

function establecerMontoExacto() {
  const total = obtenerGranTotal();
  const input = document.getElementById('montoRecibidoInput');
  if (input) {
    input.value = total;
    calcularCambio();
    input.focus();
  }
}

function agregarBilletes(monto) {
  const input = document.getElementById('montoRecibidoInput');
  if (input) {
    const actual = parseFloat(input.value) || 0;
    input.value = actual + monto;
    calcularCambio();
    input.focus();
  }
}

function calcularCambio() {
  const total = obtenerGranTotal();
  const input = document.getElementById('montoRecibidoInput');
  const display = document.getElementById('cambioCalculadoDisplay');

  if (!display) return;

  const recibido = parseFloat(input ? input.value : 0) || 0;
  const cambio = recibido - total;

  if (cambio >= 0) {
    display.textContent = `$ ${formatearMoneda(cambio)}`;
    display.style.color = '#059669';
  } else {
    display.textContent = `Faltan $ ${formatearMoneda(Math.abs(cambio))}`;
    display.style.color = '#dc2626';
  }
}

/**
 * Confirma y asienta la venta de forma atómica en el backend
 */
async function confirmarVenta() {
  const total = obtenerGranTotal();
  const inputRecibido = document.getElementById('montoRecibidoInput');
  const montoRecibido = (posState.metodoPago === 'EFECTIVO') 
    ? (parseFloat(inputRecibido ? inputRecibido.value : 0) || total)
    : total;

  if (posState.metodoPago === 'EFECTIVO' && montoRecibido < total) {
    showToast('El monto recibido es menor al total a pagar', 'warning');
    if (inputRecibido) inputRecibido.focus();
    return;
  }

  const cambio = Math.max(0, montoRecibido - total);
  const clienteNombre = document.getElementById('clienteNombreInput')?.value || 'Cliente General';
  const clienteDoc = document.getElementById('clienteDocInput')?.value || '222222222222';

  const payload = {
    subtotal: posState.cart.reduce((acc, it) => acc + (it.cantidad * it.precio), 0),
    descuento: posState.descuentoGlobal,
    impuesto: 0,
    total: total,
    metodo_pago: posState.metodoPago,
    monto_recibido: montoRecibido,
    cambio: cambio,
    cliente_nombre: clienteNombre,
    cliente_documento: clienteDoc,
    items: posState.cart
  };

  const btnConfirmar = document.getElementById('btnConfirmarVenta');
  if (btnConfirmar) {
    btnConfirmar.disabled = true;
    btnConfirmar.innerHTML = '<span>Procesando...</span>';
  }

  try {
    const res = await fetchAPI(`${window.APP_URL}/pos/procesar-venta`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    if (res.success) {
      // 1. Disparar orden de impresión térmica silenciosa en el iframe
      const printIframe = document.getElementById('printIframe');
      if (printIframe && res.print_url) {
        printIframe.src = res.print_url;
      }

      showToast(`✅ Venta #${res.numero_factura} exitosa. Cambio: $ ${formatearMoneda(res.cambio)}`, 'success', 4000);

      // 2. BUCLE DE HARD RESET CONTINUO (< 500ms)
      closeModal('modalCobro');
      posState.cart = [];
      posState.descuentoGlobal = 0;
      renderizarCarrito();

      // Restaurar botón
      if (btnConfirmar) {
        btnConfirmar.disabled = false;
        btnConfirmar.innerHTML = '<span>Confirmar e Imprimir [ENTER]</span>';
      }

      // Cursor inmediatamente enfocado en el escáner para el siguiente cliente
      setTimeout(enfocarEscaner, 100);

    } else {
      showToast(res.message || 'Error al procesar venta', 'danger');
      if (btnConfirmar) {
        btnConfirmar.disabled = false;
        btnConfirmar.innerHTML = '<span>Confirmar e Imprimir [ENTER]</span>';
      }
    }
  } catch (err) {
    showToast('Error de comunicación al registrar la venta', 'danger');
    if (btnConfirmar) {
      btnConfirmar.disabled = false;
      btnConfirmar.innerHTML = '<span>Confirmar e Imprimir [ENTER]</span>';
    }
  }
}

function cerrarTodosLosModales() {
  document.querySelectorAll('.modal-backdrop.active').forEach(m => m.classList.remove('active'));
  document.body.style.overflow = '';
  enfocarEscaner();
}

// ------------------------------------------------------------------------------
// HELPERS
// ------------------------------------------------------------------------------
function formatearMoneda(num) {
  return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num || 0);
}

function escapeHtml(text) {
  if (!text) return '';
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function debounce(func, wait) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}
