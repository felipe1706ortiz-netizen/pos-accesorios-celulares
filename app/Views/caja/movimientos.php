<!-- ==============================================================================
     VISTA: MOVIMIENTOS DE EFECTIVO (MÓDULO 5 - UI/UX PRO MAX)
     ============================================================================== -->

<div class="card" style="margin-bottom: 1.5rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h2 style="font-size: 1.45rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">
        Registro y Auditoría de Movimientos de Efectivo
      </h2>
      <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 0.35rem;">
        Entradas (ingresos manuales / sencillo) y Salidas (gastos menores / pagos a proveedores).
      </p>
    </div>

    <div style="display: flex; gap: 0.6rem;">
      <button type="button" class="btn btn-primary" onclick="abrirModalMovimiento('ENTRADA')">
        <span>➕</span> <span>Nueva Entrada / Salida</span>
      </button>
      <a href="<?= APP_URL ?>/caja" class="btn btn-outline" style="font-weight: 700;">
        <span>⬅️</span> <span>Volver a Caja</span>
      </a>
    </div>
  </div>
</div>

<!-- FILTROS POR FECHA Y TIPO -->
<div class="card" style="margin-bottom: 1.5rem;">
  <form action="<?= APP_URL ?>/caja/movimientos" method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
    
    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 160px;">
      <label class="form-label" style="font-size: 0.8rem;" for="filtro_fecha">Fecha:</label>
      <input type="date" id="filtro_fecha" name="fecha" class="form-control" value="<?= htmlspecialchars($fecha) ?>">
    </div>

    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 160px;">
      <label class="form-label" style="font-size: 0.8rem;" for="filtro_tipo">Tipo de Movimiento:</label>
      <select id="filtro_tipo" name="tipo" class="form-control">
        <option value="">Todos los Tipos</option>
        <option value="ENTRADA" <?= $tipo === 'ENTRADA' ? 'selected' : '' ?>>📥 Entradas (Ingresos)</option>
        <option value="SALIDA" <?= $tipo === 'SALIDA' ? 'selected' : '' ?>>📤 Salidas (Gastos)</option>
      </select>
    </div>

    <div style="display: flex; gap: 0.5rem;">
      <button type="submit" class="btn btn-primary" style="height: 42px;">
        <span>🔍</span> <span>Filtrar</span>
      </button>
      <a href="<?= APP_URL ?>/caja/movimientos" class="btn btn-outline" style="height: 42px;">
        Hoy
      </a>
    </div>

  </form>
</div>

<!-- TABLA DE MOVIMIENTOS -->
<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Fecha y Hora</th>
          <th>Tipo</th>
          <th>Concepto / Justificación</th>
          <th>Comprobante</th>
          <th>Turno #</th>
          <th>Usuario Responsable</th>
          <th style="text-align: right;">Monto</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($movimientos)): ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 3.5rem; color: var(--text-muted);">
              No se encontraron movimientos de efectivo registrados con los filtros aplicados.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($movimientos as $m): 
            $isEntrada = ($m['tipo'] === 'ENTRADA');
          ?>
            <tr>
              <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: var(--text-muted); white-space: nowrap;">
                <?= date('d/m/Y H:i', strtotime($m['created_at'])) ?>
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
              <td>
                <span class="badge badge-info">Turno #<?= $m['sesion_caja_id'] ?></span>
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

<!-- INCLUIR MODAL DE REGISTRO -->
<div class="modal-backdrop" id="modalMovimientoCaja">
  <div class="modal-dialog" style="max-width: 480px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 800;" id="modalMovimientoTitulo">Registrar Movimiento de Efectivo</h3>
      <button type="button" onclick="closeModal('modalMovimientoCaja')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
    </div>
    
    <form id="formMovimientoCaja">
      <input type="hidden" id="mov_tipo" name="tipo" value="ENTRADA">

      <div class="modal-body">
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
          <input type="text" id="mov_concepto" name="concepto" class="form-control" placeholder="ej: Pago almuerzo personal, Sencillo..." required>
        </div>

        <div class="form-group">
          <label class="form-label" for="mov_comprobante">Nº Recibo / Soporte Físico (Opcional)</label>
          <input type="text" id="mov_comprobante" name="comprobante" class="form-control" placeholder="ej: Recibo #012">
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalMovimientoCaja')">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnGuardarMovimiento">Guardar Movimiento</button>
      </div>
    </form>
  </div>
</div>
