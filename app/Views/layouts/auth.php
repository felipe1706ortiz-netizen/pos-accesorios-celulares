<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Acceso - ' . APP_NAME) ?></title>
  
  <link rel="stylesheet" href="<?= CSS_URL ?>/style.css">
  <style>
    .auth-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at 10% 20%, #1e1b4b 0%, #0b0f19 90%);
      padding: 1.5rem;
      position: relative;
      overflow: hidden;
    }
    .auth-container::before {
      content: '';
      position: absolute;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
      top: -100px;
      left: -100px;
      pointer-events: none;
    }
    .auth-container::after {
      content: '';
      position: absolute;
      width: 450px;
      height: 450px;
      background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
      bottom: -100px;
      right: -100px;
      pointer-events: none;
    }
    .auth-card {
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      width: 100%;
      max-width: 440px;
      border-radius: var(--radius-xl);
      padding: 2.75rem 2.5rem;
      box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.8);
      position: relative;
      z-index: 10;
    }
    .auth-header {
      text-align: center;
      margin-bottom: 2rem;
    }
    .auth-logo {
      width: 64px;
      height: 64px;
      border-radius: var(--radius-lg);
      background: linear-gradient(135deg, #6366f1 0%, #3b82f6 50%, #10b981 100%);
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin-bottom: 1rem;
      box-shadow: 0 8px 24px rgba(99, 102, 241, 0.35);
    }
    .auth-title {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.02em;
    }
    .auth-subtitle {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin-top: 0.35rem;
    }
  </style>
</head>
<body>
  <div class="auth-container">
    <?= $content ?? '' ?>
  </div>
</body>
</html>
