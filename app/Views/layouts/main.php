<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>
  
  <!-- CSS Principal -->
  <link rel="stylesheet" href="<?= CSS_URL ?>/style.css">
  <?php if (!empty($extraCss)): ?>
    <?php foreach ($extraCss as $css): ?>
      <link rel="stylesheet" href="<?= CSS_URL ?>/<?= $css ?>.css">
    <?php endforeach; ?>
  <?php endif; ?>
</head>
<body>

<div class="app-wrapper">
  <!-- SIDEBAR DE NAVEGACIÓN -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-brand-icon">📱</div>
      <div class="sidebar-brand-text">POS ACCESORIOS</div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-title">Punto de Venta</div>
      <a href="<?= APP_URL ?>/pos" class="nav-link <?= ($activeMenu ?? '') === 'pos' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <span>Nueva Venta (POS)</span>
      </a>

      <?php if (\App\Core\Auth::isAdmin()): ?>
      <div class="nav-section-title">Administración</div>
      <a href="<?= APP_URL ?>/dashboard" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
        <span>Dashboard</span>
      </a>

      <a href="<?= APP_URL ?>/inventario" class="nav-link <?= ($activeMenu ?? '') === 'inventario' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        <span>Inventario</span>
      </a>
      <?php endif; ?>

      <div class="nav-section-title">Operaciones</div>
      <a href="<?= APP_URL ?>/facturas" class="nav-link <?= ($activeMenu ?? '') === 'facturas' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Historial Facturas</span>
      </a>

      <a href="<?= APP_URL ?>/caja" class="nav-link <?= ($activeMenu ?? '') === 'caja' ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <span>Caja & Arqueo</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="user-avatar"><?= strtoupper(substr($currentUser['nombre'] ?? 'U', 0, 2)) ?></div>
        <div class="user-info">
          <div class="user-name"><?= htmlspecialchars($currentUser['nombre'] ?? 'Usuario') ?></div>
          <div class="user-role"><?= htmlspecialchars($currentUser['rol'] ?? 'Cajero') ?></div>
        </div>
      </div>
      <a href="<?= APP_URL ?>/logout" title="Cerrar Sesión" style="color: #94a3b8; text-decoration: none;">
        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      </a>
    </div>
  </aside>

  <!-- CONTENEDOR PRINCIPAL -->
  <div class="main-wrapper">
    <header class="topbar">
      <div class="topbar-left">
        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Abrir Menú">☰</button>
        <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Panel de Control') ?></h1>
      </div>
      <div class="topbar-right" style="display: flex; align-items: center; gap: 0.6rem;">
        <button type="button" class="btn btn-outline btn-sm" onclick="abrirModalEstadoGaveta()" title="Ver saldo y estado actual de caja" style="font-weight: 700; display: flex; align-items: center; gap: 0.35rem; color: var(--text-main); border-color: #cbd5e1; background: #fff;">
          💵 <span>Ver Caja</span>
        </button>
        <span class="badge badge-info" style="margin-left: 0.25rem;">🟢 En Línea</span>
      </div>
    </header>

    <main class="content-body">
      <!-- Mensajes Flash de Alerta -->
      <?php if (!empty($flashMessages)): ?>
        <?php foreach ($flashMessages as $type => $messages): ?>
          <?php foreach ($messages as $msg): ?>
            <div class="toast toast-<?= $type ?>" style="margin-bottom: 1rem; position: relative;">
              <?= htmlspecialchars($msg) ?>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- Contenido inyectado por la vista hija -->
      <?= $content ?? '' ?>

      <!-- FOOTER GLOBAL PERSISTENTE -->
      <footer class="app-main-footer" style="margin-top: 3rem; padding: 1.75rem 0 1rem 0; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.84rem; color: var(--text-muted);">
        Desarrollado por <strong style="color: var(--text-main); font-weight: 700;">Andres Felipe Ortiz Hurtatiz</strong> © <?= date('Y') ?> | Todos los derechos reservados
      </footer>
    </main>
  </div>
</div>

<!-- ==============================================================================
     MODAL GLOBAL: CONSULTA DE ESTADO DE GAVETA / DINERO EN CAJA
     ============================================================================== -->
<div class="modal-backdrop" id="modalEstadoGaveta">
  <div class="modal-dialog" style="max-width: 500px;">
    <div class="modal-header">
      <h3 style="font-size: 1.15rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
        <span>💵</span> <span>Estado de Gaveta (Caja)</span>
      </h3>
      <button type="button" onclick="closeModal('modalEstadoGaveta')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;">&times;</button>
    </div>
    
    <div class="modal-body" id="modalEstadoGavetaBody">
      <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
        Consultando saldo de la gaveta...
      </div>
    </div>

    <div class="modal-footer" style="justify-content: flex-end; gap: 0.5rem;">
      <a href="<?= APP_URL ?>/caja" class="btn btn-outline" style="font-weight: 700;">Ir al Módulo de Caja</a>
      <button type="button" class="btn btn-primary" onclick="closeModal('modalEstadoGaveta')">Cerrar</button>
    </div>
  </div>
</div>

<!-- IFRAME OCULTO GLOBAL PARA IMPRESIÓN Y PULSO DE GAVETA -->
<iframe id="printIframe" style="display:none; width:0; height:0; border:none;"></iframe>

<!-- Scripts Globales -->
<script>
  window.APP_URL = "<?= APP_URL ?>";
  window.CSRF_TOKEN = "<?= $csrfToken ?>";
</script>
<script src="<?= JS_URL ?>/main.js"></script>
<?php if (!empty($extraJs)): ?>
  <?php foreach ($extraJs as $js): ?>
    <script src="<?= JS_URL ?>/<?= $js ?>.js"></script>
  <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
