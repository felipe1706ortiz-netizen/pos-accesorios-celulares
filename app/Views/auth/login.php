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
      <input 
        type="password" 
        id="password" 
        name="password" 
        class="form-control form-control-lg" 
        placeholder="••••••••" 
        required
      >
    </div>

    <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top: 1.25rem; width: 100%; font-weight: 800; padding: 0.85rem;">
      <span>Ingresar al Sistema</span>
      <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
    </button>
  </form>

  <div style="text-align: center; margin-top: 1.25rem; font-size: 0.9rem; color: var(--text-muted);">
    ¿No tienes una cuenta de cajero? 
    <a href="<?= APP_URL ?>/registro" style="color: var(--primary); font-weight: 800; text-decoration: none;">Regístrate aquí</a>
  </div>

  <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1.5px solid var(--border-color); font-size: 0.82rem; color: var(--text-muted); text-align: center; background: #f8fafc; border-radius: var(--radius-md); padding: 0.85rem;">
    <div style="font-weight: 700; margin-bottom: 0.25rem; color: var(--text-secondary);">Credenciales de Prueba Rápidas:</div>
    <div style="display: flex; justify-content: space-around; font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; margin-top: 0.35rem;">
      <span>👤 Admin: <strong>admin</strong> / <strong>Admin123*</strong></span>
    </div>
    <div style="display: flex; justify-content: space-around; font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; margin-top: 0.25rem;">
      <span>💼 Cajero: <strong>cajero</strong> / <strong>Cajero123*</strong></span>
    </div>
  </div>
</div>
