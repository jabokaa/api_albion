<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemsWeeklyPrice extends Model
{
    protected $fillable = [
        'item_id',
        'query_date',
        'city',
        'item_count',
        'price',
        'quality',
    ];
}
