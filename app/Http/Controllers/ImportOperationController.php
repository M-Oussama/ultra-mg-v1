<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImportOperationRequest;
use App\Http\Requests\StoreImportParentInvoiceRequest;
use App\Http\Requests\StoreImportPartialInvoiceRequest;
use App\Models\ImportAuditEvent;
use App\Models\ImportOperation;
use App\Models\ImportParentInvoice;
use App\Services\ImportAllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImportOperationController extends Controller
{
    public function index(Request $request)
    {
        $query = ImportOperation::query()
            ->with('exporter')
            ->withCount('parentInvoices')
            ->select([
                'import_operations.*',
                DB::raw('(select coalesce(sum(planned_container_count), 0) from import_parent_invoices pi where pi.import_operation_id = import_operations.id) as planned_containers'),
                DB::raw('(select count(*) from import_containers c where c.import_operation_id = import_operations.id) as container_count'),
                DB::raw('(select coalesce(sum(pil.quantity * pil.commercial_unit_price), 0) from import_parent_invoices pi join import_parent_invoice_lines pil on pil.import_parent_invoice_id = pi.id where pi.import_operation_id = import_operations.id) as total_amount'),
            ])
            ->latest();

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->string('status')->toString();
            if ($status === 'in_progress') {
                $query->whereIn('status', ['in_progress', 'active', 'en_cours']);
            } elseif ($status === 'completed') {
                $query->whereIn('status', ['completed', 'done', 'termine']);
            } else {
                $query->where('status', $status);
            }
        }
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('exporter', fn ($supplier) => $supplier->where('name', 'like', "%{$search}%"));
            });
        }

        $paginated = $query->paginate(min((int) $request->input('per_page', 30), 100));

        $totalValue = (float) DB::table('import_parent_invoice_lines as pil')
            ->join('import_parent_invoices as pi', 'pi.id', '=', 'pil.import_parent_invoice_id')
            ->sum(DB::raw('pil.quantity * pil.commercial_unit_price'));
        $totalContainers = (int) DB::table('import_containers')->count();
        $plannedContainers = (int) DB::table('import_parent_invoices')->sum('planned_container_count');
        $parentCount = (int) DB::table('import_parent_invoices')->count();
        $partialCount = (int) DB::table('import_partial_invoices')->count();
        $inTransitCount = (int) DB::table('import_operations')->where('status', 'in_transit')->count();

        $statusCounts = [
            'all' => DB::table('import_operations')->whereNull('deleted_at')->count(),
            'draft' => DB::table('import_operations')->whereNull('deleted_at')->where('status', 'draft')->count(),
            'in_progress' => DB::table('import_operations')->whereNull('deleted_at')->whereIn('status', ['in_progress', 'active', 'en_cours'])->count(),
            'in_transit' => DB::table('import_operations')->whereNull('deleted_at')->where('status', 'in_transit')->count(),
            'completed' => DB::table('import_operations')->whereNull('deleted_at')->whereIn('status', ['completed', 'done', 'termine'])->count(),
            'cancelled' => DB::table('import_operations')->whereNull('deleted_at')->where('status', 'cancelled')->count(),
        ];

        $response = $paginated->toArray();
        $response['stats'] = [
            'total_value' => round($totalValue, 2),
            'total_containers' => $totalContainers,
            'planned_containers' => $plannedContainers,
            'parent_invoices_count' => $parentCount,
            'partial_invoices_count' => $partialCount,
            'in_transit_count' => $inTransitCount,
            'status_counts' => $statusCounts,
        ];

        return response()->json($response);
    }

    public function store(StoreImportOperationRequest $request)
    {
        $data = $request->validated();
        $data['currency'] = strtoupper($data['currency']);
        $data['created_by'] = Auth::id();

        $operation = DB::transaction(function () use ($data) {
            $operation = ImportOperation::create($data);
            ImportAuditEvent::create([
                'entity_type' => ImportOperation::class,
                'entity_id' => $operation->id,
                'action' => 'created',
                'actor_id' => Auth::id(),
                'after_json' => $operation->toArray(),
                'occurred_at' => now(),
            ]);

            return $operation;
        });

        return response()->json(['operation' => $operation->load('exporter')], 201);
    }

    public function overview(int $id, ImportAllocationService $allocations)
    {
        $operation = ImportOperation::with([
            'exporter',
            'legacyInvoice',
            'parentInvoices.lines.product',
            'parentInvoices.partialInvoices.lines.parentLine',
        ])->findOrFail($id);

        $factoryValue = (float) DB::table('import_factory_order_lines as line')
            ->join('import_factory_orders as factory', 'factory.id', '=', 'line.import_factory_order_id')
            ->where('factory.import_operation_id', $id)
            ->sum(DB::raw('line.quantity * line.unit_price'));
        $commercialValue = (float) DB::table('import_parent_invoice_lines as line')
            ->join('import_parent_invoices as invoice', 'invoice.id', '=', 'line.import_parent_invoice_id')
            ->where('invoice.import_operation_id', $id)
            ->sum(DB::raw('line.quantity * line.commercial_unit_price'));
        $commercialFreight = (float) DB::table('import_parent_invoices')
            ->where('import_operation_id', $id)
            ->sum(DB::raw('planned_container_count * freight_per_container'));
        $estimatedCosts = (float) DB::table('import_costs')
            ->where('import_operation_id', $id)
            ->where('value_type', 'estimate')
            ->sum('amount');
        $commissionTotal = (float) DB::table('import_commissions')
            ->where('import_operation_id', $id)
            ->sum('calculated_amount');

        $factoryOrders = DB::table('import_factory_orders as fo')
            ->leftJoin('suppliers as s', 's.id', '=', 'fo.factory_supplier_id')
            ->where('fo.import_operation_id', $id)
            ->select([
                'fo.*',
                's.name as factory_name',
                DB::raw('(select count(*) from import_factory_order_lines fol where fol.import_factory_order_id = fo.id) as product_count'),
                DB::raw('(select coalesce(sum(fol.quantity * fol.unit_price), 0) from import_factory_order_lines fol where fol.import_factory_order_id = fo.id) as total_amount'),
            ])
            ->get();

        $lines = DB::table('import_factory_order_lines')
            ->whereIn('import_factory_order_id', $factoryOrders->pluck('id'))
            ->get()
            ->groupBy('import_factory_order_id');

        $factoryOrders->transform(function ($order) use ($lines) {
            $order->lines = $lines->get($order->id, collect())->values();
            return $order;
        });

        $containers = DB::table('import_containers as c')
            ->leftJoin('import_partial_invoices as p', 'p.id', '=', 'c.import_partial_invoice_id')
            ->leftJoin('import_parent_invoices as pi', 'pi.id', '=', 'c.import_parent_invoice_id')
            ->where('c.import_operation_id', $id)
            ->select([
                'c.*',
                'p.number as partial_number',
                'pi.number as parent_number',
            ])
            ->get();

        $costs = DB::table('import_costs as cost')
            ->leftJoin('import_partial_invoices as p', 'p.id', '=', 'cost.import_partial_invoice_id')
            ->leftJoin('import_parent_invoices as pi', 'pi.id', '=', 'cost.import_parent_invoice_id')
            ->where('cost.import_operation_id', $id)
            ->select([
                'cost.*',
                'p.number as partial_number',
                'pi.number as parent_number',
            ])
            ->get();

        return response()->json([
            'operation' => $operation,
            'balances' => $operation->parentInvoices
                ->map(fn (ImportParentInvoice $parent) => $allocations->balances($parent))
                ->values(),
            'factory_orders' => $factoryOrders,
            'containers' => $containers,
            'costs' => $costs,
            'commissions' => DB::table('import_commissions')->where('import_operation_id', $id)->get(),
            'documents' => DB::table('import_documents')->where('import_operation_id', $id)->orderByDesc('version')->get(),
            'payments' => DB::table('import_operation_payments')->where('import_operation_id', $id)->get(),
            'history' => DB::table('import_audit_events')->where('entity_type', 'like', 'App\\Models\\Import%')->orderByDesc('occurred_at')->limit(100)->get(),
            'financial_summary' => [
                'factory_value' => round($factoryValue, 4),
                'commercial_invoice_value' => round($commercialValue + $commercialFreight, 4),
                'estimated_costs' => round($estimatedCosts + $commissionTotal, 4),
                'estimated_total' => round($commercialValue + $commercialFreight + $estimatedCosts + $commissionTotal, 4),
            ],
        ]);
    }

    public function storeFactoryOrder(Request $request, int $id)
    {
        ImportOperation::findOrFail($id);
        $data = $request->validate([
            'factory_supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'], 'number' => ['nullable', 'string', 'max:80'],
            'currency' => ['required', 'string', 'size:3'], 'notes' => ['nullable', 'string'], 'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['nullable', 'integer', 'exists:products,id'], 'lines.*.description' => ['required', 'string', 'max:255'],
            'lines.*.unit' => ['nullable', 'string', 'max:30'], 'lines.*.quantity' => ['required', 'numeric', 'gt:0'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
        $order = DB::transaction(function () use ($id, $data) {
            $orderId = DB::table('import_factory_orders')->insertGetId([
                'import_operation_id' => $id, 'factory_supplier_id' => $data['factory_supplier_id'] ?? null,
                'number' => $data['number'] ?? null, 'currency' => strtoupper($data['currency']), 'status' => 'draft',
                'notes' => $data['notes'] ?? null, 'created_at' => now(), 'updated_at' => now(),
            ]);
            foreach ($data['lines'] as $line) {
                DB::table('import_factory_order_lines')->insert([
                    'import_factory_order_id' => $orderId, 'product_id' => $line['product_id'] ?? null,
                    'description_snapshot' => $line['description'], 'unit' => $line['unit'] ?? 'pcs',
                    'quantity' => $line['quantity'], 'unit_price' => $line['unit_price'], 'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            return DB::table('import_factory_orders')->find($orderId);
        });

        return response()->json(['factory_order' => $order], 201);
    }

    public function updateFactoryOrder(Request $request, int $id, int $orderId)
    {
        ImportOperation::findOrFail($id);
        $order = DB::table('import_factory_orders')->where('id', $orderId)->where('import_operation_id', $id)->first();
        if (!$order) {
            return response()->json(['message' => 'Factory order not found'], 404);
        }

        $data = $request->validate([
            'factory_supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'number' => ['nullable', 'string', 'max:80'],
            'currency' => ['required', 'string', 'size:3'],
            'status' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'lines.*.description' => ['required', 'string', 'max:255'],
            'lines.*.unit' => ['nullable', 'string', 'max:30'],
            'lines.*.quantity' => ['required', 'numeric', 'gt:0'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $updated = DB::transaction(function () use ($orderId, $data) {
            DB::table('import_factory_orders')->where('id', $orderId)->update([
                'factory_supplier_id' => $data['factory_supplier_id'] ?? null,
                'number' => $data['number'] ?? null,
                'currency' => strtoupper($data['currency']),
                'status' => $data['status'] ?? 'draft',
                'notes' => $data['notes'] ?? null,
                'updated_at' => now(),
            ]);

            DB::table('import_factory_order_lines')->where('import_factory_order_id', $orderId)->delete();

            foreach ($data['lines'] as $line) {
                DB::table('import_factory_order_lines')->insert([
                    'import_factory_order_id' => $orderId,
                    'product_id' => $line['product_id'] ?? null,
                    'description_snapshot' => $line['description'],
                    'unit' => $line['unit'] ?? 'pcs',
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return DB::table('import_factory_orders')->find($orderId);
        });

        return response()->json(['factory_order' => $updated]);
    }

    public function destroyFactoryOrder(int $id, int $orderId)
    {
        ImportOperation::findOrFail($id);
        $deleted = DB::table('import_factory_orders')->where('id', $orderId)->where('import_operation_id', $id)->delete();
        if (!$deleted) {
            return response()->json(['message' => 'Factory order not found'], 404);
        }

        return response()->json(['message' => 'Factory order deleted successfully']);
    }

    public function storeContainer(Request $request, int $id)
    {
        ImportOperation::findOrFail($id);
        $data = $request->validate([
            'count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'parent_id' => ['nullable', 'integer', 'exists:import_parent_invoices,id'],
            'partial_id' => ['nullable', 'integer', 'exists:import_partial_invoices,id'],
            'container_number' => ['nullable', 'string', 'max:80'],
            'seal_number' => ['nullable', 'string', 'max:80'],
            'booking_reference' => ['nullable', 'string', 'max:100'],
            'freight_amount' => ['nullable', 'numeric', 'min:0'],
            'container_size' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'string', 'max:30'],
        ]);

        $count = (int) ($data['count'] ?? 1);
        $parentId = $data['parent_id'] ?? null;
        $partialId = $data['partial_id'] ?? null;
        $freightAmount = $data['freight_amount'] ?? '0';
        $size = $data['container_size'] ?? null;
        $baseNumber = trim((string) ($data['container_number'] ?? ''));
        $booking = $data['booking_reference'] ?? null;
        $seal = $data['seal_number'] ?? null;
        $status = $data['status'];

        $createdIds = [];
        DB::transaction(function () use (
            $id, $count, $parentId, $partialId, $freightAmount, $size,
            $baseNumber, $booking, $seal, $status, &$createdIds
        ) {
            for ($i = 0; $i < $count; $i++) {
                $number = $baseNumber;
                if ($count > 1 && $baseNumber !== '') {
                    $number = $baseNumber . '-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                } elseif ($count > 1 && $baseNumber === '') {
                    $number = 'CTN-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                }
                $containerId = DB::table('import_containers')->insertGetId([
                    'import_operation_id' => $id,
                    'import_parent_invoice_id' => $parentId,
                    'import_partial_invoice_id' => $partialId,
                    'container_number' => $number ?: null,
                    'seal_number' => $seal,
                    'booking_reference' => $booking,
                    'freight_amount' => $freightAmount,
                    'container_size' => $size,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $createdIds[] = $containerId;
            }
        });

        return response()->json([
            'container' => DB::table('import_containers')->find($createdIds[0]),
            'count' => count($createdIds),
        ], 201);
    }

    public function updateContainer(Request $request, int $id, int $containerId)
    {
        ImportOperation::findOrFail($id);
        $container = DB::table('import_containers')->where('id', $containerId)->where('import_operation_id', $id)->first();
        if (!$container) {
            return response()->json(['message' => 'Container not found'], 404);
        }

        $data = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:import_parent_invoices,id'],
            'partial_id' => ['nullable', 'integer', 'exists:import_partial_invoices,id'],
            'container_number' => ['nullable', 'string', 'max:80'],
            'seal_number' => ['nullable', 'string', 'max:80'],
            'booking_reference' => ['nullable', 'string', 'max:100'],
            'freight_amount' => ['nullable', 'numeric', 'min:0'],
            'container_size' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'string', 'max:30'],
        ]);

        $parentId = array_key_exists('parent_id', $data) ? $data['parent_id'] : $container->import_parent_invoice_id;
        $partialId = array_key_exists('partial_id', $data) ? $data['partial_id'] : $container->import_partial_invoice_id;

        DB::table('import_containers')->where('id', $containerId)->update([
            'import_parent_invoice_id' => $parentId,
            'import_partial_invoice_id' => $partialId,
            'container_number' => $data['container_number'] ?? $container->container_number,
            'seal_number' => $data['seal_number'] ?? $container->seal_number,
            'booking_reference' => $data['booking_reference'] ?? $container->booking_reference,
            'freight_amount' => $data['freight_amount'] ?? $container->freight_amount,
            'container_size' => $data['container_size'] ?? $container->container_size,
            'status' => $data['status'] ?? $container->status,
            'updated_at' => now(),
        ]);

        return response()->json(['container' => DB::table('import_containers')->find($containerId)]);
    }

    public function destroyContainer(int $id, int $containerId)
    {
        ImportOperation::findOrFail($id);
        $deleted = DB::table('import_containers')->where('id', $containerId)->where('import_operation_id', $id)->delete();
        if (!$deleted) {
            return response()->json(['message' => 'Container not found'], 404);
        }

        return response()->json(['message' => 'Container deleted successfully']);
    }

    public function storeCost(Request $request, int $id)
    {
        ImportOperation::findOrFail($id);
        $data = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:import_parent_invoices,id'],
            'partial_id' => ['nullable', 'integer', 'exists:import_partial_invoices,id'],
            'category' => ['required', 'string', 'max:50'],
            'value_type' => ['required', 'in:estimate,actual'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'exchange_rate' => ['nullable', 'numeric', 'gt:0'],
            'exchange_rate_date' => ['nullable', 'date'],
            'exchange_rate_source' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string'],
        ]);
        $parentId = $data['parent_id'] ?? null;
        $partialId = $data['partial_id'] ?? null;
        unset($data['parent_id'], $data['partial_id']);
        $data['currency'] = strtoupper($data['currency']);
        $recordId = DB::table('import_costs')->insertGetId(array_merge($data, [
            'import_operation_id' => $id,
            'import_parent_invoice_id' => $parentId,
            'import_partial_invoice_id' => $partialId,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return response()->json(['cost' => DB::table('import_costs')->find($recordId)], 201);
    }

    public function updateCost(Request $request, int $id, int $costId)
    {
        ImportOperation::findOrFail($id);
        $cost = DB::table('import_costs')->where('id', $costId)->where('import_operation_id', $id)->first();
        if (!$cost) {
            return response()->json(['message' => 'Cost not found'], 404);
        }

        $data = $request->validate([
            'parent_id' => ['nullable', 'integer', 'exists:import_parent_invoices,id'],
            'partial_id' => ['nullable', 'integer', 'exists:import_partial_invoices,id'],
            'category' => ['required', 'string', 'max:50'],
            'value_type' => ['required', 'in:estimate,actual'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'exchange_rate' => ['nullable', 'numeric', 'gt:0'],
            'exchange_rate_date' => ['nullable', 'date'],
            'exchange_rate_source' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string'],
        ]);

        $parentId = array_key_exists('parent_id', $data) ? $data['parent_id'] : $cost->import_parent_invoice_id;
        $partialId = array_key_exists('partial_id', $data) ? $data['partial_id'] : $cost->import_partial_invoice_id;
        unset($data['parent_id'], $data['partial_id']);
        $data['currency'] = strtoupper($data['currency']);

        DB::table('import_costs')->where('id', $costId)->update(array_merge($data, [
            'import_parent_invoice_id' => $parentId,
            'import_partial_invoice_id' => $partialId,
            'updated_at' => now(),
        ]));

        return response()->json(['cost' => DB::table('import_costs')->find($costId)]);
    }

    public function destroyCost(int $id, int $costId)
    {
        ImportOperation::findOrFail($id);
        $deleted = DB::table('import_costs')->where('id', $costId)->where('import_operation_id', $id)->delete();
        if (!$deleted) {
            return response()->json(['message' => 'Cost not found'], 404);
        }

        return response()->json(['message' => 'Cost deleted successfully']);
    }

    public function storePayment(Request $request, int $id)
    {
        ImportOperation::findOrFail($id);
        $data = $request->validate(['partial_id' => ['nullable', 'integer', 'exists:import_partial_invoices,id'], 'amount' => ['required', 'numeric', 'gt:0'], 'currency' => ['required', 'string', 'size:3'], 'paid_at' => ['required', 'date'], 'reference' => ['nullable', 'string', 'max:120'], 'notes' => ['nullable', 'string']]);
        $partialId = $data['partial_id'] ?? null;
        unset($data['partial_id']);
        $data['currency'] = strtoupper($data['currency']);
        $recordId = DB::table('import_operation_payments')->insertGetId(array_merge($data, ['import_operation_id' => $id, 'import_partial_invoice_id' => $partialId, 'status' => 'recorded', 'created_at' => now(), 'updated_at' => now()]));

        return response()->json(['payment' => DB::table('import_operation_payments')->find($recordId)], 201);
    }

    public function storeCommission(Request $request, int $id)
    {
        ImportOperation::findOrFail($id);
        $data = $request->validate([
            'partial_id' => ['nullable', 'integer', 'exists:import_partial_invoices,id'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'base_type' => ['required', 'in:factory_purchase_subtotal,manual'],
            'base_amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
        ]);
        $partialId = $data['partial_id'] ?? null;
        unset($data['partial_id']);
        $data['currency'] = strtoupper($data['currency']);
        $data['calculated_amount'] = number_format(
            ((float) $data['base_amount'] * (float) $data['percentage']) / 100,
            4,
            '.',
            ''
        );
        $recordId = DB::table('import_commissions')->insertGetId(array_merge($data, [
            'import_operation_id' => $id,
            'import_partial_invoice_id' => $partialId,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return response()->json(['commission' => DB::table('import_commissions')->find($recordId)], 201);
    }

    public function reconciliation(int $id, ImportAllocationService $allocations)
    {
        $operation = ImportOperation::with('parentInvoices')->findOrFail($id);
        $costs = DB::table('import_costs')->where('import_operation_id', $id)->get();

        return response()->json([
            'operation_id' => $id,
            'allocations' => $operation->parentInvoices
                ->map(fn (ImportParentInvoice $parent) => $allocations->balances($parent))->values(),
            'estimated_costs' => $costs->where('value_type', 'estimate')->groupBy('currency')->map->sum('amount'),
            'actual_costs' => $costs->where('value_type', 'actual')->groupBy('currency')->map->sum('amount'),
            'payments' => DB::table('import_operation_payments')->where('import_operation_id', $id)->get()->groupBy('currency')->map->sum('amount'),
            'commissions' => DB::table('import_commissions')->where('import_operation_id', $id)->get(),
        ]);
    }

    public function storeDocument(Request $request, int $id)
    {
        ImportOperation::findOrFail($id);
        $data = $request->validate(['file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:20480'], 'type' => ['required', 'string', 'max:50'], 'parent_id' => ['nullable', 'integer'], 'partial_id' => ['nullable', 'integer'], 'issuer' => ['nullable', 'string', 'max:120'], 'status' => ['required', 'in:draft_generated,exporter_issued,bank_submitted']]);
        $file = $request->file('file');
        $version = ((int) DB::table('import_documents')->where('import_operation_id', $id)->where('type', $data['type'])->max('version')) + 1;
        $path = $file->store("private/importations/{$id}");
        $recordId = DB::table('import_documents')->insertGetId(['import_operation_id' => $id, 'import_parent_invoice_id' => $data['parent_id'] ?? null, 'import_partial_invoice_id' => $data['partial_id'] ?? null, 'type' => $data['type'], 'version' => $version, 'storage_path' => $path, 'original_name' => $file->getClientOriginalName(), 'mime_type' => $file->getMimeType(), 'sha256' => hash_file('sha256', $file->getRealPath()), 'issuer' => $data['issuer'] ?? null, 'status' => $data['status'], 'uploaded_by' => Auth::id(), 'created_at' => now(), 'updated_at' => now()]);

        return response()->json(['document' => DB::table('import_documents')->find($recordId)], 201);
    }

    public function storeParent(
        StoreImportParentInvoiceRequest $request,
        int $id
    ) {
        $operation = ImportOperation::findOrFail($id);
        $data = $request->validated();

        $parent = DB::transaction(function () use ($operation, $data) {
            $parent = $operation->parentInvoices()->create([
                'sales_supplier_id' => $data['sales_supplier_id'] ?? null,
                'number' => $data['number'],
                'currency' => strtoupper($data['currency']),
                'planned_container_count' => $data['planned_container_count'],
                'freight_per_container' => $data['freight_per_container'] ?? '0',
                'show_container_price' => $data['show_container_price'] ?? true,
                'show_ports' => $data['show_ports'] ?? true,
                'status' => 'draft',
                'revision' => 1,
            ]);

            $parent->lines()->createMany(collect($data['lines'])->map(fn (array $line) => [
                'product_id' => $line['product_id'] ?? null,
                'description_snapshot' => $line['description'],
                'unit' => $line['unit'] ?? 'pcs',
                'quantity' => $line['quantity'],
                'commercial_unit_price' => $line['commercial_unit_price'],
            ])->all());

            if (! empty($data['container_ids'])) {
                DB::table('import_containers')
                    ->whereIn('id', $data['container_ids'])
                    ->where('import_operation_id', $operation->id)
                    ->update([
                        'import_parent_invoice_id' => $parent->id,
                        'updated_at' => now(),
                    ]);
            }

            ImportAuditEvent::create([
                'entity_type' => ImportParentInvoice::class,
                'entity_id' => $parent->id,
                'action' => 'created',
                'actor_id' => Auth::id(),
                'after_json' => $parent->load('lines')->toArray(),
                'occurred_at' => now(),
            ]);

            return $parent;
        });

        return response()->json(['parent_invoice' => $parent->load('lines.product')], 201);
    }

    public function balances(
        int $id,
        int $parentId,
        ImportAllocationService $allocations
    ) {
        $parent = $this->parentForOperation($id, $parentId);

        return response()->json($allocations->balances($parent));
    }

    public function updateParent(
        StoreImportParentInvoiceRequest $request,
        int $id,
        int $parentId,
        ImportAllocationService $allocations
    ) {
        $parent = $this->parentForOperation($id, $parentId);
        $data = $request->validated();

        DB::transaction(function () use ($parent, $data) {
            $before = $parent->load('lines')->toArray();

            $allocatedContainers = (int) $parent->partialInvoices()
                ->where('status', '!=', 'cancelled')
                ->sum('container_allocation_count');

            if ((int) $data['planned_container_count'] < $allocatedContainers) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'planned_container_count' => [
                        "Le nombre de conteneurs prévu ({$data['planned_container_count']}) ne peut pas être inférieur aux conteneurs déjà alloués ({$allocatedContainers}).",
                    ],
                ]);
            }

            $parent->update([
                'sales_supplier_id' => $data['sales_supplier_id'] ?? null,
                'number' => $data['number'],
                'currency' => strtoupper($data['currency']),
                'planned_container_count' => $data['planned_container_count'],
                'freight_per_container' => $data['freight_per_container'] ?? '0',
                'show_container_price' => $data['show_container_price'] ?? $parent->show_container_price ?? true,
                'show_ports' => $data['show_ports'] ?? $parent->show_ports ?? true,
                'revision' => $parent->revision + 1,
            ]);

            if (isset($data['container_ids'])) {
                DB::table('import_containers')
                    ->where('import_parent_invoice_id', $parent->id)
                    ->whereNotIn('id', $data['container_ids'])
                    ->update([
                        'import_parent_invoice_id' => null,
                        'updated_at' => now(),
                    ]);

                if (! empty($data['container_ids'])) {
                    DB::table('import_containers')
                        ->whereIn('id', $data['container_ids'])
                        ->where('import_operation_id', $parent->import_operation_id)
                        ->update([
                            'import_parent_invoice_id' => $parent->id,
                            'updated_at' => now(),
                        ]);
                }
            }

            $existingLines = $parent->lines()->get()->keyBy('id');
            $existingLinesList = $parent->lines()->get();
            $incomingLines = collect($data['lines']);
            $processedLineIds = [];

            // Query active allocations grouped by parent invoice line id
            $allocatedQuery = DB::table('import_partial_invoice_lines as allocation')
                ->join('import_partial_invoices as partial', 'partial.id', '=', 'allocation.import_partial_invoice_id')
                ->whereNull('partial.deleted_at')
                ->where('partial.status', '!=', 'cancelled')
                ->whereIn('allocation.import_parent_invoice_line_id', $existingLines->keys()->all())
                ->groupBy('allocation.import_parent_invoice_line_id')
                ->select([
                    'allocation.import_parent_invoice_line_id',
                    DB::raw('SUM(allocation.quantity) as total_allocated'),
                ])
                ->get()
                ->keyBy('import_parent_invoice_line_id');

            foreach ($incomingLines as $index => $incoming) {
                $lineId = $incoming['id'] ?? null;
                /** @var \App\Models\ImportParentInvoiceLine|null $line */
                $line = null;
                if ($lineId && isset($existingLines[$lineId])) {
                    $line = $existingLines[$lineId];
                } elseif (! $lineId && isset($existingLinesList[$index]) && ! in_array($existingLinesList[$index]->id, $processedLineIds)) {
                    $line = $existingLinesList[$index];
                }

                $newQty = (float) $incoming['quantity'];

                if ($line) {
                    $allocatedQty = isset($allocatedQuery[$line->id])
                        ? (float) $allocatedQuery[$line->id]->total_allocated
                        : 0.0;

                    if ($newQty < $allocatedQty) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "lines.$index.quantity" => [
                                "La quantité ({$newQty}) ne peut pas être inférieure à la quantité déjà allouée ({$allocatedQty}) pour '{$line->description_snapshot}'.",
                            ],
                        ]);
                    }

                    $line->update([
                        'product_id' => $incoming['product_id'] ?? null,
                        'description_snapshot' => $incoming['description'],
                        'unit' => $incoming['unit'] ?? 'pcs',
                        'quantity' => $incoming['quantity'],
                        'commercial_unit_price' => $incoming['commercial_unit_price'],
                    ]);
                    $processedLineIds[] = $line->id;
                } else {
                    $newLine = $parent->lines()->create([
                        'product_id' => $incoming['product_id'] ?? null,
                        'description_snapshot' => $incoming['description'],
                        'unit' => $incoming['unit'] ?? 'pcs',
                        'quantity' => $incoming['quantity'],
                        'commercial_unit_price' => $incoming['commercial_unit_price'],
                    ]);
                    $processedLineIds[] = $newLine->id;
                }
            }

            // Clean up lines that were removed
            $linesToDelete = $existingLines->whereNotIn('id', $processedLineIds);
            foreach ($linesToDelete as $oldLine) {
                $hasActiveAllocations = DB::table('import_partial_invoice_lines as allocation')
                    ->join('import_partial_invoices as partial', 'partial.id', '=', 'allocation.import_partial_invoice_id')
                    ->whereNull('partial.deleted_at')
                    ->where('partial.status', '!=', 'cancelled')
                    ->where('allocation.import_parent_invoice_line_id', $oldLine->id)
                    ->exists();

                if ($hasActiveAllocations) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'lines' => [
                            "L'article '{$oldLine->description_snapshot}' a déjà des allocations partielles actives et ne peut pas être supprimé.",
                        ],
                    ]);
                }

                // If line was only in cancelled or deleted partial invoices, delete those orphaned records
                DB::table('import_partial_invoice_lines')
                    ->where('import_parent_invoice_line_id', $oldLine->id)
                    ->delete();

                $oldLine->delete();
            }

            ImportAuditEvent::create([
                'entity_type' => ImportParentInvoice::class,
                'entity_id' => $parent->id,
                'action' => 'updated',
                'actor_id' => Auth::id(),
                'before_json' => $before,
                'after_json' => $parent->fresh('lines')->toArray(),
                'occurred_at' => now(),
            ]);
        });

        return response()->json([
            'parent_invoice' => $parent->fresh('lines.product', 'supplier'),
            'balances' => $allocations->balances($parent),
        ]);
    }

    public function destroyParent(int $id, int $parentId)
    {
        $parent = $this->parentForOperation($id, $parentId);
        if ($parent->partialInvoices()->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'invoice' => ['Delete the partial invoices before deleting this parent invoice.'],
            ]);
        }
        $parent->delete();

        return response()->json(['message' => 'Parent invoice deleted.']);
    }

    public function storePartial(
        StoreImportPartialInvoiceRequest $request,
        int $id,
        int $parentId,
        ImportAllocationService $allocations
    ) {
        $parent = $this->parentForOperation($id, $parentId);
        $partial = $allocations->createPartial($parent, $request->validated());

        return response()->json([
            'partial_invoice' => $partial,
            'balances' => $allocations->balances($parent),
        ], 201);
    }

    public function updatePartial(
        StoreImportPartialInvoiceRequest $request,
        int $id,
        int $parentId,
        int $partialId,
        ImportAllocationService $allocations
    ) {
        $parent = $this->parentForOperation($id, $parentId);
        $partial = $parent->partialInvoices()->findOrFail($partialId);
        $data = $request->validated();
        $partial->load('lines');
        $normaliseQuantity = static function ($value) {
            [$whole, $fraction] = array_pad(explode('.', (string) $value, 2), 2, '');
            return (ltrim($whole, '0') ?: '0').'.'.substr(str_pad($fraction, 3, '0'), 0, 3);
        };
        $existingLines = $partial->lines->mapWithKeys(fn ($line) => [
            (int) $line->import_parent_invoice_line_id => $normaliseQuantity($line->quantity),
        ])->sortKeys()->all();
        $incomingLines = array_key_exists('lines', $data)
            ? collect($data['lines'])->mapWithKeys(fn ($line) => [
                (int) $line['parent_line_id'] => $normaliseQuantity($line['quantity']),
            ])->sortKeys()->all()
            : $existingLines;
        $existingContainerIds = DB::table('import_containers')
            ->where('import_partial_invoice_id', $partialId)->orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->all();
        $incomingContainerIds = array_key_exists('container_ids', $data)
            ? collect($data['container_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->sort()->values()->all()
            : $existingContainerIds;
        $linesUnchanged = $incomingLines === $existingLines;
        if ($linesUnchanged) {
            $updated = DB::transaction(function () use ($parent, $partialId, $data, $incomingContainerIds) {
                $parent->newQuery()->whereKey($parent->id)->lockForUpdate()->firstOrFail();
                $record = $parent->partialInvoices()->whereKey($partialId)->lockForUpdate()->firstOrFail();
                $before = $record->load('lines')->toArray();
                $record->update([
                    'number' => $data['number'] ?? $record->number,
                    'freight_amount' => $data['freight_amount'] ?? $record->freight_amount,
                    'container_allocation_count' => $data['container_allocation_count'] ?? $record->container_allocation_count,
                    'revision' => $record->revision + 1,
                ]);

                if (array_key_exists('container_ids', $data)) {
                    // Dissociate containers previously assigned to this partial that are not in incoming
                    DB::table('import_containers')
                        ->where('import_partial_invoice_id', $partialId)
                        ->whereNotIn('id', $incomingContainerIds)
                        ->update(['import_partial_invoice_id' => null, 'updated_at' => now()]);

                    // Associate selected containers to this partial and parent
                    if (!empty($incomingContainerIds)) {
                        DB::table('import_containers')
                            ->whereIn('id', $incomingContainerIds)
                            ->where('import_operation_id', $parent->import_operation_id)
                            ->update([
                                'import_parent_invoice_id' => $parent->id,
                                'import_partial_invoice_id' => $record->id,
                                'updated_at' => now(),
                            ]);
                    }
                }

                ImportAuditEvent::create([
                    'entity_type' => \App\Models\ImportPartialInvoice::class,
                    'entity_id' => $record->id,
                    'action' => 'updated',
                    'actor_id' => Auth::id(),
                    'before_json' => $before,
                    'after_json' => $record->fresh('lines')->toArray(),
                    'occurred_at' => now(),
                ]);
                return $record->fresh('lines.parentLine');
            });
            return response()->json(['partial_invoice' => $updated, 'balances' => $allocations->balances($parent)]);
        }
        $hasReferences = DB::table('import_costs')->where('import_partial_invoice_id', $partialId)->exists()
            || DB::table('import_documents')->where('import_partial_invoice_id', $partialId)->exists()
            || DB::table('import_operation_payments')->where('import_partial_invoice_id', $partialId)->exists();
        if ($hasReferences) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'invoice' => ['This partial invoice is already referenced by operation records and cannot be replaced.'],
            ]);
        }
        $before = $partial->load('lines')->toArray();
        $data += [
            'container_allocation_count' => $partial->container_allocation_count,
            'freight_amount' => $partial->freight_amount,
            'lines' => $partial->lines->map(fn ($line) => [
                'parent_line_id' => $line->import_parent_invoice_line_id,
                'quantity' => (string) $line->quantity,
            ])->all(),
        ];
        $replacement = DB::transaction(function () use ($partial, $parent, $data, $allocations, $incomingContainerIds) {
            DB::table('import_containers')
                ->where('import_partial_invoice_id', $partial->id)
                ->update(['import_partial_invoice_id' => null]);
            $partial->lines()->delete();
            $partial->forceDelete();

            $created = $allocations->createPartial($parent, $data);
            if (!empty($incomingContainerIds)) {
                DB::table('import_containers')
                    ->whereIn('id', $incomingContainerIds)
                    ->where('import_operation_id', $parent->import_operation_id)
                    ->update([
                        'import_parent_invoice_id' => $parent->id,
                        'import_partial_invoice_id' => $created->id,
                        'updated_at' => now(),
                    ]);
            }
            return $created;
        });
        ImportAuditEvent::create([
            'entity_type' => \App\Models\ImportPartialInvoice::class,
            'entity_id' => $replacement->id,
            'action' => 'updated',
            'actor_id' => Auth::id(),
            'before_json' => $before,
            'after_json' => $replacement->toArray(),
            'occurred_at' => now(),
        ]);

        return response()->json(['partial_invoice' => $replacement, 'balances' => $allocations->balances($parent)]);
    }

    public function destroyPartial(int $id, int $parentId, int $partialId)
    {
        $parent = $this->parentForOperation($id, $parentId);
        $partial = $parent->partialInvoices()->findOrFail($partialId);
        $partial->update(['status' => 'cancelled']);
        $partial->delete();

        return response()->json(['message' => 'Partial invoice deleted.']);
    }

    private function parentForOperation(int $operationId, int $parentId): ImportParentInvoice
    {
        return ImportParentInvoice::query()
            ->where('import_operation_id', $operationId)
            ->findOrFail($parentId);
    }
}
