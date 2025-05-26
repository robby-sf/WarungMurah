<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataWarung extends Model
{
    protected $fillable = ['name','latitude','longitude','rating','price','accessibility'];
}
