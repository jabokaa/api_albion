<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRecipe;
use App\Models\ItemsDayPrice;
use App\Models\ItemsWeeklyPrice;
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
        $porcentagem = $request->input('max_porcentagem', 200);
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
                        WHEN COUNT(ingredientes.item_id) < COUNT(ir.item_ingrediente_id) THEN 'ingrediente em falta'
                        ELSE 'receita completa'
                    END as status
                "),
                DB::raw("
                    CASE 
                        WHEN items.external_id LIKE '%@%' THEN SUBSTRING_INDEX(items.external_id, '@', -1)
                        ELSE '0'
                    END as encantamento
                "),

                'value_item.maior_valor as valor',
                DB::raw('SUM(ingredientes.menor_valor * ir.amount) as custo'),
                DB::raw('(value_item.maior_valor - SUM(ingredientes.menor_valor * ir.amount)) as lucro'),
                DB::raw('(
                    (value_item.maior_valor - SUM(ingredientes.menor_valor * ir.amount)) / SUM(ingredientes.menor_valor * ir.amount) * 100
                ) as porcentagem'),
                'ir.recipe'
            )->join('item_recipes as ir', 'ir.item_id', '=', 'items.id')
            ->joinSub($newSubItemValue, 'value_item', function ($join) {
                $join->on('value_item.item_id', '=', 'items.id');
            })
            ->leftJoinSub($newSubItemRecipeValue, 'ingredientes', function ($join) {
                $join->on('ingredientes.item_id', '=', 'ir.item_ingrediente_id');
            })
            ->groupBy('items.id', 'ir.recipe', 'value_item.maior_valor', 'items.name_pt')
            ->having('porcentagem', '<', $porcentagem)
            ->having('status', 'receita completa')
            ->orderBy($orderBy, $orderDir);

        if ($nameItem) {
            $results->where('items.name_pt', 'like', "%$nameItem%")
                ->orWhere('items.name_sp', 'like', "%$nameItem%");
        }

        // dd( $results->toSql(), $results->getBindings() );
        $results = $results->get();
        return view('craft.index', compact('results'));
    }

    public function details(Request $request, $dataType)
    {
        $receita = $request->input('receita');
        $item_id = $request->input('item_id');
        $cidadeDoItem = $request->input('cidade_do_item', '');
        $cidadeDoIngrediente = $request->input('cidade_do_ingrediente', '');

        $item = Item::find($item_id)->toArray();

        $item['encantamento'] = str_contains($item['external_id'], '@') ? explode('@', $item['external_id'])[1] : '0';
        // Escolhe o modelo conforme $dataType
        $priceModel = $dataType === 'semanal' ? ItemsWeeklyPrice::class : ItemsDayPrice::class;

        $query = $priceModel::from((new $priceModel)->getTable() . ' as main')
            ->where('main.item_id', $item_id)
            ->join('items_weekly_prices as iwp', function ($join) {
                $join->on('main.city', '=', 'iwp.city')
                    ->on('main.quality', '=', 'iwp.quality')
                    ->on('main.item_id', '=', 'iwp.item_id');
            })
            ->where('iwp.item_count', '>', 100)
            ->select('main.*', 'iwp.item_count as qtd');
        if ($cidadeDoItem) {
            $query->where('main.city', $cidadeDoItem);
        }
        $item['dadosDeMercado'] = $query->orderBy('price', 'desc')->first();
        
        $item['ingredientes'] = [];

        $receita = ItemRecipe::with(['itemIngrediente'])
            ->where('item_id', $item_id)
            ->where('recipe', $receita)
            ->get();

        foreach ($receita as $ingrediente) {

            $quantidadeDeItensPorCidade = ItemsWeeklyPrice::where('item_id', $ingrediente->item_ingrediente_id)
                ->where('city', '!=', 'Black Market')
                ->select('city', 'item_count')
                ->where('item_count', '>', 100)
                ->get()->pluck('item_count', 'city')?->toArray();

            $citys = array_keys($quantidadeDeItensPorCidade);
            $tableName = (new $priceModel)->getTable();
            $ingredienteQuery = (new $priceModel)->where('item_id', $ingrediente->item_ingrediente_id)
                ->join('items', 'items.id', '=', $tableName . '.item_id')
                ->where('city', '!=', 'Black Market')
                ->whereIn('city', $citys)
                ->where('price', '>', 0)
                ->orderBy('price', 'asc');
                

            if ($cidadeDoIngrediente) {
                $ingredienteQuery->where('city', $cidadeDoIngrediente);
            }

            if ($cidadeDoIngrediente) {
                $ingredienteQuery->where('city', $cidadeDoIngrediente);
            }
            
            if ($cidadeDoIngrediente) {
                $ingredienteQuery->where('city', $cidadeDoIngrediente);
            }

            $ingredienteQuery = $ingredienteQuery->first();

            if (!$ingredienteQuery || !$quantidadeDeItensPorCidade) {
                $item['ingredientes']['error'] = 'Dados insuficientes para o ingrediente ' . $ingrediente->itemIngrediente->name_pt;
                break;
            }

            $ingredienteQuery = $ingredienteQuery->toArray();
            $ingredienteQuery['qtd'] = $ingrediente->amount;

            $ingredienteQuery['item_count'] = $quantidadeDeItensPorCidade[$ingredienteQuery['city']] ?? 0;
            $ingredienteQuery['encantamento'] = str_contains($ingredienteQuery['external_id'], '@') ? explode('@', $ingredienteQuery['external_id'])[1] : '0';
            $item['ingredientes'][] = $ingredienteQuery;
        }
        return response()->json(['data' => $item]);
    }
}


// {
//    "data":{
//       "encantamento": 1,
//       "name_sp":"Armadura de soldado del maestro",
//       "name_pt":"Armadura de Soldado do Mestre",
//       "dadosDeMercado":{
//          "id":137801,
//          "item_id":3421,
//          "query_date":"2025-09-15",
//          "city":"Black Market",
//          "item_count":null,
//          "price":"0.00",
//       },
//       "ingredientes":[
//          {
//             "id":1090,
//             "query_date":"2025-09-14",
//             "city":"Thetford",
//             "item_count":16789,
//             "price":"6760.00",
//             "quality":1,
//             "name_pt":"Barra de Aço Runita Incomum",
//             "qtd":16
//          }
//       ]
//    }
// }