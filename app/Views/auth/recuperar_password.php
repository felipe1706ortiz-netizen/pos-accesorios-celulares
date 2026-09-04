<!-- ==============================================================================
     VISTA: RECUPERACIÓN DE CONTRASEÑA (UI/UX PRO MAX)
     ============================================================================== -->

<div class="auth-card" style="max-width: 480px;">
  <div class="auth-header">
    <div class="auth-logo" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);">
      🔑
    </div>
    <h2 class="auth-title">Recuperar Contraseña</h2>
    <p class="auth-subtitle">Ingresa tu correo electrónico para recibir un enlace de restablecimiento seguro</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="toast toast-danger" style="margin-bottom: 1.5rem; background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; animation: none;">
      ⚠️ <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div style="background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem; text-align: center;">
      <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">✉️</div>
      <h3 style="font-size: 1.15rem; font-weight: 800; color: #166534; margin: 0 0 0.5rem 0;">¡Enlace Enviado con Éxito!</h3>
      <p style="font-size: 0.92rem; color: #15803d; line-height: 1.5; margin: 0 0 1rem 0;">
        <?= htmlspecialchars($success) ?><br>
        <strong style="color: #14532d; font-size: 1rem;"><?= htmlspecialchars($email ?? '') ?></strong>
      </p>
      <div style="font-size: 0.82rem; color: #166534; background: #dcfce7; padding: 0.75rem; border-radius: var(--radius-md); text-align: left;">
        💡 <strong>Recomendación:</strong> Revisa tu <strong>Bandeja de Entrada</strong> o carpeta de <strong>Spam / No deseado</strong>. El enlace expirará en 2 horas.
      </div>
    </div>

    <div style="margin-top: 1.5rem;">
      <a href="<?= APP_URL ?>/login" class="btn btn-outline btn-block" style="width: 100%; font-weight: 700;">
        Volver al Inicio de Sesión
      </a>
    </div>

  <?php else: ?>

    <form action="<?= APP_URL ?>/recuperar-password" method="POST" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="email" class="form-label" style="font-weight: 700;">Correo Electrónico Registrado</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          class="form-control form-control-lg" 
          placeholder="ej: usuario@gmail.com" 
          value="<?= htmlspecialchars($oldEmail ?? '') ?>"
          required 
          autofocus
        >
      </div>

      <button type="submit" class="btn btn-primary btn-lg btn-block" style="width: 100%; font-weight: 800; padding: 0.85rem; margin-top: 0.75rem;">
        <span>Enviar Enlace de Recuperación</span>
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
      <a href="<?= APP_URL ?>/login" style="color: var(--primary); font-weight: 700; text-decoration: none;">
        ← Volver al Inicio de Sesión
      </a>
    </div>

  <?php endif; ?>
</div>
