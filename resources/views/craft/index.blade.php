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
                    <option value="Brecilien" {{ request('city_buy') == 'Brecilien' ? 'selected' : '' }}>Brecilien</option>
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
                    <td><button class="btn btn-sm btn-primary open-modal" data-id="{{ $row->id }}" data-recipe="{{ $row->recipe }}" data-datatype="{{ request()->route('dataType') }}">Detalhes</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

<!-- Modal -->
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                  <h5 class="modal-title" id="itemModalLabel">Detalhes do Item</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="item-info"></div>
                <div class="row mb-2">
                    <div class="col">
                        <select id="cidade_do_item" class="form-control">
                            <option value="">Cidade do Item</option>
                            <option value="Bridgewatch">Bridgewatch</option>
                            <option value="Martlock">Martlock</option>
                            <option value="Fort Sterling">Fort Sterling</option>
                            <option value="Lymhurst">Lymhurst</option>
                            <option value="Thetford">Thetford</option>
                            <option value="Caerleon">Caerleon</option>
                            <option value="Black Market">Black Market</option>
                            <option value="Brecilien">Brecilien</option>
                        </select>
                    </div>
                    <div class="col">
                        <select id="cidade_do_ingrediente" class="form-control">
                            <option value="">Cidade do Ingrediente</option>
                            <option value="Bridgewatch">Bridgewatch</option>
                            <option value="Martlock">Martlock</option>
                            <option value="Fort Sterling">Fort Sterling</option>
                            <option value="Lymhurst">Lymhurst</option>
                            <option value="Thetford">Thetford</option>
                            <option value="Caerleon">Caerleon</option>
                            <option value="Black Market">Black Market</option>
                            <option value="Brecilien">Brecilien</option>
                        </select>
                    </div>
                                <div class="col-auto">
                                    <button id="aplicar-filtro" class="btn btn-success">Aplicar Filtro</button>
                                </div>
                </div>
                <div id="ingredientes-table"></div>
            </div>
            <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentId = null;
let currentRecipe = null;
let currentDataType = null;

function fetchItemDetails() {
        const cidade_do_item = $('#cidade_do_item').val();
        const cidade_do_ingrediente = $('#cidade_do_ingrediente').val();
        $.get(`/craft/details/${currentDataType}`, {
                item_id: currentId,
                receita: currentRecipe,
                cidade_do_item,
                cidade_do_ingrediente
        }, function(resp) {
                if (resp.data) {
                        const item = resp.data;
                        let infoHtml = `<strong>${item.name_pt} / ${item.name_sp}</strong><br>Encantamento: ${item.encantamento}<br>`;
                        if (item.dadosDeMercado) {
                            infoHtml += `Cidade: ${item.dadosDeMercado.city || '-'}<br>Preço: ${item.dadosDeMercado.price || '-'}<br>Qtd: ${item.dadosDeMercado.qtd || '-'}<br>`;
                            infoHtml += `Data Preço: ${item.dadosDeMercado.query_date || '-'}<br>`;
                        }
                        $('#item-info').html(infoHtml);

                        if (item.ingredientes && Array.isArray(item.ingredientes)) {
                            let tableHtml = `<table class=\"table table-bordered\"><thead><tr><th>Nome</th><th style='width:60px;'>Encantamento</th><th>Cidade</th><th>Preço</th><th>Qtd</th><th>Custo Total</th><th>Item Count</th><th style='min-width:120px;'>Data Preço</th></tr></thead><tbody>`;
                            let somaTotal = 0;
                            item.ingredientes.forEach(ing => {
                                let preco = parseFloat(ing.price) || 0;
                                let qtd = parseInt(ing.qtd) || 0;
                                let custoTotal = preco * qtd;
                                somaTotal += custoTotal;
                                tableHtml += `<tr><td>${ing.name_pt || '-'}<\/td><td>${ing.encantamento || '-'}<\/td><td>${ing.city || '-'}<\/td><td>${ing.price || '-'}<\/td><td>${ing.qtd || '-'}<\/td><td>${custoTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}<\/td><td>${ing.item_count || '-'}<\/td><td>${ing.query_date || '-'}<\/td></tr>`;
                            });
                            tableHtml += `</tbody></table>`;
                            tableHtml += `<div class=\"mt-2\"><strong>Custo Total dos Ingredientes: </strong> ${somaTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>`;
                            let precoVenda = 0;
                            if (item.dadosDeMercado && item.dadosDeMercado.price) {
                                precoVenda = parseFloat(item.dadosDeMercado.price) || 0;
                            }
                            let lucroTotal = precoVenda - somaTotal;
                            tableHtml += `<div class=\"mt-2\"><strong>Lucro Total: </strong> ${lucroTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>`;
                            $('#ingredientes-table').html(tableHtml);
                        } else if (item.ingredientes && item.ingredientes.error) {
                                $('#ingredientes-table').html(`<div class="alert alert-warning">${item.ingredientes.error}</div>`);
                        } else {
                                $('#ingredientes-table').html('<div>Nenhum ingrediente encontrado.</div>');
                        }
                }
        });
}

$(document).on('click', '.open-modal', function() {
        currentId = $(this).data('id');
        currentRecipe = $(this).data('recipe');
        currentDataType = $(this).data('datatype');
        $('#cidade_do_item').val('');
        $('#cidade_do_ingrediente').val('');
        fetchItemDetails();
        $('#itemModal').modal('show');
});

$('#cidade_do_item, #cidade_do_ingrediente').on('change', function() {
    // Não faz nada ao mudar, só ao clicar no botão
});

$('#aplicar-filtro').on('click', function() {
    fetchItemDetails();
});
</script>
@endpush
