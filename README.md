<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# api-users-laravel

Resumen
Proyecto Laravel con endpoints de usuario, autenticación y posts. Este README explica cómo ejecutar el proyecto localmente.

Requisitos
- PHP 8.x
- Composer
- MySQL (o MariaDB) u otra DB soportada
- Node.js + npm (opcional para assets)
- Git
- (Opcional) GitHub CLI `gh`

Configuración y ejecución local
1. Instalar dependencias PHP:
   - composer install
2. Copiar archivo de entorno y configurar:
   - cp .env.example .env
   - Editar .env: ajustar DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_URL, etc.
3. Generar clave de aplicación:
   - php artisan key:generate
4. JWT (tymon/jwt-auth):
   - php artisan jwt:secret
   - (Asegúrate de que las configuraciones en config/auth.php y .env sean correctas: guard api => driver jwt)
5. Ejecutar migraciones y seeders:
   - php artisan migrate --seed
6. (Opcional) Instalar assets JS/CSS:
   - npm install
   - npm run dev
7. Ejecutar servidor de desarrollo:
   - php artisan serve
   - Acceder a: http://127.0.0.1:8000

Notas sobre tests y debug
- Ruta de depuración disponible: GET /api/debug-auth (ver headers y estado de autenticación).
- Para llamadas protegidas, incluir header:
  - Authorization: Bearer <token>
  - Accept: application/json

Problemas comunes
- Error de conexión a DB: asegúrate que la base de datos existe y credenciales de .env son correctas.
- JWT no válido: regenerar secret y volver a autenticarse (login).

## Postman

- La colección de Postman está incluida en: `postman/api-users-laravel.postman_collection.json`.
- IMPORTANTE: la colección usa la variable `{{base_url}}` en las URLs. Crea un Environment en Postman con:
  - base_url = http://127.0.0.1:8000
  - token = (rellena después del login)

Uso rápido
1. Levanta la API localmente:
   - php artisan serve
2. Importa la colección y crea el Environment con `base_url`.
3. Ejecuta la petición "Registrar Usuario" luego "Login Usuario" para obtener el JWT y copia el token en la variable `token`.
4. Las peticiones protegidas usan:
   - Authorization: Bearer {{token}}
   - Accept: application/json
