<?php

namespace App\Http\Controllers;

use App\Models\BenefitBatchUsage;
use App\Models\Benefit;
use App\Models\BenefitExtraFee;
use App\Models\Department;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductExtraCost;
use App\Models\ProductBenefit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supply;
use App\Models\SupplyItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BenefitController extends Controller
{
    //
    public function getBenefits(Request $request){
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
        $departmentId = $request->input('department_id');
        // Subquery to calculate total payments per month and year
        $benefitsQuery = Benefit::select('benefits.*', DB::raw('
            (SELECT SUM(amount_paid)
             FROM payments
             WHERE MONTH(payment_date) = benefits.month
             AND YEAR(payment_date) = benefits.year) as totalPayments
        '));

        if ($departmentId !== null && $departmentId !== '') {
            $benefitsQuery->where('department_id', $departmentId);
        }

        $benefits = $benefitsQuery
            ->paginate($perPage, ['*'], 'page', $currentPage);

//        $benefits = Benefit::paginate($perPage, ['*'], 'page', $currentPage);
        $totalBenefits = $benefits->total(); // Total number of payments matching the query
        $totalPage = ceil($totalBenefits / $perPage); // Calculate total pages
        $months = $this->months(1);
        $years = $this->years(2000);



        return response()->json(["benefits" => $benefits, "totalPage" => $totalPage, "totalBenefits"=>$totalBenefits, "months"=>$months, "years"=>$years]);

    }

    public function analysis(Request $request): JsonResponse
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $departmentId = $request->input('department_id', '');

        $salesQuery = Sale::with([
            'saleItems.product.extraCosts',
            'saleItems.product.sharedCostAssignments.component.prices',
            'department',
        ])
            ->whereBetween('sale_date', [$from, $to]);

        if ($departmentId !== '') {
            $salesQuery->where('department_id', $departmentId);
        }

        $sales = $salesQuery->orderBy('sale_date')->orderBy('id')->get();

        $suppliesQuery = Supply::with(['items.product'])
            ->whereDate('supply_date', '<=', $to);

        if ($departmentId !== '') {
            $suppliesQuery->where('departement_id', $departmentId);
        }

        $supplies = $suppliesQuery->orderBy('supply_date')->orderBy('id')->get();

        $supplyBuckets = [];
        foreach ($supplies as $supply) {
            $deptId = (int) ($supply->departement_id ?? 0);
            $departmentBucket = $supplyBuckets[$deptId] ?? [];

            foreach ($supply->items as $item) {
                $productId = trim((string) ($item->product_id ?? ''));
                if ($productId === '') {
                    continue;
                }

                $quantity = (float) $item->quantity;
                if ($quantity <= 0) {
                    continue;
                }

                $bucket = $departmentBucket[$productId] ?? ['total_cost' => 0.0, 'quantity' => 0.0];
                $bucket['total_cost'] += (float) ($item->total_price ?? ($item->unit_price * $quantity));
                $bucket['quantity'] += $quantity;
                $departmentBucket[$productId] = $bucket;
            }

            $supplyBuckets[$deptId] = $departmentBucket;
        }

        $unitCostsByDepartment = [];
        foreach ($supplyBuckets as $deptId => $departmentBucket) {
            foreach ($departmentBucket as $productId => $bucket) {
                $unitCostsByDepartment[$deptId][$productId] = $bucket['quantity'] > 0
                    ? $bucket['total_cost'] / $bucket['quantity']
                    : 0.0;
            }
        }

        $rawMaterialPriceCache = [];

        $productProfitsMap = [];
        $departmentBenefits = [];
        $departmentMetrics = [];
        $totalRevenue = 0.0;
        $totalCost = 0.0;

        foreach ($sales as $sale) {
            $saleTotal = (float) ($sale->total_amount ?? 0);
            $totalRevenue += $saleTotal;

            $deptId = (int) ($sale->department_id ?? $sale->departement_id ?? 0);
            $department = $sale->department ?? Department::find($deptId);
            $isProduction = $this->isProductionDepartment($department);
            $departmentCosts = $unitCostsByDepartment[$deptId] ?? [];
            $saleDate = Carbon::parse($sale->sale_date);
            $saleMonthKey = $saleDate->format('Y-m');
            $rawMaterialPrice = $rawMaterialPriceCache[$saleMonthKey][$deptId] ?? null;
            if ($rawMaterialPrice === null) {
                $rawMaterialPrice = $this->resolveMonthlyRawMaterialPrice(
                    (int) $saleDate->year,
                    (int) $saleDate->month,
                    $deptId > 0 ? $deptId : null
                );
                $rawMaterialPriceCache[$saleMonthKey][$deptId] = $rawMaterialPrice;
            }
            $rawMaterialPrice = (float) $rawMaterialPrice;
            $saleCost = 0.0;

            foreach ($sale->saleItems as $saleItem) {
                $product = $saleItem->product;
                if (!$product) {
                    continue;
                }

                $productId = trim((string) $product->id);
                $quantity = (float) ($saleItem->quantity ?? 0);
                if ($productId === '' || $quantity <= 0) {
                    continue;
                }

                $unitCost = $isProduction
                    ? $this->resolveProductionUnitCost($product, $rawMaterialPrice, $saleDate)
                    : (float) ($departmentCosts[$productId] ?? 0);

                if ($unitCost <= 0 && $product?->cost_price > 0) {
                    $unitCost = (float) $product->cost_price;
                }

                $itemRevenue = floatval($saleItem->total_price ?? (($saleItem->price ?? $saleItem->unit_price ?? 0) * $quantity));
                $itemCost = $unitCost * $quantity;
                $saleCost += $itemCost;

                $existing = $productProfitsMap[$productId] ?? [
                    'product_id' => (string) $productId,
                    'product_name' => (string) ($product->name ?? 'Unknown'),
                    'revenue' => 0.0,
                    'cost' => 0.0,
                    'quantity_sold' => 0.0,
                ];

                $productProfitsMap[$productId] = [
                    'product_id' => $existing['product_id'],
                    'product_name' => $existing['product_name'] !== 'Unknown'
                        ? $existing['product_name']
                        : (string) ($product->name ?? 'Unknown'),
                    'revenue' => $existing['revenue'] + $itemRevenue,
                    'cost' => $existing['cost'] + $itemCost,
                    'quantity_sold' => $existing['quantity_sold'] + $quantity,
                ];
            }

            $totalCost += $saleCost;
            $departmentBenefits[$deptId] = ($departmentBenefits[$deptId] ?? 0) + ($saleTotal - $saleCost);
            $departmentMetrics[$deptId] = $departmentMetrics[$deptId] ?? [
                'revenue' => 0.0,
                'cost' => 0.0,
                'profit' => 0.0,
            ];
            $departmentMetrics[$deptId]['revenue'] += $saleTotal;
            $departmentMetrics[$deptId]['cost'] += $saleCost;
            $departmentMetrics[$deptId]['profit'] += ($saleTotal - $saleCost);
        }

        $productProfits = array_values($productProfitsMap);
        usort($productProfits, function (array $a, array $b) {
            return (float) ($b['revenue'] - $b['cost']) <=> (float) ($a['revenue'] - $a['cost']);
        });

        $monthKeys = $this->collectMonthKeys($from, $to);
        if (!empty($monthKeys)) {
            $benefitChargesQuery = Benefit::with('extraFees')
                ->where(function ($query) use ($monthKeys) {
                    foreach ($monthKeys as $monthKey) {
                        [$year, $month] = array_map('intval', explode('-', $monthKey));
                        $query->orWhere(function ($monthQuery) use ($year, $month) {
                            $monthQuery->where('year', $year)->where('month', $month);
                        });
                    }
                });

            if ($departmentId !== '') {
                $benefitChargesQuery->where('department_id', (int) $departmentId);
            }

            $benefitCharges = $benefitChargesQuery->get();
            foreach ($benefitCharges as $benefitCharge) {
                $chargeTotal = $this->benefitChargesTotal($benefitCharge);
                if ($chargeTotal <= 0) {
                    continue;
                }

                $targetDeptId = $benefitCharge->department_id !== null
                    ? (int) $benefitCharge->department_id
                    : ($departmentId !== '' ? (int) $departmentId : null);

                if ($targetDeptId !== null) {
                    $departmentMetrics[$targetDeptId] = $departmentMetrics[$targetDeptId] ?? [
                        'revenue' => 0.0,
                        'cost' => 0.0,
                        'profit' => 0.0,
                    ];
                    $departmentMetrics[$targetDeptId]['cost'] += $chargeTotal;
                    $departmentMetrics[$targetDeptId]['profit'] -= $chargeTotal;
                    $departmentBenefits[$targetDeptId] = ($departmentBenefits[$targetDeptId] ?? 0) - $chargeTotal;
                }

                $totalCost += $chargeTotal;
            }
        }

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'product_profits' => $productProfits,
            'department_benefits' => $departmentBenefits,
            'department_metrics' => $departmentMetrics,
        ]);
    }

    public function getArticlesBenefit(Request $request, $Id) {
        $articles = ProductBenefit::with('product')->where('benefit_id', $Id)->get();
        $benefit = Benefit::with([
            'batchUsages.supplyItem.product',
            'batchUsages.supplyItem.supply.supplier',
            'extraFees',
        ])->find($Id);
        $batchSummary = $this->summarizeBatchUsages($benefit?->batchUsages ?? collect());

        return response()->json([
            'articles' => $articles,
            'benefit' => $benefit,
            'batch_usages' => $benefit?->batchUsages ?? [],
            'batch_summary' => $batchSummary,
        ]);

    }

    public function store(Request $request): JsonResponse
    {

        $data = $request->input('data');
        $departmentId = $data['department_id'] ?? $request->input('department_id');
        $month = $data['month'];
        $year = $data['year'];
        $electricity = $data['electricity'];
        $employee_salary = $data['employee_salary'];
        $other_charges = $data['other_charges'];

        $benefit = Benefit::where('month',$month)->where('year',$year)->get();

        if(count($benefit) > 0) {
            throw new BadRequestHttpException('This Benefit Already Exists');
        }

        $Newbenefit = Benefit::create([
            'department_id' => $departmentId !== null && $departmentId !== ''
                ? (int) $departmentId
                : null,
            'month'=>$month,
            'year'=>$year,
            'benefit'=>0,
            'raw_material_price'=>0,
            'raw_material_quantity'=>0,
            'raw_material_cost'=>0,
            'electricity' => floatval($electricity),
            'employee_salary' => floatval($employee_salary),
            'other_charges' => floatval($other_charges),
            'netBenefit' => 0
            ]);

        $this->refreshBenefitColumns($Newbenefit->id);


        return response()->json(['message' => 'Benefit created successfully']);
    }

    public function updateBenefit(Request $request, $id): JsonResponse
    {

        $items = $request->input('data');
        $benefitObject = $request->input('benefit');
        $raw_material_price = $benefitObject['raw_material_price'];
        $total_profit =$benefitObject['total_profit'];
        $total_amount =$benefitObject['total_amount'];


        $quantity = 0;
        foreach ($items as $item) {
            $productBenefit = ProductBenefit::find($item['id']);

            $productBenefit->update([
                'raw_material_price' => floatval($raw_material_price),
                'benefit' => floatval($item['benefit']),
                'product_price' => floatval($item['product_price']),
                'total_profit' => floatval($item['total_profit']),
            ]);
            $quantity += floatval($item['quantity']);
        }
        $benefit = Benefit::find($id);

        $netBenefit = floatval($total_profit) - $this->benefitChargesTotal($benefit);

        $benefit->update([
            'raw_material_price' => round(floatval($raw_material_price), 3),
            'benefit' => round(floatval($total_profit), 3),
            'netBenefit' => round($netBenefit, 3),
            'total_amount' => round(floatval($total_amount), 3),
            'total_articles' => round($quantity, 3),
        ]);


        return response()->json(['message' => 'Benefit updated successfully']);
    }

    public function updateBenefitCharges(Request $request, $id) {
        $benefitObject = $request->input('benefit');

        $electricity = $benefitObject['electricity'];
        $employee_salary = $benefitObject['employee_salary'];
        $other_charges = $benefitObject['other_charges'];

        $benefit = Benefit::find($id);

        $netBenefit = floatval($benefit->benefit) - (
            floatval($electricity) +
            floatval($employee_salary) +
            floatval($other_charges) +
            $this->benefitExtraFeesTotal($benefit)
        );

        $benefit->update([
            'electricity' => round(floatval($electricity), 3),
            'employee_salary' => round(floatval($employee_salary), 3),
            'other_charges' => round(floatval($other_charges), 3),
            'netBenefit' => round($netBenefit, 3),
        ]);
        return response()->json(['message' => 'Benefit updated successfully']);


    }
    public function destroyBenefit($id){
        $benefit = Benefit::find($id);

        BenefitBatchUsage::where('benefit_id', $id)->delete();
        ProductBenefit::where('benefit_id', $id)->delete();
        $benefit->delete();
        return response()->json(['message' => 'Benefit deleted successfully']);

    }

    public function refreshBenefitData(Request $request, $id): JsonResponse
    {
        $this->refreshBenefitColumns($id);

        return response()->json(['message' => 'Refreshed successfully']);
    }

    public function refreshBenefitColumns($id) {
        $benefit = Benefit::find($id);
        if (!$benefit) {
            throw new \RuntimeException('Benefit not found');
        }

        $rawMaterialSummary = $this->summarizeBatchUsages(
            BenefitBatchUsage::where('benefit_id', $benefit->id)->get()
        );

        $rawMaterialPrice = $rawMaterialSummary['average_unit_price'] > 0
            ? $rawMaterialSummary['average_unit_price']
            : $this->calculateMonthlyRawMaterialPrice(
                (int) $benefit->year,
                (int) $benefit->month,
                $benefit->department_id ? (int) $benefit->department_id : null
            );

        $benefit->update([
            'raw_material_price' => round($rawMaterialPrice, 3),
            'raw_material_quantity' => round($rawMaterialSummary['total_quantity'], 3),
            'raw_material_cost' => round($rawMaterialSummary['total_cost'], 3),
        ]);

        $benefitDepartment = $benefit->department ?? Department::find($benefit->department_id);
        $isProduction = $this->isProductionDepartment($benefitDepartment);

        ProductBenefit::where('benefit_id', $benefit->id)->delete();

        $salesQuery = Sale::with([
            'saleItems.product.extraCosts',
            'saleItems.product.sharedCostAssignments.component.prices',
            'department',
        ])
            ->whereYear('sale_date', $benefit->year)
            ->whereMonth('sale_date', $benefit->month)
            ->when($benefit->department_id, function ($query) use ($benefit) {
                $query->where('department_id', $benefit->department_id);
            });

        $sales = $salesQuery->orderBy('sale_date')->orderBy('id')->get();

        $totalGrossProfit = 0.0;
        $totalAmount = 0.0;
        $totalQuantity = 0.0;
        $productAggregates = [];

        foreach ($sales as $sale) {
            $saleDate = Carbon::parse($sale->sale_date);

            foreach ($sale->saleItems as $saleItem) {
                $productModel = $saleItem->product;
                if (!$productModel) {
                    continue;
                }

                $productId = (string) $productModel->id;
                $quantity = floatval($saleItem->quantity ?? 0);
                if ($productId === '' || $quantity <= 0) {
                    continue;
                }

                $itemRevenue = floatval(
                    $saleItem->total_price
                    ?? (($saleItem->price ?? $saleItem->unit_price ?? 0) * $quantity)
                );
                $itemCostPerUnit = $isProduction
                    ? $this->resolveProductionUnitCost($productModel, $rawMaterialPrice, $saleDate)
                    : floatval($productModel->cost_price ?? 0);
                $itemCost = $itemCostPerUnit * $quantity;
                $itemBenefit = $itemRevenue - $itemCost;

                $aggregate = $productAggregates[$productId] ?? [
                    'product_id' => $productId,
                    'product' => $productModel,
                    'revenue' => 0.0,
                    'cost' => 0.0,
                    'quantity' => 0.0,
                ];

                $aggregate['revenue'] += $itemRevenue;
                $aggregate['cost'] += $itemCost;
                $aggregate['quantity'] += $quantity;
                $productAggregates[$productId] = $aggregate;

                $totalGrossProfit += $itemBenefit;
                $totalAmount += $itemRevenue;
                $totalQuantity += $quantity;
            }
        }

        foreach ($productAggregates as $aggregate) {
            /** @var Product $productModel */
            $productModel = $aggregate['product'];
            $quantity = floatval($aggregate['quantity']);
            $totalProductAmount = floatval($aggregate['revenue']);
            $totalProductCost = floatval($aggregate['cost']);
            $productBenefitValue = $totalProductAmount - $totalProductCost;
            $productPrice = $quantity > 0 ? $totalProductAmount / $quantity : 0;

            $productBenefit = ProductBenefit::firstOrNew([
                'benefit_id' => $benefit->id,
                'product_id' => $productModel->id,
            ]);

            $productBenefit->fill([
                'raw_material_price' => $rawMaterialPrice,
                'weight' => floatval($productModel->weight ?? 0),
                'benefit' => $productBenefitValue,
                'product_price' => $productPrice,
                'quantity' => $quantity,
                'total_amount' => $totalProductAmount,
                'total_profit' => $productBenefitValue,
            ]);
            $productBenefit->save();
        }

        $netBenefit = $totalGrossProfit - $this->benefitChargesTotal($benefit);

        $benefit->update([
            'benefit' => round($totalGrossProfit, 3),
            'netBenefit' => round($netBenefit, 3),
            'total_amount' => round($totalAmount, 3),
            'total_articles' => round($totalQuantity, 3),
        ]);
    }

    public function getBatchSelectionData(Request $request): JsonResponse
    {
        $month = (int) $request->input('month');
        $year = (int) $request->input('year');
        $departmentId = $request->input('department_id');

        if ($month < 1 || $month > 12 || $year < 2000) {
            return response()->json(['message' => 'Invalid month or year'], 422);
        }

        $benefitQuery = Benefit::where('month', $month)->where('year', $year);
        if ($departmentId !== null && $departmentId !== '') {
            $benefitQuery->where('department_id', (int) $departmentId);
        } else {
            $benefitQuery->whereNull('department_id');
        }

        $benefit = $benefitQuery->first();
        if (!$benefit) {
            $benefit = Benefit::create([
                'department_id' => $departmentId !== null && $departmentId !== ''
                    ? (int) $departmentId
                    : null,
                'month' => $month,
                'year' => $year,
                'benefit' => 0,
                'raw_material_price' => 0,
                'raw_material_quantity' => 0,
                'raw_material_cost' => 0,
                'electricity' => 0,
                'employee_salary' => 0,
                'other_charges' => 0,
                'netBenefit' => 0,
                'total_amount' => 0,
                'total_articles' => 0,
            ]);
        }

        $payload = $this->buildBenefitBatchPayload($benefit);

        return response()->json([
            'benefit' => $benefit,
            'batches' => $payload['batches'],
            'selected_batches' => $payload['selected_batches'],
            'batch_summary' => $payload['summary'],
        ]);
    }

    public function syncBatchSelectionData(Request $request): JsonResponse
    {
        $data = $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000'],
            'department_id' => ['sometimes', 'nullable', 'integer', 'exists:departments,id'],
            'batches' => ['sometimes', 'array'],
            'batches.*.supply_item_id' => ['required', 'integer', 'exists:supply_items,id'],
            'batches.*.quantity_used' => ['required', 'numeric', 'min:0'],
        ]);

        $departmentId = isset($data['department_id']) && $data['department_id'] !== null
            ? (int) $data['department_id']
            : null;

        $benefit = Benefit::where('month', (int) $data['month'])
            ->where('year', (int) $data['year'])
            ->when($departmentId !== null, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            }, function ($query) {
                $query->whereNull('department_id');
            })
            ->first();

        if (!$benefit) {
            $benefit = Benefit::create([
                'department_id' => $departmentId,
                'month' => (int) $data['month'],
                'year' => (int) $data['year'],
                'benefit' => 0,
                'raw_material_price' => 0,
                'raw_material_quantity' => 0,
                'raw_material_cost' => 0,
                'electricity' => 0,
                'employee_salary' => 0,
                'other_charges' => 0,
                'netBenefit' => 0,
                'total_amount' => 0,
                'total_articles' => 0,
            ]);
        }

        $payload = $this->buildBenefitBatchPayload($benefit);
        $availableMap = $payload['available_map'];
        DB::beginTransaction();
        try {
            BenefitBatchUsage::where('benefit_id', $benefit->id)->delete();

            $rows = $data['batches'] ?? [];
            foreach ($rows as $row) {
                $supplyItemId = (int) $row['supply_item_id'];
                $quantityUsed = round((float) $row['quantity_used'], 3);

                if ($quantityUsed <= 0) {
                    continue;
                }

                $availableQuantity = (float) ($availableMap[$supplyItemId] ?? 0);
                if ($quantityUsed > $availableQuantity + 0.0001) {
                    throw new \RuntimeException('Selected quantity exceeds the available batch balance.');
                }

                $supplyItem = SupplyItem::with(['product', 'supply.supplier'])->findOrFail($supplyItemId);
                $unitPrice = (float) $supplyItem->unit_price;

                BenefitBatchUsage::create([
                    'benefit_id' => $benefit->id,
                    'supply_item_id' => $supplyItemId,
                    'quantity_used' => $quantityUsed,
                    'unit_price' => $unitPrice,
                    'total_price' => $quantityUsed * $unitPrice,
                ]);
            }

            $this->refreshBenefitColumns($benefit->id);
            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();

            return response()->json([
                'message' => $throwable->getMessage(),
            ], 422);
        }

        $benefit->refresh()->load(['batchUsages.supplyItem.product', 'batchUsages.supplyItem.supply.supplier']);
        $payload = $this->buildBenefitBatchPayload($benefit);

        return response()->json([
            'message' => 'Batch selections saved successfully',
            'benefit' => $benefit,
            'batches' => $payload['batches'],
            'selected_batches' => $payload['selected_batches'],
            'batch_summary' => $payload['summary'],
        ]);
    }

    public function getBenefitFees(Request $request): JsonResponse
    {
        $month = (int) $request->input('month');
        $year = (int) $request->input('year');
        $departmentId = $request->input('department_id');

        if ($month < 1 || $month > 12 || $year < 2000) {
            return response()->json(['message' => 'Invalid month or year'], 422);
        }

        $benefit = $this->resolveMonthlyBenefitRecord($month, $year, $departmentId);
        $benefit->load('extraFees');

        return response()->json([
            'benefit' => $benefit,
            'fees' => $benefit->extraFees,
            'total_fees' => round($this->benefitExtraFeesTotal($benefit), 3),
        ]);
    }

    public function syncBenefitFees(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'fees' => ['sometimes', 'array'],
            'fees.*.id' => ['nullable', 'integer', 'exists:benefit_extra_fees,id'],
            'fees.*.name' => ['required', 'string', 'max:255'],
            'fees.*.amount' => ['required', 'numeric', 'min:0'],
            'fees.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $benefit = Benefit::findOrFail($id);

        DB::transaction(function () use ($benefit, $data) {
            $seenIds = [];
            $rows = $data['fees'] ?? [];

            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }

                $feeData = [
                    'name' => $row['name'],
                    'amount' => round((float) $row['amount'], 3),
                    'notes' => $row['notes'] ?? null,
                ];

                if (!empty($row['id'])) {
                    $fee = BenefitExtraFee::where('benefit_id', $benefit->id)
                        ->whereKey((int) $row['id'])
                        ->first();

                    if ($fee) {
                        $fee->update($feeData);
                        $seenIds[] = $fee->id;
                        continue;
                    }
                }

                $created = $benefit->extraFees()->create($feeData);
                $seenIds[] = $created->id;
            }

            if (!empty($seenIds)) {
                $benefit->extraFees()->whereNotIn('id', $seenIds)->delete();
            } else {
                $benefit->extraFees()->delete();
            }

            $this->refreshBenefitColumns($benefit->id);
        });

        $benefit->refresh()->load('extraFees');

        return response()->json([
            'message' => 'Benefit fees updated successfully',
            'benefit' => $benefit,
            'fees' => $benefit->extraFees,
            'total_fees' => round($this->benefitExtraFeesTotal($benefit), 3),
        ]);
    }

    private function calculateMonthlyRawMaterialPrice(int $year, int $month, ?int $departmentId = null): float
    {
        $monthEnd = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        $query = DB::table('supply_items')
            ->join('supplies', 'supplies.id', '=', 'supply_items.supply_id')
            ->join('products', 'products.id', '=', 'supply_items.product_id')
            ->whereDate('supplies.supply_date', '<=', $monthEnd);

        if ($departmentId !== null) {
            $query->where('supplies.departement_id', $departmentId);
        }

        $result = $query
            ->selectRaw('COALESCE(SUM(COALESCE(supply_items.total_price, supply_items.unit_price * supply_items.quantity)), 0) as total_cost')
            ->selectRaw('COALESCE(SUM(COALESCE(products.weight, 0) * COALESCE(supply_items.quantity, 0)), 0) as total_weight')
            ->first();

        if (!$result || floatval($result->total_weight) <= 0) {
            return 0.0;
        }

        return floatval($result->total_cost) / floatval($result->total_weight);
    }

    private function resolveMonthlyRawMaterialPrice(int $year, int $month, ?int $departmentId = null): float
    {
        $benefitQuery = Benefit::where('month', $month)->where('year', $year);
        if ($departmentId !== null) {
            $benefitQuery->where('department_id', $departmentId);
        } else {
            $benefitQuery->whereNull('department_id');
        }

        $benefit = $benefitQuery->first();
        if ($benefit) {
            if (floatval($benefit->raw_material_quantity) > 0 && floatval($benefit->raw_material_cost) > 0) {
                return floatval($benefit->raw_material_cost) / floatval($benefit->raw_material_quantity);
            }

            if (floatval($benefit->raw_material_price) > 0) {
                return floatval($benefit->raw_material_price);
            }
        }

        if ($departmentId !== null) {
            $globalBenefitQuery = Benefit::where('month', $month)->where('year', $year)->whereNull('department_id');
            $globalBenefit = $globalBenefitQuery->first();
            if ($globalBenefit) {
                if (floatval($globalBenefit->raw_material_quantity) > 0 && floatval($globalBenefit->raw_material_cost) > 0) {
                    return floatval($globalBenefit->raw_material_cost) / floatval($globalBenefit->raw_material_quantity);
                }

                if (floatval($globalBenefit->raw_material_price) > 0) {
                    return floatval($globalBenefit->raw_material_price);
                }
            }

            $globalMonthlyPrice = $this->calculateMonthlyRawMaterialPrice($year, $month, null);
            if ($globalMonthlyPrice > 0) {
                return $globalMonthlyPrice;
            }
        }

        return $this->calculateMonthlyRawMaterialPrice($year, $month, $departmentId);
    }

    private function collectMonthKeys(string $from, string $to): array
    {
        $start = Carbon::parse($from)->startOfMonth();
        $end = Carbon::parse($to)->startOfMonth();
        $keys = [];

        while ($start->lte($end)) {
            $keys[] = $start->format('Y-m');
            $start->addMonthNoOverflow();
        }

        return $keys;
    }

    private function resolveProductionUnitCost(Product $product, float $rawMaterialPrice, ?Carbon $referenceDate = null): float
    {
        $weight = floatval($product->weight ?? 0);
        $componentTotals = $this->productSharedComponentTotals($product, $weight, $referenceDate);
        $baseCost = $componentTotals['raw_material'];

        if ($baseCost <= 0 && $weight > 0 && $rawMaterialPrice > 0) {
            // Product weight is stored in grams, while raw material price is per KG.
            $baseCost = ($weight / 1000.0) * $rawMaterialPrice;
        }

        if ($baseCost <= 0 && floatval($product->cost_price ?? 0) > 0) {
            $baseCost = floatval($product->cost_price);
        }

        return $baseCost
            + $this->productDirectExtraCostTotal($product, $referenceDate)
            + $componentTotals['extra'];
    }

    private function isProductionDepartment(?Department $department): bool
    {
        return strtolower((string) ($department?->department_type ?? 'resell')) === 'production';
    }

    private function productDirectExtraCostTotal(Product $product, ?Carbon $referenceDate = null): float
    {
        $costs = $product->relationLoaded('extraCosts')
            ? $product->extraCosts
            : $product->extraCosts()->get();

        $total = 0.0;
        foreach ($costs as $cost) {
            if (!$this->isProductExtraCostActiveOnDate($cost, $referenceDate)) {
                continue;
            }

            $total += floatval($cost->amount ?? 0);
        }

        return $total;
    }

    private function productSharedComponentTotals(Product $product, float $weight, ?Carbon $referenceDate = null): array
    {
        $assignments = $product->relationLoaded('sharedCostAssignments')
            ? $product->sharedCostAssignments
            : $product->sharedCostAssignments()->with('component.prices')->get();

        $totals = [
            'raw_material' => 0.0,
            'extra' => 0.0,
        ];

        foreach ($assignments as $assignment) {
            $component = $assignment->component;
            if (!$component) {
                continue;
            }

            $prices = $component->relationLoaded('prices')
                ? $component->prices
                : $component->prices()->get();

            $price = $prices
                ->filter(fn ($row) => $this->isComponentPriceActiveOnDate($row, $referenceDate))
                ->sortByDesc(fn ($row) => optional($row->effective_from)->timestamp ?? 0)
                ->first();

            if (!$price) {
                continue;
            }

            $amount = floatval($price->amount ?? 0);
            $quantity = $assignment->quantity === null
                ? 1.0
                : max(0.0, floatval($assignment->quantity));
            $type = strtolower(str_replace('-', '_', (string) ($component->cost_type ?? 'extra')));

            if ($type === 'raw_material') {
                if ($weight <= 0 || $amount <= 0) {
                    continue;
                }

                $totals['raw_material'] += ($weight / 1000.0) * $amount * $quantity;
                continue;
            }

            $totals['extra'] += $amount * $quantity;
        }

        return $totals;
    }

    private function isProductExtraCostActiveOnDate(ProductExtraCost $cost, ?Carbon $referenceDate = null): bool
    {
        return $this->isDatedCostActiveOnDate($cost, $referenceDate);
    }

    private function isComponentPriceActiveOnDate($price, ?Carbon $referenceDate = null): bool
    {
        return $this->isDatedCostActiveOnDate($price, $referenceDate);
    }

    private function isDatedCostActiveOnDate($cost, ?Carbon $referenceDate = null): bool
    {
        if ($referenceDate === null) {
            return true;
        }

        $from = $cost->effective_from ? Carbon::parse($cost->effective_from)->startOfDay() : null;
        $to = $cost->effective_to ? Carbon::parse($cost->effective_to)->endOfDay() : null;

        if ($from !== null && $referenceDate->lt($from)) {
            return false;
        }

        if ($to !== null && $referenceDate->gt($to)) {
            return false;
        }

        return true;
    }

    private function benefitExtraFeesTotal(?Benefit $benefit): float
    {
        if (!$benefit) {
            return 0.0;
        }

        $fees = $benefit->relationLoaded('extraFees')
            ? $benefit->extraFees
            : $benefit->extraFees()->get();

        return (float) $fees->sum(fn ($fee) => floatval($fee->amount ?? 0));
    }

    private function benefitChargesTotal(?Benefit $benefit): float
    {
        if (!$benefit) {
            return 0.0;
        }

        return floatval($benefit->electricity ?? 0)
            + floatval($benefit->employee_salary ?? 0)
            + floatval($benefit->other_charges ?? 0)
            + $this->benefitExtraFeesTotal($benefit);
    }

    private function resolveMonthlyBenefitRecord(int $month, int $year, $departmentId): Benefit
    {
        $benefitQuery = Benefit::where('month', $month)->where('year', $year);

        if ($departmentId !== null && $departmentId !== '') {
            $benefitQuery->where('department_id', (int) $departmentId);
        } else {
            $benefitQuery->whereNull('department_id');
        }

        $benefit = $benefitQuery->first();
        if ($benefit) {
            return $benefit;
        }

        return Benefit::create([
            'department_id' => $departmentId !== null && $departmentId !== ''
                ? (int) $departmentId
                : null,
            'month' => $month,
            'year' => $year,
            'benefit' => 0,
            'raw_material_price' => 0,
            'raw_material_quantity' => 0,
            'raw_material_cost' => 0,
            'electricity' => 0,
            'employee_salary' => 0,
            'other_charges' => 0,
            'netBenefit' => 0,
            'total_amount' => 0,
            'total_articles' => 0,
        ]);
    }

    private function buildBenefitBatchPayload(Benefit $benefit): array
    {
        $monthEnd = Carbon::create($benefit->year, $benefit->month, 1)->endOfMonth()->toDateString();

        $currentSelectionTotals = BenefitBatchUsage::query()
            ->where('benefit_id', $benefit->id)
            ->select('supply_item_id', DB::raw('COALESCE(SUM(quantity_used), 0) as total_used'))
            ->groupBy('supply_item_id')
            ->pluck('total_used', 'supply_item_id');

        $globalUsageQuery = BenefitBatchUsage::query()
            ->join('benefits', 'benefits.id', '=', 'benefit_batch_usages.benefit_id')
            ->select('benefit_batch_usages.supply_item_id', DB::raw('COALESCE(SUM(benefit_batch_usages.quantity_used), 0) as total_used'))
            ->groupBy('benefit_batch_usages.supply_item_id');

        if ($benefit->department_id) {
            $globalUsageQuery->where('benefits.department_id', $benefit->department_id);
        } else {
            $globalUsageQuery->whereNull('benefits.department_id');
        }

        $globalUsageTotals = $globalUsageQuery->pluck('total_used', 'supply_item_id');

        $rows = SupplyItem::query()
            ->select([
                'supply_items.id',
                'supply_items.supply_id',
                'supply_items.product_id',
                'supply_items.reference',
                'supply_items.quantity',
                'supply_items.unit_price',
                'supply_items.total_price',
                'supplies.supply_date',
                'sales_suppliers.full_name as supplier_full_name',
                'sales_suppliers.name as supplier_name',
                'products.name as product_name',
            ])
            ->join('supplies', 'supplies.id', '=', 'supply_items.supply_id')
            ->leftJoin('sales_suppliers', 'sales_suppliers.id', '=', 'supplies.sales_supplier_id')
            ->leftJoin('products', 'products.id', '=', 'supply_items.product_id')
            ->whereNull('supply_items.deleted_at')
            ->whereNull('supplies.deleted_at')
            ->whereDate('supplies.supply_date', '<=', $monthEnd)
            ->when($benefit->department_id, function ($query) use ($benefit) {
                $query->where('supplies.departement_id', $benefit->department_id);
            })
            ->orderBy('supplies.supply_date')
            ->orderBy('supply_items.id')
            ->get();

        $batches = [];
        foreach ($rows as $row) {
            $usedInCurrentBenefit = (float) ($currentSelectionTotals[$row->id] ?? 0);
            $usedAcrossAllBenefits = (float) ($globalUsageTotals[$row->id] ?? 0);
            $usedByOtherBenefits = max(0.0, $usedAcrossAllBenefits - $usedInCurrentBenefit);
            $availableQuantity = max(0.0, (float) $row->quantity - $usedByOtherBenefits);
            $remainingAfterSelection = max(0.0, $availableQuantity - $usedInCurrentBenefit);
            $supplierName = trim((string) ($row->supplier_full_name ?? $row->supplier_name ?? 'Supplier'));

            $batches[] = [
                'id' => (int) $row->id,
                'supply_item_id' => (int) $row->id,
                'supply_id' => (int) $row->supply_id,
                'product_id' => (int) $row->product_id,
                'product_name' => (string) ($row->product_name ?? 'Product'),
                'reference' => (string) ($row->reference ?? ''),
                'supplier_name' => $supplierName !== '' ? $supplierName : 'Supplier',
                'purchase_date' => (string) $row->supply_date,
                'purchase_quantity' => (float) $row->quantity,
                'available_quantity' => $availableQuantity,
                'selected_quantity' => $usedInCurrentBenefit,
                'remaining_quantity' => $remainingAfterSelection,
                'unit_price' => (float) $row->unit_price,
                'total_price' => (float) $row->total_price,
            ];
        }

        $selectedRows = BenefitBatchUsage::query()
            ->where('benefit_id', $benefit->id)
            ->get();

        $selectedBatches = [];
        foreach ($selectedRows as $row) {
            $selectedBatches[] = [
                'id' => (int) $row->id,
                'benefit_id' => (int) $row->benefit_id,
                'supply_item_id' => (int) $row->supply_item_id,
                'quantity_used' => (float) $row->quantity_used,
                'unit_price' => (float) $row->unit_price,
                'total_price' => (float) $row->total_price,
                'product_name' => (string) ($row->supplyItem?->product?->name ?? 'Product'),
                'reference' => (string) ($row->supplyItem?->reference ?? ''),
                'supplier_name' => (string) ($row->supplyItem?->supply?->supplier?->full_name
                    ?? $row->supplyItem?->supply?->supplier?->name
                    ?? 'Supplier'),
                'purchase_date' => (string) ($row->supplyItem?->supply?->supply_date ?? ''),
            ];
        }

        $summary = $this->summarizeBatchUsages($selectedRows);

        $availableMap = [];
        $selectedMap = [];
        foreach ($batches as $batch) {
            $availableMap[$batch['supply_item_id']] = (float) $batch['available_quantity'];
            $selectedMap[$batch['supply_item_id']] = (float) $batch['selected_quantity'];
        }

        return [
            'batches' => $batches,
            'selected_batches' => $selectedBatches,
            'summary' => $summary,
            'available_map' => $availableMap,
        ];
    }

    private function summarizeBatchUsages($batchUsages): array
    {
        $totalQuantity = 0.0;
        $totalCost = 0.0;
        $count = 0;

        foreach ($batchUsages as $usage) {
            $quantity = (float) ($usage->quantity_used ?? 0);
            $cost = (float) ($usage->total_price ?? 0);

            $totalQuantity += $quantity;
            $totalCost += $cost;
            if ($quantity > 0) {
                $count++;
            }
        }

        return [
            'batch_count' => $count,
            'total_quantity' => $totalQuantity,
            'total_cost' => $totalCost,
            'average_unit_price' => $totalQuantity > 0 ? $totalCost / $totalQuantity : 0.0,
        ];
    }
}
