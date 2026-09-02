<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\ProductoModel;
use App\Models\FacturaModel;
use App\Models\SesionCajaModel;

/**
 * ==============================================================================
 * CONTROLADOR DASHBOARDCONTROLLER
 * ==============================================================================
 * Panel de resumen, indicadores clave (KPIs) y accesos rápidos para Administradores.
 * ==============================================================================
 */
class DashboardController extends Controller
{
    private ProductoModel $productoModel;
    private FacturaModel $facturaModel;
    private SesionCajaModel $sesionModel;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->productoModel = new ProductoModel();
        $this->facturaModel = new FacturaModel();
        $this->sesionModel = new SesionCajaModel();
    }

    /**
     * Muestra el resumen general del negocio con métricas en tiempo real
     */
    public function index(): void
    {
        $hoy = date('Y-m-d');
        $metricasVentas = $this->facturaModel->getMetricasHistorial($hoy, $hoy);
        $metricasInventario = $this->productoModel->getMetricas();
        $sesionActiva = $this->sesionModel->getCualquierSesionAbierta();

        $this->render('dashboard/index', [
            'title'        => 'Dashboard - ' . APP_NAME,
            'pageTitle'    => 'Resumen General del Negocio',
            'activeMenu'   => 'dashboard',
            'stats'        => [
                'ventas_hoy'      => (float)($metricasVentas['total_ventas'] ?? 0),
                'total_facturas'  => (int)($metricasVentas['total_facturas'] ?? 0),
                'productos_stock' => (int)($metricasInventario['total_productos'] ?? 0),
                'alertas_stock'   => (int)($metricasInventario['alertas_stock_bajo'] ?? 0)
            ],
            'sesionActiva' => $sesionActiva
        ], 'main');
    }
}
