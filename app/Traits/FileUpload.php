<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait FileUpload
{
    // public function fileUpload(UploadedFile $file, string $path, $disk = 'public', ?string $name = null): array
    public function fileUpload(UploadedFile $file, string $path, ?string $disk = 'public', ?string $name = null): array
    {
        // dd($disk);
        $name = $name ? str()->snake($name) . '.' . $file->getClientOriginalExtension() : $file->hashName();
        $file->storeAs($path, $name, $disk);

        return [
            'name' => $name,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'path' => $path,
        ];
    }

    // public function deleteFile($path, $filename): bool
    public function deleteFile($path, $filename, ?string $disk = 'public'): bool
    {
        if ($disk == 's3') {
            return Storage::disk($disk)->delete($path . '/' . $filename);
        }

        return Storage::delete('public/' . $path . '/' . $filename);
    }
}
