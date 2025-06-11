<?php

namespace App\Services\Astar;
use SplPriorityQueue;

use Illuminate\Support\Facades\Log;
class Astar
{
    public static function findPath($graph, $start, $finish)
    {
        Log::info("Fungsi Astar::findPath() dipanggil dengan 3 argumen.");
        $nodes = $graph['nodes'];
        $adjacencyList = $graph['edges'];

        $openSet = new SplPriorityQueue();
        $openSet->setExtractFlags(SplPriorityQueue::EXTR_DATA);

        //rute terpekdek yg ketemu
        $cameFrom = [];        

        //g(n) cost awal ke note terkini
        $g_score = [];
        foreach (array_keys($nodes) as $node) {
            $g_score[$node] = INF;
        }
        $g_score[$start] = 0;

        //f(n) gn + heuristic
        $f_score = [];
        foreach (array_keys($nodes) as $node) {
            $f_score[$node] = INF;
        }

        $f_score[$start] = SkorWarung::Haversine(
            $nodes[$start]['lat'], $nodes[$start]['lng'],
            $nodes[$finish]['lat'], $nodes[$finish]['lng']
        );

        $openSet->insert($start, -$f_score[$start]);

        $openSetHash = [$start => true];
        while (!$openSet->isEmpty()) {
                    // Ambil node dari antrian dengan f_score terkecil
                    $current = $openSet->extract();
                    unset($openSetHash[$current]);

                    // Jika sudah sampai tujuan, rekonstruksi rute dan kembalikan hasilnya.
                    if ($current === $finish) {
                        return self::reconstructPath($cameFrom, $current);
                    }

                    // Iterasi semua tetangga dari node saat ini
                    if (!isset($adjacencyList[$current])) continue;

                    foreach ($adjacencyList[$current] as $edge) {
                        $neighbor = $edge['to'];
                        
                        // tentative_g_score adalah jarak dari start ke tetangga melalui node saat ini.
                        $tentative_g_score = $g_score[$current] + $edge['cost'];

                        // Jika rute melalui 'current' lebih baik daripada rute sebelumnya ke 'neighbor'
                        if ($tentative_g_score < $g_score[$neighbor]) {
                            // Catat rute baru yang lebih baik ini
                            $cameFrom[$neighbor] = $current;
                            $g_score[$neighbor] = $tentative_g_score;
                            
                            // Hitung f_score baru untuk neighbor
                            $f_score[$neighbor] = $g_score[$neighbor] + self::Haversine(
                                $nodes[$neighbor]['lat'], $nodes[$neighbor]['lng'],
                                $nodes[$finish]['lat'], $nodes[$finish]['lng']
                            );

                            // Jika neighbor belum ada di openSet, tambahkan.
                            if (!isset($openSetHash[$neighbor])) {
                                $openSet->insert($neighbor, -$f_score[$neighbor]);
                                $openSetHash[$neighbor] = true;
                            }
                        }
                    }
                }

                
        // Jika loop selesai tapi goal tidak tercapai, berarti tidak ada rute.
        return null;
    }

    public static function Haversine($latitude1,$longitude1,$latitude2,$longitude2){
            $earthRadius = 6371;

            $dlat = deg2rad($latitude1-$latitude2);
            $dlng = deg2rad($longitude1-$longitude2);

            $a = sin($dlat/2)*sin($dlat/2)+ cos(deg2rad($latitude1)) * cos(deg2rad($latitude2))*
            sin($dlng/2)*sin($dlng/2);

            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            return $earthRadius*$c;
        }

    private static function reconstructPath($cameFrom, $current)
    {
        $totalPath = [$current];
        while (isset($cameFrom[$current])) {
            $current = $cameFrom[$current];
            array_unshift($totalPath, $current);
        }
        return $totalPath;
    }
}
