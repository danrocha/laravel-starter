<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateDatabase extends Command
{
    protected $signature = 'db:create';
    protected $description = 'Create a PostgreSQL database and update .env files';

    public function handle()
    {
        // Using Laravel\Prompts for inputs
        $port = text(label: 'Select PostgreSQL port', default: '5444');
        $dbName = text(label: 'Define the database name', default: $this->laravel['config']['app.name']);
        $user = text(label: 'Define user', default: 'postgres');
        $password = password(label: 'Define password');

        // Create the database
        $this->info('Creating database...');
        $connection = pg_connect("host=localhost port={$port} user={$user}" . ($password ? " password={$password}" : ''));
        pg_query($connection, "CREATE DATABASE \"{$dbName}\"");

        // Update .env and .env.example files
        $this->updateEnvironmentFile($dbName, $port, $user, $password);

        // Check if user wants to run migrations
        $runMigrations = confirm(label: 'Do you want to run the migrations?');
        if ($runMigrations) {
            Artisan::call('migrate', [], $this->getOutput());
        }
    }

    protected function updateEnvironmentFile($dbName, $port, $user, $password)
    {
        $envFiles = ['.env', '.env.example'];
        foreach ($envFiles as $file) {
            if (File::exists(base_path($file))) {
                $content = File::get(base_path($file));
                $content = preg_replace('/DB_PORT=.*/', "DB_PORT={$port}", $content);
                $content = preg_replace('/DB_DATABASE=.*/', "DB_DATABASE={$dbName}", $content);
                $content = preg_replace('/DB_USERNAME=.*/', "DB_USERNAME={$user}", $content);
                $content = preg_replace('/DB_PASSWORD=.*/', "DB_PASSWORD={$password}", $content);
                File::put(base_path($file), $content);
            }
        }
    }
}
