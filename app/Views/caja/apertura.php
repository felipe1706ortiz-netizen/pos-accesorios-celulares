<!-- ==============================================================================
     VISTA: APERTURA DE TURNO DE CAJA (Sober SaaS)
     ============================================================================== -->

<div class="panel" style="max-width: 500px; margin: 2rem auto; padding: 2rem;">
  <div style="text-align: center; margin-bottom: 1.75rem;">
    <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); background: var(--bg-muted); border: 1px solid var(--border-color); color: var(--text-main); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.85rem;">
      <svg style="width: 22px; height: 22px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="4" width="20" height="16" rx="2"></rect>
        <line x1="6" y1="12" x2="18" y2="12"></line>
        <line x1="12" y1="10" x2="12" y2="14"></line>
      </svg>
    </div>
    <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-main); letter-spacing: -0.02em;">Apertura de Turno de Caja</h2>
    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
      Ingrese el monto de base en efectivo para iniciar el turno.
    </p>
  </div>

  <form action="<?= APP_URL ?>/caja/abrir" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="form-group" style="margin-bottom: 1.25rem;">
      <label class="form-label" for="monto_inicial" style="font-weight: 600; text-align: center; display: block;">Monto / Fondo Inicial ($) *</label>
      <input 
        type="number" 
        step="any" 
        min="0" 
        id="monto_inicial" 
        name="monto_inicial" 
        class="form-control form-control-lg" 
        style="font-size: 1.75rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--text-main); text-align: center;"
        placeholder="100000" 
        value="0"
        required 
        autofocus
        oninput="actualizarFormatoApertura(this.value)"
      >
      <div style="margin-top: 0.4rem; text-align: center; font-size: 0.88rem; color: var(--text-muted);">
        Base digitada: <strong id="montoInicialFormateado" style="font-variant-numeric: tabular-nums; color: var(--text-main); font-size: 1.1rem; font-weight: 700;">$ 0</strong>
      </div>
    </div>

    <!-- Billetes rápidos de base sugerida -->
    <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.45rem;">
      Bases Frecuentes Sugeridas:
    </div>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.4rem; margin-bottom: 1.25rem;">
      <button type="button" class="btn btn-outline btn-sm" style="font-variant-numeric: tabular-nums;" onclick="establecerMontoApertura(50000)">$ 50.000</button>
      <button type="button" class="btn btn-outline btn-sm" style="font-variant-numeric: tabular-nums;" onclick="establecerMontoApertura(100000)">$ 100.000</button>
      <button type="button" class="btn btn-outline btn-sm" style="font-variant-numeric: tabular-nums;" onclick="establecerMontoApertura(150000)">$ 150.000</button>
      <button type="button" class="btn btn-outline btn-sm" style="font-variant-numeric: tabular-nums;" onclick="establecerMontoApertura(200000)">$ 200.000</button>
      <button type="button" class="btn btn-outline btn-sm" style="font-variant-numeric: tabular-nums;" onclick="establecerMontoApertura(300000)">$ 300.000</button>
      <button type="button" class="btn btn-outline btn-sm" style="color: var(--danger);" onclick="establecerMontoApertura(0)">Limpiar</button>
    </div>

    <div class="form-group" style="margin-bottom: 1.5rem;">
      <label class="form-label" for="notas">Observaciones de Apertura (Opcional):</label>
      <textarea id="notas" name="notas" class="form-control" rows="2" placeholder="ej: Turno de la mañana..."></textarea>
    </div>

    <div style="display: flex; gap: 0.75rem;">
      <button type="button" onclick="abrirModalEstadoGaveta()" class="btn btn-outline" style="flex: 1;">
        <span>Ver Caja</span>
      </button>
      <button type="submit" class="btn btn-primary" style="flex: 2; height: 42px; font-weight: 600;">
        <span>Confirmar y Entrar al POS</span>
      </button>
    </div>
  </form>
</div>

<script>
  function actualizarFormatoApertura(val) {
    const display = document.getElementById('montoInicialFormateado');
    const num = parseFloat(val) || 0;
    if (display) {
      display.textContent = `$ ${new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num)}`;
    }
  }

  function establecerMontoApertura(monto) {
    const input = document.getElementById('monto_inicial');
    if (input) {
      input.value = monto;
      actualizarFormatoApertura(monto);
      input.focus();
    }
  }
</script>
