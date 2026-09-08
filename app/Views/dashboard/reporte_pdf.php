<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reporte_<?= ucfirst($tipo) ?>_<?= ($tipo === 'mensual') ? $mes : $fecha ?>_<?= APP_NAME ?></title>
  
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

    @page {
      size: A4 portrait;
      margin: 12mm 15mm;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      font-size: 11px;
      color: #1e293b;
      background: #f1f5f9;
      line-height: 1.4;
      padding: 20px 0;
    }

    .report-wrapper {
      max-width: 820px;
      margin: 0 auto;
      background: #ffffff;
      padding: 32px 36px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    /* BARRA DE ACCIÓN EN PANTALLA */
    .action-bar {
      max-width: 820px;
      margin: 0 auto 16px auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #0f172a;
      padding: 12px 20px;
      border-radius: 10px;
      color: #ffffff;
    }

    .action-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 16px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      border: none;
      transition: all 0.2s ease;
    }

    .action-btn-primary {
      background: #4f46e5;
      color: #ffffff;
    }
    .action-btn-primary:hover {
      background: #4338ca;
    }

    .action-btn-outline {
      background: transparent;
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: #ffffff;
    }
    .action-btn-outline:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    /* ENCABEZADO FORMAL */
    .header-table {
      width: 100%;
      border-bottom: 2px solid #0f172a;
      padding-bottom: 14px;
      margin-bottom: 18px;
    }

    .company-title {
      font-size: 18px;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -0.5px;
      text-transform: uppercase;
    }

    .company-sub {
      font-size: 10px;
      color: #64748b;
      margin-top: 2px;
    }

    .report-badge {
      text-align: right;
    }

    .report-badge-pill {
      display: inline-block;
      background: #eef2ff;
      color: #4f46e5;
      border: 1px solid #c7d2fe;
      padding: 4px 10px;
      border-radius: 20px;
      font-weight: 700;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .period-title {
      font-size: 15px;
      font-weight: 800;
      color: #0f172a;
      margin-top: 4px;
    }

    .meta-text {
      font-size: 9.5px;
      color: #64748b;
      margin-top: 2px;
    }

    /* TARJETAS KPI RESUMEN */
    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      margin-bottom: 18px;
    }

    .kpi-card {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 10px 12px;
    }

    .kpi-label {
      font-size: 9px;
      font-weight: 700;
      text-transform: uppercase;
      color: #64748b;
      letter-spacing: 0.4px;
    }

    .kpi-value {
      font-size: 16px;
      font-weight: 800;
      font-family: 'JetBrains Mono', monospace;
      color: #0f172a;
      margin-top: 4px;
    }

    .kpi-sub {
      font-size: 9px;
      color: #64748b;
      margin-top: 2px;
    }

    /* SECCIONES Y TABLAS */
    .section-title {
      font-size: 12px;
      font-weight: 800;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 6px;
      margin: 16px 0 8px 0;
      padding-bottom: 4px;
      border-bottom: 1.5px solid #e2e8f0;
    }

    table.data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 10px;
      margin-bottom: 14px;
    }

    table.data-table th {
      background: #f1f5f9;
      color: #334155;
      font-weight: 700;
      text-align: left;
      padding: 6px 8px;
      border-top: 1px solid #cbd5e1;
      border-bottom: 1px solid #cbd5e1;
      text-transform: uppercase;
      font-size: 8.5px;
      letter-spacing: 0.3px;
    }

    table.data-table td {
      padding: 6px 8px;
      border-bottom: 1px solid #f1f5f9;
      color: #1e293b;
    }

    table.data-table tr:nth-child(even) td {
      background: #fafbfc;
    }

    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .mono { font-family: 'JetBrains Mono', monospace; }

    .tag {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 8.5px;
      font-weight: 700;
    }
    .tag-success { background: #dcfce7; color: #166534; }
    .tag-warning { background: #fef3c7; color: #92400e; }
    .tag-danger { background: #fee2e2; color: #991b1b; }
    .tag-info { background: #e0f2fe; color: #075985; }

    /* ESTADO DE RESULTADOS (P&L) */
    .pnl-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 16px;
    }

    .pnl-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 10px 14px;
    }

    .pnl-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 4px 0;
      border-bottom: 1px dashed #e2e8f0;
      font-size: 10px;
    }
    .pnl-row:last-child {
      border-bottom: none;
    }
    .pnl-row.highlight {
      font-weight: 800;
      font-size: 11px;
      color: #0f172a;
      border-top: 1.5px solid #cbd5e1;
      margin-top: 4px;
      padding-top: 6px;
    }

    /* FIRMAS */
    .signatures-row {
      display: flex;
      justify-content: space-between;
      margin-top: 36px;
      padding-top: 12px;
    }

    .sig-box {
      width: 42%;
      text-align: center;
    }

    .sig-line {
      border-top: 1px solid #64748b;
      margin-bottom: 4px;
    }

    .sig-title {
      font-size: 9px;
      color: #64748b;
      text-transform: uppercase;
      font-weight: 600;
    }

    /* OCULTAR ELEMENTOS AL IMPRIMIR */
    @media print {
      body {
        background: #ffffff;
        padding: 0;
      }
      .action-bar {
        display: none !important;
      }
      .report-wrapper {
        box-shadow: none !important;
        padding: 0 !important;
        border-radius: 0 !important;
      }
    }
  </style>
  <script src="<?= JS_URL ?>/html2pdf.bundle.min.js"></script>
</head>
<body>

  <!-- BARRA DE CONTROL SUPERIOR (SOLO PANTALLA) -->
  <div class="action-bar">
    <div style="display: flex; align-items: center; gap: 8px;">
      <a href="<?= APP_URL ?>/dashboard?tipo=<?= $tipo ?>&fecha=<?= $fecha ?>&mes=<?= $mes ?>" class="action-btn action-btn-outline">
        &larr; Volver al Dashboard
      </a>
      <span style="font-size: 11px; opacity: 0.7;">|</span>
      <span style="font-weight: 600; font-size: 12px;">Vista Preliminar de Impresión / PDF</span>
    </div>
    <div style="display: flex; gap: 8px;">
      <button type="button" onclick="descargarPDFDirecto()" id="btnDescargarPDF" class="action-btn action-btn-primary">
        📥 Descargar PDF
      </button>
      <button type="button" onclick="window.print()" class="action-btn action-btn-outline">
        🖨️ Imprimir
      </button>
    </div>
  </div>

  <!-- DOCUMENTO FORMAL A4 (IMPRIMIBLE Y CONVERTIBLE A PDF) -->
  <div class="report-wrapper" id="reportContent">
    
    <!-- CABECERA -->
    <table class="header-table">
      <tr>
        <td style="width: 60%; vertical-align: top;">
          <div class="company-title"><?= htmlspecialchars($config['empresa_nombre'] ?? 'POS ACCESORIOS & TECH') ?></div>
          <div class="company-sub">
            NIT / RUC: <?= htmlspecialchars($config['empresa_nit'] ?? '901.458.789-0') ?> &bull; 
            Tel: <?= htmlspecialchars($config['empresa_telefono'] ?? '+57 (300) 123-4567') ?><br>
            <?= htmlspecialchars($config['empresa_direccion'] ?? 'Centro Tecnológico Local 102') ?>
          </div>
        </td>
        <td style="width: 40%; vertical-align: top;" class="report-badge">
          <div class="report-badge-pill">Reporte <?= strtoupper($tipo) ?></div>
          <div class="period-title"><?= htmlspecialchars($reporte['metricas']['rango']['etiqueta']) ?></div>
          <div class="meta-text">
            Generado: <?= date('d/m/Y H:i:s') ?><br>
            Sistema POS Celulares
          </div>
        </td>
      </tr>
    </table>

    <!-- KPIS PRINCIPALES -->
    <div class="kpi-grid">
      <div class="kpi-card" style="border-left: 3px solid #4f46e5;">
        <div class="kpi-label">Ventas Totales</div>
        <div class="kpi-value" style="color: #4f46e5;">$ <?= number_format($reporte['metricas']['total_ventas'], 0, ',', '.') ?></div>
        <div class="kpi-sub"><?= $reporte['metricas']['total_facturas'] ?> facturas completadas</div>
      </div>

      <div class="kpi-card" style="border-left: 3px solid #10b981;">
        <div class="kpi-label">Ganancia Neta</div>
        <div class="kpi-value" style="color: #10b981;">$ <?= number_format($reporte['metricas']['ganancia_neta'], 0, ',', '.') ?></div>
        <div class="kpi-sub">Margen: <strong><?= $reporte['metricas']['margen_porcentaje'] ?>%</strong></div>
      </div>

      <div class="kpi-card" style="border-left: 3px solid #f59e0b;">
        <div class="kpi-label">Costo Mercadería</div>
        <div class="kpi-value" style="color: #b45309;">$ <?= number_format($reporte['metricas']['costo_total'], 0, ',', '.') ?></div>
        <div class="kpi-sub"><?= $reporte['metricas']['total_unidades'] ?> unidades vendidas</div>
      </div>

      <div class="kpi-card" style="border-left: 3px solid #f43f5e;">
        <div class="kpi-label">Pérdidas (Anuladas)</div>
        <div class="kpi-value" style="color: <?= $reporte['metricas']['perdidas_anulaciones'] > 0 ? '#e11d48' : '#64748b' ?>;">
          $ <?= number_format($reporte['metricas']['perdidas_anulaciones'], 0, ',', '.') ?>
        </div>
        <div class="kpi-sub"><?= $reporte['metricas']['facturas_anuladas'] ?> anulaciones</div>
      </div>
    </div>

    <!-- ESTADO FINANCIERO CONSOLIDADO (P&L) -->
    <div class="section-title">📊 Balance Financiero del Período</div>
    <div class="pnl-container">
      <div class="pnl-box">
        <div style="font-weight: 700; font-size: 10px; color: #475569; margin-bottom: 6px; text-transform: uppercase;">
          Flujo de Ingresos por Medio de Pago
        </div>
        <div class="pnl-row">
          <span>Ventas Efectivo:</span>
          <span class="mono">$ <?= number_format($reporte['metricas']['ventas_efectivo'], 0, ',', '.') ?></span>
        </div>
        <div class="pnl-row">
          <span>Ventas Tarjeta Débito/Crédito:</span>
          <span class="mono">$ <?= number_format($reporte['metricas']['ventas_tarjeta'], 0, ',', '.') ?></span>
        </div>
        <div class="pnl-row">
          <span>Ventas Transferencia / QR:</span>
          <span class="mono">$ <?= number_format($reporte['metricas']['ventas_transferencia'], 0, ',', '.') ?></span>
        </div>
        <div class="pnl-row highlight">
          <span>Total Recaudado:</span>
          <span class="mono" style="color: #4f46e5;">$ <?= number_format($reporte['metricas']['total_ventas'], 0, ',', '.') ?></span>
        </div>
      </div>

      <div class="pnl-box">
        <div style="font-weight: 700; font-size: 10px; color: #475569; margin-bottom: 6px; text-transform: uppercase;">
          Rentabilidad y Pérdidas
        </div>
        <div class="pnl-row">
          <span>(+) Ingresos Brutos:</span>
          <span class="mono">$ <?= number_format($reporte['metricas']['total_ventas'], 0, ',', '.') ?></span>
        </div>
        <div class="pnl-row">
          <span>(-) Costo de Adquisición (COGS):</span>
          <span class="mono" style="color: #b45309;">- $ <?= number_format($reporte['metricas']['costo_total'], 0, ',', '.') ?></span>
        </div>
        <div class="pnl-row">
          <span>(-) Facturas Anuladas:</span>
          <span class="mono" style="color: #e11d48;">- $ <?= number_format($reporte['metricas']['perdidas_anulaciones'], 0, ',', '.') ?></span>
        </div>
        <div class="pnl-row highlight">
          <span>(=) Utilidad Neta Real:</span>
          <span class="mono" style="color: #10b981;">$ <?= number_format($reporte['metricas']['ganancia_neta'], 0, ',', '.') ?></span>
        </div>
      </div>
    </div>

    <!-- PRODUCTOS MÁS VENDIDOS -->
    <div class="section-title">🏆 Top Productos Más Vendidos</div>
    <?php if (!empty($reporte['masVendidos'])): ?>
      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 38%;">Producto</th>
            <th style="width: 20%;">Categoría</th>
            <th style="width: 10%;" class="text-center">Cant.</th>
            <th style="width: 13%;" class="text-right">Total Venta</th>
            <th style="width: 14%;" class="text-right">Ganancia</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reporte['masVendidos'] as $index => $prod): ?>
            <tr>
              <td class="text-center">
                <span class="tag <?= $index === 0 ? 'tag-warning' : 'tag-info' ?>"><?= $index + 1 ?></span>
              </td>
              <td>
                <strong><?= htmlspecialchars($prod['nombre']) ?></strong>
                <div style="font-size: 8px; color: #64748b; font-family: monospace;"><?= $prod['codigo_barras'] ?></div>
              </td>
              <td><?= htmlspecialchars($prod['categoria_nombre']) ?></td>
              <td class="text-center mono" style="font-weight: 700;"><?= $prod['total_unidades'] ?></td>
              <td class="text-right mono">$ <?= number_format($prod['total_ingresos'], 0, ',', '.') ?></td>
              <td class="text-right mono" style="color: #047857; font-weight: 700;">
                $ <?= number_format($prod['ganancia_total'], 0, ',', '.') ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div style="padding: 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; text-align: center; color: #64748b; font-size: 10px; margin-bottom: 14px;">
        No se registraron ventas de productos en este período seleccionado.
      </div>
    <?php endif; ?>

    <!-- PRODUCTOS MENOS VENDIDOS / SIN ROTACIÓN -->
    <div class="section-title">⚠️ Productos con Menor Rotación (Alerta de Stock)</div>
    <?php if (!empty($reporte['menosVendidos'])): ?>
      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 40%;">Producto</th>
            <th style="width: 20%;">Categoría</th>
            <th style="width: 10%;" class="text-center">Ventas</th>
            <th style="width: 10%;" class="text-center">Stock</th>
            <th style="width: 20%;" class="text-right">Capital Inmovilizado</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reporte['menosVendidos'] as $prod): ?>
            <tr>
              <td>
                <strong><?= htmlspecialchars($prod['nombre']) ?></strong>
                <div style="font-size: 8px; color: #64748b; font-family: monospace;"><?= $prod['codigo_barras'] ?></div>
              </td>
              <td><?= htmlspecialchars($prod['categoria_nombre']) ?></td>
              <td class="text-center">
                <span class="tag <?= $prod['unidades_vendidas'] == 0 ? 'tag-danger' : 'tag-warning' ?>">
                  <?= $prod['unidades_vendidas'] ?> und
                </span>
              </td>
              <td class="text-center mono" style="font-weight: 600;"><?= $prod['stock'] ?></td>
              <td class="text-right mono" style="color: #b45309; font-weight: 700;">
                $ <?= number_format($prod['capital_inmovilizado'], 0, ',', '.') ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <!-- SECCIÓN DE FIRMAS DE CONTROL -->
    <div class="signatures-row">
      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-title">Administrador / Gerente</div>
        <div style="font-size: 9px; font-weight: 700; color: #0f172a; margin-top: 2px;">
          <?= htmlspecialchars(\App\Core\Auth::user()['nombre'] ?? 'Administración') ?>
        </div>
      </div>
      <div class="sig-box">
        <div class="sig-line"></div>
        <div class="sig-title">Revisión Contable / Auditoría</div>
        <div style="font-size: 9px; color: #94a3b8; margin-top: 2px;">Firma y Sello</div>
      </div>
    </div>

  </div>

  <script>
    function descargarPDFDirecto() {
      const btn = document.getElementById('btnDescargarPDF');
      const originalText = btn.innerHTML;
      btn.innerHTML = '⏳ Generando...';
      btn.disabled = true;

      const element = document.getElementById('reportContent');
      const opt = {
        margin:       [8, 10, 8, 10],
        filename:     'Reporte_<?= ucfirst($tipo) ?>_<?= ($tipo === 'mensual') ? $mes : $fecha ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
      };

      html2pdf().set(opt).from(element).save().then(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
      }).catch(err => {
        console.error('Error al generar PDF:', err);
        alert('Hubo un inconveniente al generar el PDF directo. Se abrirá la opción de imprimir.');
        window.print();
        btn.innerHTML = originalText;
        btn.disabled = false;
      });
    }
  </script>
</body>
</html>
