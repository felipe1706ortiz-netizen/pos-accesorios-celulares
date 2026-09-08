/**
 * ==============================================================================
 * DASHBOARD ANALÍTICO & GRÁFICOS INTERACTIVOS (UI/UX PRO MAX)
 * ==============================================================================
 * Inicialización de gráficos Chart.js, control de filtros diario/mensual,
 * formateo monetario COP y exportación ejecutiva a PDF.
 * ==============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
  initDashboardCharts();
  initFilterControls();
});

let chartVentasInstance = null;
let chartPnLInstance = null;
let chartCategoriasInstance = null;
let chartTopProdInstance = null;

/**
 * Formateador de moneda colombiana COP
 */
function formatCOP(value) {
  return '$ ' + Number(value || 0).toLocaleString('es-CO', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  });
}

/**
 * Inicializa todos los gráficos con los datos provistos desde el servidor
 */
function initDashboardCharts() {
  if (typeof Chart === 'undefined') {
    console.warn('Chart.js no está disponible aún.');
    return;
  }

  // Configuración global de fuentes y colores para Chart.js
  Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
  Chart.defaults.color = '#64748b';
  Chart.defaults.plugins.tooltip.padding = 10;
  Chart.defaults.plugins.tooltip.cornerRadius = 8;
  Chart.defaults.plugins.tooltip.titleFont = { weight: 'bold', size: 12 };

  const reportData = window.DASHBOARD_DATA || {};
  const tendencia = reportData.tendencia || { labels: [], ventas: [], ganancias: [], facturas: [] };
  const pnl = reportData.gananciasPerdidas || { labels: [], data: [], colors: [] };
  const categorias = reportData.categorias || { labels: [], ventas: [] };
  const masVendidos = reportData.masVendidos || [];

  // ----------------------------------------------------------------------------
  // 1. GRÁFICO DE TENDENCIA DE VENTAS Y GANANCIAS (Línea / Curva Spline con Área)
  // ----------------------------------------------------------------------------
  const ctxVentas = document.getElementById('chartVentas');
  if (ctxVentas) {
    const gradientVentas = ctxVentas.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradientVentas.addColorStop(0, 'rgba(79, 70, 229, 0.28)');
    gradientVentas.addColorStop(1, 'rgba(79, 70, 229, 0.01)');

    const gradientGanancias = ctxVentas.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradientGanancias.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
    gradientGanancias.addColorStop(1, 'rgba(16, 185, 129, 0.01)');

    chartVentasInstance = new Chart(ctxVentas, {
      type: 'line',
      data: {
        labels: tendencia.labels,
        datasets: [
          {
            label: 'Ventas Totales ($)',
            data: tendencia.ventas,
            borderColor: '#4f46e5',
            backgroundColor: gradientVentas,
            borderWidth: 2.5,
            fill: true,
            tension: 0.35,
            pointBackgroundColor: '#4f46e5',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
          },
          {
            label: 'Ganancia Neta ($)',
            data: tendencia.ganancias,
            borderColor: '#10b981',
            backgroundColor: gradientGanancias,
            borderWidth: 2,
            fill: true,
            tension: 0.35,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 3,
            pointHoverRadius: 5
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              boxWidth: 8,
              font: { weight: '600', size: 11 }
            }
          },
          tooltip: {
            callbacks: {
              label: (context) => ` ${context.dataset.label}: ${formatCOP(context.raw)}`
            }
          }
        },
        scales: {
          x: {
            grid: { color: '#f1f5f9' },
            ticks: { font: { size: 10, weight: '500' } }
          },
          y: {
            grid: { color: '#f1f5f9' },
            ticks: {
              font: { size: 10, weight: '500' },
              callback: (val) => formatCOP(val)
            },
            beginAtZero: true
          }
        }
      }
    });
  }

  // ----------------------------------------------------------------------------
  // 2. GRÁFICO DE GANANCIAS VS COSTOS Y PÉRDIDAS (Barras Agrupadas)
  // ----------------------------------------------------------------------------
  const ctxPnL = document.getElementById('chartGananciasPerdidas');
  if (ctxPnL) {
    chartPnLInstance = new Chart(ctxPnL, {
      type: 'bar',
      data: {
        labels: pnl.labels,
        datasets: [{
          label: 'Monto ($ COP)',
          data: pnl.data,
          backgroundColor: pnl.colors || ['#4f46e5', '#f59e0b', '#10b981', '#f43f5e'],
          borderRadius: 6,
          borderSkipped: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (context) => ` Total: ${formatCOP(context.raw)}`
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { font: { size: 10, weight: '600' } }
          },
          y: {
            grid: { color: '#f1f5f9' },
            ticks: {
              font: { size: 10 },
              callback: (val) => formatCOP(val)
            },
            beginAtZero: true
          }
        }
      }
    });
  }

  // ----------------------------------------------------------------------------
  // 3. GRÁFICO DE VENTAS POR CATEGORÍA (Donut Moderno)
  // ----------------------------------------------------------------------------
  const ctxCat = document.getElementById('chartCategorias');
  if (ctxCat) {
    const palette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#64748b'];
    
    // Si no hay ventas, pintar una sección gris representativa
    const hasData = categorias.ventas.some(v => v > 0);
    const chartLabels = hasData ? categorias.labels : ['Sin Ventas Aún'];
    const chartData = hasData ? categorias.ventas : [1];
    const chartColors = hasData ? palette.slice(0, categorias.labels.length) : ['#e2e8f0'];

    chartCategoriasInstance = new Chart(ctxCat, {
      type: 'doughnut',
      data: {
        labels: chartLabels,
        datasets: [{
          data: chartData,
          backgroundColor: chartColors,
          borderWidth: 2,
          borderColor: '#ffffff',
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              usePointStyle: true,
              boxWidth: 8,
              font: { size: 10, weight: '600' },
              padding: 12
            }
          },
          tooltip: {
            enabled: hasData,
            callbacks: {
              label: (context) => {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const val = context.raw;
                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                return ` ${context.label}: ${formatCOP(val)} (${pct}%)`;
              }
            }
          }
        }
      }
    });
  }

  // ----------------------------------------------------------------------------
  // 4. GRÁFICO DE PARTICIPACIÓN TOP PRODUCTOS (Barras Horizontales)
  // ----------------------------------------------------------------------------
  const ctxTopProd = document.getElementById('chartTopProductos');
  if (ctxTopProd) {
    const topLabels = masVendidos.slice(0, 5).map(p => {
      const name = p.nombre || '';
      return name.length > 20 ? name.substring(0, 18) + '...' : name;
    });
    const topIngresos = masVendidos.slice(0, 5).map(p => Number(p.total_ingresos || 0));

    chartTopProdInstance = new Chart(ctxTopProd, {
      type: 'bar',
      data: {
        labels: topLabels.length > 0 ? topLabels : ['Sin registros'],
        datasets: [{
          label: 'Ingresos ($ COP)',
          data: topIngresos.length > 0 ? topIngresos : [0],
          backgroundColor: '#0ea5e9',
          borderRadius: 6
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (context) => ` Ingresos: ${formatCOP(context.raw)}`
            }
          }
        },
        scales: {
          x: {
            grid: { color: '#f1f5f9' },
            ticks: {
              font: { size: 9 },
              callback: (val) => formatCOP(val)
            },
            beginAtZero: true
          },
          y: {
            grid: { display: false },
            ticks: { font: { size: 10, weight: '600' } }
          }
        }
      }
    });
  }
}

/**
 * Control y sincronización de los filtros de período (Diario vs Mensual)
 */
function initFilterControls() {
  const formFiltro = document.getElementById('formFiltroPeriodo');
  if (!formFiltro) return;

  const radDiario = document.getElementById('radTipoDiario');
  const radMensual = document.getElementById('radTipoMensual');
  const groupDiario = document.getElementById('groupFiltroDiario');
  const groupMensual = document.getElementById('groupFiltroMensual');
  const inputFecha = document.getElementById('inputFechaFiltro');
  const inputMes = document.getElementById('inputMesFiltro');

  function togglePeriodoInputs() {
    if (radMensual && radMensual.checked) {
      if (groupDiario) groupDiario.style.display = 'none';
      if (groupMensual) groupMensual.style.display = 'flex';
    } else {
      if (groupDiario) groupDiario.style.display = 'flex';
      if (groupMensual) groupMensual.style.display = 'none';
    }
  }

  if (radDiario) {
    radDiario.addEventListener('change', () => {
      togglePeriodoInputs();
      formFiltro.submit();
    });
  }

  if (radMensual) {
    radMensual.addEventListener('change', () => {
      togglePeriodoInputs();
      formFiltro.submit();
    });
  }

  if (inputFecha) {
    inputFecha.addEventListener('change', () => {
      formFiltro.submit();
    });
  }

  if (inputMes) {
    inputMes.addEventListener('change', () => {
      formFiltro.submit();
    });
  }

  togglePeriodoInputs();
}

/**
 * Atajos de fecha rápida
 */
function setFechaFiltro(tipo) {
  const inputFecha = document.getElementById('inputFechaFiltro');
  const radDiario = document.getElementById('radTipoDiario');
  const formFiltro = document.getElementById('formFiltroPeriodo');

  if (radDiario) radDiario.checked = true;

  const hoy = new Date();
  if (tipo === 'hoy') {
    const yyyy = hoy.getFullYear();
    const mm = String(hoy.getMonth() + 1).padStart(2, '0');
    const dd = String(hoy.getDate()).padStart(2, '0');
    if (inputFecha) inputFecha.value = `${yyyy}-${mm}-${dd}`;
  } else if (tipo === 'ayer') {
    const ayer = new Date(hoy);
    ayer.setDate(hoy.getDate() - 1);
    const yyyy = ayer.getFullYear();
    const mm = String(ayer.getMonth() + 1).padStart(2, '0');
    const dd = String(ayer.getDate()).padStart(2, '0');
    if (inputFecha) inputFecha.value = `${yyyy}-${mm}-${dd}`;
  }

  if (formFiltro) formFiltro.submit();
}

function setMesActual() {
  const inputMes = document.getElementById('inputMesFiltro');
  const radMensual = document.getElementById('radTipoMensual');
  const formFiltro = document.getElementById('formFiltroPeriodo');

  if (radMensual) radMensual.checked = true;

  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  const mm = String(hoy.getMonth() + 1).padStart(2, '0');
  if (inputMes) inputMes.value = `${yyyy}-${mm}`;

  if (formFiltro) formFiltro.submit();
}

/**
 * Descargar reporte en PDF abriendo la vista ejecutiva con descarga automática
 */
function abrirDescargaPDF() {
  const tipo = document.getElementById('radTipoMensual')?.checked ? 'mensual' : 'diario';
  const fecha = document.getElementById('inputFechaFiltro')?.value || '';
  const mes = document.getElementById('inputMesFiltro')?.value || '';

  const url = `${window.APP_URL}/dashboard/reporte-pdf?tipo=${tipo}&fecha=${fecha}&mes=${mes}`;
  window.open(url, '_blank');
}
