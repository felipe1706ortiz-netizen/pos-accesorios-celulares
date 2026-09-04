<!-- ==============================================================================
     VISTA DEL MÓDULO 3: PUNTO DE VENTA (POS CORE - UI/UX PRO MAX KEYBOARD FIRST)
     ============================================================================== -->

<div class="pos-container">
  
  <!-- COLUMNA IZQUIERDA: ESCÁNER Y CARRITO DE COMPRAS -->
  <div class="pos-main-column">
    
    <!-- BARRA DE ESCÁNER DE CÓDIGOS DE BARRAS -->
    <div class="scanner-bar">
      <div class="scanner-input-wrapper">
        <span class="scanner-icon">🏷️</span>
        <input 
          type="text" 
          id="barcodeScanner" 
          class="scanner-input" 
          placeholder="Escanear código de barras o teclear... (Cursor activo)" 
          autocomplete="off" 
          autofocus
        >
      </div>

      <div class="pos-shortcuts">
        <button type="button" class="shortcut-btn" id="btnShortcutF2" onclick="abrirModalBusqueda()">
          <kbd>F2</kbd> <span>Buscar</span>
        </button>
        <button type="button" class="shortcut-btn" id="btnShortcutF4" onclick="abrirModalDescuento()">
          <kbd>F4</kbd> <span>Descuento</span>
        </button>
        <button type="button" class="shortcut-btn" id="btnShortcutF6" style="color: var(--success); border-color: #a7f3d0;" onclick="abrirModalEstadoGaveta()">
          <kbd>F6</kbd> <span>💵 Ver Caja</span>
        </button>
        <button type="button" class="shortcut-btn" id="btnShortcutF8" style="color: var(--danger); border-color: #fecdd3;" onclick="cancelarVenta()">
          <kbd>F8</kbd> <span>Cancelar</span>
        </button>
      </div>
    </div>

    <!-- TARJETA DEL CARRITO DE PRODUCTOS -->
    <div class="pos-cart-card">
      <div class="pos-cart-header">
        <div style="font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; gap: 0.6rem;">
          <span>🛒 Carrito de Venta</span>
          <span class="badge badge-info" id="cartItemCountBadge">0 ítems</span>
        </div>
        <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">
          Atajos rápidos: <kbd>+</kbd> / <kbd>-</kbd> para cantidades
        </div>
      </div>

      <div class="pos-cart-table-wrapper">
        <table class="table" style="margin: 0;">
          <thead>
            <tr>
              <th style="width: 42%;">Producto / Accesorio</th>
              <th style="text-align: right; width: 18%;">Precio Unit.</th>
              <th style="text-align: center; width: 16%;">Cantidad</th>
              <th style="text-align: right; width: 16%;">Subtotal</th>
              <th style="text-align: center; width: 8%;"></th>
            </tr>
          </thead>
          <tbody id="cartTableBody">
            <!-- Renderizado dinámico vía JavaScript -->
            <tr id="emptyCartRow">
              <td colspan="5" style="text-align: center; padding: 4.5rem 1.5rem; color: var(--text-muted);">
                <div style="font-size: 3.5rem; margin-bottom: 0.75rem; opacity: 0.7;">📦</div>
                <div style="font-size: 1.2rem; font-weight: 800; color: var(--text-main);">El carrito de compras está listo</div>
                <div style="font-size: 0.92rem; margin-top: 0.35rem; color: var(--text-muted);">
                  Pase un código de barras por el lector o presione <kbd>F2</kbd> para búsqueda rápida manual.
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- COLUMNA DERECHA: TOTALES, DESCUENTOS Y BOTÓN DE COBRO -->
  <div class="checkout-panel">
    
    <div class="totals-card">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.15rem; padding-bottom: 0.85rem; border-bottom: 1.5px solid var(--border-color);">
        <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-muted);">
          Cajero: <strong style="color: var(--text-main);"><?= htmlspecialchars($currentUser['nombre'] ?? 'Cajero') ?></strong>
        </div>
        <span class="badge badge-success">Sesión #<?= $sesion['id'] ?></span>
      </div>

      <div class="total-row">
        <span>Subtotal Bruto</span>
        <span id="posSubtotal" style="font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: 1.05rem;">$ 0</span>
      </div>

      <div class="total-row" style="color: var(--danger);">
        <span>Descuento Aplicado</span>
        <span id="posDescuento" style="font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: 1.05rem;">- $ 0</span>
      </div>

      <div class="total-row">
        <span>Impuesto (<?= htmlspecialchars($config['impuesto_nombre'] ?? 'IVA') ?> <?= (float)($config['impuesto_porcentaje'] ?? 0) ?>%)</span>
        <span id="posImpuesto" style="font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: 1.05rem;">$ 0</span>
      </div>

      <!-- RECUADRO DE TOTAL GIGANTE -->
      <div class="grand-total-box">
        <div class="grand-total-label">Total a Cobrar</div>
        <div class="grand-total-value" id="posGrandTotal">$ 0</div>
      </div>
    </div>

    <!-- BOTÓN GIGANTE DE COBRO (F12) -->
    <button type="button" class="btn-cobrar" id="btnCobrar" onclick="abrirModalCobro()">
      <span>⚡ COBRAR VENTA</span>
      <kbd style="background: rgba(0,0,0,0.3); color: #fff; border: 1px solid rgba(255,255,255,0.4); font-size: 0.95rem; padding: 0.2rem 0.6rem;">F12</kbd>
    </button>

  </div>

</div>

<!-- ==============================================================================
     MODALES DEL MÓDULO POS
     ============================================================================== -->

<!-- 1. MODAL: BÚSQUEDA MANUAL DE PRODUCTOS (F2) -->
<div class="modal-backdrop" id="modalBusquedaManual">
  <div class="modal-dialog" style="max-width: 700px;">
    <div class="modal-header">
      <h3 style="font-size: 1.2rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
        <span>🔍 Búsqueda Manual de Accesorios</span> <kbd>F2</kbd>
      </h3>
      <button type="button" onclick="cerrarModalBusqueda()" style="background:none;border:none;font-size:1.5rem;cursor:pointer;">&times;</button>
    </div>
    
    <div class="modal-body" style="padding-bottom: 0.75rem;">
      <div class="form-group" style="margin-bottom: 1rem;">
        <input 
          type="text" 
          id="manualSearchInput" 
          class="form-control form-control-lg" 
          placeholder="Escriba modelo, tipo de accesorio o código... (Use Flechas y ENTER)" 
          autocomplete="off"
        >
      </div>

      <div style="max-height: 380px; overflow-y: auto; border: 1.5px solid var(--border-color); border-radius: var(--radius-lg);">
        <table class="table" id="tablaBusquedaManual">
          <thead>
            <tr>
              <th>Código</th>
              <th>Accesorio / Modelo</th>
              <th>Categoría</th>
              <th style="text-align: center;">Stock</th>
              <th style="text-align: right;">Precio</th>
              <th style="text-align: center;">Acción</th>
            </tr>
          </thead>
          <tbody id="tbodyBusquedaManual">
            <tr>
              <td colspan="6" style="text-align:center; padding: 2.5rem; color: var(--text-muted);">
                Escriba en el buscador para ver resultados instantáneos.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal-footer">
      <div style="font-size: 0.85rem; color: var(--text-muted); margin-right: auto; font-weight: 600;">
        Navegue con <kbd>↑</kbd> <kbd>↓</kbd> y presione <kbd>ENTER</kbd> para añadir.
      </div>
      <button type="button" class="btn btn-outline" onclick="cerrarModalBusqueda()">Cerrar <kbd>ESC</kbd></button>
    </div>
  </div>
</div>

<!-- 2. MODAL: DESCUENTO GLOBAL (F4) -->
<div class="modal-backdrop" id="modalDescuento">
  <div class="modal-dialog" style="max-width: 420px;">
    <div class="modal-header">
      <h3 style="font-size: 1.2rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
        <span>🏷️ Aplicar Descuento</span> <kbd>F4</kbd>
      </h3>
      <button type="button" onclick="closeModal('modalDescuento')" style="background:none;border:none;font-size:1.5rem;cursor:pointer;">&times;</button>
    </div>
    
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label" for="descuentoValor" style="font-weight: 800;">Valor de Descuento ($)</label>
        <input type="number" min="0" step="100" id="descuentoValor" class="form-control form-control-lg" style="font-family: 'JetBrains Mono', monospace; font-size: 1.5rem; font-weight: 800;" placeholder="0" autofocus>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-outline" onclick="closeModal('modalDescuento')">Cancelar</button>
      <button type="button" class="btn btn-primary" onclick="aplicarDescuentoGlobal()">Aplicar Descuento</button>
    </div>
  </div>
</div>

<!-- 3. MODAL: CHECKOUT / COBRO RÁPIDO (F12) -->
<div class="modal-backdrop" id="modalCobro">
  <div class="modal-dialog" style="max-width: 560px;">
    <div class="modal-header">
      <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
        <span>💳 Cobro de Venta</span> <kbd>F12</kbd>
      </h3>
      <button type="button" onclick="cerrarModalCobro()" style="background:none;border:none;font-size:1.5rem;cursor:pointer;">&times;</button>
    </div>
    
    <div class="modal-body">
      
      <!-- Selector de Método de Pago -->
      <label class="form-label" style="font-weight: 800; margin-bottom: 0.5rem;">Método de Pago:</label>
      <div class="payment-methods-grid">
        <label>
          <input type="radio" name="metodoPago" value="EFECTIVO" class="pay-method-radio" checked onchange="cambiarMetodoPago('EFECTIVO')">
          <div class="pay-method-card">
            <span class="icon">💵</span>
            <span>Efectivo</span>
          </div>
        </label>

        <label>
          <input type="radio" name="metodoPago" value="TARJETA" class="pay-method-radio" onchange="cambiarMetodoPago('TARJETA')">
          <div class="pay-method-card">
            <span class="icon">💳</span>
            <span>Tarjeta / POS</span>
          </div>
        </label>

        <label>
          <input type="radio" name="metodoPago" value="TRANSFERENCIA" class="pay-method-radio" onchange="cambiarMetodoPago('TRANSFERENCIA')">
          <div class="pay-method-card">
            <span class="icon">📱</span>
            <span>QR / Transf.</span>
          </div>
        </label>
      </div>

      <!-- Resumen del Cobro -->
      <div style="background: #f8fafc; border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.15rem 1.5rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: 800; color: var(--text-muted); font-size: 1rem;">TOTAL A COBRAR:</span>
        <span id="modalCobroTotal" style="font-size: 2rem; font-weight: 900; font-family: 'JetBrains Mono', monospace; color: var(--primary);">$ 0</span>
      </div>

      <!-- Sección Exclusiva para Pagos en Efectivo -->
      <div id="seccionEfectivo">
        <div class="form-group" style="margin-bottom: 0.6rem;">
          <label class="form-label" for="montoRecibidoInput" style="font-weight: 800; font-size: 1rem;">Monto Recibido del Cliente ($):</label>
          <input 
            type="number" 
            id="montoRecibidoInput" 
            class="form-control form-control-lg" 
            style="font-size: 1.75rem; font-weight: 900; font-family: 'JetBrains Mono', monospace; color: var(--text-main);" 
            placeholder="0"
            autocomplete="off"
          >
        </div>

        <!-- Billetes Rápidos -->
        <div class="quick-cash-grid">
          <button type="button" class="cash-pill" onclick="establecerMontoExacto()">Exacto</button>
          <button type="button" class="cash-pill" onclick="agregarBilletes(20000)">$ 20.000</button>
          <button type="button" class="cash-pill" onclick="agregarBilletes(50000)">$ 50.000</button>
          <button type="button" class="cash-pill" onclick="agregarBilletes(100000)">$ 100.000</button>
        </div>

        <!-- Recuadro de Vuelto / Cambio -->
        <div class="change-box">
          <span class="change-label">CAMBIO / VUELTO:</span>
          <span class="change-value" id="cambioCalculadoDisplay">$ 0</span>
        </div>
      </div>

      <!-- Datos del Cliente (Acordeón Simple) -->
      <div style="margin-top: 1.5rem; border-top: 1.5px solid var(--border-color); padding-top: 1rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size: 0.82rem; font-weight: 700;">Nombre Cliente:</label>
            <input type="text" id="clienteNombreInput" class="form-control" value="Cliente General">
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" style="font-size: 0.82rem; font-weight: 700;">Documento / C.C. / NIT:</label>
            <input type="text" id="clienteDocInput" class="form-control" value="222222222222">
          </div>
        </div>
      </div>

    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-outline btn-lg" onclick="cerrarModalCobro()">Cancelar <kbd>ESC</kbd></button>
      <button type="button" class="btn btn-success btn-lg" id="btnConfirmarVenta" style="font-weight: 800; padding: 0.9rem 1.75rem;" onclick="confirmarVenta()">
        <span>Confirmar e Imprimir [ENTER]</span>
      </button>
    </div>
  </div>
</div>

<!-- IFRAME OCULTO PARA IMPRESIÓN DIRECTA SIN SALIR DEL POS -->
<iframe id="printIframe" style="display:none; width:0; height:0; border:none;"></iframe>
