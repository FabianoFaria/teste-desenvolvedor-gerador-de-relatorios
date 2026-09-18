<?php

namespace Tests\Feature\Seeders;

use App\Models\Billing;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_running_the_default_seeder_leaves_the_app_ready_to_browse(): void
    {
        (new DatabaseSeeder)->run();

        // Usuários de teste — login funciona sem nenhum passo manual extra.
        $this->assertTrue(User::where('email', 'admin@teste.com')->exists());
        $this->assertTrue(User::where('email', 'test@example.com')->exists());

        // Não precisa ser exaustivo — só confirmar que `php artisan db:seed`
        // sozinho já deixa listagens/relatório com dado de verdade, não
        // vazios. O volume grande sob demanda continua sendo
        // `db:seed:volume --count=N`, sem relação com este teste.
        $this->assertGreaterThanOrEqual(100, Customer::count());
        $this->assertGreaterThanOrEqual(100, Billing::count());
    }
}
