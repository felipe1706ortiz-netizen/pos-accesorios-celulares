<!-- ==============================================================================
     VISTA: RESTABLECER CONTRASEÑA (UI/UX PRO MAX)
     ============================================================================== -->

<div class="auth-card" style="max-width: 480px;">
  <div class="auth-header">
    <div class="auth-logo" style="background: linear-gradient(135deg, #4f46e5 0%, #2563eb 50%, #06b6d4 100%);">
      🔒
    </div>
    <h2 class="auth-title">Nueva Contraseña</h2>
    <p class="auth-subtitle">Ingresa tu nueva clave de acceso para continuar</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="toast toast-danger" style="margin-bottom: 1.5rem; background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; animation: none;">
      ⚠️ <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <?php if (($status ?? '') === 'invalid' || ($status ?? '') === 'expired'): ?>
    <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem; text-align: center;">
      <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">⚠️</div>
      <h3 style="font-size: 1.15rem; font-weight: 800; color: #991b1b; margin: 0 0 0.5rem 0;">
        <?= ($status === 'expired') ? 'Enlace Expirado' : 'Enlace No Válido' ?>
      </h3>
      <p style="font-size: 0.92rem; color: #b91c1c; line-height: 1.5; margin: 0 0 1rem 0;">
        <?= htmlspecialchars($message ?? 'Este enlace de restablecimiento ha expirado o ya fue utilizado.') ?>
      </p>
      <a href="<?= APP_URL ?>/recuperar-password" class="btn btn-primary btn-sm" style="font-weight: 700; text-decoration: none;">
        Solicitar Nuevo Enlace ✉️
      </a>
    </div>

    <div style="text-align: center; margin-top: 1rem;">
      <a href="<?= APP_URL ?>/login" class="btn btn-outline btn-block" style="width: 100%; font-weight: 700;">
        Volver al Login
      </a>
    </div>

  <?php elseif (($status ?? '') === 'success'): ?>
    <div style="background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem; text-align: center;">
      <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">✅</div>
      <h3 style="font-size: 1.15rem; font-weight: 800; color: #166534; margin: 0 0 0.5rem 0;">¡Contraseña Actualizada!</h3>
      <p style="font-size: 0.92rem; color: #15803d; line-height: 1.5; margin: 0 0 1rem 0;">
        <?= htmlspecialchars($message ?? 'Tu contraseña ha sido restablecida exitosamente.') ?>
      </p>
      <a href="<?= APP_URL ?>/login" class="btn btn-primary btn-block" style="width: 100%; font-weight: 800; padding: 0.85rem; text-decoration: none;">
        Iniciar Sesión Ahora ➔
      </a>
    </div>

  <?php else: ?>

    <form action="<?= APP_URL ?>/restablecer-password" method="POST" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="password" class="form-label" style="font-weight: 700;">Nueva Contraseña</label>
        <div style="position: relative; display: flex; align-items: center;">
          <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-control form-control-lg" 
            placeholder="Mínimo 6 caracteres" 
            required 
            autofocus
            style="padding-right: 2.75rem;"
          >
          <button 
            type="button" 
            onclick="togglePasswordVisibility('password', this)" 
            title="Mostrar / Ocultar"
            style="position: absolute; right: 0.75rem; background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem; padding: 0.25rem; display: flex; align-items: center; justify-content: center; outline: none;"
          >
            👁️
          </button>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label for="password_confirm" class="form-label" style="font-weight: 700;">Confirmar Nueva Contraseña</label>
        <div style="position: relative; display: flex; align-items: center;">
          <input 
            type="password" 
            id="password_confirm" 
            name="password_confirm" 
            class="form-control form-control-lg" 
            placeholder="Repite la nueva contraseña" 
            required
            style="padding-right: 2.75rem;"
          >
          <button 
            type="button" 
            onclick="togglePasswordVisibility('password_confirm', this)" 
            title="Mostrar / Ocultar"
            style="position: absolute; right: 0.75rem; background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.2rem; padding: 0.25rem; display: flex; align-items: center; justify-content: center; outline: none;"
          >
            👁️
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-lg btn-block" style="width: 100%; font-weight: 800; padding: 0.85rem;">
        <span>Guardar Nueva Contraseña</span>
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
      </button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
      <a href="<?= APP_URL ?>/login" style="color: var(--text-muted); text-decoration: none; font-weight: 600;">
        ← Volver al Inicio de Sesión
      </a>
    </div>

  <?php endif; ?>
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
