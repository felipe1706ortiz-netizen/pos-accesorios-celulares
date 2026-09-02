/**
 * ==============================================================================
 * JAVASCRIPT: MÓDULOS 1 Y 2 (INVENTARIO Y MOVIMIENTOS)
 * ==============================================================================
 * Búsqueda en vivo en tabla, manipulación de modales, ajuste rápido de stock/precio
 * y creación dinámica de categorías vía AJAX.
 * ==============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('searchProductInput');
  const catSelect = document.getElementById('filterCategorySelect');
  const stockSelect = document.getElementById('filterStockSelect');

  // 1. Filtrado en tiempo real en la tabla
  function filtrarTabla() {
    const query = (searchInput?.value || '').toLowerCase().trim();
    const selectedCat = catSelect?.value || '';
    const selectedStock = stockSelect?.value || '';

    const rows = document.querySelectorAll('#tbodyProductos tr[data-id]');

    rows.forEach(row => {
      const codigo = (row.getAttribute('data-codigo') || '').toLowerCase();
      const nombre = (row.getAttribute('data-nombre') || '').toLowerCase();
      const descripcion = (row.getAttribute('data-descripcion') || '').toLowerCase();
      const catId = row.getAttribute('data-categoria') || '';
      const stock = parseInt(row.getAttribute('data-stock') || '0', 10);
      const stockMin = parseInt(row.getAttribute('data-stock-minimo') || '5', 10);

      // Coincidencia de texto
      const matchText = !query || codigo.includes(query) || nombre.includes(query) || descripcion.includes(query);

      // Coincidencia de categoría
      const matchCat = !selectedCat || catId === selectedCat;

      // Coincidencia de stock
      let matchStock = true;
      if (selectedStock === 'ok') {
        matchStock = stock > stockMin;
      } else if (selectedStock === 'low') {
        matchStock = stock <= stockMin && stock > 0;
      } else if (selectedStock === 'out') {
        matchStock = stock === 0;
      }

      if (matchText && matchCat && matchStock) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  if (searchInput) searchInput.addEventListener('input', filtrarTabla);
  if (catSelect) catSelect.addEventListener('change', filtrarTabla);
  if (stockSelect) stockSelect.addEventListener('change', filtrarTabla);

  // 2. Manejo de formulario de Ajuste Rápido vía AJAX
  const formAjuste = document.getElementById('formAjusteRapido');
  if (formAjuste) {
    formAjuste.addEventListener('submit', async (e) => {
      e.preventDefault();

      const prodId = document.getElementById('ajuste_producto_id').value;
      const nuevoStock = parseInt(document.getElementById('ajuste_stock').value, 10);
      const nuevoPrecio = parseFloat(document.getElementById('ajuste_precio_venta').value);
      const nuevoPrecioCompra = parseFloat(document.getElementById('ajuste_precio_compra').value || 0);
      const motivo = document.getElementById('ajuste_motivo').value;

      try {
        const res = await fetchAPI(`${window.APP_URL}/inventario/ajuste-rapido`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            producto_id: prodId,
            stock: nuevoStock,
            precio_venta: nuevoPrecio,
            precio_compra: nuevoPrecioCompra,
            motivo: motivo
          })
        });

        if (res.success) {
          showToast('✅ ' + res.message, 'success');
          closeModal('modalAjusteRapido');

          // Actualizar fila visual en la tabla directamente sin reload
          actualizarFilaVisual(prodId, nuevoStock, nuevoPrecio);
        } else {
          showToast('⚠️ ' + (res.message || 'Error al guardar ajuste'), 'danger');
        }
      } catch (err) {
        showToast('Error de comunicación con el servidor', 'danger');
      }
    });
  }

  // 3. Manejo de creación rápida de categoría vía AJAX
  const formCategoria = document.getElementById('formNuevaCategoria');
  if (formCategoria) {
    formCategoria.addEventListener('submit', async (e) => {
      e.preventDefault();

      const nombre = document.getElementById('cat_nombre').value.trim();
      const desc = document.getElementById('cat_desc').value.trim();

      if (!nombre) return;

      const formData = new FormData();
      formData.append('nombre', nombre);
      formData.append('descripcion', desc);

      try {
        const res = await fetchAPI(`${window.APP_URL}/inventario/categorias/guardar`, {
          method: 'POST',
          body: formData
        });

        if (res.success && res.categoria) {
          showToast('✅ Categoría creada exitosamente', 'success');
          closeModal('modalNuevaCategoria');
          formCategoria.reset();

          // Agregar nueva categoría a todos los selectores
          const selects = ['filterCategorySelect', 'new_categoria', 'edit_categoria'];
          selects.forEach(id => {
            const selectEl = document.getElementById(id);
            if (selectEl) {
              const option = document.createElement('option');
              option.value = res.categoria.id;
              option.textContent = res.categoria.nombre;
              selectEl.appendChild(option);
            }
          });
        } else {
          showToast('⚠️ ' + (res.message || 'Error al crear categoría'), 'warning');
        }
      } catch (err) {
        showToast('Error al procesar categoría', 'danger');
      }
    });
  }
});

/**
 * Abre y prellena el modal de edición de producto
 * @param {number} id 
 */
function abrirEditarProducto(id) {
  const row = document.getElementById(`row-prod-${id}`);
  if (!row) return;

  const form = document.getElementById('formEditarProducto');
  form.action = `${window.APP_URL}/inventario/actualizar/${id}`;

  document.getElementById('edit_codigo').value = row.getAttribute('data-codigo');
  document.getElementById('edit_nombre').value = row.getAttribute('data-nombre');
  document.getElementById('edit_categoria').value = row.getAttribute('data-categoria');
  document.getElementById('edit_precio_compra').value = row.getAttribute('data-precio-compra');
  document.getElementById('edit_precio_venta').value = row.getAttribute('data-precio-venta');
  document.getElementById('edit_stock_minimo').value = row.getAttribute('data-stock-minimo');
  document.getElementById('edit_descripcion').value = row.getAttribute('data-descripcion');

  openModal('modalEditarProducto');
}

/**
 * Abre y prellena el modal de Ajuste Rápido (Módulo 2)
 * @param {number} id 
 */
function abrirAjusteRapido(id) {
  const row = document.getElementById(`row-prod-${id}`);
  if (!row) return;

  document.getElementById('ajuste_producto_id').value = id;
  document.getElementById('ajuste_prod_nombre').textContent = row.getAttribute('data-nombre');
  document.getElementById('ajuste_prod_codigo').textContent = `Código: ${row.getAttribute('data-codigo')}`;
  document.getElementById('ajuste_stock').value = row.getAttribute('data-stock');
  document.getElementById('ajuste_precio_venta').value = row.getAttribute('data-precio-venta');
  document.getElementById('ajuste_precio_compra').value = row.getAttribute('data-precio-compra');
  document.getElementById('ajuste_motivo').value = 'Ajuste rápido de inventario';

  openModal('modalAjusteRapido');
}

/**
 * Actualiza los elementos DOM de la fila tras un ajuste AJAX
 */
function actualizarFilaVisual(id, nuevoStock, nuevoPrecio) {
  const row = document.getElementById(`row-prod-${id}`);
  if (!row) return;

  const stockMin = parseInt(row.getAttribute('data-stock-minimo') || '5', 10);
  row.setAttribute('data-stock', nuevoStock);
  row.setAttribute('data-precio-venta', nuevoPrecio);

  // Actualizar Badge de Stock
  const stockPill = document.getElementById(`stock-pill-${id}`);
  if (stockPill) {
    stockPill.className = 'badge stock-pill';
    if (nuevoStock === 0) {
      stockPill.classList.add('badge-danger');
      stockPill.textContent = '0 (Agotado)';
    } else if (nuevoStock <= stockMin) {
      stockPill.classList.add('badge-warning');
      stockPill.textContent = `${nuevoStock} (Bajo)`;
    } else {
      stockPill.classList.add('badge-success');
      stockPill.textContent = `${nuevoStock} unids`;
    }
  }

  // Actualizar Precio de Venta en la tabla
  const precioEl = document.getElementById(`precio-venta-${id}`);
  if (precioEl) {
    precioEl.textContent = `$ ${new Intl.NumberFormat('es-CO').format(nuevoPrecio)}`;
  }
}
