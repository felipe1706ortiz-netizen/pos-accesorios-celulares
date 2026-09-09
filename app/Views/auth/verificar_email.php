<!-- ==============================================================================
     VISTA: ESTADO DE VERIFICACIÓN DE CORREO (Sober SaaS)
     ============================================================================== -->

<?php if (($status ?? '') === 'success'): ?>
  <div class="auth-header" style="text-align: center;">
    <div style="width: 48px; height: 48px; border-radius: 50%; background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
      <svg style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
    </div>
    <h1 class="auth-title">Cuenta Verificada</h1>
    <p class="auth-subtitle" style="margin-top: 0.5rem; line-height: 1.5;">
      <?= htmlspecialchars($message ?? 'Tu correo electrónico ha sido confirmado exitosamente.') ?>
    </p>
  </div>

  <div style="margin-top: 2rem;">
    <a href="<?= APP_URL ?>/login" class="btn btn-primary" style="width: 100%; height: 42px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
      Ingresar al Sistema POS
    </a>
  </div>

<?php elseif (($status ?? '') === 'pending'): ?>
  <div class="auth-header" style="text-align: center;">
    <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--bg-hover); border: 1px solid var(--border-color); color: var(--text-main); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
      <svg style="width: 22px; height: 22px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
        <polyline points="22,6 12,13 2,6"></polyline>
      </svg>
    </div>

    <h1 class="auth-title">
      <?= !empty($mailSuccess) ? 'Revisa tu Correo' : 'Error en Envío' ?>
    </h1>

    <p class="auth-subtitle" style="margin-top: 0.5rem; line-height: 1.5;">
      <?= htmlspecialchars($message ?? '') ?><br>
      <strong style="color: var(--text-main); font-weight: 600;"><?= htmlspecialchars($email ?? '') ?></strong>
    </p>
  </div>

  <?php if (!empty($mailSuccess)): ?>
    <div class="alert alert-info" style="display: block; font-size: 0.88rem; line-height: 1.6; margin: 1.5rem 0;">
      <div style="font-weight: 600; margin-bottom: 0.5rem; color: #1e40af;">Próximos pasos:</div>
      1. Abre tu correo y revisa tu bandeja de entrada o carpeta de spam.<br>
      2. Haz clic en el enlace de confirmación recibido.<br>
      3. El enlace es válido durante 24 horas.
    </div>
  <?php else: ?>
    <div class="alert alert-danger" style="display: block; font-size: 0.88rem; line-height: 1.6; margin: 1.5rem 0;">
      <div style="font-weight: 600; margin-bottom: 0.5rem; color: #991b1b;">No se pudo enviar el correo de activación:</div>
      <?= htmlspecialchars($mailMessage ?? 'El servidor de correo no respondió a la conexión.') ?><br><br>
      <span style="font-size: 0.82rem;">Verifica la conectividad SMTP en tu hosting o solicita al administrador del sistema la verificación manual.</span>
    </div>
  <?php endif; ?>

  <div style="display: flex; gap: 0.75rem; justify-content: center; margin-top: 1rem; flex-wrap: wrap;">
    <?php if (!empty($email)): ?>
      <form action="<?= APP_URL ?>/reenviar-verificacion" method="POST" style="margin: 0;">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <button type="submit" class="btn btn-primary" style="font-size: 0.88rem; padding: 0.5rem 1.25rem;">
          Reintentar Envío
        </button>
      </form>
    <?php endif; ?>
    <a href="<?= APP_URL ?>/login" class="btn btn-outline" style="font-size: 0.88rem; padding: 0.5rem 1.25rem;">
      Volver al Login
    </a>
  </div>

<?php else: ?>
  <!-- Error o Expirado -->
  <div class="auth-header" style="text-align: center;">
    <div style="width: 48px; height: 48px; border-radius: 50%; background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
      <svg style="width: 22px; height: 22px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
    </div>
    <h1 class="auth-title"><?= htmlspecialchars($title ?? 'Enlace Inválido') ?></h1>
    <p class="auth-subtitle" style="margin-top: 0.5rem; line-height: 1.5;">
      <?= htmlspecialchars($message ?? 'Ocurrió un inconveniente con la verificación de tu cuenta.') ?>
    </p>
  </div>

  <?php if (!empty($email)): ?>
    <form action="<?= APP_URL ?>/reenviar-verificacion" method="POST" style="margin-top: 1.5rem;">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
      <button type="submit" class="btn btn-primary" style="width: 100%; height: 42px;">
        Reintentar Activación
      </button>
    </form>
  <?php endif; ?>

  <div style="margin-top: 1.5rem; text-align: center;">
    <a href="<?= APP_URL ?>/login" class="btn btn-outline" style="width: 100%;">
      Volver al Inicio de Sesión
    </a>
  </div>

<?php endif; ?>
