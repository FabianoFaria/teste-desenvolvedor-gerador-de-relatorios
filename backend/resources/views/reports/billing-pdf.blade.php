<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de Faturamento</title>
    <style>
        /* dompdf só suporta um subconjunto de CSS — evitar flexbox/grid. */
        body { font-family: sans-serif; font-size: 11px; color: #1a1a1a; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .filters { margin-bottom: 12px; }
        .filters p { margin: 1px 0; color: #444; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.data th, table.data td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: left;
        }
        table.data th { background-color: #f0f0f0; }
        table.data td.numeric, table.data th.numeric { text-align: right; }
        table.totals { width: 50%; border-collapse: collapse; }
        table.totals td { padding: 3px 6px; border: 1px solid #ccc; }
        table.totals td.label { font-weight: bold; background-color: #f0f0f0; }
        table.totals td.value { text-align: right; }
    </style>
</head>
<body>
    <h1>Relatório de Faturamento</h1>

    <div class="filters">
        @foreach (array_slice($filters, 1) as $line)
            <p>{{ $line }}</p>
        @endforeach
    </div>

    <table class="data">
        <thead>
            <tr>
                @foreach ($columns as $index => $column)
                    <th @class(['numeric' => $index >= 6])>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $index => $cell)
                        <td @class(['numeric' => $index >= 6])>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}">Nenhuma cobrança encontrada para este filtro.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Quantidade de cobranças</td>
            <td class="value">{{ $totals['count'] }}</td>
        </tr>
        <tr>
            <td class="label">Valor original total</td>
            <td class="value">{{ number_format($totals['original_amount_total'], 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total de juros</td>
            <td class="value">{{ number_format($totals['interest_total'], 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Valor atualizado total</td>
            <td class="value">{{ number_format($totals['updated_amount_total'], 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Valor total recebido</td>
            <td class="value">{{ number_format($totals['paid_total'], 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Valor total pendente</td>
            <td class="value">{{ number_format($totals['pending_total'], 2, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
