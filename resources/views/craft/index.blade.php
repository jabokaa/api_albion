@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Compra</h2>
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col">
                <input type="text" name="city" class="form-control" placeholder="Cidade" value="{{ request('city') }}">
            </div>
            <div class="col">
                <input type="number" name="quality" class="form-control" placeholder="Qualidade" value="{{ request('quality') }}">
            </div>
            <div class="col">
                <input type="number" name="min_count" class="form-control" placeholder="Qtd mínima" value="{{ request('min_count', 100) }}">
            </div>
            <div class="col">
                <select name="order_by" class="form-control">
                    <option value="deferenca" {{ request('order_by') == 'deferenca' ? 'selected' : '' }}>Lucro</option>
                    <option value="vender_por" {{ request('order_by') == 'vender_por' ? 'selected' : '' }}>Preço Venda</option>
                    <option value="comprar_por" {{ request('order_by') == 'comprar_por' ? 'selected' : '' }}>Preço Compra</option>
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
                <th>Qualidade</th>
                <th>Lucro</th>
                <th>Porcentagem Lucro</th>
                <th>Comprar em</th>
                <th>Qtd Compra</th>
                <th>Preço Compra</th>
                <th>Vender em</th>
                <th>Qtd Venda</th>
                <th>Preço Venda</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $row)
            <tr>
                <td>{{ $row->external_id }}</td>
                <td>{{ $row->name_pt }} / {{ $row->name_sp }}</td>
                <td>{{ $row->quality }}</td>
                <td>{{ number_format($row->deferenca, 2, ',', '.') }}</td>
                <td>{{ $row->porcemtagem_lucro }}%</td>
                <td>{{ $row->comprar_em }} </td>
                <td>{{ $row->item_count_comprar }}</td>
                <td>-{{ number_format($row->comprar_por, 2, ',', '.') }}$</td>
                <td>{{ $row->vender_em }}</td>
                <td>{{ $row->item_count_vender }}</td>
                <td>+{{ number_format($row->vender_por, 2, ',', '.') }}$</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
