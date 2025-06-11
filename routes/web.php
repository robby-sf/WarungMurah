<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarungController;


Route::get('/', [WarungController::class, 'index']);
Route::post('/lokasi', [WarungController::class, 'lokasi']);
Route::get('/cari', [WarungController::class, 'cari']);
Route::get('/rute', [WarungController::class, 'rute']);
Route::post('/rute', [WarungController::class, 'rute']);
Route::get('/sherin', [WarungController::class, 'sherinDemo']);
Route::get('/lia', [WarungController::class, 'liaDemo']);
Route::get('/get-astar-route', [WarungController::class, 'getAstarRoute']);
Route::get('/get-astar-route2', [WarungController::class, 'getAstarRoute2']);
