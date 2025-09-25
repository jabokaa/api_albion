@extends('layouts.app')

@section('content')
<div>
    <h2>Items</h2>
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nome, external id ou index" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>External Id</th>
                <th>Index</th>
                <th>Name PT</th>
                <th>Name SP</th>
                <th>Name EN</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->external_id }}</td>
                <td>{{ $item->index }}</td>
                <td>{{ $item->name_pt }}</td>
                <td>{{ $item->name_sp }}</td>
                <td>{{ $item->name_en }}</td>
                <td>
                    <button type="button" class="btn btn-info btn-sm detalhes-btn" data-item-id="{{ $item->id }}">Detalhes</button>
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
            var itemId = $(this).data('item-id');
            $('#detalhesTabela tbody').html('<tr><td colspan="5">Carregando...</td></tr>');
            $('#detalhesInfo').html('');
            $('#detalhesModal').modal('show');
            $.get('/items/details', { item_id: itemId }, function(data) {
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
        $('#modalCloseBtn').on('click', function() {
            $('#detalhesModal').modal('hide');
        });
    });
</script>
@endpush
@endsection
