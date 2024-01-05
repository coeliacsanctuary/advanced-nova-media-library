<?php

use Jpeters8889\AdvancedNovaMediaLibrary\Actions\GetUploadedMedia;
use Jpeters8889\AdvancedNovaMediaLibrary\Actions\UploadMedia;

return [
    'downloadable' => true,
    'default-croppable' => true,
    'enable-existing-media' => false,
    'hide-media-collections' => [],
    'upload-images-using' => UploadMedia::class,
    'get-uploaded-media-using' => GetUploadedMedia::class,
];
