<?php

namespace Jpeters8889\AdvancedNovaMediaLibrary\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Jpeters8889\AdvancedNovaMediaLibrary\Contracts\UploadMediaContract;
use Jpeters8889\AdvancedNovaMediaLibrary\DataObjects\UploadedFileResponse;

class UploadMedia implements UploadMediaContract
{
    public function upload(UploadedFile $file): UploadedFileResponse
    {
        return new UploadedFileResponse(
            key: $file->hashName(),
            uuid: Str::uuid()->toString(),
            filename: $file->getClientOriginalName(),
            mime_type: $file->getMimeType(),
            file_size: $file->getSize(),
        );
    }
}
