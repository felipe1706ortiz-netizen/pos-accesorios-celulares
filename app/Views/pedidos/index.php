<!-- ==============================================================================
     VISTA: TABLERO Y GESTIÓN DE PEDIDOS DE CLIENTES (UI/UX PRO MAX)
     ============================================================================== -->

<!-- CABECERA Y ACCIÓN PRINCIPAL -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.6rem;">
      <span>📋</span> <span>Pedidos y Encargos de Clientes</span>
    </h2>
    <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 0.25rem;">
      Control de pedidos especiales, anticipos, notificaciones por WhatsApp y entregas.
    </p>
  </div>
  <div>
    <a href="<?= APP_URL ?>/pedidos/nuevo" class="btn btn-primary btn-lg" style="font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
      <span style="font-size: 1.2rem;">➕</span> <span>Ingresar Nuevo Pedido</span>
    </a>
  </div>
</div>

<!-- ==============================================================================
     1. TARJETAS KPI DE PEDIDOS
     ============================================================================== -->
<div class="kpi-grid" style="margin-bottom: 1.5rem;">
  
  <div class="kpi-card" style="border-left: 4px solid var(--warning);">
    <div class="kpi-icon warning">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Pedidos Activos</div>
      <div class="kpi-value" style="color: #b45309;"><?= $metricas['pedidos_activos'] ?? 0 ?></div>
      <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">Pendientes y en camino</div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--success);">
    <div class="kpi-icon success">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Listos para Retirar</div>
      <div class="kpi-value" style="color: var(--success);"><?= $metricas['pedidos_listos'] ?? 0 ?></div>
      <div style="font-size: 0.78rem; color: #047857; margin-top: 0.2rem; font-weight: 700;">¡Notificar al cliente!</div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--primary);">
    <div class="kpi-icon primary">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Total en Abonos</div>
      <div class="kpi-value" style="color: var(--primary);">$ <?= number_format($metricas['total_abonos'] ?? 0, 0, ',', '.') ?></div>
      <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">Anticipos recaudados</div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--danger);">
    <div class="kpi-icon danger">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Saldo por Cobrar</div>
      <div class="kpi-value" style="color: var(--danger);">$ <?= number_format($metricas['saldo_por_cobrar'] ?? 0, 0, ',', '.') ?></div>
      <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">Al momento de la entrega</div>
    </div>
  </div>

</div>

<!-- ==============================================================================
     2. BARRA DE BÚSQUEDA Y PESTAÑAS DE ESTADO
     ============================================================================== -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
  
  <form method="GET" action="<?= APP_URL ?>/pedidos" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: center;">
    
    <!-- Filtros por Estado (Pills) -->
    <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
      <a href="<?= APP_URL ?>/pedidos?estado=TODOS&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'TODOS' ? 'btn-primary' : 'btn-outline' ?> btn-sm" style="font-weight: 700;">
        Todos
      </a>
      <a href="<?= APP_URL ?>/pedidos?estado=PENDIENTE&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'PENDIENTE' ? 'btn-warning' : 'btn-outline' ?> btn-sm" style="font-weight: 700;">
        🟡 Pendientes
      </a>
      <a href="<?= APP_URL ?>/pedidos?estado=EN_CAMINO&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'EN_CAMINO' ? 'btn-info' : 'btn-outline' ?> btn-sm" style="font-weight: 700;">
        🚚 En Camino
      </a>
      <a href="<?= APP_URL ?>/pedidos?estado=LISTO&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'LISTO' ? 'btn-success' : 'btn-outline' ?> btn-sm" style="font-weight: 700;">
        🟢 Listos para Retirar
      </a>
      <a href="<?= APP_URL ?>/pedidos?estado=ENTREGADO&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'ENTREGADO' ? 'btn-primary' : 'btn-outline' ?> btn-sm" style="font-weight: 700;">
        ✅ Entregados
      </a>
      <a href="<?= APP_URL ?>/pedidos?estado=CANCELADO&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'CANCELADO' ? 'btn-danger' : 'btn-outline' ?> btn-sm" style="font-weight: 700;">
        ❌ Cancelados
      </a>
    </div>

    <!-- Buscador -->
    <div style="display: flex; gap: 0.5rem; min-width: 280px; flex: 1; max-width: 420px;">
      <input type="hidden" name="estado" value="<?= htmlspecialchars($filtro) ?>">
      <input 
        type="text" 
        name="search" 
        class="form-control" 
        placeholder="Buscar por código, cliente, teléfono..." 
        value="<?= htmlspecialchars($search) ?>"
        style="font-size: 0.88rem;"
      >
      <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 700;">
        🔍 Buscar
      </button>
      <?php if (!empty($search) || $filtro !== 'TODOS'): ?>
        <a href="<?= APP_URL ?>/pedidos" class="btn btn-outline btn-sm" title="Limpiar filtros">✖</a>
      <?php endif; ?>
    </div>

  </form>

</div>

<!-- ==============================================================================
     3. TABLA LISTADO DE PEDIDOS
     ============================================================================== -->
<div class="card" style="padding: 0; overflow: hidden;">
  <div style="overflow-x: auto;">
    <table class="table" style="width: 100%; font-size: 0.88rem; margin: 0;">
      <thead>
        <tr>
          <th style="width: 12%;">Código / Fecha</th>
          <th style="width: 22%;">Cliente & Contacto</th>
          <th style="width: 26%;">Producto / Especificación</th>
          <th style="width: 16%; text-align: right;">Valores & Abono</th>
          <th style="width: 11%; text-align: center;">Estado</th>
          <th style="width: 13%; text-align: center;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($pedidos)): ?>
          <?php foreach ($pedidos as $ped): ?>
            <?php 
              $estadoClass = match($ped['estado']) {
                'PENDIENTE' => 'badge-warning',
                'EN_CAMINO' => 'badge-info',
                'LISTO'     => 'badge-success',
                'ENTREGADO' => 'badge-primary',
                'CANCELADO' => 'badge-danger',
                default     => 'badge-muted'
              };

              $telefonoLimpio = preg_replace('/[^0-9]/', '', $ped['cliente_telefono']);
              if (strlen($telefonoLimpio) === 10 && str_starts_with($telefonoLimpio, '3')) {
                $whatsappNumero = "57" . $telefonoLimpio;
              } else {
                $whatsappNumero = $telefonoLimpio;
              }

              $saldoFormateado = number_format($ped['saldo_pendiente'], 0, ',', '.');
              $empresa = htmlspecialchars($config['empresa_nombre'] ?? 'POS Accesorios');
              
              // Mensaje inteligente según el estado
              if ($ped['estado'] === 'LISTO') {
                $msgWa = "¡Hola {$ped['cliente_nombre']}! Te escribimos de {$empresa}. Te confirmamos que tu encargo [{$ped['codigo']}: {$ped['descripcion']}] ya está listo en nuestra tienda para ser retirado. Saldo restante: $ {$saldoFormateado}. ¡Te esperamos!";
              } elseif ($ped['estado'] === 'EN_CAMINO') {
                $msgWa = "Hola {$ped['cliente_nombre']}, te informamos de {$empresa} que tu pedido [{$ped['codigo']}: {$ped['descripcion']}] se encuentra en camino. Te avisaremos apenas esté disponible.";
              } else {
                $msgWa = "Hola {$ped['cliente_nombre']}, te confirmamos el registro de tu pedido [{$ped['codigo']}: {$ped['descripcion']}] en {$empresa}. Te mantendremos informado sobre su entrega.";
              }
              $waUrl = "https://wa.me/{$whatsappNumero}?text=" . urlencode($msgWa);
            ?>
            <tr>
              <td>
                <span class="badge badge-primary" style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem;">
                  <?= htmlspecialchars($ped['codigo']) ?>
                </span>
                <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 0.35rem;">
                  <?= date('d/m/Y H:i', strtotime($ped['created_at'])) ?>
                </div>
                <?php if (!empty($ped['fecha_entrega_estimada'])): ?>
                  <div style="font-size: 0.72rem; color: #0284c7; font-weight: 600; margin-top: 0.15rem;">
                    📅 Est: <?= date('d/m/Y', strtotime($ped['fecha_entrega_estimada'])) ?>
                  </div>
                <?php endif; ?>
              </td>

              <td>
                <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">
                  <?= htmlspecialchars($ped['cliente_nombre']) ?>
                </div>
                <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.25rem;">
                  <a href="<?= $waUrl ?>" target="_blank" class="badge" style="background: #25d366; color: #ffffff; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;" title="Chatear en WhatsApp">
                    <span>💬</span> <span><?= htmlspecialchars($ped['cliente_telefono']) ?></span>
                  </a>
                </div>
                <?php if (!empty($ped['cliente_documento'])): ?>
                  <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.15rem;">
                    Doc: <?= htmlspecialchars($ped['cliente_documento']) ?>
                  </div>
                <?php endif; ?>
              </td>

              <td>
                <div style="font-weight: 600; color: var(--text-main); line-height: 1.35;">
                  <?= nl2br(htmlspecialchars($ped['descripcion'])) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.3rem;">
                  Cant: <strong style="color: var(--text-main);"><?= $ped['cantidad'] ?> und</strong>
                  <?php if (!empty($ped['producto_catalogo_nombre'])): ?>
                    &bull; Catálogo: <em><?= htmlspecialchars($ped['producto_catalogo_nombre']) ?></em>
                  <?php endif; ?>
                </div>
                <?php if (!empty($ped['notas'])): ?>
                  <div style="font-size: 0.74rem; background: var(--bg-muted); padding: 0.25rem 0.5rem; border-radius: 4px; margin-top: 0.35rem; color: var(--text-secondary);">
                    📝 <?= htmlspecialchars($ped['notas']) ?>
                  </div>
                <?php endif; ?>
              </td>

              <td style="text-align: right;">
                <div style="font-size: 0.8rem; color: var(--text-muted);">
                  Total: <strong style="font-family: 'JetBrains Mono', monospace; color: var(--text-main);">$ <?= number_format($ped['precio_total'], 0, ',', '.') ?></strong>
                </div>
                <div style="font-size: 0.8rem; color: var(--success); margin-top: 0.15rem;">
                  Abono: <strong style="font-family: 'JetBrains Mono', monospace;">$ <?= number_format($ped['abono'], 0, ',', '.') ?></strong>
                </div>
                <div style="font-size: 0.88rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: <?= $ped['saldo_pendiente'] > 0 ? 'var(--danger)' : 'var(--success)' ?>; margin-top: 0.25rem; border-top: 1px dashed var(--border-color); padding-top: 0.2rem;">
                  Saldo: $ <?= $saldoFormateado ?>
                </div>
              </td>

              <td style="text-align: center;">
                <span class="badge <?= $estadoClass ?>" style="font-size: 0.78rem; padding: 0.35rem 0.65rem;">
                  <?= htmlspecialchars($ped['estado']) ?>
                </span>
                <?php if ($ped['estado'] === 'LISTO'): ?>
                  <div style="font-size: 0.7rem; color: var(--success); font-weight: 700; margin-top: 0.25rem;">
                    Por Entregar
                  </div>
                <?php endif; ?>
              </td>

              <td style="text-align: center;">
                <div style="display: flex; gap: 0.35rem; justify-content: center; flex-wrap: wrap;">
                  
                  <!-- WhatsApp Directo -->
                  <a href="<?= $waUrl ?>" target="_blank" class="btn btn-sm" style="background: #25d366; color: #fff; padding: 0.3rem 0.5rem;" title="Enviar WhatsApp al cliente">
                    💬
                  </a>

                  <!-- Cambiar Estado -->
                  <button type="button" class="btn btn-outline btn-sm" onclick="abrirModalEstadoPedido(<?= $ped['id'] ?>, '<?= $ped['codigo'] ?>', '<?= $ped['estado'] ?>')" style="padding: 0.3rem 0.5rem;" title="Actualizar estado">
                    🔄
                  </button>

                  <!-- Imprimir Comprobante -->
                  <a href="<?= APP_URL ?>/pedidos/ticket/<?= $ped['id'] ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 0.3rem 0.5rem;" title="Imprimir comprobante térmico">
                    🖨️
                  </a>

                  <!-- Abonar Saldo si falta -->
                  <?php if ($ped['saldo_pendiente'] > 0 && $ped['estado'] !== 'CANCELADO'): ?>
                    <button type="button" class="btn btn-outline btn-sm" onclick="abrirModalAbonar(<?= $ped['id'] ?>, '<?= $ped['codigo'] ?>', <?= $ped['saldo_pendiente'] ?>)" style="padding: 0.3rem 0.5rem; color: var(--success); font-weight: 700;" title="Registrar abono de saldo">
                      💵
                    </button>
                  <?php endif; ?>

                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
              <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📋</div>
              <div style="font-size: 1rem; font-weight: 700;">No hay pedidos registrados con el criterio actual.</div>
              <div style="margin-top: 0.5rem;">
                <a href="<?= APP_URL ?>/pedidos/nuevo" class="btn btn-primary btn-sm">Ingresar el Primer Pedido</a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ==============================================================================
     MODAL: CAMBIAR ESTADO DE PEDIDO
     ============================================================================== -->
<div class="modal-backdrop" id="modalEstadoPedido">
  <div class="modal-dialog" style="max-width: 440px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 800; display: flex; align-items: center; gap: 0.4rem;">
        <span>🔄</span> <span id="modalEstadoTitulo">Actualizar Estado</span>
      </h3>
      <button type="button" onclick="closeModal('modalEstadoPedido')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
    </div>

    <form id="formCambiarEstado" method="POST">
      <div class="modal-body">
        
        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label class="form-label" for="select_nuevo_estado" style="font-weight: 700;">Nuevo Estado del Pedido:</label>
          <select id="select_nuevo_estado" name="nuevo_estado" class="form-control" style="font-weight: 700; font-size: 1rem;" required>
            <option value="PENDIENTE">🟡 PENDIENTE (Recibido / Por encargar)</option>
            <option value="EN_CAMINO">🚚 EN CAMINO (Con proveedor / En tránsito)</option>
            <option value="LISTO">🟢 LISTO PARA RETIRAR (En tienda)</option>
            <option value="ENTREGADO">✅ ENTREGADO (Completado con cliente)</option>
            <option value="CANCELADO">❌ CANCELADO</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="modal_estado_notas">Notas / Observación del Cambio (Opcional):</label>
          <input type="text" id="modal_estado_notas" name="notas" class="form-control" placeholder="ej: Producto recibido de bodega, listo en vitrina...">
        </div>

      </div>

      <div class="modal-footer" style="justify-content: flex-end; gap: 0.6rem;">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalEstadoPedido')">Cancelar</button>
        <button type="submit" class="btn btn-primary" style="font-weight: 800;">Actualizar Estado</button>
      </div>
    </form>
  </div>
</div>

<!-- ==============================================================================
     MODAL: REGISTRAR ABONO / PAGO DE SALDO
     ============================================================================== -->
<div class="modal-backdrop" id="modalAbonarPedido">
  <div class="modal-dialog" style="max-width: 440px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 800; display: flex; align-items: center; gap: 0.4rem;">
        <span>💵</span> <span id="modalAbonarTitulo">Registrar Abono de Pedido</span>
      </h3>
      <button type="button" onclick="closeModal('modalAbonarPedido')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
    </div>

    <form id="formAbonarPedido" method="POST">
      <div class="modal-body">
        
        <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-muted); border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.88rem;">
          <div>
            Saldo Restante Pendiente: <strong id="modalSaldoPendienteDisplay" style="color: var(--danger); font-family: 'JetBrains Mono', monospace; font-size: 1.15rem; font-weight: 900;">$ 0</strong>
          </div>
          <button type="button" class="btn btn-sm btn-outline" id="btnPagarTodoAbono" style="font-size: 0.8rem; font-weight: 700; padding: 0.3rem 0.65rem;" title="Llenar con el total pendiente">
            ⚡ Saldo Total
          </button>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label class="form-label" for="monto_abono" style="font-weight: 800;">Monto a Abonar ($) *</label>
          <input type="number" step="any" min="0.01" id="monto_abono" name="monto_abono" class="form-control form-control-lg" style="font-family: 'JetBrains Mono', monospace; font-size: 1.5rem; font-weight: 900; color: #047857;" placeholder="0" required autofocus>
        </div>

        <div class="form-group">
          <label class="form-label" for="metodo_abono">Medio de Pago:</label>
          <select id="metodo_abono" name="metodo_abono" class="form-control" style="font-weight: 700;">
            <option value="EFECTIVO" selected>💵 Efectivo (Ingresa a Gaveta)</option>
            <option value="TRANSFERENCIA">📲 Transferencia / Nequi / Daviplata</option>
            <option value="TARJETA">💳 Tarjeta Débito / Crédito</option>
          </select>
        </div>

      </div>

      <div class="modal-footer" style="justify-content: flex-end; gap: 0.6rem;">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAbonarPedido')">Cancelar</button>
        <button type="submit" class="btn btn-success" style="font-weight: 800;">Confirmar Abono</button>
      </div>
    </form>
  </div>
</div>
