# Guía de Actualización: FrangyControl (2.x)

Esta guía explica cómo migrar y actualizar tu versión antigua de Frangy (por ejemplo, el commit de Julio de 2023) a la versión más reciente **sin perder información**.

## ¿Qué pasa con mis datos antiguos?
La actualización actual **es retrocompatible**. Esto significa que:
1. Las tablas nuevas (Cotizaciones, Inventario, Bitácora, Chat) se crearán vacías de forma segura sin borrar otras tablas.
2. Tus **clientes existentes serán encriptados automáticamente** mediante nuestra nueva capa de seguridad en la Base de Datos.
3. Tus **fotos antiguas** subidas con rutas absolutas seguirán siendo visibles.

---

## Opción A: Actualización usando Podman / Docker (Recomendado)

Si ya estás utilizando los scripts del repositorio para correr tu proyecto en contenedores, simplemente sigue estos pasos:

1. **Baja el entorno actual:**
   Si tienes el entorno levantado, detenlo con `Ctrl+C` en la consola donde corre `./unix-scrips/lanzar-pod.sh`.
   
2. **Obtén los últimos cambios (Git):**
   ```bash
   git pull origin main
   ```

3. **Reconstruye la imagen del contenedor:**
   La nueva versión actualiza PHP a la 8.4 y necesita nuevas dependencias de SO.
   ```bash
   ./unix-scrips/construir-pod.sh
   ```

4. **Lanza el nuevo entorno:**
   ```bash
   ./unix-scrips/lanzar-pod.sh
   ```
   *Nota: El script de lanzamiento (`start-frangy-app.sh`) ya está programado para ejecutar las migraciones automáticamente. Si tu base de datos estaba guardada en el volumen externo `frangy-control-db-data`, todo seguirá intacto y actualizado.*

---

## Opción B: Actualización Manual (Sin contenedores)

Si tienes el código hosteado directamente en un VPS (Apache/Nginx) usando XAMPP, Laragon, o PHP de manera nativa:

1. **Obtén el nuevo código:**
   Sube el código actualizado por FTP o usa `git pull`.

2. **Actualiza las dependencias (Composer):**
   Debes asegurar tener PHP 8.3 o 8.4 y luego instalar las nuevas versiones de Laravel:
   ```bash
   composer update --no-interaction --optimize-autoloader
   ```

3. **Corre el comando de actualización oficial de Frangy:**
   Hemos desarrollado un comando para evitar errores humanos, que comprobará si tienes PHP GD activado, limpiará tus cachés estancadas y migrará tu base de datos salvaguardando los datos viejos.
   ```bash
   php artisan frangy:upgrade
   ```
   *Responde "no" si ya respaldaste tu base de datos o si quieres proceder inmediatamente.*

4. **Reinicia tu servidor de colas (Si aplica):**
   Si usas `php artisan queue:work` para algo en el futuro, asegúrate de reiniciarlo:
   ```bash
   php artisan queue:restart
   ```

¡Disfruta de la nueva versión de FrangyControl!
