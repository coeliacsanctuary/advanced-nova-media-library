<?php

namespace Jpeters8889\AdvancedNovaMediaLibrary\Actions;

use Jpeters8889\AdvancedNovaMediaLibrary\Contracts\GetUploadedMediaContract;

class GetUploadedMedia implements GetUploadedMediaContract
{
    public function resolveFromUuid(string $uuid): string
    {
        return 'url';
    }
}
