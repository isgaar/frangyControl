#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
IMAGE_NAME="${IMAGE_NAME:-frangy-control}"
IMAGE_TAG="${IMAGE_TAG:-latest}"
DOCKERFILE_PATH="${DOCKERFILE_PATH:-${ROOT_DIR}/Dockerfile}"

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

CONTAINER_TOOL="$(detect_container_tool)"

echo "Construyendo la imagen ${IMAGE_NAME}:${IMAGE_TAG} usando ${CONTAINER_TOOL}..."
"${CONTAINER_TOOL}" build \
    -t "${IMAGE_NAME}:${IMAGE_TAG}" \
    -f "${DOCKERFILE_PATH}" \
    "${ROOT_DIR}"

echo "Imagen construida correctamente: ${IMAGE_NAME}:${IMAGE_TAG}"
