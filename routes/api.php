<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StrukController;

Route::apiResource('struks', StrukController::class);
