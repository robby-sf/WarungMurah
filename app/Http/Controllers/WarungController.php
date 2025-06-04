<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataWarung; // opsional, dulu
use App\Models\Warung; 
use App\Services\Astar\SkorWarung;

use Illuminate\Support\Facades\Log;

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
            'warung' => $warung
        ]);
    }

    public function cari(Request $request)
    {
        try {
            $userLat = $request->input('lat');
            $userLng = $request->input('lng');
            $warungs = DataWarung::all();

            $best = SkorWarung::cari($userLat, $userLng, $warungs);

            if (!$best) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada warung yang cocok.',
                    'warung_terbaik' => null
                ]);
            }

            return response()->json([
                'status' => 'success',
                'user_lat' => $userLat,
                'user_lng' => $userLng,
                'warung_terbaik' => [
                    'name' => $best->name,
                    'price' => $best->price,
                    'rating' => $best->rating,
                    'accessibility' => $best->accessibility,
                    'latitude' => $best->latitude,
                    'longitude' => $best->longitude,
                ]
            ]);

        } catch (\Throwable $e) {
            Log::error("GAGAL CARI: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan di server.',
            ], 500);
        }
    }


    
}
