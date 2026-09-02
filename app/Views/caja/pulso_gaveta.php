<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Apertura de Gaveta</title>
  <style>
    @page { margin: 0; size: 58mm auto; }
    body { margin: 0; padding: 0; font-family: monospace; font-size: 10px; }
  </style>
</head>
<body onload="window.print();">
  <!-- Pulso ESC/POS estándar de apertura de cajón monedero (ESC p 0 25 250) -->
  <span style="font-size: 1px; color: transparent;"><?= "\x1b\x70\x00\x19\xfa" ?></span>
</body>
</html>
