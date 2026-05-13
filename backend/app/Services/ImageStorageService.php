<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStorageService
{
    private const REPORT_IMAGE_DIRECTORY = 'reports';

    public function storeReportImage(UploadedFile $image): string
    {
        $extension = $image->guessExtension() ?: $image->extension() ?: 'jpg';
        $filename = Str::uuid()->toString().'.'.$extension;

        return $image->storeAs(self::REPORT_IMAGE_DIRECTORY, $filename, 'public');
    }

    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }
}
