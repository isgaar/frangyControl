#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
IMAGE_NAME="${IMAGE_NAME:-frangy-control}"
IMAGE_TAG="${IMAGE_TAG:-latest}"
DOCKERFILE_PATH="${DOCKERFILE_PATH:-${ROOT_DIR}/Dockerfile}"
POD_NAME="${POD_NAME:-frangy-control-pod}"
CONTAINER_NAME="${CONTAINER_NAME:-frangy-control-app}"
DB_CONTAINER_NAME="${DB_CONTAINER_NAME:-frangy-control-db}"
DB_VOLUME_NAME="${DB_VOLUME_NAME:-frangy-control-db-data}"
NETWORK_NAME="${NETWORK_NAME:-frangy-control-net}"

print_usage() {
    cat <<EOF
Uso: $(basename "$0") [--help]

Antes de construir, este script detecta y elimina cualquier entorno previo
de Frangy asociado a los nombres configurados: pod, contenedores, volumen
de datos y red si existen.
EOF
}

parse_args() {
    while [[ $# -gt 0 ]]; do
        case "$1" in
            -h|--help)
                print_usage
                exit 0
                ;;
            *)
                echo "Opcion no reconocida: $1" >&2
                print_usage >&2
                exit 1
                ;;
        esac

        shift
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

container_exists() {
    local tool="$1"
    local name="$2"

    "${tool}" container inspect "${name}" >/dev/null 2>&1
}

volume_exists() {
    local tool="$1"
    local name="$2"

    "${tool}" volume inspect "${name}" >/dev/null 2>&1
}

network_exists() {
    local tool="$1"
    local name="$2"

    "${tool}" network inspect "${name}" >/dev/null 2>&1
}

remove_container_if_exists() {
    local tool="$1"
    local name="$2"

    if container_exists "${tool}" "${name}"; then
        echo "Eliminando contenedor existente ${name}..."
        "${tool}" rm -f "${name}" >/dev/null 2>&1 || true
    fi
}

remove_volume_if_exists() {
    local tool="$1"
    local name="$2"

    if volume_exists "${tool}" "${name}"; then
        echo "Eliminando volumen existente ${name}..."
        "${tool}" volume rm -f "${name}" >/dev/null 2>&1 || true
    fi
}

remove_network_if_exists() {
    local tool="$1"
    local name="$2"

    if network_exists "${tool}" "${name}"; then
        echo "Eliminando red existente ${name}..."
        "${tool}" network rm "${name}" >/dev/null 2>&1 || true
    fi
}

cleanup_existing_frangy_stack() {
    local tool="$1"
    local found_existing="0"

    if [[ "${tool}" != "podman" ]]; then
        if container_exists "${tool}" "${CONTAINER_NAME}" || \
           container_exists "${tool}" "${DB_CONTAINER_NAME}" || \
           volume_exists "${tool}" "${DB_VOLUME_NAME}" || \
           network_exists "${tool}" "${NETWORK_NAME}"; then
            found_existing="1"
        fi
    else
        if podman pod inspect "${POD_NAME}" >/dev/null 2>&1 || \
           container_exists "${tool}" "${CONTAINER_NAME}" || \
           container_exists "${tool}" "${DB_CONTAINER_NAME}" || \
           volume_exists "${tool}" "${DB_VOLUME_NAME}" || \
           network_exists "${tool}" "${NETWORK_NAME}"; then
            found_existing="1"
        fi
    fi

    if [[ "${found_existing}" != "1" ]]; then
        echo "No se detecto un entorno previo de Frangy. Se construira desde cero."
        return 0
    fi

    echo "Se detecto un entorno previo de Frangy. Se eliminara antes de construir."

    if [[ "${tool}" == "podman" ]] && podman pod inspect "${POD_NAME}" >/dev/null 2>&1; then
        echo "Eliminando pod existente ${POD_NAME}..."
        podman pod rm -f "${POD_NAME}" >/dev/null 2>&1 || true
    fi

    remove_container_if_exists "${tool}" "${CONTAINER_NAME}"
    remove_container_if_exists "${tool}" "${DB_CONTAINER_NAME}"
    remove_volume_if_exists "${tool}" "${DB_VOLUME_NAME}"
    remove_network_if_exists "${tool}" "${NETWORK_NAME}"
}

parse_args "$@"

CONTAINER_TOOL="$(detect_container_tool)"
cleanup_existing_frangy_stack "${CONTAINER_TOOL}"

echo "Construyendo la imagen ${IMAGE_NAME}:${IMAGE_TAG} usando ${CONTAINER_TOOL}..."
"${CONTAINER_TOOL}" build \
    -t "${IMAGE_NAME}:${IMAGE_TAG}" \
    -f "${DOCKERFILE_PATH}" \
    "${ROOT_DIR}"

echo "Imagen construida correctamente: ${IMAGE_NAME}:${IMAGE_TAG}"
