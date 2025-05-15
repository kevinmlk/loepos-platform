<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\DocumentController;
use App\Http\Middleware\VerifyApiToken;

/**
* TODO: Add an universal token to incoming request
*/

Route::middleware(VerifyApiToken::class)->group(function () {
    Route::post('/document', [DocumentController::class, 'store']);
});
