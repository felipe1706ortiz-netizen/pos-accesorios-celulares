<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 - Página no encontrada</title>
  <link rel="stylesheet" href="<?= CSS_URL ?>/style.css">
  <style>
    .error-page {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 2rem;
      background: var(--bg-body);
    }
    .error-code {
      font-size: 6rem;
      font-weight: 900;
      color: var(--primary);
      line-height: 1;
      font-family: 'JetBrains Mono', monospace;
    }
    .error-desc {
      font-size: 1.25rem;
      color: var(--text-muted);
      margin: 1rem 0 2rem;
      max-width: 450px;
    }
  </style>
</head>
<body>
  <div class="error-page">
    <div class="error-code">404</div>
    <h2 style="font-size: 1.75rem; font-weight: 800;">Página no encontrada</h2>
    <p class="error-desc">La ruta a la que intentas acceder no existe en el sistema o ha sido movida.</p>
    <a href="<?= APP_URL ?>" class="btn btn-primary btn-lg">Volver al Inicio</a>
  </div>
</body>
</html>
