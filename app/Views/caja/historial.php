<!-- ==============================================================================
     VISTA: HISTORIAL DE ARQUEOS Y CIERRES DE CAJA (Sober SaaS)
     ============================================================================== -->

<div class="panel" style="margin-bottom: 1.5rem; padding: 1.25rem 1.5rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-main);">
        Historial de Turnos y Cierres de Caja
      </h2>
      <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
        Auditoría de conciliaciones pasadas, sobrantes, faltantes y reimpresión de arqueos.
      </p>
    </div>

    <div>
      <a href="<?= APP_URL ?>/caja" class="btn btn-outline">
        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Volver a Caja Activa</span>
      </a>
    </div>
  </div>
</div>

<!-- TABLA DEL HISTORIAL DE SESIONES -->
<div class="panel" style="padding: 0; overflow: hidden;">
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
                <strong style="font-variant-numeric: tabular-nums; font-size: 0.95rem; color: var(--primary);">
                  #<?= $s['id'] ?>
                </strong>
              </td>
              <td style="font-weight: 500; color: var(--text-main);">
                <?= htmlspecialchars($s['usuario_nombre']) ?>
              </td>
              <td style="font-size: 0.82rem; font-variant-numeric: tabular-nums; color: var(--text-muted); white-space: nowrap;">
                <?= date('d/m/Y H:i', strtotime($s['fecha_apertura'])) ?>
              </td>
              <td style="font-size: 0.82rem; font-variant-numeric: tabular-nums; color: var(--text-muted); white-space: nowrap;">
                <?= $s['fecha_cierre'] ? date('d/m/Y H:i', strtotime($s['fecha_cierre'])) : '-' ?>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums;">
                $ <?= number_format($s['monto_inicial'], 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums; color: var(--success);">
                $ <?= number_format($s['total_ventas_efectivo'], 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums; font-weight: 600;">
                $ <?= number_format($s['monto_esperado'], 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums; font-weight: 600;">
                <?= $s['monto_real'] !== null ? '$ ' . number_format($s['monto_real'], 0, ',', '.') : '-' ?>
              </td>
              <td style="text-align: center;">
                <?php if ($s['monto_real'] !== null): ?>
                  <?php if ($dif === 0.0): ?>
                    <span class="badge badge-success">Cuadre ($0)</span>
                  <?php elseif ($dif > 0): ?>
                    <span class="badge badge-neutral">+ $ <?= number_format($dif, 0, ',', '.') ?> (Sobrante)</span>
                  <?php else: ?>
                    <span class="badge badge-danger">- $ <?= number_format(abs($dif), 0, ',', '.') ?> (Faltante)</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="color: var(--text-muted);">-</span>
                <?php endif; ?>
              </td>
              <td style="text-align: center;">
                <span class="badge <?= $isAbierta ? 'badge-success' : 'badge-neutral' ?>">
                  <?= $s['estado'] ?>
                </span>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <?php if (!$isAbierta): ?>
                  <a href="<?= APP_URL ?>/caja/ticket/<?= $s['id'] ?>" target="_blank" class="btn btn-outline" style="padding: 0.35rem 0.6rem; font-size: 0.82rem;" title="Reimprimir Ticket de Arqueo">
                    <svg style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Arqueo</span>
                  </a>
                <?php else: ?>
                  <a href="<?= APP_URL ?>/caja/cierre" class="btn btn-danger" style="padding: 0.35rem 0.6rem; font-size: 0.82rem;">
                    <svg style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Cerrar</span>
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
