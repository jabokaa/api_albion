<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemsDayPrice extends Model
{
    protected $table = 'items_day_prices';
    protected $fillable = [
        'item_id',
        'query_date',
        'city',
        'item_count',
        'price',
        'quality',
    ];
}
