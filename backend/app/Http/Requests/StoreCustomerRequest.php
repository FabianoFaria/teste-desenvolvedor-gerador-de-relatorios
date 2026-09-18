<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam('document', 'string', 'CPF ou CNPJ do cliente. O valor é salvo exatamente como enviado (a API não normaliza formato nem valida dígito verificador) — os clientes já cadastrados usam pontuação, então o mesmo formato é recomendado para manter consistência.', example: '123.456.789-00')]
class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Preenche o status com o default 'active' quando não informado, antes
     * da validação — assim a regra 'in:active,inactive' abaixo também cobre
     * o valor default.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 'active'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // CPF ou CNPJ — sem validação de formato aqui, só de obrigatoriedade
            // e unicidade (a validação de formato fica para uma evolução futura).
            'document' => ['required', 'string', 'max:255', 'unique:customers,document'],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['sometimes', 'in:active,inactive'],
        ];
    }
}
