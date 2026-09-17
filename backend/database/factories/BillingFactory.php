<?php

namespace Database\Factories;

use App\Models\Billing;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Billing>
 */
class BillingFactory extends Factory
{
    /**
     * Define the model's default state: uma cobrança pendente, dentro do prazo.
     *
     * Os demais cenários (vencida, paga, cancelada) ficam disponíveis como
     * states nomeados — os seeders de volume que virão depois decidem a
     * proporção de cada um. Os states só sobrescrevem os campos que variam
     * por status (datas, valores de pagamento); customer_id, description,
     * original_amount e monthly_interest_rate ficam só aqui, para não
     * conflitar com `Billing::factory()->for($customer)`.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'description' => fake('pt_BR')->randomElement([
                'Mensalidade de serviço',
                'Fatura de consultoria',
                'Cobrança de assinatura mensal',
                'Prestação de serviços administrativos',
                'Locação de equipamento',
                'Honorários contábeis',
            ]),
            'original_amount' => fake()->randomFloat(2, 100, 20000),
            'monthly_interest_rate' => fake()->randomFloat(2, 1, 5),
            ...$this->pendingDates(),
        ];
    }

    /**
     * Cobrança dentro do prazo, ainda não vencida nem paga.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => $this->pendingDates());
    }

    /**
     * Cobrança vencida e não paga (a base do cálculo de juros em tempo real).
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => $this->overdueDates());
    }

    /**
     * Cobrança já paga — juros gravados congelam na data do pagamento.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => $this->paidDates($attributes['original_amount']));
    }

    /**
     * Cobrança cancelada.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => $this->cancelledDates());
    }

    /**
     * @return array<string, mixed>
     */
    private function pendingDates(): array
    {
        return [
            'issue_date' => Carbon::now()->subDays(fake()->numberBetween(1, 15)),
            'due_date' => Carbon::now()->addDays(fake()->numberBetween(1, 30)),
            'payment_date' => null,
            'status' => 'pending',
            'paid_amount' => null,
            'interest_amount_at_payment' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function overdueDates(): array
    {
        $dueDate = Carbon::now()->subDays(fake()->numberBetween(1, 180));

        return [
            'issue_date' => $dueDate->clone()->subDays(fake()->numberBetween(15, 30)),
            'due_date' => $dueDate,
            'payment_date' => null,
            'status' => 'overdue',
            'paid_amount' => null,
            'interest_amount_at_payment' => null,
        ];
    }

    /**
     * @param  float|string  $originalAmount
     * @return array<string, mixed>
     */
    private function paidDates($originalAmount): array
    {
        $dueDate = Carbon::now()->subDays(fake()->numberBetween(1, 180));
        $paymentDate = $dueDate->clone()->addDays(fake()->numberBetween(0, 20));
        $interestAmount = fake()->boolean(60)
            ? round((float) $originalAmount * fake()->randomFloat(4, 0, 0.1), 2)
            : 0;

        return [
            'issue_date' => $dueDate->clone()->subDays(fake()->numberBetween(15, 30)),
            'due_date' => $dueDate,
            'payment_date' => $paymentDate,
            'status' => 'paid',
            'paid_amount' => round((float) $originalAmount + $interestAmount, 2),
            'interest_amount_at_payment' => $interestAmount,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cancelledDates(): array
    {
        $issueDate = Carbon::now()->subDays(fake()->numberBetween(1, 60));

        return [
            'issue_date' => $issueDate,
            'due_date' => $issueDate->clone()->addDays(fake()->numberBetween(15, 30)),
            'payment_date' => null,
            'status' => 'cancelled',
            'paid_amount' => null,
            'interest_amount_at_payment' => null,
        ];
    }
}
