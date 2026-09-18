<?php

namespace App\Http\Resources;

use App\Models\Billing;
use App\Services\InterestCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Billing
 */
class BillingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Juros/valor atualizado NUNCA são lidos de coluna persistida — são
        // sempre recalculados aqui, na serialização, via a fonte única de
        // verdade (App\Services\InterestCalculatorService).
        $calculation = app(InterestCalculatorService::class)->calculate($this->resource);

        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            // whenLoaded: se o controller não tiver dado with('customer')/
            // load('customer'), o campo some da resposta em vez de disparar
            // lazy loading implícito aqui.
            'customer' => $this->whenLoaded('customer', fn () => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ]),
            'description' => $this->description,
            'original_amount' => (float) $this->original_amount,
            'issue_date' => $this->issue_date->toDateString(),
            'due_date' => $this->due_date->toDateString(),
            'payment_date' => $this->payment_date?->toDateString(),
            'monthly_interest_rate' => (float) $this->monthly_interest_rate,
            'status' => $this->status,
            'paid_amount' => $this->paid_amount !== null ? (float) $this->paid_amount : null,
            'interest_amount_at_payment' => $this->interest_amount_at_payment !== null
                ? (float) $this->interest_amount_at_payment
                : null,
            'interest_amount' => $calculation['interest_amount'],
            'updated_amount' => $calculation['updated_amount'],
            'days_overdue' => $calculation['days_overdue'],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
