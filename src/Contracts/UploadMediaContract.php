<?php

namespace Jpeters8889\AdvancedNovaMediaLibrary\Contracts;

use Illuminate\Http\UploadedFile;
use Jpeters8889\AdvancedNovaMediaLibrary\DataObjects\UploadedFileResponse;

interface UploadMediaContract
{
    public function upload(UploadedFile $file): UploadedFileResponse;
}
