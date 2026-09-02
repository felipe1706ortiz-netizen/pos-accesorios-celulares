<!-- ==============================================================================
     VISTA DE DETALLE COMPLETO DE FACTURA
     ============================================================================== -->

<div class="card" style="margin-bottom: 1.5rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main);">
          Factura <?= htmlspecialchars($factura['numero_factura']) ?>
        </h2>
        <span class="badge <?= $factura['estado'] === 'ANULADA' ? 'badge-danger' : 'badge-success' ?>">
          <?= htmlspecialchars($factura['estado']) ?>
        </span>
      </div>
      <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
        Emitida el <?= date('d/m/Y \a \l\a\s H:i', strtotime($factura['created_at'])) ?>
      </p>
    </div>

    <div style="display: flex; gap: 0.6rem;">
      <a href="<?= APP_URL ?>/facturas" class="btn btn-outline">
        ⬅️ Volver al Historial
      </a>
      <a href="<?= APP_URL ?>/pos/imprimir/<?= $factura['id'] ?>" target="_blank" class="btn btn-primary">
        🖨️ Imprimir Ticket Térmico
      </a>
    </div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
  
  <!-- TABLA DE PRODUCTOS VENDIDOS -->
  <div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="margin: 0; padding: 1.25rem 1.5rem; background: #f8fafc;">
      <h3 class="card-title">Ítems / Accesorios Facturados</h3>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Código</th>
            <th>Producto</th>
            <th style="text-align: center;">Cantidad</th>
            <th style="text-align: right;">Precio Unit.</th>
            <th style="text-align: right;">Descuento</th>
            <th style="text-align: right;">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($factura['items'] as $it): ?>
            <tr>
              <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; color: var(--text-muted);">
                <?= htmlspecialchars($it['codigo_barras']) ?>
              </td>
              <td style="font-weight: 600;">
                <?= htmlspecialchars($it['producto_nombre']) ?>
              </td>
              <td style="text-align: center; font-weight: 700; font-family: 'JetBrains Mono', monospace;">
                <?= $it['cantidad'] ?>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">
                $ <?= number_format($it['precio_unitario'], 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace; color: var(--danger);">
                - $ <?= number_format($it['descuento'], 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 800; color: var(--primary);">
                $ <?= number_format($it['subtotal'], 0, ',', '.') ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- INFORMACIÓN DEL CLIENTE Y RESUMEN FINANCIERO -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    
    <!-- DATOS DE LA TRANSACCIÓN -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Datos de Venta</h3>
      </div>
      <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.9rem;">
        <div><strong>Cajero:</strong> <?= htmlspecialchars($factura['cajero_nombre']) ?></div>
        <div><strong>Cliente:</strong> <?= htmlspecialchars($factura['cliente_nombre']) ?></div>
        <div><strong>Documento:</strong> <?= htmlspecialchars($factura['cliente_documento']) ?></div>
        <div><strong>Método de Pago:</strong> <span class="badge badge-info"><?= htmlspecialchars($factura['metodo_pago']) ?></span></div>
        <?php if (!empty($factura['notas'])): ?>
          <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.6rem; font-size: 0.82rem; margin-top: 0.4rem;">
            <strong>Notas:</strong> <?= htmlspecialchars($factura['notas']) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- TOTALES -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Resumen de Cobro</h3>
      </div>
      <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">Subtotal:</span>
          <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">$ <?= number_format($factura['subtotal'], 0, ',', '.') ?></span>
        </div>
        <?php if ($factura['descuento'] > 0): ?>
        <div style="display: flex; justify-content: space-between; color: var(--danger);">
          <span>Descuento:</span>
          <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">- $ <?= number_format($factura['descuento'], 0, ',', '.') ?></span>
        </div>
        <?php endif; ?>
        <div style="display: flex; justify-content: space-between; border-top: 2px solid var(--border-color); padding-top: 0.75rem; margin-top: 0.25rem;">
          <span style="font-size: 1.15rem; font-weight: 800;">TOTAL:</span>
          <span style="font-size: 1.35rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: var(--primary);">$ <?= number_format($factura['total'], 0, ',', '.') ?></span>
        </div>
        <?php if ($factura['metodo_pago'] === 'EFECTIVO'): ?>
        <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: var(--text-muted); margin-top: 0.25rem;">
          <span>Monto Recibido:</span>
          <span style="font-family: 'JetBrains Mono', monospace;">$ <?= number_format($factura['monto_recibido'], 0, ',', '.') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 0.95rem; font-weight: 700; color: var(--success);">
          <span>Cambio / Vuelto:</span>
          <span style="font-family: 'JetBrains Mono', monospace;">$ <?= number_format($factura['cambio'], 0, ',', '.') ?></span>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </div>

</div>
