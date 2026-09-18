<?php

namespace App\Services;

use App\Models\Billing;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Fonte única de verdade do cálculo de juros de cobranças vencidas — ver
 * CLAUDE.md, seção "Regras de negócio críticas > Cálculo de juros".
 *
 * Fórmula (juros compostos):
 *   valor_atualizado = valor_original * (1 + taxa_mensal) ^ (dias_em_atraso / 30)
 *
 * O valor atualizado é sempre calculado em tempo real a partir daqui e NUNCA
 * persistido como verdade no banco (só valor pago e juros no momento do
 * pagamento são gravados ao registrar um pagamento). Nenhum outro lugar do
 * código — listagem, tela de cobrança, relatório, exportações — deve
 * reimplementar essa fórmula; todos devem chamar este service.
 */
class InterestCalculatorService
{
    /**
     * @return array{
     *     original_amount: float,
     *     interest_amount: float,
     *     updated_amount: float,
     *     days_overdue: int,
     * }
     */
    public function calculate(Billing $billing, ?CarbonInterface $referenceDate = null): array
    {
        // Normalizado para o início do dia: uma cobrança com vencimento hoje
        // não deve ser tratada como vencida só porque o horário atual já
        // passou da meia-noite.
        $referenceDate = ($referenceDate ?? Carbon::now())->copy()->startOfDay();
        $dueDate = $billing->due_date->copy()->startOfDay();

        $originalAmount = round((float) $billing->original_amount, 2);

        // Paga ou cancelada: não há mais cobrança em aberto acumulando juros,
        // mesmo com due_date no passado. Ainda não vencida
        // (due_date >= referenceDate): nada a cobrar ainda.
        if (
            in_array($billing->status, ['paid', 'cancelled'], true)
            || $dueDate->greaterThanOrEqualTo($referenceDate)
        ) {
            return [
                'original_amount' => $originalAmount,
                'interest_amount' => 0.0,
                'updated_amount' => $originalAmount,
                'days_overdue' => 0,
            ];
        }

        // diffInDays() retorna float nesta versão do Carbon; dias em atraso
        // é sempre um número inteiro de dias.
        $daysOverdue = (int) abs($referenceDate->diffInDays($dueDate));
        $monthlyRate = (float) $billing->monthly_interest_rate / 100;

        $updatedAmount = round($originalAmount * (1 + $monthlyRate) ** ($daysOverdue / 30), 2);

        // Juro derivado da diferença entre os valores já arredondados, para
        // que original + interest sempre bata exatamente com updated (evita
        // dois arredondamentos independentes divergirem em um centavo).
        $interestAmount = round($updatedAmount - $originalAmount, 2);

        return [
            'original_amount' => $originalAmount,
            'interest_amount' => $interestAmount,
            'updated_amount' => $updatedAmount,
            'days_overdue' => $daysOverdue,
        ];
    }
}
