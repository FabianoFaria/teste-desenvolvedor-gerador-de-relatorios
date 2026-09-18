<?php

namespace Database\Seeders;

use App\Services\InterestCalculatorService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Gera um volume grande e realista de clientes e cobranças para testar
 * performance com dados em escala (o README do projeto pede demonstrar que
 * o sistema funciona bem com tabelas de milhões de registros).
 *
 * NÃO é chamado pelo DatabaseSeeder padrão — é uma ação explícita, disparada
 * via `php artisan db:seed:volume` (ver App\Console\Commands\SeedVolumeCommand),
 * justamente para não travar o `migrate:fresh --seed` do dia a dia com
 * centenas de milhares de registros desnecessários.
 *
 * Decisões de performance (o ponto central deste seeder):
 * - Nunca Model::factory()->create() em loop — cada chamada seria um INSERT
 *   próprio. Tudo aqui é array puro + DB::table()->insert() em chunks.
 * - Nenhum Eloquent model é instanciado para as linhas em massa, então não
 *   há casts/mutators/events de model no caminho quente — só o array já no
 *   formato de colunas do banco.
 * - DB::disableQueryLog() antes de tudo: o log de query do Laravel guarda
 *   cada query executada em memória: com centenas de milhares de INSERTs
 *   isso estouraria memória rapidinho.
 * - Cada chunk (1000 linhas) é sua própria transação — não uma transação
 *   gigante para o volume inteiro (evita travar o MySQL com uma transação
 *   longa e evita estourar memória de rollback se algo falhar no meio).
 */
class VolumeSeeder extends Seeder
{
    private const CUSTOMER_COUNT = 1000;

    private const CHUNK_SIZE = 1000;

    private const DESCRIPTIONS = [
        'Mensalidade de serviço',
        'Fatura de consultoria',
        'Cobrança de assinatura mensal',
        'Prestação de serviços administrativos',
        'Locação de equipamento',
        'Honorários contábeis',
    ];

    public function run(int $billingCount = 50000): void
    {
        DB::disableQueryLog();

        $this->command?->info('Gerando '.self::CUSTOMER_COUNT.' clientes...');
        $customerIds = $this->seedCustomers();

        $this->command?->info("Gerando {$billingCount} cobranças entre ".count($customerIds).' clientes...');
        $this->seedBillings($billingCount, $customerIds);
    }

    /**
     * @return list<int>
     */
    private function seedCustomers(): array
    {
        $rows = [];
        $now = now()->format('Y-m-d H:i:s');

        for ($i = 0; $i < self::CUSTOMER_COUNT; $i++) {
            $rows[] = [
                'name' => fake('pt_BR')->name(),
                'document' => fake('pt_BR')->boolean(70) ? fake('pt_BR')->cpf() : fake('pt_BR')->cnpj(),
                'email' => fake('pt_BR')->unique()->safeEmail(),
                // Majoritariamente ativos: só ~10% inactive.
                'status' => random_int(1, 100) <= 90 ? 'active' : 'inactive',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, self::CHUNK_SIZE) as $chunk) {
            DB::transaction(fn () => DB::table('customers')->insert($chunk));
        }

        // insert() em lote não retorna os IDs gerados — relê do banco. Pega
        // todos os clientes existentes (não só os recém-criados), para que
        // um customer já presente antes desta rodada também receba cobranças.
        return DB::table('customers')->pluck('id')->all();
    }

    /**
     * @param  list<int>  $customerIds
     */
    private function seedBillings(int $billingCount, array $customerIds): void
    {
        $interestCalculator = new InterestCalculatorService;
        $today = Carbon::now()->startOfDay();
        $now = $today->format('Y-m-d H:i:s');

        $progressBar = $this->command?->getOutput()->createProgressBar($billingCount);
        $progressBar?->start();

        $remaining = $billingCount;

        while ($remaining > 0) {
            $currentChunkSize = min(self::CHUNK_SIZE, $remaining);
            $chunk = [];

            for ($i = 0; $i < $currentChunkSize; $i++) {
                $chunk[] = $this->buildBillingRow($customerIds, $today, $now, $interestCalculator);
            }

            DB::transaction(fn () => DB::table('billings')->insert($chunk));

            $remaining -= $currentChunkSize;
            $progressBar?->advance($currentChunkSize);
        }

        $progressBar?->finish();
        $this->command?->newLine(2);
    }

    /**
     * @param  list<int>  $customerIds
     * @return array<string, mixed>
     */
    private function buildBillingRow(
        array $customerIds,
        Carbon $today,
        string $now,
        InterestCalculatorService $interestCalculator
    ): array {
        $status = $this->randomStatus();
        [$issueDate, $dueDate, $paymentDate] = $this->randomDatesForStatus($status, $today);

        // R$ 50,00 a R$ 5.000,00, em centavos para evitar imprecisão de float.
        $originalAmount = random_int(5000, 500000) / 100;
        // 1,00% a 5,00% ao mês.
        $monthlyInterestRate = random_int(100, 500) / 100;

        $paidAmount = null;
        $interestAmountAtPayment = null;

        if ($status === 'paid') {
            $daysOverdueAtPayment = $paymentDate->greaterThan($dueDate)
                ? (int) abs($paymentDate->diffInDays($dueDate))
                : 0;

            // Mesma fórmula de sempre (InterestCalculatorService) — nunca
            // duplicada aqui, só chamada com os primitivos já calculados.
            $calculation = $interestCalculator->calculateFromValues(
                $originalAmount,
                $monthlyInterestRate,
                $daysOverdueAtPayment
            );

            $paidAmount = $calculation['updated_amount'];
            $interestAmountAtPayment = $calculation['interest_amount'];
        }

        return [
            'customer_id' => $customerIds[array_rand($customerIds)],
            'description' => self::DESCRIPTIONS[array_rand(self::DESCRIPTIONS)],
            'original_amount' => $originalAmount,
            'monthly_interest_rate' => $monthlyInterestRate,
            'issue_date' => $issueDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'payment_date' => $paymentDate?->toDateString(),
            'status' => $status,
            'paid_amount' => $paidAmount,
            'interest_amount_at_payment' => $interestAmountAtPayment,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * ~50% paid, ~30% pending (a vencer), ~20% overdue (vencida e não paga).
     */
    private function randomStatus(): string
    {
        $roll = random_int(1, 100);

        return match (true) {
            $roll <= 50 => 'paid',
            $roll <= 80 => 'pending',
            default => 'overdue',
        };
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: Carbon|null}
     */
    private function randomDatesForStatus(string $status, Carbon $today): array
    {
        return match ($status) {
            'pending' => $this->pendingDates($today),
            'overdue' => $this->overdueDates($today),
            default => $this->paidDates($today),
        };
    }

    /**
     * A vencer: emitida recentemente, vencimento ainda no futuro.
     *
     * @return array{0: Carbon, 1: Carbon, 2: null}
     */
    private function pendingDates(Carbon $today): array
    {
        $dueDate = $today->copy()->addDays(random_int(1, 45));
        $issueDate = $dueDate->copy()->subDays(random_int(15, 60));

        // due_date pode estar só alguns dias no futuro enquanto o prazo
        // (term) é maior que isso — sem esse clamp, issue_date acabaria
        // caindo no futuro também (uma cobrança não pode ser emitida antes
        // de hoje).
        if ($issueDate->greaterThan($today)) {
            $issueDate = $today->copy();
        }

        return [$issueDate, $dueDate, null];
    }

    /**
     * Vencida e não paga: vencimento no passado, de 1 dia a 1 ano de atraso.
     *
     * @return array{0: Carbon, 1: Carbon, 2: null}
     */
    private function overdueDates(Carbon $today): array
    {
        $dueDate = $today->copy()->subDays(random_int(1, 365));
        $issueDate = $dueDate->copy()->subDays(random_int(15, 60));

        return [$issueDate, $dueDate, null];
    }

    /**
     * Paga: espalhada pelos últimos 24 meses, para o relatório com filtro de
     * período fazer sentido ao testar. Pagamento entre 10 dias antes e 60
     * dias depois do vencimento (a maioria paga perto do vencimento, uma
     * parte com atraso real que gera juros), nunca no futuro nem antes da
     * emissão.
     *
     * @return array{0: Carbon, 1: Carbon, 2: Carbon}
     */
    private function paidDates(Carbon $today): array
    {
        $dueDate = $today->copy()->subDays(random_int(1, 730));
        $issueDate = $dueDate->copy()->subDays(random_int(15, 60));

        $paymentDate = $dueDate->copy()->addDays(random_int(-10, 60));

        if ($paymentDate->greaterThan($today)) {
            $paymentDate = $today->copy();
        }

        if ($paymentDate->lessThan($issueDate)) {
            $paymentDate = $issueDate->copy();
        }

        return [$issueDate, $dueDate, $paymentDate];
    }
}
