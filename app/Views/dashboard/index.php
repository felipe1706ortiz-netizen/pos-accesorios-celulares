<!-- ==============================================================================
     VISTA: DASHBOARD PRINCIPAL (UI/UX PRO MAX)
     ============================================================================== -->

<!-- TARJETAS KPI DE ALTO IMPACTO -->
<div class="kpi-grid">
  <div class="kpi-card" style="border-left: 4px solid var(--primary);">
    <div class="kpi-icon primary">
      <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Ventas del Día</div>
      <div class="kpi-value" style="color: var(--primary);">$ <?= number_format($stats['ventas_hoy'] ?? 0, 0, ',', '.') ?></div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--success);">
    <div class="kpi-icon success">
      <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Facturas Emitidas</div>
      <div class="kpi-value" style="color: var(--success);"><?= $stats['total_facturas'] ?? 0 ?></div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--info);">
    <div class="kpi-icon info">
      <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Catálogo Activo</div>
      <div class="kpi-value" style="color: var(--info);"><?= $stats['productos_stock'] ?? 0 ?> <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">ítems</span></div>
    </div>
  </div>

  <div class="kpi-card" style="border-left: 4px solid var(--warning);">
    <div class="kpi-icon warning">
      <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div class="kpi-details">
      <div class="kpi-label">Stock Crítico</div>
      <div class="kpi-value" style="color: <?= ($stats['alertas_stock'] ?? 0) > 0 ? 'var(--danger)' : 'var(--success)' ?>;">
        <?= $stats['alertas_stock'] ?? 0 ?> <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">alertas</span>
      </div>
    </div>
  </div>
</div>

<!-- ACCIONES RÁPIDAS Y ESTADO OPERACIONAL -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
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
          <div style="font-size: 0.78rem; opacity: 0.85; font-weight: 500;">Facturación rápida y escáner de códigos</div>
        </div>
      </a>
      
      <a href="<?= APP_URL ?>/inventario" class="btn btn-outline" style="justify-content: flex-start; gap: 0.85rem; padding: 0.9rem 1.25rem;">
        <span style="font-size: 1.2rem;">📦</span>
        <div style="text-align: left;">
          <div style="font-weight: 700; color: var(--text-main);">Gestión de Inventario y Precios</div>
          <div style="font-size: 0.78rem; color: var(--text-muted);">Stock, códigos de barra y categorías</div>
        </div>
      </a>
      
      <a href="<?= APP_URL ?>/caja" class="btn btn-outline" style="justify-content: flex-start; gap: 0.85rem; padding: 0.9rem 1.25rem;">
        <span style="font-size: 1.2rem;">💵</span>
        <div style="text-align: left;">
          <div style="font-weight: 700; color: var(--text-main);">Caja y Arqueo en Vivo</div>
          <div style="font-size: 0.78rem; color: var(--text-muted);">Entradas, salidas, balance y cierre de turno</div>
        </div>
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <svg style="width: 20px; height: 20px; color: var(--info);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span>Salud del Sistema y Datos</span>
      </h3>
      <span class="badge badge-success">🟢 Operativo</span>
    </div>
    
    <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 1.25rem;">
      Servidor Apache/PHP y Base de Datos MySQL sincronizados y optimizados para alta velocidad.
    </p>

    <div style="background: #f8fafc; border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.15rem; font-size: 0.85rem;">
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
        <span style="color: var(--text-muted);">Base de Datos:</span>
        <strong style="font-family: 'JetBrains Mono', monospace; color: var(--text-main);">pos_accesorios</strong>
      </div>
      <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
        <span style="color: var(--text-muted);">Moneda del Sistema:</span>
        <strong style="font-family: 'JetBrains Mono', monospace; color: var(--text-main);">COP (Pesos Colombianos)</strong>
      </div>
      <div style="display: flex; justify-content: space-between;">
        <span style="color: var(--text-muted);">Impresión Térmica:</span>
        <strong style="color: #047857;">ESC/POS 80mm / 58mm</strong>
      </div>
    </div>
  </div>
</div>
