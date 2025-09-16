<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CraftController extends Controller
{
    public function index(Request $request, $dataType)
    {
        $citySale = $request->input('city_sale');
        $cityBuy = $request->input('city_buy');
        $orderBy = $request->input('order_by', 'lucro');
        $orderDir = $request->input('order_dir', 'desc');
        $porcentagem = $request->input('max_porcentagem', 100000);
        $minCount = $request->input('min_count', 100);
        $nameItem = $request->input('name_item', '');

        $tableMain = $dataType !== 'semanal' ? 'items_day_prices' : 'items_weekly_prices';
        $subItemValue = DB::query()->from("$tableMain as market_data")
            ->select(
                'market_data.item_id',
                DB::raw('MIN(NULLIF(market_data.price, 0)) as menor_valor'),
                DB::raw('MAX(market_data.price) as maior_valor'),
            )
            ->groupBy('market_data.item_id')
            ->havingRaw('MAX(market_data.price) > 0');

        if ($dataType !== 'semanal') {
            $subItemValue->join('items_weekly_prices as iwp', function ($join) {
                $join->on('market_data.city', '=', 'iwp.city')
                    ->on('market_data.quality', '=', 'iwp.quality')
                    ->on('market_data.item_id', '=', 'iwp.item_id');
            })->where('iwp.item_count', '>=', $minCount);
        } else {
            $subItemValue->where('market_data.item_count', '>=', $minCount);
        }

        $newSubItemValue = clone $subItemValue;
        $newSubItemRecipeValue = clone $subItemValue;
        $newSubItemRecipeValue->where('market_data.city', "!=", 'Black Market');

        if ($citySale) {
            $newSubItemValue->where('market_data.city', $citySale);
        }
        
        if ($cityBuy) {
            $newSubItemRecipeValue->where('market_data.city', $cityBuy);
        }

        $results = DB::query()->from("items")->select(
                'items.id',
                'items.external_id',
                'items.name_pt',
                'items.name_sp',
                DB::raw("
                    CASE 
                        WHEN items.external_id LIKE '%@%' THEN SUBSTRING_INDEX(items.external_id, '@', -1)
                        ELSE '0'
                    END as encantamento
                "),
                'value_item.maior_valor as valor',
                DB::raw('SUM(ingredientes.menor_valor * ir.amount) as custo'),
                DB::raw('(value_item.maior_valor - SUM(ingredientes.menor_valor * ir.amount)) as lucro'),
                DB::raw('((value_item.maior_valor - SUM(ingredientes.menor_valor * ir.amount))/ value_item.maior_valor * 100) as porcentagem'),
                'ir.recipe'
            )->join('item_recipes as ir', 'ir.item_id', '=', 'items.id')
            ->joinSub($newSubItemValue, 'value_item', function ($join) {
                $join->on('value_item.item_id', '=', 'items.id');
            })
            ->joinSub($newSubItemRecipeValue, 'ingredientes', function ($join) {
                $join->on('ingredientes.item_id', '=', 'ir.item_ingrediente_id');
            })
            ->groupBy('items.id', 'ir.recipe', 'value_item.maior_valor', 'items.name_pt')
            ->having('porcentagem', '<', $porcentagem)
            ->orderBy($orderBy, $orderDir);

        if ($nameItem) {
            $results->where('items.name_pt', 'like', "%$nameItem%")
                ->orWhere('items.name_sp', 'like', "%$nameItem%");
        }

        // dd( $results->toSql(), $results->getBindings() );
        $results = $results->get();
        return view('craft.index', compact('results'));
    }
}
