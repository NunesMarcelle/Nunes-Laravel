<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Serviços</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <h2>Relatório de Serviços</h2>
    <table>
        <thead>
            <tr>
                <th>Nome do Serviço</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $service)
            <tr>
                <td>{{ $service->name }}</td>
                <td>{{ $service->description }}</td>
                <td>R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                <td>{{ $service->status ? 'Ativo' : 'Inativo' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
