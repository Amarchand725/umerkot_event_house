<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ModuleSetupCommand extends Command
{
    protected $signature = 'module:setup';

    protected $description = 'Run migrations and global application seeders';

    public function handle(): int
    {
        $this->newLine();
        $this->info('Running module setup...');
        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | 1. Run Migrations
        |--------------------------------------------------------------------------
        */

        $this->info('Running migrations...');

        $this->call('migrate', [
            '--force' => true,
        ]);

        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | 2. Status Seeder
        |--------------------------------------------------------------------------
        */

        $this->info('Running StatusSeeder...');

        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\StatusSeeder',
            '--force' => true,
        ]);

        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | 3. Permission Seeder
        |--------------------------------------------------------------------------
        */

        $this->info('Running PermissionSeeder...');

        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\PermissionSeeder',
            '--force' => true,
        ]);

        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | 4. Role Permission Seeder
        |--------------------------------------------------------------------------
        */

        $this->info('Running RolePermissionSeeder...');

        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\RolePermissionSeeder',
            '--force' => true,
        ]);

        $this->newLine();

        $this->info('Module setup completed successfully.');

        return self::SUCCESS;
    }
}