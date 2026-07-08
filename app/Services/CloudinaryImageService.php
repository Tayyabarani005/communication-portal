<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CloudinaryImageService
{
    public function isConfigured(): bool
    {
        return filled(config('services.cloudinary.cloud_name'))
            && filled(config('services.cloudinary.api_key'))
            && filled(config('services.cloudinary.api_secret'));
    }

    public function upload(UploadedFile $file, string $folder): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Cloudinary is not configured.');
        }

        $baseFolder = trim((string) config('services.cloudinary.folder', 'communication-portal'), '/');
        $targetFolder = trim($baseFolder . '/' . trim($folder, '/'), '/');
        $timestamp = time();

        $params = [
            'folder' => $targetFolder,
            'timestamp' => $timestamp,
        ];

        $params['signature'] = $this->signature($params);
        $params['api_key'] = config('services.cloudinary.api_key');

        $stream = fopen($file->getRealPath(), 'r');

        try {
            $response = Http::attach('file', $stream, $file->getClientOriginalName())
                ->post($this->uploadUrl(), $params)
                ->throw()
                ->json();
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        $url = $response['secure_url'] ?? $response['url'] ?? null;

        if (!$url) {
            throw new RuntimeException('Cloudinary did not return an image URL.');
        }

        return $url;
    }

    /**
     * Cloudinary signs all upload params except file, api_key, resource_type, and cloud_name.
     */
    private function signature(array $params): string
    {
        ksort($params);

        $payload = collect($params)
            ->map(fn ($value, $key) => $key . '=' . $value)
            ->implode('&');

        return sha1($payload . config('services.cloudinary.api_secret'));
    }

    private function uploadUrl(): string
    {
        $cloudName = config('services.cloudinary.cloud_name');

        return "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
    }
}
