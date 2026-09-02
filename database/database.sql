-- ==============================================================================
-- BASE DE DATOS: pos_accesorios
-- SISTEMA DE INVENTARIO Y FACTURACIÓN (POS) - TIENDA DE ACCESORIOS PARA CELULARES
-- ==============================================================================
-- Motor de almacenamiento: InnoDB
-- Juego de caracteres: utf8mb4 (Soporte completo internacional y emojis)
-- Collation: utf8mb4_unicode_ci
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `pos_accesorios` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `pos_accesorios`;

-- Desactivar temporalmente verificación de claves foráneas para recreación segura
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. TABLA: usuarios
-- Gestión de accesos, roles y credenciales seguras (bcrypt)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL COMMENT 'Nombre completo del empleado/administrador',
  `usuario` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Nombre de usuario para inicio de sesión',
  `email` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Correo electrónico de contacto/recuperación',
  `password` VARCHAR(255) NOT NULL COMMENT 'Hash seguro de contraseña generado con password_hash (bcrypt)',
  `rol` ENUM('admin', 'cajero') NOT NULL DEFAULT 'cajero' COMMENT 'Rol dentro del sistema POS',
  `estado` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = Activo, 0 = Inactivo',
  `email_verificado` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = Verificado, 0 = Pendiente',
  `token_verificacion` VARCHAR(100) NULL COMMENT 'Token de activación por correo',
  `token_expira` DATETIME NULL COMMENT 'Fecha de expiración del token',
  `ultimo_login` DATETIME NULL COMMENT 'Fecha y hora del último acceso exitoso',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_usuario_login` (`usuario`, `estado`),
  INDEX `idx_usuario_rol` (`rol`),
  INDEX `idx_usuario_token` (`token_verificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. TABLA: categorias
-- Clasificación de productos y accesorios de celulares
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(80) NOT NULL UNIQUE COMMENT 'Nombre de la categoría (ej: Fundas, Cargadores, etc.)',
  `descripcion` VARCHAR(255) NULL COMMENT 'Detalles descriptivos de la categoría',
  `estado` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = Activo, 0 = Desactivado',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_categoria_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. TABLA: productos
-- Catálogo maestro de accesorios, control de inventario y código de barras
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `codigo_barras` VARCHAR(60) NOT NULL UNIQUE COMMENT 'Código de barras EAN/UPC o código interno para escáner',
  `nombre` VARCHAR(150) NOT NULL COMMENT 'Nombre comercial y modelo compatible (ej: Case iPhone 15 Pro Magsafe)',
  `descripcion` TEXT NULL COMMENT 'Especificaciones técnicas o detalles del accesorio',
  `categoria_id` INT UNSIGNED NOT NULL COMMENT 'Relación con la categoría del producto',
  `precio_compra` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Costo de adquisición para calcular margen y utilidad',
  `precio_venta` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio de venta al público en punto de cobro',
  `stock` INT NOT NULL DEFAULT 0 COMMENT 'Existencias actuales disponibles para venta',
  `stock_minimo` INT NOT NULL DEFAULT 5 COMMENT 'Umbral para alertas automáticas de reposición',
  `imagen` VARCHAR(255) NULL COMMENT 'Ruta relativa de la imagen del producto',
  `estado` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = Disponible para venta, 0 = Descontinuado/Inactivo',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_producto_codigo` (`codigo_barras`),
  INDEX `idx_producto_nombre` (`nombre`),
  INDEX `idx_producto_categoria` (`categoria_id`),
  INDEX `idx_producto_stock` (`stock`, `stock_minimo`),
  INDEX `idx_producto_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. TABLA: movimientos_inventario (Kárdex / Auditoría de Stock)
-- Registro de trazabilidad para cada cambio de inventario o ajuste de precios
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `movimientos_inventario`;
CREATE TABLE `movimientos_inventario` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `producto_id` INT UNSIGNED NOT NULL COMMENT 'Producto afectado en el movimiento',
  `usuario_id` INT UNSIGNED NOT NULL COMMENT 'Empleado o administrador que autoriza el movimiento',
  `tipo_movimiento` ENUM('ENTRADA', 'SALIDA', 'AJUSTE', 'VENTA', 'DEVOLUCION') NOT NULL COMMENT 'Naturaleza del movimiento',
  `cantidad` INT NOT NULL COMMENT 'Cantidad de unidades operadas (siempre positiva)',
  `stock_anterior` INT NOT NULL COMMENT 'Stock antes de aplicar el movimiento',
  `stock_nuevo` INT NOT NULL COMMENT 'Stock resultante tras aplicar el movimiento',
  `precio_unitario` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio unitario registrado al momento del movimiento',
  `motivo` VARCHAR(255) NOT NULL COMMENT 'Razón del movimiento (ej: Venta POS #0012, Compra proveedor, Ajuste físico)',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_mov_inv_producto` (`producto_id`),
  INDEX `idx_mov_inv_tipo` (`tipo_movimiento`),
  INDEX `idx_mov_inv_fecha` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. TABLA: sesiones_caja (Control de Turnos y Arqueo)
-- Control de apertura, balance en tiempo real y cierre de caja diario por cajero
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `sesiones_caja`;
CREATE TABLE `sesiones_caja` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT UNSIGNED NOT NULL COMMENT 'Cajero asignado al turno de caja',
  `fecha_apertura` DATETIME NOT NULL COMMENT 'Momento en que se abrió la caja',
  `fecha_cierre` DATETIME NULL COMMENT 'Momento en que se ejecutó el arqueo/cierre',
  `monto_inicial` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Fondo de caja/base inicial para cambio',
  `total_ventas_efectivo` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Acumulado de ventas cobradas en efectivo',
  `total_ventas_tarjeta` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Acumulado de ventas cobradas con tarjeta',
  `total_ventas_transferencia` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Acumulado de ventas por transferencia/QR',
  `total_entradas` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Acumulado de ingresos de efectivo manuales',
  `total_salidas` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Acumulado de egresos de efectivo manuales',
  `monto_esperado` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Saldo teórico esperado en efectivo (Inicial + Ventas Ef + Entradas - Salidas)',
  `monto_real` DECIMAL(12,2) NULL COMMENT 'Saldo contado físicamente por el cajero en el cierre ciego',
  `diferencia` DECIMAL(12,2) NULL COMMENT 'Monto Real - Monto Esperado (Sobrante positivo, Faltante negativo)',
  `estado` ENUM('ABIERTA', 'CERRADA') NOT NULL DEFAULT 'ABIERTA',
  `notas` TEXT NULL COMMENT 'Observaciones al momento del cierre',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_sesion_usuario_estado` (`usuario_id`, `estado`),
  INDEX `idx_sesion_fechas` (`fecha_apertura`, `fecha_cierre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 6. TABLA: movimientos_caja
-- Ingresos y egresos de efectivo no relacionados directamente con una venta (gastos, sencillo, etc.)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `movimientos_caja`;
CREATE TABLE `movimientos_caja` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sesion_caja_id` INT UNSIGNED NOT NULL COMMENT 'Turno de caja donde se efectúa el movimiento',
  `usuario_id` INT UNSIGNED NOT NULL COMMENT 'Usuario que registra el movimiento',
  `tipo` ENUM('ENTRADA', 'SALIDA') NOT NULL COMMENT 'ENTRADA (ingreso de cambio/fondo) o SALIDA (gasto menor/pago)',
  `monto` DECIMAL(12,2) NOT NULL COMMENT 'Valor monetario del movimiento (siempre positivo)',
  `concepto` VARCHAR(255) NOT NULL COMMENT 'Descripción y justificación del movimiento',
  `comprobante` VARCHAR(100) NULL COMMENT 'Número de recibo o soporte físico del gasto/ingreso',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sesion_caja_id`) REFERENCES `sesiones_caja`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_mov_caja_sesion` (`sesion_caja_id`),
  INDEX `idx_mov_caja_tipo` (`tipo`),
  INDEX `idx_mov_caja_fecha` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 7. TABLA: facturas (Cabecera de Ventas POS)
-- Registro consolidado de transacciones de venta procesadas
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `facturas`;
CREATE TABLE `facturas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `numero_factura` VARCHAR(40) NOT NULL UNIQUE COMMENT 'Identificador correlativo de venta (ej: FAC-2026-00001)',
  `sesion_caja_id` INT UNSIGNED NOT NULL COMMENT 'Sesión de caja activa en la que se generó la venta',
  `usuario_id` INT UNSIGNED NOT NULL COMMENT 'Cajero o vendedor que despachó la venta',
  `cliente_nombre` VARCHAR(150) NOT NULL DEFAULT 'Cliente General' COMMENT 'Nombre del cliente',
  `cliente_documento` VARCHAR(30) NOT NULL DEFAULT '222222222222' COMMENT 'Cédula/NIT/DNI del cliente',
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Suma de subtotales de productos antes de descuento e impuesto',
  `descuento` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor monetario total del descuento aplicado',
  `impuesto` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Impuesto calculado si aplica (IVA/IGV)',
  `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Monto neto final a cobrar (Subtotal - Descuento + Impuesto)',
  `metodo_pago` ENUM('EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'MIXTO') NOT NULL DEFAULT 'EFECTIVO' COMMENT 'Medio de pago principal',
  `monto_recibido` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Monto entregado por el cliente',
  `cambio` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Vuelto o cambio retornado al cliente',
  `estado` ENUM('COMPLETADA', 'ANULADA') NOT NULL DEFAULT 'COMPLETADA' COMMENT 'Estado legal de la factura',
  `notas` TEXT NULL COMMENT 'Comentarios adicionales o motivo de anulación',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sesion_caja_id`) REFERENCES `sesiones_caja`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_factura_numero` (`numero_factura`),
  INDEX `idx_factura_sesion` (`sesion_caja_id`),
  INDEX `idx_factura_usuario` (`usuario_id`),
  INDEX `idx_factura_metodo` (`metodo_pago`),
  INDEX `idx_factura_estado` (`estado`),
  INDEX `idx_factura_fecha` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 8. TABLA: detalle_facturas
-- Renglones individuales de productos en cada venta
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `detalle_facturas`;
CREATE TABLE `detalle_facturas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `factura_id` BIGINT UNSIGNED NOT NULL COMMENT 'Factura a la que pertenece la línea',
  `producto_id` INT UNSIGNED NOT NULL COMMENT 'Producto vendido',
  `cantidad` INT NOT NULL DEFAULT 1 COMMENT 'Cantidad de unidades despachadas',
  `precio_unitario` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio de venta unitario aplicado al momento del cobro',
  `precio_compra` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Costo de compra histórico para reportes de rentabilidad',
  `descuento` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Descuento unitario o de línea aplicado',
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Importe neto de la línea: (Cantidad * Precio) - Descuento',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`factura_id`) REFERENCES `facturas`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (`producto_id`) REFERENCES `productos`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX `idx_det_factura_id` (`factura_id`),
  INDEX `idx_det_producto_id` (`producto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 9. TABLA: configuracion
-- Parámetros del negocio, ticket térmico ESC/POS e impuestos
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `configuracion`;
CREATE TABLE `configuracion` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clave` VARCHAR(60) NOT NULL UNIQUE COMMENT 'Clave identificadora del parámetro',
  `valor` TEXT NOT NULL COMMENT 'Valor configurado en el sistema',
  `descripcion` VARCHAR(255) NULL COMMENT 'Propósito y contexto del ajuste',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reactivar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- SEEDS / DATOS INICIALES POR DEFECTO
-- ==============================================================================

-- 1. Usuarios del sistema
-- Clave Admin: Admin123* (bcrypt)
-- Clave Cajero: Cajero123* (bcrypt)
INSERT INTO `usuarios` (`id`, `nombre`, `usuario`, `email`, `password`, `rol`, `estado`) VALUES
(1, 'Administrador Principal', 'admin', 'admin@tiendacelulares.com', '$2y$10$AmgEHtyIQeXKxLA620AjKejGiQWMbtfX017HioXvox0cUBTp2s.pe', 'admin', 1),
(2, 'Cajero Turno Principal', 'cajero', 'cajero@tiendacelulares.com', '$2y$10$2eLrXGmhB88RaqeYLf7VzO/BmufYdgcq721WK187lSR9cyzQzqnCy', 'cajero', 1);

-- 2. Categorías especializadas de accesorios de celulares
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Fundas y Protectores', 'Cases de silicona, acrílico, uso rudo y MagSafe para iPhone, Samsung, Xiaomi', 1),
(2, 'Vidrios y Protectores de Pantalla', 'Vidrios templados 9D, cerámicos, hidrogel y protectores de lentes de cámara', 1),
(3, 'Cargadores y Cables', 'Adaptadores de carga rápida 20W/33W/65W, cables Tipo C, Lightning y Micro USB', 1),
(4, 'Audio y Auriculares', 'Auriculares Bluetooth TWS, audífonos con cable jack 3.5mm y Tipo C', 1),
(5, 'Soportes y Accesorios Auto', 'Soportes magnéticos para carro, popsockets, trípodes y aros de luz', 1),
(6, 'Memorias y Adaptadores', 'Tarjetas MicroSD, memorias USB OTG, adaptadores Tipo C a Jack 3.5mm', 1);

-- 3. Catálogo inicial de productos con códigos de barra de prueba
INSERT INTO `productos` (`id`, `codigo_barras`, `nombre`, `descripcion`, `categoria_id`, `precio_compra`, `precio_venta`, `stock`, `stock_minimo`, `estado`) VALUES
(1, '7701234567801', 'Funda MagSafe Transparente iPhone 15 Pro', 'Funda antigolpes con anillo magnético de alta resistencia', 1, 12000.00, 35000.00, 25, 5, 1),
(2, '7701234567802', 'Funda Silicona Case Samsung Galaxy S24 Ultra', 'Silicona suave interior afelpado color negro', 1, 10000.00, 28000.00, 20, 5, 1),
(3, '7701234567803', 'Vidrio Templado 9D Full Cover iPhone 15/15 Pro', 'Protector con marco negro, dureza 9H y borde biselado', 2, 3500.00, 15000.00, 50, 10, 1),
(4, '7701234567804', 'Vidrio Templado Cerámico Mate Xiaomi Redmi Note 13', 'Protector irrompible flexible anti-huellas', 2, 4000.00, 18000.00, 40, 10, 1),
(5, '7701234567805', 'Cargador Carga Rápida 20W USB-C PD Compatible iPhone', 'Cubo adaptador 20W Power Delivery certificado', 3, 15000.00, 45000.00, 30, 8, 1),
(6, '7701234567806', 'Cable Tipo C a Tipo C 60W Trenzado 1.2m', 'Cable ultra resistente con recubrimiento de nylon', 3, 6000.00, 20000.00, 35, 8, 1),
(7, '7701234567807', 'Auriculares In-Ear Bluetooth TWS Air Pro 6', 'Audífonos inalámbricos con estuche de carga y touch control', 4, 22000.00, 60000.00, 15, 4, 1),
(8, '7701234567808', 'Soporte Magnético Rejilla para Auto 360°', 'Soporte universal con imanes de neodimio de alto agarre', 5, 8000.00, 25000.00, 18, 5, 1),
(9, '7701234567809', 'Adaptador Tipo C a Jack 3.5mm con DAC Hi-Fi', 'Adaptador para conectar audífonos de cable a celulares Tipo C', 6, 7000.00, 22000.00, 22, 6, 1);

-- 4. Parámetros de configuración del sistema POS y ticket térmico
INSERT INTO `configuracion` (`clave`, `valor`, `descripcion`) VALUES
('empresa_nombre', 'CELL ACCESSORIES & TECH', 'Nombre comercial de la tienda impreso en encabezado de factura'),
('empresa_nit', '901.458.789-0', 'Número de identificación tributaria o RUC'),
('empresa_direccion', 'Av. Central # 45-20 Local 102, Centro Comercial Tecnológico', 'Dirección física del establecimiento'),
('empresa_telefono', '+57 (300) 123-4567', 'Teléfono o WhatsApp de atención al cliente'),
('empresa_email', 'contacto@cellaccessories.com', 'Correo comercial oficial'),
('moneda_simbolo', '$', 'Símbolo de moneda local ($, S/., €, MXN)'),
('moneda_codigo', 'COP', 'Código ISO de la moneda (COP, USD, PEN, MXN)'),
('impuesto_porcentaje', '0', 'Porcentaje de impuesto a las ventas (0 si régimen simplificado/no responsable)'),
('impuesto_nombre', 'IVA', 'Nombre del gravamen (IVA, IGV, TAX)'),
('ticket_pie_pagina', '¡Gracias por su compra!\nConserve este ticket para garantía (30 días en accesorios).\nNo cubre daños físicos ni humedad.', 'Texto al pie del ticket térmico'),
('impresora_tipo', 'POS-58', 'Modelo/ancho de papel (POS-58 o POS-80 mm)'),
('impresora_nombre', 'XP-58', 'Nombre de la impresora compartida en Windows/Red'),
('apertura_cajon_automatica', '1', 'Disparar pulso al cajón monedero en pagos en efectivo (1=Si, 0=No)');
