<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStorageService
{
    private const REPORT_IMAGE_DIRECTORY = 'reports';
    private const CLAIM_IMAGE_DIRECTORY = 'claims';

    public function storeReportImage(UploadedFile $image): string
    {
        return $this->storeImage($image, self::REPORT_IMAGE_DIRECTORY);
    }

    public function storeClaimImage(UploadedFile $image): string
    {
        return $this->storeImage($image, self::CLAIM_IMAGE_DIRECTORY);
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function storeImage(UploadedFile $image, string $directory): string
    {
        $filename = Str::uuid()->toString().'.'.$image->getClientOriginalExtension();

        return $image->storeAs($directory, $filename, 'public');
    }
}
