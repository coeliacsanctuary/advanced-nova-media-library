<?php

namespace Jpeters8889\AdvancedNovaMediaLibrary\Http\Controllers;

use Jpeters8889\AdvancedNovaMediaLibrary\Contracts\UploadMediaContract;
use Jpeters8889\AdvancedNovaMediaLibrary\Http\Requests\UploadFileRequest;

class UploadController extends Controller
{
    public function __invoke(UploadFileRequest $request)
    {
        /** @var class-string<UploadMediaContract> $action */
        $action = config('nova-media-library.upload-images-using');

        return (new $action())->upload($request->file('image'))->toArray();
    }
}
