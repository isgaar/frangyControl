Frangy Control

Version actual: `1.1.0`

Proyecto Laravel para el control de clientes, usuarios y ordenes de servicio.

**Instalacion Dev**
1. Clona el repositorio y entra al proyecto.
```bash
git clone <repo-url>
cd frangyControl
```
2. Crea tu archivo de entorno.
```bash
cp .env.example .env
```
3. Configura en `.env` tu conexion a base de datos y, si quieres, ajusta las credenciales del admin inicial:
```dotenv
DEV_ADMIN_NAME="Super Usuario Dev"
DEV_ADMIN_EMAIL=superusuario@outlook.com
DEV_ADMIN_PASSWORD=superusuariofrangy
```
4. Instala dependencias PHP y front.
```bash
composer install
npm install
```
5. Ejecuta la instalacion de desarrollo. Este paso corre migraciones, roles, permisos, `storage:link` y lanza el job que crea o actualiza el administrador inicial.
```bash
composer run install-dev
```
6. Levanta el proyecto en desarrollo.
```bash
php artisan serve
```

**Frontend Dev**
Si vas a trabajar estilos o assets, en otra terminal puedes usar:
```bash
npm run dev
```

**Acceso Inicial**
- Usuario: `superusuario@outlook.com`
- Password: `superusuariofrangy`

Si cambias `DEV_ADMIN_EMAIL` o `DEV_ADMIN_PASSWORD` en `.env`, el comando `composer run install-dev` actualizara ese administrador.

**Contenedores**
Tambien puedes usar los scripts basados en el `Dockerfile`:
```bash
./unix-scrips/construir-pod.sh
./unix-scrips/lanzar-pod.sh
```

`lanzar-pod.sh` puede reutilizar o eliminar un entorno existente, pregunta por el administrador inicial, levanta la base de datos con volumen persistente, inicia Laravel dentro del contenedor de app, ejecuta `php artisan project:install-dev --force`, deja la aplicacion disponible en `http://localhost:9000` y sigue mostrando los logs de Laravel en tiempo real. Si presionas `Ctrl+C`, el script detiene el pod o los contenedores levantados.

Dev Ismael
