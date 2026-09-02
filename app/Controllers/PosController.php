<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use App\Models\FacturaModel;
use App\Models\SesionCajaModel;
use App\Models\ConfiguracionModel;
use Exception;

/**
 * ==============================================================================
 * CONTROLADOR POSCONTROLLER (MÓDULO 3 CORE)
 * ==============================================================================
 * Núcleo del Punto de Venta: escaneo de código de barras, búsqueda en modal F2,
 * cálculo de carrito en tiempo real, checkout y bucle continuo de facturación.
 * ==============================================================================
 */
class PosController extends Controller
{
    private ProductoModel $productoModel;
    private CategoriaModel $categoriaModel;
    private FacturaModel $facturaModel;
    private SesionCajaModel $sesionCajaModel;
    private ConfiguracionModel $configModel;

    public function __construct()
    {
        Auth::requireAuth();

        $this->productoModel = new ProductoModel();
        $this->categoriaModel = new CategoriaModel();
        $this->facturaModel = new FacturaModel();
        $this->sesionCajaModel = new SesionCajaModel();
        $this->configModel = new ConfiguracionModel();
    }

    /**
     * Muestra la interfaz principal del Punto de Venta (Keyboard-First)
     */
    public function index(): void
    {
        $usuarioId = Auth::id();
        $sesion = $this->sesionCajaModel->getSesionActiva($usuarioId);

        if (!$sesion && Auth::isAdmin()) {
            $sesion = $this->sesionCajaModel->getCualquierSesionAbierta();
        }

        if (!$sesion) {
            \App\Core\Session::setFlash('warning', 'Debe abrir el turno de caja indicando el fondo/base inicial antes de registrar ventas.');
            $this->redirect('/caja/apertura');
            return;
        }

        $categorias = $this->categoriaModel->getActivas();
        $config = $this->configModel->getMapaConfiguracion();

        $this->render('pos/index', [
            'title'      => 'Punto de Venta (POS) - ' . APP_NAME,
            'pageTitle'  => 'Nueva Venta (POS)',
            'activeMenu' => 'pos',
            'sesion'     => $sesion,
            'categorias' => $categorias,
            'config'     => $config,
            'extraCss'   => ['pos'],
            'extraJs'    => ['pos', 'caja']
        ], 'main');
    }

    /**
     * API AJAX para consulta de productos por código de barras o búsqueda manual (F2)
     */
    public function buscarProducto(): void
    {
        $codigo = $this->getQuery('codigo', '');
        $q = $this->getQuery('q', '');

        // 1. Búsqueda exacta por código de barras (Escáner)
        if (!empty($codigo)) {
            $producto = $this->productoModel->findByCodigoBarras($codigo);

            if ($producto) {
                $this->jsonResponse([
                    'success'  => true,
                    'producto' => [
                        'id'            => (int)$producto['id'],
                        'codigo_barras' => $producto['codigo_barras'],
                        'nombre'        => $producto['nombre'],
                        'precio_venta'  => (float)$producto['precio_venta'],
                        'stock'         => (int)$producto['stock'],
                        'categoria'     => $producto['categoria_nombre'] ?? ''
                    ]
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => "Producto con código '{$codigo}' no encontrado en el catálogo."
                ], 404);
            }
            return;
        }

        // 2. Búsqueda manual por nombre o coincidencia parcial (Modal F2)
        if (!empty($q)) {
            $productos = $this->productoModel->getListado($q);
            $formateados = array_map(function($p) {
                return [
                    'id'            => (int)$p['id'],
                    'codigo_barras' => $p['codigo_barras'],
                    'nombre'        => $p['nombre'],
                    'precio_venta'  => (float)$p['precio_venta'],
                    'stock'         => (int)$p['stock'],
                    'categoria'     => $p['categoria_nombre'] ?? ''
                ];
            }, $productos);

            $this->jsonResponse([
                'success'   => true,
                'productos' => $formateados
            ]);
            return;
        }

        $this->jsonResponse(['success' => false, 'message' => 'Parámetro de búsqueda no provisto.'], 400);
    }

    /**
     * API AJAX para procesar y asentar la venta en base de datos
     */
    public function procesarVenta(): void
    {
        $data = $this->getJsonBody();

        if (empty($data) || empty($data['items'])) {
            $this->jsonResponse(['success' => false, 'message' => 'El carrito no contiene productos válidos.'], 400);
            return;
        }

        try {
            $usuarioId = Auth::id();
            $resultado = $this->facturaModel->procesarVenta($data, $data['items'], $usuarioId);

            $this->jsonResponse([
                'success'        => true,
                'message'        => 'Venta completada con éxito',
                'factura_id'     => $resultado['id'],
                'numero_factura' => $resultado['numero_factura'],
                'total'          => $resultado['total'],
                'cambio'         => $resultado['cambio'],
                'metodo_pago'    => $resultado['metodo_pago'],
                'print_url'      => APP_URL . '/pos/imprimir/' . $resultado['id']
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera la vista imprimible del ticket térmico (58mm / 80mm ESC/POS compatible)
     * 
     * @param int|string $id
     */
    public function imprimirTicket($id): void
    {
        $factura = $this->facturaModel->getFacturaCompleta((int)$id);

        if (!$factura) {
            $this->renderError("La factura #{$id} no existe.", 404);
            return;
        }

        $config = $this->configModel->getMapaConfiguracion();

        $this->render('pos/ticket', [
            'factura' => $factura,
            'config'  => $config
        ], null); // Renderizado directo sin layout maestro para impresión térmica limpia
    }
}
