<!-- ==============================================================================
     VISTA DE INVENTARIO Y MOVIMIENTOS (Sober SaaS)
     ============================================================================== -->

<!-- MÉTRICAS SUPERIORES DE INVENTARIO (METRIC STRIP) -->
<div class="metric-strip" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 1.5rem;">
  <div class="metric-cell">
    <span class="metric-cell-label">Total Referencias</span>
    <span class="metric-cell-val" id="kpiTotalRefs"><?= count($productos) ?></span>
    <span class="metric-cell-sub">Catálogo activo</span>
  </div>

  <div class="metric-cell">
    <span class="metric-cell-label">Unidades en Stock</span>
    <span class="metric-cell-val" id="kpiTotalUnits"><?= number_format($metricas['total_unidades'] ?? 0, 0, ',', '.') ?></span>
    <span class="metric-cell-sub">En bodega y vitrina</span>
  </div>

  <div class="metric-cell">
    <span class="metric-cell-label">Alertas Stock Bajo</span>
    <span class="metric-cell-val" id="kpiLowStock" style="color: <?= ($metricas['alertas_stock_bajo'] ?? 0) > 0 ? 'var(--danger)' : 'inherit' ?>;"><?= $metricas['alertas_stock_bajo'] ?? 0 ?></span>
    <span class="metric-cell-sub">Bajo umbral mínimo</span>
  </div>

  <div class="metric-cell">
    <span class="metric-cell-label">Valoración Total</span>
    <span class="metric-cell-val">$ <?= number_format($metricas['valor_venta_total'] ?? 0, 0, ',', '.') ?></span>
    <span class="metric-cell-sub">Precio venta al público</span>
  </div>
</div>

<!-- BARRA DE HERRAMIENTAS Y FILTROS -->
<div class="panel" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem;">
  <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between;">
    
    <!-- Filtros de búsqueda en vivo -->
    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; flex: 1; min-width: 280px;">
      <div style="position: relative; flex: 1; min-width: 220px;">
        <input 
          type="text" 
          id="searchProductInput" 
          class="form-control" 
          placeholder="Buscar por nombre o código de barras..." 
          value="<?= htmlspecialchars($search) ?>"
          autocomplete="off"
        >
      </div>

      <select id="filterCategorySelect" class="form-control" style="width: auto; min-width: 180px;">
        <option value="">Todas las Categorías</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= $selectedCat == $cat['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select id="filterStockSelect" class="form-control" style="width: auto; min-width: 160px;">
        <option value="">Todo el Stock</option>
        <option value="ok" <?= $stockStatus === 'ok' ? 'selected' : '' ?>>Stock Normal</option>
        <option value="low" <?= $stockStatus === 'low' ? 'selected' : '' ?>>Stock Bajo</option>
        <option value="out" <?= $stockStatus === 'out' ? 'selected' : '' ?>>Agotados</option>
      </select>
    </div>

    <!-- Botones de Acción -->
    <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
      <a href="<?= APP_URL ?>/inventario/kardex" class="btn btn-outline" title="Ver Historial de Entradas y Salidas">
        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        <span>Kárdex</span>
      </a>
      <button type="button" class="btn btn-outline" onclick="openModal('modalNuevaCategoria')">
        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
        <span>Nueva Categoría</span>
      </button>
      <button type="button" class="btn btn-primary" onclick="openModal('modalNuevoProducto')">
        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Nuevo Producto</span>
      </button>
    </div>
  </div>
</div>

<!-- TABLA MAESTRA DE PRODUCTOS -->
<div class="panel" style="padding: 0; overflow: hidden;">
  <div class="table-responsive">
    <table class="table" id="tablaProductos">
      <thead>
        <tr>
          <th>Código</th>
          <th>Producto / Accesorio</th>
          <th>Categoría</th>
          <th style="text-align: center;">Stock</th>
          <th style="text-align: right;">Precio Compra</th>
          <th style="text-align: right;">Precio Venta</th>
          <th style="text-align: center;">Margen</th>
          <th style="text-align: right;">Acciones</th>
        </tr>
      </thead>
      <tbody id="tbodyProductos">
        <?php if (empty($productos)): ?>
          <tr>
            <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
              No se encontraron productos registrados en el inventario.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($productos as $prod): 
            $stock = (int)$prod['stock'];
            $stockMin = (int)$prod['stock_minimo'];
            $pCompra = (float)$prod['precio_compra'];
            $pVenta = (float)$prod['precio_venta'];
            $margen = $pVenta > 0 ? round((($pVenta - $pCompra) / $pVenta) * 100, 1) : 0;
            
            // Determinar estado de stock
            $badgeClass = 'badge-success';
            $stockText = "{$stock} unids";
            if ($stock === 0) {
              $badgeClass = 'badge-danger';
              $stockText = '0 (Agotado)';
            } elseif ($stock <= $stockMin) {
              $badgeClass = 'badge-warning';
              $stockText = "{$stock} (Bajo)";
            }
          ?>
            <tr id="row-prod-<?= $prod['id'] ?>" 
                data-id="<?= $prod['id'] ?>"
                data-codigo="<?= htmlspecialchars($prod['codigo_barras']) ?>"
                data-nombre="<?= htmlspecialchars($prod['nombre']) ?>"
                data-categoria="<?= $prod['categoria_id'] ?>"
                data-categoria-nombre="<?= htmlspecialchars($prod['categoria_nombre'] ?? 'Sin categoría') ?>"
                data-precio-compra="<?= $pCompra ?>"
                data-precio-venta="<?= $pVenta ?>"
                data-stock="<?= $stock ?>"
                data-stock-minimo="<?= $stockMin ?>"
                data-descripcion="<?= htmlspecialchars($prod['descripcion'] ?? '') ?>"
            >
              <td>
                <span style="font-variant-numeric: tabular-nums; font-weight: 600; font-size: 0.82rem; color: var(--text-secondary); background: #f4f4f5; padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border-color);">
                  <?= htmlspecialchars($prod['codigo_barras']) ?>
                </span>
              </td>
              <td>
                <div style="font-weight: 600; color: var(--text-main); font-size: 0.95rem;">
                  <?= htmlspecialchars($prod['nombre']) ?>
                </div>
                <?php if (!empty($prod['descripcion'])): ?>
                  <div style="font-size: 0.78rem; color: var(--text-muted); max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($prod['descripcion']) ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge badge-neutral">
                  <?= htmlspecialchars($prod['categoria_nombre'] ?? 'Sin categoría') ?>
                </span>
              </td>
              <td style="text-align: center;">
                <span class="badge <?= $badgeClass ?> stock-pill" id="stock-pill-<?= $prod['id'] ?>">
                  <?= $stockText ?>
                </span>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums; color: var(--text-muted);">
                $ <?= number_format($pCompra, 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-variant-numeric: tabular-nums; font-weight: 600; color: var(--text-main);" id="precio-venta-<?= $prod['id'] ?>">
                $ <?= number_format($pVenta, 0, ',', '.') ?>
              </td>
              <td style="text-align: center;">
                <span style="font-size: 0.82rem; font-weight: 600; font-variant-numeric: tabular-nums; color: <?= $margen >= 40 ? 'var(--success)' : 'var(--text-muted)' ?>;">
                  <?= $margen ?>%
                </span>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <!-- Botón Ajuste Rápido -->
                <button 
                  type="button" 
                  class="btn btn-outline" 
                  style="padding: 0.35rem 0.6rem; font-size: 0.82rem;" 
                  title="Ajuste Rápido de Stock y Precio"
                  onclick="abrirAjusteRapido(<?= $prod['id'] ?>)"
                >
                  <svg style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                  <span>Ajustar</span>
                </button>
                
                <!-- Botón Editar Completo -->
                <button 
                  type="button" 
                  class="btn btn-outline" 
                  style="padding: 0.35rem 0.55rem; font-size: 0.82rem;" 
                  title="Editar Producto"
                  onclick="abrirEditarProducto(<?= $prod['id'] ?>)"
                >
                  <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>

                <!-- Botón Ver Kárdex del Producto -->
                <a 
                  href="<?= APP_URL ?>/inventario/kardex/<?= $prod['id'] ?>" 
                  class="btn btn-outline" 
                  style="padding: 0.35rem 0.55rem; font-size: 0.82rem;" 
                  title="Historial de Movimientos"
                >
                  <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </a>

                <?php if (\App\Core\Auth::isAdmin()): ?>
                <!-- Botón Eliminar -->
                <a 
                  href="<?= APP_URL ?>/inventario/eliminar/<?= $prod['id'] ?>" 
                  class="btn btn-outline" 
                  style="padding: 0.35rem 0.55rem; font-size: 0.82rem; color: var(--danger);" 
                  title="Eliminar Producto"
                  onclick="return confirm('¿Está seguro de eliminar el producto \'<?= addslashes($prod['nombre']) ?>\'?')"
                >
                  <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </a>
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
     MODALES INTERACTIVOS
     ============================================================================== -->

<!-- 1. MODAL: NUEVO PRODUCTO -->
<div class="modal-backdrop" id="modalNuevoProducto">
  <div class="modal-dialog" style="max-width: 600px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 700;">Registrar Nuevo Accesorio / Producto</h3>
      <button type="button" onclick="closeModal('modalNuevoProducto')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>
    
    <form action="<?= APP_URL ?>/inventario/guardar" method="POST" id="formNuevoProducto">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      
      <div class="modal-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label class="form-label" for="new_codigo">Código de Barras *</label>
            <input type="text" id="new_codigo" name="codigo_barras" class="form-control" placeholder="Escanear o teclear..." required autofocus>
          </div>

          <div class="form-group">
            <label class="form-label" for="new_categoria">Categoría *</label>
            <select id="new_categoria" name="categoria_id" class="form-control" required>
              <option value="">Seleccione categoría...</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="new_nombre">Nombre del Producto / Modelo *</label>
          <input type="text" id="new_nombre" name="nombre" class="form-control" placeholder="ej: Case Silicona iPhone 15 Pro Max" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label class="form-label" for="new_precio_compra">Precio de Compra ($)</label>
            <input type="number" step="0.01" min="0" id="new_precio_compra" name="precio_compra" class="form-control" value="0.00" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="new_precio_venta">Precio de Venta ($) *</label>
            <input type="number" step="0.01" min="0.01" id="new_precio_venta" name="precio_venta" class="form-control" placeholder="0.00" required>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label class="form-label" for="new_stock">Stock Inicial (Unidades)</label>
            <input type="number" min="0" id="new_stock" name="stock" class="form-control" value="0" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="new_stock_minimo">Stock Mínimo (Alerta)</label>
            <input type="number" min="1" id="new_stock_minimo" name="stock_minimo" class="form-control" value="5" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="new_descripcion">Descripción / Compatibilidad</label>
          <textarea id="new_descripcion" name="descripcion" class="form-control" rows="2" placeholder="Color, compatibilidad o notas técnicas..."></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalNuevoProducto')">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar en Inventario</button>
      </div>
    </form>
  </div>
</div>

<!-- 2. MODAL: EDITAR PRODUCTO COMPLETO -->
<div class="modal-backdrop" id="modalEditarProducto">
  <div class="modal-dialog" style="max-width: 600px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 700;">Editar Información de Producto</h3>
      <button type="button" onclick="closeModal('modalEditarProducto')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>
    
    <form action="" method="POST" id="formEditarProducto">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      
      <div class="modal-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label class="form-label" for="edit_codigo">Código de Barras *</label>
            <input type="text" id="edit_codigo" name="codigo_barras" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="edit_categoria">Categoría *</label>
            <select id="edit_categoria" name="categoria_id" class="form-control" required>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_nombre">Nombre del Producto *</label>
          <input type="text" id="edit_nombre" name="nombre" class="form-control" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label class="form-label" for="edit_precio_compra">Precio Compra ($)</label>
            <input type="number" step="0.01" min="0" id="edit_precio_compra" name="precio_compra" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="edit_precio_venta">Precio Venta ($) *</label>
            <input type="number" step="0.01" min="0.01" id="edit_precio_venta" name="precio_venta" class="form-control" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_stock_minimo">Stock Mínimo (Alerta)</label>
          <input type="number" min="1" id="edit_stock_minimo" name="stock_minimo" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="edit_descripcion">Descripción</label>
          <textarea id="edit_descripcion" name="descripcion" class="form-control" rows="2"></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalEditarProducto')">Cancelar</button>
        <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- 3. MODAL: AJUSTE RÁPIDO DE STOCK Y PRECIO -->
<div class="modal-backdrop" id="modalAjusteRapido">
  <div class="modal-dialog" style="max-width: 480px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 700;">Ajuste Rápido de Stock y Precio</h3>
      <button type="button" onclick="closeModal('modalAjusteRapido')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>
    
    <form id="formAjusteRapido">
      <input type="hidden" id="ajuste_producto_id" name="producto_id" value="">
      
      <div class="modal-body">
        <div style="background: #f4f4f5; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem; margin-bottom: 1rem;">
          <div style="font-weight: 600; color: var(--text-main);" id="ajuste_prod_nombre">Cargando...</div>
          <div style="font-size: 0.8rem; color: var(--text-muted); font-variant-numeric: tabular-nums;" id="ajuste_prod_codigo">Código: -</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label class="form-label" for="ajuste_stock">Cantidad en Stock</label>
            <input type="number" min="0" id="ajuste_stock" name="stock" class="form-control form-control-lg" style="font-weight: 600; font-variant-numeric: tabular-nums;" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="ajuste_precio_venta">Precio Venta ($)</label>
            <input type="number" step="0.01" min="0.01" id="ajuste_precio_venta" name="precio_venta" class="form-control form-control-lg" style="font-weight: 600; font-variant-numeric: tabular-nums;" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="ajuste_precio_compra">Precio Compra ($) (Opcional)</label>
          <input type="number" step="0.01" min="0" id="ajuste_precio_compra" name="precio_compra" class="form-control" style="font-variant-numeric: tabular-nums;">
        </div>

        <div class="form-group">
          <label class="form-label" for="ajuste_motivo">Motivo del Ajuste *</label>
          <input type="text" id="ajuste_motivo" name="motivo" class="form-control" placeholder="ej: Ingreso mercancía proveedor, Conteo físico..." value="Ajuste rápido de inventario" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAjusteRapido')">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar Ajuste</button>
      </div>
    </form>
  </div>
</div>

<!-- 4. MODAL: NUEVA CATEGORÍA -->
<div class="modal-backdrop" id="modalNuevaCategoria">
  <div class="modal-dialog" style="max-width: 440px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 700;">Crear Nueva Categoría</h3>
      <button type="button" onclick="closeModal('modalNuevaCategoria')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);">&times;</button>
    </div>
    
    <form id="formNuevaCategoria">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="cat_nombre">Nombre de la Categoría *</label>
          <input type="text" id="cat_nombre" name="nombre" class="form-control" placeholder="ej: Correas Smartwatch, Baterías..." required autofocus>
        </div>

        <div class="form-group">
          <label class="form-label" for="cat_desc">Descripción</label>
          <input type="text" id="cat_desc" name="descripcion" class="form-control" placeholder="Breve detalle...">
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalNuevaCategoria')">Cancelar</button>
        <button type="submit" class="btn btn-primary">Crear Categoría</button>
      </div>
    </form>
  </div>
</div>
