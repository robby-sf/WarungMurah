<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarungController;


Route::get('/', [WarungController::class, 'index']);
