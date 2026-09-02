<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ticket #<?= htmlspecialchars($factura['numero_factura']) ?></title>
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
    .items-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11px;
      margin-top: 4px;
    }
    .items-table th {
      text-align: left;
      border-bottom: 1px dashed #000;
      padding-bottom: 2px;
      font-size: 10px;
    }
    .items-table td {
      padding: 3px 0;
      vertical-align: top;
    }
    .text-right {
      text-align: right;
    }
    .text-center {
      text-align: center;
    }
    .totals-table {
      width: 100%;
      margin-top: 4px;
      font-size: 11px;
    }
    .totals-table td {
      padding: 2px 0;
    }
    .grand-total {
      font-size: 14px;
      font-weight: bold;
    }
    .ticket-footer {
      text-align: center;
      font-size: 10px;
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
      <?= htmlspecialchars($config['empresa_direccion'] ?? '') ?><br>
      Tel: <?= htmlspecialchars($config['empresa_telefono'] ?? '') ?>
    </div>
  </div>

  <div class="divider"></div>

  <div class="ticket-info">
    <strong>FACTURA:</strong> <?= htmlspecialchars($factura['numero_factura']) ?><br>
    <strong>FECHA:</strong> <?= date('d/m/Y H:i', strtotime($factura['created_at'])) ?><br>
    <strong>CAJERO:</strong> <?= htmlspecialchars($factura['cajero_nombre']) ?><br>
    <strong>CLIENTE:</strong> <?= htmlspecialchars($factura['cliente_nombre']) ?><br>
    <strong>DOC/NIT:</strong> <?= htmlspecialchars($factura['cliente_documento']) ?>
  </div>

  <div class="divider"></div>

  <table class="items-table">
    <thead>
      <tr>
        <th style="width: 50%;">DESCRIPCIÓN</th>
        <th style="width: 20%;" class="text-center">CANT</th>
        <th style="width: 30%;" class="text-right">TOTAL</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($factura['items'] as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['producto_nombre']) ?></td>
          <td class="text-center"><?= $it['cantidad'] ?></td>
          <td class="text-right">$<?= number_format($it['subtotal'], 0, ',', '.') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="divider"></div>

  <table class="totals-table">
    <tr>
      <td>Subtotal:</td>
      <td class="text-right">$<?= number_format($factura['subtotal'], 0, ',', '.') ?></td>
    </tr>
    <?php if ($factura['descuento'] > 0): ?>
    <tr>
      <td>Descuento:</td>
      <td class="text-right">- $<?= number_format($factura['descuento'], 0, ',', '.') ?></td>
    </tr>
    <?php endif; ?>
    <tr class="grand-total">
      <td>TOTAL:</td>
      <td class="text-right">$<?= number_format($factura['total'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <td>Método Pago:</td>
      <td class="text-right"><?= htmlspecialchars($factura['metodo_pago']) ?></td>
    </tr>
    <?php if ($factura['metodo_pago'] === 'EFECTIVO'): ?>
    <tr>
      <td>Recibido:</td>
      <td class="text-right">$<?= number_format($factura['monto_recibido'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <td><strong>Cambio:</strong></td>
      <td class="text-right"><strong>$<?= number_format($factura['cambio'], 0, ',', '.') ?></strong></td>
    </tr>
    <?php endif; ?>
  </table>

  <div class="divider"></div>

  <div class="ticket-footer">
    <?= nl2br(htmlspecialchars($config['ticket_pie_pagina'] ?? '¡Gracias por su compra!')) ?>
  </div>

</body>
</html>
