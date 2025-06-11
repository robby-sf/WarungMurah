<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataWarung; // opsional, dulu
use App\Models\Warung; 
use App\Services\Astar\SkorWarung;
use App\Services\Astar\Astar;
use App\Services\Astar\Graph;

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
        Log::info("ini Cari bisa");
        try {
            $userLat = $request->input('lat');
            $userLng = $request->input('lng');
            $warungs = DataWarung::all();

            $best = SkorWarung::cari($userLat, $userLng, $warungs,3);

            if (empty($best)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada warung yang cocok.',
                    'warung_terbaik' => null
                ]);
            }
            Log::info('BEST WARUNG', ['id' => $best->id ?? 'NULL']);

            return response()->json([
                'status' => 'success',
                'user_lat' => $userLat,
                'user_lng' => $userLng,
                'rekomendasi' => array_map(function($item){
                    return [
                    'id' => $item['warung']->id,
                    'name' => $item['warung']->name,
                    'price' => $item['warung']->price,
                    'rating' => $item['warung']->rating,
                    'accessibility' => $item['warung']->accessibility,
                    'latitude' => $item['warung']->latitude,
                    'longitude' => $item['warung']->longitude,
                    'score' => $item['score']
                ];
                }, $best)
            ]);

        } catch (\Throwable $e) {
            Log::error("GAGAL CARI: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan di server.',
            ], 500);
        }
    }


// public function rute(Request $request)
// {
//     Log::info("ini Rute bisa");
//     try {
//         $userLat = $request->input('lat');
//         $userLng = $request->input('lng');
//         $goalId = $request->input('goal_id'); // ID warung yang dipilih
        
//         if (!$userLat || !$userLng || !$goalId) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Data lokasi atau tujuan tidak lengkap.'
//             ], 400);
//         }
//         Log::info("ini Rute bisa di try");
        
//         Log::info('RUTE STARTED', [
//             'user_lat' => $userLat,
//             'user_lng' => $userLng,
//             'goal' => $goalId
//         ]);

//         $warungs = DataWarung::all();
//         $userNode = ['id' => 'user', 'lat' => $userLat, 'lng' => $userLng];

//         $graph = Graph::buildFullyConnectedGraph($userNode, $warungs);
//         Log::info('NODES', array_keys($graph['nodes']));
//         Log::info('EDGES COUNT', [count($graph['edges'])]);
//         Log::info("GOAL ID dump:", ['goal_id' => $goalId]);



//         if (!isset($graph['nodes']['user']) || !isset($graph['nodes'][$goalId])) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Node tidak ditemukan dalam graph.'
//             ], 500);
//         }

//         $path = Astar::findPath($graph['nodes'], $graph['edges'], 'user', $goalId);

//         if (!$path) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Rute tidak ditemukan.'
//             ], 500);
//         }

//         return response()->json([
//             'status' => 'success',
//             'path' => $path
//         ]);
//     } catch (\Throwable $e) {
//         Log::error("ERROR di rute(): " . $e->getMessage(), [
//             'line' => $e->getLine(),
//             'file' => $e->getFile(),
//             'trace' => $e->getTraceAsString()
//         ]);
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Terjadi kesalahan saat memproses rute'
//         ], 500);
//     }
// }


    public function sherinDemo()
        {
            // Panggil metode statis buildgraph dari class sherinGraph
            $graphData = Graph::buildgraph();

            // Kembalikan data dalam format JSON, sesuai yang diharapkan oleh JavaScript
            return response()->json([
                'status' => 'success',
                'graph' => $graphData
            ]);
        }

    public function getAstarRouteDemo()
{
    Log::info("Masuk ke getAstarRouteDemo");

    try {
        $graph = Graph::buildgraph();

        
        $adjacencyList = [];
        Log::info("Contoh edge pertama: " . json_encode($graph['edges'][0] ?? 'Kosong'));

                foreach ($graph['edges'] as $idx => $edge) {
            if (!is_array($edge)) {
                Log::warning("Edge bukan array di index $idx:", ['value' => $edge]);
                continue;
            }

            if (!isset($edge['from'], $edge['to'], $edge['cost'])) {
                Log::warning("Edge tidak lengkap di index $idx:", $edge);
                continue;
            }

            $adjacencyList[$edge['from']][] = ['to' => $edge['to'], 'cost' => $edge['cost']];
            $adjacencyList[$edge['to']][] = ['to' => $edge['from'], 'cost' => $edge['cost']];
        }
        
        Log::info("Adjacency List berhasil dibuat:", $adjacencyList);

        Log::info("Total edges: " . count($graph['edges']));
        Log::info("Contoh edge pertama:", [$graph['edges'][0] ?? 'Kosong']);



        $startNode = 'user';
        $endNode = 'warung';

        Log::info("Menjalankan A* dari $startNode ke $endNode");
        Log::info("Nodes tersedia:", array_keys($graph['nodes']));
        Log::info("Jumlah edge:", [count($graph['edges'])]);
        
        $path = Astar::findPath(
            ['nodes' => $graph['nodes'], 'edges' => $adjacencyList],
            $startNode,
            $endNode
        );
        Log::info("Hasil Path:", $path);


        
        if (!$path) {
            return response()->json([
                'status' => 'error',
                'message' => "Rute tidak ditemukan dari $startNode ke $endNode."
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'path' => $path
        ]);

    } catch (\Throwable $e) {
        Log::error("ERROR di getAstarRouteDemo(): " . $e->getMessage() . " di baris " . $e->getLine());
        return response()->json([
            'status' => 'error',
            'message' => 'Kesalahan Server saat mencari rute A*.'
        ], 500);
    }
}

}
