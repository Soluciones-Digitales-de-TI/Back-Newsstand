<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Init extends Command
{
    /**
     * The name and signature of the console command.
     * @param ci composer install
     * @var string
     */
    protected $signature = 'init {ci=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ci = $this->argument('ci');
        $this->info($ci);

        if (false) {
            $this->info('Composer install...');
            $composerInstall = new Process(['composer', 'install']);
            $composerInstall->run();

            if (!$composerInstall->isSuccessful()) {
                $this->error('composer install failed: ' . $composerInstall->getErrorOutput());
                return 1;
            }
            $this->info($composerInstall->getOutput());
        }

        $this->info('Running git pull...');
        $gitPull = new Process(['git', 'pull']);
        $gitPull->run();

        if (!$gitPull->isSuccessful()) {
            $this->error('git pull failed: ' . $gitPull->getErrorOutput());
            return 1;
        }
        $this->info($gitPull->getOutput());

        $this->info('Clean route...');
        $routeCache = new Process(['php', 'artisan', 'route:cache']);
        $routeCache->run();

        if (!$routeCache->isSuccessful()) {
            $this->error('route:cache failed: ' . $routeCache->getErrorOutput());
            return 1;
        }
        $this->info($routeCache->getOutput());

        $this->info('Running php artisan migrate:fresh --seed...');
        $migrate = new Process(['php', 'artisan', 'migrate:fresh', '--seed']);
        $migrate->run();

        if (!$migrate->isSuccessful()) {
            $this->error('migrate:fresh --seed failed: ' . $migrate->getErrorOutput());
            return 1;
        }
        $this->info($migrate->getOutput());

        $this->info('Update and migration completed successfully.');
        return 0;
    }
}
