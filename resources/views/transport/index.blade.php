@extends('layouts.app')

@section('content')
<div>
    <h2>Transport</h2>
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col">
                <select name="city_sale" class="form-control">
                    <option value="">Cidade venda</option>
                    <option value="Bridgewatch" {{ request('city_sale') == 'Bridgewatch' ? 'selected' : '' }}>Bridgewatch</option>
                    <option value="Martlock" {{ request('city_sale') == 'Martlock' ? 'selected' : '' }}>Martlock</option>
                    <option value="Fort Sterling" {{ request('city_sale') == 'Fort Sterling' ? 'selected' : '' }}>Fort Sterling</option>
                    <option value="Lymhurst" {{ request('city_sale') == 'Lymhurst' ? 'selected' : '' }}>Lymhurst</option>
                    <option value="Thetford" {{ request('city_sale') == 'Thetford' ? 'selected' : '' }}>Thetford</option>
                    <option value="Caerleon" {{ request('city_sale') == 'Caerleon' ? 'selected' : '' }}>Caerleon</option>
                    <option value="Black Market" {{ request('city_sale') == 'Black Market' ? 'selected' : '' }}>Black Market</option>
                </select>
            </div>
            <div class="col">
                <select name="city_buy" class="form-control">
                    <option value="">Cidade Compra</option>
                    <option value="Bridgewatch" {{ request('city_buy') == 'Bridgewatch' ? 'selected' : '' }}>Bridgewatch</option>
                    <option value="Martlock" {{ request('city_buy') == 'Martlock' ? 'selected' : '' }}>Martlock</option>
                    <option value="Fort Sterling" {{ request('city_buy') == 'Fort Sterling' ? 'selected' : '' }}>Fort Sterling</option>
                    <option value="Lymhurst" {{ request('city_buy') == 'Lymhurst' ? 'selected' : '' }}>Lymhurst</option>
                    <option value="Thetford" {{ request('city_buy') == 'Thetford' ? 'selected' : '' }}>Thetford</option>
                    <option value="Caerleon" {{ request('city_buy') == 'Caerleon' ? 'selected' : '' }}>Caerleon</option>
                    <option value="Black Market" {{ request('city_buy') == 'Black Market' ? 'selected' : '' }}>Black Market</option>
                </select>
            </div>
            <div class="col">
                <input type="number" name="max_lucro" class="form-control" placeholder="% Lucro Máximo" value="{{ request('max_lucro', 200) }}">
            </div>
            <div class="col">
                <select name="order_by" class="form-control">
                    <option value="porcemtagem_lucro" {{ request('order_by') == 'porcemtagem_lucro' ? 'selected' : '' }}>Porcentagem Lucro</option>
                    <option value="diferenca" {{ request('order_by') == 'diferenca' ? 'selected' : '' }}>Lucro</option>
                </select>
            </div>
            <div class="col">
                <select name="order_dir" class="form-control">
                    <option value="desc" {{ request('order_dir') == 'desc' ? 'selected' : '' }}>Desc</option>
                    <option value="asc" {{ request('order_dir') == 'asc' ? 'selected' : '' }}>Asc</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>External Id</th>
                <th>Item</th>
                <th>Encantamento</th>
                <th>Qualidade</th>
                <th>Lucro</th>
                <th>Porcentagem Lucro</th>
                <th>Comprar em</th>
                <th>Preço Compra</th>
                <th>Qtd Compra</th>
                <th>Atualização Compra</th>
                <th>Vender em</th>
                <th>Preço Venda</th>
                <th>Qtd Venda</th>
                <th>Atualização Venda</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $row)
            <tr>
                <td>{{ $row->external_id }}</td>
                <td>{{ $row->name_pt }} / {{ $row->name_sp }}</td>
                <td>{{ $row->encantamento }}</td>
                <td>{{ $row->quality }}</td>
                <td>{{ number_format($row->diferenca, 2, ',', '.') }}</td>
                <td>{{ number_format($row->porcemtagem_lucro, 2, ',', '.') }}%</td>
                <td>{{ $row->comprar_em }} </td>
                <td>-{{ number_format($row->comprar_por, 2, ',', '.') }}$</td>
                <td>{{ $row->item_count_comprar }}</td>
                <td>{{ \Carbon\Carbon::parse($row->data_atulizacao_compra)->format('d/m/Y H:i:s') }}</td>
                <td>{{ $row->vender_em }}</td>
                <td>+{{ number_format($row->vender_por, 2, ',', '.') }}$</td>
                <td>{{ $row->item_count_vender }}</td>
                <td>{{ \Carbon\Carbon::parse($row->data_atulizacao_venda)->format('d/m/Y H:i:s') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
