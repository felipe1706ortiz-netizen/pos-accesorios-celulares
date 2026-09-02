<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\UsuarioModel;

/**
 * ==============================================================================
 * CONTROLADOR AUTHCONTROLLER
 * ==============================================================================
 * Gestiona el inicio de sesión, validación de contraseñas con bcrypt,
 * control de sesiones y redirección según el rol asignado (Admin / Cajero).
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
        // Si el usuario ya está autenticado, redirigir según su rol y estado de caja
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
        // 1. Validar Token CSRF
        if (!$this->validateCsrf()) {
            $this->render('auth/login', [
                'title' => 'Acceso al Sistema POS',
                'error' => 'La sesión del formulario expiró. Por favor intente nuevamente.'
            ], 'auth');
            return;
        }

        // 2. Extraer y sanitizar credenciales
        $usuario = $this->getPost('usuario');
        $password = $this->getPost('password');

        if (empty($usuario) || empty($password)) {
            $this->render('auth/login', [
                'title'      => 'Acceso al Sistema POS',
                'error'      => 'Por favor ingrese su usuario y contraseña.',
                'oldUsuario' => $usuario
            ], 'auth');
            return;
        }

        // 3. Buscar usuario en base de datos
        $user = $this->usuarioModel->findByLogin($usuario);

        // 4. Validar contraseña con password_verify
        if (!$user || !password_verify($password, $user['password'])) {
            $this->render('auth/login', [
                'title'      => 'Acceso al Sistema POS',
                'error'      => 'Credenciales inválidas. Verifique su usuario y contraseña.',
                'oldUsuario' => $usuario
            ], 'auth');
            return;
        }

        // 5. Iniciar sesión y actualizar último acceso
        Auth::login($user);
        $this->usuarioModel->updateLastLogin((int)$user['id']);

        // 6. Redirigir según el rol del usuario
        if ($user['rol'] === 'admin') {
            $this->redirect('/dashboard');
        } else {
            // Cajero: Si no tiene turno de caja abierto, llevarlo primero a la apertura
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
     * Cierra la sesión activa y redirige al login
     */
    public function logout(): void
    {
        Auth::logout();
        Session::setFlash('info', 'Ha cerrado sesión correctamente.');
        $this->redirect('/login');
    }
}
