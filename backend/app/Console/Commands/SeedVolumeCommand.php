<?php

namespace App\Console\Commands;

use Database\Seeders\VolumeSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('db:seed:volume {--count=50000 : Quantidade de cobranças a gerar}')]
#[Description('Popula o banco com um volume grande e realista de clientes e cobranças, para testar performance com dados em escala. Não roda como parte do db:seed padrão.')]
class SeedVolumeCommand extends Command
{
    public function handle(): int
    {
        $count = (int) $this->option('count');

        if ($count < 1) {
            $this->components->error('--count deve ser um número inteiro positivo.');

            return self::FAILURE;
        }

        $seeder = new VolumeSeeder;
        $seeder->setCommand($this);

        $startedAt = microtime(true);

        $seeder(['billingCount' => $count]);

        $elapsedSeconds = microtime(true) - $startedAt;

        $this->components->info(sprintf(
            'Concluído: %d cobranças geradas em %s.',
            $count,
            $this->formatElapsed($elapsedSeconds)
        ));

        return self::SUCCESS;
    }

    private function formatElapsed(float $seconds): string
    {
        $minutes = (int) floor($seconds / 60);
        $remainingSeconds = $seconds - ($minutes * 60);

        if ($minutes === 0) {
            return sprintf('%.2fs', $remainingSeconds);
        }

        return sprintf('%dmin %.2fs', $minutes, $remainingSeconds);
    }
}
