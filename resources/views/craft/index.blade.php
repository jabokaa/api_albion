@extends('layouts.app')

@section('content')
<div>
    <h2>Craft</h2>
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
                <input type="text" name="name_item" class="form-control" placeholder="Nome do item" value="{{ request('name_item') }}">
            </div>
            <div class="col">
                <input type="number" name="min_count" class="form-control" placeholder="Qtd mínima" value="{{ request('min_count', 100) }}">
            </div>
            <div class="col">
                <select name="order_by" class="form-control">
                    <option value="lucro" {{ request('order_by') == 'lucro' ? 'selected' : '' }}>Lucro</option>
                    <option value="porcentagem" {{ request('order_by') == 'porcentagem' ? 'selected' : '' }}>Porcentagem</option>
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
                <th>Custo</th>
                <th>Valor venda</th>
                <th>Lucro</th>
                <th>Porcentagem</th>
                <th>Receita</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $row)
            <tr>
                <td>{{ $row->external_id }}</td>
                <td>{{ $row->name_pt }} / {{ $row->name_sp }}</td>
                <td>{{ $row->encantamento }}</td>
                <td>{{ number_format($row->custo, 2, ',', '.') }}$</td>
                <td>{{ number_format($row->valor, 2, ',', '.') }}$</td>
                <td>{{ number_format($row->lucro, 2, ',', '.') }}$</td>
                <td>{{ number_format($row->porcentagem, 2, ',', '.') }}%</td>
                <td>{{ $row->recipe }} </td>
                <td><a href="https://albiononline.com/en/market/#!/item/{{ $row->external_id }}" target="_blank" class="btn btn-sm btn-info">Ver no Market</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
