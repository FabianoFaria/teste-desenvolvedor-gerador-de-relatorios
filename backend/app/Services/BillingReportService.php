<?php

namespace App\Services;

use App\Models\Billing;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Fonte única dos dados do Relatório de Faturamento — usada tanto pela tela
 * (listagem paginada + totalizadores) quanto, futuramente, pela exportação
 * CSV/PDF (via filteredQuery()), para garantir que a tela e o arquivo
 * exportado nunca divirjam.
 *
 * ## Decisão técnica: como o total de juros é calculado
 *
 * O total de juros do conjunto filtrado completo é o ponto mais delicado de
 * performance deste relatório, porque juros NÃO é uma coluna persistida —
 * para uma cobrança vencida e não paga ele depende de "hoje", calculado sob
 * demanda pelo InterestCalculatorService (fórmula de juros compostos).
 *
 * Três estratégias foram avaliadas:
 *
 * 1. Somar em PHP linha a linha, streamando o resultado completo do filtro
 *    via chunk()/cursor() e chamando InterestCalculatorService::calculate()
 *    para cada uma. Vantagem: 100% idêntico ao valor exibido por linha (é
 *    literalmente a mesma chamada). Desvantagem: para um filtro amplo (sem
 *    período, ex: "todas as cobranças"), isso lê do banco toda a tabela que
 *    bate o filtro — com centenas de milhares/milhões de linhas, mesmo em
 *    chunks (memória limitada), o tempo de resposta escala linearmente com
 *    o volume e pode passar de segundos para dezenas de segundos. Ruim para
 *    uma tela de relatório que o usuário espera carregar rápido.
 *
 * 2. Expressão SQL replicando a fórmula composta, agregada com SUM() em uma
 *    única query. Vantagem: o banco resolve tudo internamente, sem round-trip
 *    de milhares/milhões de linhas para o PHP — o tempo de resposta deixa de
 *    ser proporcional ao volume de linhas e passa a depender só da
 *    seletividade dos índices already existentes (status, due_date/issue_date/
 *    payment_date). Desvantagem: é uma SEGUNDA implementação da fórmula (em
 *    SQL), correndo o risco de divergir do InterestCalculatorService se a
 *    regra de negócio mudar e alguém esquecer de atualizar os dois lugares.
 *
 * 3. Pré-computar/cachear o total periodicamente (ex: coluna materializada,
 *    job agendado). Rejeitada para este projeto: o requisito é "juros em
 *    tempo real, nunca persistido como verdade", então qualquer valor
 *    cacheado ficaria desatualizado a cada dia que passa (dias_em_atraso
 *    muda todo dia, para toda cobrança vencida, mesmo sem nenhuma escrita).
 *
 * ESCOLHIDA: estratégia 2 (SQL agregado), porque este projeto prioriza
 * explicitamente performance em escala (README pede "tabelas com milhões de
 * registros") sobre uma tela de relatório específica. O risco de divergência
 * da estratégia 2 é mitigado assim:
 *   - A fórmula em SQL replica exatamente a sequência de arredondamento do
 *     PHP (round(valor_atualizado, 2) primeiro, juros = atualizado - original
 *     depois — nunca arredondando os dois lados independentemente), então os
 *     dois caminhos devem bater ao centavo na esmagadora maioria dos casos;
 *     só um empate exatíssimo em ponto flutuante na borda de um
 *     arredondamento poderia teoricamente divergir em 1 centavo numa linha
 *     isolada — praticamente nunca observável.
 *   - Toda a duplicação fica isolada em UM único método privado desta classe
 *     (liveInterestSql()) em vez de espalhada — se a fórmula mudar, é um
 *     lugar a mais para atualizar, não vários.
 *   - Testado (BillingReportControllerTest::test_totals_match_manual_...)
 *     comparando o total retornado pela API com um cálculo manual esperado.
 *
 * Se no futuro a exatidão ao centavo contra auditoria for um requisito
 * inegociável (não só "praticamente sempre bate"), a estratégia 1 deve
 * substituir esta, aceitando o custo de performance em filtros muito amplos.
 *
 * ## Cobranças 'cancelled' não entram em pending_total
 *
 * "Pendente" (pending_total) é definido como status NOT IN ('paid',
 * 'cancelled') — não apenas "!= 'paid'". Uma cobrança cancelada não é uma
 * cobrança em aberto esperando pagamento; contá-la em "valor pendente"
 * infla o totalizador com um valor que ninguém está de fato cobrando. Isso
 * mantém consistência com o InterestCalculatorService, que já trata
 * 'cancelled' igual a 'paid' no early-return de flatResult() (zero juros,
 * valor atualizado = original) — uma cobrança cancelada não acumula juros
 * nem entra em interest_total/updated_amount_total por essa mesma razão.
 *
 * ## O que "total de juros" e "valor atualizado total" significam aqui
 *
 * Para cobranças pagas, o juro que entra no total é o valor HISTÓRICO
 * realmente cobrado (interest_amount_at_payment), não o resultado de rodar
 * InterestCalculatorService::calculate() hoje (que devolveria 0, já que uma
 * cobrança paga não acumula mais juros — ver flatResult() do service). Isso é
 * intencional: um relatório financeiro quer saber quanto juros essa
 * população de cobranças gerou de fato (realizado nas pagas + projetado nas
 * vencidas em aberto), não fingir que uma cobrança já quitada nunca teve
 * juros. Por isso "valor atualizado total" também não é a soma literal do
 * campo `updated_amount` de cada linha exibida (que é 0/original para pagas)
 * — é original_amount_total + interest_total, com essa definição de juros.
 */
class BillingReportService
{
    /**
     * Mesma allowlist de ordenação já usada em Customer/Billing — evita SQL
     * injection por nome de coluna arbitrário vindo do client.
     */
    private const SORTABLE_COLUMNS = ['due_date', 'issue_date', 'status', 'created_at'];

    private const DATE_FIELDS = ['issue_date', 'due_date', 'payment_date'];

    /**
     * @return array{
     *     date_from: string|null,
     *     date_to: string|null,
     *     date_field: string,
     *     customer_id: int|null,
     *     status: string|null,
     * }
     */
    public function parseFilters(Request $request): array
    {
        $dateField = $request->query('date_field');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // date_from/date_to são "obrigatórios juntos": só aplicamos o filtro
        // de período se os dois vierem e forem datas válidas. Um dos dois
        // sozinho, ou um valor inválido, é tratado como "sem filtro de
        // período" (silenciosamente, mesmo padrão de sort_by/status
        // inválidos já usado em Customer/Billing — não é um 422).
        $hasValidRange = $this->isValidDate($dateFrom) && $this->isValidDate($dateTo);

        return [
            'date_from' => $hasValidRange ? $dateFrom : null,
            'date_to' => $hasValidRange ? $dateTo : null,
            'date_field' => in_array($dateField, self::DATE_FIELDS, true) ? $dateField : 'due_date',
            'customer_id' => $request->query('customer_id') ? (int) $request->query('customer_id') : null,
            'status' => $request->query('status') ?: null,
        ];
    }

    /**
     * Query filtrada, já com o cliente eager-loaded — base compartilhada
     * pela listagem paginada e (futuramente) pela exportação CSV/PDF.
     *
     * @param  array<string, mixed>  $filters  Ver parseFilters().
     */
    public function filteredQuery(array $filters): Builder
    {
        $query = Billing::query()->with('customer');

        if ($filters['customer_id']) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['date_from'] && $filters['date_to']) {
            $query->whereBetween($filters['date_field'], [$filters['date_from'], $filters['date_to']]);
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(
        array $filters,
        ?string $sortBy,
        ?string $sortDirection,
        int $perPage
    ): LengthAwarePaginator {
        $sortColumn = in_array($sortBy, self::SORTABLE_COLUMNS, true) ? $sortBy : 'due_date';
        $direction = $sortDirection === 'asc' ? 'asc' : 'desc';

        return $this->filteredQuery($filters)
            ->orderBy($sortColumn, $direction)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Totalizadores sobre o CONJUNTO COMPLETO filtrado — nunca só a página
     * atual — resolvidos em uma única query de agregação no banco (ver
     * decisão técnica no topo da classe). Nenhum registro é carregado para
     * o PHP para ser somado em loop.
     *
     * @param  array<string, mixed>  $filters
     * @return array{
     *     count: int,
     *     original_amount_total: float,
     *     interest_total: float,
     *     updated_amount_total: float,
     *     paid_total: float,
     *     pending_total: float,
     * }
     */
    public function totals(array $filters): array
    {
        // now() e não a data de referência de cada linha: mesmo referencial
        // usado pelo InterestCalculatorService quando chamado sem um
        // segundo argumento (calculate($billing) sozinho).
        $today = Carbon::now()->toDateString();

        $liveInterest = $this->liveInterestSql($today);
        $liveUpdatedAmount = "(original_amount + {$liveInterest})";

        $row = $this->filteredQuery($filters)
            ->toBase() // agregação pura — não precisa hidratar Billing nem a relação customer.
            ->selectRaw('COUNT(*) as aggregate_count')
            ->selectRaw('COALESCE(SUM(original_amount), 0) as original_amount_total')
            ->selectRaw("COALESCE(SUM(
                CASE
                    WHEN status = 'paid' THEN COALESCE(interest_amount_at_payment, 0)
                    WHEN status = 'cancelled' THEN 0
                    WHEN due_date >= '{$today}' THEN 0
                    ELSE {$liveInterest}
                END
            ), 0) as interest_total")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'paid' THEN paid_amount ELSE 0 END), 0) as paid_total")
            // "Pendente" = NOT IN ('paid', 'cancelled'), não só "!= paid" —
            // ver docblock da classe ("Cobranças 'cancelled' não entram em
            // pending_total").
            ->selectRaw("COALESCE(SUM(
                CASE
                    WHEN status = 'paid' THEN 0
                    WHEN status = 'cancelled' THEN 0
                    WHEN due_date >= '{$today}' THEN original_amount
                    ELSE {$liveUpdatedAmount}
                END
            ), 0) as pending_total")
            ->first();

        $originalAmountTotal = round((float) $row->original_amount_total, 2);
        $interestTotal = round((float) $row->interest_total, 2);

        return [
            'count' => (int) $row->aggregate_count,
            'original_amount_total' => $originalAmountTotal,
            'interest_total' => $interestTotal,
            // Identidade original + juros — não uma SUM(updated_amount) à
            // parte, para garantir que os três números sempre batem entre si.
            'updated_amount_total' => round($originalAmountTotal + $interestTotal, 2),
            'paid_total' => round((float) $row->paid_total, 2),
            'pending_total' => round((float) $row->pending_total, 2),
        ];
    }

    /**
     * Expressão SQL do juro composto vivo (valor_atualizado - original) para
     * uma cobrança vencida e não paga — mesma fórmula do
     * InterestCalculatorService, ver decisão técnica no topo da classe.
     * $today já validado (Carbon::toDateString()), nunca vem de input do
     * usuário — seguro interpolar diretamente.
     */
    private function liveInterestSql(string $today): string
    {
        $daysOverdue = $this->daysOverdueSql($today);

        return "ROUND(original_amount * POWER(1 + monthly_interest_rate / 100, ({$daysOverdue}) / 30.0), 2) - original_amount";
    }

    /**
     * DATEDIFF() não existe no SQLite (usado pela suíte de testes) — só no
     * MySQL/MariaDB (produção). julianday() é o equivalente portável no
     * SQLite. Isolado aqui como o único lugar deste service que depende do
     * driver do banco.
     */
    private function daysOverdueSql(string $today): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(julianday('{$today}') - julianday(due_date) AS INTEGER)",
            default => "DATEDIFF('{$today}', due_date)",
        };
    }

    private function isValidDate(?string $value): bool
    {
        if (! $value) {
            return false;
        }

        try {
            Carbon::parse($value);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
