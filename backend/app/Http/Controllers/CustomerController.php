<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    /**
     * Colunas permitidas para ordenação via query string — allowlist para
     * evitar SQL injection por nome de coluna arbitrário vindo do client.
     */
    private const SORTABLE_COLUMNS = ['id', 'name', 'status', 'created_at'];

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

    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerResource
    {
        $customer->update($request->validated());

        return new CustomerResource($customer);
    }
}
