<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albion Trade Opportunities</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">Albion API</a>
            <div class="d-flex gap-2">
                <a class="btn {{ request()->routeIs('transport.index') && request()->route('dataType') == 'semanal' ? 'btn-primary active' : 'btn-light' }} border-0" href="{{ route('transport.index', ['dataType' => 'semanal']) }}">Transporte Semanal</a>
                <a class="btn {{ request()->routeIs('transport.index') && request()->route('dataType') == 'diario' ? 'btn-primary active' : 'btn-light' }} border-0" href="{{ route('transport.index', ['dataType' => 'diario']) }}">Transporte Diário</a>
                <a class="btn {{ request()->routeIs('craft.index') && request()->route('dataType') == 'semanal' ? 'btn-success active' : 'btn-light' }} border-0" href="{{ route('craft.index', ['dataType' => 'semanal']) }}">Craft Semanal</a>
                <a class="btn {{ request()->routeIs('craft.index') && request()->route('dataType') == 'diario' ? 'btn-success active' : 'btn-light' }} border-0" href="{{ route('craft.index', ['dataType' => 'diario']) }}">Craft Diário</a>
            </div>
        </div>
    </nav>
    <main>
        @yield('content')
            @stack('scripts')
    </main>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</html>
