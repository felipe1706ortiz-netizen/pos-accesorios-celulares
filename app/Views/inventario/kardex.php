<!-- ==============================================================================
     VISTA DE KÁRDEX Y TRAZABILIDAD DE MOVIMIENTOS DE INVENTARIO (Sober SaaS)
     ============================================================================== -->

<div class="panel" style="margin-bottom: 1.5rem; padding: 1.25rem 1.5rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em;">
        <?= $productoSeleccionado ? 'Kárdex: ' . htmlspecialchars($productoSeleccionado['nombre']) : 'Historial de Movimientos de Inventario' ?>
      </h2>
      <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
        Registro cronológico de entradas, salidas, ventas y ajustes de existencias.
      </p>
    </div>
    <div>
      <a href="<?= APP_URL ?>/inventario" class="btn btn-outline">
        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Volver a Inventario</span>
      </a>
    </div>
  </div>
</div>

<div class="panel" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Fecha / Hora</th>
          <th>Tipo</th>
          <th>Producto</th>
          <th>Código</th>
          <th style="text-align: center;">Cantidad</th>
          <th style="text-align: center;">Stock Ant. &rarr; Nuevo</th>
          <th style="text-align: right;">Precio Unit.</th>
          <th>Motivo / Justificación</th>
          <th>Usuario</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($historial)): ?>
          <tr>
            <td colspan="9" style="text-align: center; padding: 3.5rem; color: var(--text-muted);">
              No se han registrado movimientos de inventario todavía.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($historial as $mov): 
            $tipo = strtoupper($mov['tipo_movimiento']);
            $tipoBadge = 'badge-neutral';
            $signo = '';
            
            if ($tipo === 'ENTRADA') {
              $tipoBadge = 'badge-success';
              $signo = '+';
            } elseif ($tipo === 'SALIDA' || $tipo === 'VENTA') {
              $tipoBadge = 'badge-danger';
              $signo = '-';
            } elseif ($tipo === 'AJUSTE') {
              $tipoBadge = 'badge-warning';
              $signo = '±';
            }
          ?>
            <tr>
              <td style="font-size: 0.82rem; font-variant-numeric: tabular-nums; color: var(--text-muted); white-space: nowrap;">
                <?= date('d/m/Y H:i', strtotime($mov['created_at'])) ?>
              </td>
              <td>
                <span class="badge <?= $tipoBadge ?>"><?= htmlspecialchars($tipo) ?></span>
              </td>
              <td style="font-weight: 600; color: var(--text-main);">
                <?= htmlspecialchars($mov['producto_nombre']) ?>
              </td>
              <td>
                <span style="font-variant-numeric: tabular-nums; font-size: 0.82rem; background: #f4f4f5; padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border-color); font-weight: 600; color: var(--text-secondary);">
                  <?= htmlspecialchars($mov['codigo_barras']) ?>
                </span>
              </td>
              <td style="text-align: center; font-weight: 600; font-variant-numeric: tabular-nums; font-size: 0.95rem;">
                <span style="color: <?= $signo === '+' ? 'var(--success)' : ($signo === '-' ? 'var(--danger)' : 'var(--text-main)') ?>;">
                  <?= $signo ?><?= $mov['cantidad'] ?>
                </span>
              </td>
              <td style="text-align: center; font-variant-numeric: tabular-nums; font-size: 0.88rem;">
                <span style="color: var(--text-muted);"><?= $mov['stock_anterior'] ?></span>
                <span style="color: var(--text-muted); margin: 0 4px;">&rarr;</span>
                <strong style="color: var(--text-main); font-weight: 600;"><?= $mov['stock_nuevo'] ?></strong>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums; font-weight: 600;">
                $ <?= number_format($mov['precio_unitario'], 0, ',', '.') ?>
              </td>
              <td style="font-size: 0.85rem; color: var(--text-secondary);">
                <?= htmlspecialchars($mov['motivo']) ?>
              </td>
              <td style="font-size: 0.85rem; color: var(--text-secondary);">
                <?= htmlspecialchars($mov['usuario_nombre']) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
