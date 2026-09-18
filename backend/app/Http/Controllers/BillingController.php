<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayBillingRequest;
use App\Http\Requests\StoreBillingRequest;
use App\Http\Requests\UpdateBillingRequest;
use App\Http\Resources\BillingResource;
use App\Models\Billing;
use App\Services\InterestCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;

#[Group('Cobranças', 'Cadastro, edição, listagem, visualização e registro de pagamento de cobranças. Todas as rotas exigem Bearer token.')]
class BillingController extends Controller
{
    /**
     * Colunas permitidas para ordenação via query string — allowlist para
     * evitar SQL injection por nome de coluna arbitrário vindo do client.
     */
    private const SORTABLE_COLUMNS = ['due_date', 'issue_date', 'status', 'created_at'];

    public function __construct(private readonly InterestCalculatorService $interestCalculator) {}

    /**
     * Listar cobranças
     *
     * Listagem paginada no backend, com filtro por cliente e status. Cada
     * item já inclui os campos calculados de juros/valor atualizado (ver
     * grupo Relatórios para a mesma lógica aplicada ao relatório completo).
     */
    #[QueryParam('customer_id', 'integer', 'Filtra pelas cobranças de um cliente.', required: false, example: 1)]
    #[QueryParam('status', 'string', required: false, example: 'overdue', enum: ['pending', 'overdue', 'paid', 'cancelled'])]
    #[QueryParam('sort_by', 'string', required: false, example: 'due_date', enum: ['due_date', 'issue_date', 'status', 'created_at'])]
    #[QueryParam('sort_direction', 'string', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam('page', 'integer', required: false, example: 1)]
    #[QueryParam('per_page', 'integer', 'Itens por página (máximo 100).', required: false, example: 15)]
    public function index(Request $request): AnonymousResourceCollection
    {
        // Eager load explícito — nunca lazy loading implícito, que dispararia
        // uma query por cobrança (N+1) ao acessar $billing->customer no
        // BillingResource para cada item da página.
        $query = Billing::query()->with('customer');

        if ($customerId = $request->query('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $sortColumn = in_array($request->query('sort_by'), self::SORTABLE_COLUMNS, true)
            ? $request->query('sort_by')
            : 'created_at';

        $sortDirection = $request->query('sort_direction') === 'asc' ? 'asc' : 'desc';

        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

        // paginate() resolve LIMIT/OFFSET no banco — nunca carregamos a tabela
        // inteira para depois fatiar/ordenar em memória.
        $billings = $query
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return BillingResource::collection($billings);
    }

    /**
     * Visualizar cobrança
     *
     * Inclui os campos calculados de juros/valor atualizado em tempo real
     * (nunca lidos de coluna persistida).
     */
    public function show(Billing $billing): BillingResource
    {
        // Registro único: sem risco de N+1, mas ainda um load() explícito
        // (não uma leitura de $billing->customer não carregada).
        $billing->load('customer');

        return new BillingResource($billing);
    }

    /**
     * Cadastrar cobrança
     *
     * Toda cobrança nova começa com status `pending` — não é possível criar
     * já como `paid`/`overdue`/`cancelled` via este endpoint.
     */
    public function store(StoreBillingRequest $request): JsonResponse
    {
        // 'status' não é um campo aceito do client (nem existe na validação
        // do Store) — toda cobrança nova começa 'pending'. Setado
        // explicitamente aqui, e não só via default de coluna no banco,
        // porque o model recém-criado em memória não reflete um default do
        // schema sem um refresh() — a instância retornada pela resposta
        // ficaria com status nulo até ser relida do banco.
        $billing = Billing::create([
            ...$request->validated(),
            'status' => 'pending',
        ]);

        return (new BillingResource($billing))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Editar cobrança
     *
     * Retorna 422 se a cobrança já estiver paga — uma cobrança paga não
     * pode ser editada.
     */
    public function update(UpdateBillingRequest $request, Billing $billing): BillingResource
    {
        $billing->update($request->validated());

        return new BillingResource($billing);
    }

    /**
     * Registrar pagamento
     *
     * Não aceita nenhum campo no corpo da requisição: a data de pagamento é
     * sempre `now()` e o valor pago é sempre o valor atualizado calculado no
     * momento do registro — nunca dados enviados pelo client. Retorna 422 se
     * a cobrança já estiver paga ou cancelada.
     */
    public function pay(PayBillingRequest $request, Billing $billing): BillingResource
    {
        // Momento único reaproveitado no cálculo e na persistência: a data de
        // pagamento nunca vem do client, é sempre now() no momento do registro.
        $paymentMoment = now();

        $calculation = $this->interestCalculator->calculate($billing, $paymentMoment);

        $billing->update([
            'payment_date' => $paymentMoment,
            'paid_amount' => $calculation['updated_amount'],
            'interest_amount_at_payment' => $calculation['interest_amount'],
            'status' => 'paid',
        ]);

        return new BillingResource($billing);
    }
}
