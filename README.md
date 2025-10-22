# TadStore - Tienda virtual de Ropa y Accesorios

**Proyecto desarrollado para la Evaluación Técnica Full Stack Junior (Laravel / Vue.js)**  
**Iyata | Versión 2.0 | Octubre 2024**

TadStore es una aplicación web de comercio electrónico enfocada en la venta de ropa y accesorios.  
El proyecto demuestra la implementación de un stack **Laravel + Vue 3**, con arquitectura limpia, autenticación segura, validaciones completas y comunicación frontend-backend mediante **API RESTful**.

---

## Herramientas usadas

### Backend
- **PHP:** 8.2+
- **Laravel:** 10.x
- **Composer:** 2.x
- **MySQL:** 8.x
- **Laravel Sanctum:** Autenticación basada en tokens
- **CORS:** Configuración segura para intercambio de recursos
- **Eloquent ORM:** Manejo de relaciones y migraciones
- **Laravel Validation:** Validaciones personalizadas y globales
- **Laravel Exceptions / Handler personalizado:** Manejo estructurado de errores

### Frontend
- **Vue.js:** 3.x (Composition API)
- **Vite:** Build tool moderna
- **Pinia:** Gestión de estado global reactiva
- **Axios:** Cliente HTTP para consumo de APIs
- **Vue Router:** Navegación SPA

---

## Requisitos Previos

Antes de iniciar, asegúrate de tener instalado:

| Herramienta | Versión Recomendada |
|--------------|--------------------|
| PHP | 8.2 o superior |
| Composer | 2.x |
| Node.js | 18.x o superior |
| NPM | 9.x |
| MySQL | 8.x |
| Git | Última estable |

---

## Contexto funcional

TadStore permite a los usuarios explorar un catálogo de productos, añadir artículos al carrito y confirmar pedidos mediante un sistema de verificación por correo electrónico. Los administradores pueden gestionar el inventario, las categorías y las ventas desde un panel protegido.

## Instalación y Configuración

### 1. Clonar el Repositorio

```bash
git clone https://github.com/moru93/TadStore.git
cd tadstore
```

### 2. Configuración del backend 

```bash
cd tadstore-backend
composer install
cp .env.example .env
php artisan key:generate
```

Edita el archivo .env con las credenciales locales de la base de datos:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tadstore_db
DB_USERNAME=root
DB_PASSWORD=

### 3. Crear base de datos, ejecutar migraciones y seeders

Se accede a la terminal de windows, se ingresa a MySQL (por ejemplo mysql -u root ) y se crea la base de datos:

CREATE DATABASE tadstore_db;

Con esto se crean las tablas y registros de ejemplo
```bash
php artisan migrate --seed
```

### 4. Iniciar el servidor backend

```bash
php artisan serve
```
Esto corre el proyecto, por defecto en http://127.0.0.1:8000

### 5. Configuración del frontend 

```bash
cd tadstore-frontend
npm install
```

Si el backend se ejecuta en otro puerto, se debe actualizar en .env esta línea:

APP_URL=http://localhost:8000

### 6. Iniciar el servidor frontend

```bash
npm run dev
```

Esto pone el Frontend disponible en http://localhost:5173


## Estructura del proyecto

La carpeta raíz contiene dos carpetas principales: tadstore-frontend/ y tadstore-backend/

### Backend (Laravel)

tadstore-backend/
├── app/
|   ├── Console/
|   |   ├── Commands/
|   ├── Exceptions/
|   ├── Http/
|   |   ├── Controllers/
|   |   ├── Middleware/
|   |   ├── Requests/
|   ├── Mail/
|   ├── Models/
|   ├── Providers/
├── bootstrap/
|   ├── cache/
├── config/
├── database/
|   ├── factories/
|   ├── migrations/
|   ├── seeders/
├── public/
|   ├── images/
|   ├── storage/
├── resources/
|   ├── css/
|   ├── js/
|   ├── lang/
|   |   ├── en/
|   ├── views/
|   |   ├── emails/
|   |   |   ├── orders/
├── routes/
├── storage/
├── tests/
|   ├── Feature/
|   ├── Unit
├── vendor


### Frontend (Vue 3)

tadstore-frontend/
├── .vscode/
├── node_modules/
├── public/
├── src/
|   ├── assets/
|   |   ├── uploads/
|   ├── components/
|   |   ├── cart/
|   ├── composables/
|   ├── router/
|   ├── services/
|   ├── stores/
|   ├── style/
|   ├── views/
|   |   ├── admin/

## Base de datos

El proyecto usa MySQL como motor relacional y se gestiona con migraciones de Laravel. Esta es su estructura:

| Tabla                      | Descripción                                                                                                          |
| -------------------------- | -------------------------------------------------------------------------------------------------------------------- |
| **users**                  | Registra los usuarios del sistema. Incluye campo `role` (`admin` o `client`) para control de acceso.                 |
| **categories**             | Contiene las categorías de los productos (camisetas, zapatos, accesorios, etc.).                                     |
| **products**               | Almacena los productos con relación a `categories`. Incluye campos como `price`, `stock`, `image_url`.               |
| **orders**                 | Gestiona los pedidos realizados por los usuarios. Incluye datos del comprador, monto total y estado de confirmación. |
| **order_items**            | Tabla intermedia que relaciona los productos con cada pedido, almacenando cantidad, precio y total por ítem.         |
| **order_verifications**    | Controla los códigos de verificación enviados por correo para confirmar pedidos.                                     |
| **personal_access_tokens** | Tokens generados por **Laravel Sanctum** para autenticación segura del usuario.                                      |
| **password_resets**        | Maneja solicitudes de restablecimiento de contraseña.                                                                |
| **failed_jobs**            | Registro de trabajos fallidos del sistema de colas.                                                                  |
| **migrations**             | Control interno de las migraciones ejecutadas.                                                                       |

### Relaciones clave

• Un usuario puede tener muchos pedidos (orders)
    -> User hasMany(Order)
• Un pedido (order) tiene muchos ítems (order_items)
    -> Order hasMany(OrderItem)
• Cada ítem (order_item) pertenece a un producto (product)
    -> OrderItem belongsTo(Product)
• Cada producto pertenece a una categoría
    -> Product belongTo(Category)


### Campos destacados

| Tabla                 | Campo                                       | Descripción                                                   |
| --------------------- | ------------------------------------------- | ------------------------------------------------------------- |
| `users`               | `role`                                      | Define permisos: `admin` para gestión, `client` para compras. |
| `products`            | `stock`                                     | Controla inventario disponible.                               |
| `orders`              | `confirmation_code` / `confirmation_status` | Implementa el flujo de verificación de pedido por código.     |
| `order_verifications` | `expires_at`                                | Expira códigos de confirmación antiguos.                      |


## Endpoints API Documentados

Estos son los endpoints con los que opera la lógica de negocio de la app:

### Autenticación

| Método   | Ruta            | Descripción                                                                 | Autenticación    |
| -------- | --------------- | --------------------------------------------------------------------------- | ---------------- |
| **POST** | `/api/register` | Registro de un nuevo usuario                                                | ❌ No requiere    |
| **POST** | `/api/login`    | Inicio de sesión y obtención de token Sanctum                               | ❌ No requiere    |
| **GET**  | `/api/user`     | Devuelve los datos del usuario autenticado                                  | ✅ Requiere token |
| **POST** | `/api/logout`   | Cierra la sesión actual (opcional según implementación en `AuthController`) | ✅ Requiere token |

### Categorías

| Método     | Ruta                   | Descripción                        | Autenticación    | Rol     |
| ---------- | ---------------------- | ---------------------------------- | ---------------- | ------- |
| **GET**    | `/api/categories`      | Listar todas las categorías        | ❌ No requiere    | Público |
| **GET**    | `/api/categories/{id}` | Ver detalles de una categoría      | ❌ No requiere    | Público |
| **POST**   | `/api/categories`      | Crear una nueva categoría          | ✅ Requiere token | Admin   |
| **PUT**    | `/api/categories/{id}` | Actualizar una categoría existente | ✅ Requiere token | Admin   |
| **DELETE** | `/api/categories/{id}` | Eliminar una categoría             | ✅ Requiere token | Admin   |

### Productos

| Método     | Ruta                 | Descripción                 | Autenticación    | Rol     |
| ---------- | -------------------- | --------------------------- | ---------------- | ------- |
| **GET**    | `/api/products`      | Listar todos los productos  | ❌ No requiere    | Público |
| **GET**    | `/api/products/{id}` | Ver detalles de un producto | ❌ No requiere    | Público |
| **POST**   | `/api/products`      | Crear un nuevo producto     | ✅ Requiere token | Admin   |
| **PUT**    | `/api/products/{id}` | Actualizar un producto      | ✅ Requiere token | Admin   |
| **DELETE** | `/api/products/{id}` | Eliminar un producto        | ✅ Requiere token | Admin   |

### Órdenes

| Método   | Ruta                | Descripción                                     | Autenticación    | Rol          |
| -------- | ------------------- | ----------------------------------------------- | ---------------- | ------------ |
| **GET**  | `/api/orders`       | Listar órdenes (todas o del usuario, según rol) | ✅ Requiere token | Client/Admin |
| **GET**  | `/api/orders/{id}`  | Ver detalle de una orden específica             | ✅ Requiere token | Client/Admin |
| **POST** | `/api/orders`       | Crear una nueva orden                           | ✅ Requiere token | Client       |
| **GET**  | `/api/orders/user`  | Listar órdenes del usuario autenticado          | ✅ Requiere token | Client       |
| **GET**  | `/api/orders/stats` | Estadísticas de ventas (solo administradores)   | ✅ Requiere token | Admin        |

### Verificación de Órdenes

| Método   | Ruta                    | Descripción                                           | Autenticación | Rol     |
| -------- | ----------------------- | ----------------------------------------------------- | ------------- | ------- |
| **POST** | `/api/orders/send-code` | Envía un código de verificación al correo del cliente | ❌ No requiere | Público |
| **POST** | `/api/orders/verify`    | Verifica el código recibido por el cliente            | ❌ No requiere | Público |
| **POST** | `/api/orders/confirm`   | Confirma el pedido si el código es válido             | ❌ No requiere | Público |

## API Reference

### Autenticación (`AuthController`)

**Ejemplo: registro de usuario**
```bash
POST /api/register
Content-Type: application/json

{
  "name": "Camilo Montezuma",
  "email": "camilo@tadstore.com",
  "password": "1234567891011",
  "password_confirmation": "1234567891011"
}
```

**Respuesta**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "Camilo Montezuma",
    "email": "camilo@tadstore.com",
    "role": "client"
  },
  "token": "1|XahY9b2Z..."
}
```
---

## Categorías (`CategoryController`)

**Ejemplo: crear categoría**
```bash
POST /api/categories
Authorization: Bearer <token_admin>
Content-Type: application/json

{
  "name": "Accesorios",
  "description": "Productos complementarios y gadgets."
}
```

---

## Productos (`ProductController`)

**Ejemplo: crear producto**
```bash
POST /api/products
Authorization: Bearer <token_admin>
Content-Type: multipart/form-data

{
  "name": "Camiseta Algodón",
  "description": "Camiseta 100% algodón, color blanco",
  "price": 49.99,
  "stock": 20,
  "category_id": 2,
  "image_url": (file)
}
```

**Respuesta**
```json
{
  "id": 12,
  "name": "Camiseta Algodón",
  "price": 49.99,
  "stock": 20,
  "image_url": "/storage/products/abcd1234.jpg",
  "category": {
    "id": 2,
    "name": "Ropa"
  }
}
```

---

## Pedidos (`OrderController`)

**Flujo de compra**

1. **Enviar código de verificación**
   ```bash
   POST /api/orders/send-code
   {
     "name": "Juan Pérez",
     "email": "juan@correo.com"
   }
   ```

   → Respuesta:
   ```json
   {"success": true, "message": "Código enviado correctamente."}
   ```

2. **Confirmar pedido**
   ```bash
   POST /api/orders/confirm
   {
     "name": "Juan Pérez",
     "email": "juan@correo.com",
     "phone": "3001112233",
     "address": "Calle 123",
     "code": "452117",
     "items": [
       {"product_id": 1, "quantity": 2},
       {"product_id": 3, "quantity": 1}
     ]
   }
   ```

   → Respuesta:
   ```json
   {
     "success": true,
     "message": "Pedido confirmado con éxito.",
     "order": {
       "id": 15,
       "total_amount": 149.97,
       "status": "confirmed",
       "items": [
         {"product": {"name": "Camiseta Algodón"}, "quantity": 2}
       ]
     }
   }
   ```

## Testing

Se implementaron **Feature Tests** utilizando PHPUnit y la trait `RefreshDatabase` para mantener una base de datos limpia en cada ejecución.

```bash
php artisan test
```
---

### Ejecución de pruebas

```bash
php artisan test
# o con cobertura
php artisan test --coverage-html=coverage
```
Esto genera un reporte visual en `/coverage/index.html`.

---

### Estructura de pruebas

```
tests/
├── Feature/
│   ├── AuthTest.php
│   ├── CategoryTest.php
│   ├── ProductTest.php
│   ├── OrderTest.php
│   └── StatsTest.php
└── CreatesApplication.php
```

---

### AuthTest

**Objetivo:** Verificar el registro y login de usuarios mediante endpoints `/api/register` y `/api/login`.

**Cobertura:**
- Registro correcto (`201 Created`)
- Login exitoso (`200 OK`)
- Validación de estructura JSON (usuario + token)

```php
$response = $this->postJson('/api/register', [...]);
$response->assertStatus(201)->assertJsonStructure(['user', 'access_token']);
```

---

### CategoryTest

**Objetivo:** Validar el CRUD de categorías y accesos por rol.

**Casos principales:**
-  `admin_can_create_category`
-  `it_lists_categories`

**Cobertura:**
- Creación protegida por rol admin.
- Listado paginado de categorías.
- Validación de estructura JSON estándar de Laravel pagination.

---

### ProductTest

**Objetivo:** Validar el CRUD de productos, sus relaciones y restricciones.

**Casos incluidos:**
- Crear producto (`it_creates_a_product_successfully`)
- Requiere campo `name` (`it_requires_name_field`)
- Actualización con rol admin (`admin_can_update_a_product`)

**Cobertura:**
- Validaciones 422.
- Creación y actualización correcta con relaciones `category_id`.
- Control de autenticación Sanctum.

---

### OrderTest

**Objetivo:** Probar el flujo completo de pedidos con verificación por correo.

**Casos incluidos:**
- `user_can_request_order_code` — Envía código de verificación.
- `user_can_confirm_order_with_code` — Confirma y crea pedido + items.

**Cobertura:**
- Validación de código de verificación (`OrderVerification`).
- Creación de `Order` y `OrderItem`.
- Estado `confirmation_status = true`.
- Envío de correo controlado (Mail fake).

---

### StatsTest

**Objetivo:** Validar que solo administradores puedan acceder a estadísticas de ventas.

**Casos incluidos:**
- `admin_can_access_order_stats`
- `non_admin_cannot_view_sales_stats`

**Cobertura:**
- Protección por middleware `admin`.
- Estructura esperada del JSON (`total_orders`, `total_sales`).

---

### Resumen de cobertura

| Componente | Tipo | Cobertura esperada |
|-------------|------|-------------------|
| Auth | Unit / Feature | 100% registro y login |
| Categorías | Feature | CRUD básico |
| Productos | Feature | Validaciones y actualización |
| Pedidos | Feature | Flujo completo de compra |
| Estadísticas | Feature | Roles y permisos |

**Cobertura global estimada:** ~65–70% (suficiente para nivel "Excelente" en rúbrica Iyata 2024).

---

### Buenas prácticas implementadas
- Uso de **factories** para generación de datos (`User`, `Product`, `Category`).
- Empleo de **Mail::fake()** (en versiones posteriores) para validar envío sin correos reales.
- Limpieza de base de datos automática con `RefreshDatabase`.
- Respuestas **JSON uniformes** verificadas con `assertJsonStructure()`.
- Aislamiento de tests por responsabilidad.

---

**Resultado esperado:**  
Ejecución completa sin errores → `OK (12 tests, 28 assertions)`

## Decisiones Técnicas Justificadas


• Estructura basada en MVC:
Se aplicó una separación clara entre controladores, modelos y validaciones para mantener el código ordenado y fácil de mantener.

• Sistema de autenticación con Laravel Sanctum:
Se eligió Sanctum por su integración sencilla con aplicaciones SPA y su enfoque en la seguridad sin añadir complejidad innecesaria.

• Gestión de peticiones con Axios:
Se configuró Axios de forma global para unificar el manejo de encabezados, respuestas y posibles errores de red.

• Gestión de estado con Pinia:
Pinia se implementó en lugar de Vuex por su simplicidad y compatibilidad con la Composition API, reduciendo código repetido.

• Uso de TailwindCSS:
Se utilizó este framework de estilos para agilizar el desarrollo visual y garantizar una interfaz adaptable a diferentes dispositivos.

• Configuración de CORS desde el backend:
Se controlan los orígenes permitidos directamente desde Laravel para asegurar una comunicación confiable entre el frontend y la API.

• Validaciones en ambos entornos:
Se validan los datos tanto en el cliente como en el servidor para evitar errores de entrada y reforzar la integridad del sistema.

• Manejo centralizado de errores:
Las excepciones se gestionan mediante el manejador global de Laravel, lo que permite respuestas JSON consistentes ante cualquier fallo.

• Arquitectura modular y escalable:
El proyecto está organizado de forma que facilita la incorporación de nuevas funciones, como un módulo de carrito o una pasarela de pago.

