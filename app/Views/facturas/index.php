<!-- ==============================================================================
     VISTA DE HISTORIAL DE FACTURAS (MÓDULO 4 - UI/UX PRO MAX)
     ============================================================================== -->

<!-- MÉTRICAS DEL HISTORIAL EN EL RANGO SELECCIONADO -->
<div class="kpi-grid">
  <div class="kpi-card" style="border-left: 4px solid var(--primary);">
    <div class="kpi-icon primary">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Ventas Totales</div>
      <div class="kpi-value" style="color: var(--primary);">$ <?= number_format($metricas['total_ventas'] ?? 0, 0, ',', '.') ?></div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--success);">
    <div class="kpi-icon success">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Facturas Emitidas</div>
      <div class="kpi-value" style="color: var(--success);"><?= number_format($metricas['total_facturas'] ?? 0, 0, ',', '.') ?></div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--info);">
    <div class="kpi-icon info">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Ticket Promedio</div>
      <div class="kpi-value" style="color: var(--info);">$ <?= number_format($metricas['ticket_promedio'] ?? 0, 0, ',', '.') ?></div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--danger);">
    <div class="kpi-icon danger">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Anuladas</div>
      <div class="kpi-value" style="color: var(--danger);"><?= $metricas['total_anuladas'] ?? 0 ?></div>
    </div>
  </div>
</div>

<!-- BARRA DE HERRAMIENTAS Y FILTROS POR FECHA Y MÉTODO DE PAGO -->
<div class="card" style="margin-bottom: 1.5rem;">
  <form action="<?= APP_URL ?>/facturas" method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
    
    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 140px;">
      <label class="form-label" style="font-size: 0.8rem;" for="fecha_inicio">Desde:</label>
      <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($fechaInicio) ?>">
    </div>

    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 140px;">
      <label class="form-label" style="font-size: 0.8rem;" for="fecha_fin">Hasta:</label>
      <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fechaFin) ?>">
    </div>

    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
      <label class="form-label" style="font-size: 0.8rem;" for="metodo_pago">Método de Pago:</label>
      <select id="metodo_pago" name="metodo_pago" class="form-control">
        <option value="">Todos los Métodos</option>
        <option value="EFECTIVO" <?= $metodoPago === 'EFECTIVO' ? 'selected' : '' ?>>💵 Efectivo</option>
        <option value="TARJETA" <?= $metodoPago === 'TARJETA' ? 'selected' : '' ?>>💳 Tarjeta</option>
        <option value="TRANSFERENCIA" <?= $metodoPago === 'TRANSFERENCIA' ? 'selected' : '' ?>>📱 Transferencia</option>
      </select>
    </div>

    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 140px;">
      <label class="form-label" style="font-size: 0.8rem;" for="estado">Estado:</label>
      <select id="estado" name="estado" class="form-control">
        <option value="">Todos</option>
        <option value="COMPLETADA" <?= $estado === 'COMPLETADA' ? 'selected' : '' ?>>Completadas</option>
        <option value="ANULADA" <?= $estado === 'ANULADA' ? 'selected' : '' ?>>Anuladas</option>
      </select>
    </div>

    <div class="form-group" style="margin-bottom: 0; flex: 2; min-width: 200px;">
      <label class="form-label" style="font-size: 0.8rem;" for="q">Buscar Factura / Cliente:</label>
      <input type="text" id="q" name="q" class="form-control" placeholder="Nº Factura, Nombre, Cédula..." value="<?= htmlspecialchars($search) ?>">
    </div>

    <div style="display: flex; gap: 0.5rem;">
      <button type="submit" class="btn btn-primary" style="height: 42px;">
        <span>🔍</span> <span>Filtrar</span>
      </button>
      <a href="<?= APP_URL ?>/facturas" class="btn btn-outline" style="height: 42px;" title="Ver ventas de hoy">
        Hoy
      </a>
    </div>

  </form>
</div>

<!-- TABLA MAESTRA DEL HISTORIAL DE FACTURAS -->
<div class="card" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Nº Factura</th>
          <th>Fecha y Hora</th>
          <th>Cajero</th>
          <th>Cliente</th>
          <th>Método Pago</th>
          <th style="text-align: right;">Total Venta</th>
          <th style="text-align: center;">Estado</th>
          <th style="text-align: right;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($facturas)): ?>
          <tr>
            <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">
              No se encontraron facturas en el rango de fechas seleccionado.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($facturas as $f): 
            $isAnulada = ($f['estado'] === 'ANULADA');
            
            // Icono método de pago
            $metodoIcon = '💵';
            if ($f['metodo_pago'] === 'TARJETA') $metodoIcon = '💳';
            elseif ($f['metodo_pago'] === 'TRANSFERENCIA') $metodoIcon = '📱';
          ?>
            <tr style="<?= $isAnulada ? 'opacity: 0.65; background-color: #fff1f2;' : '' ?>">
              <td>
                <strong style="font-family: 'JetBrains Mono', monospace; font-size: 0.95rem; color: <?= $isAnulada ? 'var(--danger)' : 'var(--primary)' ?>;">
                  <?= htmlspecialchars($f['numero_factura']) ?>
                </strong>
              </td>
              <td style="font-size: 0.85rem; font-family: 'JetBrains Mono', monospace; color: var(--text-muted); white-space: nowrap;">
                <?= date('d/m/Y H:i', strtotime($f['created_at'])) ?>
              </td>
              <td style="font-size: 0.88rem; font-weight: 600;">
                👤 <?= htmlspecialchars($f['cajero_nombre']) ?>
              </td>
              <td>
                <div style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($f['cliente_nombre']) ?></div>
                <div style="font-size: 0.78rem; font-family: 'JetBrains Mono', monospace; color: var(--text-muted);">Doc: <?= htmlspecialchars($f['cliente_documento']) ?></div>
              </td>
              <td>
                <span class="badge badge-info">
                  <?= $metodoIcon ?> <?= htmlspecialchars($f['metodo_pago']) ?>
                </span>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 800; font-size: 1.05rem; color: <?= $isAnulada ? 'var(--text-muted); text-decoration: line-through;' : 'var(--text-main)' ?>;">
                $ <?= number_format($f['total'], 0, ',', '.') ?>
              </td>
              <td style="text-align: center;">
                <span class="badge <?= $isAnulada ? 'badge-danger' : 'badge-success' ?>">
                  <?= $isAnulada ? 'ANULADA' : 'COMPLETADA' ?>
                </span>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <!-- Ver Detalle (Modal Rápido) -->
                <button 
                  type="button" 
                  class="btn btn-outline" 
                  style="padding: 0.4rem 0.7rem; font-size: 0.82rem; font-weight: 700;" 
                  title="Ver Detalle de la Venta"
                  onclick="verDetalleFactura(<?= $f['id'] ?>)"
                >
                  👁️ Detalle
                </button>

                <!-- Reimprimir Ticket Térmico -->
                <button 
                  type="button" 
                  class="btn btn-outline" 
                  style="padding: 0.4rem 0.65rem; font-size: 0.82rem;" 
                  title="Reimprimir Ticket Térmico"
                  onclick="reimprimirTicket(<?= $f['id'] ?>)"
                >
                  🖨️
                </button>

                <?php if (\App\Core\Auth::isAdmin() && !$isAnulada): ?>
                <!-- Anular Factura (Admin) -->
                <button 
                  type="button" 
                  class="btn btn-outline" 
                  style="padding: 0.4rem 0.65rem; font-size: 0.82rem; color: var(--danger); border-color: #fecdd3;" 
                  title="Anular Factura y Devolver Stock"
                  onclick="abrirModalAnulacion(<?= $f['id'] ?>, '<?= htmlspecialchars($f['numero_factura']) ?>')"
                >
                  🚫
                </button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ==============================================================================
     MODAL: DETALLE DE FACTURA (AJAX)
     ============================================================================== -->
<div class="modal-backdrop" id="modalDetalleFactura">
  <div class="modal-dialog" style="max-width: 650px;">
    <div class="modal-header">
      <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--primary);" id="modalDetalleTitulo">Factura: -</h3>
      <button type="button" onclick="closeModal('modalDetalleFactura')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
    </div>
    
    <div class="modal-body" id="modalDetalleContenido">
      <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
        Cargando información de la venta...
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeModal('modalDetalleFactura')">Cerrar</button>
      <button type="button" class="btn btn-primary" id="btnImprimirModal" onclick="">🖨️ Imprimir Ticket</button>
    </div>
  </div>
</div>

<!-- ==============================================================================
     MODAL: CONFIRMAR ANULACIÓN DE FACTURA (ADMIN)
     ============================================================================== -->
<div class="modal-backdrop" id="modalAnularFactura">
  <div class="modal-dialog" style="max-width: 480px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--danger);">🚫 Anular Factura de Venta</h3>
      <button type="button" onclick="closeModal('modalAnularFactura')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
    </div>
    
    <form action="" method="POST" id="formAnularFactura">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      
      <div class="modal-body">
        <div style="background: #fff1f2; border: 1.5px solid #fecdd3; border-radius: var(--radius-md); padding: 1.15rem; color: #be123c; font-size: 0.88rem; margin-bottom: 1.25rem;">
          <strong>Atención:</strong> Al anular la factura <strong id="anularNumeroFacturaDisplay">-</strong>, todas las unidades vendidas se <strong>revertirán automáticamente al inventario</strong> y se registrará la devolución en el Kárdex.
        </div>

        <div class="form-group">
          <label class="form-label" for="anular_motivo">Motivo de la Anulación *</label>
          <textarea id="anular_motivo" name="motivo" class="form-control" rows="3" placeholder="ej: Error en método de pago, devolución de cliente..." required></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAnularFactura')">Cancelar</button>
        <button type="submit" class="btn btn-danger">Confirmar Anulación</button>
      </div>
    </form>
  </div>
</div>
