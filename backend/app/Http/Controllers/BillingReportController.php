<?php

namespace App\Http\Controllers;

use App\Http\Resources\BillingResource;
use App\Models\Billing;
use App\Services\BillingReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;

#[Group('Relatórios', 'Relatório de faturamento por período, com totalizadores e exportação em CSV/PDF. Todas as rotas exigem Bearer token.')]
class BillingReportController extends Controller
{
    public function __construct(private readonly BillingReportService $reportService) {}

    /**
     * Relatório de faturamento
     *
     * Listagem paginada de cobranças com totalizadores sobre o conjunto
     * completo filtrado (não só a página atual). `date_from`/`date_to` só
     * são aplicados quando os dois vierem juntos e forem datas válidas —
     * um deles sozinho, ou inválido, é tratado como "sem filtro de período"
     * (não gera erro 422).
     */
    #[QueryParam('date_from', 'string', 'Início do período (formato YYYY-MM-DD). Precisa vir junto com date_to.', required: false, example: '2026-01-01')]
    #[QueryParam('date_to', 'string', 'Fim do período (formato YYYY-MM-DD). Precisa vir junto com date_from.', required: false, example: '2026-06-30')]
    #[QueryParam(
        'date_field',
        'string',
        "Qual data usar para o filtro de período acima. `issue_date` = data de emissão da cobrança; `due_date` = data de vencimento (padrão); `payment_date` = data em que o pagamento foi registrado (só existe para cobranças pagas — filtrar por payment_date exclui automaticamente as demais).",
        required: false,
        example: 'due_date',
        enum: ['issue_date', 'due_date', 'payment_date'],
    )]
    #[QueryParam('customer_id', 'integer', 'Filtra pelas cobranças de um cliente.', required: false, example: 1)]
    #[QueryParam('status', 'string', required: false, example: 'overdue', enum: ['pending', 'overdue', 'paid', 'cancelled'])]
    #[QueryParam('sort_by', 'string', 'Ordenação da listagem paginada (não afeta os totalizadores, que somam sempre o conjunto completo).', required: false, example: 'due_date', enum: ['due_date', 'issue_date', 'status', 'created_at'])]
    #[QueryParam('sort_direction', 'string', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam('page', 'integer', required: false, example: 1)]
    #[QueryParam('per_page', 'integer', 'Itens por página (máximo 100).', required: false, example: 15)]
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
