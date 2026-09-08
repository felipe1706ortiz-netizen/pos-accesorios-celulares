<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedido #<?= htmlspecialchars($pedido['codigo']) ?></title>
  <style>
    @page {
      margin: 0;
      size: 58mm auto;
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Courier New', Courier, monospace;
      font-size: 12px;
      color: #000000;
      width: 58mm;
      padding: 4mm 2mm;
      margin: 0 auto;
      background: #ffffff;
    }
    .ticket-header {
      text-align: center;
      margin-bottom: 6px;
    }
    .store-title {
      font-size: 14px;
      font-weight: bold;
      text-transform: uppercase;
    }
    .store-info {
      font-size: 10px;
      line-height: 1.2;
      margin-top: 2px;
    }
    .divider {
      border-top: 1px dashed #000000;
      margin: 6px 0;
    }
    .ticket-info {
      font-size: 11px;
      line-height: 1.3;
    }
    .totals-table {
      width: 100%;
      margin-top: 4px;
      font-size: 11px;
    }
    .totals-table td {
      padding: 2px 0;
    }
    .text-right {
      text-align: right;
    }
    .text-center {
      text-align: center;
    }
    .grand-total {
      font-size: 13px;
      font-weight: bold;
    }
    .notice {
      font-size: 9px;
      text-align: center;
      margin-top: 8px;
      line-height: 1.2;
    }
    @media print {
      body {
        width: 100%;
        padding: 0;
      }
    }
  </style>
</head>
<body onload="window.print();">

  <div class="ticket-header">
    <div class="store-title"><?= htmlspecialchars($config['empresa_nombre'] ?? 'TIENDA DE ACCESORIOS') ?></div>
    <div class="store-info">
      NIT: <?= htmlspecialchars($config['empresa_nit'] ?? '') ?><br>
      Tel: <?= htmlspecialchars($config['empresa_telefono'] ?? '') ?><br>
      <?= htmlspecialchars($config['empresa_direccion'] ?? '') ?><br>
      <strong>COMPROBANTE DE PEDIDO / ENCARGO</strong>
    </div>
  </div>

  <div class="divider"></div>

  <div class="ticket-info">
    <strong>PEDIDO #:</strong> <?= htmlspecialchars($pedido['codigo']) ?><br>
    <strong>FECHA:</strong> <?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?><br>
    <?php if (!empty($pedido['fecha_entrega_estimada'])): ?>
      <strong>ESTIMADO:</strong> <?= date('d/m/Y', strtotime($pedido['fecha_entrega_estimada'])) ?><br>
    <?php endif; ?>
    <strong>CLIENTE:</strong> <?= htmlspecialchars($pedido['cliente_nombre']) ?><br>
    <strong>TEL:</strong> <?= htmlspecialchars($pedido['cliente_telefono']) ?><br>
    <strong>ATIENDE:</strong> <?= htmlspecialchars($pedido['cajero_nombre'] ?? 'Personal de Tienda') ?>
  </div>

  <div class="divider"></div>

  <div style="font-size: 11px; margin-bottom: 4px;">
    <strong>DETALLE DEL ENCARGO:</strong><br>
    <?= nl2br(htmlspecialchars($pedido['descripcion'])) ?><br>
    <small>Cantidad: <?= $pedido['cantidad'] ?> und</small>
  </div>

  <div class="divider"></div>

  <table class="totals-table">
    <tr>
      <td>Precio Acordado:</td>
      <td class="text-right">$<?= number_format($pedido['precio_total'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <td>(-) Abono / Anticipo:</td>
      <td class="text-right" style="font-weight: bold;">$<?= number_format($pedido['abono'], 0, ',', '.') ?></td>
    </tr>
    <tr class="grand-total" style="border-top: 1px solid #000;">
      <td>SALDO PENDIENTE:</td>
      <td class="text-right" style="font-size: 14px;">$<?= number_format($pedido['saldo_pendiente'], 0, ',', '.') ?></td>
    </tr>
  </table>

  <div class="divider"></div>

  <div class="notice">
    * Conserve este ticket para reclamar su encargo y cancelar el saldo restante.<br>
    * Le avisaremos vía WhatsApp apenas su producto esté disponible en tienda.<br>
    ¡Gracias por confiar en nosotros!
  </div>

</body>
</html>
