<!-- ==============================================================================
     VISTA DE HISTORIAL DE FACTURAS (Sober SaaS)
     ============================================================================== -->

<!-- MÉTRICAS DEL HISTORIAL EN EL RANGO SELECCIONADO (METRIC STRIP) -->
<div class="metric-strip" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 1.5rem;">
  <div class="metric-cell">
    <span class="metric-cell-label">Ventas Totales</span>
    <span class="metric-cell-val">$ <?= number_format($metricas['total_ventas'] ?? 0, 0, ',', '.') ?></span>
    <span class="metric-cell-sub">En período filtrado</span>
  </div>

  <div class="metric-cell">
    <span class="metric-cell-label">Facturas Emitidas</span>
    <span class="metric-cell-val"><?= number_format($metricas['total_facturas'] ?? 0, 0, ',', '.') ?></span>
    <span class="metric-cell-sub">Transacciones registradas</span>
  </div>

  <div class="metric-cell">
    <span class="metric-cell-label">Ticket Promedio</span>
    <span class="metric-cell-val">$ <?= number_format($metricas['ticket_promedio'] ?? 0, 0, ',', '.') ?></span>
    <span class="metric-cell-sub">Por venta completada</span>
  </div>

  <div class="metric-cell">
    <span class="metric-cell-label">Anuladas</span>
    <span class="metric-cell-val" style="color: <?= ($metricas['total_anuladas'] ?? 0) > 0 ? 'var(--danger)' : 'inherit' ?>;"><?= $metricas['total_anuladas'] ?? 0 ?></span>
    <span class="metric-cell-sub">Ventas canceladas</span>
  </div>
</div>

<!-- BARRA DE HERRAMIENTAS Y FILTROS POR FECHA Y MÉTODO DE PAGO -->
<div class="panel" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem;">
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
        <option value="EFECTIVO" <?= $metodoPago === 'EFECTIVO' ? 'selected' : '' ?>>Efectivo</option>
        <option value="TARJETA" <?= $metodoPago === 'TARJETA' ? 'selected' : '' ?>>Tarjeta</option>
        <option value="TRANSFERENCIA" <?= $metodoPago === 'TRANSFERENCIA' ? 'selected' : '' ?>>Transferencia</option>
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
      <button type="submit" class="btn btn-primary" style="height: 40px;">
        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <span>Filtrar</span>
      </button>
      <a href="<?= APP_URL ?>/facturas" class="btn btn-outline" style="height: 40px;" title="Ver ventas de hoy">
        Hoy
      </a>
    </div>

  </form>
</div>

<!-- TABLA MAESTRA DEL HISTORIAL DE FACTURAS -->
<div class="panel" style="padding: 0; overflow: hidden;">
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
          ?>
            <tr style="<?= $isAnulada ? 'opacity: 0.6; background-color: #fafafa;' : '' ?>">
              <td>
                <strong style="font-variant-numeric: tabular-nums; font-size: 0.95rem; color: <?= $isAnulada ? 'var(--danger)' : 'var(--primary)' ?>;">
                  <?= htmlspecialchars($f['numero_factura']) ?>
                </strong>
              </td>
              <td style="font-size: 0.82rem; font-variant-numeric: tabular-nums; color: var(--text-muted); white-space: nowrap;">
                <?= date('d/m/Y H:i', strtotime($f['created_at'])) ?>
              </td>
              <td style="font-size: 0.88rem; color: var(--text-secondary);">
                <?= htmlspecialchars($f['cajero_nombre']) ?>
              </td>
              <td>
                <div style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($f['cliente_nombre']) ?></div>
                <div style="font-size: 0.78rem; font-variant-numeric: tabular-nums; color: var(--text-muted);">Doc: <?= htmlspecialchars($f['cliente_documento']) ?></div>
              </td>
              <td>
                <span class="badge badge-neutral">
                  <?= htmlspecialchars($f['metodo_pago']) ?>
                </span>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums; font-weight: 600; font-size: 1rem; color: <?= $isAnulada ? 'var(--text-muted); text-decoration: line-through;' : 'var(--text-main)' ?>;">
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
                  style="padding: 0.35rem 0.65rem; font-size: 0.82rem;" 
                  title="Ver Detalle de la Venta"
                  onclick="verDetalleFactura(<?= $f['id'] ?>)"
                >
                  <svg style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  <span>Detalle</span>
                </button>

                <!-- Reimprimir Ticket Térmico -->
                <button 
                  type="button" 
                  class="btn btn-outline" 
                  style="padding: 0.35rem 0.55rem; font-size: 0.82rem;" 
                  title="Reimprimir Ticket Térmico"
                  onclick="reimprimirTicket(<?= $f['id'] ?>)"
                >
                  <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </button>

                <?php if (\App\Core\Auth::isAdmin() && !$isAnulada): ?>
                <!-- Anular Factura (Admin) -->
                <button 
                  type="button" 
                  class="btn btn-outline" 
                  style="padding: 0.35rem 0.55rem; font-size: 0.82rem; color: var(--danger); border-color: var(--border-color);" 
                  title="Anular Factura y Devolver Stock"
                  onclick="abrirModalAnulacion(<?= $f['id'] ?>, '<?= htmlspecialchars($f['numero_factura']) ?>')"
                >
                  <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
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
      <h3 style="font-size: 1.15rem; font-weight: 700;" id="modalDetalleTitulo">Factura: -</h3>
      <button type="button" onclick="closeModal('modalDetalleFactura')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>
    
    <div class="modal-body" id="modalDetalleContenido">
      <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
        Cargando información de la venta...
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeModal('modalDetalleFactura')">Cerrar</button>
      <button type="button" class="btn btn-primary" id="btnImprimirModal" onclick="">
        <svg style="width: 15px; height: 15px; display: inline-block; vertical-align: -2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Imprimir Ticket</span>
      </button>
    </div>
  </div>
</div>

<!-- ==============================================================================
     MODAL: CONFIRMAR ANULACIÓN DE FACTURA (ADMIN)
     ============================================================================== -->
<div class="modal-backdrop" id="modalAnularFactura">
  <div class="modal-dialog" style="max-width: 480px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--danger);">Anular Factura de Venta</h3>
      <button type="button" onclick="closeModal('modalAnularFactura')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>
    
    <form action="" method="POST" id="formAnularFactura">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      
      <div class="modal-body">
        <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: var(--radius-sm); padding: 1rem; color: #991b1b; font-size: 0.85rem; margin-bottom: 1.25rem;">
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
