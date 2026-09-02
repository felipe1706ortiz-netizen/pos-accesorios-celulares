<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\SesionCajaModel;
use App\Models\MovimientoCajaModel;
use App\Models\ConfiguracionModel;
use Exception;

/**
 * ==============================================================================
 * CONTROLADOR CAJACONTROLLER (MÓDULOS 5 Y 6: CAJA, MOVIMIENTOS Y ARQUEO)
 * ==============================================================================
 * Apertura de turno, registro de entradas/salidas de efectivo, arqueo diario,
 * conciliación de saldos y ticket de cierre de caja.
 * ==============================================================================
 */
class CajaController extends Controller
{
    private SesionCajaModel $sesionModel;
    private MovimientoCajaModel $movimientoModel;
    private ConfiguracionModel $configModel;

    public function __construct()
    {
        Auth::requireAuth();
        $this->sesionModel = new SesionCajaModel();
        $this->movimientoModel = new MovimientoCajaModel();
        $this->configModel = new ConfiguracionModel();
    }

    /**
     * Panel principal de control de caja y estado del turno activo
     */
    public function index(): void
    {
        $usuarioId = Auth::id();
        $sesionActiva = $this->sesionModel->getSesionActiva($usuarioId);

        // Si es admin y no tiene sesión personal abierta, verificar si hay alguna otra abierta
        if (!$sesionActiva && Auth::isAdmin()) {
            $sesionActiva = $this->sesionModel->getCualquierSesionAbierta();
        }

        $movimientos = [];
        if ($sesionActiva) {
            $movimientos = $this->movimientoModel->getPorSesion((int)$sesionActiva['id']);
        }

        $historialSesiones = $this->sesionModel->getHistorial(10);
        $config = $this->configModel->getMapaConfiguracion();

        $this->render('caja/index', [
            'title'             => 'Control de Caja & Turno - ' . APP_NAME,
            'pageTitle'         => 'Gestión de Caja y Movimientos',
            'activeMenu'        => 'caja',
            'sesion'            => $sesionActiva,
            'movimientos'       => $movimientos,
            'historialSesiones' => $historialSesiones,
            'config'            => $config,
            'extraJs'           => ['caja']
        ], 'main');
    }

    /**
     * Muestra la pantalla o modal para apertura de turno
     */
    public function apertura(): void
    {
        $usuarioId = Auth::id();
        $sesion = $this->sesionModel->getSesionActiva($usuarioId);

        if ($sesion) {
            Session::setFlash('info', "Ya cuenta con una sesión de caja abierta (Turno #{$sesion['id']}).");
            $this->redirect('/caja');
            return;
        }

        $this->render('caja/apertura', [
            'title'      => 'Apertura de Caja - ' . APP_NAME,
            'pageTitle'  => 'Apertura de Turno de Caja',
            'activeMenu' => 'caja'
        ], 'main');
    }

    /**
     * Procesa la apertura de una nueva sesión de caja
     */
    public function abrir(): void
    {
        if (!$this->validateCsrf()) {
            Session::setFlash('danger', 'Error de seguridad CSRF.');
            $this->redirect('/caja');
            return;
        }

        $montoInicial = (float)$this->getPost('monto_inicial', 0);
        $notas = $this->getPost('notas', '');

        if ($montoInicial < 0) {
            Session::setFlash('danger', 'El monto inicial no puede ser negativo.');
            $this->redirect('/caja/apertura');
            return;
        }

        try {
            $usuarioId = Auth::id();
            $sesionId = $this->sesionModel->abrirSesion($usuarioId, $montoInicial, $notas);

            Session::setFlash('success', "Turno de caja #{$sesionId} abierto correctamente con una base de $ " . number_format($montoInicial, 0, ',', '.'));
            $this->redirect(Auth::isAdmin() ? '/caja' : '/pos');
        } catch (Exception $e) {
            Session::setFlash('danger', "Error al abrir caja: " . $e->getMessage());
            $this->redirect('/caja/apertura');
        }
    }

    /**
     * Vista de registro y listado de movimientos de caja (Entradas y Salidas)
     */
    public function movimientos(): void
    {
        $usuarioId = Auth::id();
        $sesionActiva = $this->sesionModel->getSesionActiva($usuarioId);

        $fecha = $this->getQuery('fecha', date('Y-m-d'));
        $tipo = $this->getQuery('tipo', '');

        $movimientos = $this->movimientoModel->getHistorial($fecha ?: null, $tipo ?: null, 150);

        $this->render('caja/movimientos', [
            'title'        => 'Movimientos de Efectivo - ' . APP_NAME,
            'pageTitle'    => 'Ingresos y Egresos de Caja',
            'activeMenu'   => 'caja',
            'sesion'       => $sesionActiva,
            'movimientos'  => $movimientos,
            'fecha'        => $fecha,
            'tipo'         => $tipo,
            'extraJs'      => ['caja']
        ], 'main');
    }

    /**
     * Guarda un movimiento de efectivo (Entrada o Salida)
     */
    public function guardarMovimiento(): void
    {
        $isAjax = $this->isAjax();
        $data = $isAjax ? ($this->getJsonBody() ?: $this->getPost()) : $this->getPost();

        $usuarioId = Auth::id();
        $sesion = $this->sesionModel->getSesionActiva($usuarioId);

        if (!$sesion && Auth::isAdmin()) {
            $sesion = $this->sesionModel->getCualquierSesionAbierta();
        }

        if (!$sesion) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => 'No hay una sesión de caja abierta para registrar movimientos.'], 400);
            }
            Session::setFlash('danger', 'Debe abrir una sesión de caja antes de registrar movimientos.');
            $this->redirect('/caja');
            return;
        }

        $tipo = strtoupper(trim($data['tipo'] ?? ''));
        $monto = (float)($data['monto'] ?? 0);
        $concepto = trim($data['concepto'] ?? '');
        $comprobante = trim($data['comprobante'] ?? '');

        if (!in_array($tipo, ['ENTRADA', 'SALIDA']) || $monto <= 0 || empty($concepto)) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => 'Complete todos los campos con un monto válido.'], 400);
            }
            Session::setFlash('danger', 'Datos del movimiento inválidos.');
            $this->redirect('/caja');
            return;
        }

        try {
            $movId = $this->movimientoModel->registrar(
                (int)$sesion['id'],
                $usuarioId,
                $tipo,
                $monto,
                $concepto,
                $comprobante
            );

            // Obtener saldo actualizado
            $sesionActualizada = $this->sesionModel->findById((int)$sesion['id']);

            if ($isAjax) {
                $this->jsonResponse([
                    'success'               => true,
                    'message'               => "Movimiento de {$tipo} por $ " . number_format($monto, 0, ',', '.') . " registrado con éxito.",
                    'movimiento_id'         => $movId,
                    'nuevo_monto_esperado'  => (float)$sesionActualizada['monto_esperado']
                ]);
            }

            Session::setFlash('success', "Movimiento de {$tipo} por $ " . number_format($monto, 0, ',', '.') . " registrado exitosamente.");
        } catch (Exception $e) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
            }
            Session::setFlash('danger', "Error al registrar movimiento: " . $e->getMessage());
        }

        $this->redirect('/caja');
    }

    /**
     * Muestra la pantalla de Cierre de Caja y Arqueo
     */
    public function cierre(): void
    {
        $usuarioId = Auth::id();
        $sesion = $this->sesionModel->getSesionActiva($usuarioId);

        if (!$sesion && Auth::isAdmin()) {
            $sesion = $this->sesionModel->getCualquierSesionAbierta();
        }

        if (!$sesion) {
            Session::setFlash('warning', 'No hay ninguna sesión de caja abierta actualmente para cerrar.');
            $this->redirect('/caja');
            return;
        }

        $movimientos = $this->movimientoModel->getPorSesion((int)$sesion['id']);
        $config = $this->configModel->getMapaConfiguracion();

        $this->render('caja/cierre', [
            'title'       => 'Cierre de Caja & Arqueo - ' . APP_NAME,
            'pageTitle'   => "Cierre del Turno #{$sesion['id']}",
            'activeMenu'  => 'caja',
            'sesion'      => $sesion,
            'movimientos' => $movimientos,
            'config'      => $config,
            'isAdmin'     => Auth::isAdmin(),
            'extraJs'     => ['caja']
        ], 'main');
    }

    /**
     * Procesa el Cierre formal de la sesión de caja
     */
    public function cerrar(): void
    {
        if (!$this->validateCsrf()) {
            Session::setFlash('danger', 'Error de seguridad CSRF.');
            $this->redirect('/caja');
            return;
        }

        $sesionId = (int)$this->getPost('sesion_id');
        $montoReal = (float)$this->getPost('monto_real', 0);
        $notas = $this->getPost('notas', '');

        try {
            $resultado = $this->sesionModel->cerrarSesion($sesionId, $montoReal, $notas);

            $diferencia = (float)$resultado['diferencia'];
            $difTexto = ($diferencia === 0.0) 
                ? "Sin diferencias (Cuadre perfecto)" 
                : (($diferencia > 0) ? "Sobrante de $ " . number_format($diferencia, 0, ',', '.') : "Faltante de $ " . number_format(abs($diferencia), 0, ',', '.'));

            Session::setFlash('success', "Caja #{$sesionId} cerrada correctamente. Balance: {$difTexto}.");
            $this->redirect('/caja/ticket/' . $sesionId);
        } catch (Exception $e) {
            Session::setFlash('danger', "Error al cerrar la caja: " . $e->getMessage());
            $this->redirect('/caja/cierre');
        }
    }

    /**
     * Genera el ticket térmico de resumen del cierre de caja (Arqueo ESC/POS)
     * 
     * @param int|string $id
     */
    public function ticketCierre($id): void
    {
        $id = (int)$id;
        $sesion = $this->sesionModel->getSesionCompleta($id);

        if (!$sesion) {
            $this->renderError("La sesión de caja #{$id} no existe.", 404);
            return;
        }

        $config = $this->configModel->getMapaConfiguracion();

        $this->render('caja/ticket_cierre', [
            'sesion' => $sesion,
            'config' => $config
        ], null); // Renderizado directo sin layout maestro para impresión térmica de arqueo
    }

    /**
     * Historial de turnos de caja pasados para auditoría del administrador
     */
    public function historial(): void
    {
        Auth::requireAdmin();
        $historial = $this->sesionModel->getHistorial(100);

        $this->render('caja/historial', [
            'title'      => 'Historial de Cierres de Caja - ' . APP_NAME,
            'pageTitle'  => 'Auditoría de Turnos y Arqueos',
            'activeMenu' => 'caja',
            'historial'  => $historial
        ], 'main');
    }

    /**
     * Emite el pulso ESC/POS para la apertura física de la gaveta / cajón monedero
     */
    public function pulsoGaveta(): void
    {
        $this->render('caja/pulso_gaveta', [], null);
    }

    /**
     * API AJAX: Consulta el balance y estado actual de la gaveta en tiempo real
     */
    public function estadoGavetaAjax(): void
    {
        $usuarioId = Auth::id();
        $sesion = $this->sesionModel->getSesionActiva($usuarioId);

        if (!$sesion && Auth::isAdmin()) {
            $sesion = $this->sesionModel->getCualquierSesionAbierta();
        }

        if (!$sesion) {
            $this->jsonResponse(['success' => false, 'message' => 'No hay ninguna sesión de caja abierta actualmente.'], 404);
            return;
        }

        $montoInicial = (float)$sesion['monto_inicial'];
        $ventasEf = (float)$sesion['total_ventas_efectivo'];
        $ventasTarj = (float)$sesion['total_ventas_tarjeta'];
        $ventasTransf = (float)$sesion['total_ventas_transferencia'];
        $entradas = (float)$sesion['total_entradas'];
        $salidas = (float)$sesion['total_salidas'];
        $montoEsperado = (float)$sesion['monto_esperado'];

        $this->jsonResponse([
            'success'            => true,
            'sesion_id'          => $sesion['id'],
            'usuario_nombre'     => $sesion['usuario_nombre'] ?? (Auth::user()['nombre'] ?? 'Cajero'),
            'fecha_apertura'     => date('d/m/Y H:i', strtotime($sesion['fecha_apertura'])),
            'monto_inicial'      => $montoInicial,
            'ventas_efectivo'    => $ventasEf,
            'ventas_tarjeta'     => $ventasTarj,
            'ventas_transfer'    => $ventasTransf,
            'entradas'           => $entradas,
            'salidas'            => $salidas,
            'monto_esperado'     => $montoEsperado,
            'total_facturado'    => ($ventasEf + $ventasTarj + $ventasTransf)
        ]);
    }

    private function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}
