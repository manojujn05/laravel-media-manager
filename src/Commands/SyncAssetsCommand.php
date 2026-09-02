<?php

namespace Innopanda\AssetManager\Commands;

use Illuminate\Console\Command;
use Innopanda\AssetManager\Services\AssetDiscoveryService;

class SyncAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset-manager:sync 
                            {--disk= : The disk to synchronize (defaults to config)}
                            {--root= : The root path to scan (defaults to config)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize the asset manager database with the physical storage disk';

    /**
     * Execute the console command.
     */
    public function handle(AssetDiscoveryService $discoveryService)
    {
        $diskName = $this->option('disk') ?? config('asset-manager.disk', 'public');
        $rootPath = $this->option('root') ?? config('asset-manager.sync.root_path', '/');

        $this->info("Asset Manager Storage Sync");
        $this->newLine();
        $this->line("Disk: <comment>{$diskName}</comment>");
        $this->line("Root Path: <comment>{$rootPath}</comment>");
        $this->newLine();
        
        $this->info('Discovering files and syncing database... This may take a while for large buckets.');

        try {
            $stats = $discoveryService->sync($diskName, $rootPath);

            $this->newLine();
            $this->line("Discovered: <info>{$stats['discovered']}</info>");
            $this->line("Created: <info>{$stats['created']}</info>");
            $this->line("Updated: <info>{$stats['updated']}</info>");
            $this->line("Unchanged: <info>{$stats['unchanged']}</info>");
            
            if ($stats['missing'] > 0) {
                $this->line("Missing (stale): <comment>{$stats['missing']}</comment>");
            } else {
                $this->line("Missing: <info>0</info>");
            }
            
            if ($stats['failed'] > 0) {
                $this->line("Failed: <error>{$stats['failed']}</error>");
            } else {
                $this->line("Failed: <info>0</info>");
            }

            $this->newLine();
            
            if ($stats['failed'] > 0) {
                $this->warn('Synchronization completed with some errors. Check Laravel logs for details.');
                return self::FAILURE;
            }
            
            $this->info('Synchronization completed successfully.');
            
            return self::SUCCESS;
            
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('Synchronization failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
