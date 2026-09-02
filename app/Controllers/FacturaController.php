<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\FacturaModel;
use App\Models\ConfiguracionModel;
use Exception;

/**
 * ==============================================================================
 * CONTROLADOR FACTURACONTROLLER (MÓDULO 4: HISTORIAL DE VENTAS)
 * ==============================================================================
 * Gestión del historial de facturas emitidas, filtros por rango de fechas,
 * visualización de detalle de transacciones, reimpresión y anulación.
 * ==============================================================================
 */
class FacturaController extends Controller
{
    private FacturaModel $facturaModel;
    private ConfiguracionModel $configModel;

    public function __construct()
    {
        Auth::requireAuth();
        $this->facturaModel = new FacturaModel();
        $this->configModel = new ConfiguracionModel();
    }

    /**
     * Muestra el listado del historial de facturas con filtros y estadísticas
     */
    public function index(): void
    {
        $fechaInicio = $this->getQuery('fecha_inicio', date('Y-m-d'));
        $fechaFin = $this->getQuery('fecha_fin', date('Y-m-d'));
        $metodoPago = $this->getQuery('metodo_pago', '');
        $estado = $this->getQuery('estado', '');
        $search = $this->getQuery('q', '');

        $facturas = $this->facturaModel->getHistorial(
            $fechaInicio ?: null,
            $fechaFin ?: null,
            $metodoPago ?: null,
            $estado ?: null,
            $search
        );

        $metricas = $this->facturaModel->getMetricasHistorial(
            $fechaInicio ?: null,
            $fechaFin ?: null
        );

        $config = $this->configModel->getMapaConfiguracion();

        $this->render('facturas/index', [
            'title'       => 'Historial de Facturas - ' . APP_NAME,
            'pageTitle'   => 'Historial y Auditoría de Facturas',
            'activeMenu'  => 'facturas',
            'facturas'    => $facturas,
            'metricas'    => $metricas,
            'config'      => $config,
            'fechaInicio' => $fechaInicio,
            'fechaFin'    => $fechaFin,
            'metodoPago'  => $metodoPago,
            'estado'      => $estado,
            'search'      => $search,
            'extraJs'     => ['facturas']
        ], 'main');
    }

    /**
     * Muestra o retorna el detalle de una factura específica
     * 
     * @param int|string $id
     */
    public function detalle($id): void
    {
        $id = (int)$id;
        $factura = $this->facturaModel->getFacturaCompleta($id);

        if (!$factura) {
            if ($this->isAjax()) {
                $this->jsonResponse(['success' => false, 'message' => 'Factura no encontrada'], 404);
            }
            $this->renderError("La factura #{$id} no existe.", 404);
            return;
        }

        // Si es una petición AJAX (ej. para abrir modal de detalle rápido)
        if ($this->isAjax()) {
            $this->jsonResponse([
                'success' => true,
                'factura' => $factura
            ]);
            return;
        }

        $config = $this->configModel->getMapaConfiguracion();

        $this->render('facturas/detalle', [
            'title'      => "Factura #{$factura['numero_factura']} - " . APP_NAME,
            'pageTitle'  => "Detalle de Venta: {$factura['numero_factura']}",
            'activeMenu' => 'facturas',
            'factura'    => $factura,
            'config'     => $config
        ], 'main');
    }

    /**
     * Anula una factura de venta (Exclusivo Administradores)
     * 
     * @param int|string $id
     */
    public function anular($id): void
    {
        Auth::requireAdmin();

        $id = (int)$id;
        $motivo = $this->getPost('motivo', 'Anulación de venta por error');

        try {
            $this->facturaModel->anularFactura($id, Auth::id(), $motivo);

            if ($this->isAjax()) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Factura anulada con éxito y stock restaurado en el inventario.'
                ]);
            }

            Session::setFlash('success', 'Factura anulada correctamente. Las unidades han retornado al inventario.');
        } catch (Exception $e) {
            if ($this->isAjax()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            Session::setFlash('danger', 'Error al anular la factura: ' . $e->getMessage());
        }

        $this->redirect('/facturas');
    }

    /**
     * Helper para verificar si la petición es AJAX
     * @return bool
     */
    private function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}
