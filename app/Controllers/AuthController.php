<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\UsuarioModel;
use App\Services\MailService;

/**
 * ==============================================================================
 * CONTROLADOR AUTHCONTROLLER
 * ==============================================================================
 * Gestiona el inicio de sesión, registro de nuevos usuarios, verificación
 * de correo electrónico con PHPMailer, control de roles y cierre de sesión.
 * ==============================================================================
 */
class AuthController extends Controller
{
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Muestra la pantalla de Login
     */
    public function showLogin(): void
    {
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                $this->redirect('/dashboard');
            } else {
                $sesionModel = new \App\Models\SesionCajaModel();
                $sesionActiva = $sesionModel->getSesionActiva(Auth::id());
                $this->redirect($sesionActiva ? '/pos' : '/caja/apertura');
            }
            return;
        }

        $this->render('auth/login', [
            'title' => 'Acceso al Sistema POS - ' . APP_NAME
        ], 'auth');
    }

    /**
     * Procesa la autenticación del usuario
     */
    public function login(): void
    {
        if (!$this->validateCsrf()) {
            $this->render('auth/login', [
                'title' => 'Acceso al Sistema POS',
                'error' => 'La sesión del formulario expiró. Por favor intente nuevamente.'
            ], 'auth');
            return;
        }

        $usuario = trim($this->getPost('usuario', ''));
        $password = $this->getPost('password', '');

        if (empty($usuario) || empty($password)) {
            $this->render('auth/login', [
                'title'      => 'Acceso al Sistema POS',
                'error'      => 'Por favor ingrese su usuario o correo y su contraseña.',
                'oldUsuario' => $usuario
            ], 'auth');
            return;
        }

        $user = $this->usuarioModel->findByLogin($usuario);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->render('auth/login', [
                'title'      => 'Acceso al Sistema POS',
                'error'      => 'Credenciales inválidas. Verifique su usuario y contraseña.',
                'oldUsuario' => $usuario
            ], 'auth');
            return;
        }

        // Validar si el correo electrónico ha sido verificado
        if (isset($user['email_verificado']) && (int)$user['email_verificado'] === 0) {
            $this->render('auth/login', [
                'title'          => 'Acceso al Sistema POS',
                'error'          => 'Tu cuenta aún no ha sido activada. Por favor revisa tu correo electrónico para verificarla.',
                'unverifiedEmail'=> $user['email'],
                'oldUsuario'     => $usuario
            ], 'auth');
            return;
        }

        // Iniciar sesión y actualizar último acceso
        Auth::login($user);
        $this->usuarioModel->updateLastLogin((int)$user['id']);

        if ($user['rol'] === 'admin') {
            $this->redirect('/dashboard');
        } else {
            $sesionModel = new \App\Models\SesionCajaModel();
            $sesionActiva = $sesionModel->getSesionActiva((int)$user['id']);
            if (!$sesionActiva) {
                $this->redirect('/caja/apertura');
            } else {
                $this->redirect('/pos');
            }
        }
    }

    /**
     * Muestra la pantalla de Registro de Nuevo Usuario
     */
    public function showRegistro(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
            return;
        }

        $this->render('auth/registro', [
            'title' => 'Crear Cuenta - ' . APP_NAME
        ], 'auth');
    }

    /**
     * Procesa el registro de un nuevo usuario y envía el correo de verificación
     */
    public function registro(): void
    {
        if (!$this->validateCsrf()) {
            $this->render('auth/registro', [
                'title' => 'Crear Cuenta',
                'error' => 'La sesión del formulario expiró. Por favor intente nuevamente.'
            ], 'auth');
            return;
        }

        $nombre = trim($this->getPost('nombre', ''));
        $usuario = strtolower(trim($this->getPost('usuario', '')));
        $email = strtolower(trim($this->getPost('email', '')));
        $password = $this->getPost('password', '');
        $passwordConfirm = $this->getPost('password_confirm', '');

        // 1. Validaciones de campos requeridos
        if (empty($nombre) || empty($usuario) || empty($email) || empty($password)) {
            $this->render('auth/registro', [
                'title' => 'Crear Cuenta',
                'error' => 'Todos los campos son obligatorios.',
                'old'   => ['nombre' => $nombre, 'usuario' => $usuario, 'email' => $email]
            ], 'auth');
            return;
        }

        // 2. Validación de formato de correo
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/registro', [
                'title' => 'Crear Cuenta',
                'error' => 'El formato de correo electrónico no es válido.',
                'old'   => ['nombre' => $nombre, 'usuario' => $usuario, 'email' => $email]
            ], 'auth');
            return;
        }

        // 3. Validación de contraseña
        if (strlen($password) < 6) {
            $this->render('auth/registro', [
                'title' => 'Crear Cuenta',
                'error' => 'La contraseña debe tener al menos 6 caracteres.',
                'old'   => ['nombre' => $nombre, 'usuario' => $usuario, 'email' => $email]
            ], 'auth');
            return;
        }

        if ($password !== $passwordConfirm) {
            $this->render('auth/registro', [
                'title' => 'Crear Cuenta',
                'error' => 'Las contraseñas ingresadas no coinciden.',
                'old'   => ['nombre' => $nombre, 'usuario' => $usuario, 'email' => $email]
            ], 'auth');
            return;
        }

        // 4. Validación de unicidad de usuario y correo
        if ($this->usuarioModel->findByUsername($usuario)) {
            $this->render('auth/registro', [
                'title' => 'Crear Cuenta',
                'error' => 'El nombre de usuario ya se encuentra registrado. Elija otro.',
                'old'   => ['nombre' => $nombre, 'usuario' => $usuario, 'email' => $email]
            ], 'auth');
            return;
        }

        if ($this->usuarioModel->findByEmail($email)) {
            $this->render('auth/registro', [
                'title' => 'Crear Cuenta',
                'error' => 'El correo electrónico ya está registrado con otra cuenta.',
                'old'   => ['nombre' => $nombre, 'usuario' => $usuario, 'email' => $email]
            ], 'auth');
            return;
        }

        // 5. Generar token de verificación de 64 caracteres
        $token = bin2hex(random_bytes(32));
        $tokenExpira = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // 6. Insertar usuario en la base de datos
        $nuevoId = $this->usuarioModel->insert([
            'nombre'             => $nombre,
            'usuario'            => $usuario,
            'email'              => $email,
            'password'           => $passwordHash,
            'rol'                => 'cajero',
            'estado'             => 1,
            'email_verificado'   => 0,
            'token_verificacion' => $token,
            'token_expira'       => $tokenExpira
        ]);

        // 7. Enviar correo de verificación con PHPMailer / Resend API
        $mailResult = MailService::sendVerificationEmail($email, $nombre, $token);

        $this->render('auth/verificar_email', [
            'title'       => $mailResult['success'] ? 'Verificación de Cuenta Enviada' : 'Activación de Cuenta',
            'status'      => 'pending',
            'email'       => $email,
            'nombre'      => $nombre,
            'mailSuccess' => $mailResult['success'],
            'mailMessage' => $mailResult['message'],
            'devLink'     => $mailResult['dev_link'] ?? null,
            'message'     => $mailResult['success'] 
                ? 'Hemos enviado un enlace de activación a tu correo. Por favor revisa tu bandeja de entrada o spam para activarla.'
                : 'El servidor en la nube no pudo enviar el correo directamente por SMTP (bloqueo de puertos en proveedores cloud gratuitos). Puedes activar tu cuenta directamente con el botón que aparece abajo:'
        ], 'auth');
    }

    /**
     * Verifica el token de activación recibido por correo
     */
    public function verificarEmail(): void
    {
        $token = trim($this->getQuery('token', ''));

        if (empty($token)) {
            $this->render('auth/verificar_email', [
                'title'   => 'Enlace Inválido',
                'status'  => 'error',
                'message' => 'El enlace de verificación no contiene un token válido o está incompleto.'
            ], 'auth');
            return;
        }

        $user = $this->usuarioModel->findByToken($token);

        if (!$user) {
            $this->render('auth/verificar_email', [
                'title'   => 'Token Inválido o Ya Utilizado',
                'status'  => 'error',
                'message' => 'Este enlace de activación no es válido o la cuenta ya fue verificada previamente.'
            ], 'auth');
            return;
        }

        // Verificar expiración del token
        if (!empty($user['token_expira']) && strtotime($user['token_expira']) < time()) {
            $this->render('auth/verificar_email', [
                'title'   => 'Enlace Expirado',
                'status'  => 'expired',
                'email'   => $user['email'],
                'message' => 'El enlace de verificación ha expirado (validez de 24 horas). Puedes solicitar uno nuevo.'
            ], 'auth');
            return;
        }

        // Marcar correo como verificado
        $this->usuarioModel->markEmailVerified((int)$user['id']);

        $this->render('auth/verificar_email', [
            'title'   => '¡Cuenta Activada con Éxito!',
            'status'  => 'success',
            'nombre'  => $user['nombre'],
            'message' => 'Tu correo ha sido confirmado correctamente. Ya puedes iniciar sesión con tus credenciales.'
        ], 'auth');
    }

    /**
     * Reenvía el correo de verificación
     */
    public function reenviarVerificacion(): void
    {
        $email = strtolower(trim($this->getPost('email', '')));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('danger', 'Ingrese un correo electrónico válido.');
            $this->redirect('/login');
            return;
        }

        $user = $this->usuarioModel->findByEmail($email);

        if (!$user) {
            Session::setFlash('danger', 'No se encontró ninguna cuenta asociada a ese correo.');
            $this->redirect('/login');
            return;
        }

        if ((int)$user['email_verificado'] === 1) {
            Session::setFlash('info', 'Tu cuenta ya está verificada. Puedes iniciar sesión normalmente.');
            $this->redirect('/login');
            return;
        }

        $token = bin2hex(random_bytes(32));
        $tokenExpira = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $this->usuarioModel->setVerificationToken((int)$user['id'], $token, $tokenExpira);

        $mailResult = MailService::sendVerificationEmail($user['email'], $user['nombre'], $token);

        $this->render('auth/verificar_email', [
            'title'       => $mailResult['success'] ? 'Correo Reenviado' : 'Activación de Cuenta',
            'status'      => 'pending',
            'email'       => $user['email'],
            'nombre'      => $user['nombre'],
            'mailSuccess' => $mailResult['success'],
            'mailMessage' => $mailResult['message'],
            'devLink'     => $mailResult['dev_link'] ?? null,
            'message'     => $mailResult['success'] 
                ? 'Se ha enviado un nuevo enlace de activación a tu correo electrónico.'
                : 'El servidor en la nube no pudo enviar el correo directamente por SMTP (bloqueo de puertos en proveedores cloud gratuitos). Puedes activar tu cuenta directamente con el botón que aparece abajo:'
        ], 'auth');
    }

    /**
     * Cierra la sesión activa y redirige al login
     */
    public function logout(): void
    {
        Auth::logout();
        Session::setFlash('info', 'Ha cerrado sesión correctamente.');
        $this->redirect('/login');
    }
}
