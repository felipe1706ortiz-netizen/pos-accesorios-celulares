<!-- ==============================================================================
     VISTA: HISTORIAL DE ARQUEOS Y CIERRES DE CAJA (ADMIN)
     ============================================================================== -->

<div class="card" style="margin-bottom: 1.5rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--text-main);">
        Historial de Turnos y Cierres de Caja
      </h2>
      <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
        Auditoría de conciliaciones pasadas, sobrantes, faltantes y reimpresión de arqueos.
      </p>
    </div>

    <div>
      <a href="<?= APP_URL ?>/caja" class="btn btn-outline">
        ⬅️ Volver a Caja Activa
      </a>
    </div>
  </div>
</div>

<!-- TABLA DEL HISTORIAL DE SESIONES -->
<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Turno #</th>
          <th>Cajero</th>
          <th>Apertura</th>
          <th>Cierre</th>
          <th style="text-align: right;">Base Inicial</th>
          <th style="text-align: right;">Ventas Efectivo</th>
          <th style="text-align: right;">Saldo Esperado</th>
          <th style="text-align: right;">Efectivo Real</th>
          <th style="text-align: center;">Diferencia</th>
          <th style="text-align: center;">Estado</th>
          <th style="text-align: right;">Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($historial)): ?>
          <tr>
            <td colspan="11" style="text-align: center; padding: 3rem; color: var(--text-muted);">
              No se han registrado turnos de caja cerrados todavía.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($historial as $s): 
            $dif = (float)($s['diferencia'] ?? 0);
            $isAbierta = ($s['estado'] === 'ABIERTA');
          ?>
            <tr>
              <td>
                <strong style="font-family: 'JetBrains Mono', monospace; font-size: 0.95rem; color: var(--primary);">
                  #<?= $s['id'] ?>
                </strong>
              </td>
              <td style="font-weight: 600;">
                👤 <?= htmlspecialchars($s['usuario_nombre']) ?>
              </td>
              <td style="font-size: 0.82rem; font-family: 'JetBrains Mono', monospace; color: var(--text-muted); white-space: nowrap;">
                <?= date('d/m/Y H:i', strtotime($s['fecha_apertura'])) ?>
              </td>
              <td style="font-size: 0.82rem; font-family: 'JetBrains Mono', monospace; color: var(--text-muted); white-space: nowrap;">
                <?= $s['fecha_cierre'] ? date('d/m/Y H:i', strtotime($s['fecha_cierre'])) : '-' ?>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">
                $ <?= number_format($s['monto_inicial'], 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace; color: var(--success);">
                $ <?= number_format($s['total_ventas_efectivo'], 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                $ <?= number_format($s['monto_esperado'], 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 800;">
                <?= $s['monto_real'] !== null ? '$ ' . number_format($s['monto_real'], 0, ',', '.') : '-' ?>
              </td>
              <td style="text-align: center;">
                <?php if ($s['monto_real'] !== null): ?>
                  <?php if ($dif === 0.0): ?>
                    <span class="badge badge-success">Cuadre ($0)</span>
                  <?php elseif ($dif > 0): ?>
                    <span class="badge badge-info">+ $ <?= number_format($dif, 0, ',', '.') ?> (Sobrante)</span>
                  <?php else: ?>
                    <span class="badge badge-danger">- $ <?= number_format(abs($dif), 0, ',', '.') ?> (Faltante)</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="color: var(--text-muted);">-</span>
                <?php endif; ?>
              </td>
              <td style="text-align: center;">
                <span class="badge <?= $isAbierta ? 'badge-success' : 'badge-info' ?>">
                  <?= $s['estado'] ?>
                </span>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <?php if (!$isAbierta): ?>
                  <a href="<?= APP_URL ?>/caja/ticket/<?= $s['id'] ?>" target="_blank" class="btn btn-outline" style="padding: 0.35rem 0.6rem; font-size: 0.82rem;" title="Reimprimir Ticket de Arqueo">
                    🖨️ Arqueo
                  </a>
                <?php else: ?>
                  <a href="<?= APP_URL ?>/caja/cierre" class="btn btn-danger" style="padding: 0.35rem 0.6rem; font-size: 0.82rem;">
                    🔒 Cerrar
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
