<?php

namespace App\Services\Astar;



class Node {
    public $id;
    public $latitude;
    public $longitude;
    public $cost;
    public $f;
    public $g;
    public $h;
    public $parent;

    public function __construct($id, $lat, $lng, $cost = 0){
        $this-> id = $id;
        $this-> latitude = $lat;
        $this-> longitude = $lng;
        $this-> cost = $cost;
        $this-> g = 0;
        $this-> h = 0;
        $this-> f = 0;
        $this-> parent = null;
        
    }

}