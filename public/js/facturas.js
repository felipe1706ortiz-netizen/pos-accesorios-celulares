/**
 * ==============================================================================
 * JAVASCRIPT: MÓDULO 4 (HISTORIAL Y AUDITORÍA DE FACTURAS)
 * ==============================================================================
 * Visualización de detalles vía AJAX, reimpresión silenciosa y anulación de ventas.
 * ==============================================================================
 */

/**
 * Consulta la API y muestra el detalle de la factura en el modal rápido
 * @param {number} facturaId 
 */
async function verDetalleFactura(facturaId) {
  openModal('modalDetalleFactura');
  const tituloEl = document.getElementById('modalDetalleTitulo');
  const contenidoEl = document.getElementById('modalDetalleContenido');
  const btnPrint = document.getElementById('btnImprimirModal');

  tituloEl.textContent = 'Cargando...';
  contenidoEl.innerHTML = `
    <div style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
      <div style="font-size: 2rem; margin-bottom: 0.5rem;">⏳</div>
      <div>Consultando detalles de la factura #${facturaId}...</div>
    </div>
  `;

  try {
    const res = await fetchAPI(`${window.APP_URL}/facturas/detalle/${facturaId}`, {
      headers: { 'Accept': 'application/json' }
    });

    if (res.success && res.factura) {
      const f = res.factura;
      tituloEl.textContent = `Factura: ${f.numero_factura}`;

      if (btnPrint) {
        btnPrint.onclick = () => reimprimirTicket(f.id);
      }

      const isAnulada = (f.estado === 'ANULADA');
      const badgeClass = isAnulada ? 'badge-danger' : 'badge-success';

      let itemsHtml = '';
      if (f.items && f.items.length > 0) {
        f.items.forEach(it => {
          itemsHtml += `
            <tr>
              <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; color: var(--text-muted);">${escapeHtml(it.codigo_barras)}</td>
              <td style="font-weight: 600;">${escapeHtml(it.producto_nombre)}</td>
              <td style="text-align: center; font-weight: 700; font-family: 'JetBrains Mono', monospace;">${it.cantidad}</td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">$ ${formatearMoneda(it.precio_unitario)}</td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 800; color: var(--primary);">$ ${formatearMoneda(it.subtotal)}</td>
            </tr>
          `;
        });
      }

      contenidoEl.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
          <div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">Fecha: <strong>${f.created_at}</strong></div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">Cajero: <strong>${escapeHtml(f.cajero_nombre)}</strong></div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">Cliente: <strong>${escapeHtml(f.cliente_nombre)} (${escapeHtml(f.cliente_documento)})</strong></div>
          </div>
          <div style="text-align: right;">
            <span class="badge ${badgeClass}" style="font-size: 0.9rem; margin-bottom: 0.35rem;">${f.estado}</span>
            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">Método: ${f.metodo_pago}</div>
          </div>
        </div>

        <div style="max-height: 250px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: var(--radius-md); margin-bottom: 1rem;">
          <table class="table" style="margin: 0;">
            <thead>
              <tr>
                <th>Código</th>
                <th>Producto</th>
                <th style="text-align: center;">Cant.</th>
                <th style="text-align: right;">P. Unit.</th>
                <th style="text-align: right;">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              ${itemsHtml}
            </tbody>
          </table>
        </div>

        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; font-size: 0.9rem;">
            <span style="color: var(--text-muted);">Subtotal:</span>
            <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">$ ${formatearMoneda(f.subtotal)}</span>
          </div>
          ${f.descuento > 0 ? `
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; font-size: 0.9rem; color: var(--danger);">
              <span>Descuento:</span>
              <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">- $ ${formatearMoneda(f.descuento)}</span>
            </div>
          ` : ''}
          <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; border-top: 1px solid var(--border-color); padding-top: 0.5rem; margin-top: 0.35rem;">
            <span>TOTAL:</span>
            <span style="font-family: 'JetBrains Mono', monospace; color: var(--primary);">$ ${formatearMoneda(f.total)}</span>
          </div>
          ${f.metodo_pago === 'EFECTIVO' ? `
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-muted); margin-top: 0.35rem;">
              <span>Recibido: $ ${formatearMoneda(f.monto_recibido)}</span>
              <span style="font-weight: 700; color: var(--success);">Cambio: $ ${formatearMoneda(f.cambio)}</span>
            </div>
          ` : ''}
          ${f.notas ? `
            <div style="margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px dashed var(--border-color); font-size: 0.8rem; color: var(--text-muted);">
              <strong>Notas:</strong> ${escapeHtml(f.notas)}
            </div>
          ` : ''}
        </div>
      `;
    } else {
      contenidoEl.innerHTML = `<div style="text-align: center; color: var(--danger); padding: 2rem;">Error al cargar la factura.</div>`;
    }
  } catch (err) {
    contenidoEl.innerHTML = `<div style="text-align: center; color: var(--danger); padding: 2rem;">Error de conexión con el servidor.</div>`;
  }
}

/**
 * Dispara la reimpresión del ticket térmico en el iframe oculto
 * @param {number} facturaId 
 */
function reimprimirTicket(facturaId) {
  const iframe = document.getElementById('printIframe');
  if (iframe) {
    iframe.src = `${window.APP_URL}/pos/imprimir/${facturaId}`;
    showToast(`🖨️ Enviando factura #${facturaId} a impresión...`, 'info', 2000);
  } else {
    window.open(`${window.APP_URL}/pos/imprimir/${facturaId}`, '_blank');
  }
}

/**
 * Abre el modal de confirmación para anulación de factura
 * @param {number} facturaId 
 * @param {string} numeroFactura 
 */
function abrirModalAnulacion(facturaId, numeroFactura) {
  const form = document.getElementById('formAnularFactura');
  const display = document.getElementById('anularNumeroFacturaDisplay');

  if (form) form.action = `${window.APP_URL}/facturas/anular/${facturaId}`;
  if (display) display.textContent = numeroFactura;

  const motivoInput = document.getElementById('anular_motivo');
  if (motivoInput) {
    motivoInput.value = '';
  }

  openModal('modalAnularFactura');
  if (motivoInput) motivoInput.focus();
}

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
