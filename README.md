# 📱 POS Celulares & Accesorios — Sistema de Punto de Venta e Inventario

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777bb4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Architecture](https://img.shields.io/badge/Architecture-MVC%20Vanilla-6366f1?style=for-the-badge)](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller)
[![UI/UX](https://img.shields.io/badge/Design-UI%2FUX%20Pro%20Max-10b981?style=for-the-badge)](https://uupm.cc)
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

Sistema integral de **Punto de Venta (POS), Facturación, Control de Inventario y Arqueo de Caja** diseñado para tiendas de tecnología y accesorios para celulares. Desarrollado con arquitectura **MVC en PHP puro**, Vanilla CSS con Design Tokens, JavaScript moderno y base de datos relacional MySQL optimizada con transacciones e índices.

---

## ⚡ Características Principales

### 🛒 1. Terminal de Punto de Venta (POS Keyboard-First)
- **Escaneo ultrarrápido:** Soporte nativo para lectores de códigos de barras (foco automático sin perder cursor).
- **Atajos de Teclado Profesionales:**
  - <kbd>F2</kbd> Búsqueda manual de accesorios por nombre/código con navegación por flechas.
  - <kbd>F4</kbd> Aplicación de descuento global en venta.
  - <kbd>F6</kbd> Consulta instantánea del saldo en efectivo de la gaveta.
  - <kbd>F9</kbd> Pulso de apertura física de cajón monedero (ESC/POS).
  - <kbd>F8</kbd> Cancelar y vaciar carrito.
  - <kbd>F12</kbd> Modal de cobro rápido con cálculo automático de vuelto/cambio.
  - <kbd>+</kbd> / <kbd>-</kbd> Modificación rápida de cantidades en carrito.
- **Múltiples Métodos de Pago:** Efectivo, Tarjeta/Datáfono y Transferencia (Nequi, Daviplata, QR).
- **Impresión Térmica:** Tickets de 80mm y 58mm en silencio vía iframe.

### 💵 2. Caja, Gaveta y Arqueo Diario
- **Flujo de Acceso para Cajeros:** Inicio de sesión obligatorio con ingreso de base inicial antes de entrar al POS.
- **Pulso de Cajón Monedero:** Disparo de comando estándar ESC/POS (`\x1b\x70\x00\x19\xfa`) vía RJ11 a la impresora térmica.
- **Control de Movimientos:** Registro de entradas manuales (sencillo) y salidas de efectivo (gastos menores) con comprobante.
- **Arqueo y Cierre Ciego/Guiado:** Calculadora interactiva por denominación de billetes colombianos ($100k, $50k, $20k, $10k, $5k, monedas) y conciliación en tiempo real (verde para cuadre exacto, azul para sobrante, rojo para faltante).

### 📦 3. Inventario & Kárdex
- Catálogo de productos con código de barras, categoría, precio de compra, precio de venta y cálculo automático de margen de utilidad (%).
- **Alertas de Stock Crítico:** Semáforo visual en tiempo real para productos agotados o por debajo del stock mínimo.
- **Ajuste Rápido:** Modificación instantánea de precio y stock sin recargar la página.
- **Kárdex Completo:** Trazabilidad histórica de entradas, salidas, ventas y devoluciones por producto.

### 📊 4. Facturación & Dashboard
- Auditoría histórica de facturas con filtros por rango de fechas, cajero y método de pago.
- Reimpresión de tickets térmicos con un solo clic.
- Anulación de ventas (exclusivo Administrador) con **reversión automática de stock al inventario**.
- Métricas operacionales en vivo: Ventas del día, ticket promedio y salud del sistema.

---

## 🛠️ Requisitos del Sistema

- **PHP:** Versión 8.0 o superior (Extensiones: `pdo`, `pdo_mysql`, `mbstring`, `openssl`).
- **Base de Datos:** MySQL 5.7+ / 8.0+ o MariaDB 10.3+.
- **Servidor Web:** Apache con módulo `mod_rewrite` habilitado o Nginx.

---

## 🚀 Instalación Local Paso a Paso

### 1. Clonar o Descargar el Proyecto
Coloque la carpeta del proyecto dentro del directorio de su servidor web local:
- **XAMPP (Windows):** `C:\xampp\htdocs\poss`
- **WAMP (Windows):** `C:\wamp64\www\poss`
- **Linux Apache:** `/var/www/html/poss`

```bash
git clone https://github.com/TU_USUARIO/TU_REPOSITORIO.git C:/xampp/htdocs/poss
```

### 2. Crear la Base de Datos e Importar Tablas
1. Abra su gestor de base de datos favorito (ej: **phpMyAdmin** en `http://localhost/phpmyadmin` o MySQL Workbench).
2. Cree una base de datos llamada `pos_accesorios`.
3. Importe el archivo SQL ubicado en:
   ```
   database/database.sql
   ```
   *(Este archivo crea todas las tablas con sus claves foráneas, índices y datos iniciales de prueba).*

### 3. Configurar Conexión (Opcional)
Por defecto, el sistema viene configurado para XAMPP (`localhost`, usuario `root`, sin contraseña). Si utiliza credenciales diferentes, puede ajustar el archivo [app/Config/config.php](app/Config/config.php) o definir variables de entorno en un archivo `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=pos_accesorios
DB_USER=root
DB_PASS=
```

### 4. Abrir en el Navegador
Inicie **Apache** y **MySQL** en su panel de XAMPP y navegue a:
```
http://localhost/poss
```

---

## 🔑 Credenciales por Defecto

| Rol | Usuario / Login | Contraseña | Acceso |
| :--- | :--- | :--- | :--- |
| **👑 Administrador** | `admin` | `Admin123*` | Dashboard, Inventario, Facturación, Reportes y Cajas |
| **💼 Cajero** | `cajero` | `Cajero123*` | Apertura de Caja, Punto de Venta (POS) y Cierre de Turno |

---

## 🌐 Cómo Subir a GitHub y Publicar en Línea

### Paso 1: Subir tu Código a GitHub

1. Instala [Git](https://git-scm.com/) en tu computadora si aún no lo tienes.
2. Abre una terminal (PowerShell o CMD) en la carpeta del proyecto:
   ```bash
   cd C:\xampp\htdocs\poss
   ```
3. Inicializa el repositorio y haz tu primer commit:
   ```bash
   git init
   git add .
   git commit -m "feat: Sistema POS Celulares & Accesorios con UI/UX Pro Max"
   git branch -M main
   ```
4. Crea un nuevo repositorio vacío en [GitHub](https://github.com/new).
5. Vincula tu repositorio local y sube los cambios:
   ```bash
   git remote add origin https://github.com/TU_USUARIO/NOMBRE_DEL_REPOSITORIO.git
   git push -u origin main
   ```

---

### Paso 2: Opciones para Publicar en Línea (Deploy)

Cualquier persona puede probar tu sistema en internet utilizando una de las siguientes opciones:

#### Opción A: Hosting Compartido / cPanel / Hostinger / InfinityFree (Fácil y Rápido)
1. Exporta tu base de datos local `pos_accesorios` desde phpMyAdmin.
2. Sube los archivos del proyecto a la carpeta `public_html` de tu hosting (vía FTP o File Manager).
3. Crea la base de datos MySQL en tu hosting e importa el archivo `database/database.sql`.
4. Edita `app/Config/config.php` con el nombre de usuario y contraseña de la base de datos de tu hosting.

#### Opción B: Despliegue en la Nube (Render / Railway / Clever Cloud)
1. Conecta tu repositorio de GitHub directamente a **Railway** o **Render**.
2. Agrega un servicio de **MySQL Database**.
3. En las variables de entorno (*Environment Variables*), define:
   - `DB_HOST`: Host de tu base de datos en la nube.
   - `DB_NAME`: Nombre de la base de datos.
   - `DB_USER`: Usuario de la base de datos.
   - `DB_PASS`: Contraseña de la base de datos.
   - `APP_ENV`: `production`

---

## 📂 Estructura del Proyecto

```
poss/
├── app/
│   ├── Config/          # Configuración de base de datos y entorno
│   ├── Controllers/     # Controladores MVC (Auth, POS, Caja, Inventario, Facturas)
│   ├── Core/            # Router, Auth, Controller base, Model base, Sesiones
│   ├── Models/          # Modelos de datos con PDO y Prepared Statements
│   └── Views/           # Vistas responsivas con UI/UX Pro Max
├── database/
│   └── database.sql     # Script SQL maestro con esquema y datos de prueba
├── public/
│   ├── css/             # Hojas de estilo CSS (style.css, pos.css)
│   ├── js/              # Lógica frontend (pos.js, caja.js, inventario.js, main.js)
│   ├── index.php        # Front Controller principal y enrutador
│   └── .htaccess        # Reescritura de URLs limpias en Apache
├── .env.example         # Plantilla de variables de entorno
├── .gitignore           # Archivos ignorados por Git
└── README.md            # Documentación del proyecto
```

---

## 📄 Licencia

Distribuido bajo la Licencia MIT. Consulte `LICENSE` para obtener más información.
