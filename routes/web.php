<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarungController;


Route::get('/', [WarungController::class, 'index']);
Route::post('/lokasi', [WarungController::class, 'lokasi']);
Route::get('/cari', [WarungController::class, 'cari']);

