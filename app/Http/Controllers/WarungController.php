<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataWarung; // opsional, dulu
use App\Models\Warung; 

class WarungController extends Controller
{
    public function index()
    {
        $warung = DataWarung::all();
        return view('index',compact('warung')); 
    }

    public function lokasi(Request $request)
    {
        $lat = $request->latitude;
        $lng = $request->longitude;

        $warung = DataWarung::all();
        return response()->json([
            'status' => 'success',
            'user_lat' => $lat,
            'user_lng' => $lng,
            // 'warung' => $warung
        ]);
    }
}
