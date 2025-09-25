<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    // Exibe a tabela de itens
    public function index(Request $request)
    {
        $query = Item::query();
        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name_pt', 'like', "%$search%")
                  ->orWhere('name_sp', 'like', "%$search%")
                  ->orWhere('name_en', 'like', "%$search%")
                  ->orWhere('external_id', 'like', "%$search%")
                  ->orWhere('index', 'like', "%$search%");
            });
        }
        $items = $query->get();
        return view('items.index', compact('items', 'search'));
    }

    // Retorna detalhes do item via AJAX
    public function details(Request $request)
    {
        $itemId = $request->input('item_id');
        $dataType = $request->input('data_type', 'diario');
        // Exemplo de dados detalhados, ajuste conforme necessário
        $table = $dataType !== 'semanal' ? 'items_day_prices' : 'items_weekly_prices';
        $items = Item::join($table . ' as idp', function ($join) {
            $join->on('items.id', '=', 'idp.item_id');
        })
            ->where('items.id', $itemId)
            ->where('idp.price', '>', 0)
            ->get();

        $itemsArr = $items->toArray();
      
        return response()->json($itemsArr);
    }
}
