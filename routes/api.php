<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\DocumentController;

Route::post('/document', [DocumentController::class, 'store']);
