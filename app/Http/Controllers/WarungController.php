<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataWarung; 

class WarungController extends Controller
{
    public function index()
    {
        $warung = DataWarung::all(); 
        return view('index', compact('warung')); 
    }
}
