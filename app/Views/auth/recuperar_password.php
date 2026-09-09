<div>
  <div style="margin-bottom: 2rem;">
    <h1 class="auth-form-title">Recuperar Contraseña</h1>
    <p class="auth-form-subtitle">Ingresa tu correo para recibir un enlace de restablecimiento seguro</p>
  </div>

  <?php if (!empty($error)): ?>
    <div style="margin-bottom: 1.25rem; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: var(--radius-md); padding: 0.75rem 1rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.6rem;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
      <span><?= htmlspecialchars($error) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.5rem; text-align: center;">
      <h3 style="font-size: 1rem; font-weight: 600; color: #166534; margin: 0 0 0.5rem 0;">Enlace Enviado</h3>
      <p style="font-size: 0.88rem; color: #15803d; line-height: 1.5; margin: 0 0 0.75rem 0;">
        <?= htmlspecialchars($success) ?><br>
        <strong><?= htmlspecialchars($email ?? '') ?></strong>
      </p>
      <div style="font-size: 0.8rem; color: #166534; background: #dcfce7; padding: 0.6rem 0.85rem; border-radius: var(--radius-sm); text-align: left;">
        Revisa tu <strong>Bandeja de Entrada</strong> o carpeta de <strong>Spam</strong>. El enlace expira en 2 horas.
      </div>
    </div>

    <div>
      <a href="<?= APP_URL ?>/login" class="btn btn-outline btn-block">
        Volver al Inicio de Sesión
      </a>
    </div>

  <?php else: ?>

    <form action="<?= APP_URL ?>/recuperar-password" method="POST" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

      <div class="form-group">
        <label for="email" class="form-label">Correo Electrónico Registrado</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          class="form-control" 
          placeholder="usuario@correo.com" 
          value="<?= htmlspecialchars($oldEmail ?? '') ?>"
          required 
          autofocus
        >
      </div>

      <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem; padding: 0.65rem 1rem;">
        <span>Enviar Enlace</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </button>
    </form>

    <div style="text-align: center; margin-top: 1.75rem; font-size: 0.82rem;">
      <a href="<?= APP_URL ?>/login" style="color: var(--text-muted); text-decoration: none; font-weight: 500;">
        &larr; Volver a Iniciar Sesión
      </a>
    </div>

  <?php endif; ?>
</div>
