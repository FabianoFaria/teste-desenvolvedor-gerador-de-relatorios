<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Cobranças geradas por padrão em `php artisan db:seed` — só o
     * suficiente para a aplicação já vir navegável (listagens, relatório e
     * export com dados de verdade) sem exigir um segundo comando manual.
     * Volume grande sob demanda continua sendo
     * `php artisan db:seed:volume --count=N`.
     */
    private const DEFAULT_VOLUME = 300;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            UserSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Mesma VolumeSeeder::run() usada por SeedVolumeCommand (db:seed:volume)
        // — nenhuma lógica de geração duplicada, só um $billingCount bem menor
        // por padrão. VolumeSeeder::run() já aceita esse parâmetro
        // programaticamente (não só via --option de console), então basta
        // passá-lo aqui.
        $this->call(VolumeSeeder::class, false, ['billingCount' => self::DEFAULT_VOLUME]);
    }
}
