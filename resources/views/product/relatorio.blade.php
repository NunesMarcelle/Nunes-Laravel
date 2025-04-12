<!-- resources/views/relatorios/produtos.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Produtos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <h2>Relatório de Produtos</h2>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Estoque mínimo</th>

            </tr>
        </thead>
        <tbody>
            @foreach($produtos as $produto)
            <tr>
                <td>{{ $produto->name }}</td>
                <td>R$ {{ number_format($produto->price, 2, ',', '.') }}</td>
                <td>{{ $produto->amount }}</td>
                <td>{{ $produto->min_amount }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
