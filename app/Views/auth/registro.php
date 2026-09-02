<!-- ==============================================================================
     VISTA: REGISTRO DE NUEVO USUARIO (UI/UX PRO MAX)
     ============================================================================== -->

<div class="auth-card">
  <div class="auth-header">
    <div class="auth-logo">📱</div>
    <h1 class="auth-title">Crear Nueva Cuenta</h1>
    <p class="auth-subtitle">Regístrate para acceder al Punto de Venta y Gestión</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem; font-size: 0.88rem; display: flex; align-items: center; gap: 0.5rem;">
      <span>⚠️</span>
      <div><?= htmlspecialchars($error) ?></div>
    </div>
  <?php endif; ?>

  <form action="<?= APP_URL ?>/registro" method="POST" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="form-group">
      <label class="form-label" for="nombre" style="font-weight: 700;">Nombre Completo *</label>
      <input 
        type="text" 
        id="nombre" 
        name="nombre" 
        class="form-control form-control-lg" 
        placeholder="ej: Carlos Mendoza" 
        value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
        required 
        autofocus
      >
    </div>

    <div class="form-group">
      <label class="form-label" for="usuario" style="font-weight: 700;">Nombre de Usuario *</label>
      <input 
        type="text" 
        id="usuario" 
        name="usuario" 
        class="form-control form-control-lg" 
        placeholder="ej: cmendoza" 
        value="<?= htmlspecialchars($old['usuario'] ?? '') ?>"
        required
      >
    </div>

    <div class="form-group">
      <label class="form-label" for="email" style="font-weight: 700;">Correo Electrónico *</label>
      <input 
        type="email" 
        id="email" 
        name="email" 
        class="form-control form-control-lg" 
        placeholder="ej: usuario@correo.com" 
        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
        required
      >
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
      <div class="form-group">
        <label class="form-label" for="password" style="font-weight: 700;">Contraseña *</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control form-control-lg" 
          placeholder="Mín. 6 carácteres" 
          required
        >
      </div>

      <div class="form-group">
        <label class="form-label" for="password_confirm" style="font-weight: 700;">Confirmar *</label>
        <input 
          type="password" 
          id="password_confirm" 
          name="password_confirm" 
          class="form-control form-control-lg" 
          placeholder="Repite la clave" 
          required
        >
      </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 1rem; width: 100%; font-weight: 800; font-size: 1.05rem; padding: 0.85rem;">
      <span>Crear Cuenta y Validar Correo</span> <span>✉️</span>
    </button>
  </form>

  <div style="text-align: center; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); font-size: 0.9rem; color: var(--text-muted);">
    ¿Ya tienes una cuenta activa? 
    <a href="<?= APP_URL ?>/login" style="color: var(--primary); font-weight: 800; text-decoration: none;">Iniciar Sesión</a>
  </div>
</div>
