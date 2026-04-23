#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
IMAGE_NAME="${IMAGE_NAME:-frangy-control}"
IMAGE_TAG="${IMAGE_TAG:-latest}"
POD_NAME="${POD_NAME:-frangy-control-pod}"
CONTAINER_NAME="${CONTAINER_NAME:-frangy-control-app}"
HOST_PORT="${HOST_PORT:-9000}"
CONTAINER_PORT="${CONTAINER_PORT:-9000}"

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

CONTAINER_TOOL="$(detect_container_tool)"

if ! image_exists "${CONTAINER_TOOL}"; then
    echo "La imagen ${IMAGE_NAME}:${IMAGE_TAG} no existe." >&2
    echo "Ejecuta primero unix-scrips/construir-pod.sh" >&2
    exit 1
fi

if [[ "${CONTAINER_TOOL}" == "podman" ]]; then
    if ! podman pod inspect "${POD_NAME}" >/dev/null 2>&1; then
        echo "Creando pod ${POD_NAME}..."
        podman pod create --name "${POD_NAME}" -p "${HOST_PORT}:${CONTAINER_PORT}" >/dev/null
    else
        echo "El pod ${POD_NAME} ya existe. Se reutilizara."
    fi

    if podman container inspect "${CONTAINER_NAME}" >/dev/null 2>&1; then
        echo "Eliminando el contenedor previo ${CONTAINER_NAME}..."
        podman rm -f "${CONTAINER_NAME}" >/dev/null
    fi

    echo "Lanzando el contenedor ${CONTAINER_NAME} dentro del pod ${POD_NAME}..."
    podman run -d \
        --name "${CONTAINER_NAME}" \
        --pod "${POD_NAME}" \
        "${IMAGE_NAME}:${IMAGE_TAG}" >/dev/null

    echo "Pod lanzado correctamente."
    echo "PHP-FPM quedo expuesto en localhost:${HOST_PORT}"
    exit 0
fi

echo "Docker no maneja pods de forma nativa; se lanzara un contenedor equivalente."

if docker container inspect "${CONTAINER_NAME}" >/dev/null 2>&1; then
    echo "Eliminando el contenedor previo ${CONTAINER_NAME}..."
    docker rm -f "${CONTAINER_NAME}" >/dev/null
fi

docker run -d \
    --name "${CONTAINER_NAME}" \
    -p "${HOST_PORT}:${CONTAINER_PORT}" \
    "${IMAGE_NAME}:${IMAGE_TAG}" >/dev/null

echo "Contenedor lanzado correctamente."
echo "PHP-FPM quedo expuesto en localhost:${HOST_PORT}"
