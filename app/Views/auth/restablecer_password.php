<!-- ==============================================================================
     VISTA: RESTABLECER CONTRASEÑA (Sober SaaS)
     ============================================================================== -->

<div class="auth-header">
  <h1 class="auth-title">Nueva Contraseña</h1>
  <p class="auth-subtitle">Ingresa tu nueva clave de acceso para continuar</p>
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

<?php if (($status ?? '') === 'invalid' || ($status ?? '') === 'expired'): ?>
  <div class="alert alert-danger" style="display: block; padding: 1.5rem; text-align: center; margin-bottom: 1.5rem;">
    <h3 style="font-size: 1.05rem; font-weight: 600; margin: 0 0 0.5rem 0; color: #991b1b;">
      <?= ($status === 'expired') ? 'Enlace Expirado' : 'Enlace No Válido' ?>
    </h3>
    <p style="font-size: 0.88rem; color: #b91c1c; line-height: 1.5; margin: 0 0 1.25rem 0;">
      <?= htmlspecialchars($message ?? 'Este enlace de restablecimiento ha expirado o ya fue utilizado.') ?>
    </p>
    <a href="<?= APP_URL ?>/recuperar-password" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.5rem 1rem; text-decoration: none;">
      Solicitar Nuevo Enlace
    </a>
  </div>

  <div style="text-align: center; margin-top: 1rem;">
    <a href="<?= APP_URL ?>/login" class="btn btn-outline" style="width: 100%;">
      Volver al Login
    </a>
  </div>

<?php elseif (($status ?? '') === 'success'): ?>
  <div class="alert alert-success" style="display: block; padding: 1.5rem; text-align: center; margin-bottom: 1.5rem;">
    <h3 style="font-size: 1.05rem; font-weight: 600; margin: 0 0 0.5rem 0; color: #166534;">¡Contraseña Actualizada!</h3>
    <p style="font-size: 0.88rem; color: #15803d; line-height: 1.5; margin: 0 0 1.25rem 0;">
      <?= htmlspecialchars($message ?? 'Tu contraseña ha sido restablecida exitosamente.') ?>
    </p>
    <a href="<?= APP_URL ?>/login" class="btn btn-primary" style="width: 100%; height: 42px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
      Iniciar Sesión
    </a>
  </div>

<?php else: ?>

  <form action="<?= APP_URL ?>/restablecer-password" method="POST" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

    <div class="form-group" style="margin-bottom: 1.25rem;">
      <label for="password" class="form-label">Nueva Contraseña</label>
      <div style="position: relative; display: flex; align-items: center;">
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control" 
          placeholder="Mínimo 6 caracteres" 
          required 
          autofocus
          style="padding-right: 2.75rem;"
        >
        <button 
          type="button" 
          onclick="togglePasswordVisibility('password', this)" 
          title="Mostrar / Ocultar"
          style="position: absolute; right: 0.75rem; background: none; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; outline: none; padding: 0.25rem;"
        >
          <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
        </button>
      </div>
    </div>

    <div class="form-group" style="margin-bottom: 1.5rem;">
      <label for="password_confirm" class="form-label">Confirmar Nueva Contraseña</label>
      <div style="position: relative; display: flex; align-items: center;">
        <input 
          type="password" 
          id="password_confirm" 
          name="password_confirm" 
          class="form-control" 
          placeholder="Repite la nueva contraseña" 
          required
          style="padding-right: 2.75rem;"
        >
        <button 
          type="button" 
          onclick="togglePasswordVisibility('password_confirm', this)" 
          title="Mostrar / Ocultar"
          style="position: absolute; right: 0.75rem; background: none; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; outline: none; padding: 0.25rem;"
        >
          <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
        </button>
      </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; height: 42px; font-weight: 600;">
      <span>Guardar Nueva Contraseña</span>
    </button>
  </form>

  <div style="text-align: center; margin-top: 1.5rem; font-size: 0.88rem;">
    <a href="<?= APP_URL ?>/login" style="color: var(--text-muted); text-decoration: none; font-weight: 500;">
      &larr; Volver al Inicio de Sesión
    </a>
  </div>

<?php endif; ?>

<script>
function togglePasswordVisibility(inputId, btn) {
  const input = document.getElementById(inputId);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
    btn.innerHTML = `<svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
  } else {
    input.type = 'password';
    btn.innerHTML = `<svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
  }
}
</script>
