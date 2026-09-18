<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;

#[Group('Clientes', 'Cadastro, edição, listagem e visualização de clientes. Todas as rotas exigem Bearer token.')]
class CustomerController extends Controller
{
    /**
     * Colunas permitidas para ordenação via query string — allowlist para
     * evitar SQL injection por nome de coluna arbitrário vindo do client.
     */
    private const SORTABLE_COLUMNS = ['id', 'name', 'status', 'created_at'];

    /**
     * Listar clientes
     *
     * Listagem paginada no backend, com filtro por status e busca textual
     * (nome, documento ou e-mail).
     */
    #[QueryParam('status', 'string', 'Filtra por status.', required: false, example: 'active', enum: ['active', 'inactive'])]
    #[QueryParam('search', 'string', 'Busca por nome, documento ou e-mail (contém o termo).', required: false, example: 'Maria')]
    #[QueryParam('sort_by', 'string', 'Coluna de ordenação.', required: false, example: 'name', enum: ['id', 'name', 'status', 'created_at'])]
    #[QueryParam('sort_direction', 'string', required: false, example: 'asc', enum: ['asc', 'desc'])]
    #[QueryParam('page', 'integer', required: false, example: 1)]
    #[QueryParam('per_page', 'integer', 'Itens por página (máximo 100).', required: false, example: 15)]
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Customer::query();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('search')) {
            // LIKE '%termo%' com wildcard à esquerda não consegue usar um
            // índice B-tree (não há prefixo para restringir a busca), então
            // isso é um full scan da tabela. Aceitável para o volume atual;
            // a evolução natural em produção seria um índice fulltext (ex:
            // MySQL FULLTEXT) ou uma ferramenta de busca dedicada (ex:
            // Meilisearch/Elasticsearch via Laravel Scout).
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sortColumn = in_array($request->query('sort_by'), self::SORTABLE_COLUMNS, true)
            ? $request->query('sort_by')
            : 'created_at';

        $sortDirection = $request->query('sort_direction') === 'asc' ? 'asc' : 'desc';

        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

        // paginate() resolve LIMIT/OFFSET no banco — nunca carregamos a tabela
        // inteira para depois fatiar/ordenar em memória.
        $customers = $query
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return CustomerResource::collection($customers);
    }

    /**
     * Visualizar cliente
     */
    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer);
    }

    /**
     * Cadastrar cliente
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Editar cliente
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerResource
    {
        $customer->update($request->validated());

        return new CustomerResource($customer);
    }
}
