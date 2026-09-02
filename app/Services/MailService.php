<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * ==============================================================================
 * SERVICIO TRANSACCIONAL DE CORREO ELECTRÓNICO (MAILSERVICE)
 * ==============================================================================
 * Soporta envío mediante:
 * 1. Resend API (HTTPS Puerto 443 - 100% compatible con Render/Railway Free Tier)
 * 2. PHPMailer (SMTP Puerto 587/465 para local o servidores con puertos abiertos)
 * 3. Enlace directo de activación seguro en caso de bloqueo de puertos
 * ==============================================================================
 */
class MailService
{
    /**
     * Envía un correo de verificación de cuenta con token seguro
     * 
     * @param string $toEmail Correo destinatario
     * @param string $toName Nombre del usuario
     * @param string $token Token de verificación
     * @return array ['success' => bool, 'message' => string, 'dev_link' => string|null]
     */
    public static function sendVerificationEmail(string $toEmail, string $toName, string $token): array
    {
        $verificationUrl = APP_URL . '/verificar-email?token=' . urlencode($token);
        $appName = defined('APP_NAME') ? APP_NAME : 'POS Celulares & Accesorios';
        $subject = '🔐 Activa tu cuenta en ' . $appName;
        $htmlBody = self::buildVerificationTemplate($toName, $verificationUrl, $appName);
        $altBody = "Hola {$toName},\n\nGracias por registrarte en {$appName}.\nPara activar tu cuenta, ingresa al siguiente enlace:\n{$verificationUrl}\n\nEste enlace expira en 24 horas.\n\nDesarrollado por Andres Felipe Ortiz Hurtatiz.";

        // --------------------------------------------------------------------------
        // MÉTODO 1: RESEND API (HTTPS Puerto 443 - Nunca bloqueado por Render/Cloud)
        // --------------------------------------------------------------------------
        $resendApiKey = getenv('RESEND_API_KEY') ?: (defined('RESEND_API_KEY') ? RESEND_API_KEY : '');
        if (!empty($resendApiKey) && function_exists('curl_init')) {
            $fromAddress = getenv('MAIL_FROM_ADDRESS') ?: 'onboarding@resend.dev';
            $payload = [
                'from'    => $appName . ' <' . $fromAddress . '>',
                'to'      => [$toEmail],
                'subject' => $subject,
                'html'    => $htmlBody
            ];

            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . trim($resendApiKey),
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                return [
                    'success'  => true,
                    'message'  => 'Correo de activación enviado exitosamente a ' . htmlspecialchars($toEmail) . ' vía Resend API.',
                    'dev_link' => null
                ];
            } else {
                error_log("Error Resend API (HTTP {$httpCode}): " . $response . " | " . $curlError);
            }
        }

        // --------------------------------------------------------------------------
        // MÉTODO 2: SMTP VÍA PHPMAILER (Puerto 587 o 465)
        // --------------------------------------------------------------------------
        $smtpHost = getenv('SMTP_HOST') ?: (defined('SMTP_HOST') ? SMTP_HOST : '');
        $smtpUser = getenv('SMTP_USER') ?: (defined('SMTP_USER') ? SMTP_USER : '');
        $smtpPass = getenv('SMTP_PASS') ?: (defined('SMTP_PASS') ? SMTP_PASS : '');
        $smtpPort = (int)(getenv('SMTP_PORT') ?: (defined('SMTP_PORT') ? SMTP_PORT : 587));
        $smtpSecure = getenv('SMTP_SECURE') ?: (defined('SMTP_SECURE') ? SMTP_SECURE : 'tls');
        $fromAddress = getenv('MAIL_FROM_ADDRESS') ?: (defined('MAIL_FROM_ADDRESS') ? MAIL_FROM_ADDRESS : 'noreply@pos.local');
        $fromName = getenv('MAIL_FROM_NAME') ?: (defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : $appName);

        // Si no hay configuración SMTP configurada en el entorno
        if (empty($smtpHost) || empty($smtpUser)) {
            return [
                'success'  => false,
                'message'  => 'No hay un servidor de correo SMTP configurado en el servidor.',
                'dev_link' => $verificationUrl
            ];
        }

        try {
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = trim($smtpUser);
            $mail->Password   = str_replace(' ', '', trim($smtpPass));
            $mail->SMTPSecure = ($smtpSecure === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtpPort;
            $mail->Timeout    = 7;

            // Remitente y Destinatario
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($toEmail, $toName);

            // Contenido del Correo
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $altBody;

            $mail->send();

            return [
                'success'  => true,
                'message'  => 'Correo de activación enviado exitosamente a ' . htmlspecialchars($toEmail) . '.',
                'dev_link' => null
            ];
        } catch (Exception $e) {
            $errorDetalle = $mail->ErrorInfo ?: $e->getMessage();
            error_log("Error al enviar correo con PHPMailer: " . $errorDetalle);

            return [
                'success'  => false,
                'message'  => 'El servidor en la nube no pudo conectar al puerto SMTP (' . $errorDetalle . '). Los proveedores gratuitos como Render bloquean los puertos salientes 587 y 465.',
                'dev_link' => $verificationUrl
            ];
        }
    }

    /**
     * Construye la plantilla HTML del correo con diseño UI/UX Pro Max
     * 
     * @param string $name
     * @param string $url
     * @param string $appName
     * @return string
     */
    private static function buildVerificationTemplate(string $name, string $url, string $appName): string
    {
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifica tu Cuenta</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0b0f19; margin: 0; padding: 40px 15px; }
    .card { max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 18px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.3); border: 1px solid #e2e8f0; }
    .header { background: linear-gradient(135deg, #1e1b4b 0%, #4f46e5 50%, #10b981 100%); padding: 36px 30px; text-align: center; color: #ffffff; }
    .logo-icon { font-size: 40px; margin-bottom: 10px; display: inline-block; }
    .header h1 { margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.02em; }
    .body { padding: 36px 32px; color: #334155; line-height: 1.6; }
    .body h2 { font-size: 18px; font-weight: 700; color: #0f172a; margin-top: 0; }
    .btn-container { text-align: center; margin: 32px 0; }
    .btn-verify { display: inline-block; background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #ffffff !important; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-weight: 800; font-size: 15px; box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4); }
    .url-fallback { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; word-break: break-all; font-family: monospace; font-size: 12px; color: #64748b; margin-top: 20px; }
    .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <div class="logo-icon">📱</div>
      <h1>{$appName}</h1>
    </div>
    <div class="body">
      <h2>¡Hola, {$name}! 👋</h2>
      <p>Gracias por registrarte en el sistema de Punto de Venta y Gestión de Accesorios. Para activar tu cuenta y comenzar a operar, por favor confirma tu correo electrónico haciendo clic en el siguiente botón:</p>
      
      <div class="btn-container">
        <a href="{$url}" class="btn-verify" target="_blank">✅ Activar y Verificar Mi Cuenta</a>
      </div>

      <p style="font-size: 13px; color: #64748b;">Este enlace de seguridad es válido durante las próximas <strong>24 horas</strong>. Si no creaste esta cuenta, puedes ignorar este correo con total tranquilidad.</p>

      <div class="url-fallback">
        Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
        <a href="{$url}" style="color: #4f46e5;">{$url}</a>
      </div>
    </div>
    <div class="footer">
      Desarrollado por <strong>Andres Felipe Ortiz Hurtatiz</strong> © {$year} | Todos los derechos reservados
    </div>
  </div>
</body>
</html>
HTML;
    }
}
