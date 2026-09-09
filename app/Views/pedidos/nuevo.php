<!-- ==============================================================================
     VISTA: INGRESAR NUEVO PEDIDO / ENCARGO DE CLIENTE (Sober SaaS)
     ============================================================================== -->

<div style="max-width: 900px; margin: 0 auto;">
  
  <!-- CABECERA SUPERIOR -->
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
        <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
          <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
        </svg>
        <span>Ingresar Pedido de Cliente</span>
      </h2>
      <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
        Registre solicitudes especiales, accesorios por encargo y gestione el abono inicial.
      </p>
    </div>
    <div>
      <a href="<?= APP_URL ?>/pedidos" class="btn btn-outline btn-sm">
        &larr; Volver al Listado
      </a>
    </div>
  </div>

  <form action="<?= APP_URL ?>/pedidos/guardar" method="POST" id="formNuevoPedido">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <!-- 1. DATOS DEL CLIENTE -->
    <div class="panel" style="margin-bottom: 1.25rem;">
      <div class="panel-header">
        <div class="panel-title">
          <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
          <span>Información del Cliente</span>
        </div>
        <span class="badge badge-neutral">Contacto</span>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-top: 1rem;">
        
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="cliente_nombre">
            Nombre Completo <span style="color: var(--danger);">*</span>
          </label>
          <input 
            type="text" 
            id="cliente_nombre" 
            name="cliente_nombre" 
            class="form-control" 
            placeholder="ej: María Camila Pérez" 
            required 
            autofocus
          >
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="cliente_telefono">
            Teléfono / WhatsApp <span style="color: var(--danger);">*</span>
          </label>
          <div style="display: flex; gap: 0.4rem;">
            <span style="background: var(--bg-muted); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.55rem 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; display: flex; align-items: center;">
              +57
            </span>
            <input 
              type="tel" 
              id="cliente_telefono" 
              name="cliente_telefono" 
              class="form-control" 
              style="font-variant-numeric: tabular-nums;"
              placeholder="3001234567" 
              required
            >
          </div>
          <small style="color: var(--text-muted); font-size: 0.76rem; margin-top: 0.25rem; display: block;">
            Para notificación directa vía WhatsApp cuando el pedido esté listo.
          </small>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="cliente_documento">Cédula / Documento (Opcional):</label>
          <input 
            type="text" 
            id="cliente_documento" 
            name="cliente_documento" 
            class="form-control" 
            placeholder="ej: 1020304050"
          >
        </div>

      </div>
    </div>

    <!-- 2. DETALLES DEL PRODUCTO O ENCARGO -->
    <div class="panel" style="margin-bottom: 1.25rem;">
      <div class="panel-header">
        <div class="panel-title">
          <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
            <line x1="12" y1="18" x2="12.01" y2="18"></line>
          </svg>
          <span>Detalle del Accesorio o Producto Solicitado</span>
        </div>
      </div>

      <!-- Selector: Catálogo vs Pedido Especial -->
      <div style="margin-top: 0.5rem; margin-bottom: 1rem;">
        <label class="form-label" style="margin-bottom: 0.4rem;">Origen del Pedido:</label>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; font-size: 0.88rem; font-weight: 500;">
            <input type="radio" name="tipo_pedido_origen" value="personalizado" checked onchange="toggleOrigenPedido(this.value)">
            <span>Encargo Personalizado / No Disponible en Tienda</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; font-size: 0.88rem; font-weight: 500;">
            <input type="radio" name="tipo_pedido_origen" value="catalogo" onchange="toggleOrigenPedido(this.value)">
            <span>Seleccionar de Catálogo de Productos</span>
          </label>
        </div>
      </div>

      <!-- Selector de producto de catálogo (oculto por defecto) -->
      <div id="selectorCatalogoGroup" class="form-group" style="display: none; margin-bottom: 1rem; background: var(--bg-muted); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
        <label class="form-label" for="select_producto_catalogo">Seleccione el producto del catálogo:</label>
        <select id="select_producto_catalogo" class="form-control" onchange="seleccionarProductoCatalogo(this)">
          <option value="">-- Seleccionar un producto existente --</option>
          <?php foreach ($productos as $p): ?>
            <option 
              value="<?= $p['id'] ?>" 
              data-nombre="<?= htmlspecialchars($p['nombre']) ?>" 
              data-precio="<?= $p['precio_venta'] ?>"
              data-stock="<?= $p['stock'] ?>"
            >
              <?= htmlspecialchars($p['nombre']) ?> - $ <?= number_format($p['precio_venta'], 0, ',', '.') ?> (Stock: <?= $p['stock'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" id="producto_id" name="producto_id" value="">
      </div>

      <!-- Descripción del Accesorio -->
      <div class="form-group" style="margin-bottom: 1rem;">
        <label class="form-label" for="descripcion">
          Descripción detallada del pedido <span style="color: var(--danger);">*</span>
        </label>
        <textarea 
          id="descripcion" 
          name="descripcion" 
          class="form-control" 
          rows="3" 
          placeholder="ej: Funda MagSafe reforzada para iPhone 15 Pro Max, color azul titanio..." 
          required
        ></textarea>
        <small style="color: var(--text-muted); font-size: 0.76rem;">
          Indique marca, modelo exacto de celular, color, tipo de material o especificaciones acordadas.
        </small>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="cantidad">Cantidad de Unidades:</label>
          <input 
            type="number" 
            id="cantidad" 
            name="cantidad" 
            class="form-control" 
            min="1" 
            value="1" 
            required 
            style="font-variant-numeric: tabular-nums;"
            oninput="recalcularSaldos()"
          >
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="fecha_entrega_estimada">Fecha Estimada de Entrega:</label>
          <input 
            type="date" 
            id="fecha_entrega_estimada" 
            name="fecha_entrega_estimada" 
            class="form-control" 
            value="<?= date('Y-m-d', strtotime('+3 days')) ?>"
          >
        </div>
      </div>

    </div>

    <!-- 3. CONDICIONES ECONÓMICAS, ABONO Y SALDO -->
    <div class="panel" style="margin-bottom: 1.25rem;">
      <div class="panel-header">
        <div class="panel-title">
          <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="6" width="20" height="12" rx="2"></rect>
            <circle cx="12" cy="12" r="2"></circle>
          </svg>
          <span>Valores, Anticipo y Saldo Pendiente</span>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-top: 0.75rem;">
        
        <div>
          <div class="form-group" style="margin-bottom: 1rem;">
            <label class="form-label" for="precio_total">
              Precio Total Acordado ($) <span style="color: var(--danger);">*</span>
            </label>
            <input 
              type="number" 
              step="any" 
              min="0" 
              id="precio_total" 
              name="precio_total" 
              class="form-control form-control-lg" 
              style="font-size: 1.4rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--text-main);" 
              placeholder="0" 
              value="0" 
              required
              oninput="recalcularSaldos()"
            >
          </div>

          <div class="form-group" style="margin-bottom: 1rem;">
            <label class="form-label" for="abono">
              Abono Inicial / Anticipo ($):
            </label>
            <input 
              type="number" 
              step="any" 
              min="0" 
              id="abono" 
              name="abono" 
              class="form-control form-control-lg" 
              style="font-size: 1.4rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--success);" 
              placeholder="0" 
              value="0"
              oninput="recalcularSaldos()"
            >
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="metodo_pago_abono">Medio de Pago del Abono:</label>
            <select id="metodo_pago_abono" name="metodo_pago_abono" class="form-control">
              <option value="EFECTIVO" selected>Efectivo (Ingresa a Gaveta de Caja)</option>
              <option value="TRANSFERENCIA">Transferencia Bancaria / Nequi / Daviplata</option>
              <option value="TARJETA">Tarjeta Débito / Crédito</option>
            </select>
          </div>
        </div>

        <!-- PANEL DE SALDO PENDIENTE -->
        <div style="background: var(--bg-muted); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em;">
              Estado de la Cuenta
            </div>
            
            <div style="margin-top: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
              <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: var(--text-muted);">
                <span>Total a Pagar:</span>
                <span id="displayTotalPagar" style="font-weight: 600; font-variant-numeric: tabular-nums; color: var(--text-main);">$ 0</span>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: var(--success); margin-top: 0.35rem;">
                <span>(-) Anticipo / Abono:</span>
                <span id="displayAbono" style="font-weight: 600; font-variant-numeric: tabular-nums;">$ 0</span>
              </div>
            </div>

            <div style="margin-top: 1.25rem;">
              <div style="font-size: 0.78rem; color: var(--danger); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                Saldo Restante por Cobrar
              </div>
              <div id="displaySaldoPendiente" style="font-size: 2rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--danger); margin-top: 0.25rem;">
                $ 0
              </div>
            </div>
          </div>

          <div style="font-size: 0.76rem; color: var(--text-muted); line-height: 1.4; margin-top: 1rem;">
            El saldo se cobrará al momento de la entrega física del pedido al cliente.
          </div>
        </div>

      </div>
    </div>

    <!-- 4. OBSERVACIONES GENERALES -->
    <div class="panel" style="margin-bottom: 1.5rem;">
      <div class="form-group" style="margin-bottom: 0;">
        <label class="form-label" for="notas">Observaciones o Notas Internas (Opcional):</label>
        <textarea id="notas" name="notas" class="form-control" rows="2" placeholder="ej: Cliente avisó que retira en horas de la tarde..."></textarea>
      </div>
    </div>

    <!-- ACCIONES DE GUARDADO -->
    <div style="display: flex; gap: 0.75rem; justify-content: flex-end; align-items: center; margin-bottom: 3rem;">
      <a href="<?= APP_URL ?>/pedidos" class="btn btn-outline">
        Cancelar
      </a>
      <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.75rem; font-weight: 600;">
        <span>Guardar y Registrar Pedido</span>
      </button>
    </div>

  </form>

</div>

<script>
  function formatCOP(val) {
    return '$ ' + Number(val || 0).toLocaleString('es-CO', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    });
  }

  function recalcularSaldos() {
    const total = parseFloat(document.getElementById('precio_total')?.value) || 0;
    const abono = parseFloat(document.getElementById('abono')?.value) || 0;
    const saldo = Math.max(0, total - abono);

    const dispTotal = document.getElementById('displayTotalPagar');
    const dispAbono = document.getElementById('displayAbono');
    const dispSaldo = document.getElementById('displaySaldoPendiente');

    if (dispTotal) dispTotal.textContent = formatCOP(total);
    if (dispAbono) dispAbono.textContent = formatCOP(abono);
    if (dispSaldo) dispSaldo.textContent = formatCOP(saldo);
  }

  function toggleOrigenPedido(tipo) {
    const grupo = document.getElementById('selectorCatalogoGroup');
    const inputDesc = document.getElementById('descripcion');
    const hiddenProdId = document.getElementById('producto_id');

    if (tipo === 'catalogo') {
      if (grupo) grupo.style.display = 'block';
    } else {
      if (grupo) grupo.style.display = 'none';
      if (hiddenProdId) hiddenProdId.value = '';
    }
  }

  function seleccionarProductoCatalogo(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;

    const nombre = opt.getAttribute('data-nombre');
    const precio = parseFloat(opt.getAttribute('data-precio')) || 0;

    const inputDesc = document.getElementById('descripcion');
    const inputPrecio = document.getElementById('precio_total');
    const hiddenProdId = document.getElementById('producto_id');

    if (inputDesc && !inputDesc.value) {
      inputDesc.value = nombre;
    }
    if (inputPrecio) {
      inputPrecio.value = precio;
    }
    if (hiddenProdId) {
      hiddenProdId.value = opt.value;
    }

    recalcularSaldos();
  }

  document.addEventListener('DOMContentLoaded', () => {
    recalcularSaldos();
  });
</script>
