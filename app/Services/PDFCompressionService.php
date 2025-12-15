<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\PdfStorageMetadata;

class PDFCompressionService
{
    protected $tempPath;
    protected $compressionQuality;
    protected $ghostscriptPath;

    public function __construct()
    {
        $this->tempPath = storage_path('app/temp/pdf_compression');
        $this->compressionQuality = config('pdf.compression_quality', 'ebook');
        $this->ghostscriptPath = config('pdf.ghostscript_path', 'gs');

        // Ensure temp directory exists
        if (!File::exists($this->tempPath)) {
            File::makeDirectory($this->tempPath, 0755, true);
        }
    }

    /**
     * Compress PDF using ghostscript
     */
    public function compressPDF(string $inputPath, string $outputPath = null, array $options = []): array
    {
        try {
            if (!File::exists($inputPath)) {
                throw new \Exception("Input file not found: {$inputPath}");
            }

            $originalSize = File::size($inputPath);
            $outputPath = $outputPath ?? $this->generateTempPath();

            // Build ghostscript command
            $command = $this->buildGhostscriptCommand($inputPath, $outputPath, $options);

            // Execute compression
            $exitCode = 0;
            $output = [];
            exec($command, $output, $exitCode);

            if ($exitCode !== 0) {
                throw new \Exception("Ghostscript failed with exit code: {$exitCode}. Output: " . implode("\n", $output));
            }

            if (!File::exists($outputPath)) {
                throw new \Exception("Compression failed - output file not created: {$outputPath}");
            }

            $compressedSize = File::size($outputPath);
            $compressionRatio = round(($originalSize - $compressedSize) / $originalSize * 100, 2);

            // Clean up temp input file if it was created by us
            if (str_starts_with($inputPath, $this->tempPath)) {
                File::delete($inputPath);
            }

            Log::info('PDF compression completed', [
                'input_path' => $inputPath,
                'output_path' => $outputPath,
                'original_size' => $originalSize,
                'compressed_size' => $compressedSize,
                'compression_ratio' => $compressionRatio . '%'
            ]);

            return [
                'success' => true,
                'output_path' => $outputPath,
                'original_size' => $originalSize,
                'compressed_size' => $compressedSize,
                'compression_ratio' => $compressionRatio,
                'size_saved' => $originalSize - $compressedSize
            ];

        } catch (\Exception $e) {
            Log::error('PDF compression failed', [
                'input_path' => $inputPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Clean up temp files
            $this->cleanupTempFiles([$inputPath, $outputPath]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Compress PDF from content string
     */
    public function compressPDFContent(string $pdfContent, array $options = []): array
    {
        try {
            $tempInputPath = $this->generateTempPath();
            $tempOutputPath = $this->generateTempPath();

            // Write content to temp file
            File::put($tempInputPath, $pdfContent);

            // Compress
            $result = $this->compressPDF($tempInputPath, $tempOutputPath, $options);

            if ($result['success']) {
                // Read compressed content
                $compressedContent = File::get($result['output_path']);

                // Clean up temp output file
                File::delete($result['output_path']);

                return [
                    'success' => true,
                    'content' => $compressedContent,
                    'original_size' => $result['original_size'],
                    'compressed_size' => $result['compressed_size'],
                    'compression_ratio' => $result['compression_ratio'],
                    'size_saved' => $result['size_saved']
                ];
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('PDF content compression failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Compress and update stored PDF
     */
    public function compressAndUpdatePDF(int $pdfMetadataId, array $options = []): array
    {
        try {
            $pdfMetadata = PdfStorageMetadata::findOrFail($pdfMetadataId);

            if ($pdfMetadata->is_compressed) {
                return [
                    'success' => false,
                    'error' => 'PDF is already compressed'
                ];
            }

            $storageService = app(PDFStorageService::class);
            $pdfContent = $storageService->getPDF($pdfMetadata->file_path);

            if (!$pdfContent) {
                throw new \Exception("Unable to retrieve PDF content for compression");
            }

            // Compress the PDF
            $compressionResult = $this->compressPDFContent($pdfContent, $options);

            if (!$compressionResult['success']) {
                return $compressionResult;
            }

            // Store compressed version
            $compressedPath = str_replace('.pdf', '_compressed.pdf', $pdfMetadata->file_path);
            $disk = $pdfMetadata->storage_disk;

            Storage::disk($disk)->put($compressedPath, $compressionResult['content']);

            // Update metadata
            $pdfMetadata->is_compressed = true;
            $pdfMetadata->compressed_size_bytes = $compressionResult['compressed_size'];
            $pdfMetadata->metadata = array_merge($pdfMetadata->metadata ?? [], [
                'compression_info' => [
                    'original_size' => $compressionResult['original_size'],
                    'compressed_size' => $compressionResult['compressed_size'],
                    'compression_ratio' => $compressionResult['compression_ratio'],
                    'size_saved' => $compressionResult['size_saved'],
                    'compressed_at' => now()->toISOString(),
                    'compression_settings' => $options
                ]
            ]);
            $pdfMetadata->save();

            // Update transaction if applicable
            if ($pdfMetadata->pdfable_type === 'App\\Models\\transactions') {
                $transaction = \App\Models\transactions::find($pdfMetadata->pdfable_id);
                if ($transaction) {
                    $transaction->pdf_is_compressed = true;
                    $transaction->save();
                }
            }

            Log::info('PDF compressed and updated successfully', [
                'pdf_metadata_id' => $pdfMetadataId,
                'original_size' => $compressionResult['original_size'],
                'compressed_size' => $compressionResult['compressed_size'],
                'compression_ratio' => $compressionResult['compression_ratio']
            ]);

            return [
                'success' => true,
                'compressed_path' => $compressedPath,
                'original_size' => $compressionResult['original_size'],
                'compressed_size' => $compressionResult['compressed_size'],
                'compression_ratio' => $compressionResult['compression_ratio'],
                'size_saved' => $compressionResult['size_saved']
            ];

        } catch (\Exception $e) {
            Log::error('PDF compression and update failed', [
                'pdf_metadata_id' => $pdfMetadataId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Batch compress old PDFs
     */
    public function batchCompressPDFs(int $limit = 50, int $olderThanDays = 7): array
    {
        try {
            $cutoffDate = now()->subDays($olderThanDays);

            $pdfs = PdfStorageMetadata::where('created_at', '<', $cutoffDate)
                ->where('is_compressed', false)
                ->limit($limit)
                ->get();

            $results = [
                'success' => true,
                'processed' => 0,
                'successful' => 0,
                'failed' => 0,
                'total_size_saved' => 0,
                'errors' => []
            ];

            foreach ($pdfs as $pdf) {
                $result = $this->compressAndUpdatePDF($pdf->id);

                $results['processed']++;

                if ($result['success']) {
                    $results['successful']++;
                    $results['total_size_saved'] += $result['size_saved'];
                } else {
                    $results['failed']++;
                    $results['errors'][] = [
                        'pdf_id' => $pdf->id,
                        'file_path' => $pdf->file_path,
                        'error' => $result['error']
                    ];
                }
            }

            Log::info('Batch PDF compression completed', [
                'processed' => $results['processed'],
                'successful' => $results['successful'],
                'failed' => $results['failed'],
                'total_size_saved' => $results['total_size_saved']
            ]);

            return $results;

        } catch (\Exception $e) {
            Log::error('Batch PDF compression failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build ghostscript command
     */
    protected function buildGhostscriptCommand(string $inputPath, string $outputPath, array $options = []): string
    {
        $quality = $options['quality'] ?? $this->compressionQuality;
        $resolution = $options['resolution'] ?? 150;
        $downsampleImages = $options['downsample_images'] ?? true;

        $command = sprintf(
            '"%s" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/%s -dNOPAUSE -dQUIET -dBATCH',
            $this->ghostscriptPath,
            $quality
        );

        if ($downsampleImages) {
            $command .= sprintf(' -dDownScaleColorImages=true -dColorImageResolution=%d -dDownScaleGrayImages=true -dGrayImageResolution=%d -dDownScaleMonoImages=true -dMonoImageResolution=%d',
                $resolution,
                $resolution,
                $resolution
            );
        }

        $command .= sprintf(' -sOutputFile="%s" "%s"', $outputPath, $inputPath);

        return $command;
    }

    /**
     * Generate temporary file path
     */
    protected function generateTempPath(): string
    {
        return $this->tempPath . '/' . uniqid('pdf_comp_', true) . '.pdf';
    }

    /**
     * Clean up temporary files
     */
    protected function cleanupTempFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && File::exists($path) && str_starts_with($path, $this->tempPath)) {
                File::delete($path);
            }
        }
    }

    /**
     * Check if ghostscript is available
     */
    public function checkGhostscriptAvailability(): array
    {
        try {
            $command = sprintf('"%s" --version', $this->ghostscriptPath);
            $output = [];
            $exitCode = 0;
            exec($command, $output, $exitCode);

            if ($exitCode === 0 && !empty($output)) {
                return [
                    'available' => true,
                    'version' => $output[0] ?? 'Unknown',
                    'path' => $this->ghostscriptPath
                ];
            }

            return [
                'available' => false,
                'error' => 'Ghostscript not found or not executable',
                'path' => $this->ghostscriptPath
            ];

        } catch (\Exception $e) {
            return [
                'available' => false,
                'error' => $e->getMessage(),
                'path' => $this->ghostscriptPath
            ];
        }
    }

    /**
     * Get compression statistics
     */
    public function getCompressionStats(): array
    {
        try {
            $totalPDFs = PdfStorageMetadata::count();
            $compressedPDFs = PdfStorageMetadata::where('is_compressed', true)->count();
            $uncompressedPDFs = $totalPDFs - $compressedPDFs;

            $totalOriginalSize = PdfStorageMetadata::sum('file_size_bytes');
            $totalCompressedSize = PdfStorageMetadata::where('is_compressed', true)
                ->sum('compressed_size_bytes');

            $totalSizeSaved = $totalOriginalSize - $totalCompressedSize;
            $overallCompressionRatio = $totalOriginalSize > 0
                ? round(($totalSizeSaved / $totalOriginalSize) * 100, 2)
                : 0;

            return [
                'total_pdfs' => $totalPDFs,
                'compressed_pdfs' => $compressedPDFs,
                'uncompressed_pdfs' => $uncompressedPDFs,
                'compression_rate' => $totalPDFs > 0 ? round(($compressedPDFs / $totalPDFs) * 100, 2) : 0,
                'total_original_size' => $totalOriginalSize,
                'total_compressed_size' => $totalCompressedSize,
                'total_size_saved' => $totalSizeSaved,
                'overall_compression_ratio' => $overallCompressionRatio,
                'average_compression_ratio' => $compressedPDFs > 0
                    ? round($overallCompressionRatio / $compressedPDFs, 2)
                    : 0
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get compression stats', [
                'error' => $e->getMessage()
            ]);

            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
