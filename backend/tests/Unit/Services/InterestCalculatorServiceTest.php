<?php

namespace Tests\Unit\Services;

use App\Models\Billing;
use App\Services\InterestCalculatorService;
use Carbon\Carbon;
use Tests\TestCase;

class InterestCalculatorServiceTest extends TestCase
{
    public function test_billing_not_yet_due_has_no_interest(): void
    {
        $billing = new Billing([
            'original_amount' => 1000.00,
            'monthly_interest_rate' => 2.5,
            'due_date' => Carbon::parse('2026-10-01'),
            'status' => 'pending',
        ]);

        $result = (new InterestCalculatorService)->calculate($billing, Carbon::parse('2026-09-18'));

        $this->assertSame(1000.0, $result['original_amount']);
        $this->assertSame(0.0, $result['interest_amount']);
        $this->assertSame(1000.0, $result['updated_amount']);
        $this->assertSame(0, $result['days_overdue']);
    }

    public function test_overdue_billing_matches_manual_compound_interest_calculation(): void
    {
        $referenceDate = Carbon::parse('2026-09-18');
        $dueDate = Carbon::parse('2026-08-19'); // exatamente 30 dias antes

        $billing = new Billing([
            'original_amount' => 1000.00,
            'monthly_interest_rate' => 2.5,
            'due_date' => $dueDate,
            'status' => 'overdue',
        ]);

        $result = (new InterestCalculatorService)->calculate($billing, $referenceDate);

        // Cálculo manual: dias_em_atraso = 30, taxa_mensal = 2,5% => 0.025
        // valor_atualizado = 1000 * (1 + 0.025) ^ (30 / 30) = 1000 * 1.025 = 1025.00
        // juros = 1025.00 - 1000.00 = 25.00
        $this->assertSame(30, $result['days_overdue']);
        $this->assertSame(1000.0, $result['original_amount']);
        $this->assertSame(1025.0, $result['updated_amount']);
        $this->assertSame(25.0, $result['interest_amount']);
    }

    public function test_paid_billing_never_accrues_interest_even_if_overdue(): void
    {
        $billing = new Billing([
            'original_amount' => 1000.00,
            'monthly_interest_rate' => 2.5,
            'due_date' => Carbon::parse('2026-01-01'),
            'status' => 'paid',
        ]);

        // referenceDate bem depois da due_date: se o status "paid" não fosse
        // checado primeiro, isso acumularia meses de juros.
        $result = (new InterestCalculatorService)->calculate($billing, Carbon::parse('2026-09-18'));

        $this->assertSame(0.0, $result['interest_amount']);
        $this->assertSame(1000.0, $result['updated_amount']);
        $this->assertSame(0, $result['days_overdue']);
    }

    public function test_cancelled_billing_never_accrues_interest_even_if_overdue(): void
    {
        $billing = new Billing([
            'original_amount' => 1000.00,
            'monthly_interest_rate' => 2.5,
            'due_date' => Carbon::parse('2026-01-01'),
            'status' => 'cancelled',
        ]);

        // referenceDate bem depois da due_date: se o status "cancelled" não
        // fosse checado, isso acumularia meses de juros sobre uma cobrança
        // que não está mais em aberto.
        $result = (new InterestCalculatorService)->calculate($billing, Carbon::parse('2026-09-18'));

        $this->assertSame(0.0, $result['interest_amount']);
        $this->assertSame(1000.0, $result['updated_amount']);
        $this->assertSame(0, $result['days_overdue']);
    }

    public function test_zero_interest_rate_keeps_updated_amount_equal_to_original_even_if_overdue(): void
    {
        $billing = new Billing([
            'original_amount' => 1000.00,
            'monthly_interest_rate' => 0,
            'due_date' => Carbon::parse('2026-08-01'),
            'status' => 'overdue',
        ]);

        $result = (new InterestCalculatorService)->calculate($billing, Carbon::parse('2026-09-18'));

        $this->assertGreaterThan(0, $result['days_overdue']);
        $this->assertSame(0.0, $result['interest_amount']);
        $this->assertSame(1000.0, $result['updated_amount']);
    }

    public function test_billing_due_exactly_today_is_not_yet_overdue(): void
    {
        // Horário no meio da tarde, de propósito: due_date é normalizada
        // para o início do dia, então "vencer hoje" não pode depender da
        // hora atual do relógio.
        $referenceDate = Carbon::parse('2026-09-18 15:30:00');

        $billing = new Billing([
            'original_amount' => 1000.00,
            'monthly_interest_rate' => 2.5,
            'due_date' => Carbon::parse('2026-09-18'),
            'status' => 'pending',
        ]);

        $result = (new InterestCalculatorService)->calculate($billing, $referenceDate);

        $this->assertSame(0, $result['days_overdue']);
        $this->assertSame(0.0, $result['interest_amount']);
        $this->assertSame(1000.0, $result['updated_amount']);
    }

    public function test_calculate_from_values_matches_calculate_for_the_same_inputs(): void
    {
        // calculateFromValues() existe para lotes (VolumeSeeder) evitarem
        // instanciar um Billing por linha — precisa produzir exatamente o
        // mesmo resultado que calculate() para os mesmos dados, já que é a
        // mesma fórmula por baixo.
        $billing = new Billing([
            'original_amount' => 1000.00,
            'monthly_interest_rate' => 2.5,
            'due_date' => Carbon::parse('2026-08-19'),
            'status' => 'overdue',
        ]);

        $service = new InterestCalculatorService;

        $viaModel = $service->calculate($billing, Carbon::parse('2026-09-18'));
        $viaValues = $service->calculateFromValues(1000.00, 2.5, 30);

        $this->assertSame($viaModel, $viaValues);
        $this->assertSame(1025.0, $viaValues['updated_amount']);
        $this->assertSame(25.0, $viaValues['interest_amount']);
    }

    public function test_calculate_from_values_with_zero_days_overdue_returns_original_amount(): void
    {
        $result = (new InterestCalculatorService)->calculateFromValues(1000.00, 2.5, 0);

        $this->assertSame(0.0, $result['interest_amount']);
        $this->assertSame(1000.0, $result['updated_amount']);
        $this->assertSame(0, $result['days_overdue']);
    }
}
