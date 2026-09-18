<?php

namespace App\Http\Requests;

use App\Models\Billing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam('monthly_interest_rate', 'number', 'Taxa de juros mensal em percentual — 2.5 representa 2,5% ao mês, não a fração decimal 0.025.', example: 2.5)]
class UpdateBillingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'description' => ['required', 'string', 'max:255'],
            'original_amount' => ['required', 'numeric', 'min:0.01'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'monthly_interest_rate' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $billing = $this->route('billing');

            if ($billing instanceof Billing && $billing->status === 'paid') {
                $validator->errors()->add('status', 'Não é possível editar uma cobrança já paga.');
            }
        });
    }
}
