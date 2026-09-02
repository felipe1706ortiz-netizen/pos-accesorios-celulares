# **Product Requirements Document (PRD)**

## **Sistema de Inventario y Facturación (POS) \- Tienda de Accesorios para Celulares**

### **1\. Resumen Ejecutivo**

Desarrollo de un sistema de Punto de Venta (POS) e inventario diseñado exclusivamente para uso interno. El sistema está optimizado estructuralmente para la **velocidad extrema** en el punto de cobro, eliminando la fricción de la interfaz gráfica en favor de un diseño "keyboard-first" y centrado en periféricos.

### **2\. Intención del Producto (Vibe)**

El sistema debe sentirse como una extensión de las manos del cajero.

* **Velocidad Extrema:** Despacho rápido como prioridad absoluta.  
* **Cero Fricción:** Reducción de clics al mínimo. El cursor del sistema debe estar perpetuamente enfocado en la entrada del escáner de códigos de barras.  
* **Operación "Keyboard-First":** Atajos de teclado para todas las funciones críticas (cobrar, aplicar descuento, buscar producto manual).

### **3\. Perfiles de Usuario**

* **Cajeros/Empleados:** Enfocados en el módulo POS, movimientos de efectivo y cierres de caja. Interfaz simplificada y orientada a la velocidad.  
* **Administradores:** Acceso total. Gestión de inventarios profundo, auditoría de facturas y control de caja.

### **4\. Alcance Operativo (Módulos Base)**

1. **Módulo de Inventario:** Visualización de productos, categorías, niveles de stock y precios actuales.  
2. **Movimientos de Inventario:** Interfaces para ingreso de nueva mercancía, ajuste de cantidades y actualización rápida de precios.  
3. **Nueva Factura (POS \- Core):**  
   * Entrada ininterrumpida por lector de código de barras.  
   * Fallback a búsqueda manual mediante modal rápido.  
   * Cálculo automático en tiempo real de subtotales, descuentos (globales o por ítem) e impuestos.  
   * Interfaz de pago rápido: Selección de método (Efectivo/Tarjeta/Transferencia), cálculo de cambio automático.  
4. **Historial de Facturas:** Búsqueda, filtrado por fechas y revisión de detalle de transacciones pasadas.  
5. **Movimientos de Efectivo:** Registro de ingresos/egresos menores de caja (pagos a proveedores, gastos diarios) independientes de la venta.  
6. **Cierre de Caja (Arqueo):** Consolidación diaria de ventas por método de pago, cruce con movimientos de efectivo y cálculo de saldo esperado (Ciego/Declarado).

### **5\. Lógica de Bucle (Loop Engineering)**

* **Bucle de Venta Continua:** Al confirmarse el pago y dispararse la orden de impresión, el sistema ejecuta un *hard-reset* instantáneo de la interfaz. La pantalla vuelve a un estado limpio de "Nueva Venta" en menos de 500ms, con el cursor nuevamente posicionado en el input del escáner, listo para el siguiente cliente sin requerir interacción manual.

### **6\. Hardware y Protocolos (Integración Crítica)**

* **Impresoras Térmicas:** Comunicación directa a través de protocolos ESC/POS para impresión silenciosa y sin cuadros de diálogo del navegador.  
* **Cajón Monedero:** Disparo de apertura automática vinculado al evento de impresión ESC/POS exclusivo para pagos en efectivo.  
* **Lectores de Código de Barras:** Compatibilidad universal (emulación de teclado) con prevención de inyección de caracteres no deseados en la interfaz.

### **7\. Restricciones Arquitectónicas (High-Level)**

* Separación absoluta mediante patrón MVC estricto.  
* Operación en entorno web pero con comportamiento de aplicación de escritorio (Single-Page-App feel para el módulo POS).