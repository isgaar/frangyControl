#!/usr/bin/env sh

set -eu

APP_PORT="${APP_PORT:-9000}"
DB_WAIT_TIMEOUT="${DB_WAIT_TIMEOUT:-60}"
APP_INIT_MAX_ATTEMPTS="${APP_INIT_MAX_ATTEMPTS:-10}"

echo "Esperando la base de datos en ${DB_HOST:-127.0.0.1}:${DB_PORT:-3306}..."
php -r '
$host = getenv("DB_HOST") ?: "127.0.0.1";
$port = (int) (getenv("DB_PORT") ?: 3306);
$database = getenv("DB_DATABASE") ?: "laravel";
$username = getenv("DB_USERNAME") ?: "root";
$password = getenv("DB_PASSWORD");
$password = $password === false ? "" : $password;
$timeout = (int) (getenv("DB_WAIT_TIMEOUT") ?: 60);
$start = time();
$lastError = "sin error";

do {
    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$database}",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 2,
            ]
        );
        $pdo->query("SELECT 1");
        exit(0);
    } catch (Throwable $exception) {
        $lastError = $exception->getMessage();
    }

    sleep(2);
} while ((time() - $start) < $timeout);

fwrite(STDERR, "No se pudo conectar a {$host}:{$port}/{$database} tras {$timeout} segundos. Ultimo error: {$lastError}\n");
exit(1);
'

attempt=1
while [ "$attempt" -le "$APP_INIT_MAX_ATTEMPTS" ]; do
    echo "Inicializando Laravel (intento ${attempt}/${APP_INIT_MAX_ATTEMPTS})..."
    set -- project:install-dev --force --admin-no-prompt

    if [ -n "${DEV_ADMIN_NAME:-}" ]; then
        set -- "$@" "--admin-name=${DEV_ADMIN_NAME}"
    fi

    if [ -n "${DEV_ADMIN_EMAIL:-}" ]; then
        set -- "$@" "--admin-email=${DEV_ADMIN_EMAIL}"
    fi

    if [ -n "${DEV_ADMIN_PASSWORD:-}" ]; then
        set -- "$@" "--admin-password=${DEV_ADMIN_PASSWORD}"
    fi

    if php artisan "$@"; then
        break
    fi

    if [ "$attempt" -eq "$APP_INIT_MAX_ATTEMPTS" ]; then
        echo "No se pudo completar la inicializacion de Laravel." >&2
        exit 1
    fi

    attempt=$((attempt + 1))
    sleep 3
done

echo "Levantando Laravel en 0.0.0.0:${APP_PORT}..."
exec php artisan serve --host=0.0.0.0 --port="${APP_PORT}"
