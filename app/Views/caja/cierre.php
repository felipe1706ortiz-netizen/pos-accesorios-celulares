<!-- ==============================================================================
     VISTA: CIERRE DE CAJA Y ARQUEO DIARIO (MÓDULO 6 - UI/UX PRO MAX)
     ============================================================================== -->

<?php
  $montoInicial = (float)$sesion['monto_inicial'];
  $ventasEf = (float)$sesion['total_ventas_efectivo'];
  $ventasTarj = (float)$sesion['total_ventas_tarjeta'];
  $ventasTransf = (float)$sesion['total_ventas_transferencia'];
  $entradas = (float)$sesion['total_entradas'];
  $salidas = (float)$sesion['total_salidas'];
  $saldoEsperado = (float)$sesion['monto_esperado'];
?>

<div class="panel" style="margin-bottom: 1.25rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em; margin: 0;">
        Arqueo y Cierre del Turno #<?= $sesion['id'] ?>
      </h2>
      <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
        Cajero responsable: <strong style="color: var(--text-main);"><?= htmlspecialchars($sesion['usuario_nombre'] ?? $currentUser['nombre']) ?></strong> &bull; Apertura: <strong><?= date('d/m/Y H:i', strtotime($sesion['fecha_apertura'])) ?></strong>
      </p>
    </div>
    <div>
      <a href="<?= APP_URL ?>/caja" class="btn btn-outline btn-sm">
        &larr; Volver
      </a>
    </div>
  </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.25rem;">

  <!-- COLUMNA IZQUIERDA: RESUMEN FINANCIERO Y VENTAS POR MÉTODO -->
  <div style="display: flex; flex-direction: column; gap: 1.25rem;">
    
    <!-- DESGLOSE DEL BALANCE EN EFECTIVO -->
    <div class="panel">
      <div class="panel-header">
        <div class="panel-title">
          <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="1" x2="12" y2="23"></line>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
          </svg>
          <span>Desglose Teórico del Turno</span>
        </div>
      </div>
      
      <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.88rem;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">Fondo / Base Inicial:</span>
          <span style="font-variant-numeric: tabular-nums; font-weight: 600;">$ <?= number_format($montoInicial, 0, ',', '.') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">(+) Ventas en Efectivo:</span>
          <span style="font-variant-numeric: tabular-nums; font-weight: 600; color: var(--success);">+ $ <?= number_format($ventasEf, 0, ',', '.') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">(+) Entradas Manuales:</span>
          <span style="font-variant-numeric: tabular-nums; font-weight: 600; color: var(--info);">+ $ <?= number_format($entradas, 0, ',', '.') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">(-) Salidas / Gastos:</span>
          <span style="font-variant-numeric: tabular-nums; font-weight: 600; color: var(--danger);">- $ <?= number_format($salidas, 0, ',', '.') ?></span>
        </div>

        <div style="border-top: 1px solid var(--border-color); padding-top: 0.85rem; margin-top: 0.25rem;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 600; font-size: 0.95rem; color: var(--text-main);">SALDO ESPERADO EN GAVETA:</span>
            <span id="displaySaldoEsperado" data-saldo="<?= $saldoEsperado ?>" style="font-size: 1.45rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--text-main);">
              $ <?= number_format($saldoEsperado, 0, ',', '.') ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- VENTAS ELECTRÓNICAS -->
    <div class="panel">
      <div class="panel-header">
        <div class="panel-title">
          <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
            <line x1="1" y1="10" x2="23" y2="10"></line>
          </svg>
          <span>Medios de Pago Electrónicos</span>
        </div>
      </div>
      <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.88rem;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">Datáfono / Tarjetas:</span>
          <span style="font-variant-numeric: tabular-nums; font-weight: 600;">$ <?= number_format($ventasTarj, 0, ',', '.') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">Transferencia / QR:</span>
          <span style="font-variant-numeric: tabular-nums; font-weight: 600;">$ <?= number_format($ventasTransf, 0, ',', '.') ?></span>
        </div>
        <div style="border-top: 1px solid var(--border-color); padding-top: 0.65rem; display: flex; justify-content: space-between; font-weight: 600;">
          <span>Total Facturado Global:</span>
          <span style="font-variant-numeric: tabular-nums; color: var(--text-main); font-size: 1.05rem;">$ <?= number_format($ventasEf + $ventasTarj + $ventasTransf, 0, ',', '.') ?></span>
        </div>
      </div>
    </div>

  </div>

  <!-- COLUMNA DERECHA: CONTEO FÍSICO Y FORMULARIO DE CIERRE -->
  <div>
    <div class="panel">
      <div class="panel-header">
        <div class="panel-title">
          <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="6" width="20" height="12" rx="2"></rect>
            <circle cx="12" cy="12" r="2"></circle>
          </svg>
          <span>Conteo Físico de Efectivo en Gaveta</span>
        </div>
      </div>

      <form action="<?= APP_URL ?>/caja/cerrar" method="POST" id="formCierreCaja">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="sesion_id" value="<?= $sesion['id'] ?>">

        <!-- CALCULADORA DE DENOMINACIONES DE BILLETES -->
        <div style="background: var(--bg-muted); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.25rem;">
          <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">
            Desglose por Denominación (Opcional)
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.85rem;">
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 75px; font-weight: 600; color: var(--text-secondary);">$ 100.000:</span>
              <input type="number" min="0" class="form-control denom-input" data-valor="100000" placeholder="0" style="padding: 0.35rem 0.5rem; font-variant-numeric: tabular-nums;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 75px; font-weight: 600; color: var(--text-secondary);">$ 50.000:</span>
              <input type="number" min="0" class="form-control denom-input" data-valor="50000" placeholder="0" style="padding: 0.35rem 0.5rem; font-variant-numeric: tabular-nums;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 75px; font-weight: 600; color: var(--text-secondary);">$ 20.000:</span>
              <input type="number" min="0" class="form-control denom-input" data-valor="20000" placeholder="0" style="padding: 0.35rem 0.5rem; font-variant-numeric: tabular-nums;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 75px; font-weight: 600; color: var(--text-secondary);">$ 10.000:</span>
              <input type="number" min="0" class="form-control denom-input" data-valor="10000" placeholder="0" style="padding: 0.35rem 0.5rem; font-variant-numeric: tabular-nums;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 75px; font-weight: 600; color: var(--text-secondary);">$ 5.000:</span>
              <input type="number" min="0" class="form-control denom-input" data-valor="5000" placeholder="0" style="padding: 0.35rem 0.5rem; font-variant-numeric: tabular-nums;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 75px; font-weight: 600; color: var(--text-secondary);">Monedas:</span>
              <input type="number" min="0" class="form-control" id="inputMonedasDirecto" placeholder="0" style="padding: 0.35rem 0.5rem; font-variant-numeric: tabular-nums;">
            </div>
          </div>
        </div>

        <!-- INPUT DEL TOTAL REAL CONTADO -->
        <div class="form-group" style="margin-bottom: 1rem;">
          <label class="form-label" for="monto_real">Total Efectivo Contado en Gaveta ($) *</label>
          <input 
            type="number" 
            step="any" 
            min="0" 
            id="monto_real" 
            name="monto_real" 
            class="form-control form-control-lg" 
            style="font-size: 1.6rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--text-main);" 
            placeholder="<?= (int)$saldoEsperado ?>" 
            required 
            autofocus
          >
          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.4rem; flex-wrap: wrap; gap: 0.5rem; font-size: 0.85rem;">
            <div style="color: var(--text-muted);">
              Efectivo digitado: <strong id="montoRealFormateado" style="font-variant-numeric: tabular-nums; color: var(--text-main); font-weight: 600;">$ 0</strong>
            </div>
            <div style="display: flex; gap: 0.35rem;">
              <button type="button" class="btn btn-outline btn-sm" onclick="establecerSaldoEsperado()">
                Copiar Saldo Esperado ($ <?= number_format($saldoEsperado, 0, ',', '.') ?>)
              </button>
              <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('monto_real').value = 0; document.getElementById('monto_real').dispatchEvent(new Event('input'));">
                Limpiar
              </button>
            </div>
          </div>
        </div>

        <!-- RECUADRO DE CONCILIACIÓN Y DIFERENCIA EN VIVO -->
        <div id="cajaDiferenciaBox" style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem 1.25rem; margin-bottom: 1.25rem; background: var(--bg-muted); transition: all 0.2s ease;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 600; font-size: 0.95rem;">Diferencia de Caja:</span>
            <span id="displayDiferencia" style="font-size: 1.35rem; font-weight: 700; font-variant-numeric: tabular-nums;">$ 0</span>
          </div>
          <div id="displayDiferenciaTexto" style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">
            Ingrese el efectivo contado para calcular sobrante o faltante.
          </div>
        </div>

        <!-- NOTAS / OBSERVACIONES -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
          <label class="form-label" for="notas_cierre">Observaciones de Cierre (Opcional):</label>
          <textarea id="notas_cierre" name="notas" class="form-control" rows="2" placeholder="Justificación de novedades..."></textarea>
        </div>

        <button type="submit" class="btn btn-danger btn-block" style="width: 100%; height: 42px; font-weight: 600;" onclick="return confirm('¿Está seguro de cerrar definitivamente este turno de caja?')">
          <span>Confirmar Cierre e Imprimir Arqueo</span>
        </button>

      </form>
    </div>
  </div>

</div>
