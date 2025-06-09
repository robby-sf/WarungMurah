<?php
namespace App\Services\Astar;

class Graph
{
    public static function buildFullyConnectedGraph($userNode, $warungs)
    {
        $nodes = [];
        $edges = [];

        // Tambahkan node user
        $nodes[$userNode['id']] = $userNode;

        // Tambahkan semua warung sebagai node
        foreach ($warungs as $warung) {
            $nodes[$warung->id] = [
                'id' => $warung->id,
                'lat' => $warung->latitude,
                'lng' => $warung->longitude
            ];
        }

        $allNodes = array_values($nodes);

        foreach ($allNodes as $w1) {
            foreach ($allNodes as $w2) {
                if ($w1['id'] !== $w2['id']) {
                    $jarak = SkorWarung::Haversine($w1['lat'], $w1['lng'], $w2['lat'], $w2['lng']);
                    $edges[] = [
                        'from' => $w1['id'],
                        'to' => $w2['id'],
                        'cost' => $jarak
                    ];
                }
            }
        }

        return [
            'nodes' => $nodes,
            'edges' => $edges
        ];
    }
}


?>