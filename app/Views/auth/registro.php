<!-- ==============================================================================
     VISTA: REGISTRO DE NUEVO USUARIO (Sober SaaS)
     ============================================================================== -->

<div class="auth-header">
  <h1 class="auth-title">Crear Cuenta</h1>
  <p class="auth-subtitle">Regístrate para acceder al punto de venta y administración</p>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
    <svg style="width: 16px; height: 16px; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="12" cy="12" r="10"></circle>
      <line x1="12" y1="8" x2="12" y2="12"></line>
      <line x1="12" y1="16" x2="12.01" y2="16"></line>
    </svg>
    <div><?= htmlspecialchars($error) ?></div>
  </div>
<?php endif; ?>

<form action="<?= APP_URL ?>/registro" method="POST" autocomplete="off">
  <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

  <div class="form-group" style="margin-bottom: 1rem;">
    <label class="form-label" for="nombre">Nombre Completo</label>
    <input 
      type="text" 
      id="nombre" 
      name="nombre" 
      class="form-control" 
      placeholder="Carlos Mendoza" 
      value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
      required 
      autofocus
    >
  </div>

  <div class="form-group" style="margin-bottom: 1rem;">
    <label class="form-label" for="usuario">Nombre de Usuario</label>
    <input 
      type="text" 
      id="usuario" 
      name="usuario" 
      class="form-control" 
      placeholder="cmendoza" 
      value="<?= htmlspecialchars($old['usuario'] ?? '') ?>"
      required
    >
  </div>

  <div class="form-group" style="margin-bottom: 1rem;">
    <label class="form-label" for="email">Correo Electrónico</label>
    <input 
      type="email" 
      id="email" 
      name="email" 
      class="form-control" 
      placeholder="usuario@correo.com" 
      value="<?= htmlspecialchars($old['email'] ?? '') ?>"
      required
    >
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; margin-bottom: 1.25rem;">
    <div class="form-group" style="margin-bottom: 0;">
      <label class="form-label" for="password">Contraseña</label>
      <input 
        type="password" 
        id="password" 
        name="password" 
        class="form-control" 
        placeholder="Mín. 6 caracteres" 
        required
      >
    </div>

    <div class="form-group" style="margin-bottom: 0;">
      <label class="form-label" for="password_confirm">Confirmar</label>
      <input 
        type="password" 
        id="password_confirm" 
        name="password_confirm" 
        class="form-control" 
        placeholder="Repite la contraseña" 
        required
      >
    </div>
  </div>

  <button type="submit" class="btn btn-primary" style="width: 100%; height: 42px; font-weight: 600;">
    <span>Registrar y Validar Correo</span>
  </button>
</form>

<div style="text-align: center; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); font-size: 0.88rem; color: var(--text-muted);">
  ¿Ya tienes una cuenta activa? 
  <a href="<?= APP_URL ?>/login" style="color: var(--text-main); font-weight: 600; text-decoration: underline;">Iniciar Sesión</a>
</div>
