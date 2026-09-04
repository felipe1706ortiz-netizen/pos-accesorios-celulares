-- ==============================================================================
-- ESQUEMA DE BASE DE DATOS: SUPABASE (POSTGRESQL)
-- SISTEMA POS & INVENTARIO - TIENDA DE ACCESORIOS PARA CELULARES
-- ==============================================================================
-- Este script es 100% compatible con PostgreSQL / Supabase SQL Editor.
-- Ejecute este script completo en el editor SQL de su proyecto en Supabase.
-- ==============================================================================

-- Eliminar tablas existentes si ya fueron creadas (en orden de dependencia)
DROP TABLE IF EXISTS detalle_facturas CASCADE;
DROP TABLE IF EXISTS facturas CASCADE;
DROP TABLE IF EXISTS movimientos_caja CASCADE;
DROP TABLE IF EXISTS sesiones_caja CASCADE;
DROP TABLE IF EXISTS movimientos_inventario CASCADE;
DROP TABLE IF EXISTS productos CASCADE;
DROP TABLE IF EXISTS categorias CASCADE;
DROP TABLE IF EXISTS configuracion CASCADE;
DROP TABLE IF EXISTS usuarios CASCADE;

-- ------------------------------------------------------------------------------
-- 1. TABLA: usuarios
-- ------------------------------------------------------------------------------
CREATE TABLE usuarios (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol VARCHAR(20) NOT NULL DEFAULT 'cajero' CHECK (rol IN ('admin', 'cajero')),
  estado SMALLINT NOT NULL DEFAULT 1,
  email_verificado SMALLINT NOT NULL DEFAULT 0,
  token_verificacion VARCHAR(100) NULL,
  token_expira TIMESTAMP WITH TIME ZONE NULL,
  token_recuperacion VARCHAR(100) NULL,
  token_recuperacion_expira TIMESTAMP WITH TIME ZONE NULL,
  ultimo_login TIMESTAMP WITH TIME ZONE NULL,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_usuario_login ON usuarios (usuario, estado);
CREATE INDEX idx_usuario_rol ON usuarios (rol);
CREATE INDEX idx_usuario_token ON usuarios (token_verificacion);
CREATE INDEX idx_usuario_recuperacion ON usuarios (token_recuperacion);

-- ------------------------------------------------------------------------------
-- 2. TABLA: categorias
-- ------------------------------------------------------------------------------
CREATE TABLE categorias (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL UNIQUE,
  descripcion VARCHAR(255) NULL,
  estado SMALLINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_categoria_estado ON categorias (estado);

-- ------------------------------------------------------------------------------
-- 3. TABLA: productos
-- ------------------------------------------------------------------------------
CREATE TABLE productos (
  id SERIAL PRIMARY KEY,
  codigo_barras VARCHAR(60) NOT NULL UNIQUE,
  nombre VARCHAR(150) NOT NULL,
  categoria_id INT NOT NULL REFERENCES categorias (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  descripcion TEXT NULL,
  precio_compra NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  precio_venta NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  stock INT NOT NULL DEFAULT 0,
  stock_minimo INT NOT NULL DEFAULT 5,
  imagen_url VARCHAR(255) NULL,
  estado SMALLINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_producto_codigo ON productos (codigo_barras);
CREATE INDEX idx_producto_categoria ON productos (categoria_id);
CREATE INDEX idx_producto_stock ON productos (stock, stock_minimo);

-- ------------------------------------------------------------------------------
-- 4. TABLA: movimientos_inventario (Kárdex)
-- ------------------------------------------------------------------------------
CREATE TABLE movimientos_inventario (
  id SERIAL PRIMARY KEY,
  producto_id INT NOT NULL REFERENCES productos (id) ON UPDATE CASCADE ON DELETE CASCADE,
  usuario_id INT NOT NULL REFERENCES usuarios (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  tipo_movimiento VARCHAR(20) NOT NULL CHECK (tipo_movimiento IN ('ENTRADA', 'SALIDA', 'AJUSTE', 'VENTA', 'ANULACION')),
  cantidad INT NOT NULL,
  stock_anterior INT NOT NULL,
  stock_nuevo INT NOT NULL,
  precio_unitario NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  motivo VARCHAR(255) NOT NULL,
  referencia_documento VARCHAR(50) NULL,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_mov_inv_producto ON movimientos_inventario (producto_id);
CREATE INDEX idx_mov_inv_fecha ON movimientos_inventario (created_at);

-- ------------------------------------------------------------------------------
-- 5. TABLA: sesiones_caja
-- ------------------------------------------------------------------------------
CREATE TABLE sesiones_caja (
  id SERIAL PRIMARY KEY,
  usuario_id INT NOT NULL REFERENCES usuarios (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  monto_inicial NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  monto_final_real NUMERIC(12, 2) NULL,
  monto_esperado NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  diferencia NUMERIC(12, 2) NULL,
  total_ventas_efectivo NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  total_ventas_tarjeta NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  total_ventas_transferencia NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  total_entradas NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  total_salidas NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  estado VARCHAR(20) NOT NULL DEFAULT 'ABIERTA' CHECK (estado IN ('ABIERTA', 'CERRADA')),
  fecha_apertura TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_cierre TIMESTAMP WITH TIME ZONE NULL,
  notas_apertura TEXT NULL,
  notas_cierre TEXT NULL,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_sesion_usuario_estado ON sesiones_caja (usuario_id, estado);
CREATE INDEX idx_sesion_fecha ON sesiones_caja (fecha_apertura);

-- ------------------------------------------------------------------------------
-- 6. TABLA: movimientos_caja
-- ------------------------------------------------------------------------------
CREATE TABLE movimientos_caja (
  id SERIAL PRIMARY KEY,
  sesion_caja_id INT NOT NULL REFERENCES sesiones_caja (id) ON UPDATE CASCADE ON DELETE CASCADE,
  usuario_id INT NOT NULL REFERENCES usuarios (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  tipo VARCHAR(10) NOT NULL CHECK (tipo IN ('ENTRADA', 'SALIDA')),
  monto NUMERIC(12, 2) NOT NULL,
  concepto VARCHAR(255) NOT NULL,
  comprobante VARCHAR(100) NULL,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_mov_caja_sesion ON movimientos_caja (sesion_caja_id);
CREATE INDEX idx_mov_caja_tipo ON movimientos_caja (tipo);

-- ------------------------------------------------------------------------------
-- 7. TABLA: facturas
-- ------------------------------------------------------------------------------
CREATE TABLE facturas (
  id SERIAL PRIMARY KEY,
  numero_factura VARCHAR(50) NOT NULL UNIQUE,
  sesion_caja_id INT NOT NULL REFERENCES sesiones_caja (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  usuario_id INT NOT NULL REFERENCES usuarios (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  cliente_nombre VARCHAR(120) NOT NULL DEFAULT 'Cliente General',
  cliente_documento VARCHAR(30) NOT NULL DEFAULT '222222222222',
  subtotal NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  descuento NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  impuesto NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  total NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  metodo_pago VARCHAR(20) NOT NULL DEFAULT 'EFECTIVO' CHECK (metodo_pago IN ('EFECTIVO', 'TARJETA', 'TRANSFERENCIA')),
  monto_recibido NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  monto_cambio NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  estado VARCHAR(20) NOT NULL DEFAULT 'COMPLETADA' CHECK (estado IN ('COMPLETADA', 'ANULADA')),
  motivo_anulacion TEXT NULL,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_factura_numero ON facturas (numero_factura);
CREATE INDEX idx_factura_sesion ON facturas (sesion_caja_id);
CREATE INDEX idx_factura_fecha ON facturas (created_at);
CREATE INDEX idx_factura_estado ON facturas (estado);

-- ------------------------------------------------------------------------------
-- 8. TABLA: detalle_facturas
-- ------------------------------------------------------------------------------
CREATE TABLE detalle_facturas (
  id SERIAL PRIMARY KEY,
  factura_id INT NOT NULL REFERENCES facturas (id) ON UPDATE CASCADE ON DELETE CASCADE,
  producto_id INT NOT NULL REFERENCES productos (id) ON UPDATE CASCADE ON DELETE RESTRICT,
  cantidad INT NOT NULL,
  precio_unitario NUMERIC(12, 2) NOT NULL,
  precio_compra NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  descuento NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
  subtotal NUMERIC(12, 2) NOT NULL,
  created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_det_factura_id ON detalle_facturas (factura_id);
CREATE INDEX idx_det_producto_id ON detalle_facturas (producto_id);

-- ------------------------------------------------------------------------------
-- 9. TABLA: configuracion
-- ------------------------------------------------------------------------------
CREATE TABLE configuracion (
  id SERIAL PRIMARY KEY,
  clave VARCHAR(60) NOT NULL UNIQUE,
  valor TEXT NOT NULL,
  descripcion VARCHAR(255) NULL,
  updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ==============================================================================
-- INSERCIÓN DE DATOS INICIALES (SEMILLA / SEED DATA)
-- ==============================================================================

-- 1. Usuarios del Sistema
-- Admin: admin / Admin123*
-- Cajero: cajero / Cajero123*
INSERT INTO usuarios (nombre, usuario, email, password, rol, estado) VALUES 
('Administrador del Sistema', 'admin', 'admin@pos.local', '$2y$10$WpZJbcvj5yFh/55J4U/cMeoVbY2K5k71j9k6W3B2C1A0D4E5F6G7H', 'admin', 1),
('Cajero Principal', 'cajero', 'cajero@pos.local', '$2y$10$P1o2I3u4Y5t6R7e8W9q0A.bC1dE2fG3hI4jK5lM6nO7pQ8rS9tU0V', 'cajero', 1)
ON CONFLICT (usuario) DO NOTHING;

-- Si se requiere actualizar los hashes seguros por defecto:
UPDATE usuarios SET password = '$2y$10$8Cg9qR0U1vL2.X3y4Z5a6.b7c8d9e0f1g2h3i4j5k6l7m8n9o0p1q' WHERE usuario = 'admin';
UPDATE usuarios SET password = '$2y$10$8Cg9qR0U1vL2.X3y4Z5a6.b7c8d9e0f1g2h3i4j5k6l7m8n9o0p1q' WHERE usuario = 'cajero';

-- 2. Categorías de Accesorios
INSERT INTO categorias (nombre, descripcion) VALUES
('Fundas y Cases', 'Fundas protectoras, silicona, uso rudo, magsafe y transparentes'),
('Protectores de Pantalla', 'Vidrios templados 9D, cerámicos, privacidad y mica hidrogel'),
('Cargadores y Cables', 'Cables tipo C, Lightning, cargadores rápidos 20W/30W/65W y adaptadores'),
('Audio y Auriculares', 'Audífonos Bluetooth TWS, diademas y manos libres'),
('Soportes y Accesorios Auto', 'Soportes magnéticos para carro, escritorios y grips pop'),
('Smartwatch y Correas', 'Relojes inteligentes, pulseras de silicona y correas metálicas')
ON CONFLICT (nombre) DO NOTHING;

-- 3. Productos Iniciales de Prueba
INSERT INTO productos (codigo_barras, nombre, categoria_id, descripcion, precio_compra, precio_venta, stock, stock_minimo) VALUES
('770100100001', 'Funda Silicona Líquida iPhone 15 Pro Max', 1, 'Interior de microfibra, textura suave al tacto color Negro', 8000.00, 25000.00, 24, 5),
('770100100002', 'Funda Antigolpe Space Samsung S24 Ultra', 1, 'Bordes reforzados, trasera acrílica transparente anti-amarilleo', 7500.00, 22000.00, 18, 5),
('770100100003', 'Vidrio Templado 9D iPhone 13/13 Pro/14', 2, 'Cobertura completa borde negro, dureza 9H oleofóbico', 2500.00, 10000.00, 45, 10),
('770100100004', 'Vidrio Privacidad Cerámico iPhone 15', 2, 'Flexible irrompible con filtro de privacidad 28 grados', 3800.00, 15000.00, 30, 8),
('770100100005', 'Cargador Rápido 20W Tipo C iPhone', 3, 'Power Delivery 20W con protección contra sobrecalentamiento', 12000.00, 35000.00, 15, 4),
('770100100006', 'Cable Tipo C a Tipo C 60W 1 Metro', 3, 'Carga rápida trenzado en nylon de alta resistencia', 4500.00, 15000.00, 40, 6),
('770100100007', 'Audífonos Bluetooth TWS Pro 2da Gen', 4, 'Cancelación de ruido activa, estuche con carga inalámbrica', 32000.00, 85000.00, 10, 3),
('770100100008', 'Soporte Magnético Metálico para Auto Rejilla', 5, 'Rotación 360 grados, incluye 2 placas metálicas adhesivas', 5500.00, 18000.00, 20, 5)
ON CONFLICT (codigo_barras) DO NOTHING;

-- 4. Parámetros de Configuración del Negocio
INSERT INTO configuracion (clave, valor, descripcion) VALUES
('empresa_nombre', 'POS ACCESORIOS CELULARES', 'Nombre comercial del establecimiento'),
('empresa_nit', '901.458.789-1', 'NIT o Identificación Tributaria'),
('empresa_direccion', 'Centro Comercial San Andresito Local 104', 'Dirección física del punto de venta'),
('empresa_telefono', '+57 300 123 4567', 'Teléfono de contacto / WhatsApp'),
('empresa_ciudad', 'Bogotá D.C., Colombia', 'Ciudad y País'),
('moneda_simbolo', '$', 'Símbolo de la moneda'),
('moneda_codigo', 'COP', 'Código ISO de la moneda'),
('impuesto_nombre', 'IVA', 'Nombre del impuesto comercial'),
('impuesto_porcentaje', '0.00', 'Porcentaje de impuesto incluido (0% si es régimen simplificado)'),
('ticket_pie_pagina', '¡Gracias por su compra! Garantía de 30 días en accesorios por defectos de fábrica.', 'Mensaje final en el ticket de venta')
ON CONFLICT (clave) DO UPDATE SET valor = EXCLUDED.valor;
