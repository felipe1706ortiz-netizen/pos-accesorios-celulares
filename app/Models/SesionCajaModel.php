<?php
namespace App\Models;

use App\Core\Model;
use Exception;

/**
 * ==============================================================================
 * MODELO SESIONCAJAMODEL (MÓDULOS 5 Y 6: CONTROL DE TURNOS Y ARQUEO DE CAJA)
 * ==============================================================================
 * Apertura de turnos, acumuladores en tiempo real, cierre de caja (ciego/admin),
 * conciliación de saldos y cálculo de diferencias (sobrantes/faltantes).
 * ==============================================================================
 */
class SesionCajaModel extends Model
{
    protected string $table = 'sesiones_caja';

    /**
     * Obtiene la sesión de caja actualmente abierta para un usuario
     * 
     * @param int $usuarioId
     * @return array|null
     */
    public function getSesionActiva(int $usuarioId): ?array
    {
        $sql = "SELECT s.*, u.nombre AS usuario_nombre 
                FROM {$this->table} s
                INNER JOIN usuarios u ON u.id = s.usuario_id
                WHERE s.usuario_id = :usuario_id AND s.estado = 'ABIERTA' 
                ORDER BY s.id DESC LIMIT 1";

        return $this->fetchOne($sql, [':usuario_id' => $usuarioId]);
    }

    /**
     * Obtiene cualquier sesión actualmente abierta en el sistema
     * @return array|null
     */
    public function getCualquierSesionAbierta(): ?array
    {
        $sql = "SELECT s.*, u.nombre AS usuario_nombre 
                FROM {$this->table} s
                INNER JOIN usuarios u ON u.id = s.usuario_id
                WHERE s.estado = 'ABIERTA' 
                ORDER BY s.id DESC LIMIT 1";

        return $this->fetchOne($sql);
    }

    /**
     * Obtiene o abre automáticamente una sesión de caja
     * 
     * @param int $usuarioId
     * @param float $montoInicial
     * @return array
     */
    public function obtenerOCrearSesion(int $usuarioId, float $montoInicial = 0.00): array
    {
        $sesion = $this->getSesionActiva($usuarioId);
        if ($sesion) {
            return $sesion;
        }

        $id = $this->abrirSesion($usuarioId, $montoInicial);
        return $this->findById($id);
    }

    /**
     * Abre formalmente una nueva sesión de caja con un monto inicial
     * 
     * @param int $usuarioId
     * @param float $montoInicial
     * @param string $notas
     * @return int ID de la nueva sesión
     * @throws Exception
     */
    public function abrirSesion(int $usuarioId, float $montoInicial = 0.00, string $notas = ''): int
    {
        $activa = $this->getSesionActiva($usuarioId);
        if ($activa) {
            throw new Exception("Ya existe una sesión de caja abierta (Turno #{$activa['id']}).");
        }

        $montoInicial = max(0, (float)$montoInicial);

        return $this->insert([
            'usuario_id'     => $usuarioId,
            'fecha_apertura' => date('Y-m-d H:i:s'),
            'monto_inicial'  => $montoInicial,
            'monto_esperado' => $montoInicial,
            'estado'         => 'ABIERTA',
            'notas'          => trim($notas)
        ]);
    }

    /**
     * Cierra formalmente la sesión de caja (Arqueo)
     * 
     * @param int $sesionId
     * @param float $montoReal Efectivo físico contado por el cajero
     * @param string $notas Observaciones de cierre
     * @return array Datos consolidados del cierre de caja
     * @throws Exception
     */
    public function cerrarSesion(int $sesionId, float $montoReal, string $notas = ''): array
    {
        $sesion = $this->findById($sesionId);
        if (!$sesion) {
            throw new Exception("Sesión de caja no encontrada.");
        }

        if ($sesion['estado'] === 'CERRADA') {
            throw new Exception("La sesión de caja #{$sesionId} ya fue cerrada previamente.");
        }

        // Saldo teórico esperado en efectivo: Base Inicial + Ventas Efectivo + Entradas - Salidas
        $montoInicial = (float)$sesion['monto_inicial'];
        $ventasEfectivo = (float)$sesion['total_ventas_efectivo'];
        $entradas = (float)$sesion['total_entradas'];
        $salidas = (float)$sesion['total_salidas'];

        $montoEsperado = ($montoInicial + $ventasEfectivo + $entradas) - $salidas;
        $montoReal = max(0, (float)$montoReal);
        $diferencia = $montoReal - $montoEsperado;

        $fechaCierre = date('Y-m-d H:i:s');

        $this->update($sesionId, [
            'fecha_cierre'   => $fechaCierre,
            'monto_esperado' => $montoEsperado,
            'monto_real'     => $montoReal,
            'diferencia'     => $diferencia,
            'estado'         => 'CERRADA',
            'notas'          => trim($notas)
        ]);

        return array_merge($sesion, [
            'fecha_cierre'   => $fechaCierre,
            'monto_esperado' => $montoEsperado,
            'monto_real'     => $montoReal,
            'diferencia'     => $diferencia,
            'estado'         => 'CERRADA',
            'notas'          => trim($notas)
        ]);
    }

    /**
     * Acumula el valor de una venta en los totales de la sesión de caja
     * 
     * @param int $sesionId
     * @param float $monto
     * @param string $metodoPago
     */
    public function acumularVenta(int $sesionId, float $monto, string $metodoPago): void
    {
        $columna = 'total_ventas_efectivo';
        $metodo = strtoupper($metodoPago);

        if ($metodo === 'TARJETA') {
            $columna = 'total_ventas_tarjeta';
        } elseif ($metodo === 'TRANSFERENCIA') {
            $columna = 'total_ventas_transferencia';
        }

        $incrementoEsperado = ($metodo === 'EFECTIVO') ? ", monto_esperado = monto_esperado + :monto_esp" : "";

        $sql = "UPDATE {$this->table} 
                SET {$columna} = {$columna} + :monto
                    {$incrementoEsperado}
                WHERE id = :id";

        $params = [
            ':monto' => $monto,
            ':id'    => $sesionId
        ];

        if ($metodo === 'EFECTIVO') {
            $params[':monto_esp'] = $monto;
        }

        $this->query($sql, $params);
    }

    /**
     * Obtiene los datos consolidados completos de una sesión (con cajero y movimientos)
     * 
     * @param int $sesionId
     * @return array|null
     */
    public function getSesionCompleta(int $sesionId): ?array
    {
        $sql = "SELECT s.*, u.nombre AS usuario_nombre, u.usuario AS usuario_username 
                FROM {$this->table} s
                INNER JOIN usuarios u ON u.id = s.usuario_id
                WHERE s.id = :id LIMIT 1";

        $sesion = $this->fetchOne($sql, [':id' => $sesionId]);
        if (!$sesion) {
            return null;
        }

        $movModel = new MovimientoCajaModel();
        $sesion['movimientos'] = $movModel->getPorSesion($sesionId);

        return $sesion;
    }

    /**
     * Obtiene el historial de sesiones de caja (turnos pasados) para auditoría
     * 
     * @param int $limit
     * @return array
     */
    public function getHistorial(int $limit = 50): array
    {
        $sql = "SELECT s.*, u.nombre AS usuario_nombre 
                FROM {$this->table} s
                INNER JOIN usuarios u ON u.id = s.usuario_id
                ORDER BY s.id DESC LIMIT {$limit}";

        return $this->fetchAll($sql);
    }
}
