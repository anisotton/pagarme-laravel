<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Commands;

use Illuminate\Console\Command;

class PagarmeCommand extends Command
{
    protected $signature = 'pagarme:info';

    protected $description = 'Display Pagarme package information';

    public function handle(): int
    {
        $this->info('🚀 Pagarme Laravel Package');
        $this->line('Version: 1.0.0');
        $this->line('Description: A Laravel package to integrate with Pagar.me API');
        $this->line('Documentation: https://github.com/anisotton/pagarme-laravel');

        $this->newLine();
        $this->comment('Configuration file: config/pagarme.php');
        $this->comment('API Key: '.(config('pagarme.api_key') ? 'Set ✓' : 'Not configured ✗'));
        $this->comment('Base URL: '.config('pagarme.base_url'));
        $this->comment('API Version: '.config('pagarme.api_version'));

        return self::SUCCESS;
    }
}
