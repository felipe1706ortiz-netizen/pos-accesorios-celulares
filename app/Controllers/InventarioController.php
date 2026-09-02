<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\ProductoModel;
use App\Models\CategoriaModel;
use App\Models\MovimientoInventarioModel;
use Exception;

/**
 * ==============================================================================
 * CONTROLADOR INVENTARIOCONTROLLER (MÓDULOS 1 Y 2)
 * ==============================================================================
 * Gestiona el catálogo de productos, categorías, niveles de stock, precios
 * y trazabilidad de movimientos de inventario (Kárdex).
 * ==============================================================================
 */
class InventarioController extends Controller
{
    private ProductoModel $productoModel;
    private CategoriaModel $categoriaModel;
    private MovimientoInventarioModel $movimientoModel;

    public function __construct()
    {
        // El módulo de inventario requiere autenticación (acceso para Admins y Cajeros autorizados)
        Auth::requireAuth();

        $this->productoModel = new ProductoModel();
        $this->categoriaModel = new CategoriaModel();
        $this->movimientoModel = new MovimientoInventarioModel();
    }

    /**
     * Muestra el catálogo principal de inventario con filtros y métricas
     */
    public function index(): void
    {
        $search = $this->getQuery('q', '');
        $categoriaId = !empty($this->getQuery('categoria')) ? (int)$this->getQuery('categoria') : null;
        $stockStatus = $this->getQuery('stock_status', null);

        $productos = $this->productoModel->getListado($search, $categoriaId, $stockStatus);
        $categorias = $this->categoriaModel->getActivas();
        $metricas = $this->productoModel->getMetricas();

        $this->render('inventario/index', [
            'title'        => 'Inventario de Accesorios - ' . APP_NAME,
            'pageTitle'    => 'Gestión de Inventario y Stock',
            'activeMenu'   => 'inventario',
            'productos'    => $productos,
            'categorias'   => $categorias,
            'metricas'     => $metricas,
            'search'       => $search,
            'selectedCat'  => $categoriaId,
            'stockStatus'  => $stockStatus,
            'extraJs'      => ['inventario']
        ], 'main');
    }

    /**
     * Crea un nuevo producto en el catálogo
     */
    public function guardar(): void
    {
        if (!$this->validateCsrf()) {
            Session::setFlash('danger', 'Error de seguridad CSRF: Por favor recargue e intente nuevamente.');
            $this->redirect('/inventario');
            return;
        }

        $codigoBarras = $this->getPost('codigo_barras');
        $nombre = $this->getPost('nombre');
        $categoriaId = $this->getPost('categoria_id');
        $precioCompra = (float)$this->getPost('precio_compra', 0);
        $precioVenta = (float)$this->getPost('precio_venta', 0);
        $stock = (int)$this->getPost('stock', 0);
        $stockMinimo = (int)$this->getPost('stock_minimo', 5);
        $descripcion = $this->getPost('descripcion', '');

        // Validaciones
        if (empty($codigoBarras) || empty($nombre) || empty($categoriaId) || $precioVenta <= 0) {
            Session::setFlash('danger', 'Complete todos los campos obligatorios y asegure un precio de venta mayor a 0.');
            $this->redirect('/inventario');
            return;
        }

        // Verificar código de barras único
        if ($this->productoModel->existsCodigo($codigoBarras)) {
            Session::setFlash('danger', "El código de barras [{$codigoBarras}] ya está asignado a otro producto.");
            $this->redirect('/inventario');
            return;
        }

        try {
            $this->productoModel->crear([
                'codigo_barras' => $codigoBarras,
                'nombre'        => $nombre,
                'descripcion'   => $descripcion,
                'categoria_id'  => (int)$categoriaId,
                'precio_compra' => $precioCompra,
                'precio_venta'  => $precioVenta,
                'stock'         => $stock,
                'stock_minimo'  => $stockMinimo
            ], Auth::id());

            Session::setFlash('success', "Producto '{$nombre}' registrado exitosamente en el inventario.");
        } catch (Exception $e) {
            Session::setFlash('danger', "Error al registrar el producto: " . $e->getMessage());
        }

        $this->redirect('/inventario');
    }

    /**
     * Actualiza la información completa de un producto
     * 
     * @param int|string $id
     */
    public function actualizar($id): void
    {
        if (!$this->validateCsrf()) {
            Session::setFlash('danger', 'Error de seguridad CSRF.');
            $this->redirect('/inventario');
            return;
        }

        $id = (int)$id;
        $producto = $this->productoModel->findById($id);
        if (!$producto) {
            Session::setFlash('danger', 'El producto que intenta editar no existe.');
            $this->redirect('/inventario');
            return;
        }

        $codigoBarras = $this->getPost('codigo_barras');
        $nombre = $this->getPost('nombre');
        $categoriaId = $this->getPost('categoria_id');
        $precioCompra = (float)$this->getPost('precio_compra', 0);
        $precioVenta = (float)$this->getPost('precio_venta', 0);
        $stockMinimo = (int)$this->getPost('stock_minimo', 5);
        $descripcion = $this->getPost('descripcion', '');

        if ($this->productoModel->existsCodigo($codigoBarras, $id)) {
            Session::setFlash('danger', "El código de barras [{$codigoBarras}] ya está en uso por otro producto.");
            $this->redirect('/inventario');
            return;
        }

        try {
            $this->productoModel->update($id, [
                'codigo_barras' => $codigoBarras,
                'nombre'        => $nombre,
                'descripcion'   => $descripcion,
                'categoria_id'  => (int)$categoriaId,
                'precio_compra' => $precioCompra,
                'precio_venta'  => $precioVenta,
                'stock_minimo'  => $stockMinimo
            ]);

            Session::setFlash('success', "Producto '{$nombre}' actualizado correctamente.");
        } catch (Exception $e) {
            Session::setFlash('danger', "Error al actualizar producto: " . $e->getMessage());
        }

        $this->redirect('/inventario');
    }

    /**
     * Ajuste rápido de Stock y Precios (Soporta AJAX o Formulario estándar)
     */
    public function ajusteRapido(): void
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || !empty($this->getJsonBody());
        $data = $isAjax ? ($this->getJsonBody() ?: $this->getPost()) : $this->getPost();

        $productoId = (int)($data['producto_id'] ?? 0);
        $nuevoStock = isset($data['stock']) ? (int)$data['stock'] : null;
        $nuevoPrecioVenta = isset($data['precio_venta']) ? (float)$data['precio_venta'] : null;
        $nuevoPrecioCompra = isset($data['precio_compra']) ? (float)$data['precio_compra'] : null;
        $motivo = trim($data['motivo'] ?? 'Ajuste rápido de inventario');

        if ($productoId <= 0 || $nuevoStock === null || $nuevoPrecioVenta === null || $nuevoStock < 0 || $nuevoPrecioVenta <= 0) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => 'Valores de stock o precio inválidos.'], 400);
            }
            Session::setFlash('danger', 'Valores de stock o precio inválidos.');
            $this->redirect('/inventario');
            return;
        }

        try {
            $this->productoModel->ajusteRapido(
                $productoId,
                $nuevoStock,
                $nuevoPrecioVenta,
                $nuevoPrecioCompra,
                $motivo,
                Auth::id()
            );

            if ($isAjax) {
                $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Inventario actualizado correctamente',
                    'producto_id' => $productoId,
                    'nuevo_stock' => $nuevoStock,
                    'nuevo_precio' => $nuevoPrecioVenta
                ]);
            }

            Session::setFlash('success', 'Ajuste de inventario aplicado y registrado en el Kárdex.');
        } catch (Exception $e) {
            if ($isAjax) {
                $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
            }
            Session::setFlash('danger', 'Error al aplicar ajuste: ' . $e->getMessage());
        }

        $this->redirect('/inventario');
    }

    /**
     * Desactiva / Elimina un producto (Soft Delete)
     * 
     * @param int|string $id
     */
    public function eliminar($id): void
    {
        Auth::requireAdmin();
        $id = (int)$id;
        $producto = $this->productoModel->findById($id);

        if ($producto) {
            $this->productoModel->update($id, ['estado' => 0]);
            Session::setFlash('warning', "Producto '{$producto['nombre']}' eliminado del catálogo activo.");
        }

        $this->redirect('/inventario');
    }

    /**
     * Muestra la vista de Kárdex y auditoría de movimientos de stock
     */
    public function kardex($productoId = null): void
    {
        $productoId = $productoId ? (int)$productoId : null;
        $historial = $this->movimientoModel->getHistorial(150, $productoId);
        $productoSeleccionado = $productoId ? $this->productoModel->findById($productoId) : null;

        $this->render('inventario/kardex', [
            'title'                => 'Kárdex de Movimientos - ' . APP_NAME,
            'pageTitle'            => 'Trazabilidad y Movimientos de Inventario',
            'activeMenu'           => 'inventario',
            'historial'            => $historial,
            'productoSeleccionado' => $productoSeleccionado
        ], 'main');
    }

    /**
     * Guarda una nueva categoría vía AJAX o Formulario
     */
    public function guardarCategoria(): void
    {
        $nombre = $this->getPost('nombre');
        $descripcion = $this->getPost('descripcion', '');

        if (empty($nombre)) {
            $this->jsonResponse(['success' => false, 'message' => 'El nombre de la categoría es obligatorio.'], 400);
            return;
        }

        if ($this->categoriaModel->existsNombre($nombre)) {
            $this->jsonResponse(['success' => false, 'message' => 'La categoría ya existe.'], 400);
            return;
        }

        try {
            $catId = $this->categoriaModel->insert([
                'nombre'      => trim($nombre),
                'descripcion' => trim($descripcion),
                'estado'      => 1
            ]);

            $this->jsonResponse([
                'success'   => true, 
                'message'   => 'Categoría creada con éxito',
                'categoria' => ['id' => $catId, 'nombre' => $nombre]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API AJAX para búsqueda instantánea en vivo
     */
    public function buscarAjax(): void
    {
        $q = $this->getQuery('q', '');
        $cat = !empty($this->getQuery('categoria')) ? (int)$this->getQuery('categoria') : null;
        $status = $this->getQuery('stock_status', null);

        $productos = $this->productoModel->getListado($q, $cat, $status);
        $this->jsonResponse(['success' => true, 'productos' => $productos]);
    }
}
