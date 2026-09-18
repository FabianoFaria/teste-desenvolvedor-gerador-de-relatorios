<?php

namespace App\Http\Controllers;

use App\Http\Resources\BillingResource;
use App\Models\Billing;
use App\Services\BillingReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingReportController extends Controller
{
    public function __construct(private readonly BillingReportService $reportService) {}

    public function billing(Request $request): JsonResponse
    {
        $filters = $this->reportService->parseFilters($request);
        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

        $paginator = $this->reportService->paginate(
            $filters,
            $request->query('sort_by'),
            $request->query('sort_direction'),
            $perPage
        );

        // BillingResource calcula juros/valor atualizado por linha (via
        // InterestCalculatorService) — aqui sobre a página já paginada, nunca
        // sobre o resultado completo do filtro.
        $data = $paginator->getCollection()
            ->map(fn (Billing $billing) => (new BillingResource($billing))->resolve())
            ->all();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            // Totalizadores sobre o conjunto completo filtrado — ver
            // BillingReportService::totals() para a decisão de performance.
            'totals' => $this->reportService->totals($filters),
            'filters_applied' => $filters,
        ]);
    }
}
