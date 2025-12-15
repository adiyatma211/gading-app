<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupPdfStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:setup-storage {--force : Overwrite existing directories} {--test : Test write permissions after setup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the PDF storage directory structure with proper permissions';

    /**
     * Base storage path for PDFs
     */
    protected $basePath;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Setting up PDF Storage Directory Structure');
        $this->line(str_repeat('=', 50));

        $this->basePath = storage_path('app/pdfs');
        $force = $this->option('force');
        $test = $this->option('test');

        // Create the directory structure
        $this->createDirectoryStructure($force);

        // Create .gitkeep files
        $this->createGitkeepFiles();

        // Test write permissions if requested
        if ($test) {
            $this->testWritePermissions();
        }

        $this->info("\nPDF storage directory structure setup complete!");
        $this->info("Base path: {$this->basePath}");

        return 0;
    }

    /**
     * Create the directory structure
     */
    protected function createDirectoryStructure(bool $force = false): void
    {
        $this->info('Creating directory structure...');

        // Define the directory structure
        $directories = [
            '2025/01_January/01/thermal',
            '2025/01_January/01/invoice',
            '2025/01_January/02',
            '2025/02_February',
            'archive/2023',
            'archive/2024',
        ];

        foreach ($directories as $directory) {
            $path = $this->basePath . '/' . $directory;

            if (is_dir($path) && !$force) {
                $this->line("   Already exists: " . $directory);
                continue;
            }

            if ($this->createDirectory($path, $directory)) {
                $this->line("   Created: " . $directory);
            }
        }
    }

    /**
     * Create a single directory with proper permissions
     */
    protected function createDirectory(string $path, string $displayName): bool
    {
        try {
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Set proper permissions
            chmod($path, 0755);

            return true;
        } catch (\Exception $e) {
            $this->error("   Failed to create " . $displayName . ": " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create .gitkeep files in each directory
     */
    protected function createGitkeepFiles(): void
    {
        $this->info("\nCreating .gitkeep files...");

        // Define all directories that need .gitkeep files
        $directories = [
            '2025',
            '2025/01_January',
            '2025/01_January/01',
            '2025/01_January/01/thermal',
            '2025/01_January/01/invoice',
            '2025/01_January/02',
            '2025/02_February',
            'archive',
            'archive/2023',
            'archive/2024',
        ];

        foreach ($directories as $directory) {
            $dirPath = $this->basePath . '/' . $directory;
            $gitkeepPath = $dirPath . '/.gitkeep';

            try {
                if (!File::exists($gitkeepPath)) {
                    File::put($gitkeepPath, '');
                    $this->line("   Created .gitkeep in: " . $directory);
                } else {
                    $this->line("   .gitkeep already exists in: " . $directory);
                }
            } catch (\Exception $e) {
                $this->error("   Failed to create .gitkeep in " . $directory . ": " . $e->getMessage());
            }
        }
    }

    /**
     * Test write permissions for the created directories
     */
    protected function testWritePermissions(): void
    {
        $this->info("\nTesting write permissions...");

        $testDirectories = [
            '2025/01_January/01/thermal',
            '2025/01_January/01/invoice',
            'archive/2023',
        ];

        foreach ($testDirectories as $directory) {
            $path = $this->basePath . '/' . $directory;
            $testFile = $path . '/permission_test_' . time() . '.tmp';

            try {
                // Test write
                File::put($testFile, 'test');
                $this->line("   Write test passed for: " . $directory);

                // Clean up
                File::delete($testFile);
            } catch (\Exception $e) {
                $this->error("   Write test failed for " . $directory . ": " . $e->getMessage());
            }
        }

        // Test base directory permissions
        try {
            $baseTestFile = $this->basePath . '/base_permission_test_' . time() . '.tmp';
            File::put($baseTestFile, 'test');
            File::delete($baseTestFile);
            $this->line("   Base directory write test passed");
        } catch (\Exception $e) {
            $this->error("   Base directory write test failed: " . $e->getMessage());
        }
    }

    /**
     * Display the directory structure tree
     */
    protected function displayDirectoryTree(): void
    {
        $this->info("\nDirectory Structure:");

        $this->line("storage/app/pdfs/");
        $this->line("├── 2025/");
        $this->line("│   ├── 01_January/");
        $this->line("│   │   ├── 01/");
        $this->line("│   │   │   ├── thermal/");
        $this->line("│   │   │   └── invoice/");
        $this->line("│   │   └── 02/");
        $this->line("│   └── 02_February/");
        $this->line("└── archive/");
        $this->line("    ├── 2023/");
        $this->line("    └── 2024/");
    }
}
