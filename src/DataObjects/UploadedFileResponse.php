<?php

namespace Jpeters8889\AdvancedNovaMediaLibrary\DataObjects;

use Spatie\DataTransferObject\DataTransferObject;

class UploadedFileResponse extends DataTransferObject
{
    public string $key;
    public string $uuid;
    public string $filename;
    public string $mime_type;
    public string $file_size;
}
