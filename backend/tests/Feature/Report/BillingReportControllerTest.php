<?php

namespace Tests\Feature\Report;

use App\Models\Billing;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\VolumeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_unauthenticated_user_cannot_access_billing_report(): void
    {
        $this->getJson('/api/reports/billing')->assertStatus(401);
    }

    public function test_filters_by_issue_date_period(): void
    {
        $inside = Billing::factory()->create(['issue_date' => '2026-05-15', 'due_date' => '2026-06-15']);
        $outside = Billing::factory()->create(['issue_date' => '2026-01-01', 'due_date' => '2026-02-01']);

        $response = $this->getJson(
            '/api/reports/billing?date_field=issue_date&date_from=2026-05-01&date_to=2026-05-31',
            $this->authHeaders()
        );

        $response->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertContains($inside->id, $ids);
        $this->assertNotContains($outside->id, $ids);
        $this->assertSame('issue_date', $response->json('filters_applied.date_field'));
    }

    public function test_filters_by_due_date_period(): void
    {
        $inside = Billing::factory()->create(['issue_date' => '2026-05-01', 'due_date' => '2026-05-20']);
        $outside = Billing::factory()->create(['issue_date' => '2026-01-01', 'due_date' => '2026-02-10']);

        $response = $this->getJson(
            '/api/reports/billing?date_field=due_date&date_from=2026-05-01&date_to=2026-05-31',
            $this->authHeaders()
        );

        $response->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertContains($inside->id, $ids);
        $this->assertNotContains($outside->id, $ids);
    }

    public function test_filters_by_payment_date_period(): void
    {
        $inside = Billing::factory()->paid()->create([
            'issue_date' => '2026-04-01',
            'due_date' => '2026-04-20',
            'payment_date' => '2026-05-10',
        ]);
        // Não paga: payment_date nulo, nunca cai num filtro de payment_date.
        $outside = Billing::factory()->pending()->create([
            'issue_date' => '2026-04-01',
            'due_date' => '2026-12-01',
        ]);

        $response = $this->getJson(
            '/api/reports/billing?date_field=payment_date&date_from=2026-05-01&date_to=2026-05-31',
            $this->authHeaders()
        );

        $response->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertContains($inside->id, $ids);
        $this->assertNotContains($outside->id, $ids);
    }

    public function test_incomplete_date_range_is_ignored_not_applied(): void
    {
        Billing::factory()->count(2)->create();

        $response = $this->getJson(
            '/api/reports/billing?date_from=2026-05-01',
            $this->authHeaders()
        );

        $response->assertStatus(200);
        $this->assertNull($response->json('filters_applied.date_from'));
        $this->assertNull($response->json('filters_applied.date_to'));
        $this->assertSame(2, $response->json('totals.count'));
    }

    public function test_filters_by_customer_and_status(): void
    {
        $customerA = Customer::factory()->create();
        $customerB = Customer::factory()->create();

        Billing::factory()->for($customerA)->pending()->count(2)->create();
        Billing::factory()->for($customerA)->paid()->count(1)->create();
        Billing::factory()->for($customerB)->pending()->count(3)->create();

        $byStatus = $this->getJson('/api/reports/billing?status=pending', $this->authHeaders());
        $byStatus->assertStatus(200);
        $this->assertSame(5, $byStatus->json('totals.count'));

        $byCustomer = $this->getJson(
            "/api/reports/billing?customer_id={$customerA->id}",
            $this->authHeaders()
        );
        $byCustomer->assertStatus(200);
        $this->assertSame(3, $byCustomer->json('totals.count'));
    }

    public function test_totals_match_manual_calculation_for_known_records(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-18'));

        // Pendente: ainda não vencida, sem juros. Contribui 1000.00 ao
        // original, 0 de juros, 1000.00 ao "pendente" (não pago).
        Billing::factory()->create([
            'original_amount' => 1000.00,
            'monthly_interest_rate' => 2.5,
            'issue_date' => '2026-09-01',
            'due_date' => '2026-10-01',
            'status' => 'pending',
            'payment_date' => null,
            'paid_amount' => null,
            'interest_amount_at_payment' => null,
        ]);

        // Vencida há exatamente 30 dias: 1000 * 1.025^1 = 1025.00 -> juros 25.00.
        Billing::factory()->create([
            'original_amount' => 1000.00,
            'monthly_interest_rate' => 2.5,
            'issue_date' => '2026-07-01',
            'due_date' => '2026-08-19',
            'status' => 'overdue',
            'payment_date' => null,
            'paid_amount' => null,
            'interest_amount_at_payment' => null,
        ]);

        // Paga: juros vem do snapshot histórico (interest_amount_at_payment),
        // não é recalculado.
        Billing::factory()->create([
            'original_amount' => 2000.00,
            'monthly_interest_rate' => 3.0,
            'issue_date' => '2026-06-01',
            'due_date' => '2026-07-01',
            'status' => 'paid',
            'payment_date' => '2026-07-15',
            'paid_amount' => 2030.00,
            'interest_amount_at_payment' => 30.00,
        ]);

        // Cancelada, com due_date bem no passado e taxa alta: se o service
        // (ou a query) tratasse "cancelled" como "vencida e não paga", isso
        // geraria uma quantia de juros bem visível. Deve contribuir 0 para
        // interest_total/updated_amount_total (mesmo comportamento do
        // InterestCalculatorService) e também 0 para pending_total (não é
        // "pendente" — não in ('paid', 'cancelled')).
        Billing::factory()->create([
            'original_amount' => 500.00,
            'monthly_interest_rate' => 5.0,
            'issue_date' => '2026-01-01',
            'due_date' => '2026-02-01',
            'status' => 'cancelled',
            'payment_date' => null,
            'paid_amount' => null,
            'interest_amount_at_payment' => null,
        ]);

        $response = $this->getJson('/api/reports/billing', $this->authHeaders());

        $response->assertStatus(200);

        $totals = $response->json('totals');

        // Manual:
        // count = 4
        // original_total = 1000 + 1000 + 2000 + 500 = 4500.00
        // interest_total = 0 (pending) + 25.00 (overdue) + 30.00 (paid, snapshot) + 0 (cancelled) = 55.00
        // updated_total  = original_total + interest_total = 4555.00
        // paid_total     = 2030.00 (só a paga)
        // pending_total  = 1000.00 (pending) + 1025.00 (overdue) + 0 (cancelled, excluída) = 2025.00
        $this->assertSame(4, $totals['count']);
        $this->assertEqualsWithDelta(4500.00, $totals['original_amount_total'], 0.001);
        $this->assertEqualsWithDelta(55.00, $totals['interest_total'], 0.001);
        $this->assertEqualsWithDelta(4555.00, $totals['updated_amount_total'], 0.001);
        $this->assertEqualsWithDelta(2030.00, $totals['paid_total'], 0.001);
        $this->assertEqualsWithDelta(2025.00, $totals['pending_total'], 0.001);
    }

    public function test_billing_report_responds_quickly_for_a_volume_slice(): void
    {
        // VolumeSeeder gera via insert em lote (não factory()->create() em
        // loop) — mesmo em SQLite in-memory, popular 10 mil linhas fica na
        // casa de segundos, não minutos.
        (new VolumeSeeder)->run(10000);

        $startedAt = microtime(true);

        $response = $this->getJson('/api/reports/billing?per_page=20', $this->authHeaders());

        $elapsedSeconds = microtime(true) - $startedAt;

        $response->assertStatus(200);
        $this->assertSame(10000, $response->json('totals.count'));

        // Não é uma trava dura de performance real — SQLite in-memory nos
        // testes tem características bem diferentes do MySQL em produção.
        // Serve só como sinal de regressão grosseira (ex: alguém trocar a
        // agregação SQL por um loop carregando tudo em memória). O tempo
        // observado contra MySQL com volume real está documentado no README.
        $this->assertLessThan(5.0, $elapsedSeconds, sprintf(
            'Relatório demorou %.3fs para 10 mil registros (SQLite in-memory).',
            $elapsedSeconds
        ));
    }

    /**
     * @return array<string, string>
     */
    private function authHeaders(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }
}
