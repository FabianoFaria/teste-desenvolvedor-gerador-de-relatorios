<?php

namespace App\Http\Requests;

use App\Models\Billing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PayBillingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Nenhum campo é aceito do client: a data de pagamento é sempre now() e
     * o valor pago é sempre o valor_atualizado calculado via
     * InterestCalculatorService no momento do registro — nunca o que o
     * client enviar.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $billing = $this->route('billing');

            if ($billing instanceof Billing && in_array($billing->status, ['paid', 'cancelled'], true)) {
                $validator->errors()->add('status', 'Esta cobrança não pode ser paga.');
            }
        });
    }
}
