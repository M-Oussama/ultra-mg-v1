<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\AttendanceActiveEmployee;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeCareer;
use App\Models\EmployeeMonthlyPayroll;
use App\Models\EmployeeMonthlyWorkDay;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SupplyItem;
use App\Models\SalesSupplier;
use App\Models\CertifyInvoices;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\YearlyVacation;
use App\Http\Helpers\NumberToLetter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\Process\ExecutableFinder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

class PDFController extends Controller
{
    private function shouldShowCompanyInfo(?Company $company): bool
    {
        return (bool) ($company?->show_company_info ?? true);
    }

    private function resolveShowCompanyInfo(Request $request, ?Company $company, ?bool $fallback = null): bool
    {
        $requested = $request->query('show_company_info');

        if ($requested !== null) {
            $normalized = strtolower(trim((string) $requested));

            if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
                return true;
            }

            if (in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
                return false;
            }
        }

        if ($fallback !== null) {
            return $fallback;
        }

        return $this->shouldShowCompanyInfo($company);
    }

    private function formatAllocatedQuantity(float $quantity): string
    {
        $formatted = number_format($quantity, 2, '.', '');
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted === '' ? '0' : $formatted;
    }

    private function formatRawQuantity(float $quantity): string
    {
        return number_format($quantity, 0, ',', ' ');
    }

    private function resolvedPackageType(SaleItem $saleItem): string
    {
        $type = trim((string) ($saleItem->package_type ?? $saleItem->product?->package_type ?? ''));

        return $type !== '' ? $type : 'package';
    }

    private function resolvedUnitsPerPackage(SaleItem $saleItem): int
    {
        return (int) ($saleItem->units_per_package ?? $saleItem->product?->units_per_package ?? 0);
    }

    private function formatQuantityForDisplay(SaleItem $saleItem, float $quantity): string
    {
        $unitsPerPackage = $this->resolvedUnitsPerPackage($saleItem);

        if ($unitsPerPackage > 0 && $saleItem->hasPackaging()) {
            $packageType = $this->resolvedPackageType($saleItem);
            $packages = (int) floor($quantity / $unitsPerPackage);
            $remainder = (int) round(fmod($quantity, $unitsPerPackage));

            if ($remainder > 0) {
                return $packages . ' ' . $packageType . ' (' . $unitsPerPackage . ') + ' . $remainder . ' pieces';
            }

            return $packages . ' ' . $packageType . ' (' . $unitsPerPackage . ')';
        }

        return $this->formatAllocatedQuantity($quantity);
    }


    private function formatAllocatedStockLine(SaleItem $saleItem, float $quantity, string $reference): string
    {
        if ($saleItem->hasPackaging()) {
            return $this->formatQuantityForDisplay($saleItem, $quantity) . ' - #' . $reference;
        }

        return $this->formatRawQuantity($quantity) . ' - #' . $reference;
    }

    private function recordAllocationLine(
        array &$allocations,
        SaleItem $saleItem,
        float $consumed,
        string $reference
    ): void {
        $referenceKey = ltrim($reference, '#');
        $existingLines = $allocations[$saleItem->id] ?? [];

        if (isset($existingLines[$referenceKey])) {
            $existingLines[$referenceKey]['quantity_value'] += $consumed;
        } else {
            $existingLines[$referenceKey] = [
                'quantity_value' => $consumed,
                'reference' => '#' . $referenceKey,
            ];
        }

        $existingLines[$referenceKey]['quantity_total'] = $this->formatPreparationTotalQuantity(
            $saleItem,
            (float) $existingLines[$referenceKey]['quantity_value']
        );
        $existingLines[$referenceKey]['carton_breakdown'] = $this->formatPreparationCartonBreakdown(
            $saleItem,
            (float) $existingLines[$referenceKey]['quantity_value']
        );

        $allocations[$saleItem->id] = $existingLines;
    }

    private function isFullPackageOrder(SaleItem $saleItem, float $quantity): bool
    {
        if (!$saleItem->hasPackaging()) {
            return false;
        }

        $unitsPerPackage = $this->resolvedUnitsPerPackage($saleItem);
        if ($unitsPerPackage <= 0) {
            return false;
        }

        $remainder = fmod($quantity, $unitsPerPackage);
        return abs($remainder) < 0.00001 || abs($remainder - $unitsPerPackage) < 0.00001;
    }

    private function allocateFromQueue(
        array &$queue,
        SaleItem $saleItem,
        int $saleId,
        float &$remainingToAllocate,
        array &$allocations,
        string $allocationPreference = 'all'
    ): void {
        $unitsPerPackage = $this->resolvedUnitsPerPackage($saleItem);

        foreach ($queue as &$batch) {
            if ($remainingToAllocate <= 0) {
                break;
            }

            $availableQuantity = (float) ($batch['remaining_quantity'] ?? 0);
            if ($availableQuantity <= 0) {
                continue;
            }

            if ($allocationPreference === 'cartons') {
                if (!$saleItem->hasPackaging() || $unitsPerPackage <= 0) {
                    continue;
                }

                $availableQuantity = (float) (floor($availableQuantity / $unitsPerPackage) * $unitsPerPackage);
            } elseif ($allocationPreference === 'loose') {
                if (!$saleItem->hasPackaging() || $unitsPerPackage <= 0) {
                    continue;
                }

                $availableQuantity = fmod($availableQuantity, $unitsPerPackage);
            }

            if ($availableQuantity <= 0) {
                continue;
            }

            $consumed = min($availableQuantity, $remainingToAllocate);
            $batch['remaining_quantity'] -= $consumed;
            $remainingToAllocate -= $consumed;

            if ($consumed > 0 && (int) $saleItem->sale_id === $saleId) {
                $this->recordAllocationLine(
                    $allocations,
                    $saleItem,
                    $consumed,
                    (string) $batch['reference']
                );
            }
        }

        unset($batch);
    }

    private function allocateStockReferencesForSaleItem(
        array &$queues,
        SaleItem $saleItem,
        int $saleId,
        array &$allocations
    ): void {
        $productId = (string) $saleItem->product_id;
        if (!isset($queues[$productId])) {
            return;
        }

        $remainingToAllocate = (float) $saleItem->quantity;

        if (!$saleItem->hasPackaging()) {
            $this->allocateFromQueue($queues[$productId], $saleItem, $saleId, $remainingToAllocate, $allocations, 'all');
            return;
        }

        if ($this->isFullPackageOrder($saleItem, (float) $saleItem->quantity)) {
            $this->allocateFromQueue($queues[$productId], $saleItem, $saleId, $remainingToAllocate, $allocations, 'cartons');
            return;
        }

        $this->allocateFromQueue($queues[$productId], $saleItem, $saleId, $remainingToAllocate, $allocations, 'loose');

        if ($remainingToAllocate > 0) {
            $this->allocateFromQueue($queues[$productId], $saleItem, $saleId, $remainingToAllocate, $allocations, 'cartons');
        }
    }

    private function buildSaleItemStockReferences(Sale $sale): array
    {
        $departmentId = $sale->department_id;
        $saleDate = (string) $sale->sale_date;

        $supplyItems = SupplyItem::query()
            ->select([
                'supply_items.id',
                'supply_items.product_id',
                'supply_items.reference',
                'supply_items.quantity',
                'supplies.id as supply_id',
                'supplies.supply_date',
            ])
            ->join('supplies', 'supplies.id', '=', 'supply_items.supply_id')
            ->whereNull('supply_items.deleted_at')
            ->whereNull('supplies.deleted_at')
            ->when($departmentId !== null, function ($query) use ($departmentId) {
                $query->where('supplies.departement_id', $departmentId);
            })
            ->whereDate('supplies.supply_date', '<=', $saleDate)
            ->orderBy('supplies.supply_date')
            ->orderBy('supplies.id')
            ->orderBy('supply_items.id')
            ->get();

        $saleItems = SaleItem::query()
            ->select([
                'sale_items.id',
                'sale_items.sale_id',
                'sale_items.product_id',
                'sale_items.quantity',
                'sales.sale_date',
            ])
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereNull('sale_items.deleted_at')
            ->whereNull('sales.deleted_at')
            ->when($departmentId !== null, function ($query) use ($departmentId) {
                $query->where('sales.department_id', $departmentId);
            })
            ->where(function ($query) use ($saleDate, $sale) {
                $query->whereDate('sales.sale_date', '<', $saleDate)
                    ->orWhere(function ($innerQuery) use ($saleDate, $sale) {
                        $innerQuery->whereDate('sales.sale_date', '=', $saleDate)
                            ->where('sales.id', '<=', $sale->id);
                    });
            })
            ->orderBy('sales.sale_date')
            ->orderBy('sales.id')
            ->orderBy('sale_items.id')
            ->get();

        $queues = [];
        foreach ($supplyItems as $supplyItem) {
            $productId = (string) $supplyItem->product_id;
            if (!isset($queues[$productId])) {
                $queues[$productId] = [];
            }

            $queues[$productId][] = [
                'reference' => (string) ($supplyItem->reference ?? ''),
                'remaining_quantity' => (float) $supplyItem->quantity,
            ];
        }

        $allocations = [];

        foreach ($saleItems as $saleItem) {
            $this->allocateStockReferencesForSaleItem($queues, $saleItem, (int) $sale->id, $allocations);
        }

        foreach ($allocations as $saleItemId => $lines) {
            if (is_array($lines)) {
                $allocations[$saleItemId] = array_values($lines);
            }
        }

        return $allocations;
    }

    private function formatPreparationTotalQuantity(SaleItem $saleItem, float $quantity): string
    {
        $formatted = $this->formatRawQuantity($quantity);

        if ($saleItem->hasPackaging()) {
            return $formatted . ' pcs';
        }

        return $formatted;
    }

    private function formatPreparationCartonBreakdown(SaleItem $saleItem, float $quantity): string
    {
        if (!$saleItem->hasPackaging()) {
            return $this->formatRawQuantity($quantity);
        }

        $unitsPerPackage = $this->resolvedUnitsPerPackage($saleItem);
        if ($unitsPerPackage <= 0) {
            return $this->formatRawQuantity($quantity);
        }

        $cartons = (int) floor($quantity / $unitsPerPackage);
        $remainder = (int) round(fmod($quantity, $unitsPerPackage));
        $label = $cartons . ' Cartons (' . $unitsPerPackage . ')';

        if ($remainder > 0) {
            $label .= ' + ' . $remainder . ' pcs isolées';
        }

        return $label;
    }

    private function buildPreparationSaleItemStockReferences(Sale $sale): array
    {
        $departmentId = $sale->department_id;
        $saleDate = (string) $sale->sale_date;

        $supplyItems = SupplyItem::query()
            ->select([
                'supply_items.id',
                'supply_items.product_id',
                'supply_items.reference',
                'supply_items.quantity',
                'supplies.id as supply_id',
                'supplies.supply_date',
            ])
            ->join('supplies', 'supplies.id', '=', 'supply_items.supply_id')
            ->whereNull('supply_items.deleted_at')
            ->whereNull('supplies.deleted_at')
            ->when($departmentId !== null, function ($query) use ($departmentId) {
                $query->where('supplies.departement_id', $departmentId);
            })
            ->whereDate('supplies.supply_date', '<=', $saleDate)
            ->orderBy('supplies.supply_date')
            ->orderBy('supplies.id')
            ->orderBy('supply_items.id')
            ->get();

        $saleItems = SaleItem::query()
            ->select([
                'sale_items.id',
                'sale_items.sale_id',
                'sale_items.product_id',
                'sale_items.quantity',
                'sale_items.package_type',
                'sale_items.units_per_package',
                'sales.sale_date',
            ])
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereNull('sale_items.deleted_at')
            ->whereNull('sales.deleted_at')
            ->when($departmentId !== null, function ($query) use ($departmentId) {
                $query->where('sales.department_id', $departmentId);
            })
            ->where(function ($query) use ($saleDate, $sale) {
                $query->whereDate('sales.sale_date', '<', $saleDate)
                    ->orWhere(function ($innerQuery) use ($saleDate, $sale) {
                        $innerQuery->whereDate('sales.sale_date', '=', $saleDate)
                            ->where('sales.id', '<=', $sale->id);
                    });
            })
            ->orderBy('sales.sale_date')
            ->orderBy('sales.id')
            ->orderBy('sale_items.id')
            ->get();

        $queues = [];
        foreach ($supplyItems as $supplyItem) {
            $productId = (string) $supplyItem->product_id;
            if (!isset($queues[$productId])) {
                $queues[$productId] = [];
            }

            $queues[$productId][] = [
                'reference' => (string) ($supplyItem->reference ?? ''),
                'remaining_quantity' => (float) $supplyItem->quantity,
            ];
        }

        $allocations = [];

        foreach ($saleItems as $saleItem) {
            $this->allocateStockReferencesForSaleItem($queues, $saleItem, (int) $sale->id, $allocations);
        }

        foreach ($allocations as $saleItemId => $lines) {
            if (is_array($lines)) {
                $allocations[$saleItemId] = array_values($lines);
            }
        }

        return $allocations;
    }

    private function pdfOptions(): array
    {
        return [
            'defaultFont' => 'DejaVu Sans',
            'isFontSubsettingEnabled' => true,
            'isRemoteEnabled' => true,
        ];
    }

    private function companyInfoContext(): array
    {
        $company = Company::first();

        return [
            'company' => $company,
            'showCompanyInfo' => $this->shouldShowCompanyInfo($company),
        ];
    }

    private function resolveBrowserBinary(): ?string
    {
        $envCandidates = array_filter([
            env('CHROME_BIN'),
            env('CHROMIUM_BIN'),
            env('BROWSER_BIN'),
        ]);

        foreach ($envCandidates as $candidate) {
            if ($this->isUsableBinaryPath((string) $candidate)) {
                return (string) $candidate;
            }
        }

        $pathCandidates = [
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
            'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
            '/usr/bin/chromium',
            '/usr/bin/chromium-browser',
            '/snap/bin/chromium',
            '/usr/bin/microsoft-edge',
            '/usr/bin/microsoft-edge-stable',
        ];

        foreach ($pathCandidates as $candidate) {
            if ($this->isUsableBinaryPath($candidate)) {
                return $candidate;
            }
        }

        $finder = new ExecutableFinder();
        foreach ([
            'google-chrome',
            'google-chrome-stable',
            'chromium',
            'chromium-browser',
            'chrome',
            'msedge',
            'microsoft-edge',
        ] as $binaryName) {
            $found = $finder->find($binaryName);
            if ($found !== false && $this->isUsableBinaryPath($found)) {
                return $found;
            }
        }

        return null;
    }

    private function isUsableBinaryPath(string $path): bool
    {
        return $path !== '' && (is_file($path) || is_executable($path));
    }

    private function toFileUrl(string $path): string
    {
        $normalized = str_replace('\\', '/', $path);

        if (preg_match('/^[A-Za-z]:\\//', $normalized) === 1) {
            return 'file:///' . $normalized;
        }

        return 'file://' . $normalized;
    }

    private function renderHtmlToPdfWithBrowser(string $html, string $prefix, int $pageCount = 1): ?string
    {
        $tempRoot = storage_path('app/tmp');

        if (!is_dir($tempRoot) && !mkdir($tempRoot, 0777, true) && !is_dir($tempRoot)) {
            return null;
        }

        $token = $prefix . '_' . uniqid('', true);
        $htmlPath = $tempRoot . DIRECTORY_SEPARATOR . $token . '.html';
        $pngPath = $tempRoot . DIRECTORY_SEPARATOR . $token . '.png';
        $pdfPath = $tempRoot . DIRECTORY_SEPARATOR . $token . '.pdf';
        $wrapperPath = $tempRoot . DIRECTORY_SEPARATOR . $token . '_wrapper.html';
        $profileDir = $tempRoot . DIRECTORY_SEPARATOR . $token . '_profile';

        if (!is_dir($profileDir) && !mkdir($profileDir, 0777, true) && !is_dir($profileDir)) {
            return null;
        }

        $browserBinary = null;

        try {
            $htmlWritten = file_put_contents($htmlPath, $html) !== false;

            $browserBinary = $this->resolveBrowserBinary();

            if ($htmlWritten && $browserBinary !== null) {
                $fileUrl = $this->toFileUrl(realpath($htmlPath) ?: $htmlPath);
                $headlessFlags = ['--headless=new', '--headless'];
                $linuxFlags = PHP_OS_FAMILY === 'Windows'
                    ? []
                    : ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'];

                $pageCount = max(1, $pageCount);
                $viewportHeight = 1754 * $pageCount;
                $pageImages = [];

                foreach ($headlessFlags as $headlessFlag) {
                    $screenshotProcess = new Process([
                        $browserBinary,
                        $headlessFlag,
                        '--lang=ar',
                        '--disable-gpu',
                        '--hide-scrollbars',
                        '--no-first-run',
                        '--no-default-browser-check',
                        '--disable-extensions',
                        '--allow-file-access-from-files',
                        '--run-all-compositor-stages-before-draw',
                        ...$linuxFlags,
                        '--window-size=1240,' . $viewportHeight,
                        '--screenshot=' . $pngPath,
                        '--user-data-dir=' . $profileDir,
                        $fileUrl,
                    ]);

                    $screenshotProcess->setTimeout(180);
                    $screenshotProcess->run();

                    if ($screenshotProcess->isSuccessful() && File::exists($pngPath) && File::size($pngPath) > 0) {
                        $pageImages = $this->sliceCertificateScreenshot($pngPath, $pageCount);
                        if (empty($pageImages)) {
                            continue;
                        }

                        $wrapperHtml = $this->buildCertificateImageWrapperHtml($pageImages);
                        file_put_contents($wrapperPath, $wrapperHtml);

                        $pdfProcess = new Process([
                            $browserBinary,
                            $headlessFlag,
                            '--lang=ar',
                            '--disable-gpu',
                            '--hide-scrollbars',
                            '--no-first-run',
                            '--no-default-browser-check',
                            '--disable-extensions',
                            '--allow-file-access-from-files',
                            ...$linuxFlags,
                            '--window-size=1240,' . $viewportHeight,
                            '--print-to-pdf=' . $pdfPath,
                            '--no-pdf-header-footer',
                            '--print-to-pdf-no-header',
                            '--user-data-dir=' . $profileDir,
                            $this->toFileUrl(realpath($wrapperPath) ?: $wrapperPath),
                        ]);

                        $pdfProcess->setTimeout(180);
                        $pdfProcess->run();

                        if ($pdfProcess->isSuccessful() && File::exists($pdfPath) && File::size($pdfPath) > 0) {
                            File::delete($htmlPath);
                            File::delete($pngPath);
                            File::delete($wrapperPath);
                            foreach (glob(dirname($pngPath) . DIRECTORY_SEPARATOR . pathinfo($pngPath, PATHINFO_FILENAME) . '_page_*.png') ?: [] as $segmentPath) {
                                File::delete($segmentPath);
                            }
                            File::deleteDirectory($profileDir);

                            return $pdfPath;
                        }

                        if (File::exists($pdfPath)) {
                            File::delete($pdfPath);
                        }
                    }

                    if (File::exists($pngPath)) {
                        File::delete($pngPath);
                    }
                    if (File::exists($wrapperPath)) {
                        File::delete($wrapperPath);
                    }
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('Vacation PDF browser render failed; using Dompdf fallback.', [
                'error' => $e->getMessage(),
                'prefix' => $prefix,
            ]);
        }

        if (File::exists($pdfPath)) {
            File::delete($pdfPath);
        }
        if (File::exists($pngPath)) {
            File::delete($pngPath);
        }
        if (File::exists($wrapperPath)) {
            File::delete($wrapperPath);
        }
        foreach (glob(dirname($pngPath) . DIRECTORY_SEPARATOR . pathinfo($pngPath, PATHINFO_FILENAME) . '_page_*.png') ?: [] as $segmentPath) {
            File::delete($segmentPath);
        }
        File::delete($htmlPath);
        File::deleteDirectory($profileDir);

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function sliceCertificateScreenshot(string $pngPath, int $pageCount): array
    {
        $pageCount = max(1, $pageCount);
        $source = @imagecreatefrompng($pngPath);

        if ($source === false) {
            return [];
        }

        $width = imagesx($source);
        $height = imagesy($source);
        if ($width <= 0 || $height <= 0) {
            imagedestroy($source);
            return [];
        }

        $segments = [];
        $pageHeight = 1754;
        $splitY = null;

        if ($pageCount > 1) {
            $splitY = $this->detectCertificateSplitY($source);
        }

        if ($pageCount === 1 || $splitY === null) {
            $pageCrop = min($pageHeight, $height);
            $segments = [$this->createCertificatePageImage($source, $pngPath, 1, 0, $pageCrop, $pageHeight)];
        } else {
            $secondStart = max(0, min($height - 1, $splitY));
            $firstHeight = max(1, min($pageHeight, $secondStart));
            $secondHeight = max(1, min($pageHeight, $height - $secondStart));

            $firstPath = $this->createCertificatePageImage($source, $pngPath, 1, 0, $firstHeight, $pageHeight);
            $secondPath = $this->createCertificatePageImage($source, $pngPath, 2, $secondStart, $secondHeight, $pageHeight);

            if ($firstPath !== null) {
                $segments[] = $firstPath;
            }
            if ($secondPath !== null) {
                $segments[] = $secondPath;
            }
        }

        imagedestroy($source);

        return $segments;
    }

    private function detectCertificateSplitY($source): ?int
    {
        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= 0 || $height <= 0) {
            return null;
        }

        $contentRows = [];
        $sampleStep = max(1, (int) floor($width / 160));

        for ($y = 0; $y < $height; $y++) {
            $hasContent = false;

            for ($x = 0; $x < $width; $x += $sampleStep) {
                $color = imagecolorat($source, $x, $y);
                $rgb = imagecolorsforindex($source, $color);

                if ($rgb['r'] < 250 || $rgb['g'] < 250 || $rgb['b'] < 250) {
                    $hasContent = true;
                    break;
                }
            }

            if ($hasContent) {
                $contentRows[] = $y;
            }
        }

        if (count($contentRows) < 2) {
            return null;
        }

        $clusters = [];
        $clusterStart = $contentRows[0];
        $clusterEnd = $contentRows[0];

        foreach (array_slice($contentRows, 1) as $row) {
            if ($row - $clusterEnd > 120) {
                $clusters[] = [$clusterStart, $clusterEnd];
                $clusterStart = $row;
                $clusterEnd = $row;
                continue;
            }

            $clusterEnd = $row;
        }

        $clusters[] = [$clusterStart, $clusterEnd];

        if (count($clusters) < 2) {
            return null;
        }

        return (int) round(($clusters[0][1] + $clusters[1][0]) / 2);
    }

    private function createCertificatePageImage($source, string $pngPath, int $pageIndex, int $offsetY, int $cropHeight, int $pageHeight): ?string
    {
        $width = imagesx($source);
        $height = imagesy($source);

        if ($width <= 0 || $height <= 0 || $cropHeight <= 0) {
            return null;
        }

        $offsetY = max(0, min($height - 1, $offsetY));
        $cropHeight = max(1, min($cropHeight, $height - $offsetY));

        $crop = imagecreatetruecolor($width, $cropHeight);
        $white = imagecolorallocate($crop, 255, 255, 255);
        imagefill($crop, 0, 0, $white);
        imagealphablending($crop, true);
        imagesavealpha($crop, true);
        imagecopy($crop, $source, 0, 0, 0, $offsetY, $width, $cropHeight);

        $canvas = imagecreatetruecolor($width, $pageHeight);
        $canvasWhite = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $canvasWhite);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);
        imagecopy($canvas, $crop, 0, 0, 0, 0, $width, min($cropHeight, $pageHeight));

        $segmentPath = dirname($pngPath) . DIRECTORY_SEPARATOR . pathinfo($pngPath, PATHINFO_FILENAME) . '_page_' . $pageIndex . '.png';
        imagepng($canvas, $segmentPath);

        imagedestroy($crop);
        imagedestroy($canvas);

        if (File::exists($segmentPath) && File::size($segmentPath) > 0) {
            return $segmentPath;
        }

        if (File::exists($segmentPath)) {
            File::delete($segmentPath);
        }

        return null;
    }

    /**
     * @param array<int, string> $pageImages
     */
    private function buildCertificateImageWrapperHtml(array $pageImages): string
    {
        $pagesHtml = '';

        foreach ($pageImages as $index => $pageImage) {
            $imageBytes = File::get($pageImage);
            $imageBase64 = base64_encode($imageBytes);
            $pagesHtml .= '<div class="page" style="' . ($index < count($pageImages) - 1 ? 'page-break-after: always;' : '') . '">'
                . '<img src="data:image/png;base64,' . $imageBase64 . '" alt="Vacation certificate page ' . ($index + 1) . '">'
                . '</div>';
        }

        return '<!DOCTYPE html><html lang="ar"><head><meta charset="UTF-8"><style>'
            . '@page{margin:0;size:A4 portrait;}'
            . 'html,body{margin:0;padding:0;background:#ffffff;}'
            . '.page{width:210mm;height:297mm;page-break-after:always;break-after:page;}'
            . '.page:last-child{page-break-after:auto;break-after:auto;}'
            . 'img{display:block;width:210mm;height:297mm;}'
            . '</style></head><body>' . $pagesHtml . '</body></html>';
    }

    private function departmentHeaderContext(Request $request): array
    {
        $company = Company::first();
        $showCompanyInfo = $this->shouldShowCompanyInfo($company);
        $businessId = (int) $request->query('business_id', $request->query('department_id', 0));

        $departmentColumns = ['name'];
        foreach (['profession', 'address', 'phone', 'email', 'logo_url', 'logo'] as $optionalColumn) {
            if (Schema::hasColumn('departments', $optionalColumn)) {
                $departmentColumns[] = $optionalColumn;
            }
        }

        $department = $businessId > 0
            ? DB::table('departments')->select($departmentColumns)->where('id', $businessId)->first()
            : null;

        $departmentName = $department?->name ?? ($company->name ?? '');
        $departmentProfession = $department?->profession ?? null;
        $departmentAddress = $department?->address ?? ($company->address ?? '');
        $departmentPhone = $department?->phone ?? ($company->phone ?? '');
        $departmentEmail = $department?->email ?? ($company->email ?? '');

        $logoPath = $department?->logo_url ?? ($department?->logo ?? null);
        $logoAbsolutePath = null;
        if (!empty($logoPath)) {
            $cleanPath = ltrim((string) $logoPath, '/\\');
            $publicCandidate = public_path($cleanPath);
            $storageCandidate = storage_path('app/public/' . $cleanPath);

            if (file_exists($publicCandidate)) {
                $logoAbsolutePath = $publicCandidate;
            } elseif (file_exists($storageCandidate)) {
                $logoAbsolutePath = $storageCandidate;
            }
        }

        return compact(
            'company',
            'showCompanyInfo',
            'businessId',
            'departmentName',
            'departmentProfession',
            'departmentAddress',
            'departmentPhone',
            'departmentEmail',
            'logoAbsolutePath'
        );
    }

    public function exportProductsList(Request $request)
    {
        $searchValue = trim((string) $request->query('searchValue', ''));
        $departmentId = $request->query('department_id');
        $lowStockOnly = filter_var($request->query('low_stock', false), FILTER_VALIDATE_BOOLEAN);

        $products = Product::with(['productStock', 'department'])
            ->when($searchValue !== '', function ($query) use ($searchValue) {
                $query->where(function ($innerQuery) use ($searchValue) {
                    $innerQuery->where('name', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('brand', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('SKU', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('product_code', 'LIKE', '%' . $searchValue . '%');
                });
            })
            ->when($departmentId !== null && $departmentId !== '', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->when($lowStockOnly, function ($query) {
                $query->where('stockable', true)
                    ->whereHas('productStock', function ($stockQuery) {
                        $stockQuery->whereColumn('quantity', '<=', 'products.min_stock_level');
                    });
            })
            ->orderBy('name')
            ->get();

        $context = $this->companyInfoContext();
        $pdf = Pdf::loadView('exports.products_list_pdf', array_merge($context, [
            'products' => $products,
            'searchValue' => $searchValue,
            'departmentId' => $departmentId,
            'lowStockOnly' => $lowStockOnly,
        ]));

        $pdf->setPaper('a4', 'portrait')->setOptions($this->pdfOptions());

        return $pdf->download('Products_List.pdf');
    }

    public function exportClientsList(Request $request)
    {
        $searchValue = trim((string) $request->query('searchValue', ''));
        $departmentId = $request->query('department_id');

        $query = Client::query()
            ->with(['city', 'department', 'balance', 'sales', 'payments'])
            ->withSum(['sales as total_spent' => function ($salesQuery) use ($departmentId) {
                if ($departmentId !== null && $departmentId !== '') {
                    $salesQuery->where('department_id', $departmentId);
                }
            }], 'total_amount');

        $user = auth()->user();
        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                $deptIds = $user->departments->pluck('id')->toArray();
                $query->whereIn('department_id', $deptIds);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        if ($searchValue !== '') {
            $query->where(function ($innerQuery) use ($searchValue) {
                $innerQuery->where('name', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('surname', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('phone', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchValue . '%');
            });
        }

        if ($departmentId !== null && $departmentId !== '') {
            $query->where('department_id', $departmentId);
        }

        $clients = $query->orderBy('name')->get();

        foreach ($clients as $client) {
            $this->calculateClientBalance($client);
        }

        $context = $this->companyInfoContext();
        $pdf = Pdf::loadView('exports.clients_list_pdf', array_merge($context, [
            'clients' => $clients,
            'searchValue' => $searchValue,
            'departmentId' => $departmentId,
        ]));

        $pdf->setPaper('a4', 'portrait')->setOptions($this->pdfOptions());

        return $pdf->download('Clients_List.pdf');
    }

    public function exportSuppliersList(Request $request)
    {
        $searchValue = trim((string) $request->query('searchValue', ''));

        $suppliers = Supplier::with(['city'])
            ->when($searchValue !== '', function ($query) use ($searchValue) {
                $query->where(function ($innerQuery) use ($searchValue) {
                    $innerQuery->where('name', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('surname', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('phone', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('email', 'LIKE', '%' . $searchValue . '%');
                });
            })
            ->orderBy('name')
            ->get();

        $context = $this->companyInfoContext();
        $pdf = Pdf::loadView('exports.suppliers_list_pdf', array_merge($context, [
            'suppliers' => $suppliers,
            'searchValue' => $searchValue,
        ]));

        $pdf->setPaper('a4', 'portrait')->setOptions($this->pdfOptions());

        return $pdf->download('Suppliers_List.pdf');
    }

    public function exportSalesSuppliersList(Request $request)
    {
        $searchValue = trim((string) $request->query('searchValue', ''));
        $departmentId = $request->query('department_id', $request->query('departement_id'));

        $suppliers = SalesSupplier::with(['city', 'department'])
            ->when($departmentId !== null && $departmentId !== '', function ($query) use ($departmentId) {
                $query->where('departement_id', $departmentId);
            })
            ->when($searchValue !== '', function ($query) use ($searchValue) {
                $query->where(function ($innerQuery) use ($searchValue) {
                    $innerQuery->where('name', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('surname', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('full_name', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('phone', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('email', 'LIKE', '%' . $searchValue . '%');
                });
            })
            ->orderBy('name')
            ->get();

        $context = $this->companyInfoContext();
        $pdf = Pdf::loadView('exports.suppliers_list_pdf', array_merge($context, [
            'suppliers' => $suppliers,
            'searchValue' => $searchValue,
        ]));

        $pdf->setPaper('a4', 'portrait')->setOptions($this->pdfOptions());

        return $pdf->download('Sales_Suppliers_List.pdf');
    }

    public function exportEmployeesList(Request $request)
    {
        $searchValue = trim((string) $request->query('searchValue', ''));

        $employees = Employee::with(['birthCity', 'cardIssuedCity'])
            ->when($searchValue !== '', function ($query) use ($searchValue) {
                $query->where(function ($innerQuery) use ($searchValue) {
                    $innerQuery->where('name', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('surname', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('email', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('phone', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('NIN', 'LIKE', '%' . $searchValue . '%');
                });
            })
            ->orderBy('name')
            ->get();

        $context = $this->companyInfoContext();
        $pdf = Pdf::loadView('exports.employees_list_pdf', array_merge($context, [
            'employees' => $employees,
            'searchValue' => $searchValue,
        ]));

        $pdf->setPaper('a4', 'landscape')->setOptions($this->pdfOptions());

        return $pdf->download('Employees_List.pdf');
    }

    public function exportPayrollEmployeesList(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $activeIds = AttendanceActiveEmployee::query()
            ->where('month', $month)
            ->where('year', $year)
            ->where('is_active', true)
            ->pluck('employee_id')
            ->values()
            ->all();

        $payrollEmployeeIds = EmployeeMonthlyPayroll::query()
            ->where('month', $month)
            ->where('year', $year)
            ->pluck('employee_id')
            ->values()
            ->all();

        $employeeIds = !empty($activeIds) ? $activeIds : $payrollEmployeeIds;

        $employees = Employee::with(['birthCity', 'cardIssuedCity'])
            ->whereIn('id', $employeeIds)
            ->orderBy('name')
            ->get();

        $context = $this->companyInfoContext();
        $pdf = Pdf::loadView('exports.employees_list_pdf', array_merge($context, [
            'employees' => $employees,
            'searchValue' => '',
            'title' => 'Payroll Employees List',
            'subtitle' => sprintf('%02d/%04d payroll roster', $month, $year),
            'scopeLabel' => sprintf('Payroll month: %02d/%04d', $month, $year),
            'month' => $month,
            'year' => $year,
        ]));

        $pdf->setPaper('a4', 'landscape')->setOptions($this->pdfOptions());

        return $pdf->download(sprintf('Payroll_Employees_%04d_%02d.pdf', $year, $month));
    }

    public function exportEmployeeHistory(Request $request, $id)
    {
        $employee = Employee::with([
            'birthCity',
            'cardIssuedCity',
            'employeeCareer' => function ($query) {
                $query->orderBy('start_date')->orderBy('id');
            },
        ])->findOrFail($id);

        $summaryResponse = app(VacationController::class)->getEmployeeVacationSummary($id);
        $summary = json_decode($summaryResponse->getContent(), true) ?? [];
        $careerSummaryIndex = collect($summary['careers'] ?? [])->keyBy('employee_career_id');

        $periods = $employee->employeeCareer
            ->map(function (EmployeeCareer $career) use ($careerSummaryIndex) {
                $careerSummary = $careerSummaryIndex->get($career->id, []);

                return [
                    'id' => $career->id,
                    'position' => $career->position ?: '-',
                    'start_date' => $career->start_date,
                    'end_date' => $career->end_date,
                    'real_start_date' => $career->real_start_date,
                    'real_end_date' => $career->real_end_date,
                    'worked_days' => (int) ($careerSummary['worked_days'] ?? 0),
                    'worked_months' => (float) ($careerSummary['worked_months'] ?? 0),
                    'accrued_days' => (float) ($careerSummary['accrued_days'] ?? 0),
                    'used_days' => (float) ($careerSummary['used_days'] ?? 0),
                    'balance_days' => (float) ($careerSummary['balance_days'] ?? 0),
                    'vacation_count' => (int) ($careerSummary['vacation_count'] ?? 0),
                    'group_id' => $careerSummary['group_id'] ?? null,
                    'is_closed' => (bool) ($careerSummary['is_closed'] ?? false),
                ];
            })
            ->values()
            ->all();

        $vacations = YearlyVacation::with(['employee_career'])
            ->where('employee_id', $employee->id)
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();

        $vacationRows = $vacations->map(function (YearlyVacation $vacation) {
            return [
                'id' => $vacation->id,
                'start_date' => $vacation->start_date,
                'end_date' => $vacation->end_date,
                'count' => (int) $vacation->count,
                'position' => $vacation->employee_career?->position ?? $vacation->employee_career?->position_ar ?? '-',
                'career_id' => $vacation->employee_career_id,
            ];
        })->values()->all();

        $context = array_merge($this->companyInfoContext(), [
            'generatedAt' => now(),
            'summary' => $summary,
            'periods' => $periods,
            'vacations' => $vacationRows,
            'employee' => $employee,
        ]);

        $pdf = Pdf::loadView('exports.employee_history_pdf', $context);
        $pdf->setPaper('a4', 'landscape')->setOptions($this->pdfOptions());

        return $pdf->download('Employee_History_' . $employee->id . '.pdf');
    }

    public function exportVacationCertificate(Request $request, int $careerId)
    {
        $career = EmployeeCareer::with(['employee'])->findOrFail($careerId);

        if (!$career->employee) {
            abort(404, 'Employee not found');
        }

        $variant = strtolower(trim((string) $request->query('variant', 'signed')));
        if (!in_array($variant, ['signed', 'blank', 'both'], true)) {
            $variant = 'signed';
        }

        $vacation = null;
        if ($variant !== 'blank') {
            $vacationId = $request->query('vacation_id');

            if ($vacationId !== null && $vacationId !== '') {
                $vacation = YearlyVacation::query()
                    ->where('employee_id', $career->employee_id)
                    ->where('employee_career_id', $career->id)
                    ->find((int) $vacationId);
            } else {
                $vacation = YearlyVacation::query()
                    ->where('employee_id', $career->employee_id)
                    ->where('employee_career_id', $career->id)
                    ->orderByDesc('start_date')
                    ->orderByDesc('id')
                    ->first();
            }

            if (!$vacation) {
                abort(404, 'Vacation record not found');
            }
        }

        $context = array_merge($this->companyInfoContext(), [
            'generatedAt' => now(),
            'career' => $career,
            'employee' => $career->employee,
            'vacation' => $vacation,
            'variant' => $variant,
            'certificateNumber' => $vacation !== null
                ? $this->buildVacationCertificateNumber($vacation)
                : null,
        ]);

        $suffix = $variant === 'both' ? 'bundle' : $variant;
        $downloadName = sprintf('Vacation_Certificate_%d_%s.pdf', $career->id, $suffix);

        $html = view('exports.vacation_certificate_pdf', $context)->render();
        $browserPdfPath = $this->renderHtmlToPdfWithBrowser(
            $html,
            'vacation_certificate_' . $career->id . '_' . $suffix,
            $variant === 'both' ? 2 : 1
        );

        if ($browserPdfPath !== null) {
            try {
                $browserPdfContent = File::get($browserPdfPath);
                File::delete($browserPdfPath);

                return response()->streamDownload(
                    static function () use ($browserPdfContent): void {
                        echo $browserPdfContent;
                    },
                    $downloadName,
                    ['Content-Type' => 'application/pdf']
                );
            } catch (\Throwable $e) {
                logger()->warning('Vacation PDF browser stream failed; using Dompdf fallback.', [
                    'error' => $e->getMessage(),
                    'career_id' => $career->id,
                ]);

                if (File::exists($browserPdfPath)) {
                    File::delete($browserPdfPath);
                }
            }
        }

        $pdf = Pdf::loadView('exports.vacation_certificate_pdf', $context);
        $pdf->setPaper('a4', 'portrait')->setOptions($this->pdfOptions());

        return $pdf->download($downloadName);
    }

    private function buildVacationCertificateNumber(YearlyVacation $vacation): string
    {
        $seed = implode('|', [
            'vacation-certificate',
            (string) $vacation->id,
            (string) $vacation->employee_id,
            (string) $vacation->employee_career_id,
            (string) optional($vacation->created_at)->timestamp,
        ]);

        $hash = hash_hmac('sha256', $seed, (string) config('app.key'));
        $compact = (int) (hexdec(substr($hash, 0, 8)) % 10000);

        return '00' . str_pad((string) $compact, 4, '0', STR_PAD_LEFT);
    }

    public function exportEmployeeBlankContract(int $careerId)
    {
        $career = EmployeeCareer::with([
            'employee',
            'employee.birthCity',
            'employee.cardIssuedCity',
        ])->findOrFail($careerId);

        if (!$career->employee) {
            abort(404, 'Employee not found');
        }

        $context = array_merge($this->companyInfoContext(), [
            'generatedAt' => now(),
            'career' => $career,
            'employee' => $career->employee,
        ]);

        $downloadName = sprintf('Blank_Contract_%d.pdf', $career->id);

        $html = view('exports.employee_blank_contract_pdf', $context)->render();
        $browserPdfPath = $this->renderHtmlToPdfWithBrowser($html, 'blank_contract_' . $career->id, 1);

        if ($browserPdfPath !== null) {
            try {
                $browserPdfContent = File::get($browserPdfPath);
                File::delete($browserPdfPath);

                return response()->streamDownload(
                    static function () use ($browserPdfContent): void {
                        echo $browserPdfContent;
                    },
                    $downloadName,
                    ['Content-Type' => 'application/pdf']
                );
            } catch (\Throwable $e) {
                logger()->warning('Blank contract browser stream failed; using Dompdf fallback.', [
                    'error' => $e->getMessage(),
                    'career_id' => $career->id,
                ]);

                if (File::exists($browserPdfPath)) {
                    File::delete($browserPdfPath);
                }
            }
        }

        $pdf = Pdf::loadView('exports.employee_blank_contract_pdf', $context);
        $pdf->setPaper('a4', 'portrait')->setOptions($this->pdfOptions());

        return $pdf->download($downloadName);
    }

    public function exportAttendancesList(Request $request)
    {
        $searchValue = trim((string) $request->query('searchValue', ''));

        $attendances = Attendance::query()
            ->withCount('employees')
            ->when($searchValue !== '', function ($query) use ($searchValue) {
                $query->where(function ($innerQuery) use ($searchValue) {
                    $innerQuery->where('month', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('year', 'LIKE', '%' . $searchValue . '%')
                        ->orWhereRaw("CONCAT(month, '/', year) LIKE ?", ['%' . $searchValue . '%']);
                });
            })
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $context = $this->companyInfoContext();
        $pdf = Pdf::loadView('exports.attendances_list_pdf', array_merge($context, [
            'attendances' => $attendances,
            'searchValue' => $searchValue,
        ]));

        $pdf->setPaper('a4', 'portrait')->setOptions($this->pdfOptions());

        return $pdf->download('Attendances_List.pdf');
    }

    public function exportMonthlyWorkDays(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $activeIds = AttendanceActiveEmployee::query()
            ->where('month', $month)
            ->where('year', $year)
            ->where('is_active', true)
            ->pluck('employee_id')
            ->values()
            ->all();

        $employees = Employee::query()
            ->whereIn('id', $activeIds)
            ->orderBy('name')
            ->get();

        $entries = EmployeeMonthlyWorkDay::query()
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('employee_id');

        $employees->each(function (Employee $employee) use ($entries) {
            $employee->setAttribute('work_days', (int) ($entries[$employee->id]->work_days ?? 0));
        });

        $context = $this->companyInfoContext();
        $pdf = Pdf::loadView('exports.employee_monthly_work_days_pdf', array_merge($context, [
            'employees' => $employees,
            'month' => $month,
            'year' => $year,
        ]));

        $pdf->setPaper('a4', 'portrait')->setOptions($this->pdfOptions());

        return $pdf->download('Employee_Monthly_Work_Days.pdf');
    }

    public function exportPayrollSheet(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $activeIds = AttendanceActiveEmployee::query()
            ->where('month', $month)
            ->where('year', $year)
            ->where('is_active', true)
            ->pluck('employee_id')
            ->values()
            ->all();

        $payrollEmployeeIds = EmployeeMonthlyPayroll::query()
            ->where('month', $month)
            ->where('year', $year)
            ->pluck('employee_id')
            ->values()
            ->all();

        $employeeIds = !empty($activeIds) ? $activeIds : $payrollEmployeeIds;

        $employees = Employee::query()
            ->whereIn('id', $employeeIds)
            ->orderBy('name')
            ->get();

        $workDayEntries = EmployeeMonthlyWorkDay::query()
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('employee_id');

        $payrollEntries = EmployeeMonthlyPayroll::query()
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('employee_id');

        $totals = [
            'gross_salary' => 0,
            'objectives' => 0,
            'total_payable' => 0,
        ];

        $employees->each(function (Employee $employee) use ($workDayEntries, $payrollEntries, &$totals) {
            $payroll = $payrollEntries[$employee->id] ?? null;
            $workDays = (int) ($workDayEntries[$employee->id]->work_days ?? 0);
            $monthlySalary = (float) ($payroll->monthly_salary ?? 0);
            $objectives = (float) ($payroll->objectives_amount ?? 0);
            $salaryPart = $monthlySalary * ($workDays / 30);
            $total = $salaryPart + $objectives;

            $employee->setAttribute('work_days', $workDays);
            $employee->setAttribute('monthly_salary', $monthlySalary);
            $employee->setAttribute('objectives_amount', $objectives);
            $employee->setAttribute('salary_part', $salaryPart);
            $employee->setAttribute('total_payable', $total);

            $totals['gross_salary'] += $salaryPart;
            $totals['objectives'] += $objectives;
            $totals['total_payable'] += $total;
        });

        $context = $this->companyInfoContext();
        $pdf = Pdf::loadView('exports.employee_payroll_sheet_pdf', array_merge($context, [
            'employees' => $employees,
            'month' => $month,
            'year' => $year,
            'totals' => $totals,
        ]));

        $pdf->setPaper('a4', 'landscape')->setOptions($this->pdfOptions());

        return $pdf->download('Employee_Payroll_Sheet.pdf');
    }

    public function exportStockReport(Request $request)
    {
        $stockResponse = app(SupplyController::class)->getStockBatches($request);
        $stockData = json_decode($stockResponse->getContent(), true) ?? [];

        $context = $this->departmentHeaderContext($request);
        $pdf = Pdf::loadView('exports.stock_report_pdf', array_merge($context, [
            'summary' => $stockData['summary'] ?? [],
            'products' => $stockData['products'] ?? [],
            'containers' => $stockData['containers'] ?? [],
            'search' => trim((string) $request->query('search', '')),
        ]));

        $pdf->setPaper('a4', 'landscape')->setOptions($this->pdfOptions());

        return $pdf->download('Stock_Report.pdf');
    }

    public function exportSaleDeliveryNote(Request $request, $id)
    {
        $sale = Sale::with(['client', 'saleItems.product'])->findOrFail($id);
        $businessId = (int) $request->query('business_id', $sale->department_id ?? 0);

        $company = Company::first();
        $showCompanyInfo = $this->resolveShowCompanyInfo(
            $request,
            $company,
            $sale->show_company_info ?? null
        );
        $amountLetter = $this->convertAmoutToLetter((float) $sale->total_amount);
        $saleItemStockReferences = $this->buildPreparationSaleItemStockReferences($sale);

        $departmentColumns = ['name'];
        foreach (['profession', 'address', 'phone', 'email', 'logo_url', 'logo'] as $optionalColumn) {
            if (Schema::hasColumn('departments', $optionalColumn)) {
                $departmentColumns[] = $optionalColumn;
            }
        }

        $department = $businessId > 0
            ? DB::table('departments')->select($departmentColumns)->where('id', $businessId)->first()
            : null;

        $departmentName = $department?->name ?? ($company->name ?? '');
        $departmentProfession = $department?->profession ?? null;
        $departmentAddress = $department?->address ?? ($company->address ?? '');
        $departmentPhone = $department?->phone ?? ($company->phone ?? '');
        $departmentEmail = $department?->email ?? ($company->email ?? '');

        $logoPath = $department?->logo_url ?? ($department?->logo ?? null);
        $logoAbsolutePath = null;
        if (!empty($logoPath)) {
            $cleanPath = ltrim((string) $logoPath, '/\\');
            $publicCandidate = public_path($cleanPath);
            $storageCandidate = storage_path('app/public/' . $cleanPath);
            if (file_exists($publicCandidate)) {
                $logoAbsolutePath = $publicCandidate;
            } elseif (file_exists($storageCandidate)) {
                $logoAbsolutePath = $storageCandidate;
            }
        }

        $pdf = Pdf::loadView('sale_delivery_pdf', compact(
            'sale',
            'amountLetter',
            'businessId',
            'departmentName',
            'departmentProfession',
            'departmentAddress',
            'departmentPhone',
            'departmentEmail',
            'logoAbsolutePath',
            'showCompanyInfo',
            'saleItemStockReferences'
        ));

        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download('Bon_de_livraison_' . $sale->id . '.pdf');
    }

    public function exportSalePreparationNote(Request $request, $id)
    {
        $sale = Sale::with(['saleItems.product'])->findOrFail($id);
        $businessId = (int) $request->query('business_id', $sale->department_id ?? 0);

        $company = Company::first();
        $showCompanyInfo = $this->resolveShowCompanyInfo(
            $request,
            $company,
            $sale->show_company_info ?? null
        );
        $saleItemStockReferences = $this->buildSaleItemStockReferences($sale);

        $departmentColumns = ['name'];
        foreach (['profession', 'address', 'phone', 'email', 'logo_url', 'logo'] as $optionalColumn) {
            if (Schema::hasColumn('departments', $optionalColumn)) {
                $departmentColumns[] = $optionalColumn;
            }
        }

        $department = $businessId > 0
            ? DB::table('departments')->select($departmentColumns)->where('id', $businessId)->first()
            : null;

        $departmentName = $department?->name ?? ($company->name ?? '');
        $departmentProfession = $department?->profession ?? null;
        $departmentAddress = $department?->address ?? ($company->address ?? '');
        $departmentPhone = $department?->phone ?? ($company->phone ?? '');
        $departmentEmail = $department?->email ?? ($company->email ?? '');

        $logoPath = $department?->logo_url ?? ($department?->logo ?? null);
        $logoAbsolutePath = null;
        if (!empty($logoPath)) {
            $cleanPath = ltrim((string) $logoPath, '/\\');
            $publicCandidate = public_path($cleanPath);
            $storageCandidate = storage_path('app/public/' . $cleanPath);
            if (file_exists($publicCandidate)) {
                $logoAbsolutePath = $publicCandidate;
            } elseif (file_exists($storageCandidate)) {
                $logoAbsolutePath = $storageCandidate;
            }
        }

        $pdf = Pdf::loadView('sale_preparation_pdf', compact(
            'sale',
            'businessId',
            'departmentName',
            'departmentProfession',
            'departmentAddress',
            'departmentPhone',
            'departmentEmail',
            'logoAbsolutePath',
            'showCompanyInfo',
            'saleItemStockReferences'
        ));

        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('Preparation_commande_' . $sale->id . '.pdf');
    }

    public function exportSale($saleId)
    {
        $sale = Sale::with(['client', 'saleItems.product'])->findOrFail($saleId);
        $company = Company::first();
        $showCompanyInfo = $sale->show_company_info ?? $this->shouldShowCompanyInfo($company);
        $saleItemStockReferences = $this->buildSaleItemStockReferences($sale);
        
        $amountLetter = $this->convertAmoutToLetter($sale->total_amount * 1.19);
        
        $pdf = Pdf::loadView('sale_pdf', compact('sale', 'company', 'amountLetter', 'showCompanyInfo', 'saleItemStockReferences'));
        
        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
            
        return $pdf->stream('Facture_' . $sale->id . '.pdf');
    }

    public function exportCertifyInvoice($invoiceId)
    {
        $invoice = CertifyInvoices::with(['client', 'certifyInvoiceProducts.product'])->findOrFail($invoiceId);
        $company = Company::first();
        $showCompanyInfo = $this->shouldShowCompanyInfo($company);
        $logoAbsolutePath = file_exists(public_path('logo.png')) ? public_path('logo.png') : null;
        $logoDataUri = null;

        if (!empty($logoAbsolutePath) && file_exists($logoAbsolutePath)) {
            $mimeType = @mime_content_type($logoAbsolutePath) ?: 'image/png';
            $logoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($logoAbsolutePath));
        }
        
        $totalTTC = $invoice->amount + ($invoice->tva_amount ?: ($invoice->amount * 0.19)) + ($invoice->timbre_amount ?: 0);
        $amountLetter = $this->convertAmoutToLetter($totalTTC);
        
        $pdf = Pdf::loadView('certify_invoice_pdf', compact(
            'invoice',
            'company',
            'amountLetter',
            'showCompanyInfo',
            'logoDataUri'
        ));
        
        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
            
        return $pdf->stream('Facture_Certifiee_' . $invoice->fac_id . '.pdf');
    }
    public function exportMultiSales(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return response()->json(['error' => 'No IDs provided'], 400);
        }

        $sales = Sale::with(['client', 'saleItems.product'])->whereIn('id', $ids)->get();
        $company = Company::first();
        $showCompanyInfo = $this->shouldShowCompanyInfo($company);

        foreach ($sales as $sale) {
            $sale->amountLetter = $this->convertAmoutToLetter($sale->total_amount * 1.19);
        }

        $pdf = Pdf::loadView('multi_sale_pdf', compact('sales', 'company', 'showCompanyInfo'));

        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('Factures_Multiples.pdf');
    }

    public function exportMultiCertifyInvoices(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return response()->json(['error' => 'No IDs provided'], 400);
        }

        $invoices = CertifyInvoices::with(['client', 'certifyInvoiceProducts.product'])->whereIn('id', $ids)->get();
        $company = Company::first();
        $showCompanyInfo = $this->shouldShowCompanyInfo($company);
        $logoAbsolutePath = file_exists(public_path('logo.png')) ? public_path('logo.png') : null;
        $logoDataUri = null;

        if (!empty($logoAbsolutePath) && file_exists($logoAbsolutePath)) {
            $mimeType = @mime_content_type($logoAbsolutePath) ?: 'image/png';
            $logoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($logoAbsolutePath));
        }

        foreach ($invoices as $invoice) {
            $totalTTC = $invoice->amount + ($invoice->tva_amount ?: ($invoice->amount * 0.19)) + ($invoice->timbre_amount ?: 0);
            $invoice->amountLetter = $this->convertAmoutToLetter($totalTTC);
        }

        $pdf = Pdf::loadView('multi_certify_invoice_pdf', compact(
            'invoices',
            'company',
            'showCompanyInfo',
            'logoDataUri'
        ));

        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('Factures_Certifiees_Multiples.pdf');
    }
    public function exportSubCertifyInvoice($invoiceId)
    {
        $invoice = \App\Models\SubCertifyInvoices::with(['client', 'subCertifyInvoiceProducts.product'])->findOrFail($invoiceId);
        $company = Company::first();
        $showCompanyInfo = $this->shouldShowCompanyInfo($company);
        $logoAbsolutePath = file_exists(public_path('logo.png')) ? public_path('logo.png') : null;
        $logoDataUri = null;

        if (!empty($logoAbsolutePath) && file_exists($logoAbsolutePath)) {
            $mimeType = @mime_content_type($logoAbsolutePath) ?: 'image/png';
            $logoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($logoAbsolutePath));
        }
        
        $totalTTC = $invoice->amount + ($invoice->tva_amount ?: ($invoice->amount * 0.19)) + ($invoice->timbre_amount ?: 0);
        $amountLetter = $this->convertAmoutToLetter($totalTTC);
        
        $pdf = Pdf::loadView('sub_certify_invoice_pdf', compact('invoice', 'company', 'amountLetter', 'showCompanyInfo', 'logoDataUri'));
        
        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
            
        return $pdf->stream('Facture_Sous_Traitant_' . $invoice->fac_id . '.pdf');
    }

    public function exportMultiCheques(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return response()->json(['error' => 'No IDs provided'], 400);
        }

        $cheques = \App\Models\Cheque::with(['client'])->whereIn('id', $ids)->get();
        $company = Company::first();

        foreach ($cheques as $cheque) {
            $cheque->amountLetter = $this->convertAmoutToLetter($cheque->amount);
        }

        $pdf = Pdf::loadView('multi_cheques_pdf', compact('cheques', 'company'));

        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('Cheques_Multiples.pdf');
    }

    public function generateCustomerLog()
    {
    }
}
