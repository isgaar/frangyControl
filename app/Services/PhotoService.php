<?php

namespace App\Services;

use App\Models\Ordenes;
use App\Models\Fotografia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoService
{
    public const TEMP_PHOTO_SESSION_KEY = 'ordenes_temp_photos';

    public function storeTemporaryPhotos(array $tokens, array &$tempPhotos, Ordenes $orden): void
    {
        $tokens = collect($tokens)->filter()->unique()->values();

        if ($tokens->isEmpty()) return;

        foreach ($tokens as $token) {
            $photo = $tempPhotos[$token] ?? null;

            if (!$photo || empty($photo['path']) || !Storage::disk('local')->exists($photo['path'])) {
                throw new \RuntimeException('No se pudo recuperar una fotografía temporal. Vuelve a seleccionarla.');
            }

            $extension = $photo['extension'] ?? 'jpg';
            $finalPath = $this->finalPhotoPath($orden, $token, $extension);

            Storage::disk('local')->makeDirectory(dirname($finalPath));
            Storage::disk('local')->move($photo['path'], $finalPath);

            Fotografia::create([
                'ruta'       => $finalPath,
                'ordenes_id' => $orden->id_ordenes,
            ]);

            unset($tempPhotos[$token]);
        }
    }

    public function processAndStoreUploadedPhotos(array $photos, Ordenes $orden): void
    {
        foreach ($photos as $photo) {
            if (!$photo instanceof UploadedFile) continue;

            $token     = (string) Str::uuid();
            $extension = $this->safePhotoExtension($photo);
            $path      = $this->finalPhotoPath($orden, $token, $extension);

            $this->putEncryptedPhoto($photo, $path);

            Fotografia::create([
                'ruta'       => $path,
                'ordenes_id' => $orden->id_ordenes,
            ]);
        }
    }

    public function putEncryptedPhoto(UploadedFile $photo, string $path): void
    {
        $contents = file_get_contents($photo->getRealPath());

        if ($contents === false) {
            throw new \RuntimeException('No se pudo leer una fotografía seleccionada.');
        }

        $contents = $this->compressPhotoIfSupported($contents, $photo->getMimeType());

        Storage::disk('local')->makeDirectory(dirname($path));
        Storage::disk('local')->put($path, Crypt::encryptString(base64_encode($contents)));
    }

    public function deleteStoredPhoto(Fotografia $fotografia): void
    {
        if (Str::endsWith($fotografia->ruta, '.enc')) {
            Storage::disk('local')->delete($fotografia->ruta);
            return;
        }

        $legacyPath = public_path($fotografia->ruta);
        if (is_file($legacyPath)) {
            @unlink($legacyPath);
        }
    }

    public function finalPhotoPath(Ordenes $orden, string $token, string $extension): string
    {
        return 'ordenes/fotografias/' . $orden->id_ordenes . '/' . $token . '.' . $extension . '.enc';
    }

    public function safePhotoExtension(UploadedFile $photo): string
    {
        $extension = strtolower($photo->guessExtension() ?: $photo->getClientOriginalExtension() ?: 'jpg');
        return in_array($extension, ['jpg', 'jpeg', 'png'], true) ? $extension : 'jpg';
    }

    public function mimeTypeFromEncryptedPath(string $path): string
    {
        return Str::contains($path, '.png.enc') ? 'image/png' : 'image/jpeg';
    }

    private function compressPhotoIfSupported(string $contents, ?string $mimeType): string
    {
        if (!$this->gdImageFunctionsAreAvailable()) {
            return $contents;
        }

        $imageResource = @imagecreatefromstring($contents);
        if ($imageResource === false) {
            return $contents;
        }

        $width = imagesx($imageResource);
        $height = imagesy($imageResource);
        $maxWidth = 1920;
        $maxHeight = 1080;

        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = max(1, (int) ($width * $ratio));
            $newHeight = max(1, (int) ($height * $ratio));

            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            if ($mimeType === 'image/png') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($newImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($imageResource);
            $imageResource = $newImage;
        }

        ob_start();
        if ($mimeType === 'image/png') {
            imagepng($imageResource, null, 9);
        } else {
            imagejpeg($imageResource, null, 85);
        }
        $compressedContents = ob_get_clean();
        imagedestroy($imageResource);

        return $compressedContents !== false ? $compressedContents : $contents;
    }

    private function gdImageFunctionsAreAvailable(): bool
    {
        return function_exists('imagecreatefromstring')
            && function_exists('imagesx')
            && function_exists('imagesy')
            && function_exists('imagecreatetruecolor')
            && function_exists('imagecopyresampled')
            && function_exists('imagedestroy')
            && function_exists('imagepng')
            && function_exists('imagejpeg');
    }
}
