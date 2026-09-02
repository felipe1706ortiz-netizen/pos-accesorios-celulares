/**
 * ==============================================================================
 * JAVASCRIPT: MÓDULOS 5 Y 6 (CAJA, MOVIMIENTOS Y ARQUEO)
 * ==============================================================================
 * Registro AJAX de entradas/salidas, calculadora de denominaciones y conciliación en vivo.
 * ==============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Escuchadores para la calculadora de denominaciones de billetes en el Arqueo
  const denomInputs = document.querySelectorAll('.denom-input');
  const monedasInput = document.getElementById('inputMonedasDirecto');
  const montoRealInput = document.getElementById('monto_real');

  function recalcularTotalDenominaciones() {
    let totalContado = 0;

    denomInputs.forEach(input => {
      const valor = parseFloat(input.getAttribute('data-valor')) || 0;
      const cant = parseInt(input.value, 10) || 0;
      totalContado += (valor * cant);
    });

    if (monedasInput) {
      const monedas = parseFloat(monedasInput.value) || 0;
      totalContado += monedas;
    }

    if (montoRealInput) {
      montoRealInput.value = totalContado;
      calcularDiferenciaCaja();
    }
  }

  denomInputs.forEach(input => {
    input.addEventListener('input', recalcularTotalDenominaciones);
  });

  if (monedasInput) {
    monedasInput.addEventListener('input', recalcularTotalDenominaciones);
  }

  if (montoRealInput) {
    montoRealInput.addEventListener('input', calcularDiferenciaCaja);
    // Calcular inmediatamente al inicio para mostrar el estado correcto
    calcularDiferenciaCaja();
  }

  // 2. Manejo de formulario de Entrada/Salida de Efectivo vía AJAX
  const formMov = document.getElementById('formMovimientoCaja');
  if (formMov) {
    formMov.addEventListener('submit', async (e) => {
      e.preventDefault();

      const tipo = document.getElementById('mov_tipo').value;
      const monto = parseFloat(document.getElementById('mov_monto').value);
      const concepto = document.getElementById('mov_concepto').value.trim();
      const comprobante = document.getElementById('mov_comprobante').value.trim();

      if (isNaN(monto) || monto <= 0 || !concepto) {
        showToast('Complete el monto y concepto válidos', 'warning');
        return;
      }

      const btn = document.getElementById('btnGuardarMovimiento');
      if (btn) btn.disabled = true;

      try {
        const res = await fetchAPI(`${window.APP_URL}/caja/guardar-movimiento`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ tipo, monto, concepto, comprobante })
        });

        if (res.success) {
          showToast(`✅ ${res.message}`, 'success');
          closeModal('modalMovimientoCaja');
          formMov.reset();
          setTimeout(() => window.location.reload(), 800);
        } else {
          showToast(`⚠️ ${res.message || 'Error al guardar movimiento'}`, 'danger');
        }
      } catch (err) {
        showToast('Error de comunicación con el servidor', 'danger');
      } finally {
        if (btn) btn.disabled = false;
      }
    });
  }
});

/**
 * Calcula la diferencia entre el saldo teórico esperado y el efectivo contado en gaveta
 */
function calcularDiferenciaCaja() {
  const displaySaldoEsperado = document.getElementById('displaySaldoEsperado');
  const inputReal = document.getElementById('monto_real');
  const displayDif = document.getElementById('displayDiferencia');
  const displayDifTexto = document.getElementById('displayDiferenciaTexto');
  const box = document.getElementById('cajaDiferenciaBox');
  const displayFormateado = document.getElementById('montoRealFormateado');

  if (!inputReal || !displayDif) return;

  // Extraer número de saldo esperado de data-saldo o sanitizar dígitos
  let saldoEsperado = 0;
  if (displaySaldoEsperado) {
    if (displaySaldoEsperado.hasAttribute('data-saldo')) {
      saldoEsperado = parseFloat(displaySaldoEsperado.getAttribute('data-saldo')) || 0;
    } else {
      // Eliminar puntos de miles y caracteres no numéricos
      const cleanDigits = displaySaldoEsperado.textContent.replace(/[^0-9]/g, '');
      saldoEsperado = parseFloat(cleanDigits) || 0;
    }
  }

  const montoReal = parseFloat(inputReal.value) || 0;

  if (displayFormateado) {
    displayFormateado.textContent = `$ ${formatearMoneda(montoReal)}`;
  }

  const diferencia = montoReal - saldoEsperado;

  if (diferencia === 0) {
    displayDif.textContent = '$ 0';
    displayDif.style.color = 'var(--success)';
    if (displayDifTexto) displayDifTexto.innerHTML = '<span style="color: var(--success); font-weight:700;">🟢 Cuadre Exacto: El dinero físico coincide perfectamente con el sistema.</span>';
    if (box) {
      box.style.borderColor = 'var(--success)';
      box.style.background = '#f0fdf4';
    }
  } else if (diferencia > 0) {
    displayDif.textContent = `+ $ ${formatearMoneda(diferencia)}`;
    displayDif.style.color = '#0284c7';
    if (displayDifTexto) displayDifTexto.innerHTML = `<span style="color: #0284c7; font-weight:700;">🔵 Sobrante en Caja: Hay $ ${formatearMoneda(diferencia)} de más respecto al saldo teórico esperado.</span>`;
    if (box) {
      box.style.borderColor = '#0284c7';
      box.style.background = '#f0f9ff';
    }
  } else {
    displayDif.textContent = `- $ ${formatearMoneda(Math.abs(diferencia))}`;
    displayDif.style.color = 'var(--danger)';
    if (displayDifTexto) displayDifTexto.innerHTML = `<span style="color: var(--danger); font-weight:700;">🔴 Faltante en Caja: Faltan $ ${formatearMoneda(Math.abs(diferencia))} respecto al saldo teórico esperado.</span>`;
    if (box) {
      box.style.borderColor = 'var(--danger)';
      box.style.background = '#fef2f2';
    }
  }
}

/**
 * Copia el saldo esperado al input de conteo real
 */
function establecerSaldoEsperado() {
  const displaySaldoEsperado = document.getElementById('displaySaldoEsperado');
  const inputReal = document.getElementById('monto_real');
  if (!displaySaldoEsperado || !inputReal) return;

  let saldo = 0;
  if (displaySaldoEsperado.hasAttribute('data-saldo')) {
    saldo = parseFloat(displaySaldoEsperado.getAttribute('data-saldo')) || 0;
  } else {
    const cleanDigits = displaySaldoEsperado.textContent.replace(/[^0-9]/g, '');
    saldo = parseFloat(cleanDigits) || 0;
  }

  inputReal.value = saldo;
  calcularDiferenciaCaja();
}

/**
 * Abre el modal para registrar Entrada o Salida de efectivo
 * @param {'ENTRADA' | 'SALIDA'} tipo 
 */
function abrirModalMovimiento(tipo = 'ENTRADA') {
  openModal('modalMovimientoCaja');
  seleccionarTipoMovimiento(tipo);
  const inputMonto = document.getElementById('mov_monto');
  if (inputMonto) {
    inputMonto.value = '';
    setTimeout(() => inputMonto.focus(), 100);
  }
}

/**
 * Alterna entre Entrada y Salida en el modal
 * @param {'ENTRADA' | 'SALIDA'} tipo 
 */
function seleccionarTipoMovimiento(tipo) {
  const inputTipo = document.getElementById('mov_tipo');
  const btnEntrada = document.getElementById('btnToggleEntrada');
  const btnSalida = document.getElementById('btnToggleSalida');
  const titulo = document.getElementById('modalMovimientoTitulo');

  if (inputTipo) inputTipo.value = tipo;

  if (tipo === 'ENTRADA') {
    if (btnEntrada) { btnEntrada.className = 'btn btn-success'; }
    if (btnSalida) { btnSalida.className = 'btn btn-outline'; }
    if (titulo) {
      titulo.textContent = '📥 Registrar Entrada de Efectivo (Ingreso)';
      titulo.style.color = 'var(--success)';
    }
  } else {
    if (btnEntrada) { btnEntrada.className = 'btn btn-outline'; }
    if (btnSalida) { btnSalida.className = 'btn btn-danger'; }
    if (titulo) {
      titulo.textContent = '📤 Registrar Salida de Efectivo (Gasto/Pago)';
      titulo.style.color = 'var(--danger)';
    }
  }
}

function formatearMoneda(num) {
  return new Intl.NumberFormat('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num || 0);
}
