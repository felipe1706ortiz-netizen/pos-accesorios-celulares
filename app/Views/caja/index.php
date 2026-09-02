<!-- ==============================================================================
     VISTA PRINCIPAL: CONTROL DE CAJA Y ESTADO DEL TURNO (MÓDULOS 5 Y 6 - UI/UX PRO MAX)
     ============================================================================== -->

<?php if (!$sesion): ?>
  <!-- ESTADO: SIN SESIÓN DE CAJA ABIERTA -->
  <div class="card" style="text-align: center; padding: 3.5rem 2rem; max-width: 580px; margin: 2rem auto;">
    <div style="font-size: 3.5rem; margin-bottom: 0.75rem;">🔒</div>
    <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; letter-spacing: -0.02em;">
      No hay ninguna sesión de caja abierta
    </h2>
    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.6;">
      Para comenzar a facturar en el POS y registrar movimientos de efectivo, abra un nuevo turno indicando el fondo inicial.
    </p>

    <form action="<?= APP_URL ?>/caja/abrir" method="POST" style="max-width: 420px; margin: 0 auto; text-align: left;">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

      <div class="form-group">
        <label class="form-label" for="monto_inicial" style="font-weight: 800; font-size: 1rem;">Base / Fondo Inicial en Efectivo ($):</label>
        <input 
          type="number" 
          step="100" 
          min="0" 
          id="monto_inicial" 
          name="monto_inicial" 
          class="form-control form-control-lg" 
          style="font-family: 'JetBrains Mono', monospace; font-size: 1.5rem; font-weight: 800;"
          placeholder="ej: 100000" 
          value="0" 
          required 
          autofocus
        >
      </div>

      <div class="form-group">
        <label class="form-label" for="notas_apertura">Notas u Observaciones (Opcional):</label>
        <input type="text" id="notas_apertura" name="notas" class="form-control" placeholder="ej: Turno mañana...">
      </div>

      <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top: 1.25rem;">
        <span>🔓</span> <span>Abrir Turno de Caja</span>
      </button>
    </form>
  </div>

<?php else: 
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
  <div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
      <div>
        <div style="display: flex; align-items: center; gap: 0.85rem;">
          <h2 style="font-size: 1.45rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">
            Turno de Caja #<?= $sesion['id'] ?>
          </h2>
          <span class="badge badge-success">🟢 ABIERTA Y OPERATIVA</span>
        </div>
        <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 0.35rem;">
          Cajero responsable: <strong style="color: var(--text-main);"><?= htmlspecialchars($sesion['usuario_nombre'] ?? $currentUser['nombre']) ?></strong> • Apertura: <strong><?= date('d/m/Y H:i', strtotime($sesion['fecha_apertura'])) ?></strong>
        </p>
      </div>

      <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-outline" style="font-weight: 700; color: var(--success); border-color: #a7f3d0;" onclick="abrirModalMovimiento('ENTRADA')">
          <span>➕</span> <span>Entrada Efectivo</span>
        </button>
        <button type="button" class="btn btn-outline" style="font-weight: 700; color: var(--danger); border-color: #fecdd3;" onclick="abrirModalMovimiento('SALIDA')">
          <span>➖</span> <span>Salida / Gasto</span>
        </button>
        <a href="<?= APP_URL ?>/caja/cierre" class="btn btn-danger" style="font-weight: 800;">
          <span>🔒</span> <span>Cerrar Caja (Arqueo)</span>
        </a>
      </div>
    </div>
  </div>

  <!-- RESUMEN DE SALDOS EN TIEMPO REAL -->
  <div class="kpi-grid">
    <div class="kpi-card" style="border-left: 4px solid var(--primary);">
      <div class="kpi-icon primary">
        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
      </div>
      <div class="kpi-details">
        <div class="kpi-label">Fondo / Base Inicial</div>
        <div class="kpi-value" style="color: var(--primary);">$ <?= number_format($montoInicial, 0, ',', '.') ?></div>
      </div>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--success);">
      <div class="kpi-icon success">
        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div class="kpi-details">
        <div class="kpi-label">Ventas en Efectivo</div>
        <div class="kpi-value" style="color: var(--success);">$ <?= number_format($ventasEf, 0, ',', '.') ?></div>
      </div>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--info);">
      <div class="kpi-icon info">
        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
      </div>
      <div class="kpi-details">
        <div class="kpi-label">Entradas Manuales</div>
        <div class="kpi-value" style="color: var(--info);">+ $ <?= number_format($entradas, 0, ',', '.') ?></div>
      </div>
    </div>

    <div class="kpi-card" style="border-left: 4px solid var(--danger);">
      <div class="kpi-icon danger">
        <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
      </div>
      <div class="kpi-details">
        <div class="kpi-label">Salidas / Gastos</div>
        <div class="kpi-value" style="color: var(--danger);">- $ <?= number_format($salidas, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>

  <!-- BALANCE PRINCIPAL Y OTROS MEDIOS DE PAGO -->
  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    
    <!-- SALDO TEÓRICO ESPERADO EN CAJA (DESTACADO HERO CARD) -->
    <div class="card" style="background: linear-gradient(135deg, #0b0f19 0%, #1e1b4b 50%, #0f172a 100%); color: #ffffff; display: flex; flex-direction: column; justify-content: center; padding: 2.25rem; border: 1px solid rgba(255, 255, 255, 0.1); position: relative; overflow: hidden;">
      <div style="font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8;">
        Saldo Teórico Esperado en Gaveta (Efectivo Físico)
      </div>
      <div style="font-size: 3.2rem; font-weight: 900; font-family: 'JetBrains Mono', monospace; color: #10b981; margin: 0.5rem 0; text-shadow: 0 0 25px rgba(16, 185, 129, 0.35);">
        $ <?= number_format($saldoEsperado, 0, ',', '.') ?>
      </div>
      <div style="font-size: 0.88rem; color: #cbd5e1;">
        Fórmula: (Base $ <?= number_format($montoInicial, 0, ',', '.') ?> + Ventas Ef. $ <?= number_format($ventasEf, 0, ',', '.') ?> + Entradas $ <?= number_format($entradas, 0, ',', '.') ?>) − Salidas $ <?= number_format($salidas, 0, ',', '.') ?>
      </div>
    </div>

    <!-- VENTAS POR OTROS MÉTODOS DE PAGO (DIGITAL) -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <svg style="width: 20px; height: 20px; color: var(--info);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
          <span>Ventas Digitales</span>
        </h3>
      </div>
      <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.92rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="color: var(--text-muted);">💳 Datáfono / Tarjetas:</span>
          <strong style="font-family: 'JetBrains Mono', monospace; font-size: 1.05rem;">$ <?= number_format($ventasTarj, 0, ',', '.') ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="color: var(--text-muted);">📱 Transferencia / QR:</span>
          <strong style="font-family: 'JetBrains Mono', monospace; font-size: 1.05rem;">$ <?= number_format($ventasTransf, 0, ',', '.') ?></strong>
        </div>
        <div style="border-top: 1.5px solid var(--border-color); padding-top: 0.85rem; display: flex; justify-content: space-between; align-items: center;">
          <span style="font-weight: 800; color: var(--text-main);">Total Facturado:</span>
          <strong style="font-family: 'JetBrains Mono', monospace; font-size: 1.25rem; color: var(--primary);">$ <?= number_format($ventasEf + $ventasTarj + $ventasTransf, 0, ',', '.') ?></strong>
        </div>
      </div>
    </div>

  </div>

  <!-- TABLA DE MOVIMIENTOS RECIENTES DE LA SESIÓN -->
  <div class="card" style="padding: 0; overflow: hidden;">
    <div class="card-header" style="margin: 0; padding: 1.25rem 1.75rem; background: #f8fafc; display: flex; align-items: center; justify-content: space-between;">
      <h3 class="card-title">Movimientos Manuales del Turno</h3>
      <a href="<?= APP_URL ?>/caja/movimientos" class="btn btn-outline btn-sm">
        Ver Todo el Historial
      </a>
    </div>
    
    <div class="table-responsive">
      <table class="table">
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
                <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: var(--text-muted);">
                  <?= date('H:i:s', strtotime($m['created_at'])) ?>
                </td>
                <td>
                  <span class="badge <?= $isEntrada ? 'badge-success' : 'badge-danger' ?>">
                    <?= $isEntrada ? '📥 ENTRADA' : '📤 SALIDA' ?>
                  </span>
                </td>
                <td style="font-weight: 700; color: var(--text-main);">
                  <?= htmlspecialchars($m['concepto']) ?>
                </td>
                <td style="font-size: 0.85rem; color: var(--text-muted);">
                  <?= htmlspecialchars($m['comprobante'] ?? '-') ?>
                </td>
                <td style="font-size: 0.85rem; font-weight: 600;">
                  👤 <?= htmlspecialchars($m['usuario_nombre']) ?>
                </td>
                <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 800; font-size: 1.05rem; color: <?= $isEntrada ? 'var(--success)' : 'var(--danger)' ?>;">
                  <?= $isEntrada ? '+' : '-' ?> $ <?= number_format($m['monto'], 0, ',', '.') ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<?php endif; ?>

<!-- ==============================================================================
     MODAL: REGISTRAR ENTRADA O SALIDA DE EFECTIVO (MÓDULO 5)
     ============================================================================== -->
<div class="modal-backdrop" id="modalMovimientoCaja">
  <div class="modal-dialog" style="max-width: 480px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 800;" id="modalMovimientoTitulo">Registrar Movimiento de Efectivo</h3>
      <button type="button" onclick="closeModal('modalMovimientoCaja')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
    </div>
    
    <form id="formMovimientoCaja">
      <input type="hidden" id="mov_tipo" name="tipo" value="ENTRADA">

      <div class="modal-body">
        
        <!-- Toggle Tipo -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; margin-bottom: 1.25rem;">
          <button type="button" id="btnToggleEntrada" class="btn btn-success" onclick="seleccionarTipoMovimiento('ENTRADA')">
            📥 Entrada (Ingreso)
          </button>
          <button type="button" id="btnToggleSalida" class="btn btn-outline" onclick="seleccionarTipoMovimiento('SALIDA')">
            📤 Salida (Gasto)
          </button>
        </div>

        <div class="form-group">
          <label class="form-label" for="mov_monto" style="font-weight: 800;">Monto ($) *</label>
          <input type="number" step="100" min="1" id="mov_monto" name="monto" class="form-control form-control-lg" style="font-family: 'JetBrains Mono', monospace; font-weight: 800;" placeholder="ej: 50000" required autofocus>
        </div>

        <div class="form-group">
          <label class="form-label" for="mov_concepto">Concepto / Justificación *</label>
          <input type="text" id="mov_concepto" name="concepto" class="form-control" placeholder="ej: Cambio sencillo, Pago flete, Papelería..." required>
        </div>

        <div class="form-group">
          <label class="form-label" for="mov_comprobante">Nº Recibo / Soporte Físico (Opcional)</label>
          <input type="text" id="mov_comprobante" name="comprobante" class="form-control" placeholder="ej: Recibo #0045, Factura Proveedor">
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalMovimientoCaja')">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnGuardarMovimiento">Guardar Movimiento</button>
      </div>
    </form>
  </div>
</div>
