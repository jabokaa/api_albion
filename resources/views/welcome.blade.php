
@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Comandos do Sistema</h1>
    <div class="card">
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>php artisan populate:items</strong><br>
                    Popula a tabela <code>items</code> a partir do JSON oficial do Albion Online.
                </li>
                <li class="list-group-item">
                    <strong>php artisan populate:day-prices</strong><br>
                    Popula a tabela <code>items_day_prices</code> com os preços diários dos itens.
                </li>
                <li class="list-group-item">
                    <strong>php artisan populate:weekly-prices</strong><br>
                    Popula a tabela <code>items_weekly_prices</code> com os preços semanais dos itens.
                </li>
                <li class="list-group-item">
                    <strong>php artisan import:item-recipes equipamentos.json</strong><br>
                    Importa receitas de equipamentos de um arquivo JSON para a tabela <code>item_recipes</code>.
                </li>
                <li class="list-group-item">
                    <strong>php artisan import:item-recipes food.json</strong><br>
                    Importa receitas de foods e poções de um arquivo JSON para a tabela <code>item_recipes</code>.
                </li>
                <li class="list-group-item">
                    <strong>php artisan fetch:wiki-data</strong><br>
                    Busca e extrai dados de uma página HTML da Wiki do Albion Online.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
