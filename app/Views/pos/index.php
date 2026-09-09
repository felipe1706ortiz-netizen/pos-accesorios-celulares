<!-- ==============================================================================
     VISTA DEL MÓDULO 3: PUNTO DE VENTA (POS CORE - UI/UX PRO MAX KEYBOARD FIRST)
     ============================================================================== -->

<div class="pos-container">
  
  <!-- COLUMNA IZQUIERDA: ESCÁNER Y CARRITO DE COMPRAS -->
  <div class="pos-main-column">
    
    <!-- BARRA DE ESCÁNER DE CÓDIGOS DE BARRAS -->
    <div class="scanner-bar">
      <div class="scanner-input-wrapper">
        <span class="scanner-icon">
          <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="5" x2="3" y2="19"></line>
            <line x1="7" y1="5" x2="7" y2="19"></line>
            <line x1="11" y1="5" x2="11" y2="19"></line>
            <line x1="15" y1="5" x2="15" y2="19"></line>
            <line x1="19" y1="5" x2="19" y2="19"></line>
            <line x1="21" y1="5" x2="21" y2="19"></line>
          </svg>
        </span>
        <input 
          type="text" 
          id="barcodeScanner" 
          class="scanner-input" 
          placeholder="Escanear código de barras o teclear..." 
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
        <button type="button" class="shortcut-btn" id="btnShortcutF6" onclick="abrirModalEstadoGaveta()">
          <kbd>F6</kbd> <span>Ver Caja</span>
        </button>
        <button type="button" class="shortcut-btn" id="btnShortcutF8" style="color: var(--danger); border-color: #fecaca;" onclick="cancelarVenta()">
          <kbd>F8</kbd> <span>Cancelar</span>
        </button>
      </div>
    </div>

    <!-- PANEL DEL CARRITO DE PRODUCTOS -->
    <div class="pos-cart-card">
      <div class="pos-cart-header">
        <div style="font-weight: 600; font-size: 1rem; display: flex; align-items: center; gap: 0.5rem;">
          <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          <span>Carrito de Venta</span>
          <span class="badge badge-neutral" id="cartItemCountBadge">0 ítems</span>
        </div>
        <div style="font-size: 0.8rem; color: var(--text-muted);">
          Atajos: <kbd>+</kbd> / <kbd>-</kbd> para cantidades
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
                <div style="width: 48px; height: 48px; margin: 0 auto 0.75rem; border-radius: 50%; background: var(--bg-muted); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                  <svg style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                  </svg>
                </div>
                <div style="font-size: 1.05rem; font-weight: 600; color: var(--text-main);">El carrito de compras está listo</div>
                <div style="font-size: 0.85rem; margin-top: 0.25rem; color: var(--text-muted);">
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
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.15rem; padding-bottom: 0.85rem; border-bottom: 1px solid var(--border-color);">
        <div style="font-size: 0.84rem; color: var(--text-muted);">
          Cajero: <strong style="color: var(--text-main); font-weight: 600;"><?= htmlspecialchars($currentUser['nombre'] ?? 'Cajero') ?></strong>
        </div>
        <span class="badge badge-success">Sesión #<?= $sesion['id'] ?></span>
      </div>

      <div class="total-row">
        <span>Subtotal Bruto</span>
        <span id="posSubtotal" style="font-weight: 600; font-size: 1rem; font-variant-numeric: tabular-nums;">$ 0</span>
      </div>

      <div class="total-row" style="color: var(--danger);">
        <span>Descuento Aplicado</span>
        <span id="posDescuento" style="font-weight: 600; font-size: 1rem; font-variant-numeric: tabular-nums;">- $ 0</span>
      </div>

      <div class="total-row">
        <span>Impuesto (<?= htmlspecialchars($config['impuesto_nombre'] ?? 'IVA') ?> <?= (float)($config['impuesto_porcentaje'] ?? 0) ?>%)</span>
        <span id="posImpuesto" style="font-weight: 600; font-size: 1rem; font-variant-numeric: tabular-nums;">$ 0</span>
      </div>

      <!-- RECUADRO DE TOTAL GIGANTE -->
      <div class="grand-total-box">
        <div class="grand-total-label">Total a Cobrar</div>
        <div class="grand-total-value" id="posGrandTotal">$ 0</div>
      </div>
    </div>

    <!-- BOTÓN DE COBRO (F12) -->
    <button type="button" class="btn-cobrar" id="btnCobrar" onclick="abrirModalCobro()">
      <span>COBRAR VENTA</span>
      <kbd style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.25); font-size: 0.88rem; padding: 0.15rem 0.5rem;">F12</kbd>
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
      <h3 style="font-size: 1.05rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <span>Búsqueda Manual de Accesorios</span> <kbd>F2</kbd>
      </h3>
      <button type="button" onclick="cerrarModalBusqueda()" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>
    
    <div class="modal-body" style="padding-bottom: 0.75rem;">
      <div class="form-group" style="margin-bottom: 1rem;">
        <input 
          type="text" 
          id="manualSearchInput" 
          class="form-control" 
          placeholder="Escriba modelo, accesorio o código... (Use Flechas y ENTER)" 
          autocomplete="off"
        >
      </div>

      <div style="max-height: 380px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
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
      <div style="font-size: 0.82rem; color: var(--text-muted); margin-right: auto;">
        Navegue con <kbd>&uarr;</kbd> <kbd>&darr;</kbd> y presione <kbd>ENTER</kbd> para añadir.
      </div>
      <button type="button" class="btn btn-outline btn-sm" onclick="cerrarModalBusqueda()">Cerrar <kbd>ESC</kbd></button>
    </div>
  </div>
</div>

<!-- 2. MODAL: DESCUENTO GLOBAL (F4) -->
<div class="modal-backdrop" id="modalDescuento">
  <div class="modal-dialog" style="max-width: 420px;">
    <div class="modal-header">
      <h3 style="font-size: 1.05rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
          <line x1="7" y1="7" x2="7.01" y2="7"></line>
        </svg>
        <span>Aplicar Descuento</span> <kbd>F4</kbd>
      </h3>
      <button type="button" onclick="closeModal('modalDescuento')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>
    
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label" for="descuentoValor">Valor de Descuento ($)</label>
        <input type="number" min="0" step="any" id="descuentoValor" class="form-control" style="font-size: 1.25rem; font-weight: 600; font-variant-numeric: tabular-nums;" placeholder="0" autofocus>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalDescuento')">Cancelar</button>
      <button type="button" class="btn btn-primary btn-sm" onclick="aplicarDescuentoGlobal()">Aplicar Descuento</button>
    </div>
  </div>
</div>

<!-- 3. MODAL: CHECKOUT / COBRO RÁPIDO (F12) -->
<div class="modal-backdrop" id="modalCobro">
  <div class="modal-dialog" style="max-width: 560px;">
    <div class="modal-header">
      <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
        <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
          <line x1="1" y1="10" x2="23" y2="10"></line>
        </svg>
        <span>Cobro de Venta</span> <kbd>F12</kbd>
      </h3>
      <button type="button" onclick="cerrarModalCobro()" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>
    
    <div class="modal-body">
      
      <!-- Selector de Método de Pago -->
      <label class="form-label" style="margin-bottom: 0.5rem;">Método de Pago:</label>
      <div class="payment-methods-grid">
        <label>
          <input type="radio" name="metodoPago" value="EFECTIVO" class="pay-method-radio" checked onchange="cambiarMetodoPago('EFECTIVO')">
          <div class="pay-method-card">
            <span class="icon">
              <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                <circle cx="12" cy="12" r="2"></circle>
              </svg>
            </span>
            <span>Efectivo</span>
          </div>
        </label>

        <label>
          <input type="radio" name="metodoPago" value="TARJETA" class="pay-method-radio" onchange="cambiarMetodoPago('TARJETA')">
          <div class="pay-method-card">
            <span class="icon">
              <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                <line x1="1" y1="10" x2="23" y2="10"></line>
              </svg>
            </span>
            <span>Tarjeta</span>
          </div>
        </label>

        <label>
          <input type="radio" name="metodoPago" value="TRANSFERENCIA" class="pay-method-radio" onchange="cambiarMetodoPago('TRANSFERENCIA')">
          <div class="pay-method-card">
            <span class="icon">
              <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                <line x1="12" y1="18" x2="12.01" y2="18"></line>
              </svg>
            </span>
            <span>Transferencia</span>
          </div>
        </label>
      </div>

      <!-- Resumen del Cobro -->
      <div style="background: var(--bg-muted); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem 1.25rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: 600; color: var(--text-muted); font-size: 0.92rem;">TOTAL A COBRAR:</span>
        <span id="modalCobroTotal" style="font-size: 1.65rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--text-main);">$ 0</span>
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
