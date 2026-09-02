# **Technical Requirements Document (TRD)**

## **Sistema de Inventario y Facturación (POS) \- Tienda de Accesorios**

### **1\. Arquitectura del Sistema**

El sistema emplea un patrón de arquitectura **MVC (Modelo-Vista-Controlador) Estricto** para garantizar la separación de responsabilidades:

* **Modelos (Models):** Clases PHP exclusivas para interacciones con MySQL (CRUD). No procesan HTML ni reciben peticiones HTTP directamente.  
* **Vistas (Views):** Archivos PHP/HTML que renderizan la interfaz de usuario. No contienen lógica de negocio ni consultas directas a la base de datos.  
* **Controladores (Controllers):** Gestionan el flujo (Routing), reciben requests POST/GET, validan, consultan a los modelos y despachan los datos a las Vistas.

**Stack Tecnológico:**

* **Backend:** PHP 8.x (Puro, sin frameworks).  
* **Base de Datos:** MySQL 8.x (Comunicación vía capa PDO).  
* **Frontend:** HTML5, CSS3, JavaScript Vanilla.  
* **Protocolos:** ESC/POS (Impresoras térmicas).

### **2\. Estructura de Directorios (MVC)**

pos\_accesorios/  
│  
├── app/                        \# Carpeta protegida de lógica de la aplicación  
│   ├── Config/                 \# Archivos de configuración  
│   │   ├── config.php          \# Constantes (DB\_HOST, DB\_USER, etc.)  
│   │   └── Database.php        \# Clase Singleton PDO  
│   │  
│   ├── Controllers/            \# Controladores del negocio  
│   │   ├── PosController.php  
│   │   ├── InventarioController.php  
│   │   └── ReportesController.php  
│   │  
│   ├── Models/                 \# Clases de manipulación de datos  
│   │   ├── ProductoModel.php  
│   │   ├── FacturaModel.php  
│   │   └── CajaModel.php  
│   │  
│   └── Views/                  \# Archivos de presentación  
│       ├── layouts/            \# Plantillas maestras (header, footer)  
│       ├── pos/                \# Interfaz del cajero (keyboard-first)  
│       └── inventario/         \# Vistas de CRUD de productos  
│  
├── public/                     \# Único directorio accesible vía web  
│   ├── index.php               \# Front Controller (Enrutador principal)  
│   ├── css/                    \# Estilos CSS  
│   ├── js/                     \# Scripts Vanilla JS (Lógica de bucle y escáner)  
│   └── assets/                 \# Imágenes, iconos  
│  
└── .htaccess                   \# Reglas de enrutamiento (Reescribe todo a public/index.php)  
