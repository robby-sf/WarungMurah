<?php

namespace App\Services\Astar;
use Illuminate\Support\Facades\Log;


class SkorWarung {

    public static function TotalScore($warung, $userlatitude, $userlongitude,$maxJarak){
        $jarak = self::Haversine($userlatitude, $userlongitude, $warung->latitude, $warung->longitude);

        $hargaScore = 1 - (($warung->price - 1)/2);
        $ratingScore = $warung->rating / 5;
        $aksesScore = $warung->accessibility / 10;
        $jarakScore = 1 - min(1, $jarak / $maxJarak);

        
        
        $score = ($hargaScore*0.3) + ($jarakScore*0.3) + ($aksesScore*0.2) + ($ratingScore*0.2);
        Log::info("ini Scoring");
        // Log::info("Scoring Warung: {$warung->name}, Score: {$score}, User: {$userlatitude},{$userlongitude}");

        return $score;
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

    public static function cari($userlatitude,$userlongitude,$warungs){
        $bestScore = -INF;
        $bestWarung = null;
        $maxJarak = 10; //km

        foreach($warungs as $warung){
            $score = self::TotalScore($warung, $userlatitude, $userlongitude,$maxJarak);
            if($score > $bestScore){
                $bestScore = $score;
                $bestWarung = $warung;
            }
        }

        Log::info("Mengembalikan warung terbaik:", ['name' => optional($bestWarung)->name]);

        return $bestWarung;

    }


}   