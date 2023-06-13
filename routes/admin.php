<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\HomeController;

Route::get('', [HomeController::class, 'index']);