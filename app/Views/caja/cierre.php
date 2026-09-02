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

<div class="card" style="margin-bottom: 1.5rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h2 style="font-size: 1.45rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">
        Arqueo y Cierre del Turno #<?= $sesion['id'] ?>
      </h2>
      <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 0.35rem;">
        Cajero responsable: <strong style="color: var(--text-main);"><?= htmlspecialchars($sesion['usuario_nombre'] ?? $currentUser['nombre']) ?></strong> • Apertura: <strong><?= date('d/m/Y H:i', strtotime($sesion['fecha_apertura'])) ?></strong>
      </p>
    </div>
    <div>
      <a href="<?= APP_URL ?>/caja" class="btn btn-outline" style="font-weight: 700;">
        <span>⬅️</span> <span>Cancelar y Volver</span>
      </a>
    </div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

  <!-- COLUMNA IZQUIERDA: RESUMEN FINANCIERO Y VENTAS POR MÉTODO -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    
    <!-- DESGLOSE DEL BALANCE EN EFECTIVO -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <svg style="width: 20px; height: 20px; color: var(--primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
          <span>Desglose Teórico del Turno</span>
        </h3>
      </div>
      
      <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.95rem;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">🏦 Fondo / Base Inicial:</span>
          <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">$ <?= number_format($montoInicial, 0, ',', '.') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">(+) Ventas en Efectivo:</span>
          <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--success);">+ $ <?= number_format($ventasEf, 0, ',', '.') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">(+) Entradas Manuales:</span>
          <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--info);">+ $ <?= number_format($entradas, 0, ',', '.') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">(-) Salidas / Gastos:</span>
          <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--danger);">- $ <?= number_format($salidas, 0, ',', '.') ?></span>
        </div>

        <div style="border-top: 2px solid var(--border-color); padding-top: 1rem; margin-top: 0.35rem;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 800; font-size: 1.05rem; color: var(--text-main);">SALDO ESPERADO EN GAVETA:</span>
            <span id="displaySaldoEsperado" data-saldo="<?= $saldoEsperado ?>" style="font-size: 1.65rem; font-weight: 900; font-family: 'JetBrains Mono', monospace; color: var(--primary);">
              $ <?= number_format($saldoEsperado, 0, ',', '.') ?>
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- VENTAS ELECTRÓNICAS -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <svg style="width: 20px; height: 20px; color: var(--info);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
          <span>Medios de Pago Electrónicos (Informativo)</span>
        </h3>
      </div>
      <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.92rem;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">💳 Datáfono / Tarjetas:</span>
          <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">$ <?= number_format($ventasTarj, 0, ',', '.') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted);">📱 Transferencia / QR:</span>
          <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">$ <?= number_format($ventasTransf, 0, ',', '.') ?></span>
        </div>
        <div style="border-top: 1.5px solid var(--border-color); padding-top: 0.75rem; display: flex; justify-content: space-between; font-weight: 800;">
          <span>Total Facturado Global:</span>
          <span style="font-family: 'JetBrains Mono', monospace; color: var(--primary); font-size: 1.15rem;">$ <?= number_format($ventasEf + $ventasTarj + $ventasTransf, 0, ',', '.') ?></span>
        </div>
      </div>
    </div>

  </div>

  <!-- COLUMNA DERECHA: CONTEO FÍSICO Y FORMULARIO DE CIERRE -->
  <div>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <svg style="width: 20px; height: 20px; color: var(--success);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <span>Conteo Físico de Efectivo en Gaveta</span>
        </h3>
      </div>

      <form action="<?= APP_URL ?>/caja/cerrar" method="POST" id="formCierreCaja">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="sesion_id" value="<?= $sesion['id'] ?>">

        <!-- CALCULADORA DE DENOMINACIONES DE BILLETES -->
        <div style="background: #f8fafc; border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.15rem; margin-bottom: 1.5rem;">
          <div style="font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.85rem;">
            🔢 Desglose por Denominación (Opcional)
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; font-size: 0.88rem;">
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 80px; font-weight: 700; color: var(--text-secondary);">$ 100.000:</span>
              <input type="number" min="0" class="form-control denom-input" data-valor="100000" placeholder="0" style="padding: 0.4rem 0.6rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 80px; font-weight: 700; color: var(--text-secondary);">$ 50.000:</span>
              <input type="number" min="0" class="form-control denom-input" data-valor="50000" placeholder="0" style="padding: 0.4rem 0.6rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 80px; font-weight: 700; color: var(--text-secondary);">$ 20.000:</span>
              <input type="number" min="0" class="form-control denom-input" data-valor="20000" placeholder="0" style="padding: 0.4rem 0.6rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 80px; font-weight: 700; color: var(--text-secondary);">$ 10.000:</span>
              <input type="number" min="0" class="form-control denom-input" data-valor="10000" placeholder="0" style="padding: 0.4rem 0.6rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 80px; font-weight: 700; color: var(--text-secondary);">$ 5.000:</span>
              <input type="number" min="0" class="form-control denom-input" data-valor="5000" placeholder="0" style="padding: 0.4rem 0.6rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
              <span style="width: 80px; font-weight: 700; color: var(--text-secondary);">Monedas:</span>
              <input type="number" min="0" class="form-control" id="inputMonedasDirecto" placeholder="0" style="padding: 0.4rem 0.6rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
            </div>
          </div>
        </div>

        <!-- INPUT DEL TOTAL REAL CONTADO -->
        <div class="form-group">
          <label class="form-label" for="monto_real" style="font-weight: 800; font-size: 1.05rem;">Total Efectivo Contado en Gaveta ($) *</label>
          <input 
            type="number" 
            step="100" 
            min="0" 
            id="monto_real" 
            name="monto_real" 
            class="form-control form-control-lg" 
            style="font-size: 1.85rem; font-family: 'JetBrains Mono', monospace; font-weight: 900; color: var(--text-main);" 
            placeholder="ej: <?= (int)$saldoEsperado ?>" 
            required 
            autofocus
          >
          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem; flex-wrap: wrap; gap: 0.5rem; font-size: 0.9rem;">
            <div style="color: var(--text-muted); font-size: 0.92rem;">
              Efectivo digitado: <strong id="montoRealFormateado" style="font-family: 'JetBrains Mono', monospace; color: var(--text-main); font-size: 1.1rem; font-weight: 800;">$ 0</strong>
            </div>
            <div style="display: flex; gap: 0.4rem;">
              <button type="button" class="btn btn-outline btn-sm" style="font-size: 0.82rem; font-weight: 700; color: var(--primary);" onclick="establecerSaldoEsperado()">
                ⚡ Copiar Saldo Esperado ($ <?= number_format($saldoEsperado, 0, ',', '.') ?>)
              </button>
              <button type="button" class="btn btn-outline btn-sm" style="font-size: 0.82rem;" onclick="document.getElementById('monto_real').value = 0; document.getElementById('monto_real').dispatchEvent(new Event('input'));">
                Limpiar
              </button>
            </div>
          </div>
        </div>

        <!-- RECUADRO DE CONCILIACIÓN Y DIFERENCIA EN VIVO -->
        <div id="cajaDiferenciaBox" style="border: 2px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.15rem 1.35rem; margin-bottom: 1.5rem; background: #f8fafc; transition: all 0.25s ease;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 800; font-size: 1rem;">Diferencia de Caja:</span>
            <span id="displayDiferencia" style="font-size: 1.45rem; font-weight: 900; font-family: 'JetBrains Mono', monospace;">$ 0</span>
          </div>
          <div id="displayDiferenciaTexto" style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.35rem;">
            Ingrese el efectivo contado para calcular sobrante o faltante.
          </div>
        </div>

        <!-- NOTAS / OBSERVACIONES -->
        <div class="form-group">
          <label class="form-label" for="notas_cierre">Observaciones de Cierre (Opcional):</label>
          <textarea id="notas_cierre" name="notas" class="form-control" rows="2" placeholder="Justificación de sobrantes/faltantes o novedades del turno..."></textarea>
        </div>

        <button type="submit" class="btn btn-danger btn-lg btn-block" style="margin-top: 1.25rem; font-weight: 800;" onclick="return confirm('¿Está seguro de cerrar definitivamente este turno de caja?')">
          <span>🔒</span> <span>Confirmar Cierre e Imprimir Arqueo</span>
        </button>

      </form>
    </div>
  </div>

</div>
