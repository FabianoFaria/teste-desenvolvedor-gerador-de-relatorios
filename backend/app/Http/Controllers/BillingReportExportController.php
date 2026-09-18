<?php

namespace App\Http\Controllers;

use App\Services\BillingReportExportService;
use App\Services\BillingReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BillingReportExportController extends Controller
{
    public function __construct(
        private readonly BillingReportService $reportService,
        private readonly BillingReportExportService $exportService,
    ) {}

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
