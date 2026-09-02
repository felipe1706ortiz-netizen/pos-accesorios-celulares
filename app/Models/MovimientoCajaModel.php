<?php
namespace App\Models;

use App\Core\Model;
use Exception;

/**
 * ==============================================================================
 * MODELO MOVIMIENTOCAJAMODEL (MÓDULO 5: MOVIMIENTOS DE EFECTIVO)
 * ==============================================================================
 * Registra entradas (fondo, sencillo) y salidas (gastos menores, proveedores)
 * de dinero en efectivo no relacionadas directamente con ventas.
 * ==============================================================================
 */
class MovimientoCajaModel extends Model
{
    protected string $table = 'movimientos_caja';

    /**
     * Registra un ingreso o egreso de efectivo y actualiza la sesión de caja de forma atómica
     * 
     * @param int $sesionCajaId
     * @param int $usuarioId
     * @param string $tipo 'ENTRADA' | 'SALIDA'
     * @param float $monto
     * @param string $concepto
     * @param string|null $comprobante
     * @return int ID del movimiento creado
     * @throws Exception
     */
    public function registrar(
        int $sesionCajaId,
        int $usuarioId,
        string $tipo,
        float $monto,
        string $concepto,
        ?string $comprobante = null
    ): int {
        $tipo = strtoupper(trim($tipo));
        $monto = abs($monto);

        if (!in_array($tipo, ['ENTRADA', 'SALIDA'])) {
            throw new Exception("Tipo de movimiento de caja inválido: {$tipo}");
        }

        if ($monto <= 0) {
            throw new Exception("El monto debe ser superior a 0.");
        }

        if (empty($concepto)) {
            throw new Exception("El concepto o justificación del movimiento es obligatorio.");
        }

        $this->beginTransaction();

        try {
            // 1. Insertar el movimiento en la tabla
            $movimientoId = $this->insert([
                'sesion_caja_id' => $sesionCajaId,
                'usuario_id'     => $usuarioId,
                'tipo'           => $tipo,
                'monto'          => $monto,
                'concepto'       => trim($concepto),
                'comprobante'    => $comprobante ? trim($comprobante) : null
            ]);

            // 2. Actualizar acumulados y saldo esperado en sesiones_caja
            $columna = ($tipo === 'ENTRADA') ? 'total_entradas' : 'total_salidas';
            $signoEsperado = ($tipo === 'ENTRADA') ? '+' : '-';

            $sqlSesion = "UPDATE sesiones_caja 
                          SET {$columna} = {$columna} + :monto,
                              monto_esperado = monto_esperado {$signoEsperado} :monto_esp
                          WHERE id = :id";

            $this->query($sqlSesion, [
                ':monto'     => $monto,
                ':monto_esp' => $monto,
                ':id'        => $sesionCajaId
            ]);

            $this->commit();
            return $movimientoId;

        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene los movimientos de efectivo de una sesión específica
     * 
     * @param int $sesionCajaId
     * @return array
     */
    public function getPorSesion(int $sesionCajaId): array
    {
        $sql = "SELECT m.*, u.nombre AS usuario_nombre 
                FROM {$this->table} m
                INNER JOIN usuarios u ON u.id = m.usuario_id
                WHERE m.sesion_caja_id = :sesion_id
                ORDER BY m.id DESC";

        return $this->fetchAll($sql, [':sesion_id' => $sesionCajaId]);
    }

    /**
     * Obtiene el listado general de movimientos de caja con filtros
     * 
     * @param string|null $fecha
     * @param string|null $tipo
     * @param int $limit
     * @return array
     */
    public function getHistorial(?string $fecha = null, ?string $tipo = null, int $limit = 100): array
    {
        $sql = "SELECT m.*, u.nombre AS usuario_nombre, s.estado AS sesion_estado 
                FROM {$this->table} m
                INNER JOIN usuarios u ON u.id = m.usuario_id
                LEFT JOIN sesiones_caja s ON s.id = m.sesion_caja_id
                WHERE 1=1";

        $params = [];

        if (!empty($fecha)) {
            $sql .= " AND DATE(m.created_at) = :fecha";
            $params[':fecha'] = $fecha;
        }

        if (!empty($tipo)) {
            $sql .= " AND m.tipo = :tipo";
            $params[':tipo'] = strtoupper($tipo);
        }

        $sql .= " ORDER BY m.id DESC LIMIT {$limit}";

        return $this->fetchAll($sql, $params);
    }
}
