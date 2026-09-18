<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Monta os dados de exportação (CSV/PDF) do relatório de faturamento a
 * partir de BillingReportService::filteredQuery() e ::totals() — nunca
 * reimplementa filtro ou totalizador aqui, só formata o que já existe. Isso
 * garante que a tela, o CSV e o PDF sempre mostram exatamente os mesmos
 * números para o mesmo filtro.
 */
class BillingReportExportService
{
    private const CSV_COLUMNS = [
        'Cliente', 'Descrição', 'Emissão', 'Vencimento', 'Pagamento',
        'Status', 'Valor Original', 'Juros', 'Valor Atualizado', 'Valor Pago',
    ];

    private const STATUS_LABELS = [
        'pending' => 'Pendente',
        'overdue' => 'Vencida',
        'paid' => 'Paga',
        'cancelled' => 'Cancelada',
    ];

    private const DATE_FIELD_LABELS = [
        'issue_date' => 'Emissão',
        'due_date' => 'Vencimento',
        'payment_date' => 'Pagamento',
    ];

    public function __construct(
        private readonly BillingReportService $reportService,
        private readonly InterestCalculatorService $interestCalculator,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function csvFilename(array $filters): string
    {
        if ($filters['date_from'] && $filters['date_to']) {
            return "relatorio-faturamento_{$filters['date_from']}_a_{$filters['date_to']}.csv";
        }

        return 'relatorio-faturamento_'.Carbon::now()->format('Y-m-d').'.csv';
    }

    /**
     * Escreve o CSV completo (metadata + tabela + totalizadores) diretamente
     * no handle de saída fornecido, streamando a query filtrada em chunks —
     * nunca monta um array com todas as linhas em memória antes de escrever.
     *
     * @param  array<string, mixed>  $filters
     * @param  resource  $handle  Normalmente fopen('php://output', 'w').
     */
    public function writeCsv($handle, array $filters): void
    {
        // BOM UTF-8: sem isso o Excel abre acentuação (ex: "Descrição")
        // corrompida.
        fwrite($handle, "\xEF\xBB\xBF");

        foreach ($this->filterSummaryLines($filters) as $line) {
            $this->writeCsvRow($handle, [$line]);
        }

        $this->writeCsvRow($handle, []);
        $this->writeCsvRow($handle, self::CSV_COLUMNS);

        $rowsWritten = 0;

        $this->reportService->filteredQuery($filters)
            ->lazyById(1000)
            ->each(function (Billing $billing) use ($handle, &$rowsWritten) {
                $this->writeCsvRow($handle, $this->csvRow($billing));

                // Sem isso, o buffer de saída do PHP/servidor pode acumular
                // internamente mesmo escrevendo linha a linha — flush
                // periódico garante memória de fato constante num export
                // de centenas de milhares de linhas, não só "sem array
                // grande em PHP".
                if (++$rowsWritten % 5000 === 0) {
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            });

        $totals = $this->reportService->totals($filters);

        $this->writeCsvRow($handle, []);
        $this->writeCsvRow($handle, ['Totalizadores']);
        $this->writeCsvRow($handle, ['Quantidade de cobranças', $totals['count']]);
        $this->writeCsvRow($handle, ['Valor original total', $this->money($totals['original_amount_total'])]);
        $this->writeCsvRow($handle, ['Total de juros', $this->money($totals['interest_total'])]);
        $this->writeCsvRow($handle, ['Valor atualizado total', $this->money($totals['updated_amount_total'])]);
        $this->writeCsvRow($handle, ['Valor total recebido', $this->money($totals['paid_total'])]);
        $this->writeCsvRow($handle, ['Valor total pendente', $this->money($totals['pending_total'])]);
    }

    /**
     * Wrapper fino sobre fputcsv() passando $escape explicitamente — a
     * partir do PHP 8.4 omitir esse parâmetro emite um deprecation warning
     * POR CHAMADA, o que é significativo quando são centenas de milhares de
     * linhas (chegou a inflar visivelmente o tempo de um export real neste
     * projeto).
     *
     * @param  resource  $handle
     * @param  array<int, mixed>  $fields
     */
    private function writeCsvRow($handle, array $fields): void
    {
        fputcsv($handle, $fields, ',', '"', '\\');
    }

    /**
     * Quantidade de registros que o filtro retorna — só COUNT(*), nunca
     * carrega os registros. Usado para decidir se a exportação em PDF pode
     * prosseguir (ver config('reports.pdf_row_limit')).
     *
     * @param  array<string, mixed>  $filters
     */
    public function countForPdf(array $filters): int
    {
        return $this->reportService->filteredQuery($filters)->toBase()->count();
    }

    public function pdfRowLimit(): int
    {
        return (int) config('reports.pdf_row_limit', 5000);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{
     *     filters: array<int, string>,
     *     columns: array<int, string>,
     *     rows: Collection<int, array<int, string>>,
     *     totals: array<string, mixed>,
     *     generated_at: string,
     * }
     */
    public function pdfData(array $filters): array
    {
        // Só chamado depois de countForPdf() confirmar que está dentro do
        // limite — carregar os registros aqui é seguro (volume já limitado
        // pela config, não "todos os registros" sem bound nenhum).
        $billings = $this->reportService->filteredQuery($filters)->orderBy('id')->get();

        return [
            'filters' => $this->filterSummaryLines($filters),
            'columns' => self::CSV_COLUMNS,
            'rows' => $billings->map(fn (Billing $billing) => $this->csvRow($billing)),
            'totals' => $this->reportService->totals($filters),
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function csvRow(Billing $billing): array
    {
        $calculation = $this->interestCalculator->calculate($billing);

        return [
            $billing->customer?->name ?? '—',
            $billing->description,
            $billing->issue_date->toDateString(),
            $billing->due_date->toDateString(),
            $billing->payment_date?->toDateString() ?? '',
            self::STATUS_LABELS[$billing->status] ?? $billing->status,
            $this->money((float) $billing->original_amount),
            $this->money($calculation['interest_amount']),
            $this->money($calculation['updated_amount']),
            $billing->paid_amount !== null ? $this->money((float) $billing->paid_amount) : '',
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, string>
     */
    private function filterSummaryLines(array $filters): array
    {
        $lines = ['Relatório de Faturamento'];

        if ($filters['date_from'] && $filters['date_to']) {
            $dateFieldLabel = self::DATE_FIELD_LABELS[$filters['date_field']] ?? $filters['date_field'];
            $lines[] = "Período ({$dateFieldLabel}): {$filters['date_from']} a {$filters['date_to']}";
        } else {
            $lines[] = 'Período: todos os registros (sem filtro de período)';
        }

        $lines[] = 'Cliente: '.$this->customerLabel($filters['customer_id']);
        $lines[] = 'Status: '.($filters['status'] ? (self::STATUS_LABELS[$filters['status']] ?? $filters['status']) : 'Todos');
        $lines[] = 'Gerado em: '.Carbon::now()->format('d/m/Y H:i');

        return $lines;
    }

    private function customerLabel(?int $customerId): string
    {
        if (! $customerId) {
            return 'Todos';
        }

        $customer = Customer::find($customerId);

        return $customer ? $customer->name : "Cliente #{$customerId} (não encontrado)";
    }

    private function money(float $value): string
    {
        return number_format($value, 2, ',', '.');
    }
}
