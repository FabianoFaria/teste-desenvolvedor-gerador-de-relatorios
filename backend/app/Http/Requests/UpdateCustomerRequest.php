<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam('document', 'string', 'CPF ou CNPJ do cliente. O valor é salvo exatamente como enviado (a API não normaliza formato nem valida dígito verificador) — os clientes já cadastrados usam pontuação, então o mesmo formato é recomendado para manter consistência.', example: '123.456.789-00')]
class UpdateCustomerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'document' => [
                'required',
                'string',
                'max:255',
                Rule::unique('customers', 'document')->ignore($this->route('customer')),
            ],
            'email' => ['required', 'email', 'max:255'],
            // Ao contrário do Store, aqui NÃO aplicamos default 'active' quando
            // ausente — isso resetaria silenciosamente o status de um cliente
            // inactive a cada edição que não reenviasse o campo. Se omitido,
            // o status atual do registro é preservado.
            'status' => ['sometimes', 'in:active,inactive'],
        ];
    }
}
