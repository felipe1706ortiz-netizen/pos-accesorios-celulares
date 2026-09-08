<!-- ==============================================================================
     VISTA: INGRESAR NUEVO PEDIDO / ENCARGO DE CLIENTE (UI/UX PRO MAX)
     ============================================================================== -->

<div style="max-width: 900px; margin: 0 auto;">
  
  <!-- CABECERA SUPERIOR -->
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.6rem;">
        <span>📋</span> <span>Ingresar Pedido de Cliente</span>
      </h2>
      <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 0.25rem;">
        Registre solicitudes especiales, accesorios por encargo y gestione el abono inicial.
      </p>
    </div>
    <div>
      <a href="<?= APP_URL ?>/pedidos" class="btn btn-outline" style="font-weight: 700;">
        &larr; Volver al Listado
      </a>
    </div>
  </div>

  <form action="<?= APP_URL ?>/pedidos/guardar" method="POST" id="formNuevoPedido">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <!-- 1. DATOS DEL CLIENTE -->
    <div class="card" style="margin-bottom: 1.5rem; border-top: 4px solid var(--primary);">
      <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
        <h3 class="card-title" style="font-size: 1.05rem;">
          <span style="font-size: 1.2rem;">👤</span> <span>Información del Cliente</span>
        </h3>
        <span class="badge badge-info">Contacto para Notificación</span>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-top: 1.25rem;">
        
        <div class="form-group">
          <label class="form-label" for="cliente_nombre" style="font-weight: 700;">
            Nombre Completo del Cliente <span style="color: var(--danger);">*</span>
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

        <div class="form-group">
          <label class="form-label" for="cliente_telefono" style="font-weight: 700;">
            Teléfono / WhatsApp <span style="color: var(--danger);">*</span>
          </label>
          <div style="display: flex; gap: 0.4rem;">
            <span style="background: var(--bg-muted); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 0.65rem 0.85rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: var(--text-muted); font-size: 0.9rem;">
              🇨🇴 +57
            </span>
            <input 
              type="tel" 
              id="cliente_telefono" 
              name="cliente_telefono" 
              class="form-control" 
              style="font-family: 'JetBrains Mono', monospace; font-weight: 700;"
              placeholder="3001234567" 
              required
            >
          </div>
          <small style="color: var(--text-muted); font-size: 0.76rem; margin-top: 0.25rem; display: block;">
            Se utilizará para avisar con 1 clic por WhatsApp cuando el pedido esté listo.
          </small>
        </div>

        <div class="form-group">
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
    <div class="card" style="margin-bottom: 1.5rem; border-top: 4px solid var(--secondary);">
      <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
        <h3 class="card-title" style="font-size: 1.05rem;">
          <span style="font-size: 1.2rem;">📱</span> <span>Detalle del Accesorio o Producto Solicitado</span>
        </h3>
      </div>

      <!-- Selector: Catálogo vs Pedido Especial -->
      <div style="margin-top: 1.25rem; margin-bottom: 1rem;">
        <label class="form-label" style="font-weight: 700;">Origen del Pedido:</label>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem; font-weight: 600;">
            <input type="radio" name="tipo_pedido_origen" value="personalizado" checked onchange="toggleOrigenPedido(this.value)">
            <span>✨ Encargo Personalizado / No Disponible en Tienda</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem; font-weight: 600;">
            <input type="radio" name="tipo_pedido_origen" value="catalogo" onchange="toggleOrigenPedido(this.value)">
            <span>📦 Seleccionar de Catálogo de Productos</span>
          </label>
        </div>
      </div>

      <!-- Selector de producto de catálogo (oculto por defecto) -->
      <div id="selectorCatalogoGroup" class="form-group" style="display: none; margin-bottom: 1.25rem; background: var(--bg-muted); padding: 1rem; border-radius: var(--radius-md);">
        <label class="form-label" for="select_producto_catalogo" style="font-weight: 700;">Seleccione el producto del catálogo:</label>
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
      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label class="form-label" for="descripcion" style="font-weight: 700;">
          Descripción detallada del pedido <span style="color: var(--danger);">*</span>
        </label>
        <textarea 
          id="descripcion" 
          name="descripcion" 
          class="form-control" 
          rows="3" 
          placeholder="ej: Funda MagSafe reforzada para iPhone 15 Pro Max, color azul titanio, con bordes de silicona de alto impacto..." 
          required
        ></textarea>
        <small style="color: var(--text-muted); font-size: 0.76rem;">
          Indique marca, modelo exacto de celular, color, tipo de material o especificaciones acordadas con el cliente.
        </small>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
        <div class="form-group">
          <label class="form-label" for="cantidad" style="font-weight: 700;">Cantidad de Unidades:</label>
          <input 
            type="number" 
            id="cantidad" 
            name="cantidad" 
            class="form-control" 
            min="1" 
            value="1" 
            required 
            style="font-family: 'JetBrains Mono', monospace; font-weight: 800;"
            oninput="recalcularSaldos()"
          >
        </div>

        <div class="form-group">
          <label class="form-label" for="fecha_entrega_estimada">Fecha Estimada de Entrega:</label>
          <input 
            type="date" 
            id="fecha_entrega_estimada" 
            name="fecha_entrega_estimada" 
            class="form-control" 
            value="<?= date('Y-m-d', strtotime('+3 days')) ?>"
            style="font-family: 'JetBrains Mono', monospace; font-weight: 700;"
          >
        </div>
      </div>

    </div>

    <!-- 3. CONDICIONES ECONÓMICAS, ABONO Y SALDO -->
    <div class="card" style="margin-bottom: 1.5rem; border-top: 4px solid var(--success);">
      <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
        <h3 class="card-title" style="font-size: 1.05rem;">
          <span style="font-size: 1.2rem;">💵</span> <span>Valores, Anticipo y Saldo Pendiente</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-top: 1.25rem;">
        
        <div>
          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="precio_total" style="font-weight: 800; font-size: 1rem;">
              Precio Total Acordado ($) <span style="color: var(--danger);">*</span>
            </label>
            <input 
              type="number" 
              step="100" 
              min="0" 
              id="precio_total" 
              name="precio_total" 
              class="form-control form-control-lg" 
              style="font-family: 'JetBrains Mono', monospace; font-size: 1.6rem; font-weight: 900; color: var(--primary);" 
              placeholder="0" 
              value="0" 
              required
              oninput="recalcularSaldos()"
            >
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="abono" style="font-weight: 800; font-size: 1rem;">
              Abono Inicial / Anticipo ($):
            </label>
            <input 
              type="number" 
              step="100" 
              min="0" 
              id="abono" 
              name="abono" 
              class="form-control form-control-lg" 
              style="font-family: 'JetBrains Mono', monospace; font-size: 1.6rem; font-weight: 900; color: #047857;" 
              placeholder="0" 
              value="0"
              oninput="recalcularSaldos()"
            >
          </div>

          <div class="form-group">
            <label class="form-label" for="metodo_pago_abono">Medio de Pago del Abono:</label>
            <select id="metodo_pago_abono" name="metodo_pago_abono" class="form-control" style="font-weight: 700;">
              <option value="EFECTIVO" selected>💵 Efectivo (Ingresa a Gaveta de Caja)</option>
              <option value="TRANSFERENCIA">📲 Transferencia Bancaria / Nequi / Daviplata</option>
              <option value="TARJETA">💳 Tarjeta Débito / Crédito</option>
            </select>
          </div>
        </div>

        <!-- TARJETA DESTACADA: SALDO PENDIENTE -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); border-radius: var(--radius-lg); padding: 1.75rem; color: #ffffff; display: flex; flex-direction: column; justify-content: space-between; box-shadow: var(--shadow-md);">
          <div>
            <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em;">
              Estado de la Cuenta
            </div>
            
            <div style="margin-top: 1rem; border-bottom: 1px dashed rgba(255, 255, 255, 0.2); padding-bottom: 0.75rem;">
              <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: #cbd5e1;">
                <span>Total a Pagar:</span>
                <span id="displayTotalPagar" style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">$ 0</span>
              </div>
              <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: #34d399; margin-top: 0.35rem;">
                <span>(-) Anticipo / Abono:</span>
                <span id="displayAbono" style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">$ 0</span>
              </div>
            </div>

            <div style="margin-top: 1.25rem;">
              <div style="font-size: 0.85rem; color: #f87171; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                Saldo Restante por Cobrar
              </div>
              <div id="displaySaldoPendiente" style="font-size: 2.2rem; font-weight: 900; font-family: 'JetBrains Mono', monospace; color: #fb7185; margin-top: 0.25rem;">
                $ 0
              </div>
            </div>
          </div>

          <div style="font-size: 0.76rem; color: #94a3b8; line-height: 1.4; margin-top: 1rem;">
            ℹ️ El saldo se cobrará al momento de la entrega física del pedido al cliente.
          </div>
        </div>

      </div>
    </div>

    <!-- 4. OBSERVACIONES GENERALES -->
    <div class="card" style="margin-bottom: 2rem;">
      <div class="form-group">
        <label class="form-label" for="notas">Observaciones o Notas Internas (Opcional):</label>
        <textarea id="notas" name="notas" class="form-control" rows="2" placeholder="ej: Cliente avisó que retira en horas de la tarde, proveedor confirmó entrega para el viernes..."></textarea>
      </div>
    </div>

    <!-- ACCIONES DE GUARDADO -->
    <div style="display: flex; gap: 1rem; justify-content: flex-end; align-items: center; margin-bottom: 3rem;">
      <a href="<?= APP_URL ?>/pedidos" class="btn btn-outline" style="padding: 0.75rem 1.5rem; font-weight: 700;">
        Cancelar
      </a>
      <button type="submit" class="btn btn-primary btn-lg" style="padding: 0.75rem 2.25rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
        <span>💾</span> <span>Guardar y Registrar Pedido</span>
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
