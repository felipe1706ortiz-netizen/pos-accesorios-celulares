<!-- ==============================================================================
     VISTA: TABLERO Y GESTIÓN DE PEDIDOS DE CLIENTES (Sober SaaS)
     ============================================================================== -->

<!-- CABECERA Y ACCIÓN PRINCIPAL -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
      <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
      </svg>
      <span>Pedidos y Encargos de Clientes</span>
    </h2>
    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
      Control de pedidos especiales, anticipos, notificaciones por WhatsApp y entregas.
    </p>
  </div>
  <div>
    <a href="<?= APP_URL ?>/pedidos/nuevo" class="btn btn-primary" style="font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
      <svg style="width: 15px; height: 15px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      <span>Ingresar Nuevo Pedido</span>
    </a>
  </div>
</div>

<!-- ==============================================================================
     1. FRANJA MÉTRICA DE PEDIDOS (METRIC STRIP)
     ============================================================================== -->
<div class="metric-strip" style="margin-bottom: 1.25rem;">
  
  <div class="metric-cell">
    <div class="metric-cell-label">Pedidos Activos</div>
    <div class="metric-cell-value" style="color: #b45309;"><?= $metricas['pedidos_activos'] ?? 0 ?></div>
    <div class="metric-cell-sub">Pendientes y en tránsito</div>
  </div>

  <div class="metric-cell">
    <div class="metric-cell-label">Listos para Retirar</div>
    <div class="metric-cell-value" style="color: var(--success);"><?= $metricas['pedidos_listos'] ?? 0 ?></div>
    <div class="metric-cell-sub">Pendientes por entrega</div>
  </div>

  <div class="metric-cell">
    <div class="metric-cell-label">Total en Abonos</div>
    <div class="metric-cell-value">$ <?= number_format($metricas['total_abonos'] ?? 0, 0, ',', '.') ?></div>
    <div class="metric-cell-sub">Anticipos recaudados</div>
  </div>

  <div class="metric-cell">
    <div class="metric-cell-label">Saldo por Cobrar</div>
    <div class="metric-cell-value" style="color: var(--danger);">$ <?= number_format($metricas['saldo_por_cobrar'] ?? 0, 0, ',', '.') ?></div>
    <div class="metric-cell-sub">Al momento de la entrega</div>
  </div>

</div>

<!-- ==============================================================================
     2. BARRA DE BÚSQUEDA Y PESTAÑAS DE ESTADO
     ============================================================================== -->
<div class="panel" style="margin-bottom: 1.25rem; padding: 1rem 1.25rem;">
  
  <form method="GET" action="<?= APP_URL ?>/pedidos" style="display: flex; flex-wrap: wrap; gap: 0.85rem; justify-content: space-between; align-items: center;">
    
    <!-- Filtros por Estado (Pills) -->
    <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
      <a href="<?= APP_URL ?>/pedidos?estado=TODOS&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'TODOS' ? 'btn-primary' : 'btn-outline' ?> btn-sm">
        Todos
      </a>
      <a href="<?= APP_URL ?>/pedidos?estado=PENDIENTE&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'PENDIENTE' ? 'btn-warning' : 'btn-outline' ?> btn-sm">
        Pendientes
      </a>
      <a href="<?= APP_URL ?>/pedidos?estado=EN_CAMINO&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'EN_CAMINO' ? 'btn-info' : 'btn-outline' ?> btn-sm">
        En Camino
      </a>
      <a href="<?= APP_URL ?>/pedidos?estado=LISTO&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'LISTO' ? 'btn-success' : 'btn-outline' ?> btn-sm">
        Listos
      </a>
      <a href="<?= APP_URL ?>/pedidos?estado=ENTREGADO&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'ENTREGADO' ? 'btn-primary' : 'btn-outline' ?> btn-sm">
        Entregados
      </a>
      <a href="<?= APP_URL ?>/pedidos?estado=CANCELADO&search=<?= urlencode($search) ?>" class="btn <?= $filtro === 'CANCELADO' ? 'btn-danger' : 'btn-outline' ?> btn-sm">
        Cancelados
      </a>
    </div>

    <!-- Buscador -->
    <div style="display: flex; gap: 0.4rem; min-width: 280px; flex: 1; max-width: 420px;">
      <input type="hidden" name="estado" value="<?= htmlspecialchars($filtro) ?>">
      <input 
        type="text" 
        name="search" 
        class="form-control" 
        placeholder="Buscar por código, cliente, teléfono..." 
        value="<?= htmlspecialchars($search) ?>"
        style="font-size: 0.85rem; padding: 0.4rem 0.75rem;"
      >
      <button type="submit" class="btn btn-primary btn-sm">
        <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <span>Buscar</span>
      </button>
      <?php if (!empty($search) || $filtro !== 'TODOS'): ?>
        <a href="<?= APP_URL ?>/pedidos" class="btn btn-outline btn-sm" title="Limpiar filtros">&times;</a>
      <?php endif; ?>
    </div>

  </form>

</div>

<!-- ==============================================================================
     3. TABLA LISTADO DE PEDIDOS
     ============================================================================== -->
<div class="panel" style="padding: 0; overflow: hidden;">
  <div style="overflow-x: auto;">
    <table class="table" style="width: 100%; font-size: 0.85rem; margin: 0;">
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
                'ENTREGADO' => 'badge-neutral',
                'CANCELADO' => 'badge-danger',
                default     => 'badge-neutral'
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
                <span class="badge badge-neutral" style="font-variant-numeric: tabular-nums; font-weight: 600;">
                  <?= htmlspecialchars($ped['codigo']) ?>
                </span>
                <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 0.35rem; font-variant-numeric: tabular-nums;">
                  <?= date('d/m/Y H:i', strtotime($ped['created_at'])) ?>
                </div>
                <?php if (!empty($ped['fecha_entrega_estimada'])): ?>
                  <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.15rem;">
                    Est: <?= date('d/m/Y', strtotime($ped['fecha_entrega_estimada'])) ?>
                  </div>
                <?php endif; ?>
              </td>

              <td>
                <div style="font-weight: 600; color: var(--text-main);">
                  <?= htmlspecialchars($ped['cliente_nombre']) ?>
                </div>
                <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.25rem;">
                  <a href="<?= $waUrl ?>" target="_blank" class="badge" style="background: #15803d; color: #ffffff; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 0.3rem;" title="Chatear en WhatsApp">
                    <svg style="width: 12px; height: 12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                    <span><?= htmlspecialchars($ped['cliente_telefono']) ?></span>
                  </a>
                </div>
                <?php if (!empty($ped['cliente_documento'])): ?>
                  <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.15rem;">
                    Doc: <?= htmlspecialchars($ped['cliente_documento']) ?>
                  </div>
                <?php endif; ?>
              </td>

              <td>
                <div style="color: var(--text-main); line-height: 1.35;">
                  <?= nl2br(htmlspecialchars($ped['descripcion'])) ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.3rem;">
                  Cant: <strong style="color: var(--text-main); font-variant-numeric: tabular-nums;"><?= $ped['cantidad'] ?> und</strong>
                  <?php if (!empty($ped['producto_catalogo_nombre'])): ?>
                    &bull; Catálogo: <em><?= htmlspecialchars($ped['producto_catalogo_nombre']) ?></em>
                  <?php endif; ?>
                </div>
                <?php if (!empty($ped['notas'])): ?>
                  <div style="font-size: 0.74rem; background: var(--bg-muted); padding: 0.25rem 0.5rem; border-radius: 4px; margin-top: 0.35rem; color: var(--text-secondary); border: 1px solid var(--border-color);">
                    <?= htmlspecialchars($ped['notas']) ?>
                  </div>
                <?php endif; ?>
              </td>

              <td style="text-align: right;">
                <div style="font-size: 0.8rem; color: var(--text-muted);">
                  Total: <strong style="font-variant-numeric: tabular-nums; color: var(--text-main);">$ <?= number_format($ped['precio_total'], 0, ',', '.') ?></strong>
                </div>
                <div style="font-size: 0.8rem; color: var(--success); margin-top: 0.15rem;">
                  Abono: <strong style="font-variant-numeric: tabular-nums;">$ <?= number_format($ped['abono'], 0, ',', '.') ?></strong>
                </div>
                <div style="font-size: 0.88rem; font-weight: 600; font-variant-numeric: tabular-nums; color: <?= $ped['saldo_pendiente'] > 0 ? 'var(--danger)' : 'var(--success)' ?>; margin-top: 0.25rem; border-top: 1px dashed var(--border-color); padding-top: 0.2rem;">
                  Saldo: $ <?= $saldoFormateado ?>
                </div>
              </td>

              <td style="text-align: center;">
                <span class="badge <?= $estadoClass ?>" style="font-size: 0.76rem; padding: 0.3rem 0.6rem;">
                  <?= htmlspecialchars($ped['estado']) ?>
                </span>
              </td>

              <td style="text-align: center;">
                <div style="display: flex; gap: 0.3rem; justify-content: center; flex-wrap: wrap;">
                  
                  <!-- WhatsApp Directo -->
                  <a href="<?= $waUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 0.25rem 0.45rem; color: #15803d;" title="Enviar WhatsApp al cliente">
                    <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                  </a>

                  <!-- Cambiar Estado -->
                  <button type="button" class="btn btn-outline btn-sm" onclick="abrirModalEstadoPedido(<?= $ped['id'] ?>, '<?= $ped['codigo'] ?>', '<?= $ped['estado'] ?>')" style="padding: 0.25rem 0.45rem;" title="Actualizar estado">
                    <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="23 4 23 10 17 10"></polyline>
                      <polyline points="1 20 1 14 7 14"></polyline>
                      <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                    </svg>
                  </button>

                  <!-- Imprimir Comprobante -->
                  <a href="<?= APP_URL ?>/pedidos/ticket/<?= $ped['id'] ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 0.25rem 0.45rem;" title="Imprimir comprobante térmico">
                    <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="6 9 6 2 18 2 18 9"></polyline>
                      <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                      <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                  </a>

                  <!-- Abonar Saldo si falta -->
                  <?php if ($ped['saldo_pendiente'] > 0 && $ped['estado'] !== 'CANCELADO'): ?>
                    <button type="button" class="btn btn-outline btn-sm" onclick="abrirModalAbonar(<?= $ped['id'] ?>, '<?= $ped['codigo'] ?>', <?= $ped['saldo_pendiente'] ?>)" style="padding: 0.25rem 0.45rem; color: var(--success);" title="Registrar abono de saldo">
                      <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                        <circle cx="12" cy="12" r="2"></circle>
                      </svg>
                    </button>
                  <?php endif; ?>

                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
              <div style="font-size: 0.95rem; font-weight: 600;">No hay pedidos registrados con el criterio actual.</div>
              <div style="margin-top: 0.75rem;">
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
      <h3 style="font-size: 1.05rem; font-weight: 600; display: flex; align-items: center; gap: 0.4rem; margin: 0;">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 4 23 10 17 10"></polyline>
          <polyline points="1 20 1 14 7 14"></polyline>
          <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
        </svg>
        <span id="modalEstadoTitulo">Actualizar Estado</span>
      </h3>
      <button type="button" onclick="closeModal('modalEstadoPedido')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>

    <form id="formCambiarEstado" method="POST">
      <div class="modal-body">
        
        <div class="form-group" style="margin-bottom: 1rem;">
          <label class="form-label" for="select_nuevo_estado">Nuevo Estado del Pedido:</label>
          <select id="select_nuevo_estado" name="nuevo_estado" class="form-control" required>
            <option value="PENDIENTE">PENDIENTE (Recibido / Por encargar)</option>
            <option value="EN_CAMINO">EN CAMINO (Con proveedor / En tránsito)</option>
            <option value="LISTO">LISTO PARA RETIRAR (En tienda)</option>
            <option value="ENTREGADO">ENTREGADO (Completado con cliente)</option>
            <option value="CANCELADO">CANCELADO</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="modal_estado_notas">Notas / Observación del Cambio (Opcional):</label>
          <input type="text" id="modal_estado_notas" name="notas" class="form-control" placeholder="ej: Producto recibido de bodega, listo en vitrina...">
        </div>

      </div>

      <div class="modal-footer" style="justify-content: flex-end; gap: 0.5rem;">
        <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalEstadoPedido')">Cancelar</button>
        <button type="submit" class="btn btn-primary btn-sm">Actualizar Estado</button>
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
      <h3 style="font-size: 1.05rem; font-weight: 600; display: flex; align-items: center; gap: 0.4rem; margin: 0;">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="6" width="20" height="12" rx="2"></rect>
          <circle cx="12" cy="12" r="2"></circle>
        </svg>
        <span id="modalAbonarTitulo">Registrar Abono de Pedido</span>
      </h3>
      <button type="button" onclick="closeModal('modalAbonarPedido')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>

    <form id="formAbonarPedido" method="POST">
      <div class="modal-body">
        
        <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-muted); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.85rem;">
          <div>
            Saldo Pendiente: <strong id="modalSaldoPendienteDisplay" style="color: var(--danger); font-variant-numeric: tabular-nums; font-size: 1rem;">$ 0</strong>
          </div>
          <button type="button" class="btn btn-sm btn-outline" id="btnPagarTodoAbono" style="font-size: 0.78rem; padding: 0.25rem 0.5rem;" title="Llenar con el total pendiente">
            Pagar Total
          </button>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
          <label class="form-label" for="monto_abono">Monto a Abonar ($) *</label>
          <input type="number" step="any" min="0.01" id="monto_abono" name="monto_abono" class="form-control form-control-lg" style="font-size: 1.35rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--success);" placeholder="0" required autofocus>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="metodo_abono">Medio de Pago:</label>
          <select id="metodo_abono" name="metodo_abono" class="form-control">
            <option value="EFECTIVO" selected>Efectivo (Ingresa a Gaveta)</option>
            <option value="TRANSFERENCIA">Transferencia / Nequi / Daviplata</option>
            <option value="TARJETA">Tarjeta Débito / Crédito</option>
          </select>
        </div>

      </div>

      <div class="modal-footer" style="justify-content: flex-end; gap: 0.5rem;">
        <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalAbonarPedido')">Cancelar</button>
        <button type="submit" class="btn btn-primary btn-sm">Confirmar Abono</button>
      </div>
    </form>
  </div>
</div>
