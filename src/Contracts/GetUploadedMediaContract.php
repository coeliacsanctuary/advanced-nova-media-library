<?php

namespace Jpeters8889\AdvancedNovaMediaLibrary\Contracts;

interface GetUploadedMediaContract
{
    public function resolveFromUuid(string $uuid): string;
}
