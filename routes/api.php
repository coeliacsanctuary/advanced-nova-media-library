<?php

use Illuminate\Support\Facades\Route;
use Jpeters8889\AdvancedNovaMediaLibrary\Http\Controllers\UploadController;

Route::post('/upload', UploadController::class);
