# **User Flow (Flujo de Navegación) \- Sistema POS Accesorios**

Este documento describe las rutas de interacción del usuario dentro del sistema, destacando la eficiencia, la reducción de clics y el bucle de venta de velocidad extrema.

## **1\. Autenticación (Login)**

**Actor:** Cajero / Administrador

* **Paso 1.1:** El usuario ingresa a la URL local del sistema (ej. http://localhost/pos\_accesorios).  
* **Paso 1.2:** Se muestra la pantalla de Login (Usuario y Contraseña).  
* **Paso 1.3:** El sistema valida credenciales contra la base de datos (bcrypt).  
* **Paso 1.4:**  
  * *Si es Cajero:* Redirección inmediata a la vista del POS (Punto de Venta) con el input de escáner ya enfocado.  
  * *Si es Administrador:* Redirección al Dashboard/Panel de Control (Resumen de ventas e inventario).

## **2\. Bucle de Venta (Core POS) \- *Velocidad Extrema***

**Actor:** Cajero (Flujo Principal)

* **Estado Inicial:** Pantalla limpia, cursor parpadeando automáticamente en el \<input id="barcode-scanner"\>.  
* **Paso 2.1 (Ingreso de Items):**  
  * *Flujo A (Escáner \- Principal):* El cajero escanea el producto. El sistema detecta el "Enter" automático del lector, añade 1 unidad al carrito virtual, recalcula subtotales e instantáneamente devuelve el foco al input del escáner.  
  * *Flujo B (Búsqueda Manual \- Atajo F2):* Se abre un modal. El cajero teclea el nombre, usa las flechas Arriba/Abajo para seleccionar y presiona Enter. El item se añade y el modal se cierra.  
* **Paso 2.2 (Edición Rápida):** Mediante atajos (ej. flechas direccionales y teclado numérico), el cajero puede ajustar cantidades o aplicar un descuento a una línea sin usar el ratón.  
* **Paso 2.3 (Cobro \- Atajo F12 o Enter en input vacío):**  
  * Se despliega el Modal de Checkout superpuesto.  
  * Se selecciona por defecto el método de pago "Efectivo".  
  * El cursor se auto-enfoca en el input "Monto Recibido".  
* **Paso 2.4 (Confirmación):** El cajero ingresa el billete recibido (ej. "50000") y presiona Enter.  
  * El sistema calcula el cambio en pantalla.  
  * Se procesa la transacción (Backend: Inserts en facturas, detalle\_facturas, update de stock).  
  * Se dispara el comando ESC/POS a la impresora (Impresión \+ Apertura de Cajón).  
* **Paso 2.5 (Bucle de Hard Reset):** En \< 500ms tras el Enter del paso anterior, el carrito se vacía, los totales vuelven a $0.00, y el cursor retorna al \<input id="barcode-scanner"\>. El sistema está listo para el siguiente cliente.

## **3\. Gestión de Inventario**

**Actor:** Administrador / Empleado Autorizado

* **Paso 3.1:** Navegación al módulo de Inventario desde el menú lateral.  
* **Paso 3.2:** Vista de tabla de productos con paginación y barra de búsqueda rápida (filtrado en vivo con JS).  
* **Paso 3.3 (Edición de Stock/Precio):**  
  * Se hace clic en "Editar" (o doble clic en la fila).  
  * Se abre un modal con el formulario poblado del producto.  
  * Se actualiza stock o precio\_venta.  
  * Al guardar, notificación "Toast" de éxito (sin recargar la página completa).  
* **Paso 3.4 (Nuevo Producto):** Botón "Agregar" \-\> Formulario de creación \-\> Inserción en BD \-\> Retorno automático a la tabla.

## **4\. Control de Caja y Movimientos**

**Actor:** Administrador / Cajero (con permisos)

* **Paso 4.1 (Movimiento Manual):**  
  * Acceso a "Movimientos de Caja".  
  * Selección de "Entrada" (ej. sencillo) o "Salida" (ej. pago papelería).  
  * Ingreso de monto y concepto. Guardar.  
* **Paso 4.2 (Cierre de Caja \- Arqueo):**  
  * Al final del turno, el cajero accede a "Cierre de Caja".  
  * El sistema muestra un resumen (Solo para Admins, los cajeros ven un cierre "ciego" donde deben ingresar lo que cuentan).  
  * El sistema cruza: (Ventas Efectivo \+ Entradas) \- Salidas \= Saldo Teórico.  
  * Se registra el cierre y se imprime un ticket de resumen del turno (Comando ESC/POS).