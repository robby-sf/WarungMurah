<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataWarung; // opsional, dulu

class WarungController extends Controller
{
    public function index()
    {
        return view('index'); 
    }

    public function lokasi(Request $request)
    {
        $lat = $request->latitude;
        $lng = $request->longitude;

        // Cek data
        return response()->json([
            'status' => 'success',
            'latitude' => $lat,
            'longitude' => $lng,
            //buat warung ntar
        ]);
    }
}
