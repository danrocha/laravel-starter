<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class InitializeProject extends Command
{
    protected $signature = 'init';
    protected $description = 'Initialize the project by installing dependencies, setting up .env, and creating the database';

    public function handle()
    {
        $this->info('Initializing project...');

        // Run Composer Install
        $this->runProcess(['composer', 'install'], 'Running composer install...');

        // Run PNPM Install
        $this->runProcess(['pnpm', 'install'], 'Running pnpm install...');

        // Check and copy .env file
        $this->setupEnvironmentFile();

        // Run custom db:create command
        $this->call('db:create');

        // generate project key
        $this->runProcess(['php', 'artisan', 'key:generate'], 'Generating application key...');
    }

    private function runProcess(array $command, string $description)
    {
        $this->info($description);
        $process = new Process($command, base_path(), null, null, null);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->error('Command failed: ' . implode(' ', $command));

            return false;
        }

        return true;
    }

    private function setupEnvironmentFile()
    {
        if (! File::exists(base_path('.env'))) {
            $this->info('Copying .env.example to .env...');
            File::copy(base_path('.env.example'), base_path('.env'));
        } else {
            $this->info('.env file already exists.');
        }
    }
}
