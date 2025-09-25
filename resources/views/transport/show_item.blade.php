@extends('layouts.app')

@section('content')
<div>
    <h2>Detalhes do Item</h2>
    @if($info)
        <div class="mb-3">
            <strong>Item ID:</strong> {{ $info['item_id'] }} &nbsp;
            <strong>External ID:</strong> {{ $info['external_id'] }} &nbsp;
            <strong>Nome PT:</strong> {{ $info['name_pt'] }}
        </div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>query_date</th>
                <th>city</th>
                <th>item_count</th>
                <th>price</th>
                <th>quality</th>
            </tr>
        </thead>
        <tbody>
            @foreach($itemsArr as $item)
            <tr>
                <td>{{ $item['query_date'] }}</td>
                <td>{{ $item['city'] }}</td>
                <td>{{ $item['item_count'] }}</td>
                <td>{{ $item['price'] }}</td>
                <td>{{ $item['quality'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
