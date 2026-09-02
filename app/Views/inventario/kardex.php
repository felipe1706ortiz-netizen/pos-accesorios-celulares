<!-- ==============================================================================
     VISTA DE KÁRDEX Y TRAZABILIDAD DE MOVIMIENTOS DE INVENTARIO (UI/UX PRO MAX)
     ============================================================================== -->

<div class="card" style="margin-bottom: 1.5rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h2 style="font-size: 1.45rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">
        <?= $productoSeleccionado ? 'Kárdex: ' . htmlspecialchars($productoSeleccionado['nombre']) : 'Historial de Movimientos de Inventario' ?>
      </h2>
      <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 0.35rem;">
        Registro cronológico de entradas, salidas, ventas y ajustes de existencias.
      </p>
    </div>
    <div>
      <a href="<?= APP_URL ?>/inventario" class="btn btn-outline" style="font-weight: 700;">
        <span>⬅️</span> <span>Volver a Inventario</span>
      </a>
    </div>
  </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Fecha / Hora</th>
          <th>Tipo</th>
          <th>Producto</th>
          <th>Código</th>
          <th style="text-align: center;">Cantidad</th>
          <th style="text-align: center;">Stock Ant. ➔ Nuevo</th>
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
            $tipoBadge = 'badge-info';
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
              <td style="font-size: 0.85rem; font-family: 'JetBrains Mono', monospace; color: var(--text-muted); white-space: nowrap;">
                <?= date('d/m/Y H:i', strtotime($mov['created_at'])) ?>
              </td>
              <td>
                <span class="badge <?= $tipoBadge ?>"><?= htmlspecialchars($tipo) ?></span>
              </td>
              <td style="font-weight: 700; color: var(--text-main);">
                <?= htmlspecialchars($mov['producto_nombre']) ?>
              </td>
              <td>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; background: #f8fafc; padding: 3px 8px; border-radius: 6px; border: 1px solid var(--border-color); font-weight: 600;">
                  <?= htmlspecialchars($mov['codigo_barras']) ?>
                </span>
              </td>
              <td style="text-align: center; font-weight: 800; font-family: 'JetBrains Mono', monospace; font-size: 0.95rem;">
                <span style="color: <?= $signo === '+' ? 'var(--success)' : ($signo === '-' ? 'var(--danger)' : 'var(--text-main)') ?>;">
                  <?= $signo ?><?= $mov['cantidad'] ?>
                </span>
              </td>
              <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-size: 0.88rem;">
                <span style="color: var(--text-muted);"><?= $mov['stock_anterior'] ?></span>
                <span style="color: var(--text-muted); margin: 0 4px;">➔</span>
                <strong style="color: var(--text-main); font-weight: 800;"><?= $mov['stock_nuevo'] ?></strong>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                $ <?= number_format($mov['precio_unitario'], 0, ',', '.') ?>
              </td>
              <td style="font-size: 0.88rem; color: var(--text-secondary);">
                <?= htmlspecialchars($mov['motivo']) ?>
              </td>
              <td style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">
                👤 <?= htmlspecialchars($mov['usuario_nombre']) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
