<!-- ==============================================================================
     VISTA: ESTADO DE VERIFICACIÓN DE CORREO (UI/UX PRO MAX)
     ============================================================================== -->

<div class="auth-card" style="max-width: 500px; text-align: center;">

  <?php if (($status ?? '') === 'success'): ?>
    <div style="width: 72px; height: 72px; border-radius: 50%; background: #dcfce7; color: #16a34a; font-size: 2.2rem; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; box-shadow: 0 10px 25px rgba(22, 163, 74, 0.2);">
      ✅
    </div>
    <h1 class="auth-title" style="color: var(--success); font-size: 1.6rem;">¡Cuenta Verificada!</h1>
    <p class="auth-subtitle" style="font-size: 0.95rem; margin-top: 0.5rem; line-height: 1.5;">
      <?= htmlspecialchars($message ?? 'Tu correo electrónico ha sido confirmado exitosamente.') ?>
    </p>

    <div style="margin-top: 2rem;">
      <a href="<?= APP_URL ?>/login" class="btn btn-primary btn-block btn-lg" style="width: 100%; font-weight: 800; font-size: 1rem; padding: 0.85rem;">
        <span>Ingresar al Sistema POS</span> <span>➔</span>
      </a>
    </div>

  <?php elseif (($status ?? '') === 'pending'): ?>
    <div style="width: 72px; height: 72px; border-radius: 50%; background: <?= !empty($mailSuccess) ? '#e0e7ff' : '#fee2e2' ?>; color: <?= !empty($mailSuccess) ? '#4f46e5' : '#dc2626' ?>; font-size: 2.2rem; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.25);">
      <?= !empty($mailSuccess) ? '✉️' : '⚠️' ?>
    </div>

    <h1 class="auth-title" style="font-size: 1.5rem;">
      <?= !empty($mailSuccess) ? 'Revisa tu Correo' : 'Error en Envío' ?>
    </h1>

    <p class="auth-subtitle" style="font-size: 0.95rem; margin-top: 0.5rem; line-height: 1.5;">
      <?= htmlspecialchars($message ?? '') ?><br>
      <strong style="color: var(--text-main); font-size: 1rem;"><?= htmlspecialchars($email ?? '') ?></strong>
    </p>

    <?php if (!empty($mailSuccess)): ?>
      <div style="background: #f8fafc; border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.15rem; margin: 1.5rem 0; font-size: 0.88rem; color: var(--text-muted); text-align: left;">
        <div style="font-weight: 700; color: var(--text-main); margin-bottom: 0.35rem;">💡 Próximos pasos:</div>
        1. Abre tu correo y revisa tu <strong>Bandeja de Entrada</strong> o <strong>Spam</strong>.<br>
        2. Haz clic en <strong>"Activar y Verificar Mi Cuenta"</strong> en el correo recibido.<br>
        3. El enlace es válido durante 24 horas.
      </div>
    <?php else: ?>
      <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: var(--radius-lg); padding: 1.15rem; margin: 1.5rem 0; font-size: 0.88rem; color: #991b1b; text-align: left;">
        <div style="font-weight: 700; margin-bottom: 0.35rem;">⚠️ No se pudo enviar el correo de activación:</div>
        <?= htmlspecialchars($mailMessage ?? 'El servidor de correo no respondió a la conexión.') ?><br><br>
        <span style="font-size: 0.82rem; color: #7f1d1d;">Verifica la conectividad SMTP en tu hosting o solicita al administrador del sistema la verificación manual de tu cuenta.</span>
      </div>
    <?php endif; ?>

    <div style="display: flex; gap: 0.75rem; justify-content: center; margin-top: 1rem; flex-wrap: wrap;">
      <?php if (!empty($email)): ?>
        <form action="<?= APP_URL ?>/reenviar-verificacion" method="POST" style="margin: 0;">
          <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
          <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
          <button type="submit" class="btn btn-primary" style="font-weight: 700; padding: 0.65rem 1.25rem;">
            Reintentar Envío ✉️
          </button>
        </form>
      <?php endif; ?>
      <a href="<?= APP_URL ?>/login" class="btn btn-outline" style="font-weight: 700;">
        Volver al Login
      </a>
    </div>

  <?php else: ?>
    <!-- Error o Expirado -->
    <div style="width: 72px; height: 72px; border-radius: 50%; background: #fee2e2; color: #dc2626; font-size: 2.2rem; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; box-shadow: 0 10px 25px rgba(220, 38, 38, 0.2);">
      ⚠️
    </div>
    <h1 class="auth-title" style="color: var(--danger); font-size: 1.5rem;"><?= htmlspecialchars($title ?? 'Enlace Inválido') ?></h1>
    <p class="auth-subtitle" style="font-size: 0.95rem; margin-top: 0.5rem; line-height: 1.5;">
      <?= htmlspecialchars($message ?? 'Ocurrió un inconveniente con la verificación de tu cuenta.') ?>
    </p>

    <?php if (!empty($email)): ?>
      <form action="<?= APP_URL ?>/reenviar-verificacion" method="POST" style="margin-top: 1.5rem;">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <button type="submit" class="btn btn-primary btn-block" style="width: 100%; font-weight: 700; padding: 0.75rem;">
          Reintentar Activación ✉️
        </button>
      </form>
    <?php endif; ?>

    <div style="margin-top: 1.5rem;">
      <a href="<?= APP_URL ?>/login" class="btn btn-outline">
        Volver al Inicio de Sesión
      </a>
    </div>

  <?php endif; ?>

</div>
