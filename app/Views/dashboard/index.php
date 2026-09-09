<!-- ==============================================================================
     VISTA: DASHBOARD ANALÍTICO & CENTRO DE INTELIGENCIA POS (Sober SaaS)
     ============================================================================== -->

<!-- INYECCIÓN DE DATOS PARA GRÁFICOS INTERACTIVOS (CHART.JS) -->
<script>
  window.DASHBOARD_DATA = <?= json_encode($reporte, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>

<!-- ==============================================================================
     1. BARRA DE CONTROL DE PERÍODO Y EXPORTACIÓN
     ============================================================================== -->
<div class="panel" style="margin-bottom: 1.25rem; padding: 1rem 1.25rem;">
  <form id="formFiltroPeriodo" method="GET" action="<?= APP_URL ?>/dashboard" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
    
    <!-- Selector de Tipo de Reporte (Segmented Switch) -->
    <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
      <div style="display: inline-flex; background: var(--bg-muted); padding: 3px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
        <label style="display: flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: var(--radius-xs); font-size: 0.84rem; font-weight: 600; cursor: pointer; transition: all var(--transition-fast); <?= $tipo === 'diario' ? 'background: #ffffff; color: var(--text-main); box-shadow: var(--shadow-xs);' : 'color: var(--text-muted);' ?>">
          <input type="radio" name="tipo" value="diario" id="radTipoDiario" <?= $tipo === 'diario' ? 'checked' : '' ?> style="display:none;">
          <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
          </svg>
          <span>Diario</span>
        </label>
        
        <label style="display: flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: var(--radius-xs); font-size: 0.84rem; font-weight: 600; cursor: pointer; transition: all var(--transition-fast); <?= $tipo === 'mensual' ? 'background: #ffffff; color: var(--text-main); box-shadow: var(--shadow-xs);' : 'color: var(--text-muted);' ?>">
          <input type="radio" name="tipo" value="mensual" id="radTipoMensual" <?= $tipo === 'mensual' ? 'checked' : '' ?> style="display:none;">
          <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
          <span>Mensual</span>
        </label>
      </div>

      <!-- Selector de Fecha (Diario) -->
      <div id="groupFiltroDiario" style="display: <?= $tipo === 'diario' ? 'flex' : 'none' ?>; align-items: center; gap: 0.4rem;">
        <input type="date" name="fecha" id="inputFechaFiltro" value="<?= htmlspecialchars($fecha) ?>" class="form-control" style="font-size: 0.85rem; padding: 0.38rem 0.75rem; width: auto;">
        <button type="button" class="btn btn-outline btn-sm" onclick="setFechaFiltro('hoy')">Hoy</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="setFechaFiltro('ayer')">Ayer</button>
      </div>

      <!-- Selector de Mes (Mensual) -->
      <div id="groupFiltroMensual" style="display: <?= $tipo === 'mensual' ? 'flex' : 'none' ?>; align-items: center; gap: 0.4rem;">
        <input type="month" name="mes" id="inputMesFiltro" value="<?= htmlspecialchars($mes) ?>" class="form-control" style="font-size: 0.85rem; padding: 0.38rem 0.75rem; width: auto;">
        <button type="button" class="btn btn-outline btn-sm" onclick="setMesActual()">Mes Actual</button>
      </div>
    </div>

    <!-- Acciones de Descarga e Impresión -->
    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
      <span class="badge badge-neutral" style="font-size: 0.82rem; padding: 0.4rem 0.65rem;">
        <?= htmlspecialchars($reporte['metricas']['rango']['etiqueta']) ?>
      </span>
      
      <button type="button" class="btn btn-primary btn-sm" onclick="abrirDescargaPDF()" title="Generar informe en PDF">
        <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <polyline points="14 2 14 8 20 8"></polyline>
          <line x1="16" y1="13" x2="8" y2="13"></line>
          <line x1="16" y1="17" x2="8" y2="17"></line>
        </svg>
        <span>Descargar PDF</span>
      </button>

      <a href="<?= APP_URL ?>/dashboard/reporte-pdf?tipo=<?= $tipo ?>&fecha=<?= $fecha ?>&mes=<?= $mes ?>" target="_blank" class="btn btn-outline btn-sm" title="Vista preliminar de impresión">
        <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 6 2 18 2 18 9"></polyline>
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
          <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        <span>Imprimir</span>
      </a>
    </div>

  </form>
</div>

<!-- ==============================================================================
     2. FRANJA MÉTRICA CONTINUA DE KPIs (SOBER SAAS METRIC STRIP)
     ============================================================================== -->
<div class="metric-strip">
  
  <div class="metric-cell">
    <div class="metric-cell-label">Ventas Totales (<?= ucfirst($tipo) ?>)</div>
    <div class="metric-cell-value">$ <?= number_format($reporte['metricas']['total_ventas'], 0, ',', '.') ?></div>
    <div class="metric-cell-sub"><strong><?= $reporte['metricas']['total_facturas'] ?></strong> facturas emitidas</div>
  </div>

  <div class="metric-cell">
    <div class="metric-cell-label">Ganancia Neta</div>
    <div class="metric-cell-value" style="color: var(--success);">$ <?= number_format($reporte['metricas']['ganancia_neta'], 0, ',', '.') ?></div>
    <div class="metric-cell-sub">Margen: <strong><?= $reporte['metricas']['margen_porcentaje'] ?>%</strong></div>
  </div>

  <div class="metric-cell">
    <div class="metric-cell-label">Costo Mercancía (COGS)</div>
    <div class="metric-cell-value">$ <?= number_format($reporte['metricas']['costo_total'], 0, ',', '.') ?></div>
    <div class="metric-cell-sub"><strong><?= $reporte['metricas']['total_unidades'] ?></strong> unidades despachadas</div>
  </div>

  <div class="metric-cell">
    <div class="metric-cell-label">Anulaciones / Pérdidas</div>
    <div class="metric-cell-value" style="color: <?= $reporte['metricas']['perdidas_anulaciones'] > 0 ? 'var(--danger)' : 'var(--text-muted)' ?>;">
      $ <?= number_format($reporte['metricas']['perdidas_anulaciones'], 0, ',', '.') ?>
    </div>
    <div class="metric-cell-sub"><strong><?= $reporte['metricas']['facturas_anuladas'] ?></strong> facturas anuladas</div>
  </div>

  <div class="metric-cell">
    <div class="metric-cell-label">Ticket Promedio</div>
    <div class="metric-cell-value">$ <?= number_format($reporte['metricas']['ticket_promedio'], 0, ',', '.') ?></div>
    <div class="metric-cell-sub">Por transacción</div>
  </div>

  <div class="metric-cell">
    <div class="metric-cell-label">Efectivo en Gaveta</div>
    <div class="metric-cell-value">$ <?= number_format($reporte['metricas']['ventas_efectivo'], 0, ',', '.') ?></div>
    <div class="metric-cell-sub">Cobros en efectivo</div>
  </div>

  <div class="metric-cell">
    <div class="metric-cell-label">Tarjetas & Transferencias</div>
    <div class="metric-cell-value">$ <?= number_format($reporte['metricas']['ventas_tarjeta'] + $reporte['metricas']['ventas_transferencia'], 0, ',', '.') ?></div>
    <div class="metric-cell-sub">Cobros electrónicos</div>
  </div>

</div>

<!-- ==============================================================================
     3. ZONA DE GRÁFICOS ANALÍTICOS (CHART.JS)
     ============================================================================== -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(460px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
  
  <!-- Gráfico 1: Tendencia de Ventas y Ganancias -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="20" x2="18" y2="10"></line>
          <line x1="12" y1="20" x2="12" y2="4"></line>
          <line x1="6" y1="20" x2="6" y2="14"></line>
        </svg>
        <span>Tendencia de Ventas & Ganancias (<?= $tipo === 'mensual' ? 'Por Día' : 'Por Hora' ?>)</span>
      </div>
      <span class="badge badge-neutral"><?= ucfirst($tipo) ?></span>
    </div>
    <div style="position: relative; height: 280px; margin-top: 0.5rem;">
      <canvas id="chartVentas"></canvas>
    </div>
  </div>

  <!-- Gráfico 2: Ganancias vs Costos y Pérdidas -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
        </svg>
        <span>Balance de Ganancias vs Costos & Pérdidas</span>
      </div>
      <span class="badge badge-success">Rentabilidad</span>
    </div>
    <div style="position: relative; height: 280px; margin-top: 0.5rem;">
      <canvas id="chartGananciasPerdidas"></canvas>
    </div>
  </div>

  <!-- Gráfico 3: Distribución por Categorías -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
          <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
        </svg>
        <span>Ventas por Categoría de Accesorios</span>
      </div>
      <span class="badge badge-neutral">Categorías</span>
    </div>
    <div style="position: relative; height: 260px; margin-top: 0.5rem;">
      <canvas id="chartCategorias"></canvas>
    </div>
  </div>

  <!-- Gráfico 4: Participación de Productos Estrella -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
        </svg>
        <span>Top Productos por Ingresos</span>
      </div>
      <span class="badge badge-neutral">Líderes</span>
    </div>
    <div style="position: relative; height: 260px; margin-top: 0.5rem;">
      <canvas id="chartTopProductos"></canvas>
    </div>
  </div>

</div>

<!-- ==============================================================================
     4. RANKINGS DE PRODUCTOS: MÁS VENDIDOS Y MENOS VENDIDOS
     ============================================================================== -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(460px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
  
  <!-- TABLA 1: PRODUCTOS MÁS VENDIDOS -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
          <polyline points="17 6 23 6 23 12"></polyline>
        </svg>
        <span>Productos Más Vendidos</span>
      </div>
      <span class="badge badge-success">Top <?= count($reporte['masVendidos']) ?></span>
    </div>

    <div style="overflow-x: auto;">
      <?php if (!empty($reporte['masVendidos'])): ?>
        <table class="table" style="font-size: 0.85rem; width: 100%;">
          <thead>
            <tr>
              <th style="width: 40px;">#</th>
              <th>Producto / Categoría</th>
              <th style="text-align: center;">Vendidos</th>
              <th style="text-align: right;">Total Venta</th>
              <th style="text-align: right;">Ganancia</th>
              <th style="text-align: center;">Stock</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reporte['masVendidos'] as $idx => $prod): ?>
              <tr>
                <td style="font-weight: 600; color: var(--text-muted); font-variant-numeric: tabular-nums;">
                  <?= $idx + 1 ?>
                </td>
                <td>
                  <div style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($prod['nombre']) ?></div>
                  <div style="font-size: 0.74rem; color: var(--text-muted); display: flex; gap: 0.4rem;">
                    <span><?= htmlspecialchars($prod['categoria_nombre']) ?></span>
                    <span>&bull;</span>
                    <span style="font-variant-numeric: tabular-nums;"><?= $prod['codigo_barras'] ?></span>
                  </div>
                </td>
                <td style="text-align: center; font-weight: 600; font-variant-numeric: tabular-nums;">
                  <?= $prod['total_unidades'] ?>
                </td>
                <td style="text-align: right; font-weight: 600; font-variant-numeric: tabular-nums;">
                  $ <?= number_format($prod['total_ingresos'], 0, ',', '.') ?>
                </td>
                <td style="text-align: right; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--success);">
                  $ <?= number_format($prod['ganancia_total'], 0, ',', '.') ?>
                </td>
                <td style="text-align: center;">
                  <span class="badge <?= $prod['stock'] <= 5 ? 'badge-danger' : 'badge-neutral' ?>" style="font-size: 0.75rem;">
                    <?= $prod['stock'] ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted);">
          <div style="font-weight: 600; font-size: 0.92rem;">Sin ventas registradas en este período</div>
          <div style="font-size: 0.8rem; margin-top: 0.25rem;">Realiza ventas desde el terminal POS para ver el ranking.</div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- TABLA 2: PRODUCTOS MENOS VENDIDOS / SIN ROTACIÓN -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <span>Baja Rotación (Stock Inmovilizado)</span>
      </div>
      <span class="badge badge-warning">Atención</span>
    </div>

    <div style="overflow-x: auto;">
      <?php if (!empty($reporte['menosVendidos'])): ?>
        <table class="table" style="font-size: 0.85rem; width: 100%;">
          <thead>
            <tr>
              <th>Producto / Categoría</th>
              <th style="text-align: center;">Ventas</th>
              <th style="text-align: center;">Stock</th>
              <th style="text-align: right;">Costo</th>
              <th style="text-align: right;">Capital Detenido</th>
              <th style="text-align: center;">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reporte['menosVendidos'] as $prod): ?>
              <tr>
                <td>
                  <div style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($prod['nombre']) ?></div>
                  <div style="font-size: 0.74rem; color: var(--text-muted); display: flex; gap: 0.4rem;">
                    <span><?= htmlspecialchars($prod['categoria_nombre']) ?></span>
                    <span>&bull;</span>
                    <span style="font-variant-numeric: tabular-nums;"><?= $prod['codigo_barras'] ?></span>
                  </div>
                </td>
                <td style="text-align: center;">
                  <span class="badge <?= $prod['unidades_vendidas'] == 0 ? 'badge-danger' : 'badge-warning' ?>" style="font-size: 0.75rem;">
                    <?= $prod['unidades_vendidas'] ?> und
                  </span>
                </td>
                <td style="text-align: center; font-weight: 600; font-variant-numeric: tabular-nums;">
                  <?= $prod['stock'] ?>
                </td>
                <td style="text-align: right; font-variant-numeric: tabular-nums; color: var(--text-muted);">
                  $ <?= number_format($prod['precio_compra'], 0, ',', '.') ?>
                </td>
                <td style="text-align: right; font-weight: 600; font-variant-numeric: tabular-nums; color: #b45309;">
                  $ <?= number_format($prod['capital_inmovilizado'], 0, ',', '.') ?>
                </td>
                <td style="text-align: center;">
                  <a href="<?= APP_URL ?>/inventario?search=<?= urlencode($prod['codigo_barras']) ?>" class="btn btn-outline btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;" title="Ver en Inventario">
                    Ver
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted);">
          <div style="font-weight: 600;">No hay productos con baja rotación</div>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- ==============================================================================
     5. ACCESOS RÁPIDOS Y ESTADO OPERACIONAL
     ============================================================================== -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
  
  <!-- Acceso Rápido Operacional -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
        </svg>
        <span>Acceso Operacional</span>
      </div>
    </div>
    <div style="display: flex; flex-direction: column; gap: 0.65rem;">
      <a href="<?= APP_URL ?>/pos" class="btn btn-primary" style="justify-content: flex-start; gap: 0.75rem; padding: 0.75rem 1rem;">
        <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <div style="text-align: left;">
          <div style="font-weight: 600; font-size: 0.9rem;">Terminal de Ventas (POS)</div>
          <div style="font-size: 0.76rem; opacity: 0.8;">Facturación rápida y lector de barras</div>
        </div>
      </a>
      
      <a href="<?= APP_URL ?>/inventario" class="btn btn-outline" style="justify-content: flex-start; gap: 0.75rem; padding: 0.75rem 1rem;">
        <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
          <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
          <line x1="12" y1="22.08" x2="12" y2="12"></line>
        </svg>
        <div style="text-align: left;">
          <div style="font-weight: 600; color: var(--text-main); font-size: 0.9rem;">Gestión de Inventario</div>
          <div style="font-size: 0.76rem; color: var(--text-muted);"><?= $metricasInventario['total_productos'] ?? 0 ?> productos (<?= $metricasInventario['total_unidades'] ?? 0 ?> unds)</div>
        </div>
      </a>
      
      <a href="<?= APP_URL ?>/caja" class="btn btn-outline" style="justify-content: flex-start; gap: 0.75rem; padding: 0.75rem 1rem;">
        <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="4" width="20" height="16" rx="2"></rect>
          <line x1="6" y1="12" x2="18" y2="12"></line>
          <line x1="12" y1="10" x2="12" y2="14"></line>
        </svg>
        <div style="text-align: left;">
          <div style="font-weight: 600; color: var(--text-main); font-size: 0.9rem;">Caja & Arqueo</div>
          <div style="font-size: 0.76rem; color: var(--text-muted);"><?= $sesionActiva ? 'Turno #' . $sesionActiva['id'] . ' abierto' : 'Sin turno activo' ?></div>
        </div>
      </a>
    </div>
  </div>

  <!-- Estado del Sistema y Datos Financieros -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
        </svg>
        <span>Estado del Sistema & Valoración</span>
      </div>
      <span class="badge badge-success" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 500;">
        <span style="width: 6px; height: 6px; border-radius: 50%; background: #16a34a;"></span>
        <span>Operativo</span>
      </span>
    </div>
    
    <p style="font-size: 0.84rem; color: var(--text-muted); margin-bottom: 0.85rem;">
      Servidor Apache/PHP y Base de Datos PostgreSQL sincronizados en tiempo real.
    </p>

    <div style="background: var(--bg-muted); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem; font-size: 0.84rem;">
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.45rem;">
        <span style="color: var(--text-muted);">Valoración Inventario (Costo):</span>
        <strong style="font-variant-numeric: tabular-nums; color: #b45309;">$ <?= number_format($metricasInventario['valor_costo_total'] ?? 0, 0, ',', '.') ?></strong>
      </div>
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.45rem;">
        <span style="color: var(--text-muted);">Valor Potencial de Venta:</span>
        <strong style="font-variant-numeric: tabular-nums; color: var(--text-main);">$ <?= number_format($metricasInventario['valor_venta_total'] ?? 0, 0, ',', '.') ?></strong>
      </div>
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.45rem;">
        <span style="color: var(--text-muted);">Alertas de Stock Bajo:</span>
        <strong style="color: <?= ($metricasInventario['alertas_stock_bajo'] ?? 0) > 0 ? 'var(--danger)' : 'var(--success)' ?>;">
          <?= $metricasInventario['alertas_stock_bajo'] ?? 0 ?> productos
        </strong>
      </div>
      <div style="display: flex; justify-content: space-between;">
        <span style="color: var(--text-muted);">Impresora Térmica:</span>
        <strong style="color: var(--text-main);">ESC/POS <?= htmlspecialchars($config['impresora_tipo'] ?? 'POS-58') ?></strong>
      </div>
    </div>
  </div>

</div>
