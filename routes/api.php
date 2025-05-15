<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\DocumentController;
use App\Http\Middleware\VerifyApiToken;

Route::middleware(VerifyApiToken::class)->group(function () {
    Route::post('/document', [DocumentController::class, 'store']);
});
