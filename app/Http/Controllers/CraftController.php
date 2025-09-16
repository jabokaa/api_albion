<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CraftController extends Controller
{
    public function index(Request $request)
    {
        $city = $request->input('city');
        $quality = $request->input('quality');
        $orderBy = $request->input('order_by', 'diferenca');
        $orderDir = $request->input('order_dir', 'desc');
        $minCount = $request->input('min_count', 100);

        $query = "
        select
            iwp.external_id,
            iwp.name_pt,
            iwp.name_sp,
            iwp.quality,
            iwp.diferenca,
            info_maior.city vender_em,
            info_maior.item_count item_count_vender,
            info_maior.price vender_por,
            info_menor.city comprar_em,
            info_menor.item_count item_count_comprar,
            (iwp.diferenca/info_menor.price) * 100 as porcemtagem_lucro,
            info_menor.price comprar_por
        from
            (
                select
                    i.external_id,
                    i.name_pt,
                    i.name_sp ,
                    iwp.item_id,
                    iwp.quality,
                    max(iwp.price) maior,
                    min(iwp.price) menor,
                    max(iwp.price) - min(iwp.price) diferenca,
                    AVG(iwp.item_count) qtd_avg
                from
                    items_weekly_prices iwp
                join items i on
                    i.id = iwp.item_id
                where
                    iwp.item_count > ?
                group by
                    iwp.item_id,
                    iwp.quality
            ) iwp
        join (
            select
                info_maior.city,
                info_maior.price,
                info_maior.item_id,
                info_maior.item_count,
                info_maior.quality
            from
                items_weekly_prices as info_maior
        ) info_maior on
            info_maior.price = iwp.maior
            and info_maior.quality = iwp.quality
            and info_maior.item_id = iwp.item_id
        join (
            select
                info_menor.city,
                info_menor.price,
                info_menor.item_id ,
                info_menor.item_count,
                info_menor.quality
            from
                items_weekly_prices as info_menor
        ) info_menor on
            info_menor.price = iwp.menor
            and info_menor.quality = iwp.quality
            and info_menor.item_id = iwp.item_id
        WHERE (iwp.diferenca/info_menor.price) * 100 <= 150
        ";

        $bindings = [$minCount];
        $where = [];
        if ($city) {
            $where[] = "(info_maior.city = ? OR info_menor.city = ?)";
            $bindings[] = $city;
            $bindings[] = $city;
        }
        if ($quality) {
            $where[] = "iwp.quality = ?";
            $bindings[] = $quality;
        }
        if ($where) {
            $query .= " WHERE " . implode(' AND ', $where);
        }
        $query .= " ORDER BY $orderBy $orderDir";

        $results = DB::select($query, $bindings);
        return view('craft.index', compact('results'));
    }


}
