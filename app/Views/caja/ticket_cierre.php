<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cierre de Caja #<?= htmlspecialchars($sesion['id']) ?></title>
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
    .signatures {
      margin-top: 25px;
      font-size: 10px;
      text-align: center;
    }
    .sig-line {
      border-top: 1px solid #000;
      width: 80%;
      margin: 20px auto 4px;
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
      <strong>ARQUEO Y CIERRE DE CAJA</strong>
    </div>
  </div>

  <div class="divider"></div>

  <div class="ticket-info">
    <strong>TURNO #:</strong> <?= $sesion['id'] ?><br>
    <strong>CAJERO:</strong> <?= htmlspecialchars($sesion['usuario_nombre']) ?><br>
    <strong>APERTURA:</strong> <?= date('d/m/Y H:i', strtotime($sesion['fecha_apertura'])) ?><br>
    <strong>CIERRE:</strong> <?= $sesion['fecha_cierre'] ? date('d/m/Y H:i', strtotime($sesion['fecha_cierre'])) : date('d/m/Y H:i') ?>
  </div>

  <div class="divider"></div>

  <table class="totals-table">
    <tr>
      <td>Base Inicial:</td>
      <td class="text-right">$<?= number_format($sesion['monto_inicial'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <td>(+) Ventas Efectivo:</td>
      <td class="text-right">$<?= number_format($sesion['total_ventas_efectivo'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <td>(+) Entradas Efectivo:</td>
      <td class="text-right">$<?= number_format($sesion['total_entradas'], 0, ',', '.') ?></td>
    </tr>
    <tr>
      <td>(-) Salidas Efectivo:</td>
      <td class="text-right">- $<?= number_format($sesion['total_salidas'], 0, ',', '.') ?></td>
    </tr>
    <tr class="grand-total">
      <td>SALDO ESPERADO:</td>
      <td class="text-right">$<?= number_format($sesion['monto_esperado'], 0, ',', '.') ?></td>
    </tr>
    <tr class="grand-total">
      <td>EFECTIVO REAL:</td>
      <td class="text-right">$<?= number_format($sesion['monto_real'] ?? 0, 0, ',', '.') ?></td>
    </tr>
    <tr>
      <td><strong>DIFERENCIA:</strong></td>
      <td class="text-right">
        <?php 
          $dif = (float)($sesion['diferencia'] ?? 0);
          if ($dif == 0) echo "$0 (Cuadre)";
          elseif ($dif > 0) echo "+$" . number_format($dif, 0, ',', '.') . " (Sobrante)";
          else echo "-$" . number_format(abs($dif), 0, ',', '.') . " (Faltante)";
        ?>
      </td>
    </tr>
  </table>

  <div class="divider"></div>

  <div style="font-size: 11px; margin-top: 4px;">
    <strong>OTROS MEDIOS:</strong><br>
    Ventas Tarjeta: $<?= number_format($sesion['total_ventas_tarjeta'], 0, ',', '.') ?><br>
    Ventas Transf.: $<?= number_format($sesion['total_ventas_transferencia'], 0, ',', '.') ?><br>
    <strong>TOTAL VENTAS: $<?= number_format($sesion['total_ventas_efectivo'] + $sesion['total_ventas_tarjeta'] + $sesion['total_ventas_transferencia'], 0, ',', '.') ?></strong>
  </div>

  <?php if (!empty($sesion['notas'])): ?>
    <div class="divider"></div>
    <div style="font-size: 10px;">
      <strong>Notas:</strong> <?= htmlspecialchars($sesion['notas']) ?>
    </div>
  <?php endif; ?>

  <div class="signatures">
    <div class="sig-line"></div>
    Firma Cajero
    
    <div class="sig-line" style="margin-top: 25px;"></div>
    Firma Administrador
  </div>

</body>
</html>
