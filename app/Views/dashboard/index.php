<!-- ==============================================================================
     VISTA: DASHBOARD ANALÍTICO & CENTRO DE INTELIGENCIA POS (UI/UX PRO MAX)
     ============================================================================== -->

<!-- INYECCIÓN DE DATOS PARA GRÁFICOS INTERACTIVOS (CHART.JS) -->
<script>
  window.DASHBOARD_DATA = <?= json_encode($reporte, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>

<!-- ==============================================================================
     1. BARRA DE CONTROL DE PERÍODO Y EXPORTACIÓN A PDF
     ============================================================================== -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem 1.5rem; border-top: 4px solid var(--primary); background: #ffffff;">
  <form id="formFiltroPeriodo" method="GET" action="<?= APP_URL ?>/dashboard" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
    
    <!-- Selector de Tipo de Reporte (Segmented Switch) -->
    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
      <div style="display: inline-flex; background: var(--bg-muted); padding: 4px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
        <label style="display: flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; border-radius: var(--radius-sm); font-size: 0.88rem; font-weight: 700; cursor: pointer; transition: all 0.2s ease; <?= $tipo === 'diario' ? 'background: #ffffff; color: var(--primary); box-shadow: var(--shadow-sm);' : 'color: var(--text-muted);' ?>">
          <input type="radio" name="tipo" value="diario" id="radTipoDiario" <?= $tipo === 'diario' ? 'checked' : '' ?> style="display:none;">
          <span>☀️ Reporte Diario</span>
        </label>
        
        <label style="display: flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; border-radius: var(--radius-sm); font-size: 0.88rem; font-weight: 700; cursor: pointer; transition: all 0.2s ease; <?= $tipo === 'mensual' ? 'background: #ffffff; color: var(--primary); box-shadow: var(--shadow-sm);' : 'color: var(--text-muted);' ?>">
          <input type="radio" name="tipo" value="mensual" id="radTipoMensual" <?= $tipo === 'mensual' ? 'checked' : '' ?> style="display:none;">
          <span>📅 Reporte Mensual</span>
        </label>
      </div>

      <!-- Selector de Fecha (Diario) -->
      <div id="groupFiltroDiario" style="display: <?= $tipo === 'diario' ? 'flex' : 'none' ?>; align-items: center; gap: 0.5rem;">
        <input type="date" name="fecha" id="inputFechaFiltro" value="<?= htmlspecialchars($fecha) ?>" class="form-control" style="font-weight: 700; font-size: 0.9rem; padding: 0.45rem 0.8rem; width: auto; font-family: 'JetBrains Mono', monospace;">
        <button type="button" class="btn btn-outline btn-sm" onclick="setFechaFiltro('hoy')" style="font-weight: 700;">Hoy</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="setFechaFiltro('ayer')" style="font-weight: 700;">Ayer</button>
      </div>

      <!-- Selector de Mes (Mensual) -->
      <div id="groupFiltroMensual" style="display: <?= $tipo === 'mensual' ? 'flex' : 'none' ?>; align-items: center; gap: 0.5rem;">
        <input type="month" name="mes" id="inputMesFiltro" value="<?= htmlspecialchars($mes) ?>" class="form-control" style="font-weight: 700; font-size: 0.9rem; padding: 0.45rem 0.8rem; width: auto; font-family: 'JetBrains Mono', monospace;">
        <button type="button" class="btn btn-outline btn-sm" onclick="setMesActual()" style="font-weight: 700;">Mes Actual</button>
      </div>
    </div>

    <!-- Acciones de Descarga e Impresión -->
    <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
      <span class="badge badge-info" style="font-size: 0.85rem; padding: 0.45rem 0.75rem; border-radius: var(--radius-sm);">
        📍 <?= htmlspecialchars($reporte['metricas']['rango']['etiqueta']) ?>
      </span>
      
      <button type="button" class="btn btn-primary btn-sm" onclick="abrirDescargaPDF()" title="Generar informe en PDF" style="font-weight: 700; display: flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem;">
        <span>📄</span> <span>Descargar PDF</span>
      </button>

      <a href="<?= APP_URL ?>/dashboard/reporte-pdf?tipo=<?= $tipo ?>&fecha=<?= $fecha ?>&mes=<?= $mes ?>" target="_blank" class="btn btn-outline btn-sm" title="Vista preliminar de impresión A4" style="font-weight: 700; display: flex; align-items: center; gap: 0.4rem; padding: 0.5rem 0.85rem;">
        <span>🖨️</span> <span>Imprimir</span>
      </a>
    </div>

  </form>
</div>

<!-- ==============================================================================
     2. TARJETAS KPI FINANCIERAS Y OPERACIONALES (RESUMEN EJECUTIVO)
     ============================================================================== -->
<div class="kpi-grid" style="margin-bottom: 1.5rem;">
  
  <!-- Tarjeta 1: Ventas Totales -->
  <div class="kpi-card" style="border-left: 4px solid var(--primary);">
    <div class="kpi-icon primary">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Ventas Totales (<?= ucfirst($tipo) ?>)</div>
      <div class="kpi-value" style="color: var(--primary);">$ <?= number_format($reporte['metricas']['total_ventas'], 0, ',', '.') ?></div>
      <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">
        <strong><?= $reporte['metricas']['total_facturas'] ?></strong> facturas emitidas
      </div>
    </div>
  </div>

  <!-- Tarjeta 2: Ganancia Neta / Utilidad -->
  <div class="kpi-card" style="border-left: 4px solid var(--success);">
    <div class="kpi-icon success">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Ganancia Neta (Utilidad)</div>
      <div class="kpi-value" style="color: var(--success);">$ <?= number_format($reporte['metricas']['ganancia_neta'], 0, ',', '.') ?></div>
      <div style="font-size: 0.78rem; color: #047857; margin-top: 0.2rem; font-weight: 700;">
        Margen Comercial: <?= $reporte['metricas']['margen_porcentaje'] ?>%
      </div>
    </div>
  </div>

  <!-- Tarjeta 3: Costo de Mercancía (COGS) -->
  <div class="kpi-card" style="border-left: 4px solid var(--warning);">
    <div class="kpi-icon warning">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Costo de Compra (COGS)</div>
      <div class="kpi-value" style="color: #b45309;">$ <?= number_format($reporte['metricas']['costo_total'], 0, ',', '.') ?></div>
      <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">
        <strong><?= $reporte['metricas']['total_unidades'] ?></strong> unidades despachadas
      </div>
    </div>
  </div>

  <!-- Tarjeta 4: Pérdidas / Facturas Anuladas -->
  <div class="kpi-card" style="border-left: 4px solid var(--danger);">
    <div class="kpi-icon danger">
      <svg style="width: 26px; height: 26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Pérdidas (Anulaciones)</div>
      <div class="kpi-value" style="color: <?= $reporte['metricas']['perdidas_anulaciones'] > 0 ? 'var(--danger)' : 'var(--text-muted)' ?>;">
        $ <?= number_format($reporte['metricas']['perdidas_anulaciones'], 0, ',', '.') ?>
      </div>
      <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">
        <strong><?= $reporte['metricas']['facturas_anuladas'] ?></strong> facturas anuladas
      </div>
    </div>
  </div>

</div>

<!-- Tarjetas secundarias: Ticket promedio y Cobros -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
  
  <div class="card" style="padding: 1.1rem 1.4rem; display: flex; align-items: center; justify-content: space-between;">
    <div>
      <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.4px;">
        Ticket Promedio de Venta
      </div>
      <div style="font-size: 1.35rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: var(--text-main); margin-top: 0.2rem;">
        $ <?= number_format($reporte['metricas']['ticket_promedio'], 0, ',', '.') ?>
      </div>
    </div>
    <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
      🎯
    </div>
  </div>

  <div class="card" style="padding: 1.1rem 1.4rem; display: flex; align-items: center; justify-content: space-between;">
    <div>
      <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.4px;">
        Cobrado en Efectivo
      </div>
      <div style="font-size: 1.35rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: #047857; margin-top: 0.2rem;">
        $ <?= number_format($reporte['metricas']['ventas_efectivo'], 0, ',', '.') ?>
      </div>
    </div>
    <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
      💵
    </div>
  </div>

  <div class="card" style="padding: 1.1rem 1.4rem; display: flex; align-items: center; justify-content: space-between;">
    <div>
      <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.4px;">
        Tarjetas & Transferencias
      </div>
      <div style="font-size: 1.35rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: var(--secondary); margin-top: 0.2rem;">
        $ <?= number_format($reporte['metricas']['ventas_tarjeta'] + $reporte['metricas']['ventas_transferencia'], 0, ',', '.') ?>
      </div>
    </div>
    <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--secondary-light); color: var(--secondary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
      💳
    </div>
  </div>

</div>

<!-- ==============================================================================
     3. ZONA DE GRÁFICOS ANALÍTICOS (CHART.JS)
     ============================================================================== -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(460px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
  
  <!-- Gráfico 1: Tendencia de Ventas y Ganancias -->
  <div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
      <h3 class="card-title">
        <svg style="width: 20px; height: 20px; color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
        <span>Tendencia de Ventas & Ganancias (<?= $tipo === 'mensual' ? 'Por Día del Mes' : 'Por Hora' ?>)</span>
      </h3>
      <span class="badge badge-primary"><?= ucfirst($tipo) ?></span>
    </div>
    <div style="position: relative; height: 300px; margin-top: 1rem;">
      <canvas id="chartVentas"></canvas>
    </div>
  </div>

  <!-- Gráfico 2: Ganancias vs Costos y Pérdidas -->
  <div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
      <h3 class="card-title">
        <svg style="width: 20px; height: 20px; color: var(--success);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <span>Balance de Ganancias vs Costos & Pérdidas</span>
      </h3>
      <span class="badge badge-success">Rentabilidad</span>
    </div>
    <div style="position: relative; height: 300px; margin-top: 1rem;">
      <canvas id="chartGananciasPerdidas"></canvas>
    </div>
  </div>

  <!-- Gráfico 3: Distribución por Categorías -->
  <div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
      <h3 class="card-title">
        <svg style="width: 20px; height: 20px; color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
        <span>Ventas por Categoría de Accesorios</span>
      </h3>
      <span class="badge badge-info">Mix de Catálogo</span>
    </div>
    <div style="position: relative; height: 280px; margin-top: 1rem;">
      <canvas id="chartCategorias"></canvas>
    </div>
  </div>

  <!-- Gráfico 4: Participación de Productos Estrella -->
  <div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
      <h3 class="card-title">
        <svg style="width: 20px; height: 20px; color: var(--secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <span>Top Productos por Ingresos ($ COP)</span>
      </h3>
      <span class="badge badge-warning">Líderes de Ventas</span>
    </div>
    <div style="position: relative; height: 280px; margin-top: 1rem;">
      <canvas id="chartTopProductos"></canvas>
    </div>
  </div>

</div>

<!-- ==============================================================================
     4. RANKINGS DE PRODUCTOS: MÁS VENDIDOS Y MENOS VENDIDOS (DUAL GRID)
     ============================================================================== -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(460px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
  
  <!-- TABLA 1: PRODUCTOS MÁS VENDIDOS -->
  <div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
      <h3 class="card-title">
        <span style="font-size: 1.2rem;">🏆</span>
        <span>Productos Más Vendidos</span>
      </h3>
      <span class="badge badge-success">Top <?= count($reporte['masVendidos']) ?></span>
    </div>

    <div style="overflow-x: auto; margin-top: 0.5rem;">
      <?php if (!empty($reporte['masVendidos'])): ?>
        <table class="table" style="font-size: 0.85rem; width: 100%;">
          <thead>
            <tr>
              <th style="width: 5%;">#</th>
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
                <td>
                  <?php if ($idx === 0): ?>
                    <span style="font-size: 1.1rem;" title="1er Lugar">🥇</span>
                  <?php elseif ($idx === 1): ?>
                    <span style="font-size: 1.1rem;" title="2do Lugar">🥈</span>
                  <?php elseif ($idx === 2): ?>
                    <span style="font-size: 1.1rem;" title="3er Lugar">🥉</span>
                  <?php else: ?>
                    <span class="badge" style="background: var(--bg-muted); color: var(--text-main);"><?= $idx + 1 ?></span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($prod['nombre']) ?></div>
                  <div style="font-size: 0.74rem; color: var(--text-muted); display: flex; gap: 0.5rem;">
                    <span><?= htmlspecialchars($prod['categoria_nombre']) ?></span>
                    <span>&bull;</span>
                    <span style="font-family: 'JetBrains Mono', monospace;"><?= $prod['codigo_barras'] ?></span>
                  </div>
                </td>
                <td style="text-align: center; font-weight: 800; font-family: 'JetBrains Mono', monospace;">
                  <?= $prod['total_unidades'] ?>
                </td>
                <td style="text-align: right; font-weight: 700; font-family: 'JetBrains Mono', monospace; color: var(--primary);">
                  $ <?= number_format($prod['total_ingresos'], 0, ',', '.') ?>
                </td>
                <td style="text-align: right; font-weight: 700; font-family: 'JetBrains Mono', monospace; color: var(--success);">
                  $ <?= number_format($prod['ganancia_total'], 0, ',', '.') ?>
                </td>
                <td style="text-align: center;">
                  <span class="badge <?= $prod['stock'] <= 5 ? 'badge-danger' : 'badge-info' ?>" style="font-size: 0.75rem;">
                    <?= $prod['stock'] ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted);">
          <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">📦</div>
          <div style="font-weight: 700; font-size: 0.95rem;">Sin ventas registradas en este período</div>
          <div style="font-size: 0.8rem; margin-top: 0.25rem;">Realiza ventas desde el terminal POS para ver el ranking de productos estrella.</div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- TABLA 2: PRODUCTOS MENOS VENDIDOS / SIN ROTACIÓN -->
  <div class="card">
    <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
      <h3 class="card-title">
        <span style="font-size: 1.2rem;">⚠️</span>
        <span>Productos con Menor Rotación (Stock Inmovilizado)</span>
      </h3>
      <span class="badge badge-warning">Atención</span>
    </div>

    <div style="overflow-x: auto; margin-top: 0.5rem;">
      <?php if (!empty($reporte['menosVendidos'])): ?>
        <table class="table" style="font-size: 0.85rem; width: 100%;">
          <thead>
            <tr>
              <th>Producto / Categoría</th>
              <th style="text-align: center;">Ventas Período</th>
              <th style="text-align: center;">Stock Detenido</th>
              <th style="text-align: right;">Costo Compra</th>
              <th style="text-align: right;">Capital Retenido</th>
              <th style="text-align: center;">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reporte['menosVendidos'] as $prod): ?>
              <tr>
                <td>
                  <div style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($prod['nombre']) ?></div>
                  <div style="font-size: 0.74rem; color: var(--text-muted); display: flex; gap: 0.5rem;">
                    <span><?= htmlspecialchars($prod['categoria_nombre']) ?></span>
                    <span>&bull;</span>
                    <span style="font-family: 'JetBrains Mono', monospace;"><?= $prod['codigo_barras'] ?></span>
                  </div>
                </td>
                <td style="text-align: center;">
                  <span class="badge <?= $prod['unidades_vendidas'] == 0 ? 'badge-danger' : 'badge-warning' ?>" style="font-size: 0.75rem;">
                    <?= $prod['unidades_vendidas'] ?> und
                  </span>
                </td>
                <td style="text-align: center; font-weight: 700; font-family: 'JetBrains Mono', monospace;">
                  <?= $prod['stock'] ?>
                </td>
                <td style="text-align: right; font-family: 'JetBrains Mono', monospace; color: var(--text-muted);">
                  $ <?= number_format($prod['precio_compra'], 0, ',', '.') ?>
                </td>
                <td style="text-align: right; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: #b45309;">
                  $ <?= number_format($prod['capital_inmovilizado'], 0, ',', '.') ?>
                </td>
                <td style="text-align: center;">
                  <a href="<?= APP_URL ?>/inventario?search=<?= urlencode($prod['codigo_barras']) ?>" class="btn btn-outline btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;" title="Ver en Inventario para promocionar o ajustar">
                    Ver
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted);">
          <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">✨</div>
          <div style="font-weight: 700;">No hay productos con baja rotación</div>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- ==============================================================================
     5. ACCESOS RÁPIDOS Y ESTADO OPERACIONAL
     ============================================================================== -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
  
  <!-- Acceso Rápido Operacional -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <svg style="width: 20px; height: 20px; color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <span>Acceso Rápido Operacional</span>
      </h3>
    </div>
    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
      <a href="<?= APP_URL ?>/pos" class="btn btn-primary btn-lg" style="justify-content: flex-start; gap: 0.85rem;">
        <span style="font-size: 1.3rem;">🛒</span>
        <div style="text-align: left;">
          <div style="font-weight: 800; font-size: 1rem;">Terminal de Ventas (POS)</div>
          <div style="font-size: 0.78rem; opacity: 0.85; font-weight: 500;">Facturación rápida y escáner de códigos de barras</div>
        </div>
      </a>
      
      <a href="<?= APP_URL ?>/inventario" class="btn btn-outline" style="justify-content: flex-start; gap: 0.85rem; padding: 0.9rem 1.25rem;">
        <span style="font-size: 1.2rem;">📦</span>
        <div style="text-align: left;">
          <div style="font-weight: 700; color: var(--text-main);">Gestión de Inventario y Precios</div>
          <div style="font-size: 0.78rem; color: var(--text-muted);">Stock actual: <?= $metricasInventario['total_productos'] ?? 0 ?> productos (<?= $metricasInventario['total_unidades'] ?? 0 ?> unidades)</div>
        </div>
      </a>
      
      <a href="<?= APP_URL ?>/caja" class="btn btn-outline" style="justify-content: flex-start; gap: 0.85rem; padding: 0.9rem 1.25rem;">
        <span style="font-size: 1.2rem;">💵</span>
        <div style="text-align: left;">
          <div style="font-weight: 700; color: var(--text-main);">Caja y Arqueo en Vivo</div>
          <div style="font-size: 0.78rem; color: var(--text-muted);"><?= $sesionActiva ? 'Sesión #' . $sesionActiva['id'] . ' en curso' : 'Sin turno abierto' ?></div>
        </div>
      </a>
    </div>
  </div>

  <!-- Estado del Sistema y Datos Financieros -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <svg style="width: 20px; height: 20px; color: var(--info);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span>Salud del Sistema & Valoración de Inventario</span>
      </h3>
      <span class="badge badge-success">🟢 Operativo</span>
    </div>
    
    <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 1rem;">
      Servidor Apache/PHP y Base de Datos PostgreSQL (Supabase) sincronizados en tiempo real.
    </p>

    <div style="background: #f8fafc; border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.15rem; font-size: 0.85rem;">
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
        <span style="color: var(--text-muted);">Valoración Inventario (Costo):</span>
        <strong style="font-family: 'JetBrains Mono', monospace; color: #b45309;">$ <?= number_format($metricasInventario['valor_costo_total'] ?? 0, 0, ',', '.') ?></strong>
      </div>
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
        <span style="color: var(--text-muted);">Valor Potencial de Venta:</span>
        <strong style="font-family: 'JetBrains Mono', monospace; color: var(--primary);">$ <?= number_format($metricasInventario['valor_venta_total'] ?? 0, 0, ',', '.') ?></strong>
      </div>
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
        <span style="color: var(--text-muted);">Alertas de Stock Bajo:</span>
        <strong style="color: <?= ($metricasInventario['alertas_stock_bajo'] ?? 0) > 0 ? 'var(--danger)' : 'var(--success)' ?>;">
          <?= $metricasInventario['alertas_stock_bajo'] ?? 0 ?> productos
        </strong>
      </div>
      <div style="display: flex; justify-content: space-between;">
        <span style="color: var(--text-muted);">Impresora Térmica:</span>
        <strong style="color: #047857;">ESC/POS <?= htmlspecialchars($config['impresora_tipo'] ?? 'POS-58') ?></strong>
      </div>
    </div>
  </div>

</div>
