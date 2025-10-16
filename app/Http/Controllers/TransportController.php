<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemsDayPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportController extends Controller
{
    public function index(Request $request, $dataType)
    {
        $citySale = $request->input('city_sale');
        $cityBuy = $request->input('city_buy');

        $orderBy = $request->input('order_by', 'porcemtagem_lucro');
        $orderDir = $request->input('order_dir', 'desc');
        $maxLucro = $request->input('max_lucro', 200);
        $minLucro = $request->input('min_lucro', 0);

        $tableMain = $dataType !== 'semanal' ? 'items_day_prices' : 'items_weekly_prices';
        $mainTable = DB::query()->from("$tableMain as idp");

        $selectMaxValue = $citySale ? "MAX(CASE WHEN market_data.city = '$citySale' THEN market_data.price END)" : "max(market_data.price)";
        $selectMinValue = $cityBuy ? "MIN(CASE WHEN market_data.city = '$cityBuy' THEN market_data.price END)" : "min(market_data.price)";
        $subQueryValoresDeCompraEVenda = DB::query()->from("$tableMain as market_data")
            ->select(
                'items.external_id',
                'items.name_pt',
                'items.name_sp',
                'market_data.item_id',
                'market_data.quality',
                DB::raw( $selectMaxValue . ' as maior'),
                DB::raw( $selectMinValue . ' as menor'),
                DB::raw("$selectMaxValue - $selectMinValue as diferenca")
            )
            ->join('items', 'items.id', '=', 'market_data.item_id')
            ->where('market_data.price', '>', 0)
            ->where(( $dataType !== 'semanal' ? 'iwp.item_count' : 'market_data.item_count' ), '>=', 100)
            ->groupBy(
                'market_data.item_id',
                'market_data.quality',
                'items.external_id',
                'items.name_pt',
                'items.name_sp'
            )
            ->having('diferenca', '>', 0);

        if ($dataType !== 'semanal') {
            $subQueryValoresDeCompraEVenda->join('items_weekly_prices as iwp', function ($join) {
                $join->on('market_data.city', '=', 'iwp.city')
                    ->on('market_data.quality', '=', 'iwp.quality')
                    ->on('market_data.item_id', '=', 'iwp.item_id');
            })->where('iwp.item_count', '>=', 100);
        } else {
            $subQueryValoresDeCompraEVenda->where('market_data.item_count', '>=', 100);
        }

        $subQueryInformacoesMarketData = DB::query()->from("$tableMain as market_data")
            ->select(
                'market_data.city',
                'market_data.price',
                'market_data.item_id',
                'market_data.quality',
                'market_data.query_date',
                ( $dataType !== 'semanal' ? 'iwp.item_count' : 'market_data.item_count' ) . ' as item_count',
            );

        if ($dataType !== 'semanal') {
            $subQueryInformacoesMarketData->join('items_weekly_prices as iwp', function ($join) {
                $join->on('market_data.city', '=', 'iwp.city')
                    ->on('market_data.quality', '=', 'iwp.quality')
                    ->on('market_data.item_id', '=', 'iwp.item_id');
            });
        }

        $query = DB::query()->fromSub($subQueryValoresDeCompraEVenda, 'idp')
            ->select(
                'idp.item_id',
                'idp.external_id',
                DB::raw("
                    CASE 
                        WHEN idp.external_id LIKE '%@%' THEN SUBSTRING_INDEX(idp.external_id, '@', -1)
                        ELSE '0'
                    END as encantamento
                "),
                'idp.name_pt',
                'idp.name_sp',
                'idp.quality',
                'idp.diferenca',
                'info_maior.city as vender_em',
                'info_maior.price as vender_por',
                'info_menor.city as comprar_em',
                DB::raw('(idp.diferenca/info_menor.price) * 100 as porcemtagem_lucro'),
                'info_menor.price as comprar_por',
                'info_menor.query_date as data_atulizacao_compra',
                'info_maior.item_count as item_count_vender',
                'info_maior.query_date as data_atulizacao_venda',
                'info_menor.item_count as item_count_comprar'
            )
            ->joinSub($subQueryInformacoesMarketData, 'info_maior', function ($join) {
                $join->on('info_maior.price', '=', 'idp.maior')
                    ->on('info_maior.quality', '=', 'idp.quality')
                    ->on('info_maior.item_id', '=', 'idp.item_id');
            })->joinSub($subQueryInformacoesMarketData, 'info_menor', function ($join) {
                $join->on('info_menor.price', '=', 'idp.menor')
                    ->on('info_menor.quality', '=', 'idp.quality')
                    ->on('info_menor.item_id', '=', 'idp.item_id');
            })->where(DB::raw('(idp.diferenca / info_menor.price) * 100'), '<=', $maxLucro)
            ->where('idp.diferenca', '>=', $minLucro)
            ->orderBy($orderBy, $orderDir);

            dd( $query->toSql(), $query->getBindings() );
        $results = $query->get();
        
        return view('transport.phone', compact('results'));
    }

    public function show(Request $request, $dataType)
    {

        $table = $dataType !== 'semanal' ? 'items_day_prices' : 'items_weekly_prices';
        $item = $request->item_id;
    
        $item = Item::join($table . ' as idp', function ($join) {
                $join->on('items.id', '=', 'idp.item_id');
            })->where('items.id', $item)
            ->where('idp.price', '>', 0)
            ->get();

        $item = $item->toArray();
        return response()->json($item);
    }

    public function showItem(Request $request, $dataType, $name)
    {
        $table = $dataType !== 'semanal' ? 'items_day_prices' : 'items_weekly_prices';
        $items = Item::join($table . ' as idp', function ($join) {
                $join->on('items.id', '=', 'idp.item_id');
            })
            ->where('items.name_pt', 'like', $name)
            ->where('idp.price', '>', 0)
            ->get();

        $itemsArr = $items->toArray();
        $info = null;
        if (count($itemsArr) > 0) {
            $info = [
                'item_id' => $itemsArr[0]['item_id'],
                'external_id' => $itemsArr[0]['external_id'],
                'name_pt' => $itemsArr[0]['name_pt'],
            ];
        }
        return view('transport.show_item', compact('itemsArr', 'info'));
    }
}
