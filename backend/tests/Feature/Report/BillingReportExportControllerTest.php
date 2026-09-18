<?php

namespace Tests\Feature\Report;

use App\Models\Billing;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingReportExportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_export_csv_or_pdf(): void
    {
        $this->getJson('/api/reports/billing/export/csv')->assertStatus(401);
        $this->getJson('/api/reports/billing/export/pdf')->assertStatus(401);
    }

    public function test_csv_export_respects_filters(): void
    {
        $customerA = Customer::factory()->create();
        $customerB = Customer::factory()->create();

        $matching = Billing::factory()->for($customerA)->pending()->create(['description' => 'Cobrança A']);
        Billing::factory()->for($customerB)->pending()->create(['description' => 'Cobrança B']);
        Billing::factory()->for($customerA)->paid()->create(['description' => 'Cobrança C (paga)']);

        $response = $this->get(
            "/api/reports/billing/export/csv?customer_id={$customerA->id}&status=pending",
            $this->authHeaders()
        );

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $csv = $this->parseCsv($response->streamedContent());

        $this->assertCount(1, $csv['rows']);
        $this->assertSame($customerA->name, $csv['rows'][0][0]);
        $this->assertSame('Cobrança A', $csv['rows'][0][1]);
    }

    public function test_csv_totals_match_listing_api_totals(): void
    {
        Customer::factory()->has(Billing::factory()->pending()->count(2))->create();
        Customer::factory()->has(Billing::factory()->overdue()->count(3))->create();
        Customer::factory()->has(Billing::factory()->paid()->count(4))->create();

        $headers = $this->authHeaders();

        $listingResponse = $this->getJson('/api/reports/billing', $headers);
        $listingResponse->assertStatus(200);
        $apiTotals = $listingResponse->json('totals');

        $csvResponse = $this->get('/api/reports/billing/export/csv', $headers);
        $csvResponse->assertStatus(200);
        $csv = $this->parseCsv($csvResponse->streamedContent());

        $this->assertSame($apiTotals['count'], (int) $csv['totals']['Quantidade de cobranças']);
        $this->assertEqualsWithDelta(
            $apiTotals['original_amount_total'],
            $this->parseBrazilianNumber($csv['totals']['Valor original total']),
            0.01
        );
        $this->assertEqualsWithDelta(
            $apiTotals['interest_total'],
            $this->parseBrazilianNumber($csv['totals']['Total de juros']),
            0.01
        );
        $this->assertEqualsWithDelta(
            $apiTotals['updated_amount_total'],
            $this->parseBrazilianNumber($csv['totals']['Valor atualizado total']),
            0.01
        );
        $this->assertEqualsWithDelta(
            $apiTotals['paid_total'],
            $this->parseBrazilianNumber($csv['totals']['Valor total recebido']),
            0.01
        );
        $this->assertEqualsWithDelta(
            $apiTotals['pending_total'],
            $this->parseBrazilianNumber($csv['totals']['Valor total pendente']),
            0.01
        );
    }

    public function test_pdf_export_within_limit_succeeds(): void
    {
        Billing::factory()->count(3)->create();

        $response = $this->get('/api/reports/billing/export/pdf', $this->authHeaders());

        $response->assertStatus(200);
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_pdf_export_above_limit_returns_422(): void
    {
        // Ajusta o limite para baixo só neste teste — evita ter que gerar
        // 500+ registros reais só para provar o caminho de erro.
        config(['reports.pdf_row_limit' => 2]);

        Billing::factory()->count(3)->create();

        $response = $this->getJson('/api/reports/billing/export/pdf', $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonPath('message', fn (string $message) => str_contains($message, 'CSV'));
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

    /**
     * @return array{header: array<int, string>, rows: array<int, array<int, string>>, totals: array<string, string>}
     */
    private function parseCsv(string $content): array
    {
        $content = ltrim($content, "\xEF\xBB\xBF");
        $lines = preg_split('/\r\n|\n/', $content);

        $headerIndex = null;
        foreach ($lines as $index => $line) {
            if (str_starts_with($line, 'Cliente,')) {
                $headerIndex = $index;
                break;
            }
        }

        $this->assertNotNull($headerIndex, 'Linha de cabeçalho da tabela não encontrada no CSV.');

        $rows = [];
        for ($i = $headerIndex + 1; $i < count($lines) && $lines[$i] !== ''; $i++) {
            $rows[] = str_getcsv($lines[$i]);
        }

        $totalsIndex = null;
        foreach ($lines as $index => $line) {
            if (trim($line, '"') === 'Totalizadores') {
                $totalsIndex = $index;
                break;
            }
        }

        $this->assertNotNull($totalsIndex, 'Seção de totalizadores não encontrada no CSV.');

        $totals = [];
        for ($i = $totalsIndex + 1; $i < count($lines) && $lines[$i] !== ''; $i++) {
            [$label, $value] = str_getcsv($lines[$i]);
            $totals[$label] = $value;
        }

        return [
            'header' => str_getcsv($lines[$headerIndex]),
            'rows' => $rows,
            'totals' => $totals,
        ];
    }

    private function parseBrazilianNumber(string $value): float
    {
        return (float) str_replace(',', '.', str_replace('.', '', $value));
    }
}
