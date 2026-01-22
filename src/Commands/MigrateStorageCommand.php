<?php

namespace DiegoDrese\StorageMigrator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateStorageCommand extends Command {
    protected $signature = 'storage:migrate
        {--dry-run : Apenas simula a migração sem copiar arquivos}
        {--replace : Substitui arquivos existentes no destino}
        {--prefix= : Migra apenas arquivos que começam com este prefixo (ex: tickets/)}
    ';

    protected $description = 'Migrate files between two storages preserving directory structure';

    public function handle(): int
    {
        $config = config('storage-migrator');

        if (!$config || !isset($config['source'], $config['destination'])) {
            $this->error('storage-migrator config not found or invalid.');
            return self::FAILURE;
        }

        $dryRun  = (bool) $this->option('dry-run');
        $replace = (bool) $this->option('replace');
        $prefix  = $this->option('prefix');

        $this->info('🔄 Laravel Storage Migrator');
        $this->line('----------------------------------');
        $this->line('Source bucket      : ' . ($config['source']['bucket'] ?? '-'));
        $this->line('Destination bucket : ' . ($config['destination']['bucket'] ?? '-'));
        $this->line('Dry run             : ' . ($dryRun ? 'YES' : 'NO'));
        $this->line('Replace files       : ' . ($replace ? 'YES' : 'NO'));
        $this->line('Prefix filter       : ' . ($prefix ?: 'NONE'));
        $this->line('----------------------------------');

        $source = Storage::build($config['source']);
        $dest   = Storage::build($config['destination']);

        $files = $source->allFiles();

        if ($prefix) {
            $files = array_filter($files, fn ($file) => Str::startsWith($file, $prefix));
        }

        if (empty($files)) {
            $this->warn('No files found to migrate.');
            return self::SUCCESS;
        }

        $migrated = 0;
        $skipped  = 0;
        $errors   = 0;

        foreach ($files as $file) {
            try {
                if ($dest->exists($file) && !$replace) {
                    $this->line("⏭️  Skipped (exists): {$file}");
                    $skipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->line("🧪 Dry-run: {$file}");
                    $migrated++;
                    continue;
                }

                $stream = $source->readStream($file);

                if (!$stream) {
                    throw new \RuntimeException('Unable to read file stream');
                }

                $dest->writeStream($file, $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }

                $this->line("✅ Migrated: {$file}");
                $migrated++;

            } catch (\Throwable $e) {
                $this->error("❌ Error migrating {$file}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->line('----------------------------------');
        $this->info('📊 Migration summary');
        $this->line("Migrated : {$migrated}");
        $this->line("Skipped  : {$skipped}");
        $this->line("Errors   : {$errors}");
        $this->line('----------------------------------');

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
