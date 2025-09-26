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
                    <option value="Brecilien" {{ request('city_sale') == 'Brecilien' ? 'selected' : '' }}>Brecilien</option>
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
                    <option value="Brecilien" {{ request('city_buy') == 'Brecilien' ? 'selected' : '' }}>Brecilien</option>
                </select>
            </div>
            <div class="col">
                <input type="number" name="min_lucro" class="form-control" placeholder="Lucro Mínimo" value="{{ request('min_lucro', 0) }}">
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
                <td>
                        <button type="button" class="btn btn-info btn-sm detalhes-btn" data-item-id="{{ $row->external_id }}" data-id="{{ $row->item_id }}">Detalhes</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalhesModalLabel">Detalhes do Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="modalCloseBtn">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <div id="detalhesInfo" class="mb-3"></div>
                        <table class="table table-bordered" id="detalhesTabela">
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
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.detalhes-btn').on('click', function() {
                var itemId = $(this).data('id');
                $('#detalhesTabela tbody').html('<tr><td colspan="5">Carregando...</td></tr>');
                $('#detalhesInfo').html('');
                $('#detalhesModal').modal('show');
                $.get('http://localhost/transport/details/diario', { item_id: itemId }, function(data) {
                    if (data.length > 0) {
                        var info = '<strong>Item ID:</strong> ' + data[0].item_id + ' &nbsp; ' +
                            '<strong>External ID:</strong> ' + data[0].external_id + ' &nbsp; ' +
                            '<strong>Nome PT:</strong> ' + data[0].name_pt;
                        $('#detalhesInfo').html(info);
                    }
                    var rows = '';
                    data.forEach(function(item) {
                        rows += '<tr>' +
                            '<td>' + item.query_date + '</td>' +
                            '<td>' + item.city + '</td>' +
                            '<td>' + (item.item_count ?? '') + '</td>' +
                            '<td>' + item.price + '</td>' +
                            '<td>' + item.quality + '</td>' +
                        '</tr>';
                    });
                    $('#detalhesTabela tbody').html(rows);
                });
            });
            // Garantir fechamento do modal ao clicar no X
            $('#modalCloseBtn').on('click', function() {
                $('#detalhesModal').modal('hide');
            });
        });
    </script>
    @endpush
</div>
@endsection
