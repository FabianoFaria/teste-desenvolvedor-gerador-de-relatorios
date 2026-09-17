<?php

namespace Tests\Feature\Customer;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_customer_routes(): void
    {
        $customer = Customer::factory()->create();

        $this->getJson('/api/customers')->assertStatus(401);
        $this->getJson("/api/customers/{$customer->id}")->assertStatus(401);
        $this->postJson('/api/customers', [])->assertStatus(401);
        $this->putJson("/api/customers/{$customer->id}", [])->assertStatus(401);
    }

    public function test_can_create_customer_with_valid_data(): void
    {
        $response = $this->postJson('/api/customers', [
            'name' => 'Maria Souza',
            'document' => '123.456.789-00',
            'email' => 'maria@example.com',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Maria Souza')
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('customers', [
            'document' => '123.456.789-00',
            'email' => 'maria@example.com',
        ]);
    }

    public function test_creating_customer_with_duplicate_document_returns_422(): void
    {
        $existing = Customer::factory()->create(['document' => '123.456.789-00']);

        $response = $this->postJson('/api/customers', [
            'name' => 'Outro Cliente',
            'document' => $existing->document,
            'email' => 'outro@example.com',
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors('document');
    }

    public function test_can_update_existing_customer(): void
    {
        $customer = Customer::factory()->create(['name' => 'Nome Antigo']);

        $response = $this->putJson("/api/customers/{$customer->id}", [
            'name' => 'Nome Novo',
            'document' => $customer->document,
            'email' => $customer->email,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Nome Novo');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Nome Novo',
        ]);
    }

    public function test_customer_listing_is_paginated(): void
    {
        Customer::factory()->count(30)->create();

        $response = $this->getJson('/api/customers', $this->authHeaders());

        $response->assertStatus(200);
        $this->assertCount(15, $response->json('data'));
        $this->assertSame(30, $response->json('meta.total'));
    }

    public function test_customer_listing_filters_by_status(): void
    {
        Customer::factory()->count(3)->active()->create();
        Customer::factory()->count(2)->inactive()->create();

        $response = $this->getJson('/api/customers?status=inactive', $this->authHeaders());

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));

        collect($response->json('data'))->each(
            fn (array $customer) => $this->assertSame('inactive', $customer['status'])
        );
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
