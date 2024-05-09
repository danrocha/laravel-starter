<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\text;

class CreateR2Buckets extends Command
{
    protected $signature = 'r2:create-buckets';
    protected $description = 'Create Cloudflare R2 buckets and update .env files';

    public function handle()
    {
        $this->line("\n<fg=blue>🚀 Preparing to create Cloudflare R2 buckets...</>\n");

        // Project name input with pre-filled default
        $defaultProjectName = $this->laravel['config']['app.name'];
        $projectName = text(label: 'Enter the project name', default: $defaultProjectName);

        // Create development bucket
        $devBucket = "{$projectName}-dev";
        $this->line("<fg=green>🛠️ Creating development bucket: {$devBucket}</>");
        $this->executeWranglerCommand('wrangler r2 bucket create ' . $devBucket);
        $this->line("<fg=green>✅ Development bucket created: {$devBucket}</>\n");

        // Create production bucket
        $prodBucket = $projectName;
        $this->line("<fg=green>🛠️ Creating production bucket: {$prodBucket}</>");
        $this->executeWranglerCommand('wrangler r2 bucket create ' . $prodBucket);
        $this->line("<fg=green>✅ Production bucket created: {$prodBucket}</>\n");

        // Update .env.example file
        $this->updateEnvironmentFile('.env.example', $prodBucket);

        // Update .env file
        $this->updateEnvironmentFile('.env', $devBucket);

        $this->line("\n<fg=cyan>📘 Further Configuration Instructions:</>\n");
        $this->line('<fg=yellow>For CLOUDFLARE_R2_ACCESS_KEY_ID and CLOUDFLARE_R2_SECRET_ACCESS_KEY, visit:</>');
        $this->line("<href=https://dash.cloudflare.com/aadb3f382857e96bd2f6c9f1b5889f8d/r2/api-tokens>https://dash.cloudflare.com/aadb3f382857e96bd2f6c9f1b5889f8d/r2/api-tokens</>\n");

        $this->line('<fg=yellow>For CLOUDFLARE_R2_ENDPOINT and to connect a domain (to setup CLOUDFLARE_R2_URL), visit:</>');
        $this->line("<fg=white>(DEV):</> <href=https://dash.cloudflare.com/aadb3f382857e96bd2f6c9f1b5889f8d/r2/default/buckets/{$devBucket}/settings>https://dash.cloudflare.com/aadb3f382857e96bd2f6c9f1b5889f8d/r2/default/buckets/{$devBucket}/settings</>");
        $this->line("<fg=white>(PROD):</> <href=https://dash.cloudflare.com/aadb3f382857e96bd2f6c9f1b5889f8d/r2/default/buckets/{$prodBucket}/settings>https://dash.cloudflare.com/aadb3f382857e96bd2f6c9f1b5889f8d/r2/default/buckets/{$prodBucket}/settings</>\n");
    }

    protected function executeWranglerCommand($command)
    {
        $output = shell_exec($command);
        if ($output === null) {
            $this->error("❌ Failed to execute command: {$command}");
        } else {
            $this->info("✔️ Command executed successfully: {$command}");
        }
    }

    protected function updateEnvironmentFile($fileName, $bucketName)
    {
        if (File::exists(base_path($fileName))) {
            $content = File::get(base_path($fileName));
            $newContent = "\n"; // Start with a newline to ensure separation
            $newContent .= "CLOUDFLARE_R2_ACCESS_KEY_ID=\n";
            $newContent .= "CLOUDFLARE_R2_SECRET_ACCESS_KEY=\n";
            $newContent .= "CLOUDFLARE_R2_BUCKET={$bucketName}\n";
            $newContent .= "CLOUDFLARE_R2_ENDPOINT=\n";
            $newContent .= "CLOUDFLARE_R2_URL=\n";

            // Append the new variables
            $content .= $newContent;
            File::put(base_path($fileName), $content);
            $this->info("📝 .{$fileName} updated with new R2 bucket details.");
        }
    }
}
