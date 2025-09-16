<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRecipe extends Model
{
    protected $table = 'item_recipes';

    protected $fillable = [
        'item_id',
        'item_ingrediente_id',
        'amount',
        'recipe',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function itemIngrediente()
    {
        return $this->belongsTo(Item::class, 'item_ingrediente_id');
    }
}
