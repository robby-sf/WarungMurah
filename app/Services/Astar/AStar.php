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
                    $current = $openSet->extract();
                    unset($openSetHash[$current]);
                    Log::info("📍 Current node dievaluasi: $current");

                    if ($current === $finish) {
                        Log::info("✅ Tujuan ($finish) ditemukan! Memulai rekonstruksi path...");
                        return self::reconstructPath($cameFrom, $current);
                    }

                    if (!isset($adjacencyList[$current])) {
                        Log::warning("⚠️ Node $current tidak memiliki tetangga!");
                        continue;}

                    foreach ($adjacencyList[$current] as $edge) {
                        $neighbor = $edge['to'];
                        $cost = $edge['cost'];

                        $tentative_g_score = $g_score[$current] + $cost;
                        Log::info("🔄 Mengecek neighbor $neighbor dari $current");
                        Log::info("Cost dari $current ke $neighbor: $cost");
                        Log::info("g_score[$current]: {$g_score[$current]} → tentative_g_score[$neighbor]: $tentative_g_score");


                        if ($tentative_g_score < $g_score[$neighbor]) {
                            $cameFrom[$neighbor] = $current;
                            $g_score[$neighbor] = $tentative_g_score;
                            
                            $heuristic = self::Haversine(
                                $nodes[$neighbor]['lat'], $nodes[$neighbor]['lng'],
                                $nodes[$finish]['lat'], $nodes[$finish]['lng']
                            );

                            $f_score[$neighbor] = $g_score[$neighbor] + $heuristic;

                            Log::info("💡 Update path ke $neighbor:");
                            Log::info("→ g_score: {$g_score[$neighbor]} | h(n): $heuristic | f(n): {$f_score[$neighbor]}");


                            if (!isset($openSetHash[$neighbor])) {
                                $openSet->insert($neighbor, -$f_score[$neighbor]);
                                $openSetHash[$neighbor] = true;
                            }
                        }
                    }
                }

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
