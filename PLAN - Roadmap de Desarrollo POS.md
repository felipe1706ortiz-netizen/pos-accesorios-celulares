# **PLAN: Roadmap de Desarrollo \- Sistema POS Accesorios**

Este plan de desarrollo está estructurado en 6 fases incrementales. Sigue un enfoque de "Core-First", asegurando que la arquitectura base y el bucle de venta principal se construyan e integren con el hardware lo antes posible.

## **Fase 1: Arquitectura Base y Setup (Semana 1\)**

*Objetivo: Establecer los cimientos del MVC y la persistencia de datos.*

* **1.1. Inicialización de Base de Datos:** Ejecución del script SQL para crear tablas, relaciones e índices.  
* **1.2. Estructura de Directorios:** Creación del árbol de carpetas MVC (Controllers, Models, Views, public).  
* **1.3. Conexión y Enrutamiento:** Implementación de Database.php (Singleton PDO) y el index.php (Front Controller) para el ruteo básico de URLs a Controladores.  
* **1.4. Autenticación Básica:** Login de usuarios (Cajeros/Admins) y protección de rutas.

## **Fase 2: Módulo de Inventario (Semana 2\)**

*Objetivo: Poblar el sistema para habilitar las ventas.*

* **2.1. CRUD Categorías:** Modelos, Vistas y Controladores para gestionar categorías.  
* **2.2. CRUD Productos:** Interfaz para crear, leer, actualizar y eliminar productos.  
* **2.3. Movimientos de Inventario:** Formularios ágiles para actualizar stock y modificar precios de compra/venta rápidamente.

## **Fase 3: Núcleo POS \- Interfaz y Carrito (Semana 3\)**

*Objetivo: Construir la vista "Keyboard-First" y la lógica del carrito en memoria.*

* **3.1. Maquetación POS:** Interfaz de usuario hiper-minimalista (CSS/HTML).  
* **3.2. Lógica de Escáner (JS):** Captura global de eventos de teclado, prevención de submit accidental y búsqueda instantánea (Fetch API a ProductoController).  
* **3.3. Gestión del Carrito (JS/PHP):** Añadir items, agrupar cantidades, aplicar descuentos globales o por línea.  
* **3.4. Cálculos en Tiempo Real:** Subtotales, impuestos y totales dinámicos sin recargar la página.

## **Fase 4: Checkout y Hardware (Semana 4\) \- *Fase Crítica***

*Objetivo: Cerrar la venta, actualizar BD e interactuar con periféricos.*

* **4.1. Modal de Pago:** Selección rápida de método de pago (Efectivo/Tarjeta), cálculo de cambio.  
* **4.2. Transacción de Venta (Backend):** Registro en facturas, detalle\_facturas y deducción atómica de stock en productos mediante transacciones PDO.  
* **4.3. Integración ESC/POS:** Script PHP para enviar comandos directos a la impresora térmica local.  
* **4.4. Bucle de Reset:** Disparo del cajón monedero, limpieza del carrito (JS) y auto-enfoque al input del escáner en \< 500ms tras la impresión.

## **Fase 5: Caja y Reportería (Semana 5\)**

*Objetivo: Control financiero y auditoría.*

* **5.1. Movimientos de Efectivo:** Interfaz para registrar entradas (sencillo) y salidas (gastos menores).  
* **5.2. Historial de Facturas:** Vista de auditoría con filtros por fecha, método de pago y opción de reimpresión.  
* **5.3. Cierre de Caja (Arqueo):** Algoritmo para sumarizar ventas diarias, cruzar con movimientos manuales y proyectar el saldo teórico esperado.

## **Fase 6: Pruebas de Estrés y Despliegue (Semana 6\)**

*Objetivo: Garantizar estabilidad en el entorno real.*

* **6.1. QA de Hardware:** Pruebas intensivas con lector de códigos de barras (escaneos rápidos) e impresoras (colas de impresión).  
* **6.2. Depuración UI/UX:** Eliminación de cualquier clic residual innecesario en el flujo del POS.  
* **6.3. Despliegue Local:** Configuración del servidor web local (Apache/Nginx), PHP y MySQL en la máquina principal de la tienda.