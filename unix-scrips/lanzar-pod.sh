#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="${ENV_FILE:-${ROOT_DIR}/.env}"
APP_START_SCRIPT_HOST="${APP_START_SCRIPT_HOST:-${ROOT_DIR}/docker/app/start.sh}"
APP_START_SCRIPT_CONTAINER="${APP_START_SCRIPT_CONTAINER:-/usr/local/bin/start-frangy-app.sh}"
APP_ENV_FILE_SOURCE_CONTAINER="${APP_ENV_FILE_SOURCE_CONTAINER:-/var/www/html/.env.host}"
APP_ENV_FILE_TARGET_CONTAINER="${APP_ENV_FILE_TARGET_CONTAINER:-/var/www/html/.env}"
IMAGE_NAME="${IMAGE_NAME:-frangy-control}"
IMAGE_TAG="${IMAGE_TAG:-latest}"
POD_NAME="${POD_NAME:-frangy-control-pod}"
CONTAINER_NAME="${CONTAINER_NAME:-frangy-control-app}"
DB_CONTAINER_NAME="${DB_CONTAINER_NAME:-frangy-control-db}"
DB_IMAGE="${DB_IMAGE:-docker.io/library/mysql:8.0}"
DB_VOLUME_NAME="${DB_VOLUME_NAME:-frangy-control-db-data}"
DB_SERVICE_NAME="${DB_SERVICE_NAME:-db}"
NETWORK_NAME="${NETWORK_NAME:-frangy-control-net}"
PODMAN_FALLBACK_DB_HOST="${PODMAN_FALLBACK_DB_HOST:-host.containers.internal}"
HOST_PORT="${HOST_PORT:-9000}"
CONTAINER_PORT="${CONTAINER_PORT:-9000}"
HOST_DB_PORT="${HOST_DB_PORT:-3306}"
DB_CONTAINER_PORT="${DB_CONTAINER_PORT:-3306}"
APP_WAIT_TIMEOUT="${APP_WAIT_TIMEOUT:-90}"
STOP_TIMEOUT="${STOP_TIMEOUT:-10}"
LOG_TAIL="${LOG_TAIL:-30}"
FOLLOW_LOGS="${FOLLOW_LOGS:-1}"
PROMPT_ADMIN_ON_LAUNCH="${PROMPT_ADMIN_ON_LAUNCH:-1}"

STACK_MODE=""
LOG_FOLLOW_PID=""
ADMIN_NAME_VALUE=""
ADMIN_EMAIL_VALUE=""
ADMIN_PASSWORD_VALUE=""

ensure_required_files_exist() {
    if [[ ! -f "${ENV_FILE}" ]]; then
        echo "No existe ${ENV_FILE}. Crea tu .env antes de lanzar el proyecto." >&2
        exit 1
    fi

    if [[ ! -f "${APP_START_SCRIPT_HOST}" ]]; then
        echo "No existe ${APP_START_SCRIPT_HOST}. Falta el script de arranque de la app." >&2
        exit 1
    fi
}

load_environment() {
    if [[ -f "${ENV_FILE}" ]]; then
        set -a
        # shellcheck disable=SC1090
        . "${ENV_FILE}"
        set +a
    fi
}

ensure_required_files_exist
load_environment

APP_DB_DATABASE="${DB_DATABASE:-laravel}"
APP_DB_USERNAME="${DB_USERNAME:-root}"
APP_DB_PASSWORD="${DB_PASSWORD:-}"
DB_ROOT_PASSWORD="${DB_ROOT_PASSWORD:-rootpassword}"
ADMIN_NAME_VALUE="${DEV_ADMIN_NAME:-Super Usuario Dev}"
ADMIN_EMAIL_VALUE="${DEV_ADMIN_EMAIL:-superusuario@outlook.com}"
ADMIN_PASSWORD_VALUE="${DEV_ADMIN_PASSWORD:-superusuariofrangy}"

prompt_value_with_default() {
    local label="$1"
    local default_value="$2"
    local value=""

    if [[ ! -t 0 ]]; then
        printf '%s' "${default_value}"
        return 0
    fi

    read -r -p "${label} [${default_value}]: " value || true
    printf '%s' "${value:-${default_value}}"
}

prompt_secret_with_default() {
    local label="$1"
    local default_value="$2"
    local value=""

    if [[ ! -t 0 ]]; then
        printf '%s' "${default_value}"
        return 0
    fi

    read -r -s -p "${label} [enter para conservar el actual]: " value || true
    echo
    printf '%s' "${value:-${default_value}}"
}

prompt_admin_configuration() {
    if [[ "${PROMPT_ADMIN_ON_LAUNCH}" == "0" || ! -t 0 ]]; then
        return 0
    fi

    echo "Configura el administrador inicial que se instalara en Laravel."

    while true; do
        ADMIN_NAME_VALUE="$(prompt_value_with_default "Nombre del administrador" "${ADMIN_NAME_VALUE}")"
        [[ -n "${ADMIN_NAME_VALUE}" ]] && break
    done

    while true; do
        ADMIN_EMAIL_VALUE="$(prompt_value_with_default "Correo del administrador" "${ADMIN_EMAIL_VALUE}")"
        [[ -n "${ADMIN_EMAIL_VALUE}" ]] && break
    done

    while true; do
        ADMIN_PASSWORD_VALUE="$(prompt_secret_with_default "Password del administrador" "${ADMIN_PASSWORD_VALUE}")"
        [[ -n "${ADMIN_PASSWORD_VALUE}" ]] && break
    done
}

detect_container_tool() {
    if [[ -n "${CONTAINER_TOOL:-}" ]]; then
        echo "${CONTAINER_TOOL}"
        return 0
    fi

    if command -v podman >/dev/null 2>&1; then
        echo "podman"
        return 0
    fi

    if command -v docker >/dev/null 2>&1; then
        echo "docker"
        return 0
    fi

    echo "No se encontro podman ni docker en el sistema." >&2
    exit 1
}

image_exists() {
    local tool="$1"

    if [[ "${tool}" == "podman" ]]; then
        podman image exists "${IMAGE_NAME}:${IMAGE_TAG}"
        return $?
    fi

    docker image inspect "${IMAGE_NAME}:${IMAGE_TAG}" >/dev/null 2>&1
}

container_exists() {
    local tool="$1"
    local name="$2"

    "${tool}" container inspect "${name}" >/dev/null 2>&1
}

remove_existing_container() {
    local tool="$1"
    local name="$2"

    if container_exists "${tool}" "${name}"; then
        echo "Eliminando el contenedor previo ${name}..."
        "${tool}" rm -f "${name}" >/dev/null
    fi
}

ensure_volume_exists() {
    local tool="$1"
    local volume_name="$2"

    if ! "${tool}" volume inspect "${volume_name}" >/dev/null 2>&1; then
        echo "Creando volumen ${volume_name}..."
        "${tool}" volume create "${volume_name}" >/dev/null
    else
        echo "El volumen ${volume_name} ya existe. Se reutilizara."
    fi
}

ensure_network_exists() {
    local tool="$1"
    local network_name="$2"

    if ! "${tool}" network inspect "${network_name}" >/dev/null 2>&1; then
        echo "Creando red ${network_name}..."
        "${tool}" network create "${network_name}" >/dev/null
    else
        echo "La red ${network_name} ya existe. Se reutilizara."
    fi
}

show_recent_app_logs() {
    local tool="$1"

    echo "Ultimos logs de ${CONTAINER_NAME}:"
    "${tool}" logs --tail 80 "${CONTAINER_NAME}" 2>/dev/null || true
}

stop_container_if_exists() {
    local tool="$1"
    local name="$2"

    if container_exists "${tool}" "${name}"; then
        echo "Deteniendo ${name}..."
        "${tool}" stop -t "${STOP_TIMEOUT}" "${name}" >/dev/null 2>&1 || true
    fi
}

stop_pod_if_exists() {
    if podman pod inspect "${POD_NAME}" >/dev/null 2>&1; then
        echo "Deteniendo pod ${POD_NAME}..."
        podman pod stop -t "${STOP_TIMEOUT}" "${POD_NAME}" >/dev/null 2>&1 || true
    fi
}

remove_pod_if_exists() {
    if podman pod inspect "${POD_NAME}" >/dev/null 2>&1; then
        echo "Eliminando pod ${POD_NAME}..."
        podman pod rm -f "${POD_NAME}" >/dev/null 2>&1 || true
    fi
}

stop_current_stack() {
    case "${STACK_MODE:-}" in
        podman_pod)
            stop_pod_if_exists
            ;;
        podman_fallback)
            stop_container_if_exists "podman" "${CONTAINER_NAME}"
            stop_container_if_exists "podman" "${DB_CONTAINER_NAME}"
            ;;
        docker_stack)
            stop_container_if_exists "docker" "${CONTAINER_NAME}"
            stop_container_if_exists "docker" "${DB_CONTAINER_NAME}"
            ;;
    esac
}

start_container_if_exists() {
    local tool="$1"
    local name="$2"

    if container_exists "${tool}" "${name}"; then
        "${tool}" start "${name}" >/dev/null 2>&1 || true
    fi
}

reuse_existing_stack() {
    local tool="$1"
    local existing_mode="$2"

    case "${existing_mode}" in
        podman_pod)
            echo "Se reutilizara el pod existente ${POD_NAME}."
            podman pod start "${POD_NAME}" >/dev/null 2>&1 || true
            STACK_MODE="podman_pod"
            wait_for_laravel_http "podman"
            ;;
        podman_fallback)
            echo "Se reutilizaran los contenedores existentes."
            start_container_if_exists "podman" "${DB_CONTAINER_NAME}"
            start_container_if_exists "podman" "${CONTAINER_NAME}"
            STACK_MODE="podman_fallback"
            wait_for_laravel_http "podman"
            ;;
        docker_stack)
            echo "Se reutilizaran los contenedores existentes."
            start_container_if_exists "docker" "${DB_CONTAINER_NAME}"
            start_container_if_exists "docker" "${CONTAINER_NAME}"
            STACK_MODE="docker_stack"
            wait_for_laravel_http "docker"
            ;;
    esac
}

detect_existing_stack_mode() {
    local tool="$1"

    if [[ "${tool}" == "podman" ]]; then
        if podman pod inspect "${POD_NAME}" >/dev/null 2>&1 && \
           podman ps -a --filter "pod=${POD_NAME}" --format '{{.Names}}' | \
           grep -Fxq -e "${CONTAINER_NAME}" -e "${DB_CONTAINER_NAME}"; then
            echo "podman_pod"
            return 0
        fi

        if container_exists "podman" "${CONTAINER_NAME}" || container_exists "podman" "${DB_CONTAINER_NAME}"; then
            echo "podman_fallback"
            return 0
        fi
    fi

    if [[ "${tool}" == "docker" ]] && ( container_exists "docker" "${CONTAINER_NAME}" || container_exists "docker" "${DB_CONTAINER_NAME}" ); then
        echo "docker_stack"
        return 0
    fi

    return 1
}

handle_existing_stack_before_launch() {
    local tool="$1"
    local existing_mode=""

    if ! existing_mode="$(detect_existing_stack_mode "${tool}")"; then
        return 0
    fi

    echo "Ya existe un entorno con esos nombres. Se reutilizara."
    reuse_existing_stack "${tool}" "${existing_mode}"
    return 1
}

handle_interrupt() {
    echo
    echo "Interrupcion recibida. Deteniendo el entorno..."

    if [[ -n "${LOG_FOLLOW_PID}" ]] && kill -0 "${LOG_FOLLOW_PID}" >/dev/null 2>&1; then
        kill "${LOG_FOLLOW_PID}" >/dev/null 2>&1 || true
        wait "${LOG_FOLLOW_PID}" 2>/dev/null || true
    fi

    stop_current_stack
    exit 130
}

wait_for_laravel_http() {
    local tool="$1"
    local elapsed=0

    if ! command -v curl >/dev/null 2>&1; then
        echo "curl no esta disponible; se omitira la verificacion HTTP final."
        return 0
    fi

    echo "Esperando respuesta HTTP de Laravel en http://127.0.0.1:${HOST_PORT}..."
    while (( elapsed < APP_WAIT_TIMEOUT )); do
        if curl -I --silent --fail --max-time 2 "http://127.0.0.1:${HOST_PORT}" >/dev/null 2>&1; then
            return 0
        fi

        if ! "${tool}" container inspect "${CONTAINER_NAME}" >/dev/null 2>&1; then
            echo "El contenedor ${CONTAINER_NAME} ya no existe mientras se esperaba la respuesta HTTP." >&2
            show_recent_app_logs "${tool}"
            return 1
        fi

        sleep 2
        elapsed=$((elapsed + 2))
    done

    echo "Laravel no respondio por HTTP tras ${APP_WAIT_TIMEOUT} segundos." >&2
    show_recent_app_logs "${tool}"
    return 1
}

follow_laravel_logs() {
    local tool="$1"

    if [[ "${FOLLOW_LOGS}" == "0" ]]; then
        return 0
    fi

    echo "Mostrando logs de Laravel en tiempo real. Presiona Ctrl+C para detener el entorno."
    trap 'handle_interrupt' INT TERM

    "${tool}" logs --follow --tail "${LOG_TAIL}" "${CONTAINER_NAME}" &
    LOG_FOLLOW_PID=$!
    wait "${LOG_FOLLOW_PID}" || true
    LOG_FOLLOW_PID=""
}

build_database_env_args() {
    DB_ENV_ARGS=(
        -e "MYSQL_DATABASE=${APP_DB_DATABASE}"
    )

    if [[ "${APP_DB_USERNAME}" == "root" ]]; then
        if [[ -n "${APP_DB_PASSWORD}" ]]; then
            DB_ENV_ARGS+=(-e "MYSQL_ROOT_PASSWORD=${APP_DB_PASSWORD}")
        else
            DB_ENV_ARGS+=(-e "MYSQL_ALLOW_EMPTY_PASSWORD=yes")
        fi

        return 0
    fi

    if [[ -z "${APP_DB_PASSWORD}" ]]; then
        echo "DB_PASSWORD no puede estar vacio cuando DB_USERNAME no es root." >&2
        exit 1
    fi

    DB_ENV_ARGS+=(
        -e "MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD}"
        -e "MYSQL_USER=${APP_DB_USERNAME}"
        -e "MYSQL_PASSWORD=${APP_DB_PASSWORD}"
    )
}

build_app_db_env_args() {
    local db_host="$1"
    local db_port="$2"

    APP_DB_ENV_ARGS=(
        -e "APP_PORT=${CONTAINER_PORT}"
        -e "ENV_FILE_SOURCE=${APP_ENV_FILE_SOURCE_CONTAINER}"
        -e "ENV_FILE_TARGET=${APP_ENV_FILE_TARGET_CONTAINER}"
        -e "DB_HOST=${db_host}"
        -e "DB_PORT=${db_port}"
        -e "DB_DATABASE=${APP_DB_DATABASE}"
        -e "DB_USERNAME=${APP_DB_USERNAME}"
        -e "DB_PASSWORD=${APP_DB_PASSWORD}"
        -e "DEV_ADMIN_NAME=${ADMIN_NAME_VALUE}"
        -e "DEV_ADMIN_EMAIL=${ADMIN_EMAIL_VALUE}"
        -e "DEV_ADMIN_PASSWORD=${ADMIN_PASSWORD_VALUE}"
        -v "${ENV_FILE}:${APP_ENV_FILE_SOURCE_CONTAINER}:ro"
        -v "${APP_START_SCRIPT_HOST}:${APP_START_SCRIPT_CONTAINER}:ro"
    )
}

launch_database_container_with_tool() {
    local tool="$1"
    local pod_name="${2:-}"
    local run_error=""
    local db_run_args=()

    remove_existing_container "${tool}" "${DB_CONTAINER_NAME}"

    if [[ -n "${pod_name}" ]]; then
        echo "Lanzando la base de datos ${DB_CONTAINER_NAME} dentro del pod ${pod_name}..."
        db_run_args=(
            --name "${DB_CONTAINER_NAME}"
            --pod "${pod_name}"
            -v "${DB_VOLUME_NAME}:/var/lib/mysql"
        )

        if run_error="$("${tool}" run -d \
            "${db_run_args[@]}" \
            "${DB_ENV_ARGS[@]}" \
            "${DB_IMAGE}" 2>&1 >/dev/null)"; then
            return 0
        fi

        if [[ "${run_error}" == *"catatonit"* ]]; then
            echo "Podman no encontro catatonit en el host mientras iniciaba MySQL; se continuara sin pod." >&2
        elif [[ "${run_error}" == *"cannot set hostname when joining the pod UTS namespace"* ]]; then
            echo "Podman rechazo el hostname del contenedor porque el pod comparte el namespace UTS." >&2
        elif [[ -n "${run_error}" ]]; then
            echo "${run_error}" >&2
        fi

        return 1
    fi

    echo "Lanzando la base de datos ${DB_CONTAINER_NAME} en la red ${NETWORK_NAME}..."
    db_run_args=(
        --name "${DB_CONTAINER_NAME}"
        --hostname "${DB_SERVICE_NAME}"
        --network "${NETWORK_NAME}"
        --network-alias "${DB_SERVICE_NAME}"
        -p "${HOST_DB_PORT}:${DB_CONTAINER_PORT}"
        -v "${DB_VOLUME_NAME}:/var/lib/mysql"
    )

    if run_error="$("${tool}" run -d \
        "${db_run_args[@]}" \
        "${DB_ENV_ARGS[@]}" \
        "${DB_IMAGE}" 2>&1 >/dev/null)"; then
        return 0
    fi

    if [[ -n "${run_error}" ]]; then
        echo "${run_error}" >&2
    fi

    return 1
}

launch_app_container_with_tool() {
    local tool="$1"
    local db_host="$2"
    local db_port="$3"
    local pod_name="${4:-}"
    local run_error=""

    build_app_db_env_args "${db_host}" "${db_port}"
    remove_existing_container "${tool}" "${CONTAINER_NAME}"

    if [[ -n "${pod_name}" ]]; then
        echo "Lanzando el contenedor ${CONTAINER_NAME} dentro del pod ${pod_name}..."
        if run_error="$("${tool}" run -d \
            --name "${CONTAINER_NAME}" \
            --pod "${pod_name}" \
            "${APP_DB_ENV_ARGS[@]}" \
            "${IMAGE_NAME}:${IMAGE_TAG}" \
            sh "${APP_START_SCRIPT_CONTAINER}" 2>&1 >/dev/null)"; then
            return 0
        fi

        if [[ "${run_error}" == *"catatonit"* ]]; then
            echo "Podman no encontro catatonit en el host mientras iniciaba la app; se continuara sin pod." >&2
        elif [[ "${run_error}" == *"extra host entries must be specified on the pod"* ]]; then
            echo "Podman rechazo la configuracion de hosts extra porque la red del contenedor ya viene del pod." >&2
        elif [[ -n "${run_error}" ]]; then
            echo "${run_error}" >&2
        fi

        return 1
    fi

    echo "Lanzando el contenedor ${CONTAINER_NAME} en la red ${NETWORK_NAME}..."
    if run_error="$("${tool}" run -d \
        --name "${CONTAINER_NAME}" \
        --network "${NETWORK_NAME}" \
        -p "${HOST_PORT}:${CONTAINER_PORT}" \
        "${APP_DB_ENV_ARGS[@]}" \
        "${IMAGE_NAME}:${IMAGE_TAG}" \
        sh "${APP_START_SCRIPT_CONTAINER}" 2>&1 >/dev/null)"; then
        return 0
    fi

    if [[ -n "${run_error}" ]]; then
        echo "${run_error}" >&2
    fi

    return 1
}

launch_podman_database_container_without_pod() {
    local run_error=""

    remove_existing_container "podman" "${DB_CONTAINER_NAME}"

    echo "Lanzando la base de datos ${DB_CONTAINER_NAME} sin pod..."
    if run_error="$(podman run -d \
        --name "${DB_CONTAINER_NAME}" \
        -p "${HOST_DB_PORT}:${DB_CONTAINER_PORT}" \
        -v "${DB_VOLUME_NAME}:/var/lib/mysql" \
        "${DB_ENV_ARGS[@]}" \
        "${DB_IMAGE}" 2>&1 >/dev/null)"; then
        return 0
    fi

    if [[ -n "${run_error}" ]]; then
        echo "${run_error}" >&2
    fi

    return 1
}

launch_podman_app_container_without_pod() {
    local run_error=""

    build_app_db_env_args "${PODMAN_FALLBACK_DB_HOST}" "${HOST_DB_PORT}"
    remove_existing_container "podman" "${CONTAINER_NAME}"

    echo "Lanzando el contenedor ${CONTAINER_NAME} sin pod..."
    if run_error="$(podman run -d \
        --name "${CONTAINER_NAME}" \
        -p "${HOST_PORT}:${CONTAINER_PORT}" \
        "${APP_DB_ENV_ARGS[@]}" \
        "${IMAGE_NAME}:${IMAGE_TAG}" \
        sh "${APP_START_SCRIPT_CONTAINER}" 2>&1 >/dev/null)"; then
        return 0
    fi

    if [[ -n "${run_error}" ]]; then
        echo "${run_error}" >&2
    fi

    return 1
}

launch_podman_stack_without_pod() {
    ensure_volume_exists "podman" "${DB_VOLUME_NAME}"
    build_database_env_args
    launch_podman_database_container_without_pod
    launch_podman_app_container_without_pod
    STACK_MODE="podman_fallback"
    wait_for_laravel_http "podman"

    echo "Contenedores lanzados correctamente sin pod."
    echo "Laravel quedo disponible en http://localhost:${HOST_PORT}"
    echo "MySQL quedo expuesto en localhost:${HOST_DB_PORT}"
    echo "Los datos de la base quedan persistidos en el volumen ${DB_VOLUME_NAME}."
}

try_launch_podman_with_pod() {
    local create_error=""
    local pod_created_now="0"

    if ! podman pod inspect "${POD_NAME}" >/dev/null 2>&1; then
        echo "Creando pod ${POD_NAME}..."
        if ! create_error="$(podman pod create \
            --name "${POD_NAME}" \
            -p "${HOST_PORT}:${CONTAINER_PORT}" \
            -p "${HOST_DB_PORT}:${DB_CONTAINER_PORT}" \
            2>&1 >/dev/null)"; then
            if [[ "${create_error}" == *"catatonit"* ]]; then
                echo "Podman no encontro catatonit en el host; se continuara sin pod." >&2
            else
                echo "No se pudo crear el pod ${POD_NAME}; se continuara sin pod." >&2
                echo "${create_error}" >&2
            fi

            return 1
        fi

        pod_created_now="1"
    else
        echo "El pod ${POD_NAME} ya existe. Se reutilizara."
    fi

    ensure_volume_exists "podman" "${DB_VOLUME_NAME}"
    build_database_env_args

    if ! launch_database_container_with_tool "podman" "${POD_NAME}"; then
        echo "No se pudo lanzar la base de datos dentro del pod ${POD_NAME}." >&2
        if [[ "${pod_created_now}" == "1" ]]; then
            remove_pod_if_exists
        fi
        return 1
    fi

    if ! launch_app_container_with_tool "podman" "127.0.0.1" "${DB_CONTAINER_PORT}" "${POD_NAME}"; then
        echo "No se pudo lanzar la app dentro del pod ${POD_NAME}." >&2
        if [[ "${pod_created_now}" == "1" ]]; then
            remove_pod_if_exists
        fi
        return 1
    fi

    STACK_MODE="podman_pod"
    wait_for_laravel_http "podman"

    echo "Pod lanzado correctamente."
    echo "Laravel quedo disponible en http://localhost:${HOST_PORT}"
    echo "MySQL quedo expuesto en localhost:${HOST_DB_PORT}"
    echo "Los datos de la base quedan persistidos en el volumen ${DB_VOLUME_NAME}."
}

launch_docker_stack() {
    echo "Docker no maneja pods de forma nativa; se lanzara un stack equivalente."

    ensure_network_exists "docker" "${NETWORK_NAME}"
    ensure_volume_exists "docker" "${DB_VOLUME_NAME}"
    build_database_env_args
    launch_database_container_with_tool "docker"
    launch_app_container_with_tool "docker" "${DB_SERVICE_NAME}" "${DB_CONTAINER_PORT}"
    STACK_MODE="docker_stack"
    wait_for_laravel_http "docker"

    echo "Contenedores lanzados correctamente."
    echo "Laravel quedo disponible en http://localhost:${HOST_PORT}"
    echo "MySQL quedo expuesto en localhost:${HOST_DB_PORT}"
    echo "Los datos de la base quedan persistidos en el volumen ${DB_VOLUME_NAME}."
}

CONTAINER_TOOL="$(detect_container_tool)"

if ! handle_existing_stack_before_launch "${CONTAINER_TOOL}"; then
    follow_laravel_logs "${CONTAINER_TOOL}"
    exit 0
fi

if ! image_exists "${CONTAINER_TOOL}"; then
    echo "La imagen ${IMAGE_NAME}:${IMAGE_TAG} no existe." >&2
    echo "Ejecuta primero unix-scrips/construir-pod.sh" >&2
    exit 1
fi

prompt_admin_configuration

if [[ "${CONTAINER_TOOL}" == "podman" ]]; then
    if try_launch_podman_with_pod; then
        follow_laravel_logs "podman"
        exit 0
    fi

    launch_podman_stack_without_pod
    follow_laravel_logs "podman"
    exit 0
fi

launch_docker_stack
follow_laravel_logs "docker"
