<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\PedidoModel;
use App\Models\ProductoModel;
use App\Models\ConfiguracionModel;
use App\Models\SesionCajaModel;
use App\Models\MovimientoCajaModel;
use Exception;

/**
 * ==============================================================================
 * CONTROLADOR PEDIDOCONTROLLER (Módulo de Pedidos y Encargos de Clientes)
 * ==============================================================================
 * Registro de encargos, abonos, seguimiento de entregas, contacto directo por WhatsApp
 * e impresión de comprobante térmico para el cliente.
 * ==============================================================================
 */
class PedidoController extends Controller
{
    private PedidoModel $pedidoModel;
    private ProductoModel $productoModel;
    private ConfiguracionModel $configModel;
    private SesionCajaModel $sesionModel;
    private MovimientoCajaModel $movimientoCajaModel;

    public function __construct()
    {
        Auth::requireAuth();
        $this->pedidoModel = new PedidoModel();
        $this->productoModel = new ProductoModel();
        $this->configModel = new ConfiguracionModel();
        $this->sesionModel = new SesionCajaModel();
        $this->movimientoCajaModel = new MovimientoCajaModel();
    }

    /**
     * Listado general y tablero de gestión de pedidos
     */
    public function index(): void
    {
        $estado = isset($_GET['estado']) ? strtoupper(trim($_GET['estado'])) : 'TODOS';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        $pedidos = $this->pedidoModel->getListado($estado, $search);
        $metricas = $this->pedidoModel->getMetricas();
        $config = $this->configModel->getMapaConfiguracion();

        $this->render('pedidos/index', [
            'title'      => 'Pedidos de Clientes - ' . APP_NAME,
            'pageTitle'  => 'Pedidos y Encargos de Clientes',
            'activeMenu' => 'pedidos',
            'pedidos'    => $pedidos,
            'metricas'   => $metricas,
            'filtro'     => $estado,
            'search'     => $search,
            'config'     => $config,
            'extraJs'    => ['pedidos']
        ], 'main');
    }

    /**
     * Vista de formulario para registrar un nuevo pedido
     */
    public function nuevo(): void
    {
        $productos = $this->productoModel->getListado('', null, null);
        $config = $this->configModel->getMapaConfiguracion();

        $this->render('pedidos/nuevo', [
            'title'      => 'Nuevo Pedido de Cliente - ' . APP_NAME,
            'pageTitle'  => 'Ingresar Pedido de Cliente',
            'activeMenu' => 'pedidos',
            'productos'  => $productos,
            'config'     => $config,
            'extraJs'    => ['pedidos']
        ], 'main');
    }

    /**
     * Procesa y guarda un nuevo pedido
     */
    public function guardar(): void
    {
        if (!$this->validateCsrf()) {
            Session::setFlash('danger', 'Error de seguridad CSRF.');
            $this->redirect('/pedidos/nuevo');
            return;
        }

        try {
            $usuarioId = Auth::id();
            $pedido = $this->pedidoModel->crear($_POST, $usuarioId);

            $montoAbono = (float)($_POST['abono'] ?? 0);
            $metodoAbono = strtoupper($_POST['metodo_pago_abono'] ?? 'EFECTIVO');

            // Si el cliente dejó abono en efectivo, registrar entrada en caja activa si existe
            if ($montoAbono > 0 && $metodoAbono === 'EFECTIVO') {
                $sesionActiva = $this->sesionModel->getSesionActiva($usuarioId);
                if (!$sesionActiva && Auth::isAdmin()) {
                    $sesionActiva = $this->sesionModel->getCualquierSesionAbierta();
                }

                if ($sesionActiva) {
                    $this->movimientoCajaModel->registrar(
                        (int)$sesionActiva['id'],
                        $usuarioId,
                        'ENTRADA',
                        $montoAbono,
                        "Abono inicial Pedido #{$pedido['codigo']} - Cliente: {$pedido['cliente_nombre']}",
                        $pedido['codigo']
                    );
                }
            }

            Session::setFlash('success', "¡Pedido #{$pedido['codigo']} registrado con éxito para {$pedido['cliente_nombre']}!");
            $this->redirect('/pedidos');
        } catch (Exception $e) {
            Session::setFlash('danger', "Error al registrar el pedido: " . $e->getMessage());
            $this->redirect('/pedidos/nuevo');
        }
    }

    /**
     * API AJAX / POST: Actualiza el estado operativo de un pedido
     *
     * @param int|string $id
     */
    public function cambiarEstado($id): void
    {
        $id = (int)$id;
        $nuevoEstado = $this->getPost('nuevo_estado', '');
        $notas = $this->getPost('notas', '');

        try {
            $this->pedidoModel->actualizarEstado($id, $nuevoEstado, $notas);

            if ($this->isAjax()) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => "Estado de pedido actualizado a '{$nuevoEstado}' correctamente."
                ]);
                return;
            }

            Session::setFlash('success', "Estado actualizado a '{$nuevoEstado}' exitosamente.");
            $this->redirect('/pedidos');
        } catch (Exception $e) {
            if ($this->isAjax()) {
                $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
                return;
            }

            Session::setFlash('danger', "Error: " . $e->getMessage());
            $this->redirect('/pedidos');
        }
    }

    /**
     * Registra un abono adicional hacia el saldo pendiente del pedido
     *
     * @param int|string $id
     */
    public function abonar($id): void
    {
        $id = (int)$id;
        $montoAbono = (float)$this->getPost('monto_abono', 0);
        $metodoAbono = strtoupper($this->getPost('metodo_abono', 'EFECTIVO'));

        try {
            $this->pedidoModel->registrarAbono($id, $montoAbono);
            $pedido = $this->pedidoModel->findByIdWithDetails($id);

            // Registrar en caja si fue en efectivo
            if ($montoAbono > 0 && $metodoAbono === 'EFECTIVO') {
                $usuarioId = Auth::id();
                $sesionActiva = $this->sesionModel->getSesionActiva($usuarioId);
                if (!$sesionActiva && Auth::isAdmin()) {
                    $sesionActiva = $this->sesionModel->getCualquierSesionAbierta();
                }

                if ($sesionActiva) {
                    $this->movimientoCajaModel->registrar(
                        (int)$sesionActiva['id'],
                        $usuarioId,
                        'ENTRADA',
                        $montoAbono,
                        "Abono adicional Pedido #{$pedido['codigo']} - {$pedido['cliente_nombre']}",
                        $pedido['codigo']
                    );
                }
            }

            Session::setFlash('success', "Abono de $ " . number_format($montoAbono, 0, ',', '.') . " registrado correctamente al Pedido #{$pedido['codigo']}.");
            $this->redirect('/pedidos');
        } catch (Exception $e) {
            Session::setFlash('danger', "Error al registrar abono: " . $e->getMessage());
            $this->redirect('/pedidos');
        }
    }

    /**
     * Genera comprobante térmico ESC/POS de encargo para entregar al cliente
     *
     * @param int|string $id
     */
    public function ticket($id): void
    {
        $pedido = $this->pedidoModel->findByIdWithDetails((int)$id);

        if (!$pedido) {
            $this->renderError("El pedido #{$id} no existe.", 404);
            return;
        }

        $config = $this->configModel->getMapaConfiguracion();

        $this->render('pedidos/ticket', [
            'pedido' => $pedido,
            'config' => $config
        ], null); // Renderizado directo sin layout maestro para impresión limpia
    }

    private function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}
