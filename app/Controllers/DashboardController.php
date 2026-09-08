<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\ProductoModel;
use App\Models\FacturaModel;
use App\Models\SesionCajaModel;
use App\Models\ReporteModel;
use App\Models\ConfiguracionModel;

/**
 * ==============================================================================
 * CONTROLADOR DASHBOARDCONTROLLER (Centro de Inteligencia y Analítica POS)
 * ==============================================================================
 * Panel de indicadores clave (KPIs), analítica financiera, tendencias de ventas,
 * gráficos interactivos, ranking de rotación de productos y reportes en PDF.
 * ==============================================================================
 */
class DashboardController extends Controller
{
    private ProductoModel $productoModel;
    private FacturaModel $facturaModel;
    private SesionCajaModel $sesionModel;
    private ReporteModel $reporteModel;
    private ConfiguracionModel $configModel;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->productoModel = new ProductoModel();
        $this->facturaModel = new FacturaModel();
        $this->sesionModel = new SesionCajaModel();
        $this->reporteModel = new ReporteModel();
        $this->configModel = new ConfiguracionModel();
    }

    /**
     * Muestra el panel principal con métricas, gráficos y rankings según filtro
     */
    public function index(): void
    {
        $tipo = isset($_GET['tipo']) && in_array(strtolower($_GET['tipo']), ['diario', 'mensual']) 
            ? strtolower($_GET['tipo']) 
            : 'diario';

        $fecha = isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])
            ? $_GET['fecha']
            : date('Y-m-d');

        $mes = isset($_GET['mes']) && preg_match('/^\d{4}-\d{2}$/', $_GET['mes'])
            ? $_GET['mes']
            : date('Y-m');

        // Obtener datos analíticos completos del período seleccionado
        $reporte = $this->reporteModel->getReporteCompleto($tipo, $fecha, $mes);
        $metricasInventario = $this->productoModel->getMetricas();
        $sesionActiva = $this->sesionModel->getCualquierSesionAbierta();
        $config = $this->configModel->getMapaConfiguracion();

        $this->render('dashboard/index', [
            'title'              => 'Dashboard Analítico - ' . APP_NAME,
            'pageTitle'          => 'Panel de Control e Inteligencia de Negocio',
            'activeMenu'         => 'dashboard',
            'tipo'               => $tipo,
            'fecha'              => $fecha,
            'mes'                => $mes,
            'reporte'            => $reporte,
            'metricasInventario' => $metricasInventario,
            'sesionActiva'       => $sesionActiva,
            'config'             => $config,
            'extraJs'            => ['chart.min', 'html2pdf.bundle.min', 'dashboard']
        ], 'main');
    }

    /**
     * Endpoint API AJAX: Retorna datos analíticos en JSON para actualización dinámica
     */
    public function apiData(): void
    {
        $tipo = isset($_GET['tipo']) && in_array(strtolower($_GET['tipo']), ['diario', 'mensual']) 
            ? strtolower($_GET['tipo']) 
            : 'diario';

        $fecha = isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])
            ? $_GET['fecha']
            : date('Y-m-d');

        $mes = isset($_GET['mes']) && preg_match('/^\d{4}-\d{2}$/', $_GET['mes'])
            ? $_GET['mes']
            : date('Y-m');

        $reporte = $this->reporteModel->getReporteCompleto($tipo, $fecha, $mes);

        $this->jsonResponse([
            'success' => true,
            'tipo'    => $tipo,
            'fecha'   => $fecha,
            'mes'     => $mes,
            'data'    => $reporte
        ]);
    }

    /**
     * Genera la vista imprimible y descargable en PDF de alta resolución
     */
    public function reportePdf(): void
    {
        $tipo = isset($_GET['tipo']) && in_array(strtolower($_GET['tipo']), ['diario', 'mensual']) 
            ? strtolower($_GET['tipo']) 
            : 'diario';

        $fecha = isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])
            ? $_GET['fecha']
            : date('Y-m-d');

        $mes = isset($_GET['mes']) && preg_match('/^\d{4}-\d{2}$/', $_GET['mes'])
            ? $_GET['mes']
            : date('Y-m');

        $reporte = $this->reporteModel->getReporteCompleto($tipo, $fecha, $mes);
        $metricasInventario = $this->productoModel->getMetricas();
        $config = $this->configModel->getMapaConfiguracion();

        $this->render('dashboard/reporte_pdf', [
            'tipo'               => $tipo,
            'fecha'              => $fecha,
            'mes'                => $mes,
            'reporte'            => $reporte,
            'metricasInventario' => $metricasInventario,
            'config'             => $config
        ], null); // Renderizado directo sin layout para formato A4 de impresión ejecutiva
    }
}
