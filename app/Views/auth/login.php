<div>
  <div style="margin-bottom: 2rem;">
    <h1 class="auth-form-title">Iniciar Sesión</h1>
    <p class="auth-form-subtitle">Ingresa tus credenciales para acceder al sistema</p>
  </div>

  <?php if (!empty($error)): ?>
    <div style="margin-bottom: 1.25rem; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: var(--radius-md); padding: 0.75rem 1rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.6rem;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($unverifiedEmail)): ?>
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-md); padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.82rem;">
      <p style="margin: 0 0 0.5rem 0; color: #1e40af; font-weight: 500;">¿No recibiste el correo de activación?</p>
      <form action="<?= APP_URL ?>/reenviar-verificacion" method="POST" style="margin: 0;">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($unverifiedEmail) ?>">
        <button type="submit" class="btn btn-outline btn-sm" style="color: #2563eb; border-color: #bfdbfe;">
          Reenviar enlace de activación
        </button>
      </form>
    </div>
  <?php endif; ?>

  <form action="<?= APP_URL ?>/login" method="POST" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="form-group">
      <label for="usuario" class="form-label">Usuario o Correo Electrónico</label>
      <input 
        type="text" 
        id="usuario" 
        name="usuario" 
        class="form-control" 
        placeholder="admin o cajero" 
        value="<?= htmlspecialchars($oldUsuario ?? '') ?>"
        required 
        autofocus
      >
    </div>

    <div class="form-group">
      <label for="password" class="form-label">Contraseña</label>
      <div style="position: relative; display: flex; align-items: center;">
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control" 
          placeholder="••••••••" 
          required
          style="padding-right: 2.5rem;"
        >
        <button 
          type="button" 
          id="togglePasswordBtn" 
          onclick="togglePasswordVisibility('password', this)" 
          title="Mostrar / Ocultar Contraseña"
          style="position: absolute; right: 0.65rem; background: none; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; padding: 0.25rem; outline: none;"
        >
          <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
          </svg>
        </button>
      </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin: 0.65rem 0 1.25rem 0; font-size: 0.82rem;">
      <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; user-select: none; color: var(--text-secondary); font-weight: 500;">
        <input type="checkbox" name="recordarme" id="recordarme" value="1" <?= !empty($rememberedUsuario) ? 'checked' : '' ?> style="cursor: pointer; width: 14px; height: 14px; accent-color: var(--primary);">
        <span>Recordarme</span>
      </label>
      <a href="<?= APP_URL ?>/recuperar-password" style="color: var(--text-muted); text-decoration: none; font-weight: 500;">
        ¿Olvidaste tu contraseña?
      </a>
    </div>

    <button type="submit" class="btn btn-primary btn-block" style="padding: 0.65rem 1rem;">
      <span>Ingresar al Sistema</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
    </button>
  </form>

  <div style="text-align: center; margin-top: 2rem; font-size: 0.82rem; color: var(--text-muted);">
    ¿No tienes una cuenta de cajero? 
    <a href="<?= APP_URL ?>/registro" style="color: var(--accent); font-weight: 600; text-decoration: none;">Regístrate aquí</a>
  </div>
</div>

<script>
function togglePasswordVisibility(inputId, btn) {
  const input = document.getElementById(inputId);
  const eyeIcon = document.getElementById('eyeIcon');
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
    btn.title = 'Ocultar Contraseña';
    if (eyeIcon) {
      eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    }
  } else {
    input.type = 'password';
    btn.title = 'Mostrar Contraseña';
    if (eyeIcon) {
      eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
  }
}
</script>
