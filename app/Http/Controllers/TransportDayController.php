<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportDayController extends Controller
{
    public function index(Request $request)
    {
        $city = $request->input('city');
        $quality = $request->input('quality');
        $orderBy = $request->input('order_by', 'porcemtagem_lucro');
        $orderDir = $request->input('order_dir', 'desc');
        $maxLucro = $request->input('max_lucro', 10000);

        $query = "
        select
            idp.external_id,
            idp.name_pt,
            idp.name_sp,
            idp.quality,
            idp.deferenca,
            info_maior.city vender_em,
            info_maior.price vender_por,
            info_menor.city comprar_em,
            (idp.deferenca/info_menor.price) * 100 as porcemtagem_lucro,
            info_menor.price comprar_por,
            info_menor.query_date data_atulizacao_compra,
            info_maior.item_count item_count_vender,
            info_maior.query_date data_atulizacao_venda,
            info_menor.item_count item_count_comprar
        from
            (
                select
                    i.external_id,
                    i.name_pt,
                    i.name_sp ,
                    idp.item_id,
                    idp.quality,
                    max(idp.price) maior,
                    min(idp.price) menor,
                    max(idp.price) - min(idp.price) deferenca
                from
                    items_day_prices idp
                join items i on
                    i.id = idp.item_id
                where idp.price > 0
                group by
                    idp.item_id,
                    idp.quality
            ) idp
        join (
            select
                info_maior.city,
                info_maior.price,
                info_maior.item_id,
                info_maior.quality,
                info_maior.query_date,
                iwp.item_count
            from
                items_day_prices as info_maior
            join items_weekly_prices iwp on
            info_maior.city = iwp.city
            and info_maior.quality = iwp.quality
            and info_maior.item_id = iwp.item_id
        ) info_maior on
            info_maior.price = idp.maior
            and info_maior.quality = idp.quality
            and info_maior.item_id = idp.item_id
            and info_maior.item_count >= 100
        join (
            select
                info_menor.city,
                info_menor.price,
                info_menor.item_id ,
                info_menor.quality,
                info_menor.query_date,
                iwp.item_count
            from
                items_day_prices as info_menor
            join items_weekly_prices iwp on
	            info_menor.city = iwp.city
	            and info_menor.quality = iwp.quality
	            and info_menor.item_id = iwp.item_id
        ) info_menor on
            info_menor.price = idp.menor
            and info_menor.quality = idp.quality
            and info_menor.item_id = idp.item_id
            and info_menor.item_count >= 100
        WHERE (idp.deferenca/info_menor.price) * 100 <= ?
        ";

        $bindings = [$maxLucro];
        $where = [];
        if ($city) {
            $where[] = "(info_maior.city = ? OR info_menor.city = ?)";
            $bindings[] = $city;
            $bindings[] = $city;
        }
        if ($quality) {
            $where[] = "idp.quality = ?";
            $bindings[] = $quality;
        }
        if ($where) {
            $query .= " AND " . implode(' AND ', $where);
        }
        $query .= " ORDER BY $orderBy $orderDir";

        $results = DB::select($query, $bindings);
        return view('transport-day.index', compact('results'));
    }
}
