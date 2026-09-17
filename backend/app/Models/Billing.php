<?php

namespace App\Models;

use Database\Factories\BillingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'customer_id',
    'description',
    'original_amount',
    'issue_date',
    'due_date',
    'payment_date',
    'monthly_interest_rate',
    'status',
    'paid_amount',
    'interest_amount_at_payment',
])]
class Billing extends Model
{
    /** @use HasFactory<BillingFactory> */
    use HasFactory;

    /**
     * O cálculo de juros (composto, em tempo real para cobranças vencidas)
     * fica centralizado em App\Services\InterestCalculatorService — não
     * duplicar essa regra aqui.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'payment_date' => 'date',
            'original_amount' => 'decimal:2',
            'monthly_interest_rate' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'interest_amount_at_payment' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
