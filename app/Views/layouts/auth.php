<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Acceso - ' . APP_NAME) ?></title>
  <link rel="stylesheet" href="<?= CSS_URL ?>/style.css">
</head>
<body class="auth-body">
  <div class="auth-split">
    <!-- COLUMNA IZQUIERDA: BRANDING & RESUMEN CORPORATIVO -->
    <aside class="auth-split-aside">
      <div class="auth-aside-content">
        <div class="auth-brand">
          <div class="auth-brand-logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
              <line x1="12" y1="18" x2="12.01" y2="18"></line>
            </svg>
          </div>
          <span class="auth-brand-name">POS ACCESORIOS</span>
        </div>

        <div class="auth-aside-hero">
          <h2 class="auth-hero-title">Sistema de Gestión & Punto de Venta</h2>
          <p class="auth-hero-desc">Control integral de inventario, facturación ágil en mostrador, arqueos de caja y seguimiento de pedidos para tiendas de telefonía y accesorios.</p>

          <div class="auth-features-list">
            <div class="auth-feature-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Facturación rápida con lector de código de barras y atajos</span>
            </div>
            <div class="auth-feature-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Control de turnos de caja, gaveta de dinero y balance</span>
            </div>
            <div class="auth-feature-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Seguimiento de encargos y abonos con comprobantes térmicos</span>
            </div>
          </div>
        </div>

        <div class="auth-aside-footer">
          Desarrollado por <strong>Andres Felipe Ortiz Hurtatiz</strong> &copy; <?= date('Y') ?> | Todos los derechos reservados
        </div>
      </div>
    </aside>

    <!-- COLUMNA DERECHA: FORMULARIO DIRECTO SIN TARJETA -->
    <main class="auth-split-main">
      <div class="auth-form-container">
        <?= $content ?? '' ?>
      </div>
    </main>
  </div>
</body>
</html>
