#!/usr/bin/env sh

set -eu

APP_PORT="${APP_PORT:-9000}"
DB_WAIT_TIMEOUT="${DB_WAIT_TIMEOUT:-60}"
APP_INIT_MAX_ATTEMPTS="${APP_INIT_MAX_ATTEMPTS:-10}"
ENV_FILE_SOURCE="${ENV_FILE_SOURCE:-/var/www/html/.env.host}"
ENV_FILE_TARGET="${ENV_FILE_TARGET:-/var/www/html/.env}"

upsert_env_value() {
    php -r '
[$file, $key, $value] = array_slice($argv, 1);
$contents = file_exists($file) ? file_get_contents($file) : "";
$escaped = addcslashes($value, "\\\"\n\r\t$");
$line = $key . "=\"" . $escaped . "\"";
$pattern = "/^" . preg_quote($key, "/") . "=.*$/m";

if (preg_match($pattern, $contents)) {
    $contents = preg_replace($pattern, $line, $contents, 1);
} else {
    if ($contents !== "" && !str_ends_with($contents, "\n")) {
        $contents .= "\n";
    }

    $contents .= $line . "\n";
}

file_put_contents($file, $contents);
' -- "$ENV_FILE_TARGET" "$1" "${2:-}"
}

env_value_is_set() {
    php -r '
[$file, $key] = array_slice($argv, 1);
$contents = file_exists($file) ? file_get_contents($file) : "";

if (!preg_match("/^" . preg_quote($key, "/") . "=(.*)$/m", $contents, $matches)) {
    exit(1);
}

$value = trim($matches[1]);
$value = trim($value, "\"'\''");
exit($value === "" ? 1 : 0);
' -- "$ENV_FILE_TARGET" "$1"
}

prepare_runtime_env() {
    echo "Preparando archivo de entorno runtime..."

    if [ -f "$ENV_FILE_SOURCE" ] && [ "$ENV_FILE_SOURCE" != "$ENV_FILE_TARGET" ]; then
        cp "$ENV_FILE_SOURCE" "$ENV_FILE_TARGET"
    elif [ ! -f "$ENV_FILE_TARGET" ]; then
        : > "$ENV_FILE_TARGET"
    fi

    upsert_env_value "DB_HOST" "${DB_HOST:-127.0.0.1}"
    upsert_env_value "DB_PORT" "${DB_PORT:-3306}"
    upsert_env_value "DB_DATABASE" "${DB_DATABASE:-laravel}"
    upsert_env_value "DB_USERNAME" "${DB_USERNAME:-root}"
    upsert_env_value "DB_PASSWORD" "${DB_PASSWORD:-}"

    if [ -n "${DEV_ADMIN_NAME:-}" ]; then
        upsert_env_value "DEV_ADMIN_NAME" "${DEV_ADMIN_NAME}"
    fi

    if [ -n "${DEV_ADMIN_EMAIL:-}" ]; then
        upsert_env_value "DEV_ADMIN_EMAIL" "${DEV_ADMIN_EMAIL}"
    fi

    if [ -n "${DEV_ADMIN_PASSWORD:-}" ]; then
        upsert_env_value "DEV_ADMIN_PASSWORD" "${DEV_ADMIN_PASSWORD}"
    fi
}

ensure_jwt_secret() {
    if env_value_is_set "JWT_SECRET"; then
        return 0
    fi

    echo "Generando JWT_SECRET para el entorno runtime..."
    php artisan jwt:secret --force
}

prepare_runtime_env
ensure_jwt_secret

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
