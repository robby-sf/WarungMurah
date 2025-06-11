<?php

namespace App\Services\Astar;
use Illuminate\Support\Facades\Log;



class SkorWarung {

    public static function TotalScore($warung, $jarak){
        $k= -0.4;

        $hargaScore = 1 - (($warung->price - 1)/2);
        $ratingScore = $warung->rating / 5;
        $aksesScore = $warung->accessibility / 10;
        $jarakScore = exp($k*$jarak);

        
        
        $score = ($hargaScore*0.3) + ($jarakScore*0.3) + ($aksesScore*0.2) + ($ratingScore*0.2);
        // Log::info("ini Scoring");
        Log::info("Scoring Warung: {$warung->name}, Score: {$score}");

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

    public static function cari($userlatitude,$userlongitude,$warungs,$jumlah =3){
        $scored =[];
        $maxJarak = 50; 

        

        foreach($warungs as $warung){
            $jarak = self::Haversine($userlatitude, $userlongitude, $warung->latitude, $warung->longitude);
            
            // Log::info("Jarak ke {$warung->name}: {$jarak} km");

            if($jarak>$maxJarak){
                continue;
            }

            $score = self::TotalScore($warung, $jarak);
            $warung->score = $score; 
            $scored[] = [
                'warung' => $warung,
                'score' => $score
            ];
            
        }

        if (empty($scored)){
            return [];
        }

        usort($scored, function($a, $b){
            return $b['score'] <=> $a['score'];
        });

        $bestWarung = $scored[0] ?? null;
        // Log::info("Scored warung versi bawah", [
        //     'name' => $warung->name,
        //     'jarak' => $jarak,
        //     'score' => $score
        // ]);
       if (!empty($scored)) {
        $bestWarung = $scored[0];
        Log::info("Mengembalikan warung terbaik:", [
            'name' => $bestWarung['warung']->name,
            'skor' => $bestWarung['score']
        ]);
        
}

        return array_slice($scored,0,$jumlah);

    }


}   