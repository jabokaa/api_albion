<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'external_id',
        'index',
        'name_en',
        'name_sp',
        'name_pt',
    ];
}
