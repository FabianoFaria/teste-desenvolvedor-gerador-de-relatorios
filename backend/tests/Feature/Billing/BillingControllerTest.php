<?php

namespace Tests\Feature\Billing;

use App\Models\Billing;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BillingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_unauthenticated_user_cannot_access_billing_routes(): void
    {
        $billing = Billing::factory()->create();

        $this->getJson('/api/billings')->assertStatus(401);
        $this->getJson("/api/billings/{$billing->id}")->assertStatus(401);
        $this->postJson('/api/billings', [])->assertStatus(401);
        $this->putJson("/api/billings/{$billing->id}", [])->assertStatus(401);
        $this->postJson("/api/billings/{$billing->id}/pay", [])->assertStatus(401);
    }

    public function test_can_create_billing_with_valid_data(): void
    {
        $customer = Customer::factory()->create();

        $payload = [
            'customer_id' => $customer->id,
            'description' => 'Serviço de consultoria',
            'original_amount' => 1500.50,
            'issue_date' => '2026-09-01',
            'due_date' => '2026-09-30',
            'monthly_interest_rate' => 2.5,
        ];

        $response = $this->postJson('/api/billings', $payload, $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonPath('data.customer_id', $customer->id)
            ->assertJsonPath('data.description', 'Serviço de consultoria')
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('billings', [
            'customer_id' => $customer->id,
            'description' => 'Serviço de consultoria',
            'status' => 'pending',
        ]);
    }

    public function test_can_update_non_paid_billing(): void
    {
        $billing = Billing::factory()->create([
            'status' => 'pending',
            'description' => 'Descrição antiga',
        ]);

        $payload = [
            'customer_id' => $billing->customer_id,
            'description' => 'Descrição nova',
            'original_amount' => 999.99,
            'issue_date' => $billing->issue_date->toDateString(),
            'due_date' => $billing->due_date->toDateString(),
            'monthly_interest_rate' => 3,
        ];

        $response = $this->putJson("/api/billings/{$billing->id}", $payload, $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonPath('data.description', 'Descrição nova');

        $this->assertDatabaseHas('billings', [
            'id' => $billing->id,
            'description' => 'Descrição nova',
        ]);
    }

    public function test_updating_paid_billing_returns_422(): void
    {
        $billing = Billing::factory()->paid()->create();

        $payload = [
            'customer_id' => $billing->customer_id,
            'description' => 'Tentativa de edição',
            'original_amount' => 100,
            'issue_date' => $billing->issue_date->toDateString(),
            'due_date' => $billing->due_date->toDateString(),
            'monthly_interest_rate' => 1,
        ];

        $response = $this->putJson("/api/billings/{$billing->id}", $payload, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors('status');

        $this->assertDatabaseHas('billings', [
            'id' => $billing->id,
            'description' => $billing->description,
        ]);
    }

    public function test_registering_payment_persists_payment_snapshot(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-18'));

        $billing = Billing::factory()->create([
            'original_amount' => 1000.00,
            'monthly_interest_rate' => 2.5,
            'issue_date' => Carbon::parse('2026-07-19'),
            'due_date' => Carbon::parse('2026-08-19'), // exatamente 30 dias de atraso
            'status' => 'overdue',
            'payment_date' => null,
            'paid_amount' => null,
            'interest_amount_at_payment' => null,
        ]);

        $response = $this->postJson("/api/billings/{$billing->id}/pay", [], $this->authHeaders());

        // Cálculo manual: 1000 * (1 + 0.025) ^ (30 / 30) = 1025.00, juros = 25.00
        // Valores inteiros em JSON (sem JSON_PRESERVE_ZERO_FRACTION) chegam
        // sem o ".0" e decodificam como int em PHP — daí o literal sem casas
        // decimais nesta asserção estrita (assertJsonPath usa assertSame).
        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'paid')
            ->assertJsonPath('data.paid_amount', 1025)
            ->assertJsonPath('data.interest_amount_at_payment', 25);

        $this->assertDatabaseHas('billings', [
            'id' => $billing->id,
            'status' => 'paid',
            'paid_amount' => 1025.00,
            'interest_amount_at_payment' => 25.00,
        ]);

        $billing->refresh();
        $this->assertNotNull($billing->payment_date);
        $this->assertTrue($billing->payment_date->isSameDay(Carbon::parse('2026-09-18')));
    }

    public function test_paying_already_paid_billing_returns_422(): void
    {
        $billing = Billing::factory()->paid()->create();
        $originalPaidAmount = $billing->paid_amount;

        $response = $this->postJson("/api/billings/{$billing->id}/pay", [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors('status');

        $this->assertDatabaseHas('billings', [
            'id' => $billing->id,
            'paid_amount' => $originalPaidAmount,
        ]);
    }

    public function test_paying_cancelled_billing_returns_422(): void
    {
        $billing = Billing::factory()->cancelled()->create();

        $response = $this->postJson("/api/billings/{$billing->id}/pay", [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors('status');

        $this->assertDatabaseHas('billings', [
            'id' => $billing->id,
            'status' => 'cancelled',
            'payment_date' => null,
        ]);
    }

    public function test_billing_listing_is_paginated(): void
    {
        Billing::factory()->count(30)->create();

        $response = $this->getJson('/api/billings', $this->authHeaders());

        $response->assertStatus(200);
        $this->assertCount(15, $response->json('data'));
        $this->assertSame(30, $response->json('meta.total'));
    }

    public function test_billing_listing_and_show_include_eager_loaded_customer(): void
    {
        $customer = Customer::factory()->create(['name' => 'Cliente Eager Load']);
        $billing = Billing::factory()->for($customer)->create();

        // A prova de "não escala" (teste seguinte) por si só não pega uma
        // regressão aqui: o BillingResource usa whenLoaded(), que só omite o
        // campo 'customer' quando a relação não vem eager-loaded — não faz
        // lazy load. Ou seja, sem with('customer')/load('customer') o número
        // de queries continuaria baixo e constante, mas o campo sumiria
        // silenciosamente da resposta. Por isso esta asserção de conteúdo é
        // necessária além da contagem de queries.
        $listResponse = $this->getJson('/api/billings', $this->authHeaders());
        $listResponse->assertStatus(200)
            ->assertJsonPath('data.0.customer.id', $customer->id)
            ->assertJsonPath('data.0.customer.name', 'Cliente Eager Load');

        $showResponse = $this->getJson("/api/billings/{$billing->id}", $this->authHeaders());
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.customer.id', $customer->id)
            ->assertJsonPath('data.customer.name', 'Cliente Eager Load');
    }

    public function test_billing_listing_filters_by_status_and_customer(): void
    {
        $customerA = Customer::factory()->create();
        $customerB = Customer::factory()->create();

        Billing::factory()->for($customerA)->pending()->count(2)->create();
        Billing::factory()->for($customerA)->paid()->count(1)->create();
        Billing::factory()->for($customerB)->pending()->count(3)->create();

        $byStatus = $this->getJson('/api/billings?status=pending', $this->authHeaders());
        $byStatus->assertStatus(200);
        $this->assertCount(5, $byStatus->json('data'));

        $byCustomer = $this->getJson(
            "/api/billings?customer_id={$customerA->id}",
            $this->authHeaders()
        );
        $byCustomer->assertStatus(200);
        $this->assertCount(3, $byCustomer->json('data'));
    }

    public function test_billing_listing_query_count_does_not_scale_with_number_of_billings(): void
    {
        $queriesFor5 = $this->queryCountForBillingListing(5);
        $queriesFor15 = $this->queryCountForBillingListing(15);

        // Se customer estivesse sendo lazy-loaded implicitamente no
        // BillingResource (sem with('customer')), o número de queries
        // cresceria junto com a quantidade de cobranças (uma a mais por
        // customer distinto). Com eager loading, o total fica igual
        // independente do volume.
        $this->assertSame($queriesFor5, $queriesFor15);
    }

    private function queryCountForBillingListing(int $billingCount): int
    {
        $headers = $this->authHeaders();

        Billing::query()->delete();
        Billing::factory()->count($billingCount)->create();

        DB::enableQueryLog();
        $response = $this->getJson('/api/billings?per_page='.$billingCount, $headers);
        $queryLog = DB::getQueryLog();
        DB::flushQueryLog();
        DB::disableQueryLog();

        $response->assertStatus(200);
        $this->assertCount($billingCount, $response->json('data'));

        // Só contamos queries em billings/customers — as de autenticação do
        // Sanctum (personal_access_tokens/users) variam entre chamadas dentro
        // do mesmo teste por causa de um cache do guard entre requisições de
        // teste (RequestGuard memoiza o usuário resolvido na instância, que
        // persiste entre chamadas de $this->getJson() no mesmo método), o que
        // não tem relação com o que este teste quer provar.
        return count(array_filter(
            $queryLog,
            fn (array $entry) => str_contains($entry['query'], 'billings')
                || str_contains($entry['query'], 'customers')
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
