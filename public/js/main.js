/**
 * ==============================================================================
 * JAVASCRIPT GLOBAL (Vanilla JS)
 * ==============================================================================
 * Control de UI general, menú móvil, toasts interactivos y modales accesibles.
 * ==============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Manejo de Sidebar Responsivo en Móviles
  const mobileBtn = document.getElementById('mobileMenuBtn');
  const sidebar = document.getElementById('sidebar');

  if (mobileBtn && sidebar) {
    mobileBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      sidebar.classList.toggle('open');
    });

    // Cerrar al hacer clic fuera
    document.addEventListener('click', (e) => {
      if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && e.target !== mobileBtn) {
        sidebar.classList.remove('open');
      }
    });
  }
});

/**
 * Muestra un mensaje Toast emergente en la esquina inferior
 * 
 * @param {string} message Texto del mensaje
 * @param {string} type 'success' | 'danger' | 'warning' | 'info'
 * @param {number} duration Duración en milisegundos
 */
function showToast(message, type = 'info', duration = 3500) {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const icons = {
    success: '✅',
    danger: '❌',
    warning: '⚠️',
    info: 'ℹ️'
  };

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `<span>${icons[type] || 'ℹ️'}</span> <span>${message}</span>`;
  
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

/**
 * Abre un modal por su ID
 * @param {string} modalId 
 */
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

/**
 * Cierra un modal por su ID
 * @param {string} modalId 
 */
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}

/**
 * Wrapper de Fetch con CSRF Token inyectado automáticamente
 * @param {string} url 
 * @param {object} options 
 * @returns {Promise<any>}
 */
async function fetchAPI(url, options = {}) {
  const defaultHeaders = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': window.CSRF_TOKEN || ''
  };

  options.headers = {
    ...defaultHeaders,
    ...(options.headers || {})
  };

  try {
    const response = await fetch(url, options);
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error en fetchAPI:', error);
    showToast('Error de comunicación con el servidor', 'danger');
    throw error;
  }
}

/**
 * Formatea un número como moneda colombiana
 * @param {number} num 
 * @returns {string}
 */
function formatearMoneda(num) {
  return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num || 0);
}

/**
 * Dispara el comando para abrir la gaveta física (cajón monedero)
 */
function dispararAperturaGaveta() {
  let iframe = document.getElementById('printIframe');
  if (!iframe) {
    iframe = document.createElement('iframe');
    iframe.id = 'printIframe';
    iframe.style.display = 'none';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    document.body.appendChild(iframe);
  }
  iframe.src = `${window.APP_URL}/caja/pulso-gaveta`;
  showToast('🔓 Pulso enviado al cajón monedero / gaveta', 'success');
}

/**
 * Abre el modal y consulta en vivo el saldo actual en gaveta
 */
async function abrirModalEstadoGaveta() {
  openModal('modalEstadoGaveta');
  const body = document.getElementById('modalEstadoGavetaBody');
  if (!body) return;

  body.innerHTML = `
    <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
      <div style="font-size: 2rem; margin-bottom: 0.5rem;">⏳</div>
      Consultando saldo de la gaveta...
    </div>
  `;

  try {
    const res = await fetchAPI(`${window.APP_URL}/caja/estado-ajax`);
    if (res.success) {
      body.innerHTML = `
        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.85rem 1rem; margin-bottom: 1rem; font-size: 0.85rem;">
          <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <span>Turno activo: <strong>#${res.sesion_id}</strong></span>
            <span>Cajero: <strong>${res.usuario_nombre}</strong></span>
          </div>
          <div style="color: var(--text-muted); margin-top: 0.25rem;">
            Apertura: ${res.fecha_apertura}
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.92rem; margin-bottom: 1rem;">
          <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--text-muted);">🏦 Fondo Inicial:</span>
            <strong style="font-family: 'JetBrains Mono', monospace;">$ ${formatearMoneda(res.monto_inicial)}</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--text-muted);">💵 (+) Ventas Efectivo:</span>
            <strong style="font-family: 'JetBrains Mono', monospace; color: var(--success);">+ $ ${formatearMoneda(res.ventas_efectivo)}</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--text-muted);">📥 (+) Entradas Manuales:</span>
            <strong style="font-family: 'JetBrains Mono', monospace; color: var(--success);">+ $ ${formatearMoneda(res.entradas)}</strong>
          </div>
          <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--text-muted);">📤 (-) Salidas / Gastos:</span>
            <strong style="font-family: 'JetBrains Mono', monospace; color: var(--danger);">- $ ${formatearMoneda(res.salidas)}</strong>
          </div>
        </div>

        <div style="background: linear-gradient(135deg, #1e1b4b, #0f172a); color: #ffffff; border-radius: var(--radius-md); padding: 1.25rem; text-align: center;">
          <div style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em;">
            Efectivo Total Estimado en Gaveta
          </div>
          <div style="font-size: 2rem; font-weight: 900; font-family: 'JetBrains Mono', monospace; color: #10b981; margin: 0.35rem 0;">
            $ ${formatearMoneda(res.monto_esperado)}
          </div>
          <div style="font-size: 0.78rem; color: #cbd5e1;">
            Ventas electrónicas adicionales (Tarjeta/QR): $ ${formatearMoneda(res.ventas_tarjeta + res.ventas_transfer)}
          </div>
        </div>
      `;
    } else {
      body.innerHTML = `
        <div style="text-align: center; padding: 2rem; color: var(--danger);">
          <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔒</div>
          <p style="font-weight: 700;">${res.message || 'No hay ninguna sesión de caja abierta.'}</p>
          <a href="${window.APP_URL}/caja/apertura" class="btn btn-primary" style="margin-top: 1rem;">🔓 Abrir Turno de Caja</a>
        </div>
      `;
    }
  } catch (err) {
    body.innerHTML = `
      <div style="text-align: center; padding: 2rem; color: var(--danger);">
        ⚠️ Error al obtener el estado de la gaveta.
      </div>
    `;
  }
}
