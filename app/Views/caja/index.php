<!-- ==============================================================================
     VISTA PRINCIPAL: CONTROL DE CAJA Y ESTADO DEL TURNO (MÓDULOS 5 Y 6 - UI/UX PRO MAX)
     ============================================================================== -->

<?php if (!$sesion): ?>
  <?php require __DIR__ . '/apertura.php'; return; ?>
<?php endif; ?>

<?php
// ESTADO: SESIÓN DE CAJA ACTIVA
$montoInicial = (float)$sesion['monto_inicial'];
$ventasEf = (float)$sesion['total_ventas_efectivo'];
$ventasTarj = (float)$sesion['total_ventas_tarjeta'];
$ventasTransf = (float)$sesion['total_ventas_transferencia'];
$entradas = (float)$sesion['total_entradas'];
$salidas = (float)$sesion['total_salidas'];
$saldoEsperado = (float)$sesion['monto_esperado'];
?>

  <!-- CABECERA DEL TURNO ACTIVO -->
  <div class="panel" style="margin-bottom: 1.25rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.65rem;">
          <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em; margin: 0;">
            Turno de Caja #<?= $sesion['id'] ?>
          </h2>
          <span class="badge badge-success" style="display: inline-flex; align-items: center; gap: 0.35rem;">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a; display: inline-block;"></span>
            <span>Abierta</span>
          </span>
        </div>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
          Cajero responsable: <strong style="color: var(--text-main);"><?= htmlspecialchars($sesion['usuario_nombre'] ?? $currentUser['nombre']) ?></strong> &bull; Apertura: <strong><?= date('d/m/Y H:i', strtotime($sesion['fecha_apertura'])) ?></strong>
        </p>
      </div>

      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-outline btn-sm" onclick="abrirModalMovimiento('ENTRADA')">
          <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          <span>Entrada Efectivo</span>
        </button>
        <button type="button" class="btn btn-outline btn-sm" onclick="abrirModalMovimiento('SALIDA')">
          <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          <span>Salida / Gasto</span>
        </button>
        <a href="<?= APP_URL ?>/caja/cierre" class="btn btn-primary btn-sm">
          <span>Cerrar Caja (Arqueo)</span>
        </a>
      </div>
    </div>
  </div>

  <!-- RESUMEN DE SALDOS EN TIEMPO REAL (METRIC STRIP) -->
  <div class="metric-strip">
    <div class="metric-cell">
      <div class="metric-cell-label">Fondo / Base Inicial</div>
      <div class="metric-cell-value">$ <?= number_format($montoInicial, 0, ',', '.') ?></div>
      <div class="metric-cell-sub">Apertura de turno</div>
    </div>

    <div class="metric-cell">
      <div class="metric-cell-label">Ventas en Efectivo</div>
      <div class="metric-cell-value" style="color: var(--success);">$ <?= number_format($ventasEf, 0, ',', '.') ?></div>
      <div class="metric-cell-sub">Recaudado en caja</div>
    </div>

    <div class="metric-cell">
      <div class="metric-cell-label">Entradas Manuales</div>
      <div class="metric-cell-value" style="color: var(--info);">+ $ <?= number_format($entradas, 0, ',', '.') ?></div>
      <div class="metric-cell-sub">Ingresos extraordinarios</div>
    </div>

    <div class="metric-cell">
      <div class="metric-cell-label">Salidas / Gastos</div>
      <div class="metric-cell-value" style="color: var(--danger);">- $ <?= number_format($salidas, 0, ',', '.') ?></div>
      <div class="metric-cell-sub">Retiros del turno</div>
    </div>
  </div>

  <!-- BALANCE PRINCIPAL Y OTROS MEDIOS DE PAGO -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
    
    <!-- SALDO TEÓRICO ESPERADO EN CAJA -->
    <div class="panel" style="display: flex; flex-direction: column; justify-content: center; padding: 1.75rem;">
      <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">
        Saldo Teórico Esperado en Gaveta (Efectivo Físico)
      </div>
      <div style="font-size: 2.4rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--success); margin: 0.4rem 0;">
        $ <?= number_format($saldoEsperado, 0, ',', '.') ?>
      </div>
      <div style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5;">
        Base ($ <?= number_format($montoInicial, 0, ',', '.') ?>) + Ventas Ef. ($ <?= number_format($ventasEf, 0, ',', '.') ?>) + Entradas ($ <?= number_format($entradas, 0, ',', '.') ?>) − Salidas ($ <?= number_format($salidas, 0, ',', '.') ?>)
      </div>
    </div>

    <!-- VENTAS POR OTROS MÉTODOS DE PAGO (DIGITAL) -->
    <div class="panel">
      <div class="panel-header">
        <div class="panel-title">
          <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
            <line x1="1" y1="10" x2="23" y2="10"></line>
          </svg>
          <span>Ventas Digitales</span>
        </div>
      </div>
      <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.88rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="color: var(--text-muted);">Datáfono / Tarjetas:</span>
          <strong style="font-variant-numeric: tabular-nums;">$ <?= number_format($ventasTarj, 0, ',', '.') ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="color: var(--text-muted);">Transferencia / QR:</span>
          <strong style="font-variant-numeric: tabular-nums;">$ <?= number_format($ventasTransf, 0, ',', '.') ?></strong>
        </div>
        <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
          <span style="font-weight: 600; color: var(--text-main);">Total Facturado Turno:</span>
          <strong style="font-variant-numeric: tabular-nums; font-size: 1.1rem; color: var(--text-main);">$ <?= number_format($ventasEf + $ventasTarj + $ventasTransf, 0, ',', '.') ?></strong>
        </div>
      </div>
    </div>

  </div>

  <!-- TABLA DE MOVIMIENTOS RECIENTES DE LA SESIÓN -->
  <div class="panel" style="padding: 0; overflow: hidden;">
    <div class="panel-header" style="margin: 0; padding: 1rem 1.25rem; background: var(--bg-muted);">
      <div class="panel-title">Movimientos Manuales del Turno</div>
      <a href="<?= APP_URL ?>/caja/movimientos" class="btn btn-outline btn-sm">
        Ver Historial Completo
      </a>
    </div>
    
    <div class="table-responsive">
      <table class="table" style="margin: 0; font-size: 0.85rem;">
        <thead>
          <tr>
            <th>Hora</th>
            <th>Tipo</th>
            <th>Concepto / Motivo</th>
            <th>Comprobante</th>
            <th>Usuario</th>
            <th style="text-align: right;">Monto</th>
          </tr>
        </thead>
        <tbody id="tbodyMovimientosSesion">
          <?php if (empty($movimientos)): ?>
            <tr>
              <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                No se han registrado entradas ni salidas manuales en este turno.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($movimientos as $m): 
              $isEntrada = ($m['tipo'] === 'ENTRADA');
            ?>
              <tr>
                <td style="font-variant-numeric: tabular-nums; color: var(--text-muted);">
                  <?= date('H:i:s', strtotime($m['created_at'])) ?>
                </td>
                <td>
                  <span class="badge <?= $isEntrada ? 'badge-success' : 'badge-danger' ?>">
                    <?= $isEntrada ? 'Entrada' : 'Salida' ?>
                  </span>
                </td>
                <td style="font-weight: 600; color: var(--text-main);">
                  <?= htmlspecialchars($m['concepto']) ?>
                </td>
                <td style="color: var(--text-muted);">
                  <?= htmlspecialchars($m['comprobante'] ?? '-') ?>
                </td>
                <td>
                  <?= htmlspecialchars($m['usuario_nombre']) ?>
                </td>
                <td style="text-align: right; font-variant-numeric: tabular-nums; font-weight: 600; color: <?= $isEntrada ? 'var(--success)' : 'var(--danger)' ?>;">
                  <?= $isEntrada ? '+' : '-' ?> $ <?= number_format($m['monto'], 0, ',', '.') ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<!-- ==============================================================================
     MODAL: REGISTRAR ENTRADA O SALIDA DE EFECTIVO
     ============================================================================== -->
<div class="modal-backdrop" id="modalMovimientoCaja">
  <div class="modal-dialog" style="max-width: 460px;">
    <div class="modal-header">
      <h3 style="font-size: 1.05rem; font-weight: 600; margin: 0;" id="modalMovimientoTitulo">Registrar Movimiento de Efectivo</h3>
      <button type="button" onclick="closeModal('modalMovimientoCaja')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>
    
    <form id="formMovimientoCaja">
      <input type="hidden" id="mov_tipo" name="tipo" value="ENTRADA">

      <div class="modal-body">
        
        <!-- Toggle Tipo -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.25rem;">
          <button type="button" id="btnToggleEntrada" class="btn btn-primary" onclick="seleccionarTipoMovimiento('ENTRADA')">
            Entrada (Ingreso)
          </button>
          <button type="button" id="btnToggleSalida" class="btn btn-outline" onclick="seleccionarTipoMovimiento('SALIDA')">
            Salida (Gasto)
          </button>
        </div>

        <div class="form-group">
          <label class="form-label" for="mov_monto">Monto ($) *</label>
          <input type="number" step="any" min="0.01" id="mov_monto" name="monto" class="form-control form-control-lg" style="font-weight: 600; font-variant-numeric: tabular-nums;" placeholder="50000" required autofocus>
        </div>

        <div class="form-group">
          <label class="form-label" for="mov_concepto">Concepto / Justificación *</label>
          <input type="text" id="mov_concepto" name="concepto" class="form-control" placeholder="ej: Cambio sencillo, Pago flete, Papelería..." required>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="mov_comprobante">Nº Recibo / Soporte Físico (Opcional)</label>
          <input type="text" id="mov_comprobante" name="comprobante" class="form-control" placeholder="ej: Recibo #0045, Factura Proveedor">
        </div>

      </div>

      <div class="modal-footer" style="gap: 0.5rem;">
        <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalMovimientoCaja')">Cancelar</button>
        <button type="submit" class="btn btn-primary btn-sm" id="btnGuardarMovimiento">Guardar Movimiento</button>
      </div>
    </form>
  </div>
</div>
