/**
 * ==============================================================================
 * JAVASCRIPT: MÓDULO DE PEDIDOS Y ENCARGOS DE CLIENTES
 * ==============================================================================
 * Manejo de modales para actualización de estados, abonos adicionales
 * y cálculo dinámico de saldos.
 * ==============================================================================
 */

/**
 * Abre el modal para cambiar el estado de un pedido
 * 
 * @param {number} id 
 * @param {string} codigo 
 * @param {string} estadoActual 
 */
function abrirModalEstadoPedido(id, codigo, estadoActual) {
  const form = document.getElementById('formCambiarEstado');
  const titulo = document.getElementById('modalEstadoTitulo');
  const select = document.getElementById('select_nuevo_estado');
  const inputNotas = document.getElementById('modal_estado_notas');

  if (form) {
    form.action = `${window.APP_URL}/pedidos/cambiar-estado/${id}`;
  }

  if (titulo) {
    titulo.textContent = `Actualizar Estado (${codigo})`;
  }

  if (select) {
    select.value = estadoActual;
  }

  if (inputNotas) {
    inputNotas.value = '';
  }

  openModal('modalEstadoPedido');
}

/**
 * Abre el modal para registrar un abono o pago parcial hacia el saldo pendiente
 * 
 * @param {number} id 
 * @param {string} codigo 
 * @param {number} saldoPendiente 
 */
function abrirModalAbonar(id, codigo, saldoPendiente) {
  const form = document.getElementById('formAbonarPedido');
  const titulo = document.getElementById('modalAbonarTitulo');
  const displaySaldo = document.getElementById('modalSaldoPendienteDisplay');
  const inputMonto = document.getElementById('monto_abono');
  const btnPagarTodo = document.getElementById('btnPagarTodoAbono');

  if (form) {
    form.action = `${window.APP_URL}/pedidos/abonar/${id}`;
  }

  if (titulo) {
    titulo.textContent = `Abonar a Pedido #${codigo}`;
  }

  const saldoNum = Number(saldoPendiente || 0);

  if (displaySaldo) {
    displaySaldo.textContent = `$ ${saldoNum.toLocaleString('es-CO')}`;
  }

  if (btnPagarTodo) {
    btnPagarTodo.onclick = function() {
      if (inputMonto) {
        inputMonto.value = saldoNum;
        inputMonto.focus();
      }
    };
  }

  if (inputMonto) {
    inputMonto.value = '';
    inputMonto.step = 'any';
    inputMonto.min = '0.01';
    inputMonto.max = saldoNum;
    setTimeout(() => inputMonto.focus(), 150);
  }

  openModal('modalAbonarPedido');
}
