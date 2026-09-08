<?php
namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * ==============================================================================
 * MODELO REPORTEMODEL (Inteligencia de Negocio y Métricas de Rendimiento)
 * ==============================================================================
 * Consolida el análisis de ventas, costos, márgenes de utilidad, pérdidas por
 * anulación, tendencias temporales (diarias/mensuales) y rankings de productos.
 * Compatible con MySQL y PostgreSQL (Supabase).
 * ==============================================================================
 */
class ReporteModel extends Model
{
    /**
     * Calcula los límites de fecha [inicio, fin, etiqueta_periodo] según el tipo seleccionado
     *
     * @param string $tipo 'diario' | 'mensual'
     * @param string $fecha YYYY-MM-DD
     * @param string $mes YYYY-MM
     * @return array
     */
    public function obtenerRangoFechas(string $tipo, string $fecha = '', string $mes = ''): array
    {
        if ($tipo === 'mensual') {
            if (empty($mes)) {
                $mes = date('Y-m');
            }
            $inicio = date('Y-m-01 00:00:00', strtotime("{$mes}-01"));
            $fin = date('Y-m-t 23:59:59', strtotime("{$mes}-01"));

            // Nombre legible del mes en español
            $mesesNombres = [
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
            ];
            $partesMes = explode('-', $mes);
            $ano = $partesMes[0] ?? date('Y');
            $mesNum = $partesMes[1] ?? date('m');
            $etiqueta = ($mesesNombres[$mesNum] ?? 'Mes') . " {$ano}";

            return [
                'tipo'           => 'mensual',
                'fecha_inicio'   => $inicio,
                'fecha_fin'      => $fin,
                'mes'            => $mes,
                'fecha'          => $fecha ?: date('Y-m-d'),
                'etiqueta'       => $etiqueta,
                'total_dias_mes' => (int)date('t', strtotime("{$mes}-01"))
            ];
        }

        // Modo Diario por defecto
        if (empty($fecha)) {
            $fecha = date('Y-m-d');
        }
        $inicio = "{$fecha} 00:00:00";
        $fin = "{$fecha} 23:59:59";
        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $numDia = (int)date('w', strtotime($fecha));
        $etiqueta = ($diasSemana[$numDia] ?? '') . ' ' . date('d/m/Y', strtotime($fecha));

        return [
            'tipo'         => 'diario',
            'fecha_inicio' => $inicio,
            'fecha_fin'    => $fin,
            'fecha'        => $fecha,
            'mes'          => $mes ?: date('Y-m'),
            'etiqueta'     => $etiqueta
        ];
    }

    /**
     * Obtiene el consolidado de métricas clave (Ventas, Costos, Utilidad, Pérdidas, Tickets)
     *
     * @param string $tipo 'diario' | 'mensual'
     * @param string $fecha
     * @param string $mes
     * @return array
     */
    public function getMetricasPeriodo(string $tipo = 'diario', string $fecha = '', string $mes = ''): array
    {
        $rango = $this->obtenerRangoFechas($tipo, $fecha, $mes);
        $inicio = $rango['fecha_inicio'];
        $fin = $rango['fecha_fin'];

        // 1. Resumen de facturas (Ventas e ingresos totales)
        $sqlFacturas = "SELECT 
                            COUNT(CASE WHEN estado = 'COMPLETADA' THEN 1 END) AS total_facturas,
                            COALESCE(SUM(CASE WHEN estado = 'COMPLETADA' THEN total ELSE 0 END), 0) AS total_ventas,
                            COALESCE(AVG(CASE WHEN estado = 'COMPLETADA' THEN total ELSE NULL END), 0) AS ticket_promedio,
                            COUNT(CASE WHEN estado = 'ANULADA' THEN 1 END) AS facturas_anuladas,
                            COALESCE(SUM(CASE WHEN estado = 'ANULADA' THEN total ELSE 0 END), 0) AS perdidas_anulaciones,
                            COALESCE(SUM(CASE WHEN estado = 'COMPLETADA' AND metodo_pago = 'EFECTIVO' THEN total ELSE 0 END), 0) AS ventas_efectivo,
                            COALESCE(SUM(CASE WHEN estado = 'COMPLETADA' AND metodo_pago = 'TARJETA' THEN total ELSE 0 END), 0) AS ventas_tarjeta,
                            COALESCE(SUM(CASE WHEN estado = 'COMPLETADA' AND metodo_pago = 'TRANSFERENCIA' THEN total ELSE 0 END), 0) AS ventas_transferencia
                        FROM facturas
                        WHERE created_at >= :inicio AND created_at <= :fin";

        $resFacturas = $this->fetchOne($sqlFacturas, [':inicio' => $inicio, ':fin' => $fin]) ?: [];

        // 2. Costos de compra de lo vendido y total de unidades despachadas
        $sqlCostos = "SELECT 
                          COALESCE(SUM(d.cantidad), 0) AS total_unidades,
                          COALESCE(SUM(d.cantidad * d.precio_compra), 0) AS costo_total
                      FROM detalle_facturas d
                      INNER JOIN facturas f ON f.id = d.factura_id
                      WHERE f.estado = 'COMPLETADA' 
                        AND f.created_at >= :inicio 
                        AND f.created_at <= :fin";

        $resCostos = $this->fetchOne($sqlCostos, [':inicio' => $inicio, ':fin' => $fin]) ?: [];

        $totalVentas = (float)($resFacturas['total_ventas'] ?? 0);
        $costoTotal = (float)($resCostos['costo_total'] ?? 0);
        $gananciaNeta = max(0, $totalVentas - $costoTotal);
        $perdidasAnulaciones = (float)($resFacturas['perdidas_anulaciones'] ?? 0);

        $margenPorcentaje = ($totalVentas > 0) 
            ? round(($gananciaNeta / $totalVentas) * 100, 1) 
            : 0;

        return [
            'rango'                => $rango,
            'total_ventas'         => $totalVentas,
            'costo_total'          => $costoTotal,
            'ganancia_neta'        => $gananciaNeta,
            'margen_porcentaje'    => $margenPorcentaje,
            'total_facturas'       => (int)($resFacturas['total_facturas'] ?? 0),
            'ticket_promedio'      => (float)($resFacturas['ticket_promedio'] ?? 0),
            'total_unidades'       => (int)($resCostos['total_unidades'] ?? 0),
            'facturas_anuladas'    => (int)($resFacturas['facturas_anuladas'] ?? 0),
            'perdidas_anulaciones' => $perdidasAnulaciones,
            'ventas_efectivo'      => (float)($resFacturas['ventas_efectivo'] ?? 0),
            'ventas_tarjeta'       => (float)($resFacturas['ventas_tarjeta'] ?? 0),
            'ventas_transferencia' => (float)($resFacturas['ventas_transferencia'] ?? 0)
        ];
    }

    /**
     * Obtiene la serie temporal de ventas (por horas en diario, por días en mensual)
     *
     * @param string $tipo 'diario' | 'mensual'
     * @param string $fecha
     * @param string $mes
     * @return array ['labels' => [], 'ventas' => [], 'ganancias' => [], 'facturas' => []]
     */
    public function getTendenciaVentas(string $tipo = 'diario', string $fecha = '', string $mes = ''): array
    {
        $rango = $this->obtenerRangoFechas($tipo, $fecha, $mes);
        $inicio = $rango['fecha_inicio'];
        $fin = $rango['fecha_fin'];

        if ($tipo === 'mensual') {
            $totalDias = $rango['total_dias_mes'];
            $labels = [];
            $ventasPorDia = array_fill(1, $totalDias, 0.0);
            $gananciasPorDia = array_fill(1, $totalDias, 0.0);
            $facturasPorDia = array_fill(1, $totalDias, 0);

            for ($d = 1; $d <= $totalDias; $d++) {
                $labels[] = "Día {$d}";
            }

            // Ventas y facturas por día
            $sql = "SELECT 
                        CAST(EXTRACT(DAY FROM f.created_at) AS INTEGER) AS dia,
                        COALESCE(SUM(f.total), 0) AS total_ventas,
                        COUNT(f.id) AS total_facturas
                    FROM facturas f
                    WHERE f.estado = 'COMPLETADA' 
                      AND f.created_at >= :inicio 
                      AND f.created_at <= :fin
                    GROUP BY dia";

            $rows = $this->fetchAll($sql, [':inicio' => $inicio, ':fin' => $fin]);
            foreach ($rows as $row) {
                $dia = (int)$row['dia'];
                if ($dia >= 1 && $dia <= $totalDias) {
                    $ventasPorDia[$dia] = (float)$row['total_ventas'];
                    $facturasPorDia[$dia] = (int)$row['total_facturas'];
                }
            }

            // Costos por día para calcular la ganancia neta diaria
            $sqlCostos = "SELECT 
                              CAST(EXTRACT(DAY FROM f.created_at) AS INTEGER) AS dia,
                              COALESCE(SUM(d.cantidad * d.precio_compra), 0) AS costo_dia
                          FROM detalle_facturas d
                          INNER JOIN facturas f ON f.id = d.factura_id
                          WHERE f.estado = 'COMPLETADA' 
                            AND f.created_at >= :inicio 
                            AND f.created_at <= :fin
                          GROUP BY dia";

            $rowsCostos = $this->fetchAll($sqlCostos, [':inicio' => $inicio, ':fin' => $fin]);
            foreach ($rowsCostos as $row) {
                $dia = (int)$row['dia'];
                if ($dia >= 1 && $dia <= $totalDias) {
                    $venta = $ventasPorDia[$dia] ?? 0;
                    $costo = (float)$row['costo_dia'];
                    $gananciasPorDia[$dia] = max(0, $venta - $costo);
                }
            }

            return [
                'labels'    => $labels,
                'ventas'    => array_values($ventasPorDia),
                'ganancias' => array_values($gananciasPorDia),
                'facturas'  => array_values($facturasPorDia)
            ];
        }

        // Modo Diario: bloques de horas del día comercial (08:00 a 20:00)
        $horasComerciales = [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];
        $labels = [];
        $ventasPorHora = [];
        $gananciasPorHora = [];
        $facturasPorHora = [];

        foreach ($horasComerciales as $h) {
            $labels[] = sprintf('%02d:00', $h);
            $ventasPorHora[$h] = 0.0;
            $gananciasPorHora[$h] = 0.0;
            $facturasPorHora[$h] = 0;
        }

        $sql = "SELECT 
                    CAST(EXTRACT(HOUR FROM f.created_at) AS INTEGER) AS hora,
                    COALESCE(SUM(f.total), 0) AS total_ventas,
                    COUNT(f.id) AS total_facturas
                FROM facturas f
                WHERE f.estado = 'COMPLETADA' 
                  AND f.created_at >= :inicio 
                  AND f.created_at <= :fin
                GROUP BY hora";

        $rows = $this->fetchAll($sql, [':inicio' => $inicio, ':fin' => $fin]);
        foreach ($rows as $row) {
            $hora = (int)$row['hora'];
            if (isset($ventasPorHora[$hora])) {
                $ventasPorHora[$hora] = (float)$row['total_ventas'];
                $facturasPorHora[$hora] = (int)$row['total_facturas'];
            }
        }

        // Costos por hora
        $sqlCostos = "SELECT 
                          CAST(EXTRACT(HOUR FROM f.created_at) AS INTEGER) AS hora,
                          COALESCE(SUM(d.cantidad * d.precio_compra), 0) AS costo_hora
                      FROM detalle_facturas d
                      INNER JOIN facturas f ON f.id = d.factura_id
                      WHERE f.estado = 'COMPLETADA' 
                        AND f.created_at >= :inicio 
                        AND f.created_at <= :fin
                      GROUP BY hora";

        $rowsCostos = $this->fetchAll($sqlCostos, [':inicio' => $inicio, ':fin' => $fin]);
        foreach ($rowsCostos as $row) {
            $hora = (int)$row['hora'];
            if (isset($gananciasPorHora[$hora])) {
                $venta = $ventasPorHora[$hora] ?? 0;
                $costo = (float)$row['costo_hora'];
                $gananciasPorHora[$hora] = max(0, $venta - $costo);
            }
        }

        return [
            'labels'    => $labels,
            'ventas'    => array_values($ventasPorHora),
            'ganancias' => array_values($gananciasPorHora),
            'facturas'  => array_values($facturasPorHora)
        ];
    }

    /**
     * Gráfico comparativo de Ganancias, Costos y Pérdidas
     *
     * @param string $tipo
     * @param string $fecha
     * @param string $mes
     * @return array
     */
    public function getGananciasPerdidas(string $tipo = 'diario', string $fecha = '', string $mes = ''): array
    {
        $metricas = $this->getMetricasPeriodo($tipo, $fecha, $mes);

        $labels = ['Ingresos Brutos', 'Costo Adquisición', 'Ganancia Neta (Utilidad)', 'Pérdidas (Anulaciones)'];
        $data = [
            $metricas['total_ventas'],
            $metricas['costo_total'],
            $metricas['ganancia_neta'],
            $metricas['perdidas_anulaciones']
        ];
        $colors = ['#4f46e5', '#f59e0b', '#10b981', '#f43f5e'];

        return [
            'labels' => $labels,
            'data'   => $data,
            'colors' => $colors,
            'metricas' => [
                'ventas'   => $metricas['total_ventas'],
                'costos'   => $metricas['costo_total'],
                'ganancia' => $metricas['ganancia_neta'],
                'perdidas' => $metricas['perdidas_anulaciones'],
                'margen'   => $metricas['margen_porcentaje']
            ]
        ];
    }

    /**
     * Distribución de ventas y unidades por Categoría de producto
     *
     * @param string $tipo
     * @param string $fecha
     * @param string $mes
     * @return array
     */
    public function getVentasPorCategoria(string $tipo = 'diario', string $fecha = '', string $mes = ''): array
    {
        $rango = $this->obtenerRangoFechas($tipo, $fecha, $mes);
        $inicio = $rango['fecha_inicio'];
        $fin = $rango['fecha_fin'];

        $sql = "SELECT 
                    COALESCE(c.nombre, 'Sin Categoría') AS categoria,
                    COALESCE(SUM(d.subtotal), 0) AS total_ventas,
                    COALESCE(SUM(d.cantidad), 0) AS total_unidades
                FROM detalle_facturas d
                INNER JOIN facturas f ON f.id = d.factura_id
                INNER JOIN productos p ON p.id = d.producto_id
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE f.estado = 'COMPLETADA' 
                  AND f.created_at >= :inicio 
                  AND f.created_at <= :fin
                GROUP BY c.id, c.nombre
                ORDER BY total_ventas DESC";

        $categorias = $this->fetchAll($sql, [':inicio' => $inicio, ':fin' => $fin]);

        // Si no hay ventas en el periodo, listar categorías maestras con 0
        if (empty($categorias)) {
            $sqlTodas = "SELECT nombre AS categoria, 0.0 AS total_ventas, 0 AS total_unidades FROM categorias WHERE estado = 1 ORDER BY id ASC LIMIT 5";
            $categorias = $this->fetchAll($sqlTodas);
        }

        $labels = [];
        $ventas = [];
        $unidades = [];

        foreach ($categorias as $cat) {
            $labels[] = $cat['categoria'];
            $ventas[] = (float)$cat['total_ventas'];
            $unidades[] = (int)$cat['total_unidades'];
        }

        return [
            'labels'   => $labels,
            'ventas'   => $ventas,
            'unidades' => $unidades,
            'raw'      => $categorias
        ];
    }

    /**
     * Ranking de productos Más Vendidos en el periodo (Top ingresos y unidades)
     *
     * @param string $tipo
     * @param string $fecha
     * @param string $mes
     * @param int $limit
     * @return array
     */
    public function getProductosMasVendidos(string $tipo = 'diario', string $fecha = '', string $mes = '', int $limit = 6): array
    {
        $rango = $this->obtenerRangoFechas($tipo, $fecha, $mes);
        $inicio = $rango['fecha_inicio'];
        $fin = $rango['fecha_fin'];

        $sql = "SELECT 
                    p.id,
                    p.nombre,
                    p.codigo_barras,
                    p.stock,
                    p.precio_venta,
                    COALESCE(c.nombre, 'General') AS categoria_nombre,
                    COALESCE(SUM(d.cantidad), 0) AS total_unidades,
                    COALESCE(SUM(d.subtotal), 0) AS total_ingresos,
                    COALESCE(SUM(d.subtotal - (d.cantidad * d.precio_compra)), 0) AS ganancia_total
                FROM detalle_facturas d
                INNER JOIN facturas f ON f.id = d.factura_id
                INNER JOIN productos p ON p.id = d.producto_id
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE f.estado = 'COMPLETADA' 
                  AND f.created_at >= :inicio 
                  AND f.created_at <= :fin
                GROUP BY p.id, p.nombre, p.codigo_barras, p.stock, p.precio_venta, c.nombre
                ORDER BY total_unidades DESC, total_ingresos DESC
                LIMIT {$limit}";

        return $this->fetchAll($sql, [':inicio' => $inicio, ':fin' => $fin]);
    }

    /**
     * Ranking de productos Menos Vendidos o Sin Rotación en el periodo
     * Muestra el stock detenido y el capital monetario inmovilizado.
     *
     * @param string $tipo
     * @param string $fecha
     * @param string $mes
     * @param int $limit
     * @return array
     */
    public function getProductosMenosVendidos(string $tipo = 'diario', string $fecha = '', string $mes = '', int $limit = 6): array
    {
        $rango = $this->obtenerRangoFechas($tipo, $fecha, $mes);
        $inicio = $rango['fecha_inicio'];
        $fin = $rango['fecha_fin'];

        $sql = "SELECT 
                    p.id,
                    p.nombre,
                    p.codigo_barras,
                    p.stock,
                    p.precio_compra,
                    p.precio_venta,
                    (p.stock * p.precio_compra) AS capital_inmovilizado,
                    COALESCE(c.nombre, 'General') AS categoria_nombre,
                    COALESCE(SUM(CASE WHEN f.estado = 'COMPLETADA' AND f.created_at >= :inicio AND f.created_at <= :fin THEN d.cantidad ELSE 0 END), 0) AS unidades_vendidas
                FROM productos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                LEFT JOIN detalle_facturas d ON d.producto_id = p.id
                LEFT JOIN facturas f ON f.id = d.factura_id AND f.estado = 'COMPLETADA' AND f.created_at >= :inicio2 AND f.created_at <= :fin2
                WHERE p.estado = 1
                GROUP BY p.id, p.nombre, p.codigo_barras, p.stock, p.precio_compra, p.precio_venta, c.nombre
                ORDER BY unidades_vendidas ASC, p.stock DESC, capital_inmovilizado DESC
                LIMIT {$limit}";

        return $this->fetchAll($sql, [
            ':inicio'  => $inicio,
            ':fin'     => $fin,
            ':inicio2' => $inicio,
            ':fin2'    => $fin
        ]);
    }

    /**
     * Consolida toda la información analítica para la vista y exportación a PDF
     *
     * @param string $tipo
     * @param string $fecha
     * @param string $mes
     * @return array
     */
    public function getReporteCompleto(string $tipo = 'diario', string $fecha = '', string $mes = ''): array
    {
        $metricas = $this->getMetricasPeriodo($tipo, $fecha, $mes);
        $tendencia = $this->getTendenciaVentas($tipo, $fecha, $mes);
        $gananciasPerdidas = $this->getGananciasPerdidas($tipo, $fecha, $mes);
        $categorias = $this->getVentasPorCategoria($tipo, $fecha, $mes);
        $masVendidos = $this->getProductosMasVendidos($tipo, $fecha, $mes, 6);
        $menosVendidos = $this->getProductosMenosVendidos($tipo, $fecha, $mes, 6);

        return [
            'metricas'          => $metricas,
            'tendencia'         => $tendencia,
            'gananciasPerdidas' => $gananciasPerdidas,
            'categorias'        => $categorias,
            'masVendidos'       => $masVendidos,
            'menosVendidos'     => $menosVendidos
        ];
    }
}
