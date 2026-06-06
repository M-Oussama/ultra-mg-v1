<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use App\Models\City;
use App\Models\Client;
use App\Models\Company;
use App\Models\PartialPayment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductReturnList;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleStatus;
use App\Models\SupplyItem;
use App\Models\TruckDriver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductStock;

class POSController extends Controller
{
    //
    private function resolvedPackageType(SaleItem $saleItem): string
    {
        $type = trim((string) ($saleItem->package_type ?? $saleItem->product?->package_type ?? ''));

        return $type !== '' ? $type : 'package';
    }

    private function resolvedUnitsPerPackage(SaleItem $saleItem): int
    {
        return (int) ($saleItem->units_per_package ?? $saleItem->product?->units_per_package ?? 0);
    }

    private function formatSaleItemCartonBreakdown(SaleItem $saleItem, float $quantity): string
    {
        if (!$saleItem->hasPackaging()) {
            return number_format($quantity, 0, ',', ' ');
        }

        $unitsPerPackage = $this->resolvedUnitsPerPackage($saleItem);
        if ($unitsPerPackage <= 0) {
            return number_format($quantity, 0, ',', ' ');
        }

        $cartons = (int) floor($quantity / $unitsPerPackage);
        $remainder = (int) round(fmod($quantity, $unitsPerPackage));
        $label = $cartons . ' ' . $this->resolvedPackageType($saleItem) . ' (' . $unitsPerPackage . ')';

        if ($remainder > 0) {
            $label .= ' + ' . $remainder . ' pcs isolées';
        }

        return $label;
    }

    private function formatSaleItemQuantityTotal(SaleItem $saleItem, float $quantity): string
    {
        $formatted = number_format($quantity, 0, ',', ' ');

        return $saleItem->hasPackaging() ? $formatted . ' pcs' : $formatted;
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
            $queues[$productId] ??= [];
            $queues[$productId][] = [
                'reference' => (string) ($supplyItem->reference ?? ''),
                'remaining_quantity' => (float) $supplyItem->quantity,
            ];
        }

        $allocations = [];

        foreach ($saleItems as $saleItem) {
            $productId = (string) $saleItem->product_id;
            $remainingToAllocate = (float) $saleItem->quantity;

            if (!isset($queues[$productId])) {
                continue;
            }

            foreach ($queues[$productId] as &$batch) {
                if ($remainingToAllocate <= 0) {
                    break;
                }

                if ($batch['remaining_quantity'] <= 0) {
                    continue;
                }

                $consumed = min($batch['remaining_quantity'], $remainingToAllocate);
                $batch['remaining_quantity'] -= $consumed;
                $remainingToAllocate -= $consumed;

                if ((int) $saleItem->sale_id === (int) $sale->id && $consumed > 0) {
                    $allocations[$saleItem->id][] = [
                        'quantity_total' => $this->formatSaleItemQuantityTotal($saleItem, $consumed),
                        'carton_breakdown' => $this->formatSaleItemCartonBreakdown($saleItem, $consumed),
                        'reference' => '#' . (string) $batch['reference'],
                    ];
                }
            }
            unset($batch);
        }

        return $allocations;
    }

    public function getSales(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
        $client_id = $request->input('client_id',  ''); // Default current page value is 1 if not provided
        $status = $request->input('status',  '');
        $to = $request->input('to',  '');
        $from = $request->input('from',  '');
        $department_id = $request->input('department_id', '');


        $sales = Sale::query();

        // Hierarchical Data Isolation
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                // Managers see all sales in their assigned departments
                $deptIds = $user->departments->pluck('id')->toArray();
                $sales->whereIn('department_id', $deptIds);
            } else {
                // Others (Salespeople) see only their own sales
                $sales->where('user_id', $user->id);
            }
        }

        if($client_id !='') {
            $sales->where('client_id', $client_id);
        }
        if(intval($status) == 1) {
            $sales->where('balance', 0);
        }
        if(intval($status) == 2) {
            $sales->where('balance', '>',0);
        }

        if($from != '' && $to!='') {
            $sales->whereBetween('sale_date', [$from, $to]);
        }else {
            if($from != ''){
                $sales->whereBetween('sale_date', [$from, date('Y-m-d')]);
            }
        }
        if($department_id != '') {
            $sales->where('department_id', $department_id);
        }
        $sales->orderBy('sale_date', 'desc');

        $paginatedResult = $sales->paginate($perPage, ['*'], 'page', $currentPage);

        $items = $paginatedResult->items();
        $totalSales = $paginatedResult->total(); // Total number of invoices matching the query
        $totalPage = ceil($totalSales / $perPage); // Calculate total pages

        $clientsQuery = Client::query();
        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                $deptIds = $user->departments->pluck('id')->toArray();
                $clientsQuery->whereIn('department_id', $deptIds);
            } else {
                $clientsQuery->where('user_id', $user->id);
            }
        }
        $clients = $clientsQuery->get();

        return response()->json(["sales" => $items, "totalPage" => $totalPage, "totalSales"=>$totalSales, 'clients' => $clients]);
    }

    public function getData() {
        $clientsQuery = Client::query();
        $driversQuery = TruckDriver::query();

        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                $deptIds = $user->departments->pluck('id')->toArray();
                $clientsQuery->whereIn('department_id', $deptIds);
                $driversQuery->whereIn('department_id', $deptIds);
            } else {
                $clientsQuery->where('user_id', $user->id);
            }
        }

        $clients = $clientsQuery->get();
        $drivers = $driversQuery->get();
        $products = Product::getAllProductsFormatted();
        $sale_statues = SaleStatus::all();
        $last_id = Sale::latest()->first();
        if(!$last_id) {
            $last_id = 0;
        } else {
            $last_id = $last_id->id;
        }


        $cities = City::all();

        return response()->json(["clients" => $clients, 'drivers'=>$drivers, 'cities'=>$cities, "products" => $products, "sale_statues"=>$sale_statues,"last_id"=>$last_id+1]);

    }

    public function getSale($saleId) {

        $sale = Sale::with(['saleItems.product'])->findOrFail($saleId);
        $saleItemStockReferences = $this->buildSaleItemStockReferences($sale);

        foreach ($sale->saleItems as $saleItem) {
            $saleItem->stock_allocation = $saleItemStockReferences[$saleItem->id] ?? [];
        }
        $sale->sale_item_stock_references = $saleItemStockReferences;

        $payment_total =  Payment::where('sale_id', $saleId)
            ->sum('amount_paid');
        $sale->payment_total = $payment_total;
        $sale->amount_letter = $this->convertAmoutToLetter(($sale->total_amount*1.19));

        $companies = Company::all();

        $clients = Client::all();

        $sold = Sale::where('client_id',$sale->client_id)
            ->where('id', '!=', $sale->id)
            ->where('sale_date','<=', $sale->sale_date)
            ->sum('balance');

        return response()->json(["sold"=>$sold, "sale" => $sale, "companies" => $companies, "clients"=>$clients]);
    }

    public function getSaleData($saleId) {

        $sale = Sale::with(['saleItems.product'])->findOrFail($saleId);
        $saleItemStockReferences = $this->buildSaleItemStockReferences($sale);

        foreach ($sale->saleItems as $saleItem) {
            $saleItem->stock_allocation = $saleItemStockReferences[$saleItem->id] ?? [];
        }
        $sale->sale_item_stock_references = $saleItemStockReferences;

        $sale->amount_letter = $this->convertAmoutToLetter(($sale->total_amount*1.19));

        $payment_total =  Payment::where('sale_id', $saleId)
            ->sum('amount_paid');
        $sale->payment_total = $payment_total;
        $sale->paymentAmount = $payment_total;
        //$sale->payment = false;

        $clients = Client::all();
        $drivers = TruckDriver::all();
        $products = Product::getAllProductsFormatted();
        $sale_statues = SaleStatus::all();

        $cities = City::all();
        return response()->json(["sale" => $sale, "cities" => $cities,"clients"=>$clients,"drivers"=>$drivers, "products"=> $products, "sale_statues"=>$sale_statues]);
    }

    public function getPriceHistory($productId, $clientId) {

        $products = SaleItem::where('client_id',$clientId)->where('product_id',$productId)->get();

        return response()->json(['products'=> $products]);
    }

    public function store(Request $request): JsonResponse
    {

        $data = $request->input('data');
        $client = $data['client'];
        $payment = $data['payment'];
        $balance =  $data['total_amount'] - $data['paymentAmount'];
        $department_id = $data['department_id'] ?? $request->input('department_id', 1);

        if($payment) {
            if($balance >= 0) {
                $sale_status = SaleStatus::PAID_ID;
            }else {
                $sale_status = SaleStatus::PARTIALLY_PAID_ID;
            }
        }else {
            $sale_status = SaleStatus::NOT_PAID_ID;
        }

        try {
            DB::beginTransaction();

            if($sale_status != SaleStatus::NOT_PAID_ID ) {
                $sale = Sale::create([
                    'sale_date' => $data['sale_date'],
                    'client_id' => $client['id'],
                    'total_amount' => $data['total_amount'],
                    'sale_statuses_id' => $sale_status,
                    'balance' => $balance,
                    'regulation' => $data['paymentAmount'],
                    'payment' => 1,
                    'department_id' => $department_id,
                    'show_company_info' => (bool) ($data['show_company_info'] ?? false),
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                ]);
            } else {
                $sale = Sale::create([
                    'sale_date' => $data['sale_date'],
                    'client_id' => $client['id'],
                    'total_amount' => $data['total_amount'],
                    'sale_statuses_id' => $sale_status,
                    'balance' => $balance,
                    'department_id' => $department_id,
                    'show_company_info' => (bool) ($data['show_company_info'] ?? false),
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                ]);
            }

            if($data['driver_id']){
                $sale->truck_driver_id = $data['driver_id'];
                $sale->picked_up = 0;
                $sale->save();
            }

            if(floatval($data['paymentAmount']) > 0) {
                $payment = new Payment();
                $payment->payment_date = $data['sale_date'];
                $payment->amount_paid = $data['paymentAmount'];
                $payment->sale_id = $sale->id;
                $payment->client_id = $client['id'];
                $payment->active = true;
                $payment->save();
            }

            $products = $data['sale_items'];

            foreach ($products as $product) {
                $unitsPerPackage = isset($product['units_per_package'])
                    ? (int) $product['units_per_package']
                    : (isset($product['product']['units_per_package']) ? (int) $product['product']['units_per_package'] : null);
                $packageType = $product['package_type'] ?? ($product['product']['package_type'] ?? null);
                $packageQuantity = isset($product['package_quantity'])
                    ? (int) $product['package_quantity']
                    : (($unitsPerPackage && $unitsPerPackage > 0) ? (int) floor(((float) $product['quantity']) / $unitsPerPackage) : null);
                $numberOfPackages = isset($product['number_of_packages'])
                    ? (int) $product['number_of_packages']
                    : $packageQuantity;
                $itemsPerPackage = isset($product['items_per_package'])
                    ? (int) $product['items_per_package']
                    : $unitsPerPackage;
                $priceActive = array_key_exists('price_active', $product)
                    ? filter_var($product['price_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                    : (isset($product['product']['price_active']) ? filter_var($product['product']['price_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true);
                $object = SaleItem::create([
                    'product_id' => $product['product']['id'],
                    'client_id' => $client['id'],
                    'quantity' => $product['quantity'],
                    'price' => floatval($product['price']),
                    'total_price' => $product['quantity'] * $product['price'],
                    'sale_id' => $sale->id,
                    'sale_date' => $data['sale_date'],
                    'package_type' => $packageType,
                    'units_per_package' => $unitsPerPackage,
                    'package_quantity' => $packageQuantity,
                    'number_of_packages' => $numberOfPackages,
                    'items_per_package' => $itemsPerPackage,
                    'price_active' => $priceActive === null ? true : $priceActive,
                ]);
                $object->save();

                // Decrement Stock
                $stock = ProductStock::where('product_id', $product['product']['id'])->first();
                if ($stock) {
                    $stock->decrement('quantity', $product['quantity']);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Product created successfully', "id" => $sale->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error creating sale: ' . $e->getMessage()], 400);
        }

//        $benefit = Benefit::where('month', date("m", strtotime($data['sale_date'])))->where('year', date("Y", strtotime($data['sale_date'])))->get();
//        if(count($benefit) > 0) {
//            $benefitController = new BenefitController();
//            $benefitController->refreshBenefitColumns($benefit->first()->id);
//        }

        return response()->json(['message' => 'Product created successfully', "id"=>$sale->id]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'data.id' => 'required|integer|exists:sales,id',
            'data.department_id' => 'nullable|integer|exists:departments,id',
        ]);

        $data = $request->input('data');

        $client = $data['client'];


        $payment = $data['payment'];
        $balance =  $data['total_amount'] - $data['paymentAmount'];

        if($balance >= 0) {
            $sale_status = SaleStatus::PAID_ID;
        }else {
             $sale_status = SaleStatus::NOT_PAID_ID;
        }


        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($data['id']);
            $department_id = array_key_exists('department_id', $data) && $data['department_id'] !== null && $data['department_id'] !== ''
                ? $data['department_id']
                : $request->input('department_id', $sale->department_id ?? 1);

            $products = $data['sale_items'] ?? null;
            $hasSaleItems = is_array($products) && count($products) > 0;

            if ($hasSaleItems) {
                // Reverse old stock only when we are replacing the line items.
                $oldItems = SaleItem::where('sale_id', $sale->id)->get();
                foreach ($oldItems as $oldItem) {
                    $stock = ProductStock::where('product_id', $oldItem->product_id)->first();
                    if ($stock) {
                        $stock->increment('quantity', $oldItem->quantity);
                    }
                }
                SaleItem::where('sale_id', $sale->id)->delete();
            }

            if($sale_status != SaleStatus::NOT_PAID_ID && $payment == 1) {
                $sale->update([
                    'sale_date' => $data['sale_date'],
                    'client_id' => $client['id'],
                    'total_amount' => $data['total_amount'],
                    'sale_statuses_id' => $sale_status,
                    'balance' => $balance - floatval($data['regulation']),
                    'department_id' => $department_id,
                    'show_company_info' => (bool) ($data['show_company_info'] ?? $sale->show_company_info ?? false),
                ]);
                $sale->payment = 1;
                $sale->regulation = floatval($data['regulation']);
                $sale->save();
            } else {
                $sale->update([
                    'sale_date' => $data['sale_date'],
                    'client_id' => $client['id'],
                    'total_amount' => $data['total_amount'],
                    'sale_statuses_id' => $sale_status,
                    'balance' => $balance,
                    'department_id' => $department_id,
                    'show_company_info' => (bool) ($data['show_company_info'] ?? $sale->show_company_info ?? false),
                ]);
                $sale->payment = 0;
                $sale->regulation = 0;
                $sale->save();
            }

            Payment::where('sale_id', $sale->id)->update([
                'department_id' => $department_id,
            ]);
            PartialPayment::where('sale_id', $sale->id)->update([
                'department_id' => $department_id,
            ]);

            if($data['truck_driver_id']){
                $sale->truck_driver_id = $data['truck_driver_id'];
                $sale->save();
            }

            if(floatval($data['regulation']) > 0) {
                $paymentObj = Payment::where('sale_id', $sale->id)->where('active', true)->first();
                if(!$paymentObj){
                    $paymentObj = new Payment();
                }
                $paymentObj->payment_date = $data['sale_date'];
                $paymentObj->amount_paid = floatval($data['regulation']);
                $paymentObj->sale_id = $sale->id;
                $paymentObj->client_id = $client['id'];
                $paymentObj->department_id = $department_id;
                $paymentObj->active = true;
                $paymentObj->save();
            }

            if ($hasSaleItems) {
                foreach ($products as $product) {
                    $unitsPerPackage = isset($product['units_per_package'])
                        ? (int) $product['units_per_package']
                        : (isset($product['product']['units_per_package']) ? (int) $product['product']['units_per_package'] : null);
                    $packageType = $product['package_type'] ?? ($product['product']['package_type'] ?? null);
                    $packageQuantity = isset($product['package_quantity'])
                        ? (int) $product['package_quantity']
                        : (($unitsPerPackage && $unitsPerPackage > 0) ? (int) floor(((float) $product['quantity']) / $unitsPerPackage) : null);
                    $numberOfPackages = isset($product['number_of_packages'])
                        ? (int) $product['number_of_packages']
                        : $packageQuantity;
                    $itemsPerPackage = isset($product['items_per_package'])
                        ? (int) $product['items_per_package']
                        : $unitsPerPackage;
                    $priceActive = array_key_exists('price_active', $product)
                        ? filter_var($product['price_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                        : (isset($product['product']['price_active']) ? filter_var($product['product']['price_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true);
                    $object = SaleItem::create([
                        'product_id' => $product['product']['id'],
                        'quantity' => $product['quantity'],
                        'price' => floatval($product['price']),
                        'total_price' => $product['quantity'] * $product['price'],
                        'sale_id' => $sale->id,
                        'client_id' => $client['id'],
                        'sale_date' => $data['sale_date'],
                        'package_type' => $packageType,
                        'units_per_package' => $unitsPerPackage,
                        'package_quantity' => $packageQuantity,
                        'number_of_packages' => $numberOfPackages,
                        'items_per_package' => $itemsPerPackage,
                        'price_active' => $priceActive === null ? true : $priceActive,
                    ]);
                    $object->save();

                    // Decrement Stock
                    $stock = ProductStock::where('product_id', $product['product']['id'])->first();
                    if ($stock) {
                        $stock->decrement('quantity', $product['quantity']);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Product updated successfully', "id" => $sale->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error updating sale: ' . $e->getMessage()], 400);
        }

//        $benefit = Benefit::where('month', date("m", strtotime($data['sale_date'])))->where('year', date("Y", strtotime($data['sale_date'])))->get();
//        if(count($benefit) > 0) {
//            $benefitController = new BenefitController();
//            $benefitController->refreshBenefitColumns($benefit->first()->id);
//        }

        return response()->json(['message' => 'Product updated successfully', "id"=>$sale->id]);
    }

    public function addPayment(Request $request) {

        $payment = $request->input('payment');

        $date = $payment['date'];
        $amount = $payment['amount'];
        $note = $payment['note'];
        $sale = $payment['sale'];

        $payment = new Payment();
        $payment->payment_date = $date;
        $payment->amount_paid = $amount;
        $payment->note = $note;
        $payment->sale_id = $sale['id'];
        $payment->client_id = $sale['client']['id'];
        $payment->save();

        $_sale = Sale::find($sale['id']);
        $_sale->balance = $_sale->balance - $amount;
        $_sale->save();

        $client = Client::find($sale['client']['id']);
        $this->calculateClientBalance($client);

        return response()->json('Payment Added Successfully');
    }

    public function createPayment(Request $request) {


        $payment = $request->input('payment');
        $paidInvoices = $request->input('paidInvoices');

        $date = $payment['date'];
        $amount = $payment['amount'];
        $note = $payment['note'];
        $client_id = $payment['client']['id'];


        $payment = new Payment();
        $payment->payment_date = $date;
        $payment->amount_paid = $amount;
        $payment->note = $note;
        $payment->client_id = $client_id;
        $payment->save();

        foreach ($paidInvoices as $paidInvoice) {
            $invoice = Sale::find($paidInvoice['id']);

            $amount = (floatval($invoice->balance) - floatval($paidInvoice['balance']));

            $invoice->update(['balance' => $amount ]);
            PartialPayment::create([
               'payment_id' => $payment->id,
               'sale_id' => $invoice->id,
               'amount' => $paidInvoice['balance'],
            ]);

        }

        $client = Client::find($client_id);
        $this->calculateClientBalance($client);

        return response()->json('Payment Added Successfully');
    }

    public function updatePayment(Request $request) {

        $payment = $request->input('payment');
        $paidInvoices = $request->input('paidInvoices');


        $date = $payment['payment_date'];
        $amount = $payment['amount_paid'];
        $note = $payment['note'];
        $sale_id = $payment['sale_id'];
        $client_id = $payment['client_id'];
        $id = $payment['id'];

        $payment = Payment::find($id);
        $payment->payment_date = $date;
        $payment->amount_paid = $amount;
        $payment->note = $note;
        $payment->client_id = $client_id;
        $payment->sale_id = $sale_id;
        $payment->save();

        $partialPayments = PartialPayment::where('payment_id', $payment->id)->get();



        foreach ($partialPayments as $partialPayment) {
            $invoice = Sale::find($partialPayment->sale_id);

            $newBalance = floatval($invoice->balance) + floatval($partialPayment->amount);
            $invoice->update(['balance' => $newBalance]);
        }

        PartialPayment::where('payment_id', $payment->id)->get()->each->delete();


        foreach ($paidInvoices as $paidInvoice) {
            $invoice = Sale::find($paidInvoice['sale_id']);

            $amount = (floatval($invoice->balance) - floatval($paidInvoice['balance']));

            $invoice->update(['balance' => $amount ]);
            PartialPayment::create([
                'payment_id' => $payment->id,
                'sale_id' => $invoice->id,
                'amount' => $paidInvoice['balance'],
            ]);

        }
        $client = Client::find($client_id);
        $this->calculateClientBalance($client);
//        if($payment->active) {
//            if($payment->sale_id){
//                $sale = Sale::find($payment->sale_id);
//                $sale->balance = floatval($sale->balance) - floatval($sale->regulation) +  floatval($amount);
//                $sale->regulation = $amount;
//                $sale->save();
//            }
//
//        }


        return response()->json('Payment updated Successfully');
    }
    public function deletePayment(Request $request) {

        $payment = $request->input('payment');

        $id = $payment['id'];
        $payment = Payment::find($id);
        if($payment->active){
            if($payment->sale_id){
                $sale = Sale::find($payment->sale_id);
                $sale->balance = $sale->balance + $sale->regulation;
                $sale->regulation = 0;
                $sale->save();
            }
        }
        $payment->delete();

        return response()->json('Payment deleted Successfully');
    }

    public function listPayment(Request $request){
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided


        $payments = Payment::orderBy('payment_date', 'desc')->paginate($perPage, ['*'], 'page', $currentPage);
        $totalSales = $payments->total(); // Total number of payments matching the query
        $totalPage = ceil($totalSales / $perPage); // Calculate total pages
        $clients = Client::all();
        return response()->json(["payments" => $payments, "totalPage" => $totalPage, "totalPayments"=>$totalSales,"clients"=>$clients]);

    }

    public function deleteSale(Request $request) {
        $id = $request->input('sale.id');
        
        try {
            DB::beginTransaction();
            $sale = Sale::findOrFail($id);
            $items = SaleItem::where('sale_id', $id)->get();

            foreach ($items as $item) {
                $stock = ProductStock::where('product_id', $item->product_id)->first();
                if ($stock) {
                    $stock->increment('quantity', $item->quantity);
                }
            }

            SaleItem::where('sale_id', $id)->delete();
            Payment::where('sale_id', $id)->get()->each->delete();
            $sale->delete();

            DB::commit();
            return response()->json('Sale deleted Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json('Error deleting sale: ' . $e->getMessage(), 400);
        }
    }

    public function recalculateStocks(Request $request): JsonResponse
    {
        $request->validate([
            'include_deleted' => 'sometimes|boolean',
            'department_id' => 'nullable|integer|exists:departments,id',
        ]);

        $includeDeleted = (bool) $request->input('include_deleted', false);
        $departmentId = $request->input('department_id');

        try {
            DB::beginTransaction();

            $now = now();

            $productQuery = Product::query()->select('id');
            if ($departmentId !== null && $departmentId !== '') {
                $productQuery->where('department_id', $departmentId);
            }
            $productIds = $productQuery->pluck('id');

            if ($productIds->isEmpty()) {
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'No products found for the selected department.',
                    'products_processed' => 0,
                ]);
            }

            DB::table('product_stocks')
                ->whereIn('product_id', $productIds)
                ->update([
                    'quantity' => 0,
                    'updated_at' => $now,
                ]);

            $saleTotals = SaleItem::query()
                ->select('sale_items.product_id', DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_quantity'))
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->whereIn('sale_items.product_id', $productIds)
                ->when($departmentId !== null && $departmentId !== '', function ($query) use ($departmentId) {
                    $query->where('sales.department_id', $departmentId);
                })
                ->when(!$includeDeleted, function ($query) {
                    $query->whereNull('sales.deleted_at');
                })
                ->groupBy('sale_items.product_id')
                ->pluck('total_quantity', 'product_id');

            $supplyTotals = SupplyItem::query()
                ->select('supply_items.product_id', DB::raw('COALESCE(SUM(supply_items.quantity), 0) as total_quantity'))
                ->join('supplies', 'supplies.id', '=', 'supply_items.supply_id')
                ->whereIn('supply_items.product_id', $productIds)
                ->when($departmentId !== null && $departmentId !== '', function ($query) use ($departmentId) {
                    $query->where('supplies.departement_id', $departmentId);
                })
                ->when(!$includeDeleted, function ($query) {
                    $query->whereNull('supplies.deleted_at');
                })
                ->groupBy('supply_items.product_id')
                ->pluck('total_quantity', 'product_id');

            $returnTotals = ProductReturnList::query()
                ->select('product_return_lists.product_id', DB::raw('COALESCE(SUM(product_return_lists.quantity), 0) as total_quantity'))
                ->join('product_returns', 'product_returns.id', '=', 'product_return_lists.return_id')
                ->whereIn('product_return_lists.product_id', $productIds)
                ->when($departmentId !== null && $departmentId !== '', function ($query) use ($departmentId) {
                    $query->where('product_returns.department_id', $departmentId);
                })
                ->when(!$includeDeleted, function ($query) {
                    $query->whereNull('product_returns.deleted_at');
                })
                ->groupBy('product_return_lists.product_id')
                ->pluck('total_quantity', 'product_id');

            foreach ($productIds as $productId) {
                $quantity = (float) ($supplyTotals[$productId] ?? 0)
                    + (float) ($returnTotals[$productId] ?? 0)
                    - (float) ($saleTotals[$productId] ?? 0);

                DB::table('product_stocks')->updateOrInsert(
                    ['product_id' => $productId],
                    [
                        'quantity' => $quantity,
                        'deleted_at' => null,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock recalculated successfully',
                'products_processed' => $productIds->count(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to recalculate stock: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function getClientInvoices($id){
        $notPaidInvoices = Sale::where('balance', '>', 0)->where('client_id', $id)->get();
        return response()->json(["notPaidInvoices" => $notPaidInvoices]);

    }
    public function getPaidInvoices($id, $payment_id){

        $notPaidInvoices = Sale::where('balance', '>', 0)->where('client_id', $id)->get();

        $paidInvoices = PartialPayment::where('payment_id', $payment_id)->get();

        return response()->json(["notPaidInvoices" => $notPaidInvoices, "paidInvoices" => $paidInvoices]);


    }

    public function updatePickUp(Request $request, Sale $sale)
    {

        // Validate that we got a boolean from frontend
        $validated = $request->validate([
            'picked_up' => 'required|boolean',
        ]);

        $sale->picked_up = $validated['picked_up'];
        $sale->save();

        return response()->json([
            'message' => 'Sale pickup status updated.',
            'sale' => $sale
        ]);
    }

    #[OA\Get(
        path: "/api/pos/sales/payments/invoice/{sale_id}",
        summary: "Get total amount paid for a specific sale",
        tags: ["POS"],
        parameters: [
            new OA\Parameter(name: "sale_id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Total amount paid",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "sale_id", type: "integer"),
                        new OA\Property(property: "total_paid", type: "number", format: "float")
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Sale not found")
        ]
    )]
    public function getSalePaymentsTotal($sale_id)
    {
        $sale = Sale::find($sale_id);
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        $direct_payment = Payment::where('sale_id', $sale_id)->sum('amount_paid');
        $partial_payment = PartialPayment::where('sale_id', $sale_id)->sum('amount');

        return response()->json([
            'sale_id' => (int) $sale_id,
            'total_paid' => (float) ($direct_payment + $partial_payment)
        ]);
    }

    /**
     * Import sales from CSV with one department_id applied to all rows.
     */
    public function importCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        $departmentId = (int) $request->input('department_id');
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);

        foreach (['id', 'sale_date', 'client_id', 'total_amount', 'sale_statuses_id', 'regulation', 'payment', 'balance', 'notes', 'truck_driver_id', 'picked_up'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $warnings = [];
        $rowNumber = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $paymentRaw = strtolower((string) ($rowData['payment'] ?? '0'));
            $payment = in_array($paymentRaw, ['1', 'true', 'yes'], true) ? 1 : 0;
            $pickedUpRaw = strtolower((string) ($rowData['picked_up'] ?? '0'));
            $pickedUp = in_array($pickedUpRaw, ['1', 'true', 'yes'], true) ? 1 : 0;
            $balanceValue = isset($rowData['balance']) ? (float) $rowData['balance'] : 0;

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'sale_date' => $rowData['sale_date'] ?? null,
                'client_id' => isset($rowData['client_id']) ? (int) $rowData['client_id'] : null,
                'total_amount' => isset($rowData['total_amount']) ? (float) $rowData['total_amount'] : null,
                'sale_statuses_id' => isset($rowData['sale_statuses_id']) ? (int) $rowData['sale_statuses_id'] : null,
                'regulation' => isset($rowData['regulation']) ? (float) $rowData['regulation'] : 0,
                'payment' => $payment,
                'balance' => $balanceValue,
                'notes' => $rowData['notes'] ?? null,
                'department_id' => $departmentId,
                'truck_driver_id' => isset($rowData['truck_driver_id']) && trim((string) $rowData['truck_driver_id']) !== '' ? (int) $rowData['truck_driver_id'] : null,
                'picked_up' => $pickedUp,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (in_array('paid_amount', $normalizedHeader, true) && isset($rowData['paid_amount']) && trim((string) $rowData['paid_amount']) !== '') {
                $payload['paid_amount'] = (float) $rowData['paid_amount'];
            }

            if (!empty($payload['client_id'])) {
                $clientExists = DB::table('clients')->where('id', $payload['client_id'])->exists();
                if (!$clientExists) {
                    $warnings[] = [
                        'row' => $rowNumber,
                        'warning' => 'client_id not found, set to null.',
                    ];
                    $payload['client_id'] = null;
                }
            }

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'sale_date' => 'required|date',
                'package_type' => 'nullable|string|max:255',
                'units_per_package' => 'nullable|integer|min:0',
                'package_quantity' => 'nullable|integer|min:0',
                'client_id' => 'nullable|integer',
                'total_amount' => 'required|numeric|min:0',
                'sale_statuses_id' => 'required|integer|min:1',
                'regulation' => 'required|numeric|min:0',
                'payment' => 'required|boolean',
                'balance' => 'required|numeric',
                'notes' => 'nullable|string',
                'department_id' => 'required|integer|exists:departments,id',
                'paid_amount' => 'nullable|numeric|min:0',
                'truck_driver_id' => 'nullable|integer',
                'picked_up' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            if (!empty($payload['id']) && DB::table('sales')->where('id', $payload['id'])->exists()) {
                $warnings[] = [
                    'row' => $rowNumber,
                    'warning' => 'Sale id exists, inserted with auto-generated id.',
                ];
                unset($payload['id']);
            }

            DB::table('sales')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ]);
    }

    /**
     * Import sale items from CSV.
     *
     * Required CSV headers:
     * id, sale_id, client_id, product_id, quantity, price, total_price, sale_date
     * Optional headers:
     * package_type, units_per_package, package_quantity, number_of_packages, items_per_package
     */
    public function importSaleItemsCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);

        foreach (['id', 'sale_id', 'client_id', 'product_id', 'quantity', 'price', 'total_price', 'sale_date'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $warnings = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'sale_id' => isset($rowData['sale_id']) ? (int) $rowData['sale_id'] : null,
                'client_id' => isset($rowData['client_id']) ? (int) $rowData['client_id'] : null,
                'product_id' => isset($rowData['product_id']) ? (int) $rowData['product_id'] : null,
                'quantity' => isset($rowData['quantity']) ? (int) $rowData['quantity'] : 0,
                'price' => isset($rowData['price']) ? (float) $rowData['price'] : 0,
                'total_price' => isset($rowData['total_price']) ? (float) $rowData['total_price'] : 0,
                'sale_date' => $rowData['sale_date'] ?? null,
                'package_type' => isset($rowData['package_type']) && $rowData['package_type'] !== '' ? $rowData['package_type'] : null,
                'units_per_package' => isset($rowData['units_per_package']) && $rowData['units_per_package'] !== '' ? (int) $rowData['units_per_package'] : null,
                'package_quantity' => isset($rowData['package_quantity']) && $rowData['package_quantity'] !== '' ? (int) $rowData['package_quantity'] : null,
                'number_of_packages' => isset($rowData['number_of_packages']) && $rowData['number_of_packages'] !== '' ? (int) $rowData['number_of_packages'] : null,
                'items_per_package' => isset($rowData['items_per_package']) && $rowData['items_per_package'] !== '' ? (int) $rowData['items_per_package'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'sale_id' => 'required|integer',
                'client_id' => 'required|integer',
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
                'sale_date' => 'required|date',
                'package_type' => 'nullable|string|max:255',
                'units_per_package' => 'nullable|integer|min:0',
                'package_quantity' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            if (!empty($payload['id']) && DB::table('sale_items')->where('id', $payload['id'])->exists()) {
                $warnings[] = [
                    'row' => $rowNumber,
                    'warning' => 'Sale item id exists, inserted with auto-generated id.',
                ];
                unset($payload['id']);
            }

            DB::table('sale_items')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ]);
    }

    /**
     * Import payments from CSV with one department_id for all rows.
     *
     * CSV columns:
     * id, sale_id, client_id, amount_paid, payment_date, note, active
     */
    public function importPaymentsCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        $departmentId = (int) $request->input('department_id');
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);
        foreach (['id', 'sale_id', 'client_id', 'amount_paid', 'payment_date', 'note', 'active'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $warnings = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $activeRaw = strtolower((string) ($rowData['active'] ?? '0'));
            $active = in_array($activeRaw, ['1', 'true', 'yes'], true) ? 1 : 0;

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'department_id' => $departmentId,
                'sale_id' => isset($rowData['sale_id']) && trim((string) $rowData['sale_id']) !== '' ? (int) $rowData['sale_id'] : null,
                'client_id' => isset($rowData['client_id']) && trim((string) $rowData['client_id']) !== '' ? (int) $rowData['client_id'] : null,
                'amount_paid' => isset($rowData['amount_paid']) ? (float) $rowData['amount_paid'] : 0,
                'payment_date' => $rowData['payment_date'] ?? null,
                'note' => $rowData['note'] ?? null,
                'active' => $active,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'department_id' => 'required|integer|exists:departments,id',
                'sale_id' => 'nullable|integer',
                'client_id' => 'nullable|integer',
                'amount_paid' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
                'note' => 'nullable|string|max:255',
                'active' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                $errors[] = ['row' => $rowNumber, 'errors' => $validator->errors()->all()];
                continue;
            }

            if (!empty($payload['id']) && DB::table('payments')->where('id', $payload['id'])->exists()) {
                $warnings[] = ['row' => $rowNumber, 'warning' => 'Payment id exists, inserted with auto-generated id.'];
                unset($payload['id']);
            }

            DB::table('payments')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ]);
    }

    /**
     * Import partial_payments from CSV with one department_id for all rows.
     *
     * CSV columns:
     * id, payment_id, sale_id, amount
     */
    public function importPartialPaymentsCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        $departmentId = (int) $request->input('department_id');
        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);
        foreach (['id', 'payment_id', 'sale_id', 'amount'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $warnings = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'department_id' => $departmentId,
                'payment_id' => isset($rowData['payment_id']) ? (int) $rowData['payment_id'] : null,
                'sale_id' => isset($rowData['sale_id']) ? (int) $rowData['sale_id'] : null,
                'amount' => isset($rowData['amount']) ? (float) $rowData['amount'] : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'department_id' => 'required|integer|exists:departments,id',
                'payment_id' => 'required|integer',
                'sale_id' => 'required|integer',
                'amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                $errors[] = ['row' => $rowNumber, 'errors' => $validator->errors()->all()];
                continue;
            }

            if (!empty($payload['id']) && DB::table('partial_payments')->where('id', $payload['id'])->exists()) {
                $warnings[] = ['row' => $rowNumber, 'warning' => 'Partial payment id exists, inserted with auto-generated id.'];
                unset($payload['id']);
            }

            DB::table('partial_payments')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ]);
    }
}





