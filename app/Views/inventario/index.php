<!-- ==============================================================================
     VISTA DE INVENTARIO Y MOVIMIENTOS (MÓDULOS 1 Y 2)
     ============================================================================== -->

<!-- MÉTRICAS SUPERIORES DE INVENTARIO -->
<div class="kpi-grid">
  <div class="kpi-card" style="border-left: 4px solid var(--primary);">
    <div class="kpi-icon primary">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Total Referencias</div>
      <div class="kpi-value" id="kpiTotalRefs" style="color: var(--primary);"><?= count($productos) ?></div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--success);">
    <div class="kpi-icon success">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Unidades en Stock</div>
      <div class="kpi-value" id="kpiTotalUnits" style="color: var(--success);"><?= number_format($metricas['total_unidades'] ?? 0, 0, ',', '.') ?></div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--warning);">
    <div class="kpi-icon warning">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Alertas Stock Bajo</div>
      <div class="kpi-value" id="kpiLowStock" style="color: <?= ($metricas['alertas_stock_bajo'] ?? 0) > 0 ? 'var(--danger)' : 'var(--success)' ?>;"><?= $metricas['alertas_stock_bajo'] ?? 0 ?></div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--info);">
    <div class="kpi-icon info">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Valoración Total</div>
      <div class="kpi-value" style="color: var(--info);">$ <?= number_format($metricas['valor_venta_total'] ?? 0, 0, ',', '.') ?></div>
    </div>
  </div>
</div>

<!-- BARRA DE HERRAMIENTAS Y FILTROS -->
<div class="card" style="margin-bottom: 1.5rem;">
  <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between;">
    
    <!-- Filtros de búsqueda en vivo -->
    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; flex: 1; min-width: 280px;">
      <div style="position: relative; flex: 1; min-width: 220px;">
        <input 
          type="text" 
          id="searchProductInput" 
          class="form-control" 
          placeholder="🔍 Buscar por nombre o código de barras..." 
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
        <option value="low" <?= $stockStatus === 'low' ? 'selected' : '' ?>>⚠️ Stock Bajo</option>
        <option value="out" <?= $stockStatus === 'out' ? 'selected' : '' ?>>🔴 Agotados</option>
      </select>
    </div>

    <!-- Botones de Acción -->
    <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
      <a href="<?= APP_URL ?>/inventario/kardex" class="btn btn-outline" title="Ver Historial de Entradas y Salidas">
        <span>📋</span> <span>Kárdex</span>
      </a>
      <button type="button" class="btn btn-outline" onclick="openModal('modalNuevaCategoria')">
        <span>📁</span> <span>Nueva Categoría</span>
      </button>
      <button type="button" class="btn btn-primary" onclick="openModal('modalNuevoProducto')">
        <span>➕</span> <span>Nuevo Producto</span>
      </button>
    </div>
  </div>
</div>

<!-- TABLA MAESTRA DE PRODUCTOS -->
<div class="card" style="padding: 0; overflow: hidden;">
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
                <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600; font-size: 0.82rem; color: #475569; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0;">
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
                <span class="badge badge-info">
                  <?= htmlspecialchars($prod['categoria_nombre'] ?? 'Sin categoría') ?>
                </span>
              </td>
              <td style="text-align: center;">
                <span class="badge <?= $badgeClass ?> stock-pill" id="stock-pill-<?= $prod['id'] ?>">
                  <?= $stockText ?>
                </span>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace; color: var(--text-muted);">
                $ <?= number_format($pCompra, 0, ',', '.') ?>
              </td>
              <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--text-main);" id="precio-venta-<?= $prod['id'] ?>">
                $ <?= number_format($pVenta, 0, ',', '.') ?>
              </td>
              <td style="text-align: center;">
                <span style="font-size: 0.82rem; font-weight: 700; color: <?= $margen >= 40 ? 'var(--success)' : 'var(--text-muted)' ?>;">
                  <?= $margen ?>%
                </span>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <!-- Botón Ajuste Rápido -->
                <button 
                  type="button" 
                  class="btn btn-outline" 
                  style="padding: 0.35rem 0.6rem; font-size: 0.82rem; color: var(--primary); border-color: var(--primary-light);" 
                  title="Ajuste Rápido de Stock y Precio (Módulo 2)"
                  onclick="abrirAjusteRapido(<?= $prod['id'] ?>)"
                >
                  ⚡ Ajustar
                </button>
                
                <!-- Botón Editar Completo -->
                <button 
                  type="button" 
                  class="btn btn-outline" 
                  style="padding: 0.35rem 0.6rem; font-size: 0.82rem;" 
                  title="Editar Producto"
                  onclick="abrirEditarProducto(<?= $prod['id'] ?>)"
                >
                  ✏️
                </button>

                <!-- Botón Ver Kárdex del Producto -->
                <a 
                  href="<?= APP_URL ?>/inventario/kardex/<?= $prod['id'] ?>" 
                  class="btn btn-outline" 
                  style="padding: 0.35rem 0.6rem; font-size: 0.82rem;" 
                  title="Historial de Movimientos"
                >
                  📋
                </a>

                <?php if (\App\Core\Auth::isAdmin()): ?>
                <!-- Botón Eliminar -->
                <a 
                  href="<?= APP_URL ?>/inventario/eliminar/<?= $prod['id'] ?>" 
                  class="btn btn-outline" 
                  style="padding: 0.35rem 0.6rem; font-size: 0.82rem; color: var(--danger);" 
                  title="Eliminar Producto"
                  onclick="return confirm('¿Está seguro de eliminar el producto \'<?= addslashes($prod['nombre']) ?>\'?')"
                >
                  🗑️
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
      <h3 style="font-size: 1.15rem; font-weight: 700;">➕ Registrar Nuevo Accesorio / Producto</h3>
      <button type="button" onclick="closeModal('modalNuevoProducto')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
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
      <h3 style="font-size: 1.15rem; font-weight: 700;">✏️ Editar Información de Producto</h3>
      <button type="button" onclick="closeModal('modalEditarProducto')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
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

<!-- 3. MODAL: AJUSTE RÁPIDO DE STOCK Y PRECIO (MÓDULO 2 CORE) -->
<div class="modal-backdrop" id="modalAjusteRapido">
  <div class="modal-dialog" style="max-width: 480px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--primary);">⚡ Ajuste Rápido de Stock y Precio</h3>
      <button type="button" onclick="closeModal('modalAjusteRapido')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
    </div>
    
    <form id="formAjusteRapido">
      <input type="hidden" id="ajuste_producto_id" name="producto_id" value="">
      
      <div class="modal-body">
        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.85rem; margin-bottom: 1rem;">
          <div style="font-weight: 700; color: var(--text-main);" id="ajuste_prod_nombre">Cargando...</div>
          <div style="font-size: 0.8rem; color: var(--text-muted);" id="ajuste_prod_codigo">Código: -</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label class="form-label" for="ajuste_stock">Cantidad en Stock</label>
            <input type="number" min="0" id="ajuste_stock" name="stock" class="form-control form-control-lg" style="font-weight: 700;" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="ajuste_precio_venta">Precio Venta ($)</label>
            <input type="number" step="0.01" min="0.01" id="ajuste_precio_venta" name="precio_venta" class="form-control form-control-lg" style="font-weight: 700;" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="ajuste_precio_compra">Precio Compra ($) (Opcional)</label>
          <input type="number" step="0.01" min="0" id="ajuste_precio_compra" name="precio_compra" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label" for="ajuste_motivo">Motivo del Ajuste *</label>
          <input type="text" id="ajuste_motivo" name="motivo" class="form-control" placeholder="ej: Ingreso mercancía proveedor, Conteo físico..." value="Ajuste rápido de inventario" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAjusteRapido')">Cancelar</button>
        <button type="submit" class="btn btn-primary">⚡ Guardar Ajuste</button>
      </div>
    </form>
  </div>
</div>

<!-- 4. MODAL: NUEVA CATEGORÍA -->
<div class="modal-backdrop" id="modalNuevaCategoria">
  <div class="modal-dialog" style="max-width: 440px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 700;">📁 Crear Nueva Categoría</h3>
      <button type="button" onclick="closeModal('modalNuevaCategoria')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
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
