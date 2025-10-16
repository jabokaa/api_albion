@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">🚚 Transport</h2>
    <form method="GET" class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade Venda</label>
                <select name="city_sale" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Selecionar cidade</option>
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
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade Compra</label>
                <select name="city_buy" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Selecionar cidade</option>
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
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lucro Mínimo</label>
                <input type="number" name="min_lucro" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0" value="{{ request('min_lucro', 0) }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">% Lucro Máximo</label>
                <input type="number" name="max_lucro" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="200" value="{{ request('max_lucro', 200) }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ordenar por</label>
                <select name="order_by" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="porcemtagem_lucro" {{ request('order_by') == 'porcemtagem_lucro' ? 'selected' : '' }}>Porcentagem Lucro</option>
                    <option value="diferenca" {{ request('order_by') == 'diferenca' ? 'selected' : '' }}>Lucro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Direção</label>
                <select name="order_dir" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="desc" {{ request('order_dir') == 'desc' ? 'selected' : '' }}>Decrescente</option>
                    <option value="asc" {{ request('order_dir') == 'asc' ? 'selected' : '' }}>Crescente</option>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">&nbsp;</label>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                    🔍 Filtrar
                </button>
            </div>
        </div>
    </form>
    @if(count($results) > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($results as $row)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6">
                <!-- Cabeçalho do Card -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h5 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                            {{ $row->name_pt }}
                        </h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $row->name_sp }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">
                            ID: {{ $row->external_id }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-green-600 dark:text-green-400">
                            {{ number_format($row->diferenca, 2, ',', '.') }}
                        </div>
                        <div class="text-sm text-green-500">
                            {{ number_format($row->porcemtagem_lucro, 2, ',', '.') }}%
                        </div>
                    </div>
                </div>

                <!-- Informações do Item -->
                <div class="mb-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Encantamento:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $row->encantamento }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Qualidade:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $row->quality }}</span>
                    </div>
                </div>

                <!-- Seção de Compra -->
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 mb-3">
                    <h6 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">📉 Comprar</h6>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Local:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $row->comprar_em }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Preço:</span>
                            <span class="font-medium text-red-600 dark:text-red-400">-{{ number_format($row->comprar_por, 2, ',', '.') }}$</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Quantidade:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $row->item_count_comprar }}</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-500">
                            Atualizado: {{ \Carbon\Carbon::parse($row->data_atulizacao_compra)->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                </div>

                <!-- Seção de Venda -->
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 mb-4">
                    <h6 class="text-sm font-semibold text-green-800 dark:text-green-300 mb-2">📈 Vender</h6>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Local:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $row->vender_em }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Preço:</span>
                            <span class="font-medium text-green-600 dark:text-green-400">+{{ number_format($row->vender_por, 2, ',', '.') }}$</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Quantidade:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $row->item_count_vender }}</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-500">
                            Atualizado: {{ \Carbon\Carbon::parse($row->data_atulizacao_venda)->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                </div>

                <!-- Botão de Detalhes -->
                <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 detalhes-btn" data-item-id="{{ $row->external_id }}" data-id="{{ $row->item_id }}">
                    Ver Detalhes
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <div class="max-w-md mx-auto">
            <div class="text-6xl mb-4">📦</div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Nenhum resultado encontrado</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Ajuste os filtros para encontrar oportunidades de transporte.</p>
            <button onclick="window.location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                🔄 Atualizar
            </button>
        </div>
    </div>
    @endif
    </div>

    <!-- Modal -->
    <div class="fixed inset-0 z-50 hidden overflow-y-auto" id="detalhesModal" aria-labelledby="detalhesModalLabel" aria-hidden="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full dark:bg-gray-800">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="detalhesModalLabel">
                            📊 Detalhes do Item
                        </h3>
                        <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300" id="modalCloseBtn">
                            <span class="sr-only">Fechar</span>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div id="detalhesInfo" class="mb-4 p-3 bg-gray-50 rounded-lg dark:bg-gray-700"></div>
                    
                    <!-- Tabela responsiva -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600" id="detalhesTabela">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Cidade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Quantidade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Preço</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Qualidade</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-600">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Função para mostrar o modal
            function showModal() {
                $('#detalhesModal').removeClass('hidden');
                setTimeout(() => {
                    $('#detalhesModal').addClass('opacity-100');
                }, 10);
            }
            
            // Função para esconder o modal
            function hideModal() {
                $('#detalhesModal').removeClass('opacity-100');
                setTimeout(() => {
                    $('#detalhesModal').addClass('hidden');
                }, 300);
            }

            $('.detalhes-btn').on('click', function() {
                var itemId = $(this).data('id');
                $('#detalhesTabela tbody').html('<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">⏳ Carregando...</td></tr>');
                $('#detalhesInfo').html('');
                showModal();
                
                $.get('http://localhost/transport/details/diario', { item_id: itemId }, function(data) {
                    if (data.length > 0) {
                        var info = '<div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">' +
                            '<div><span class="font-semibold text-gray-700 dark:text-gray-300">Item ID:</span> <span class="text-gray-900 dark:text-white">' + data[0].item_id + '</span></div>' +
                            '<div><span class="font-semibold text-gray-700 dark:text-gray-300">External ID:</span> <span class="text-gray-900 dark:text-white">' + data[0].external_id + '</span></div>' +
                            '<div><span class="font-semibold text-gray-700 dark:text-gray-300">Nome:</span> <span class="text-gray-900 dark:text-white">' + data[0].name_pt + '</span></div>' +
                            '</div>';
                        $('#detalhesInfo').html(info);
                    }
                    var rows = '';
                    data.forEach(function(item) {
                        rows += '<tr class="hover:bg-gray-50 dark:hover:bg-gray-700">' +
                            '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">' + item.query_date + '</td>' +
                            '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">' + item.city + '</td>' +
                            '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">' + (item.item_count ?? '') + '</td>' +
                            '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">' + item.price + '</td>' +
                            '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">' + item.quality + '</td>' +
                        '</tr>';
                    });
                    $('#detalhesTabela tbody').html(rows);
                });
            });

            // Fechar modal ao clicar no X ou fora do modal
            $('#modalCloseBtn, #detalhesModal .fixed.inset-0').on('click', function(e) {
                if (e.target === this) {
                    hideModal();
                }
            });

            // Fechar modal com ESC
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && !$('#detalhesModal').hasClass('hidden')) {
                    hideModal();
                }
            });
        });
    </script>
    @endpush
</div>
@endsection
