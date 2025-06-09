<?php

namespace App\Services\Astar;


class Astar
{
    public static function findPath($nodes, $edges, $startId, $goalId)
    {
        $openSet = [$startId];
        $cameFrom = [];

        $gScore = array_fill_keys(array_column($nodes, 'id'), INF);
        $gScore[$startId] = 0;

        $fScore = array_fill_keys(array_column($nodes, 'id'), INF);
        $fScore[$startId] = self::heuristic($nodes[$startId], $nodes[$goalId]);

        while (!empty($openSet)) {
            usort($openSet, fn($a, $b) => $fScore[$a] <=> $fScore[$b]);
            $current = array_shift($openSet);

            if ($current === $goalId) {
                return self::reconstructPath($cameFrom, $current);
            }

            foreach (self::neighbors($edges, $current) as $neighbor => $cost) {
                $tentativeGScore = $gScore[$current] + $cost;
                if ($tentativeGScore < $gScore[$neighbor]) {
                    $cameFrom[$neighbor] = $current;
                    $gScore[$neighbor] = $tentativeGScore;
                    $fScore[$neighbor] = $tentativeGScore + self::heuristic($nodes[$neighbor], $nodes[$goalId]);

                    if (!in_array($neighbor, $openSet)) {
                        $openSet[] = $neighbor;
                    }
                }
            }
        }

        return null;
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

    private static function heuristic($a, $b)
    {
        return SkorWarung::Haversine($a['lat'], $a['lng'], $b['lat'], $b['lng']);
    }

    private static function neighbors($edges, $nodeId)
    {
        $result = [];
        foreach ($edges as $edge) {
            if ($edge['from'] === $nodeId) {
                $result[$edge['to']] = $edge['cost'];
            }
        }
        return $result;
    }
}
