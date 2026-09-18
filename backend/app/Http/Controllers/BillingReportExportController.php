<?php

namespace App\Http\Controllers;

use App\Services\BillingReportExportService;
use App\Services\BillingReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Group('Relatórios', 'Relatório de faturamento por período, com totalizadores e exportação em CSV/PDF. Todas as rotas exigem Bearer token.')]
class BillingReportExportController extends Controller
{
    public function __construct(
        private readonly BillingReportService $reportService,
        private readonly BillingReportExportService $exportService,
    ) {}

    /**
     * Exportar relatório em CSV
     *
     * Mesmos filtros do endpoint de listagem (`date_from`, `date_to`,
     * `date_field`, `customer_id`, `status`) aplicados ao conjunto completo,
     * não só a uma página — `sort_by`/`sort_direction`/`page` não se aplicam
     * aqui e são ignorados se enviados. Streaming: retorna o arquivo direto,
     * não JSON (`Content-Disposition: attachment`).
     */
    #[QueryParam('date_from', 'string', 'Precisa vir junto com date_to.', required: false, example: '2026-01-01')]
    #[QueryParam('date_to', 'string', 'Precisa vir junto com date_from.', required: false, example: '2026-06-30')]
    #[QueryParam('date_field', 'string', required: false, example: 'due_date', enum: ['issue_date', 'due_date', 'payment_date'])]
    #[QueryParam('customer_id', 'integer', required: false, example: 1)]
    #[QueryParam('status', 'string', required: false, example: 'overdue', enum: ['pending', 'overdue', 'paid', 'cancelled'])]
    public function csv(Request $request): StreamedResponse
    {
        $filters = $this->reportService->parseFilters($request);

        return response()->streamDownload(
            function () use ($filters) {
                $handle = fopen('php://output', 'w');

                $this->exportService->writeCsv($handle, $filters);

                fclose($handle);
            },
            $this->exportService->csvFilename($filters),
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    /**
     * Exportar relatório em PDF
     *
     * Mesmos filtros da exportação em CSV. Antes de gerar, conta quantos
     * registros o filtro retorna (sem carregá-los); se exceder o limite
     * configurado (`config('reports.pdf_row_limit')`, default 500 — ver
     * README para a justificativa medida), retorna 422 sugerindo usar CSV.
     */
    #[QueryParam('date_from', 'string', 'Precisa vir junto com date_to.', required: false, example: '2026-01-01')]
    #[QueryParam('date_to', 'string', 'Precisa vir junto com date_from.', required: false, example: '2026-06-30')]
    #[QueryParam('date_field', 'string', required: false, example: 'due_date', enum: ['issue_date', 'due_date', 'payment_date'])]
    #[QueryParam('customer_id', 'integer', required: false, example: 1)]
    #[QueryParam('status', 'string', required: false, example: 'overdue', enum: ['pending', 'overdue', 'paid', 'cancelled'])]
    public function pdf(Request $request): JsonResponse|Response
    {
        $filters = $this->reportService->parseFilters($request);

        $count = $this->exportService->countForPdf($filters);
        $limit = $this->exportService->pdfRowLimit();

        // Contagem antes de carregar qualquer registro — nunca buscamos as
        // linhas só para descobrir se são muitas.
        if ($count > $limit) {
            return response()->json([
                'message' => "Este filtro retorna {$count} registros, acima do limite de {$limit} para exportação em PDF. Utilize a exportação em CSV para volumes grandes.",
            ], 422);
        }

        $data = $this->exportService->pdfData($filters);

        // dompdf é pesado de memória para tabelas grandes — medido neste
        // projeto, 500 linhas (o limite configurado) chegam a ~223MB. Eleva
        // o limite só para esta requisição pontual e bounded (nunca escala
        // com tráfego normal, é sempre <= pdf_row_limit linhas) — não altera
        // o memory_limit global do processo/outras requisições.
        ini_set('memory_limit', '512M');

        $pdf = Pdf::loadView('reports.billing-pdf', $data)->setPaper('a4', 'landscape');

        return $pdf->download($this->pdfFilename($filters));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function pdfFilename(array $filters): string
    {
        return str_replace('.csv', '.pdf', $this->exportService->csvFilename($filters));
    }
}
