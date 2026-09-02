<!-- ==============================================================================
     VISTA: APERTURA DE TURNO DE CAJA (UI/UX PRO MAX)
     ============================================================================== -->

<div class="card" style="max-width: 560px; margin: 2.5rem auto; padding: 2.75rem 2.5rem; border-radius: var(--radius-xl); box-shadow: var(--shadow-lg);">
  <div style="text-align: center; margin-bottom: 2rem;">
    <div style="width: 64px; height: 64px; border-radius: var(--radius-lg); background: linear-gradient(135deg, #6366f1 0%, #3b82f6 50%, #10b981 100%); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem; box-shadow: 0 8px 24px rgba(99, 102, 241, 0.35);">
      🔓
    </div>
    <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">Apertura de Turno de Caja</h2>
    <p style="font-size: 0.92rem; color: var(--text-muted); margin-top: 0.35rem; line-height: 1.5;">
      Ingrese el monto de dinero base en efectivo con el que inicia operaciones el cajero.
    </p>

    <!-- Botón para abrir gaveta física -->
    <div style="margin-top: 1.25rem;">
      <button type="button" class="btn btn-outline btn-sm" onclick="dispararAperturaGaveta()" style="font-weight: 800; color: #0284c7; border-color: #bae6fd; background: #f0f9ff; padding: 0.55rem 1.15rem;">
        <span>🔓</span> <span>Abrir Gaveta Física (Cajón Monedero)</span>
      </button>
    </div>
  </div>

  <form action="<?= APP_URL ?>/caja/abrir" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

    <div class="form-group" style="margin-bottom: 1.5rem;">
      <label class="form-label" for="monto_inicial" style="font-weight: 800; font-size: 1.05rem;">Monto / Fondo Inicial ($) *</label>
      <input 
        type="number" 
        step="100" 
        min="0" 
        id="monto_inicial" 
        name="monto_inicial" 
        class="form-control form-control-lg" 
        style="font-size: 2rem; font-family: 'JetBrains Mono', monospace; font-weight: 900; color: var(--text-main); text-align: center;"
        placeholder="ej: 100000" 
        value="0"
        required 
        autofocus
        oninput="actualizarFormatoApertura(this.value)"
      >
      <div style="margin-top: 0.5rem; text-align: center; font-size: 0.95rem; color: var(--text-muted);">
        Base digitada: <strong id="montoInicialFormateado" style="font-family: 'JetBrains Mono', monospace; color: var(--primary); font-size: 1.25rem; font-weight: 900;">$ 0</strong>
      </div>
    </div>

    <!-- Billetes rápidos de base sugerida -->
    <div style="font-size: 0.78rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">
      Bases Frecuentes Sugeridas:
    </div>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-bottom: 1.5rem;">
      <button type="button" class="btn btn-outline" style="font-size: 0.85rem; font-weight: 800; font-family: 'JetBrains Mono', monospace;" onclick="establecerMontoApertura(50000)">$ 50.000</button>
      <button type="button" class="btn btn-outline" style="font-size: 0.85rem; font-weight: 800; font-family: 'JetBrains Mono', monospace;" onclick="establecerMontoApertura(100000)">$ 100.000</button>
      <button type="button" class="btn btn-outline" style="font-size: 0.85rem; font-weight: 800; font-family: 'JetBrains Mono', monospace;" onclick="establecerMontoApertura(150000)">$ 150.000</button>
      <button type="button" class="btn btn-outline" style="font-size: 0.85rem; font-weight: 800; font-family: 'JetBrains Mono', monospace;" onclick="establecerMontoApertura(200000)">$ 200.000</button>
      <button type="button" class="btn btn-outline" style="font-size: 0.85rem; font-weight: 800; font-family: 'JetBrains Mono', monospace;" onclick="establecerMontoApertura(300000)">$ 300.000</button>
      <button type="button" class="btn btn-outline" style="font-size: 0.85rem; font-weight: 700; color: var(--danger);" onclick="establecerMontoApertura(0)">Limpiar (0)</button>
    </div>

    <div class="form-group" style="margin-bottom: 1.5rem;">
      <label class="form-label" for="notas">Observaciones de Apertura (Opcional):</label>
      <textarea id="notas" name="notas" class="form-control" rows="2" placeholder="ej: Turno de la mañana, base entregada por supervisor..."></textarea>
    </div>

    <div style="display: flex; gap: 0.85rem; margin-top: 1.5rem;">
      <a href="<?= APP_URL ?>/caja" class="btn btn-outline" style="flex: 1; font-weight: 700;">
        <span>💵</span> <span>Ver Caja</span>
      </a>
      <button type="submit" class="btn btn-primary btn-lg" style="flex: 2; font-weight: 800;">
        <span>🔓</span> <span>Confirmar y Entrar al POS</span>
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
