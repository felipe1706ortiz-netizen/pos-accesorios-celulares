<div class="auth-card">
  <div class="auth-header">
    <div class="auth-logo">📱</div>
    <h2 class="auth-title">Acceso al Sistema POS</h2>
    <p class="auth-subtitle">Ingrese sus credenciales para iniciar turno o administración</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="toast toast-danger" style="margin-bottom: 1.5rem; background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; animation: none;">
      ⚠️ <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($unverifiedEmail)): ?>
    <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: var(--radius-md); padding: 0.85rem; margin-bottom: 1.5rem; font-size: 0.85rem; text-align: center;">
      <p style="margin: 0 0 0.5rem 0; color: #1e40af; font-weight: 600;">¿No recibiste el correo de activación?</p>
      <form action="<?= APP_URL ?>/reenviar-verificacion" method="POST" style="margin: 0;">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($unverifiedEmail) ?>">
        <button type="submit" class="btn btn-outline btn-sm" style="font-size: 0.8rem; font-weight: 700; color: #2563eb; border-color: #93c5fd;">
          Reenviar enlace de activación ✉️
        </button>
      </form>
    </div>
  <?php endif; ?>

  <form action="<?= APP_URL ?>/login" method="POST" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="form-group">
      <label for="usuario" class="form-label" style="font-weight: 700;">Usuario o Correo Electrónico</label>
      <input 
        type="text" 
        id="usuario" 
        name="usuario" 
        class="form-control form-control-lg" 
        placeholder="ej: admin o cajero" 
        value="<?= htmlspecialchars($oldUsuario ?? '') ?>"
        required 
        autofocus
      >
    </div>

    <div class="form-group">
      <label for="password" class="form-label" style="font-weight: 700;">Contraseña</label>
      <div style="position: relative; display: flex; align-items: center;">
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control form-control-lg" 
          placeholder="••••••••" 
          required
          style="padding-right: 2.75rem;"
        >
        <button 
          type="button" 
          id="togglePasswordBtn" 
          onclick="togglePasswordVisibility('password', this)" 
          title="Mostrar / Ocultar Contraseña"
          style="position: absolute; right: 0.75rem; background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem; padding: 0.25rem; display: flex; align-items: center; justify-content: center; outline: none;"
        >
          👁️
        </button>
      </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin: 0.75rem 0 1.25rem 0; font-size: 0.88rem; flex-wrap: wrap; gap: 0.5rem;">
      <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; user-select: none; color: var(--text-secondary); font-weight: 600;">
        <input type="checkbox" name="recordarme" id="recordarme" value="1" <?= !empty($rememberedUsuario) ? 'checked' : '' ?> style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--primary);">
        <span>Recordarme</span>
      </label>
      <a href="<?= APP_URL ?>/recuperar-password" style="color: var(--primary); font-weight: 700; text-decoration: none;">
        ¿Olvidaste tu contraseña?
      </a>
    </div>

    <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top: 0.5rem; width: 100%; font-weight: 800; padding: 0.85rem;">
      <span>Ingresar al Sistema</span>
      <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
    </button>
  </form>

  <div style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
    ¿No tienes una cuenta de cajero? 
    <a href="<?= APP_URL ?>/registro" style="color: var(--primary); font-weight: 800; text-decoration: none;">Regístrate aquí</a>
  </div>
</div>

<script>
function togglePasswordVisibility(inputId, btn) {
  const input = document.getElementById(inputId);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
    btn.textContent = '🙈';
    btn.title = 'Ocultar Contraseña';
  } else {
    input.type = 'password';
    btn.textContent = '👁️';
    btn.title = 'Mostrar Contraseña';
  }
}
</script>
