<!-- ==============================================================================
     VISTA DE DETALLE COMPLETO DE FACTURA (Sober SaaS)
     ============================================================================== -->

<div class="panel" style="margin-bottom: 1.5rem; padding: 1.25rem 1.5rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-main);">
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
        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Volver al Historial</span>
      </a>
      <a href="<?= APP_URL ?>/pos/imprimir/<?= $factura['id'] ?>" target="_blank" class="btn btn-primary">
        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Imprimir Ticket</span>
      </a>
    </div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
  
  <!-- TABLA DE PRODUCTOS VENDIDOS -->
  <div class="panel" style="padding: 0; overflow: hidden;">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); background: #fafafa;">
      <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--text-main); margin: 0;">Ítems / Accesorios Facturados</h3>
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
              <td style="font-variant-numeric: tabular-nums; font-size: 0.82rem; color: var(--text-muted);">
                <?= htmlspecialchars($it['codigo_barras']) ?>
              </td>
              <td style="font-weight: 600; color: var(--text-main);">
                <?= htmlspecialchars($it['producto_nombre']) ?>
              </td>
              <td style="text-align: center; font-weight: 600; font-variant-numeric: tabular-nums;">
                <?= $it['cantidad'] ?>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums;">
                $ <?= number_format($it['precio_unitario'], 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums; color: var(--danger);">
                <?= $it['descuento'] > 0 ? '- $ ' . number_format($it['descuento'], 0, ',', '.') : '$ 0' ?>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums; font-weight: 600; color: var(--text-main);">
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
    <div class="panel" style="padding: 1.25rem;">
      <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--text-main); margin-bottom: 1rem;">Datos de Venta</h3>
      <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.88rem;">
        <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Cajero:</span> <span style="font-weight: 500;"><?= htmlspecialchars($factura['cajero_nombre']) ?></span></div>
        <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Cliente:</span> <span style="font-weight: 500;"><?= htmlspecialchars($factura['cliente_nombre']) ?></span></div>
        <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Documento:</span> <span style="font-variant-numeric: tabular-nums;"><?= htmlspecialchars($factura['cliente_documento']) ?></span></div>
        <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">Método de Pago:</span> <span class="badge badge-neutral"><?= htmlspecialchars($factura['metodo_pago']) ?></span></div>
        <?php if (!empty($factura['notas'])): ?>
          <div style="background: #f4f4f5; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.6rem; font-size: 0.82rem; margin-top: 0.4rem;">
            <strong style="color: var(--text-secondary);">Notas:</strong> <?= htmlspecialchars($factura['notas']) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- TOTALES -->
    <div class="panel" style="padding: 1.25rem;">
      <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--text-main); margin-bottom: 1rem;">Resumen de Cobro</h3>
      <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.92rem;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">Subtotal:</span>
          <span style="font-variant-numeric: tabular-nums; font-weight: 500;">$ <?= number_format($factura['subtotal'], 0, ',', '.') ?></span>
        </div>
        <?php if ($factura['descuento'] > 0): ?>
        <div style="display: flex; justify-content: space-between; color: var(--danger);">
          <span>Descuento:</span>
          <span style="font-variant-numeric: tabular-nums; font-weight: 500;">- $ <?= number_format($factura['descuento'], 0, ',', '.') ?></span>
        </div>
        <?php endif; ?>
        <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: 0.25rem;">
          <span style="font-size: 1.1rem; font-weight: 700;">TOTAL:</span>
          <span style="font-size: 1.25rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--text-main);">$ <?= number_format($factura['total'], 0, ',', '.') ?></span>
        </div>
        <?php if ($factura['metodo_pago'] === 'EFECTIVO'): ?>
        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
          <span>Monto Recibido:</span>
          <span style="font-variant-numeric: tabular-nums;">$ <?= number_format($factura['monto_recibido'], 0, ',', '.') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 0.92rem; font-weight: 600; color: var(--success);">
          <span>Cambio / Vuelto:</span>
          <span style="font-variant-numeric: tabular-nums;">$ <?= number_format($factura['cambio'], 0, ',', '.') ?></span>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </div>

</div>
